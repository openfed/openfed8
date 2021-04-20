<?php

namespace Drupal\partial_date\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\partial_date\PartialDateFormatterInterface;
use Drupal\partial_date\Plugin\Field\FieldType\PartialDateTimeItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation for Partial Date formatter.
 *
 * @FieldFormatter(
 *   id = "partial_date_formatter",
 *   module = "partial_date",
 *   label = @Translation("Default"),
 *   description = @Translation("Display partial date."),
 *   field_types = {"partial_date"},
 *   quickedit = {
 *     "editor" = "disabled",
 *   },
 * )
 */
class PartialDateFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The partial date format storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $partialDateFormatStorage;

  /**
   * The partial date formatter.
   *
   * @var \Drupal\partial_date\PartialDateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a partial date formatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\partial_date\PartialDateFormatterInterface $date_formatter
   *   The partial date formatter.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, PartialDateFormatterInterface $date_formatter) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->partialDateFormatStorage = $entity_type_manager->getStorage('partial_date_format');
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('partial_date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'use_override' => 'none',
      'format' => 'short',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = array();

    $elements['use_override'] = array(
      '#type' => 'checkbox_with_options',
      '#title' => t('Use date descriptions (if available)'),
      '#default_value' => $this->getSetting('use_override'),
      '#options' => $this->overrideOptions(),
      '#checkbox_value' => 'none',
      '#description' => t('This setting allows date values to be replaced with user specified date descriptions, if applicable.'),
    );
    $elements['format'] = array(
      '#title' => t('Partial date format'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('format'),
      '#required' => TRUE,
      '#options' => $this->formatOptions(),
//      '#id' => 'partial-date-format-selector',
//      '#attached' => array(
//        'js' => array(drupal_get_path('module', 'partial_date') . '/partial-date-admin.js'),
//      ),
      '#description' => t('You can use any of the predefined partial date formats. '
          . 'Or, you can configure partial date formats <a href=":config">here</a>.',
          array(':config' => '/admin/config/regional/partial-date-formats')),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    if ($this->getSetting('use_override') != 'none') {
      $overrides = $this->overrideOptions();
      $summary[] = t(' User text: ') . $overrides[$this->getSetting('use_override')];
    }

    $types   = $this->formatOptions();
    $example = $this->dateFormatter->format($this->generateExampleDate(), $this->getFormat());
    $summary[] = array('#markup' => t('Format: ') . $types[$this->getSetting('format')] . ' - ' . $example);

    return $summary;
  }

  protected function overrideOptions() {
    return array(
      'none' => t('Use date only', array(), array('context' => 'datetime')),
      'short' => t('Use short description', array(), array('context' => 'datetime')),
      'long' => t('Use long description', array(), array('context' => 'datetime')),
      'long_short' => t('Use long or short description', array(), array('context' => 'datetime')),
      'short_long' => t('Use short or long description', array(), array('context' => 'datetime')),
    );
  }

  protected function formatOptions() {
    $formats = $this->partialDateFormatStorage->loadMultiple();
    $options = array();
    foreach($formats as $key => $format) {
      $options[$key] = $format->label();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    foreach ($items as $delta => $item) {
      $override = $this->getTextOverride($item);
      if ($override) {
        $element[$delta] = array('#markup' => $override);
      }
      else {
        $from = $item->from;
        if ($from) {
          $element[$delta] = [
            '#theme' => 'partial_date',
            '#date' => $from,
            '#format' => $this->getFormat(),
          ];
        }
        else {
          $element[$delta] = ['#markup' => $this->t('N/A')];
        }
      }
    }
    return $element;
  }

  /**
   * Returns the configured partial date format.
   *
   * @return \Drupal\partial_date\Entity\PartialDateFormatInterface
   *   The partial date format entity.
   */
  protected function getFormat(){
    return $this->partialDateFormatStorage->load($this->getSetting('format'));
  }

  protected function getTextOverride(PartialDateTimeItem $item) {
    $override = '';
    switch ($this->getSetting('use_override')) {
      case 'short':
        if (strlen($item->txt_short)) {
          $override = $item->txt_short;
        }
        break;
      case 'long':
        if (strlen($item->txt_long)) {
          $override = $item->txt_long;
        }
        break;

      case 'long_short':
        if (strlen($item->txt_long)) {
          $override = $item->txt_long;
        }
        elseif (strlen($item->txt_short)) {
          $override = $item->txt_short;
        }
        break;
      case 'short_long':
        if (strlen($item->txt_short)) {
          $override = $item->txt_short;
        }
        elseif (strlen($item->txt_long)) {
          $override = $item->txt_long;
        }
        break;
    }
    return $override;
  }

  /**
   * This generates a date component based on the specified timestamp and
   * timezone. This is used for demonstrational purposes only, and may fall back
   * to the request timestamp and site timezone.
   *
   * This could throw errors if outside PHP's native date range.
   */
  public function generateExampleDate($timestamp = 0, $timezone = NULL) {
    // PHP Date should handle any integer, but outside of the int range, 0 is
    // returned by intval(). On 32 bit systems this is Fri, 13 Dec 1901 20:45:54
    // and Tue, 19 Jan 2038 03:14:07 GMT
    $timestamp = intval($timestamp);
    if (!$timestamp) {
      $timestamp = \Drupal::time()->getRequestTime();
    }
    if (!$timezone) {
      //$timezones = partial_date_granularity_field_options('timezone');
      //$timezone = $timezones[rand(0, count($timezones) - 1)];
      $timezone = partial_date_timezone_handling_correlation('UTC', 'site');
    }
    try {
      $tz = new \DateTimeZone($timezone);
      $date = new \DateTime('@' . $timestamp, $tz);
      if ($date) {
        return array(
          'year' => $date->format('Y'),
          'month' => $date->format('n'),
          'day' => $date->format('j'),
          'hour' => $date->format('G'),
          'minute' => $date->format('i'),
          'second' => $date->format('s'),
          'timezone' => $timezone,
        );
      }
    }
    catch (\Exception $e) {}

    return FALSE;
  }

}
