<?php

namespace Drupal\openfed_svg_file\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldWidget\FileWidget;


/**
 * Plugin implementation of the 'openfed_svg_file' widget.
 *
 * @FieldWidget(
 *   id = "openfed_svg_file_widget",
 *   label = @Translation("Openfed SVG File Widget"),
 *   field_types = {
 *     "openfed_svg_file"
 *   }
 * )
 */
class OpenfedSvgFileWidget extends FileWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'progress_indicator' => 'throbber',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    return $element;
  }

  /**
   * Form API callback: Processes a file_generic field element.
   *
   * Expands the file_generic type to include the description and display
   * fields.
   *
   * This method is assigned as a #process callback in formElement() method.
   */
  public static function process($element, FormStateInterface $form_state, $form) {
    $item = $element['#value'];
    $item['fids'] = $element['fids']['#value'];

    // Add the description field if enabled.
    if ($item['fids']) {
      $field_id = $element['#name'];

//      $element += [
//        '#type' => 'fieldset',
//      ];
//
//      $element['output_type'] = array(
//        '#type' => 'fieldset',
//        '#title' => t('Output types'),
//      );

      $element['type'] = [
        '#type' => 'radios',
        '#title' => t('Output type'),
        '#default_value' => isset($item['type']) ? $item['type'] : 'image',
        '#options' => get_output_types(),
        '#required' => TRUE,
      ];
      $element['width'] = array(
        '#type' => 'textfield',
        '#title' => t('Width'),
        '#value' => isset($item['width']) ? $item['width'] : 100,
        '#field_suffix' => 'px',
        '#size' => 4,
        '#required' => TRUE,
        '#element_validate' =>[[get_called_class(), 'element_validate_integer_positive']],
      );
      $element['height'] = array(
        '#type' => 'textfield',
        '#title' => t('Height'),
        '#value' => isset($item['height']) ? $item['height'] : 100,
        '#field_suffix' => 'px',
        '#size' => 4,
        '#required' => TRUE,
        '#element_validate' =>[[get_called_class(), 'element_validate_integer_positive']],
      );
      $element['title'] = array(
        '#type' => 'textfield',
        '#title' => t('Title'),
        '#value' => isset($item['title']) ? $item['title'] : '',
        '#disabled' => TRUE,
        '#states' => array(
          'visible' => array(
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'iframe')],
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'image')],
          ),
          'enabled' => array(
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'iframe')],
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'image')],
          ),
          'required' => array(
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'iframe')],
          ),
        ),
      );
      $element['alt'] = array(
        '#type' => 'textfield',
        '#title' => t('Alt'),
        '#value' => isset($item['alt']) ? $item['alt'] : '',
        '#disabled' => TRUE,
        '#states' => array(
          'visible' => array(
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'object')],
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'image')],
          ),
          'enabled' => array(
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'object')],
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'image')],
          ),
          'required' => array(
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'object')],
            [':input[name="' . $field_id . '[type]"]' => array('value' => 'image')],
          ),
        ),
      );
    }

    return parent::process($element, $form_state, $form);
  }

  /**
   * Form element validation handler for #type 'number'.
   */
  public static function  element_validate_integer_positive(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = $element['#value'];
    if ($value !== '' && (!is_numeric($value) || intval($value) != $value || $value < 0)) {
      $form_state->setError($element, t('%name must be a positive integer.', ['%name' => $element['#title']]));
    }
  }
}
