<?php
namespace Drupal\openfed\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form for selecting which languages to install.
 */
class SetupMenusForm extends FormBase {
  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'openfed_install_menu_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Set up menus');

    $form['menu_list'] = [
      '#type' => 'checkboxes',
      '#title' => t('Menus'),
      '#options' => \Drupal::service('openfed.helper')
        ->_openfed_get_menus_list(),
      '#description' => t('By checking these options, the menus will automatically be created in the menus list of the website and shown in the corresponding region (either the tools or the footer region).<br><br>
      If no menus are checked, they can still be created after the installation, but they will need to be created and added to the correct region manually.'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $menu_list = $form_state->getValue('menu_list');

    if (!empty($menu_list)) {
      foreach ($menu_list as $menu_key => $menu_identity) {
        if ($menu_key === $menu_identity) {
          $language_list = \Drupal::state()
            ->get('openfed_regional_list_chosen', []);
          foreach ($language_list as $lang) {
            $menu_name = substr($menu_identity, 0, strlen($menu_identity) - 4);
            $menu_name .= strtoupper($lang);
            $menu_title = str_replace('-', ' ', $menu_name);
            $menu_name = strtolower($menu_name);

            // Create custom menus.
            $this->_openfed_create_custom_menu($menu_name, ucwords($menu_title), '', $lang);

//            // Set block in region.
//            switch ($menu_identity) {
//              case 'menu-tools-menu':
//              case 'menu-tools-' . $lang->name . '-menu':
//                _openfed_set_block_into_region('menu', $menu_name, 'tools');
//                break;
//              case 'menu-footer-menu':
//              case 'menu-footer-' . $lang->name . '-menu':
//                _openfed_set_block_into_region('menu', $menu_name, 'footer');
//                break;
//            }
//
//            // Show block for language only.
//            _openfed_show_block_for_language('menu', $menu_name, $lang->language);

          }
        }
      }

      // Rebuild menu.
      \Drupal::service('router.builder')->rebuild();
    }
  }

  /**
   * Create a custom menu.
   *
   * @param string $menu_name The unique name of the custom menu.
   * @param string $title The human readable menu title.
   * @param string $description The custom menu description.
   * @param string $language The language code for the menu item.
   */
  private function _openfed_create_custom_menu($menu_name, $title, $description = '', $language = 'en') {
    $menu = [];
    $menu['id'] = $menu_name;
    $menu['label'] = $title;
    $menu['description'] = $description;
    $menu['langcode'] = $language;
    $menu['locked'] = 0;
    $menu['status'] = 1;
    \Drupal::entityTypeManager()->getStorage('menu')->create($menu)->save();
  }

}
