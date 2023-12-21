<?php

namespace Drupal\anh_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform transforms on WYSIWYG HTML from d7 to d9 syntax using the service.
 *
 * @MigrateProcessPlugin(
 *   id = "transform_link"
 * )
 *
 * @code
 * body:
 *   plugin: transform_link
 *   source: body
 *   property: field_property
 * @endcode
 */
class TransformLink extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($this->configuration['property'])) { return NULL; }
    
    // Formatted long text may be passed as an array with value and format.
    $data = NULL;
    
    $data = is_array($value) ?
            ( 
                isset($value['url']) ? 
                    ($value['url'] == 'http://' ? '' : $value['url']) 
                : 
                    ($value[0]['url'] == 'http://' ? '' : $value[0]['url']) 
            ) 
        : 
            ( $value == 'http://' ? '' : $value );

    return $data;
  }

}
