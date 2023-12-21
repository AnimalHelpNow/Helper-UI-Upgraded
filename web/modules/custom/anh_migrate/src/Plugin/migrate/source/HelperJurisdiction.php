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
 *   id = "d7_helper_jurisdiction_node",
 *   source_module = "node"
 * )
 */
class HelperJurisdiction extends AdvancedNodeComplete {
    
    public function query() {
        $query = parent::query();

        //field_jurisdiction_id
        $query->join('field_data_field_jurisdiction_id', 'jur_id', 'jur_id.entity_id = n.nid');
        $query->join('node', 'jurisdiction', 'jurisdiction.nid = jur_id.field_jurisdiction_id_nid');
        $query->addField('jurisdiction', 'title', 'jurisdiction_node');

        //body
        $query->leftJoin(
            'field_data_body', 
            'body', 
            'body.entity_id = n.nid');
        $query->addField(
            'body', 
            'body_value', 
            'body'
        );

        //field_country_abbrev
        $query->leftJoin(
            'field_data_field_country_abbrev', 
            'field_country_abbrev', 
            'field_country_abbrev.entity_id = n.nid');
        $query->addField(
            'field_country_abbrev', 
            'field_country_abbrev_value', 
            'field_country_abbrev'
        );

        //field_delete_date
        $query->leftJoin(
            'field_data_field_delete_date', 
            'field_delete_date', 
            'field_delete_date.entity_id = n.nid');
        $query->addField(
            'field_delete_date', 
            'field_delete_date_value', 
            'field_delete_date'
        );

        //field_helper_id
        $query->leftJoin(
            'field_data_field_helper_id', 
            'field_helper_id', 
            'field_helper_id.entity_id = n.nid');
        $query->addField(
            'field_helper_id', 
            'field_helper_id_nid', 
            'field_helper_id'
        );

        //field_ja_type
        $query->leftJoin(
            'field_data_field_ja_type', 
            'field_ja_type', 
            'field_ja_type.entity_id = n.nid');
        $query->addField(
            'field_ja_type', 
            'field_ja_type_value', 
            'field_ja_type'
        );

        //field_jurisdiction_text_temp
        $query->leftJoin(
            'field_data_field_jurisdiction_text_temp', 
            'field_jurisdiction_text_temp', 
            'field_jurisdiction_text_temp.entity_id = n.nid');
        $query->addField(
            'field_jurisdiction_text_temp', 
            'field_jurisdiction_text_temp_value', 
            'field_jurisdiction_text_temp'
        );

        //field_location_level_1_abbrev
        $query->leftJoin(
            'field_data_field_location_level_1_abbrev', 
            'field_location_level_1_abbrev', 
            'field_location_level_1_abbrev.entity_id = n.nid');
        $query->addField(
            'field_location_level_1_abbrev', 
            'field_location_level_1_abbrev_value', 
            'field_location_level_1_abbrev'
        );

        //field_primary_key
        $query->leftJoin(
            'field_data_field_primary_key', 
            'field_primary_key', 
            'field_primary_key.entity_id = n.nid');
        $query->addField(
            'field_primary_key', 
            'field_primary_key_value', 
            'field_primary_key'
        );

        return $query;
    }
	
	
	public function prepareRow(Row $row) {
	  
		parent::prepareRow($row);
		
		$nid = $row->getSourceProperty('nid');
		$row->setSourceProperty('field_helper_id_nid', $this->getEntityResults("field_data_field_helper_id", $nid, 'field_helper_id_nid'));

		// Example:
		$data = $row->getSource();
		//print_r($row);
		//die;
		
		return $row;
	}
  
  /**
   * {@inheritdoc}
   */
  public function getEntityResults($table, $nid, $fields, $type = NULL) {

    $return = '';

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
        $return = $_term;
      }
    }

    return $return;

  }

  
    
}