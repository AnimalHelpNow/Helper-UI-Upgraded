<?php

namespace Drupal\helperui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Defines HelperuiController class.
 */
class HelperuiController extends ControllerBase {

  /**
   * Updates Helper Jurisdiction records.
   */
  public function helperJurisdiction($node) {
    // Algorithm:
    // Given a Helper nid, get the Helper record
    // Get the jurisdiction_area_id from the Helper record
    // Get the Jurisdiction Area record
    // Get array of jurisdiction_id from the Jurisdiction Area record
    // Get all Helper Jurisdiction records for the Helper nid
    // For each, check if the jurisdiction_id is in the array from JA record
    // If not, delete the Helper Jurisdiction record
    // Make new array of jurisdiction_id
    // from all remaining Helper Jurisdiction records
    // for the given Helper nid
    // Loop through jurisdiction_id array from the Jurisdiction Area record
    // For each, check if the jurisdiction_id is in the array from HJ records
    // If not, add the Helper Jurisdiction record.
    if (empty($node)) {
      return;
    }

    $i = 0;
    $j = 0;
    $k = 0;

    // Given a Helper nid, get the Helper record.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'helper')
      ->condition('status', 1)
      ->condition('nid', $node);
    $helperIds = $query->execute();

    if (!empty($helperIds)) {
      $helper_entities = Node::loadMultiple($helperIds);
      if ($helper_entities) {
        foreach ($helper_entities as $key => $helper) {
          $helper_nid = $helper->id();

          // Get the jurisdiction_area_id from the Helper record.
          $jurisdiction_area_nid = (!empty($helper->field_jurisdiction_area_id->target_id)) ?
            $helper->field_jurisdiction_area_id->target_id : 0;
        }
      }
    }

    // Get the Jurisdiction Area record.
    if ($jurisdiction_area_nid > 0) {
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'jurisdiction_area')
        ->condition('status', 1)
        ->condition('nid', $jurisdiction_area_nid);
      $ja_ids = $query->execute();

      if (!empty($ja_ids)) {
        $ja_entities = Node::loadMultiple($ja_ids);
        if ($ja_entities) {
          foreach ($ja_entities as $key => $ja) {
            $ja_type = $ja->field_ja_type->value;
            $jurisdiction_nids = $ja_entity->field_jurisdiction_id;
          }
        }
      }
    }
    else {
      $jurisdiction_nids = [];
    }

    // Given a Helper nid, get all existing Helper Jurisdiction records.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'helper_jurisdiction')
      ->condition('status', 1)
      ->condition('field_helper_id', $node);
    $hj_ids = $query->execute();

    if (!empty($hj_ids)) {
      $hj_entities = Node::loadMultiple($hj_ids);
      if ($hj_entities) {
        foreach ($hj_entities as $key => $hj) {
          $i++;
          $hj_nid = $hj->id();

          $hj_jurisdiction_id = $hj_entity->field_jurisdiction_id;
          // Check if the jurisdiction nid is in the array from JA record.
          $ja_key = array_search($hj_jurisdiction_id, $jurisdiction_nids);

          // If not found, delete the Helper Jurisdiction record
          // Need to use === to check for False, in case it is found in
          // the jurisdiction_nids array at key = 0.
          if ($ja_key === FALSE) {
            $node = Node::load($hj_nid);
            if ($node) {
              $node->delete();
            }

            \Drupal::messenger(t("Deleted Helper Jurisdiction node nid=@nid", ['@nid' => $hj_nid]));
          }
        }
      }
    }

    // Make new array of jurisdiction_id
    // from all remaining Helper Jurisdiction records
    // for the given Helper nid
    // Initialize new array.
    $hj_jurisdiction_nids = [];

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'helper_jurisdiction')
      ->condition('status', 1)
      ->condition('field_helper_id', $node);
    $hj_remaining_nodes = $query->execute();

    if (!empty($hj_remaining_nodes)) {
      $hj_remaining_entities = Node::loadMultiple($hj_remaining_nodes);
      if ($hj_remaining_entities) {
        foreach ($hj_remaining_entities as $key => $hj) {
          $j++;
          // Get the nid of the Helper Jurisdiction record.
          $hj_remaining_nid = $hj->id();
          // Get the jurisdiction nid (as array([nid]=>999999))
          $hj_remaining_jurisdiction_id = $hj->field_jurisdiction_id;
          // Add the array element.
          $hj_jurisdiction_nids[$hj_remaining_nid] = $hj_remaining_jurisdiction_id;
        }
      }
    }

    // Loop through jurisdiction_id array from the JA record
    // For each, check if the jurisdiction_id is in
    // the new array from HJ records
    // If not, add the Helper Jurisdiction record.
    foreach ($jurisdiction_nids as $key => $jurisdiction_item) {
      if (!empty($jurisdiction_item)) {
        $k++;
        $jurisdiction_nid = $jurisdiction_item->id();

        // Check if jurisdiction_nid is in the list of HJ records.
        $hj_key = array_search($jurisdiction_item, $hj_jurisdiction_nids);

        // If not found, add the Helper Jurisdiction record
        // Need to use === to check for False, in case it is found in
        // the hj_jurisdiction_nids array at key = 0.
        if ($hj_key === FALSE) {
          // This jurisdiction_nid is not in the list of HJ records
          // so add a Heper Jurisdiction record
          // print "should add jurisdiction_nid=".$jurisdiction_nid."<br>";
          // Start by getting the jurisdiction record
          // The bundle will be set based on the ja_type which
          // was saved from the Jurisdiction Area record above.
          if (empty($ja_type)) {
            // Default to County = 4.
            $ja_type = 4;
          }

          $bundle = "";
          // Get record of appropriate geographical type.
          switch ($ja_type) {
            case 4:
              // County.
              $bundle = 'local_jurisdiction';
              break;

            case 3:
              // City.
              $bundle = 'location_level_2';
              break;

            case 2:
              // State.
              $bundle = 'location_level_1';
              break;

            case 1:
              // Country.
              $bundle = 'country';
              break;

            default:
              $ja_type = 4;
              $bundle = 'local_jurisdiction';
              break;
          }

          $query = \Drupal::entityQuery('node')
            ->condition('type', $bundle)
            ->condition('status', 1)
            ->condition('nid', $jurisdiction_nid);
          $j_ids = $query->execute();

          if (!empty($j_ids)) {
            $j_entities = Node::loadMultiple($j_ids);
            if ($j_entities) {
              foreach ($j_entities as $key => $jurisdiction) {
                // Get the title from the Jurisdiction record.
                $j_title = $jurisdiction->title->value;

                // Add a Helper Jurisdiction node.
                $node = Node::create([
                  'type' => 'helper_jurisdiction',
                  'title' => 'test title',
                  'uid' => \Drupal::currentUser()->id(),
                  'status' => 1,
                  'promote' => 0,
                  'comment' => 1,
                ]);

                $helper_nid_string = (string) $helper_nid;
                $jurisdiction_nid_string = (string) $jurisdiction_nid;
                $helper_jurisdiction_title = $helper_nid_string . "-" . $jurisdiction_nid_string;

                if (strlen($jurisdiction_nid_string) > 4) {
                  $jurisdiction_nid_string = substr($jurisdiction_nid_string, -4, 4);
                }
                $primary_key_string = $helper_nid_string . $jurisdiction_nid_string;
                $primary_key = intval($primary_key_string);
                $string_parse = explode('-', $j_title);
                // State.
                $helper_jurisdiction_state = $string_parse[2];
                // Country.
                $helper_jurisdiction_country = $string_parse[0];

                // Entity reference field.
                $node->field_helper_id->target_id = $helper_nid;
                $node->field_jurisdiction_id->target_id = $jurisdiction_nid;

                $node->title = $helper_jurisdiction_title;

                // List field.
                $node->field_ja_type->value = $ja_type;

                // Integer field.
                $node->field_primary_key->value = $primary_key;

                // Text field.
                $node->field_location_level_1_abbrev->value = $helper_jurisdiction_state;
                $node->field_country_abbrev->value = $helper_jurisdiction_country;

                $node->save();
                \Drupal::messenger(t("Added Helper Jurisdiction for @nid", ['@nid' => $jurisdiction_nid]));
              }
            }
          }
        }
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Add/update Helper Jurisdiction Nodes'),
    ];
  }

  /**
   * Call helperJurisdiction function when Jurisdiction Area record is changed.
   */
  public function helperJurisdictionJa($node) {
    if (empty($node)) {
      return;
    }

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'helper')
      ->condition('status', 1)
      ->condition('field_jurisdiction_area_id', $node);
    $helperIds = $query->execute();

    if (!empty($helperIds)) {
      $helper_entities = Node::loadMultiple($helperIds);
      if (!empty($helper_entities)) {
        foreach ($helper_entities as $key => $helper) {
          $helper_nid = $helper->id();

          // Run the helperui_helperjurisdiction function for each Helper.
          $this->helperJurisdiction($helper_nid);
        }
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => t('The Helper Jurisdiction node @nid has been updated', ['@nid' => $helper_nid]),
    ];
  }

}
