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
 *   id = "d7_profile_node",
 *   source_module = "node"
 * )
 */
class Profile extends AdvancedNodeComplete { 

  public function query() {
    $query = parent::query();

    // field_profile_address
    $query->leftJoin(
        'field_data_field_profile_address', 
        'field_profile_address', 
        'field_profile_address.entity_id = n.nid');
    $query->addField(
        'field_profile_address', 
        'field_profile_address_value', 
        'field_profile_address'
    );
    
    // field_profile_user
    $query->leftJoin(
        'field_data_field_profile_user', 
        'field_profile_user', 
        'field_profile_user.entity_id = n.nid');
    $query->addField(
        'field_profile_user', 
        'field_profile_user_uid', 
        'field_profile_user'
    );
    
    // field_profile_department
    $query->leftJoin(
        'field_data_field_profile_department', 
        'field_profile_department', 
        'field_profile_department.entity_id = n.nid');
    $query->addField(
        'field_profile_department', 
        'field_profile_department_value', 
        'field_profile_department'
    );

    // field_profile_email
    $query->leftJoin(
        'field_data_field_profile_email', 
        'field_profile_email', 
        'field_profile_email.entity_id = n.nid');
    $query->addField(
        'field_profile_email', 
        'field_profile_email_value', 
        'field_profile_email'
    );

    // field_profile_first_name
    $query->leftJoin(
        'field_data_field_profile_first_name', 
        'field_profile_first_name', 
        'field_profile_first_name.entity_id = n.nid');
    $query->addField(
        'field_profile_first_name', 
        'field_profile_first_name_value', 
        'field_profile_first_name'
    );

    // field_profile_job_title
    $query->leftJoin(
        'field_data_field_profile_job_title', 
        'field_profile_job_title', 
        'field_profile_job_title.entity_id = n.nid');
    $query->addField(
        'field_profile_job_title', 
        'field_profile_job_title_value', 
        'field_profile_job_title'
    );

    // field_profile_last_name
    $query->leftJoin(
        'field_data_field_profile_last_name', 
        'field_profile_last_name', 
        'field_profile_last_name.entity_id = n.nid');
    $query->addField(
        'field_profile_last_name', 
        'field_profile_last_name_value', 
        'field_profile_last_name'
    );

    // field_profile_leadership
    $query->leftJoin(
        'field_data_field_profile_leadership', 
        'field_profile_leadership', 
        'field_profile_leadership.entity_id = n.nid');
    $query->addField(
        'field_profile_leadership', 
        'field_profile_leadership_value', 
        'field_profile_leadership'
    );

    // field_profile_middle_name
    $query->leftJoin(
        'field_data_field_profile_middle_name', 
        'field_profile_middle_name', 
        'field_profile_middle_name.entity_id = n.nid');
    $query->addField(
        'field_profile_middle_name', 
        'field_profile_middle_name_value', 
        'field_profile_middle_name'
    );
    
    // field_phone_number
    $query->leftJoin(
        'field_data_field_phone_number', 
        'field_phone_number', 
        'field_phone_number.entity_id = n.nid');
    $query->addField(
        'field_phone_number', 
        'field_phone_number_value', 
        'field_phone_number'
    );
    
    // field_profile_photo
    $query->leftJoin(
        'field_data_field_profile_photo', 
        'field_profile_photo', 
        'field_profile_photo.entity_id = n.nid');
    $query->addField(
        'field_profile_photo', 
        'field_profile_photo_fid', 
        'field_profile_photo'
    );
    
    // field_profile_social_media
    $query->leftJoin(
        'field_data_field_profile_social_media', 
        'field_profile_social_media', 
        'field_profile_social_media.entity_id = n.nid');
    $query->addField(
        'field_profile_social_media', 
        'field_profile_social_media_value', 
        'field_profile_social_media'
    );

    return $query;
  }


}