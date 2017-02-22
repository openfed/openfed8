<?php

/**
 * @file
 * Provide Views fields to ease content administration.
 */

/**
 * Implements hook_views_data_alter().
 */
function openfed_administration_views_data_alter(array &$data) {
  $entities = \Drupal::entityTypeManager()->getDefinitions();
  foreach ($entities as $entity => $entity_definition) {
    $data[$entity . '_field_data']['entity_translations'] = [
      'title' => t('@entity translation links', ['@entity' => ucfirst($entity)]),
      'field' => [
        'title' => t('@entity translation links', ['@entity' => ucfirst($entity)]),
        'help' => t('Displays a list of translations for each entity, linking to the edit form.'),
        'id' => 'entity_translations',
      ],
    ];
  }
}
