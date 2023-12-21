<?php

namespace Drupal\lssutilities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Html;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Defines HelloController class.
 */
class LssutilitiesController extends ControllerBase {

  /**
   * Return full detail of node
   */
  public function tenatest($node) 
  {
    $debug = true;

    $nid = $node->id();
    $label = 'nid = ' . $nid;
    $string = Html::escape($debug ? print_r($node, TRUE) : var_export($node, TRUE));
    $string = '<pre>' . $string . '</pre>';
      $output .= trim($label ? '$label: $string' : $string);
      
    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /**
   * Return full detail of taxonomy term
   */
  public function lssTenatestTaxonomy($term) 
  {
    $output = debug($term,'taxonomy', true);
    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /**
   * This function is used to display info for an Entity
   */
  public function tenatestEntity($entity) 
  {
    $output = '';
    $query = \Drupal::entityQuery('node')
            ->condition('type', 'local_jurisdiction')
            ->condition('status', 1)
            ->condition('title', 'US-State-CO-County-Boulder');
    
    $results = $query->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($results);
    if (!empty($nodes)) {
      foreach ($nodes as $key => $country) {
        if ($country) {
          $output .= $country->title->value . ' country, ';
          $output .= $country->id() . ' nid<br>';
        }
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /**
   * Utility to change the max length of a text field
   */
  public function tenatestTextlength($field_name, $field_length) 
  {
    $entity_type = 'node';
    $database = \Drupal::database();
    // Resize the main field data table.
    $database->query('ALTER TABLE {$entity_type}__{$field_name} MODIFY {$field_name}_value VARCHAR({$field_length})');
    // Resize the revision field data table.
    $database->query('ALTER TABLE {$entity_type}_revision__{$field_name} MODIFY {$field_name}_value VARCHAR({$field_length})');

    // Update storage schema.
    $storage_key = $entity_type . '.field_schema_data.' . $field_name;
    $storage_schema = \Drupal::keyValue('entity.storage_schema.sql');
    $field_schema = $storage_schema->get($storage_key);
    $field_schema[$entity_type . '__' . $field_name]['fields'][$field_name . '_value']['length'] = $field_length;
    $field_schema[$entity_type . '_revision__' . $field_name]['fields'][$field_name . '_value']['length'] = $field_length;
    $storage_schema->set($storage_key, $field_schema);

    // Update field configuration.
    $config = \Drupal::configFactory()
      ->getEditable('field.storage.{$entity_type}.{$field_name}');
    $config->set('settings.max_length', $field_length);
    $config->save(TRUE);

    // Update field storage configuration.
    FieldStorageConfig::loadByName($entity_type, $field_name)->save();

    $output = t('Length of @entity-type.@field-name updated to @field-length', [
      '@entity-type' => $entity_type,
      '@field-name' => $field_name,
      '@field-length' => $field_length,
    ]);

    return [
      '#type' => 'markup',
      '#markup' => $output
    ];
  }
}

