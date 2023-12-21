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
 *   id = "transform_date"
 * )
 *
 * @code
 * body:
 *   plugin: transform_date
 *   source: body
 * @endcode
 */
class TransformDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
   return date('Y-m-d', strtotime($value));
  }

}
