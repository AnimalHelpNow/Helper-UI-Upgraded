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
 *   id = "d7_country_node",
 *   source_module = "node"
 * )
 */
class Country extends AdvancedNodeComplete { 

  protected $addedFields = [
    'field_address_ll1_juris_type_id' => [
        'field_address_ll1_juris_type_id_nid'
    ],
    'field_address_ll2_juris_type_id' => [
        'field_address_ll2_juris_type_id_nid'
    ],
    'field_country_name' => [
        'field_country_name_value'
    ],
    'field_uniqueid' => [
        'field_uniqueid_value'
    ]
  ];

  public function query() {
    $query = parent::query();
    

    return $query;
  }

	public function prepareRow(Row $row) {
	  
		parent::prepareRow($row);
		
		$nid = $row->getSourceProperty('nid');
		
		$row->setSourceProperty('field_country_id', $this->getEntityResultsArray("field_data_field_country_id", $nid, 'field_country_id_nid'));
		
		$row->setSourceProperty('field_address_ll1_juris_type_id_nid', $this->getEntityResultsArray("field_data_field_address_ll1_juris_type_id", $nid, 'field_address_ll1_juris_type_id_nid'));
		
		$row->setSourceProperty('field_address_ll2_juris_type_id_nid', $this->getEntityResultsArray("field_data_field_address_ll2_juris_type_id", $nid, 'field_address_ll2_juris_type_id_nid'));
		
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