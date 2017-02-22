<?php

namespace Drupal\openfed;

/**
 * Helper class to provide some global functions.
 */
class Helper {

  const CONTENT_EDITOR_ROLE_ID = 'content_editor';

  const CONTENT_AUTHOR_ROLE_ID = 'content_author';

  const ADMINISTRATOR_ROLE_ID = 'administrator';

  const WORKFLOW_BASIC_CONFIG = 'basic';

  const WORKFLOW_ADVANCED_CONFIG = 'advanced';

  protected $drupal_root;

  protected $site_path;

  /**
   * Helper constructor.
   *
   * @param string $drupal_root
   *   The path to the Drupal root.
   * @param string $site_path
   *   The path to the site's configuration (e.g. sites/default).
   */
  function __construct($drupal_root, $site_path) {
    $this->root = $drupal_root;
    $this->sitePath = (string) $site_path;
  }

  /**
   * Gets the list of potential languages.
   *
   * @return array
   *  The language list.
   */
  function _openfed_get_languages_list() {
    // Set the language list. Defined order should be used to set the weight.
    $languages_list = [
      'nl' => t('Dutch (Nederlands)'),
      'fr' => t('French (Français)'),
      'de' => t('German (Deutsch)'),
    ];

    // Return.
    return $languages_list;
  }

  /**
   * Gets the list of potential menus to enable.
   *
   * @return array
   *   The menus list.
   */
  function _openfed_get_menus_list() {
    $menu_list = [
      'menu-tools-menu' => t('Tools Menu: placed at the very top of the screen.'),
      'menu-footer-menu' => t('Footer Menu: placed at the very bottom of the screen.'),
    ];
    return $menu_list;
  }

  /**
   * Gets the list of all potential roles to enable.
   *
   * @return array
   *   The roles list.
   */
  function _openfed_get_roles_list() {
    $role_list = [
      'configurator' => t('Configurator: Experienced Drupal user, can configure modules.'),
      'user_manager' => t('User Manager: Can create new users and manage their permissions.'),
    ];
    return $role_list;
  }

  /**
   * Gets the name of all roles available or created by default.
   *
   * Used to be called after install.
   */
  function _openfed_get_roles_list_default() {
    // Set roles.
    $role_key = [
      'authenticated' => [
        'label' => 'Authenticated',
        'weight' => 0,
        'required' => TRUE,
        'permissions' => [],
      ],
      'anonymous' => [
        'label' => 'Anonymous',
        'weight' => 1,
        'required' => TRUE,
        'permissions' => [],
      ],
      self::ADMINISTRATOR_ROLE_ID => [
        'label' => 'Administrator',
        'weight' => 2,
        'required' => TRUE,
        'permissions' => [],
      ],
      'builder' => [
        'label' => 'Builder',
        'weight' => 3,
        'required' => TRUE,
        'permissions' => [],
      ],
      'configurator' => [
        'label' => 'Configurator',
        'weight' => 4,
        'required' => FALSE,
        'permissions' => [],
      ],
      self::CONTENT_AUTHOR_ROLE_ID => [
        'label' => 'Content Author',
        'weight' => 5,
        'required' => FALSE,
        'permissions' => [],
      ],
      self::CONTENT_EDITOR_ROLE_ID => [
        'label' => 'Content Editor',
        'weight' => 6,
        'required' => TRUE,
        'permissions' => [],
      ],
      'user_manager' => [
        'label' => 'User Manager',
        'weight' => 7,
        'required' => FALSE,
        'permissions' => [],
      ],
    ];

    // Return.
    return $role_key;
  }

  /**
   * Gets a list of Workflow options to set up.
   *
   * @return array
   *   The workflow option list.
   */
  function _openfed_get_workflow_list() {
    $workflow_list = [
      self::WORKFLOW_BASIC_CONFIG => t('Basic configuration: there will be "Draft" and "Published" states, 
      and everyone can change transitions from one to another.'),
      self::WORKFLOW_ADVANCED_CONFIG => t('Advanced configuration: in adition to the basic configuration, 
      there will be a "Needs review" state and a new role "Content Author" will be created. 
      The Content Author will be able only to promote the content from "Draft" to "Needs review". 
      It\'s up to the Content Editor to manage transitions between all the states.'),
    ];
    return $workflow_list;
  }

}
