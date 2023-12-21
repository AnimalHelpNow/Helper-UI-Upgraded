<?php

namespace Drupal\anh_migrate\Plugin\migrate\source;

use Drupal\node\Plugin\migrate\source\d7\NodeComplete;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Row;

/**
 * Drupal 7 Fieldable Pane source from database.
 *
 * For available configuration keys, refer to the parent class.
 *
 * @MigrateSource(
 *   id = "d7_advanced_node",
 *   source_module = "node"
 * )
 */
class AdvancedNodeComplete extends NodeComplete { 

  public function query() {
    $query = parent::query();
    // field_animalgroup_sort_index

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    
    parent::prepareRow($row);

    $this->addedFields['field_uniqueid'] = ['field_uniqueid_value'];
    if(count($this->addedFields) > 0) {
        foreach($this->addedFields as $addField => $fields) {
            $row = $this->addField($row, $addField, $fields);
        }
    }

    // Get node alias from drupal 7
    $row = $this->nodeAlias($row);
  }
  
  /**
   * Process an alias for a node.
   */
  protected function nodeAlias(Row &$row) {
      $nid = $row->getSourceProperty('nid');
      $query = $this->select('url_alias', 'alias');
      $query->condition('source', "node/{$nid}");
      $query->fields('alias', ['alias']);
      $results = $query->execute()->fetchAll();
      $alias = [];
      foreach($results as $record) {
          $row->setSourceProperty('alias', "/" . $record['alias']);
          return $row;
      }
      
      return $row;
  }
  
  protected $addedFields = [];

  /**
   * Helper function to add field data tables to
   * the query.
   */
  protected function addField(&$row, $field_id, $fields = []) {
      set_time_limit(3000);
      $query = $this->select("field_data_{$field_id}", $field_id);
      $query->condition("{$field_id}.entity_type", "node");
      $query->condition("{$field_id}.entity_id", $row->getSourceProperty('nid'));
      $query->addField($field_id, "entity_id");
      $query->fields($field_id, $fields);
      $results = $query->execute()->fetchAll();
      
      $data = [];
      foreach($results as $record) {
          foreach($fields as $field_id) {
              if(!isset($data[$field_id])) { $data[$field_id] = []; }
              $data[$field_id][] = $record[$field_id];
          }
      }
      
      foreach($fields as $field_id) {
          if (isset($data[$field_id])) {
              $row->setSourceProperty($field_id, $data[$field_id]);
          }
      }

      return $row;
  }

  protected function processFileToMedia(Row &$row, $field) {
    $data = [];
    foreach($row->getSourceProperty($field) as $value) {
        $data[] = $value['fid'];
    }
    $row->setSourceProperty($field, $data);
    return $row;
  }


}