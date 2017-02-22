<?php

namespace Drupal\partial_date\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\partial_date\Entity\PartialDateFormatInterface;

/**
 * Description of FormatTypeEditForm
 *
 * @author CosminFr
 */
class PartialDateFormatEditForm extends EntityForm {

  /**
   * The partial date format entity.
   *
   * @var \Drupal\partial_date\Entity\PartialDateFormatInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $elements = parent::form($form, $form_state);
    $format   = $this->entity;

    $elements['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $format->label(),
      '#description' => $this->t('Label for the partial date format.'),
      '#required' => TRUE,
    );
    $elements['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $format->id(),
      '#machine_name' => array(
        'exists' => array(get_class($format), 'load'),
      ),
      '#disabled' => !$format->isNew(),
    );

    // Additional custom properties.
    $elements['meridiem'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Ante meridiem and Post meridiem format'),
      '#options' => array(
        'a' => $this->t('Lowercase (am or pm)'),
        'A' => $this->t('Uppercase (AM or PM)')
      ),
      '#default_value' => $format->getMeridiem(),
    );
    $elements['year_designation'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Year designation format'),
      '#default_value' => $format->getYearDesignation(),
      '#options' => array(
        'sign' => $this->t('Negative sign (-)', array(), array('context' => 'datetime')),
        'ad' => $this->t('Anno Domini (BC/AD)', array(), array('context' => 'datetime')),
        'bc' => $this->t('Anno Domini (BC only)', array(), array('context' => 'datetime')),
        'ce' => $this->t('Common Era (BCE/CE)', array(), array('context' => 'datetime')),
        'bce' => $this->t('Common Era (BCE only)', array(), array('context' => 'datetime'))
      ),
      '#required' => TRUE,
      '#description' => $this->t('This controls how year designation is handled: 1BC = 1BCE = -1 and 1AD = 1CE = 1.'),
    );
    $components = partial_date_components();
    $elements['display']   = $this->buildDisplayElements($components, $format);
    $elements['separator'] = $this->buildSeparatorElements($format);

    $custom = array('c1' => $this->t('Custom component 1'), 'c2' => $this->t('Custom component 2'), 'c3' => $this->t('Custom component 3'), 'approx' => $this->t('Approximation text'));
    $elements['components'] = $this->buildComponentsTable($components + $custom, $format);

    return $elements;
  }

  private function buildDisplayElements($components, PartialDateFormatInterface $format) {
    $elements = array(
      '#type' => 'details',
      '#title' => $this->t('Component display'),
      '#tree' => TRUE,
    );

    foreach ($components as $key => $label) {
      $elements[$key] = array(
        '#type' => 'select',
        '#title' => t('Display source for %label', array('%label' => $label)),
        '#options' => $this->partial_date_estimate_handling_options(),
        '#default_value' => $format->getDisplay($key),
        '#required' => TRUE,
      );
    }
    return $elements;
  }
  
  private function buildSeparatorElements(PartialDateFormatInterface $format) {
    $elements = array(
      '#type' => 'details',
      '#title' => $this->t('Component separators'),
      '#tree' => TRUE,
    );
    $elements['date'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Date separators'),
      '#maxlength' => 15,
      '#size' => 8,
      '#default_value' => $format->getSeparator('date'),
      '#description' => $this->t('This separator is used within date part. Empty value is allowed (ex. 20151231). Add spaces if you needed between the separator and the date values.'),
    );
    $elements['time'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Time separators'),
      '#maxlength' => 15,
      '#size' => 8,
      '#default_value' => $format->getSeparator('time'),
      '#description' => $this->t('This separator is used within time component. Empty value is allowed. Add spaces if needed.'),
    );
    $elements['datetime'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Date and time separators'),
      '#size' => 8,
      '#maxlength' => 15,
      '#default_value' => $format->getSeparator('datetime'),
      '#description' => $this->t('This separator is used between date and time components. '),
      '#attributes' => array('class' => array('field--label-inline')),
    );
    $elements['other'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Other separators'),
      '#size' => 8,
      '#maxlength' => 15,
      '#default_value' => $format->getSeparator('other'),
      '#description' => $this->t('This separator may be used with year estimations. TODO add better description or deprecate.'),
      '#attributes' => array('class' => array('field--label-inline')),
    );
    $elements['range'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Range separator'),
      '#size' => 8,
      '#maxlength' => 15,
      '#default_value' => $format->getSeparator('range'),
      '#description' => $this->t('This separator is used to seperate date components in the range element. Add spaces if you need spaces between the separator and the date values.'),
      '#attributes' => array('class' => array('field--label-inline')),
    );
    return $elements;
  }

  private function buildComponentsTable($components, PartialDateFormatInterface $format) {
    $table = array(
      '#type' => 'table',
      '#header' => array(
        'label' => $this->t('Component'),
        'value' => $this->t('Value format'),
        'empty' => $this->t('Value empty text'),
        'weight' => $this->t('Weight'),
      ),
      '#empty' => $this->t('This should not be empty. Try re-installing Partial Date module.'),
      '#tableselect' => FALSE,
      // The weight column is currently not hidden due to the colspan usage.
      // See https://www.drupal.org/node/997370.
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'partial-date-format-order-weight',
        )
      )
    );

    // Build the table rows and columns.
    foreach ($format->getComponents() as $key => $component) {
      $label = $components[$key];
      $table[$key]['#attributes']['class'][] = 'draggable';
      $table[$key]['#weight'] = $component['weight'];
      $table[$key]['label']['#plain_text'] = $label;

      if (in_array($key, array('c1', 'c2', 'c3', 'approx'))) {
        $table[$key]['value'] = array(
          '#type' => 'textfield',
          '#title' => $label,
          '#title_display' => 'invisible',
          '#default_value' => $component['value'],
          '#wrapper_attributes' => array('colspan' => 2),
        );
        if ($key == 'approx') {
          $table[$key]['value']['#description'] = $this->t('Only shows if the date is flagged as approximate.');
        }
      }
      else {
        $table[$key]['format'] = array(
          '#type' => 'radios',
          '#title' => $this->t('Format for %label', array('%label' => $label)),
          '#title_display' => 'invisible',
          '#options' => $this->getComponentOptions($key),
          '#default_value' => $component['format'],
          '#required' => TRUE,
        );

        $table[$key]['empty'] = array(
          '#type' => 'textfield',
          '#title' => $this->t('Empty text for %label', array('%label' => $label)),
          '#title_display' => 'invisible',
          '#default_value' => $component['empty'],
          '#size' => 8,
        );
      }

      $table[$key]['weight'] = array(
        '#type' => 'weight',
        '#title' => $this->t('Weight for %label', array('%label' => $label)),
        '#title_display' => 'invisible',
        '#default_value' => $component['weight'],
        '#attributes' => array('class' => array('partial-date-format-order-weight')),
        '#required' => TRUE,
      );
    }
    
    return $table;
  }

  protected function getComponentOptions($component) {
    switch ($component) {

      case 'year':
        return array(
          'Y' => $this->t('A full numeric representation of a year. Eg: -125, 2003', array(), array('context' => 'datetime')),
          'y' => $this->t('A two digit representation of a year. Eg: -25, 03', array(), array('context' => 'datetime')),
          'Y-ce' => $this->t('A full numeric representation of a year with year designation. Eg: 125BC, 125BCE or -125', array(), array('context' => 'datetime')),
          'y-ce' => $this->t('A two digit representation of a year with year designation. Eg: 25BC, 25BCE or -25', array(), array('context' => 'datetime')),
        );

      case 'month':
        return array(
          'F' => $this->t('A full textual representation of a month, January through December.', array(), array('context' => 'datetime')),
          'm' => $this->t('Numeric representation of a month, with leading zeros, 01 through 12', array(), array('context' => 'datetime')),
          'M' => $this->t('A short textual representation of a month, three letters, Jan through Dec.', array(), array('context' => 'datetime')),
          'n' => $this->t('Numeric representation of a month, without leading zeros, 1 through 12', array(), array('context' => 'datetime')),
        );

      case 'day':
        return array(
          'd' => $this->t('Day of the month, 2 digits with leading zeros, 01 through 31', array(), array('context' => 'datetime')),
          'j' => $this->t('Day of the month without leading zeros, 1 through 31.', array(), array('context' => 'datetime')),
          'd-S' => $this->t('Day of the month, 2 digits with leading zeros with English ordinal suffix.', array(), array('context' => 'datetime')),
          'j-S' => $this->t('Day of the month without leading zeros with English ordinal suffix.', array(), array('context' => 'datetime')),
          'l' => $this->t('A full textual representation of the day of the week.', array(), array('context' => 'datetime')),
          'D' => $this->t('A textual representation of a day, three letters.', array(), array('context' => 'datetime')),
          'w' => $this->t('Numeric representation of the day of the week  0 (for Sunday) through 6 (for Saturday).', array(), array('context' => 'datetime')),
        );

      case 'hour':
        return array(
          'g' => $this->t('12-hour format of an hour without leading zeros, 1 through 12.', array(), array('context' => 'datetime')),
          'G' => $this->t('24-hour format of an hour without leading zeros, 0 through 23.', array(), array('context' => 'datetime')),
          'h' => $this->t('12-hour format of an hour with leading zeros, 01 through 12.', array(), array('context' => 'datetime')),
          'H' => $this->t('24-hour format of an hour with leading zeros, 00 through 23.', array(), array('context' => 'datetime')),
        );

      case 'minute':
        return array(
          'i' => $this->t('Minutes with leading zeros, 00 through 59.', array(), array('context' => 'datetime')),
        );

      case 'second':
        return array(
          's' => $this->t('Seconds, with leading zeros, 00 through 59.', array(), array('context' => 'datetime')),
        );

      case 'timezone':
        return array(
          'e' => $this->t('Timezone identifier. Eg: UTC, GMT, Atlantic/Azores.', array(), array('context' => 'datetime')),
          'T' => $this->t('Timezone abbreviation. Eg: EST, MDT', array(), array('context' => 'datetime')),
        );

      default:
        return array();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    if ($status) {
      drupal_set_message($this->t('Saved the %label format.', array(
        '%label' => $this->entity->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label format was not saved.', array(
        '%label' => $this->entity->label(),
      )));
    }

    $form_state->setRedirect('entity.partial_date_format.collection');
  }

  public function partial_date_estimate_handling_options() {
    return array(
      'none' => t('Hide', array(), array('context' => 'datetime')),
      'estimate_label' => t('Estimate label', array(), array('context' => 'datetime')),
      'estimate_range' => t('Estimate range', array(), array('context' => 'datetime')),
      'estimate_component' => t('Start (single or from dates) or End (to dates) of estimate range', array(), array('context' => 'datetime')),
      'date_only' => t('Date component if set', array(), array('context' => 'datetime')),
      'date_or' => t('Date component with fallback to estimate component', array(), array('context' => 'datetime')),
    );
  }

}
