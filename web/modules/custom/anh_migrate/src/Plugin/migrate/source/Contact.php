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
 *   id = "d7_contact_node",
 *   source_module = "node"
 * )
 */
class Contact extends AdvancedNodeComplete {
  
    protected $addedFields = [
        'field_lic_alterphone' => ['field_lic_alterphone_value'],
        'field_lic_alterphonetext' => ['field_lic_alterphonetext_value'],
        'field_lic_email' => ['field_lic_email_value'],
        'field_lic_federallicense' => ['field_lic_federallicense_value'],
        'field_lic_firstname' => [ 'field_lic_firstname_value' ],
        'field_lic_lastname' => ['field_lic_lastname_value'],
        'field_lic_mailaddress' => ['field_lic_mailaddress_value'],
        'field_lic_middlename' => ['field_lic_middlename_value'],
        'field_lic_namesuffix' => ['field_lic_namesuffix_value'],
        'field_lic_nametitle' => ['field_lic_nametitle_value'],
        'field_lic_primaryphone' => ['field_lic_primaryphone_value'],
        'field_lic_primaryphonetext' => ['field_lic_primaryphonetext_value'],
        'field_lic_statelicense' => ['field_lic_statelicense_value']
    ];


    public function query() {
        $query = parent::query();
        return $query;
    }
}