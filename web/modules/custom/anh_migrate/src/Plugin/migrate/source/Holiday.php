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
 *   id = "d7_holiday_node",
 *   source_module = "node"
 * )
 */
class Holiday extends AdvancedNodeComplete { 

  public function query() {
    $query = parent::query();

    // field_delete_date
    $query->leftJoin(
        'field_data_field_delete_date', 
        'field_delete_date', 
        'field_delete_date.entity_id = n.nid');
    $query->addField(
        'field_delete_date', 
        'field_delete_date_value', 
        'field_delete_date'
    );
    
    // field_holiday_date
    $query->leftJoin(
        'field_data_field_holiday_date', 
        'field_holiday_date', 
        'field_holiday_date.entity_id = n.nid');
    $query->addField(
        'field_holiday_date', 
        'field_holiday_date_value', 
        'field_holiday_date'
    );
    
    // field_holiday_description
    $query->leftJoin(
        'field_data_field_holiday_description', 
        'field_holiday_description', 
        'field_holiday_description.entity_id = n.nid');
    $query->addField(
        'field_holiday_description', 
        'field_holiday_description_value', 
        'field_holiday_description'
    );
    return $query;
  }
  
	public function prepareRow(Row $row) {
	  
		parent::prepareRow($row);
		
		$nid = $row->getSourceProperty('nid');
		//$row->setSourceProperty('field_helper_id_nid', $this->getEntityResults("field_data_field_helper_id", $nid, 'field_helper_id_nid'));

		// Example:
		$data = $row->getSource();
		$row->setSourceProperty('field_holiday_date', date("Y-m-d", strtotime($data['field_holiday_date'][0]['value'])));
		//print_r($row);
		//die;
		
		return $row;
	}


}