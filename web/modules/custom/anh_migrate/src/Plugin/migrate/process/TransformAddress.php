<?php

namespace Drupal\anh_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform transforms on WYSIWYG HTML from d7 to d9 syntax using the service.
 *
 * @MigrateProcessPlugin(
 *   id = "transform_address"
 * )
 *
 * @code
 * body:
 *   plugin: transform_address
 *   source: body
 * @endcode
 */
class TransformAddress extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Formatted long text may be passed as an array with value and format.
    if (is_array($value)) {
        $address = isset($value['country']) ? $value : (isset($value[0]['country']) ? array_shift($value) : FALSE);
        
        if ($address) {
            $new_address = [
                'country_code' => $address['country'],
                'locality' => $address['locality'],
                'postal_code' => $address['postal_code'],
                'address_line1' => $address['thoroughfare'],
                'administrative_area' => $address['administrative_area'],
            ];
            return $new_address;
        }
    }
    return [];
  }

}
