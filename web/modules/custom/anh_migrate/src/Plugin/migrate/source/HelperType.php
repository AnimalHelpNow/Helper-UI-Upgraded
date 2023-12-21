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
 *   id = "d7_helpertype_node",
 *   source_module = "node"
 * )
 */
class HelperType extends AdvancedNodeComplete { 

  public function query() {
    $query = parent::query();

    // field_helpertype_description
    $query->leftJoin(
        'field_data_field_helpertype_description', 
        'field_helpertype_description', 
        'field_helpertype_description.entity_id = n.nid');
    $query->addField(
        'field_helpertype_description', 
        'field_helpertype_description_value', 
        'field_helpertype_description'
    );
    
    // field_holiday_date
    $query->leftJoin(
        'field_data_field_helpertype_jurisdictional', 
        'field_helpertype_jurisdictional', 
        'field_helpertype_jurisdictional.entity_id = n.nid');
    $query->addField(
        'field_helpertype_jurisdictional', 
        'field_helpertype_jurisdictional_value', 
        'field_helpertype_jurisdictional'
    );
    return $query;
  }


}