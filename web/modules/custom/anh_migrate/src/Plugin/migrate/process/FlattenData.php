<?php

namespace Drupal\anh_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform transforms on WYSIWYG HTML from d7 to d9 syntax using the service.
 *
 * @MigrateProcessPlugin(
 *   id = "transform_flat"
 * )
 *
 * @code
 * body:
 *   plugin: transform_flat
 *   source: body
 *   property: field_property
 * @endcode
 */
class FlattenData extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($this->configuration['property'])) { return NULL; }
    
    // Formatted long text may be passed as an array with value and format.
    $data = NULL;
    if (is_array($value)) {
        $data = isset($value[$this->configuration['property']]) 
            ? $value[$this->configuration['property']] : 
            (
                isset($value[0][$this->configuration['property']]) ? 
                $value[0][$this->configuration['property']] : NULL
            );
    }
    
    return $data;
  }

}
