<?php

namespace Drupal\anh_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Drupal 7 file source from database.
 *
 * @MigrateSource(
 *   id = "d7_file_by_type",
 *   source_module = "file"
 * )
 */
class FileByType extends File {

  /**
   * The public file directory path.
   *
   * @var string
   */
  protected $publicPath;

  /**
   * The private file directory path, if any.
   *
   * @var string
   */
  protected $privatePath;

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();

    // Filter by file type, if configured.
    if (isset($this->configuration['type'])) {
      $query->condition('f.type', $this->configuration['type']);
    }

    // Get the alt text, if configured.
    if (isset($this->configuration['get_alt'])) {
      $alias = $query->addJoin('left', 'field_data_field_file_image_alt_text', 'alt', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_file_image_alt_text_value', 'alt');
    }

    // Get the title text, if configured.
    if (isset($this->configuration['get_title'])) {
      $alias = $query->addJoin('left', 'field_data_field_file_image_title_text', 'title', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_file_image_title_text_value', 'title');
    }

    // Get the caption, if configured.
    if (isset($this->configuration['get_caption'])) {
      $alias = $query->addJoin('left', 'field_data_field_image_caption', 'caption', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_image_caption_value', 'caption');
    }

    // Get the credit, if configured.
    if (isset($this->configuration['get_credit'])) {
      $alias = $query->addJoin('left', 'field_data_field_image_attribution', 'credit', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_image_attribution_value', 'credit');
    }

    // Get the government value, if configured.
    if (isset($this->configuration['get_government'])) {
      $alias = $query->addJoin('left', 'field_data_field_image_government', 'government', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_image_government_value', 'government');
    }

    // Get the alignment value, if configured.
    if (isset($this->configuration['get_alignment'])) {
      $alias = $query->addJoin('left', 'field_data_field_image_alignment', 'alignment', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_image_alignment_value', 'alignment');
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = parent::fields();
    if (isset($this->configuration['type'])) {
      $fields['type'] = $this->t('The type of file.');
    }
    if (isset($this->configuration['get_alt'])) {
      $fields['alt'] = $this->t('Alt text of the file (if present)');
    }
    if (isset($this->configuration['get_title'])) {
      $fields['title'] = $this->t('Title text of the file (if present)');
    }
    if (isset($this->configuration['get_caption'])) {
      $fields['caption'] = $this->t('Caption (if present)');
    }
    if (isset($this->configuration['get_credit'])) {
      $fields['credit'] = $this->t('Credit (if present)');
    }
    if (isset($this->configuration['get_categories'])) {
      $fields['categories'] = $this->t('Category term ids (if present)');
    }
    if (isset($this->configuration['get_government'])) {
      $fields['government'] = $this->t('Government (if present)');
    }
    if (isset($this->configuration['get_alignment'])) {
      $fields['alignment'] = $this->t('Alignment (if present)');
    }
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (isset($this->configuration['get_categories'])) {
      // Build an array of categories for the file that can be processed.
      $categories = [];
      parent::prepareRow($row);
      $query = $this->query();
      $query->condition('f.fid', $row->getSourceProperty('fid'));
      $alias = $query->addJoin('left', 'field_data_field_image_category', 'categories', 'f.fid = %alias.entity_id');
      $query->addField($alias, 'field_image_category_tid', 'categories');
      $results = $query->execute();
      foreach ($results as $result) {
        if ($category = $result['categories']) {
          $categories[$category]['tid'] = $category;
        }
      }
      $row->setSourceProperty('categories', $categories);
      return parent::prepareRow($row);
    }
  }

}
