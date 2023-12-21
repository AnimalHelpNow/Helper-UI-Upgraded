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
 *   id = "d7_helper_modify_node",
 *   source_module = "node"
 * )
 */
class HelperModify extends AdvancedNodeComplete { 
    public function query() {
        $query = parent::query();

        // field_contactsuffix
        $query->leftJoin(
            'field_data_field_contactsuffix', 
            'field_contactsuffix', 
            'field_contactsuffix.entity_id = n.nid');
        $query->addField(
            'field_contactsuffix', 
            'field_contactsuffix_value', 
            'field_contactsuffix'
        );

        // field_contacttitle
        $query->leftJoin(
            'field_data_field_contacttitle', 
            'field_contacttitle', 
            'field_contacttitle.entity_id = n.nid');
        $query->addField(
            'field_contacttitle', 
            'field_contacttitle_value', 
            'field_contacttitle'
        );

        // field_lastmodified
        $query->leftJoin(
            'field_data_field_lastmodified', 
            'field_lastmodified', 
            'field_lastmodified.entity_id = n.nid');
        $query->addField(
            'field_lastmodified', 
            'field_lastmodified_value', 
            'field_lastmodified'
        );

        // field_pk
        $query->leftJoin(
            'field_data_field_pk', 
            'field_pk', 
            'field_pk.entity_id = n.nid');
        $query->addField(
            'field_pk', 
            'field_pk_value', 
            'field_pk'
        );

        // field_update_text
        $query->leftJoin(
            'field_data_field_update_text', 
            'field_update_text', 
            'field_update_text.entity_id = n.nid');
        $query->addField(
            'field_update_text', 
            'field_update_text_value', 
            'field_update_text'
        );

        return $query;
    }
}