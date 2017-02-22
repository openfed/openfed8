<?php
namespace Drupal\openfed\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\language\Entity\ConfigurableLanguage;

/**
 * Defines a form for selecting which languages to install.
 */
class SetupLanguagesForm extends FormBase {
  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'openfed_setup_languages';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Language Selection');

    $form['regional_list'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Regional languages'),
      '#options' => \Drupal::service('openfed.helper')
        ->_openfed_get_languages_list(),
      '#description' => '<p>' . $this->t('When checking a language, a main menu will automatically be created in the menus list for that language, and the menu will be located in the corresponding Navigation area.<br><br>
      If no languages are selected, only the English language will be enabled.<br>Consequently, the main menu will be created for the English language only and will be located in the Navigation area.<br><br>
      Additional languages can be enabled after the installation, but the main menu will then have to be created manually for each language and located in the corresponding area.') . '</p>',
      '#default_value' => \Drupal::state()->get('openfed_regional_list_chosen', [
        'nl',
        'fr',
        'de',
      ]),
    ];

    $form['cookie_and_language_selection'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Language cookie and language selection page'),
      '#description' => '<p>' . $this->t('When checking this option, the language cookie
      and language selection page modules will be automatically configured. This
      means that for determining the language of a page, the following order will
      be used: 1. URL prefix, 2. Language cookie, 3. Language selection page.') . '</p><p>' .
        $this->t('Note this option is only relevant for sites using multiple languages.') . '</p>',
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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $regional_enabled = array_filter($form_state->getValue('regional_list'));
    $cookie_and_language_selection = $form_state->getValue('cookie_and_language_selection');
    if (empty($regional_enabled) && !empty($cookie_and_language_selection)) {
      $form_state->setErrorByName('regional_list', $this->t('You chose to enable the language cookie
      and language selection page, but did not select a regional language. Please choose at
      least one regional language.'));
    }
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $regional_enabled = array_filter($form_state->getValue('regional_list'));
    $cookie_and_language_selection = $form_state->getValue('cookie_and_language_selection');

    $regional_enabled['en'] = 'en';
    // If there is more than one language or the single one is not English, we
    // should import translations.
    if (count($regional_enabled) > 1) {
      // Set up the translations to import. Translation import happens in
      // install_import_translations_remaining.
      $weight = 0;
      foreach ($regional_enabled as $index => $langcode) {
        $language = ConfigurableLanguage::load($langcode);
        // Checking if the language is already installed. If so, we just update the
        // weight.
        if (empty($language)) {
          ConfigurableLanguage::createFromLangcode($langcode)
            ->setWeight($weight)
            ->save();
        }
        else {
          $language->setWeight($weight)->save();
        }
        $weight++;
      }

      // Enable Multilingual support.
      // This is being enabled after adding new languages, otherwise
      // content_translation module will throw a warning about adding more
      // languages.
      \Drupal::service('module_installer')->install(['openfed_multilingual']);
    }

    \Drupal::state()
      ->set('openfed_regional_list_chosen', array_filter($regional_enabled));

    // Check cookie and language selection option
    if (!empty($cookie_and_language_selection)) {
      // Enable language cookie and language seleciton page cookies.
      $module_list = [
        'language_cookie',
        'language_selection_page',
      ];
      \Drupal::service('module_installer')->install($module_list);

      // Set negotiation methods and define their order.
      $type = LanguageInterface::TYPE_INTERFACE;
      $enabled_methods = [
        'language-url' => 0,
        'language-cookie' => 1,
        'language-selection-page' => 2,
        'language-selected' => 3,
      ];
      \Drupal::service('language_negotiator')
        ->saveConfiguration($type, $enabled_methods);
    }

  }
}
