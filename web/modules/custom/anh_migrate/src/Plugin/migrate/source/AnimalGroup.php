<?php

namespace Drupal\anh_migrate\Plugin\migrate\source;

use Drupal\anh_migrate\Plugin\migrate\source\AdvancedNodeComplete;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Row;

/**
 * Drupal 7 Fieldable Pane source from database.
 *
 * For available configuration keys, refer to the parent class.
 *
 * @MigrateSource(
 *   id = "d7_animal_group",
 *   source_module = "node"
 * )
 */
class AnimalGroup extends AdvancedNodeComplete { 
//field_data_field_animalgroup_sort_index
  protected $addedFields = [
    'field_animalgroup_sort_index' => ['field_animalgroup_sort_index_value']
  ];

  public function query() {
    $query = parent::query();

    // field_unique_id
    // $query->leftJoin('field_data_field_animalgroup_sort_index', 'field_animalgroup_sort_index', 'field_animalgroup_sort_index.entity_id = n.nid');
    // $query->addField('field_animalgroup_sort_index', 'field_animalgroup_sort_index_value', 'field_animalgroup_sort_index');
    
    return $query;
  }


}