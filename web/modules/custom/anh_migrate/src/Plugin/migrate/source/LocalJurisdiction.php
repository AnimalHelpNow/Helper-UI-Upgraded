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
 *   id = "d7_local_jurisdiction_node",
 *   source_module = "node"
 * )
 */
class LocalJurisdiction extends AdvancedNodeComplete {
    public function query() {
        $query = parent::query();

        //field_display_name
        $query->leftJoin(
            'field_data_field_display_name', 
            'field_display_name', 
            'field_display_name.entity_id = n.nid');
        $query->addField(
            'field_display_name', 
            'field_display_name_value', 
            'field_display_name'
        );
		
        //field_local_jurisdiction_name
        $query->leftJoin(
            'field_data_field_local_jurisdiction_name', 
            'field_local_jurisdiction_name', 
            'field_local_jurisdiction_name.entity_id = n.nid');
        $query->addField(
            'field_local_jurisdiction_name', 
            'field_local_jurisdiction_name_value', 
            'field_local_jurisdiction_name'
        );
		
        return $query;
    }
  
	public function prepareRow(Row $row) {
	  
		parent::prepareRow($row);
		
		$nid = $row->getSourceProperty('nid');
		$row->setSourceProperty('field_local_jurisdiction_type_id', $this->getEntityResultsArray("field_data_field_local_jurisdiction_type_id", $nid, 'field_local_jurisdiction_type_id_nid'));
		$row->setSourceProperty('field_location_level_1_id', $this->getEntityResultsArray("field_data_field_location_level_1_id", $nid, 'field_location_level_1_id_nid'));
		$row->setSourceProperty('field_country_id', $this->getEntityResultsArray("field_data_field_country_id", $nid, 'field_country_id_nid'));
		$row->setSourceProperty('field_geoposition', $this->getResults("field_data_field_geoposition", $nid, 'field_geoposition_value'));
		
		// Example:
		$data = $row->getSource();
		
		//print_r($row);
		//die;
		
		return $row;
	}
	
	/**
   * {@inheritdoc}
   */
  public function getResults($table, $nid, $fields, $type = NULL) {

    if ($type == "array") {
      $return = [];
    }
    else {
      $return = '';
    }

    $result = $this->getDatabase()->query('SELECT flo.' . $fields . ' as return_val FROM {' . $table . '} flo WHERE flo.entity_id = :nid ', [':nid' => $nid]);
    foreach ($result as $record) {

      if ($type == "date") {
        $return = date("Y-m-d", strtotime($record->return_val));
      }
      elseif ($type == "array") {
        $return[] = $record->return_val;
      }
      else {
        $return = $record->return_val;
      }

    }

    return $return;

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