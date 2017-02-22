<?php

namespace Drupal\partial_date\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * PartialDateSettingsForm is a simple config form to manage common settings for Partial Date module/field type
 *
 * @author CosminFr
 */
class PartialDateSettingsForm extends ConfigFormBase {
  
  CONST SETTINGS = 'partial_date.settings';
  
  public function getFormId() {
    return 'partial_date_settings_form';
  }

  protected function getEditableConfigNames() {
    return [self::SETTINGS];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::SETTINGS);

    //Only show setting you actually want users to edit
    //TODO: these are just for demo, probably should not be modified by users
    $form['txt_inline_styles'] = array(
      '#type' => 'textfield',
      '#title' => 'Text inline styles',
      '#default_value' => $config->get('partial_date_component_field_txt_inline_styles'),
    );
    $form['inline_styles'] = array(
      '#type' => 'textfield',
      '#title' => 'Inline styles',
      '#default_value' => $config->get('partial_date_component_field_inline_styles'),
    );

    return parent::buildForm($form, $form_state);
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(self::SETTINGS);
    // Save any changes.
    $config->set('partial_date_component_field_txt_inline_styles', $form_state->getValue('txt_inline_styles'));
    $config->set('partial_date_component_field_inline_styles', $form_state->getValue('inline_styles'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
