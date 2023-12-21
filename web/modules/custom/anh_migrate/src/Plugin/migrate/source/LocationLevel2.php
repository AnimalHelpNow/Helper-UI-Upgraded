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
 *   id = "d7_location_level2_node",
 *   source_module = "node"
 * )
 */
class LocationLevel2 extends AdvancedNodeComplete { 

  public function query() {
    $query = parent::query();
    
    // field_geoposition
    $query->leftJoin(
        'field_data_field_geoposition', 
        'field_geoposition', 
        'field_geoposition.entity_id = n.nid');
    $query->addField(
        'field_geoposition', 
        'field_geoposition_value', 
        'field_geoposition'
    );
    
    // field_location_level_1_id
    $query->leftJoin(
        'field_data_field_location_level_1_id', 
        'field_location_level_1_id', 
        'field_location_level_1_id.entity_id = n.nid');
    $query->addField(
        'field_location_level_1_id', 
        'field_location_level_1_id_nid', 
        'field_location_level_1_id'
    );
    
    // field_location_level_2_name
    $query->leftJoin(
        'field_data_field_location_level_2_name', 
        'field_location_level_2_name', 
        'field_location_level_2_name.entity_id = n.nid');
    $query->addField(
        'field_location_level_2_name', 
        'field_location_level_2_name_value', 
        'field_location_level_2_name'
    );
    return $query;
  }

	public function prepareRow(Row $row) {
	  
		parent::prepareRow($row);
		
		$nid = $row->getSourceProperty('nid');
		
		$row->setSourceProperty('field_country_id', $this->getEntityResultsArray("field_data_field_country_id", $nid, 'field_country_id_nid'));
		
		$row->setSourceProperty('field_ll2_jurisdiction_type_id', $this->getEntityResultsArray("field_data_field_ll2_jurisdiction_type_id", $nid, 'field_ll2_jurisdiction_type_id_nid'));
		
		$row->setSourceProperty('field_location_level_1_id', $this->getEntityResultsArray("field_data_field_location_level_1_id", $nid, 'field_location_level_1_id_nid'));
		
		// Example:
		$data = $row->getSource();
		
		//print_r($row);
		//die;
		
		return $row;
	}
  
  
  
	public function getEntityResultsArray($table, $nid, $fields, $type = NULL) {

		$return = [];

		$result = $this->getDatabase()->query('
		  SELECT
			node.title,flo.' . $fields . ' as return_val
		  FROM
			{' . $table . '} flo
		  INNER JOIN `node` ON node.nid=flo.' . $fields . '	
		  WHERE
			flo.entity_id = :nid
		', [':nid' => $nid]);
		foreach ($result as $record) {

		  $term = \Drupal::entityQuery('node')
			->condition('title', $record->title)
			->execute();

		  foreach ($term as $_term) {
			$return[] = $_term;
		  }
		}

		return $return;

	}


}