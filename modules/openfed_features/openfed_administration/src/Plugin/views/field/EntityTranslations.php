<?php

namespace Drupal\openfed_administration\Plugin\views\field;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler for showing links to translations available for a entity.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("entity_translations")
 */
class EntityTranslations extends FieldPluginBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new RenderedEntity object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // No query.
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    // Get all the enabled site languages.
    $languages = $this->languageManager->getLanguages();
    $entity_languages_options = [];

    foreach ($languages as $key => $language) {
      $entity_languages_options[$key] = $language->getName();
    }
    // Field with all the enabled languages on the website.
    $form['entity_languages'] = array(
      '#title' => $this->t('Languages to exclude'),
      '#description' => $this->t('Select the languages that should be excluded when displaying translations.'),
      '#type' => 'checkboxes',
      '#options' => $entity_languages_options,
      '#default_value' => $this->options['entity_languages'],
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var ContentEntityInterface $entity */
    $entity = $values->_entity;
    $entity_id = $entity->id();
    $entity_type = $entity->getEntityTypeId();

    if (!$entity->isTranslatable()) {
      return [];
    }

    // Get all the enabled site languages.
    $languages = $language_options = $this->languageManager->getLanguages();
    $output = [];

    // Get the languages to use, according to the view field settings.
    if (is_array($this->options['entity_languages'])) {
      // This array will have the site enabled languages, excluding any selected
      // languages on the view field settings.
      $language_options = array_diff_key($languages, array_filter($this->options['entity_languages']));
    }

    // If we have enabled languages, this will always be an array, but let's be
    // sure.
    if (is_array($language_options)) {
      foreach ($language_options as $language_prefix => $value) {
        $language_name = $this->languageManager->getLanguageName($language_prefix);
        // Check if the entity has any translations to display.

        if ($entity->hasTranslation($language_prefix)) {
          $options = [
            'language' => $languages[$language_prefix],
            'attributes' => [
              'title' => $this->t('Edit @language', ['@language' => $language_name]),
              'class' => 'entity-translations-' . $entity_type . '-edit',
            ],
          ];
          $url = Url::fromRoute('entity.' . $entity_type . '.edit_form', [$entity_type => $entity_id], $options);
          $text = $language_prefix;
        }
        else {
          // If no translations, we will output a link to add the translation.
          $options = [
            'attributes' => [
              'title' => $this->t('Add @language translation', ['@language' => $language_name]),
              'class' => 'entity-translations-translation-add',
            ],
          ];
          $url = Url::fromRoute('entity.' . $entity_type . '.content_translation_add', [
            $entity_type => $entity_id,
            'source' => $entity->language()->getId(),
            'target' => $language_prefix,
          ], $options);
          // We use a markup array to prevent the <s> tags from being escaped.
          $text = ['#markup' => '<s>' . $language_prefix . '</s>'];
        }
        $output[] = Link::fromTextAndUrl($text, $url)->toString();
      }
    }

    // Return the result as an unordered list.
    return [
      '#theme' => 'item_list',
      '#items' => $output,
      '#type' => 'ul',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    // Field options is empty by default since we don't want to exclude any
    // language.
    $options['entity_languages'] = ['default' => ''];
    return $options;
  }

}
