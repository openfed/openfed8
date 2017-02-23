<?php

namespace Drupal\partial_date\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation for Partial Date formatter.
 *
 * @FieldFormatter(
 *   id = "partial_date_range_formatter",
 *   module = "partial_date_range",
 *   label = @Translation("Default"),
 *   description = @Translation("Display partial date range."),
 *   field_types = {"partial_date_range"},
 *   quickedit = {
 *     "editor" = "disabled",
 *   },
 * )
 */
class PartialDateRangeFormatter extends PartialDateFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return parent::defaultSettings() + array(
      'range_reduce' => TRUE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['range_reduce'] = array(
      '#type' => 'checkbox',
      '#title' => t('Reduce common values from range display'),
      '#default_value' => $this->getSetting('range_reduce'),
      '#description' => t('This setting allows a simplified display for range values. For example "2015 Jan-Sep" instead of full specification "2015 Jan-2015 Sep"'),
    );
    return $elements;
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
        $to = $item->to;
        if ($this->getSetting('range_reduce')) {
          $this->reduceRange($from, $to);
        }

        if ($from && $to) {
          $element[$delta] = [
            '#theme' => 'partial_date_range',
            '#from' => $from,
            '#to' => $to,
            '#format' => $this->getFormat(),
          ];
        }
        elseif ($from) {
          $element[$delta] = [
            '#theme' => 'partial_date',
            '#date' => $from,
            '#format' => $this->getFormat(),
          ];
        }
        elseif ($to) {
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

  /*
   * Reduce identical range components to simplify the display.
   * Format is needed to know which side should be cleared. The order in which
   * year, month and day are displayed is important:
   * Ex. 2015 Jun to 2015 Sep => 2015 Jun to Sep
   * but Jun 2015 to Sep 2015 => Jun to Sep 2015
   * Rules:
   * 1. If all date correspondent components are equal, keep only left side and quit (no time compression)
   * 2. If time components are present, stop further compression (mixed date & time compression is confusing).
   * 3. If same year, check format order:
   *    a. YYYY / MM - compress right  (2015 Jun - Sep)
   *    b. MM / YYYY - compress left   (Jun - Sep 2015)
   *    (not same year - stop further compression)
   * 4. If same month, check format order:
   *    a. MM / DD - compress right  (Jun 15 - 25)
   *    b. DD / MM - compress left   (15 - 25 Jun)
   * (same day was
   */
  protected function reduceRange(array &$from, array &$to) {
    $sameDate = ($from['year']  == $to['year']) &&
                ($from['month'] == $to['month']) &&
                ($from['day']   == $to['day']);
    if ($sameDate) {
      $to['year']  = NULL;
      $to['month'] = NULL;
      $to['day']   = NULL;
      return;
    }
    $hasTime =  isset($from['hour'])   || isset($to['hour']) ||
                isset($from['minute']) || isset($to['minute']) ||
                isset($from['second']) || isset($to['second']);
    if ($hasTime) {
      return;
    }
    if ($from['year'] == $to['year']) {
      $format = $this->getFormat();
      $year_weight = $format->getComponent('year')['weight'];
      $month_weight = $format->getComponent('month')['weight'];
      //If "year before month" compress right (otherwise left)
      if ($year_weight <= $month_weight) {
        $to['year'] = NULL;
      }
      else {
        $from['year'] = NULL;
      }

      if ($from['month'] == $to['month']) {
        $day_weight = $format->getComponent('month')['weight'];
        //If "month before day" compress right (otherwise left)
        if ($month_weight <= $day_weight) {
          $to['month'] = NULL;
        }
        else {
          $from['month'] = NULL;
        }
      }
    }
  }

}
