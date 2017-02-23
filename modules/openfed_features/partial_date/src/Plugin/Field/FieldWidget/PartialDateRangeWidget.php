<?php

namespace Drupal\partial_date\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides an widget for Partial Date Range fields.
 *
 * @FieldWidget(
 *   id = "partial_date_range_widget",
 *   label = @Translation("Partial date and time range"),
 *   field_types = {"partial_date_range"},
 * )
 */
class PartialDateRangeWidget extends PartialDateWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $config = $this->configFactory->get('partial_date.settings');
    $item = $items[$delta];

    $components = $this->getSetting('components_to');
    $this->filterComponents($components);

    if ($this->getSetting('range_inline')) {
      $element['#attributes'] = array('class' => array('container-inline'));
    }

    $element['from']['#title'] = $this->t('Start date');

    $help_txt = $this->getSetting('help_txt');
    $element['_separator'] = array(
      '#type' => 'markup',
      '#markup' => '<div class="partial-date-separator">&nbsp;' . $help_txt['range_separator'] . '&nbsp;</div>',
    );
    $element['to'] = array(
      '#type' => 'partial_datetime_element',
      '#title' => t('End date'),
      '#title_display' => 'invisible',
      '#default_value' => $item->to,
      '#field_sufix' => '_to',
      '#granularity' => $components,
      '#minimum_components' => $this->getFieldSetting('minimum_components')['to']['granularity'],
      '#component_styles' => $config->get('partial_date_component_field_inline_styles'),
      '#increments' => $this->getSetting('increments'),
    );

    $estimates = array_filter($this->getSetting('estimates'));
    if ($estimates) {
      $element['estimates'] = $this->buildEstimatesElement($estimates);
    }

    return $element;
  }

  /*
   * Builds estimate selectors with (prefix/sufix help texts)
   * If no estimates are usable, return FALSE
   */
  protected function buildEstimatesElement(array $estimates) {
    $estimates = $this->getFieldSetting('estimates');
    $help_txt = $this->getSetting('help_txt');
    $has_content = FALSE;
    $element = array(
      '#type' => 'container',
      '#attributes' => array('class' => array('container-inline')),
      '#attached' => array('library' => array('partial_date/estimates')),
    );
    $element['prefix'] = array('#plain_text' => $help_txt['estimates_prefix']);
    $components = partial_date_components();
    foreach ($estimates as $key => $value) {
      if (!empty($value) && !empty($estimates[$key])) {
        $has_content = TRUE;
        $estimate_label = t('@component estimate', array('@component' => $components[$key]));
        $blank_option = array('' => $estimate_label);
        $element[$key . '_estimate'] = array(
          '#type' => 'select',
          '#title' => $estimate_label,
          '#title_display' => 'invisible',
//          '#value' => empty($element['#value'][$key . '_estimate']) ? '' : $element['#value'][$key . '_estimate'],
//          '#attributes' => $element['#attributes'],
          '#options' => $blank_option + $estimates[$key],
          '#attributes' => array(
            'class' => array('estimate_selector'),
            'date_component' => $key,
          ),
        );
      }
    }
    $element['sufix'] = array('#plain_text' => $help_txt['estimates_sufix']);
    if (!$has_content) {
      $element = FALSE;
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['components_to'] = $settings['components'];
    $settings['help_txt'] += [
      'range_separator' => new TranslatableMarkup(' to '),
      'estimates_prefix' => new TranslatableMarkup('... or choose from pre-defined estimates: '),
      'estimates_sufix' => '',
    ];
    return $settings + array(
      'estimates' => array(
        'year' => TRUE,
        'month' => TRUE,
      ),
      'range_inline' => TRUE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    //debug_only:     var_dump($this->settings);
    $elements = parent::settingsForm($form, $form_state);

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

    $elements['components_to'] = $elements['components'];
    $elements['components_to']['#title'] = t('Date components (to date)');
    $elements['components_to']['#default_value'] = $this->getSetting('components_to');
    $elements['components']['#title'] = t('Date components (from date)');

    $elements['estimates'] = array(
      '#type' => 'partial_date_components_element',
      '#title' => t('Show estimates'),
      '#default_value' => $this->getSetting('estimates'),
      '#description' => t('Select the date component estimate attributes that you want to expose.'),
      '#show_time' => $this->getFieldSetting('has_time'),
      '#time_states' => $statesVisible_HasTime,
    );
    $elements['range_inline'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show range end componets on the same line?'),
      '#default_value' => $this->getSetting('range_inline'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildHelpTxtElement() {
    $element = parent::buildHelpTxtElement();

    $help_txt = $this->getSetting('help_txt');
    $element['range_separator'] = array(
      '#type' => 'textfield',
      '#title' => t('Range separator'),
      '#default_value' => $help_txt['range_separator'],
      '#description' => t('Choose a short text between start and end components.'),
    );
    $element['estimates_prefix'] = array(
      '#type' => 'textfield',
      '#title' => t('Estimates prefix'),
      '#default_value' => $help_txt['estimates_prefix'],
      '#description' => t('Choose a short text to show before estimate selectors.'),
    );
    $element['estimates_sufix'] = array(
      '#type' => 'textfield',
      '#title' => t('Estimates sufix'),
      '#default_value' => $help_txt['estimates_sufix'],
      '#description' => t('Choose a short text to show after estimate selectors.'),
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $has_time  = $this->hasTime();

    $exclude = [];
    if (!$has_time) {
      $exclude = ['hour', 'minute', 'second', 'timezone'];
    }
    $components = partial_date_components($exclude);
    $range_components = array_filter($this->getSetting('components_to'));
    if ($range_components) {
      $txt = t('End of range components: ');
      foreach ($components as $key => $label) {
        if (!empty($range_components[$key])) {
          $txt .= $label . ', ';
        }
      }
      $summary[] = $txt;
    }
    $estimates = array_filter($this->getSetting('estimates'));
    if ($estimates) {
      $txt = t('Use estimates for: ');
      foreach ($components as $key => $label) {
        if (!empty($estimates[$key])) {
          $txt .= $label . ', ';
        }
      }
      $summary[] = $txt;
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    //prepare field components from form element
    $field = parent::massageFormValues($values, $form, $form_state);
    foreach ($values as $delta => $value) {
      $value += array('to' => '');

      foreach (partial_date_components() as $key => $label) {
        if (!empty($value['to'][$key])) {
          $field[$delta][$key . '_to'] = $value['to'][$key];
        }
      }
    }
    return $field;
  }

}
