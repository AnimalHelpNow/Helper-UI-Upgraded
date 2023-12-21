<?php

namespace Drupal\anh_migrate\Plugin\migrate\source;

use Drupal\anh_migrate\Plugin\migrate\source\AdvancedNodeComplete;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Drupal 7 Fieldable Pane source from database.
 *
 * For available configuration keys, refer to the parent class.
 *
 * @MigrateSource(
 *   id = "d7_helper_node",
 *   source_module = "node"
 * )
 */
class Helper extends AdvancedNodeComplete {
  
  /*protected $addedFields = [
    'field_animalgroup_id' => [
      'field_animalgroup_id_nid'
    ],
    'field_helper_juris_type_id' => [
      'field_helper_juris_type_id_nid'
    ],
    // 'field_jurisdictional_helper' => [
    //   'field_jurisdictional_helper_nid'
    // ],
    'field_jurisdiction_area_id' => [
      'field_jurisdiction_area_id_nid'
    ],
    'field_location_level_2_id' => [
      'field_location_level_2_id_nid'
    ],
    'field_country_id' => [
      'field_country_id_nid'
    ],
    'field_local_jurisdiction_id' => [
      'field_local_jurisdiction_id_nid'
    ],
    'field_location_level_1_id' => [
      'field_location_level_1_id_nid'
    ]
  ];*/

  public function query() {
    $query = parent::query();
    $query->groupBy('n.nid'); 
    return $query;
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

  
  
  public function getEntityTitleResults($table, $nid, $fields, $type = NULL) {

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
      $return = $record->title;
    }

    return $return;

  }
  
  public function getHoursResults($table, $nid, $fields, $type = NULL) {

    $return = [];

    $result = $this->getDatabase()->query('
	  SELECT
		node.title,flo.*
	  FROM
		{' . $table . '} flo
	  INNER JOIN `node` ON node.nid=flo.entity_id
	  WHERE
        flo.entity_id = :nid
    ', [':nid' => $nid]);
    foreach ($result as $record) {
      $return[] = ['day'=>$record->field_hours_available_day, 'starthours'=>$record->field_hours_available_starthours, 'endhours'=>$record->field_hours_available_endhours, 'comment'=>$record->field_hours_available_comment];
    }

    return $return;

  }


  public function getUserResults($table, $nid, $fields, $type = NULL) {

    $return = '';

    $result = $this->getDatabase()->query('
	  SELECT
		node.title,flo.' . $fields . ' as return_val
	  FROM
		{' . $table . '} flo
	  INNER JOIN `node` ON node.uid=flo.uid	
	  WHERE
    node.nid = :nid
    ', [':nid' => $nid]);
    foreach ($result as $record) {

      $users = \Drupal::entityQuery('user')
        ->condition('name', $record->return_val)
        ->execute();

      foreach ($users as $user) {
        $return = $user;
      }
    }

    return $return;

  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
	  
    parent::prepareRow($row);
    // Example: for setting the source property
    // $source = $row->getSourceProperty();

    $nid = $row->getSourceProperty('nid');
    //$row->setSourceProperty('field_jurisdiction_area_id', $this->getEntityResults("field_data_field_jurisdiction_area_id", $nid, 'field_jurisdiction_area_id_nid')); 
	$row->setSourceProperty('field_helper_juris_type_id', $this->getEntityResults("field_data_field_helper_juris_type_id", $nid, 'field_helper_juris_type_id_nid'));
	$row->setSourceProperty('field_helpertype_id', $this->getEntityResults("field_data_field_helpertype_id", $nid, 'field_helpertype_id_nid'));
	
	
	$row->setSourceProperty('field_timezone', $this->getResults("field_data_field_timezone", $nid, 'field_timezone_value'));	
	
	$country_code=$this->getEntityTitleResults("field_data_field_country_id", $nid, 'field_country_id_nid');
    
	$locality=$this->getEntityTitleResults("field_data_field_location_level_2_id", $nid, 'field_location_level_2_id_nid');
	$administrative_area=$this->getEntityTitleResults("field_data_field_location_level_1_id", $nid, 'field_location_level_1_id_nid');
	
	$locality=str_replace($administrative_area,"",$locality);
	$locality=str_replace("-City-","",$locality);
	
	$administrative_area=str_replace($country_code,"",$administrative_area);
	$administrative_area=str_replace("-State-","",$administrative_area);
	
	$field_physical_address=[
		'country_code'=>$this->getEntityTitleResults("field_data_field_country_id", $nid, 'field_country_id_nid'),
		'address_line1'=>$this->getResults("field_data_field_streetaddress_pub", $nid, 'field_streetaddress_pub_value'),
		'postal_code'=>$this->getResults("field_data_field_zipcode_pub", $nid, 'field_zipcode_pub_value'),
		'locality'=>$locality,
		'administrative_area'=>$administrative_area,
	];
	
	$row->setSourceProperty('field_physical_address', $field_physical_address);
	
	
	$field_mailing_address=[
		'country_code'=>$this->getEntityTitleResults("field_data_field_country_id", $nid, 'field_country_id_nid'),
		'address_line1'=>$this->getResults("field_data_field_mailaddress1", $nid, 'field_mailaddress1_value'),
		'address_line2'=>$this->getResults("field_data_field_mailaddress2", $nid, 'field_mailaddress2_value'),
		'postal_code'=>$this->getResults("field_revision_field_mailzip", $nid, 'field_mailzip_value'),
		'administrative_area'=>$this->getResults("field_data_field_mailstate", $nid, 'field_mailstate_value'),
		'locality'=>$this->getResults("field_data_field_mailcity", $nid, 'field_mailcity_value'),
	];
	
	$row->setSourceProperty('field_mailing_address', $field_mailing_address);
	
	$row->setSourceProperty('field_hours_available', $this->getHoursResults("field_data_field_hours_available", $nid, 'field_hours_available_day'));
	
	$row->setSourceProperty('field_notes_internal', $this->getResults("field_data_field_notes_internal", $nid, 'field_notes_internal_value'));
	$row->setSourceProperty('field_alterphone_pub', $this->getResults("field_data_field_alterphone_pub", $nid, 'field_alterphone_pub_value'));
	$row->setSourceProperty('field_animal_notes', $this->getResults("field_data_field_animal_notes", $nid, 'field_animal_notes_value'));
	
  $row->setSourceProperty('field_country_id_nid', $this->getEntityResults("field_data_field_country_id", $nid, 'field_country_id_nid'));
  $row->setSourceProperty('field_local_jurisdiction_id_nid', $this->getEntityResults("field_data_field_local_jurisdiction_id", $nid, 'field_local_jurisdiction_id_nid'));

  $row->setSourceProperty('field_location_level_1_id_nid', $this->getEntityResults("field_data_field_location_level_1_id", $nid, 'field_location_level_1_id_nid'));

  $row->setSourceProperty('field_location_level_2_id_nid', $this->getEntityResults("field_data_field_location_level_2_id", $nid, 'field_location_level_2_id_nid'));
	
  $row->setSourceProperty('field_animalgroup_id_nid', $this->getEntityResultsArray("field_data_field_animalgroup_id", $nid, 'field_animalgroup_id_nid'));

  $row->setSourceProperty('uid', $this->getUserResults("users", $nid, 'name'));

  $row->setSourceProperty('field_dnd_reason', $this->getResults("field_data_field_dnd_reason", $nid, 'field_dnd_reason_value'));
  $row->setSourceProperty('field_smallmammals', $this->getResults("field_data_field_smallmammals", $nid, 'field_smallmammals_value'));
  
  $row->setSourceProperty('field_jurisdictional_helper_nid', $this->getResults("field_data_field_jurisdictional_helper", $nid, 'field_jurisdictional_helper_value'));
  $row->setSourceProperty('field_helper_juris_type_id_nid', $this->getEntityResults("field_data_field_helper_juris_type_id", $nid, 'field_helper_juris_type_id_nid'));
  $row->setSourceProperty('field_jurisdiction_area_id_nid', $this->getEntityResults("field_data_field_jurisdiction_area_id", $nid, 'field_jurisdiction_area_id_nid'));
  
  $row->setSourceProperty('field_contactsuffix', $this->getResults("field_data_field_contactsuffix", $nid, 'field_contactsuffix_value'));
  $row->setSourceProperty('field_contacttitle', $this->getResults("field_data_field_contacttitle", $nid, 'field_contacttitle_value'));  
  $row->setSourceProperty('field_donotdisplay', $this->getResults("field_data_field_donotdisplay", $nid, 'field_donotdisplay_value'));
  
  $row->setSourceProperty('revision_default', 0);

    // Example:
    $data = $row->getSource();
	//print_r($row);
	//die;
    
    return $row;
  }
  
  
  
  
  
  
  /**
   * Group Query Helper - overcomes too many joins
   */
  protected function addOnQueryHelperbyTitle(&$row, $nid, $fields) {
    set_time_limit(3000);
    // Join each field
    foreach($fields as $field_id => $field_value) {
		
		$connection = $this->useSourceDb();
		$query = $connection->select('node', 'n');
		$query->condition('n.nid', $nid, '=');
		$query->leftJoin("field_data_{$field_id}", $field_id, "{$field_id}.entity_id = n.nid");
		$query->leftJoin("node", "n2", $field_id.".".$field_value." = n2.nid");
		$query->addField("n2", "title", "node_title");
		$results = $query->execute()->fetchAllAssoc();
		$record = array_shift($results);
		
		
		$connection_2 = $this->useTargetDb();
		$query_2 = $connection_2->select('node_field_data', 'n');
		$query_2->addField("n", "nid", "nid");
		$query_2->condition('n.title', $record->node_title, '=');
		/*$query_2->condition('n.type', 'helper', '=');*/
		$results_2 = $query_2->execute()->fetchAllAssoc();
		$record_2 = array_shift($results_2);		
		$row->setSourceProperty($field_id, $record_2->nid);
    }
    
    return $row;
  }

  /**
   * Group Query Helper - overcomes too many joins
   */
  protected function addOnQueryHelper(&$row, $nid, $fields) {
    set_time_limit(3000);
    $connection = $this->useSourceDb();
    $query = $connection->select('node', 'n');
    $query->fields('n', ['nid']);

    $query->condition('n.nid', $nid, '=');

    // Join each field
    foreach($fields as $field_id => $field_value) {
      $query->join("field_data_{$field_id}", $field_id, "{$field_id}.entity_id = n.nid");
      $query->addField($field_id, $field_value, $field_id);
      $results = $query->execute()->fetchAll();
      $data = [];
      foreach($results as $record) {
        $data[] = $record->{$field_id};
      }
      $row->setSourceProperty($field_id, $data);
    }
    
    $connection = $this->useTargetDb();
    
    return $row;
  }


  /**
   * Use Migration target database.
   */
  protected function useSourceDb() {
    Database::setActiveConnection('d7');
    return Database::getConnection();
  }

  /**
   * Use Site database.
   */
  protected function useTargetDb() {
    Database::setActiveConnection('default');
    return Database::getConnection();
  }

}
