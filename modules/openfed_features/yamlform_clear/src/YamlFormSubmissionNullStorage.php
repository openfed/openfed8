<?php

namespace Drupal\yamlform_clear;

use Drupal\Core\Entity\ContentEntityNullStorage;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\yamlform\YamlFormInterface;
use Drupal\yamlform\YamlFormSubmissionInterface;
use Drupal\yamlform\YamlFormSubmissionStorageInterface;

/**
 * Provides a null storage for YAML form submissions.
 */
class YamlFormSubmissionNullStorage extends ContentEntityNullStorage implements YamlFormSubmissionStorageInterface {

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::getFieldDefinitions()
   */
  public function getFieldDefinitions() {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $definitions */
    $field_definitions = $this->entityManager->getBaseFieldDefinitions('yamlform_submission');

    // For now never let any see or export the serialize YAML data field.
    unset($field_definitions['data']);

    $definitions = [];
    foreach ($field_definitions as $field_name => $field_definition) {
      $definitions[$field_name] = [
        'title' => $field_definition->getLabel(),
        'name' => $field_name,
        'type' => $field_definition->getType(),
        'target_type' => $field_definition->getSetting('target_type'),
      ];
    }

    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getMaxSerial(YamlFormInterface $yamlform) {
    return 1;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, $limit = NULL, $max_sid = NULL) {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function loadDraft(YamlFormInterface $yamlform, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotal(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getMaxSubmissionId(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstSubmission(YamlFormInterface $yamlform, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastSubmission(YamlFormInterface $yamlform, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousSubmission(YamlFormSubmissionInterface $yamlform_submission, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getNextSubmission(YamlFormSubmissionInterface $yamlform_submission, EntityInterface $source_entity = NULL, AccountInterface $account = NULL) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceEntityTypes(YamlFormInterface $yamlform) {
    return [];
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::getCustomColumns()
   */
  public function getCustomColumns(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL, $include_elements = TRUE) {
    // Get custom columns from the form's state.
    if ($source_entity) {
      $source_key = $source_entity->getEntityTypeId() . '.' . $source_entity->id();
      $custom_column_names = $yamlform->getState("results.custom.columns.$source_key", []);
      // If the source entity does not have custom columns, then see if we
      // can use the main form as the default custom columns.
      if (empty($custom_column_names) && $yamlform->getState("results.custom.default", FALSE)) {
        $custom_column_names = $yamlform->getState('results.custom.columns', []);
      }
    }
    else {
      $custom_column_names = $yamlform->getState('results.custom.columns', []);
    }

    if (empty($custom_column_names)) {
      return $this->getDefaultColumns($yamlform, $source_entity, $account, $include_elements);
    }

    // Get custom column with labels.
    $columns = $this->getColumns($yamlform, $source_entity, $account, $include_elements);
    $custom_columns = [];
    foreach ($custom_column_names as $column_name) {
      if (isset($columns[$column_name])) {
        $custom_columns[$column_name] = $columns[$column_name];
      }
    }
    return $custom_columns;
  }


  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::getDefaultColumns()
   */
  public function getDefaultColumns(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL, $include_elements = TRUE) {
    $columns = $this->getColumns($yamlform, $source_entity, $account, $include_elements);

    // Hide certain unnecessary columns, that have default set to FALSE.
    foreach ($columns as $column_name => $column) {
      if (isset($column['default']) && $column['default'] === FALSE) {
        unset($columns[$column_name]);
      }
    }

    return $columns;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::getColumns()
   */
  public function getColumns(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL, $include_elements = TRUE) {
    $view_any = ($yamlform && $yamlform->access('submission_view_any')) ? TRUE : FALSE;

    $columns = [];

    // Serial number.
    $columns['serial'] = [
      'title' => t('#'),
    ];

    // Submission ID.
    $columns['sid'] = [
      'title' => t('SID'),
      'default' => FALSE,
    ];

    // UUID.
    $columns['uuid'] = [
      'title' => t('UUID'),
      'default' => FALSE,
    ];

    // Sticky (Starred/Unstarred).
    if (empty($account)) {
      $columns['sticky'] = [
        'title' => t('Starred'),
      ];

      // Notes.
      $columns['notes'] = [
        'title' => t('Notes'),
      ];
    }

    // Created.
    $columns['created'] = [
      'title' => t('Created'),
    ];

    // Completed.
    $columns['completed'] = [
      'title' => t('Completed'),
      'default' => FALSE,
    ];

    // Changed.
    $columns['changed'] = [
      'title' => t('Changed'),
      'default' => FALSE,
    ];

    // Source entity.
    if ($view_any && empty($source_entity)) {
      $columns['entity'] = [
        'title' => t('Submitted to'),
        'sort' => FALSE,
      ];
    }

    // Submitted by.
    if (empty($account)) {
      $columns['uid'] = [
        'title' => t('User'),
      ];
    }

    // Submission language.
    if ($view_any && \Drupal::moduleHandler()->moduleExists('language')) {
      $columns['langcode'] = [
        'title' => t('Language'),
      ];
    }

    // Remote address.
    $columns['remote_addr'] = [
      'title' => t('IP address'),
    ];

    // Form.
    if (empty($yamlform) && empty($source_entity)) {
      $columns['yamlform_id'] = [
        'title' => t('Form'),
      ];
    }

    // Form elements.
    if ($yamlform && $include_elements) {
      /** @var \Drupal\yamlform\YamlFormElementManagerInterface $element_manager */
      $element_manager = \Drupal::service('plugin.manager.yamlform.element');

      $elements = $yamlform->getElementsFlattenedAndHasValue();
      foreach ($elements as $element) {
        /** @var \Drupal\yamlform\YamlFormElementInterface $element_handler */
        $element_handler = $element_manager->createInstance($element['#type']);
        $columns += $element_handler->getTableColumn($element);
      }
    }

    // Operations.
    if (empty($account)) {
      $columns['operations'] = [
        'title' => t('Operations'),
        'sort' => FALSE,
      ];
    }

    // Add name and format to all columns.
    foreach ($columns as $name => &$column) {
      $column['name'] = $name;
      $column['format'] = 'value';
    }

    return $columns;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::getCustomSetting()
   */
  public function getCustomSetting($name, $default, YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL) {
    // Return the default value is form and source entity is not defined.
    if (!$yamlform && !$source_entity) {
      return $default;
    }

    $key = "results.custom.$name";
    if (!$source_entity) {
      return $yamlform->getState($key, $default);
    }

    $source_key = $source_entity->getEntityTypeId() . '.' . $source_entity->id();
    if ($yamlform->hasState("$key.$source_key")) {
      return $yamlform->getState("$key.$source_key", $default);
    }
    if ($yamlform->getState("results.custom.default", FALSE)) {
      return $yamlform->getState($key, $default);
    }
    else {
      return $default;
    }
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::invokeYamlFormHandlers()
   */
  public function invokeYamlFormHandlers($method, YamlFormSubmissionInterface $yamlform_submission, &$context1 = NULL, &$context2 = NULL) {
    $yamlform = $yamlform_submission->getYamlForm();
    $yamlform->invokeHandlers($method, $yamlform_submission, $context1, $context2);
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\yamlform\YamlFormSubmissionStorage::invokeYamlFormElements()
   */
  public function invokeYamlFormElements($method, YamlFormSubmissionInterface $yamlform_submission, &$context1 = NULL, &$context2 = NULL) {
    $yamlform = $yamlform_submission->getYamlForm();
    $yamlform->invokeElements($method, $yamlform_submission, $context1, $context2);
  }

}