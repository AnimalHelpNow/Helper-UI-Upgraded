<?php

namespace Drupal\anh_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform transforms on WYSIWYG HTML from d7 to d9 syntax using the service.
 *
 * @MigrateProcessPlugin(
 *   id = "transform_wysiwyg"
 * )
 *
 * @code
 * body:
 *   plugin: transform_wysiwyg
 *   source: body
 * @endcode
 */
class TransformWysiwyg extends ProcessPluginBase {

  /**
   * 
   */
  protected function transform_embedded($value) {
    $value = $this->checkForImageEmbed($value);
    $value = $this->checkForMediaEmbed($value);

    return $value;
  }

  /**
   * Check for image embeds.
   */
  protected function checkForImageEmbed($value) {
    $search='/\[\[\{\"fid\"\:\"(.+?)\".+?\}\]\]/';
    $embeds_arr = [];
    preg_match_all($search, $value, $embeds_arr);

    if ($embeds_arr) {
      foreach($embeds_arr[1] as $delta => $element) {
        // Locate the media item in the new system...
        $query = \Drupal::database()->select('migrate_map_image_to_media', 'm');
        $query->condition('sourceid1', $element);
        $query->fields('m', ['destid1']);
        $results = $query->execute()->fetchAll();
        $r = array_shift($results);
        if ($r !== NULL && $r->destid1 !== NULL) { 
          $media = \Drupal::entityTypeManager()
            ->getStorage('media')
            ->load($r->destid1);
          
          if ($media) {     
            $replacement_string = $this->media_string($media);
            $value = str_replace($embeds_arr[0][$delta], $replacement_string, $value);
          } 
        }
      }
    }

    return $value;
  }

  /**
   * Check for media wysiwyg embeds
   */
  protected function checkForMediaEmbed($value) {
    $embeds = '/\[\[\{\"type\"\:\"media\".+?\}\]\]/';
    $type = '/\"class\"\:\"(.+?)\"/'; 
    $fid = '/\"fid\"\:\"(.+?)\"/';
    $embeds_arr = [];
    preg_match_all($embeds, $value, $embeds_arr);

    if ($embeds_arr) {
      
      foreach($embeds_arr[0] as $element) {
        // error_log(print_r($element, true));
        // preg_match_all($type, $element, $type_matches);
        preg_match_all($fid, $element, $fid_matches);
        if (isset($fid_matches[1][0])) {
          // Locate the media item in the new system...
          $query = \Drupal::database()->select('migrate_map_image_to_media', 'm');
          $query->condition('sourceid1', $fid_matches[1][0]);
          $query->fields('m', ['destid1']);
          $results = $query->execute()->fetchAll();
          $r = array_shift($results);
          if ($r !== NULL && $r->destid1 !== NULL) { 
            $media = \Drupal::entityTypeManager()
              ->getStorage('media')
              ->load($r->destid1);
            
            if ($media) {     
              $replacement_string = $this->media_string($media);
              $value = str_replace($element, $replacement_string, $value);
            } 
          }
        }
      }
    }
    

    return $value;
  }

  protected function media_string($media) {
    $string = '<drupal-media 
      data-align="center" 
      data-entity-type="media" 
      data-entity-uuid="' . $media->uuid->value  . '"></drupal-media>';
    
    return $string;
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    
    
    // Formatted long text may be passed as an array with value and format.

    if (is_array($value)) {
      
      $value['value'] = $this->transform_embedded($value['value']);
      $value['format'] = 'full_html';
    }
    else {
      $value = $this->transform_embedded($value);
    }
    
    return $value;
  }

}
