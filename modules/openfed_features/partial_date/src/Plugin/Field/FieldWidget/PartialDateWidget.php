<?php 

namespace Drupal\partial_date\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an widget for Partial Date fields.
 *
 * @FieldWidget(
 *   id = "partial_date_widget",
 *   label = @Translation("Partial date and time"),
 *   field_types = {"partial_date"},
 * )
 */
class PartialDateWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a PartialDateWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, ConfigFactoryInterface $config_factory){
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->configFactory = $config_factory;
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
      $configuration['third_party_settings'],
      $container->get('config.factory')
    );
  }


  public function hasTime() {
    return $this->getFieldSetting('has_time') && $this->getSetting('has_time');
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('partial_date.settings');
    $item = $items[$delta];

    $help_txt = $this->getSetting('help_txt');

    $components = $this->getSetting('components');
    $this->filterComponents($components);

    $element['#theme_wrappers'][] = 'form_element';

    $element['from'] = array(
      '#type' => 'partial_datetime_element',
      '#title' => t('Date'),
      '#title_display' => 'invisible',
      '#default_value' => $item->from,
      '#field_sufix' => '',
      '#granularity' => $components,
      '#minimum_components' => $this->getFieldSetting('minimum_components')['from']['granularity'],
      '#component_styles' => $config->get('partial_date_component_field_inline_styles'),
      '#increments' => $this->getSetting('increments'),
    );

    $element['#component_help'] = $help_txt['components'];

    $element['txt'] = array(
      '#type' => 'container',
      '#attributes' => array('class' => array('container-inline')),
    );
    $txt_element = array(
      '#type' => 'textfield',
      '#title' => t('Text override'),
      '#title_display' => 'invisible',
      '#maxlength' => 255,
    );
    if (!empty($this->getSetting('txt_long'))) {
      $element['txt']['long'] = $txt_element + array(
        '#id' => 'txt_long',
        '#placeholder' => $help_txt['txt_long'],
        '#default_value' => $item->txt_long,
        '#size' => 80,
      );
    }
    if (!empty($this->getSetting('txt_short'))) {
      $element['txt']['short'] = $txt_element + array(
        '#id' => 'txt_short',
        '#placeholder' => $help_txt['txt_short'],
        '#default_value' => $item->txt_short,
        '#maxlength' => 100,
        '#size' => 40,
      );
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $components = array_fill_keys(partial_date_component_keys(), 1);
    return array(
      'txt_short' => FALSE,
      'txt_long' => FALSE,
      'has_time' => TRUE,
      'year_estimates_values' => '',
      'tz_handling' => 'none',
      'components' => $components,
      'increments' => array(
        'second' => 1,
        'minute' => 1,
      ),
      'help_txt' => array(
        'components' => '',
        'txt_short' => new TranslatableMarkup('Short description of date'),
        'txt_long' => new TranslatableMarkup('Longer description of date'),
      ),
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    //debug_only:     var_dump($this->settings);
    $elements = array();
    $elements['txt_long'] = array(
      '#type' => 'checkbox',
      '#id' => 'txt_long',
      '#title' => t('Provide a textfield for collection of a long description of the date'),
      '#default_value' => $this->getSetting('txt_long'),
    );
    $elements['txt_short'] = array(
      '#type' => 'checkbox',
      '#id' => 'txt_short',
      '#title' => t('Provide a textfield for collection of a short description of the date'),
      '#default_value' => $this->getSetting('txt_short'),
    );
    $elements['has_time'] = array(
      '#type' => 'checkbox',
      '#id' => 'has_time',
      '#title' => t('Show time components'),
      '#default_value' => $this->hasTime(),
      '#description' => t('Clear if not interested in holding time. Check to make time controls available.'),
      '#disabled' => !$this->getFieldSetting('has_time'),
    );
    //ensure that if field does not allow time specification, the option is not available!
    if (!$this->getFieldSetting('has_time')) {
      $elements['has_time']['#type'] = 'value';
      $elements['has_time']['#value'] = 0;
      $this->setSetting('has_time', 0);
    }
    //Java Script markers to dynamically hide form elements based on the above checkboxes.
    $statesVisible_HasTime = array(
      'visible' => array(
        ':input[id="has_time"]' => array('checked' => TRUE),
      ),
    );
    $elements['components'] = array(
      '#type' => 'partial_date_components_element',
      '#title' => t('Date components'),
      '#default_value' => $this->getSetting('components'),
      '#show_time' => $this->getFieldSetting('has_time'),
      '#description' => t('Select the date attributes to collect and store.'),
      '#time_states' => $statesVisible_HasTime,
    );

    if ($this->getFieldSetting('has_time')) {
      $tz_options = $this->getTimezoneOptions();
      $elements['tz_handling'] = array(
        '#type' => 'select',
        '#title' => t('Time zone handling'),
        '#default_value' => $this->getSetting('tz_handling'),
        '#options' => $tz_options,
        '#required' => TRUE,
        '#description' => t('Currently, this is only informative; not used in any calculations. <br>')
          . t('Only %date handling option will render the timezone selector to users.', array('%date' => $tz_options['date'])),
        '#states' => $statesVisible_HasTime,
      );
      $incremtOptions = array_combine(array(1, 2, 5, 10, 15), array(1, 2, 5, 10, 15));
      $increments = $this->getSetting('increments');
      $elements['increments'] = array();
      $elements['increments']['minute'] = array(
        '#type' => 'select',
        '#title' => t('Minute increments'),
        '#default_value' => $increments['minute'],
        '#options' => $incremtOptions,
        '#required' => TRUE,
        '#states' => $statesVisible_HasTime,
      );
      $elements['increments']['second'] = array(
        '#type' => 'select',
        '#title' => t('Second increments'),
        '#default_value' => $increments['second'],
        '#options' => $incremtOptions,
        '#required' => TRUE,
        '#states' => $statesVisible_HasTime,
      );
    }
    $elements['help_txt'] = $this->buildHelpTxtElement();
    return $elements;
  }

  protected function buildHelpTxtElement() {
    $element = array(
      '#tree' => TRUE,
      '#type' => 'fieldset',
      '#title' => t('Inline help'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#description' => t('This provides additional help per component, or a way to override the default description text.'),
    );

    $help_txt = $this->getSetting('help_txt');
    $element['components'] = array(
      '#type' => 'textarea',
      '#title' => t('Date components'),
      '#default_value' => $help_txt['components'],
      '#rows' => 3,
      '#description' => t('Instructions to present under the date or date range components. No help shown by default.'),
    );
    $element['txt_short'] = array(
      '#type' => 'textfield',
      '#title' => t('Short date description'),
      '#default_value' => $help_txt['txt_short'],
      '#description' => t('Instructions to present for short date description (if used). Default is %default', array('%default' => t('Short date description'))),
      '#states' => array(
        'visible' => array(
          ':input[id="txt_short"]' => array('checked' => TRUE),
        ),
      ),
    );
    $element['txt_long'] = array(
      '#type' => 'textfield',
      '#title' => t('Long date description'),
      '#default_value' => $help_txt['txt_long'],
      '#description' => t('Instructions to present for long date description (if used). Default is %default', array('%default' => t('Longer description of date'))),
      '#states' => array(
        'visible' => array(
          ':input[id="txt_long"]' => array('checked' => TRUE),
        ),
      ),
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $has_time  = $this->hasTime();

    if ($has_time) {
      $timezone = $this->getSetting('tz_handling');
      if ($timezone == 'none') {
        $summary[] = t('No timezone translations');
      }
      else {
        $summary[] = t('Timezone handling: ') . $this->getTimezoneOptions()[$timezone];
      }
    }
    elseif ($this->getFieldSetting('has_time')) {
      $summary[] = t('Date only');
    }

    $exclude = [];
    if (!$has_time) {
      $exclude = ['hour', 'minute', 'second', 'timezone'];
    }
    $components = partial_date_components($exclude);
    $from_components = array_filter($this->getSetting('components'));
    if (!empty($from_components)) {
      $txt = t('Available components: ');
      foreach ($components as $key => $label) {
        if (!empty($from_components[$key])) {
          $txt .= $label . ', ';
        }
      }
      $summary[] = $txt;
    }
    if ($this->getSetting('txt_short') || $this->getSetting('txt_long')) {
      $summary[] = t('Allow text override');
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    //prepare field components from form element
    $field = array();
    foreach ($values as $delta => $value) {
      $value += array(
        'txt' => array(),
        'from' => '',
      );
      $field[$delta] = array();
      if (!empty($value['txt'])) {
        $field[$delta]['txt_short'] = $value['txt']['short'] ?: NULL;
        $field[$delta]['txt_long']  = $value['txt']['long'] ?: NULL;
      }
      foreach (partial_date_components() as $key => $label) {
        if (!empty($value['from'][$key])) {
          $field[$delta][$key] =  $value['from'][$key];
        }
      }
    }
    return $field;
  }

  /**
   * Returns an array of timezone handling options.
   *
   * @return array
   *   An array of options for timezone handling.
   */
  protected function getTimezoneOptions() {
    return array(
      'none' => new TranslatableMarkup('No timezone conversion'),
      'date' => new TranslatableMarkup('User selectable', array(), array('context' => 'datetime')),
      'site' => new TranslatableMarkup("Site's timezone"),
      'user' => new TranslatableMarkup("User's account timezone"),
      'utc' => new TranslatableMarkup('UTC', array(), array('context' => 'datetime')),
    );
  }

  /**
   * Filters the list of components that will be selectable in the widget.
   *
   * @param array $components
   *   An array of components, passed by reference.
   */
  protected function filterComponents(&$components) {
    // Do not filter the timezone if it is a minimum component.
    $minimum_components = $this->getFieldSetting('minimum_components');
    $tz_handling = $this->getSetting('tz_handling');
    if (!$minimum_components['from']['granularity']['timezone'] && $tz_handling !== 'none' && $tz_handling !== 'date') {
      unset($components['timezone']);
    }
  }

}
