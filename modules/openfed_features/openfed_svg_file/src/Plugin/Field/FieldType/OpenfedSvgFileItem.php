<?php

namespace Drupal\openfed_svg_file\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\file\Plugin\Field\FieldType\FileItem;

/**
 * Plugin implementation of the 'file' field type.
 *
 * @FieldType(
 *   id = "openfed_svg_file",
 *   label = @Translation("Openfed SVG File"),
 *   description = @Translation("This field stores the ID of a file as an integer value."),
 *   category = @Translation("Reference"),
 *   default_widget = "openfed_svg_file_widget",
 *   default_formatter = "openfed_svg_file_formatter",
 *   list_class = "\Drupal\file\Plugin\Field\FieldType\FileFieldItemList",
 *   constraints = {"ReferenceAccess" = {}, "FileValidation" = {}}
 * )
 */
class OpenfedSvgFileItem extends FileItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'type' => '',
      'width' => '',
      'height' => '',
      'title' => '',
      'alt' => '',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    $settings = [
        'file_extensions' => 'svg',
      ] + parent::defaultFieldSettings();

    unset($settings['description_field']);
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'target_id' => [
          'description' => 'The ID of the file entity.',
          'type' => 'int',
          'unsigned' => TRUE,
        ],
        'type' => [
          'description' => 'The display type.',
          'type' => 'varchar',
          'length' => 16,
        ],
        'width' => [
          'description' => 'The image width.',
          'type' => 'int',
          'size' => 'normal',
        ],
        'height' => [
          'description' => 'The image height.',
          'type' => 'int',
          'size' => 'normal',
        ],
        'title' => [
          'description' => "Title for the svg 'title' attribute.",
          'type' => 'varchar',
          'length' => 512,
        ],
        'alt' => [
          'description' => "Alternative text for the svg 'alt' attribute.",
          'type' => 'varchar',
          'length' => 512,
        ],
      ],
      'indexes' => [
        'target_id' => ['target_id'],
      ],
      'foreign keys' => [
        'target_id' => [
          'table' => 'file_managed',
          'columns' => ['target_id' => 'fid'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['type'] = DataDefinition::create('string')
      ->setLabel(t('Display type'));
    $properties['width'] = DataDefinition::create('integer')
      ->setLabel(t('Image width'));
    $properties['height'] = DataDefinition::create('integer')
      ->setLabel(t('Image height'));
    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('SVG title attribute'));
    $properties['alt'] = DataDefinition::create('string')
      ->setLabel(t('SVG alt attribute'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    // Get base form from FileItem.
    $element = parent::fieldSettingsForm($form, $form_state);

    // Remove the description option.
    unset($element['description_field']);

    // Make file extension non-editable.
    $element['file_extensions']['#disabled'] = TRUE;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isDisplayed() {
    // SVG items do not have per-item visibility settings.
    return TRUE;
  }

}
