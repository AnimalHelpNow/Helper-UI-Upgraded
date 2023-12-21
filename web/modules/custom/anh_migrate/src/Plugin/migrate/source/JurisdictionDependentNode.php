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
 *   id = "d7_jurisdiction_node",
 *   source_module = "node"
 * )
 */
class JurisdictionDependentNode extends NodeComplete { 

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();

    // Join from node to the field
    //$query->join('field_data_field_jurisdiction_id', 'jur_id', 'jur_id.entity_id = n.nid');
    //$query->join('node', 'jurisdiction', 'jurisdiction.nid = jur_id.field_jurisdiction_id_nid');
    //$query->addField('jurisdiction', 'title', 'jurisdiction_node');
    return $query;
  }
  
	public function prepareRow(Row $row) {
	  
		parent::prepareRow($row);
		
		$nid = $row->getSourceProperty('nid');
		$row->setSourceProperty('field_jurisdiction_id_nid', $this->getEntityResultsArray("field_data_field_jurisdiction_id", $nid, 'field_jurisdiction_id_nid'));
		$row->setSourceProperty('field_country_id_nid', $this->getEntityResultsArray("field_data_field_country_id", $nid, 'field_country_id_nid'));
		
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