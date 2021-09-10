<?php

namespace Drupal\partial_date;

use Drupal\Core\Render\Markup;
use Drupal\partial_date\Entity\PartialDateFormatInterface;

/**
 * Provides a default partial date formatter.
 */
class PartialDateFormatter implements PartialDateFormatterInterface {

  /**
   * {@inheritdoc}
   */
  public function format(array $date, PartialDateFormatInterface $format) {
    $components = [];

    $valid_components = partial_date_components();
    $last_type = FALSE;
    foreach ($format->getComponents() as $type => $component) {
      $markup = '';

      $separator = '';
      if ($last_type) {
        $separator_type = _partial_date_component_separator_type($last_type, $type);
        $separator = $format->getSeparator($separator_type);
      }

      if (isset($valid_components[$type])) {
        $display_type = $format->getDisplay($type);
        $estimate = empty($date[$type . '_estimate']) ? FALSE : $date[$type . '_estimate'];
        // If no estimate, switch to the date only formating option.
        if (!$estimate && ($display_type == 'date_or' || strpos($display_type, 'estimate_') === 0)) {
          $display_type = 'date_only';
        }

        switch ($display_type) {
          case 'none':
            // We need to avoid adding an empty option.
            continue 2;

          case 'date_only':
          case 'date_or':
            $markup = $this->formatComponent($type, $date, $format);
            break;

          case 'estimate_label':
            $markup = $date[$type . '_estimate_label'];
            // We no longer have a date / time like value.
            $type = 'other';
            break;

          case 'estimate_range':
            list($estimate_start, $estimate_end) = explode('|', $date[$type . '_estimate']);
            $start = $this->formatComponent($type, [$type => $estimate_start] + $date, $format);
            $end = $this->formatComponent($type, [$type => $estimate_end], $format);
            if (strlen($start) && strlen($end)) {
              $markup = t('@estimate_start to @estimate_end', array('@estimate_start' => $estimate_start, '@estimate_end' => $estimate_end));
            }
            elseif (strlen($estimate_start)) {
              $markup = $estimate_start;
            }
            elseif (strlen($estimate_end)) {
              $markup = $estimate_end;
            }
            break;

          case 'estimate_component':
            $markup = $this->formatComponent($type, [$type => $date[$type . '_estimate_value']] + $date, $format);
            break;
        }

        if (!strlen($markup)) {
          if (isset($component['empty']) && strlen($component['empty'])) {
            // What do we get? If numeric, assume a date / time component, otherwise
            // we can assume that we no longer have a date / time like value.
            $markup = $component['empty'];
            if (!is_numeric($markup)) {
              $type = 'other';
            }
          }
        }
        if (strlen($markup)) {
          if ($separator) {
            $components[] = $separator;
          }
          $components[] = $markup;
          $last_type = $type;
        }
      }
      elseif (isset($component['value']) && strlen($component['value'])) {
        if ($separator) {
          $components[] = $separator;
        }
        $components[] = $component['value'];
        $last_type = $type;
      }

    }
    // This is required to support the <sup> markup of ordinals.
    // @todo Find a different solution for ordinals.
    return Markup::create(implode('', $components));
  }

  /**
   * Formats a single date component.
   *
   * @param $key
   *   The component key.
   * @param $date
   *   The partial date array.
   * @param \Drupal\partial_date\Entity\PartialDateFormatInterface $format
   *   The partial date format.
   *
   * @return string
   *   The formatted component.
   */
  protected function formatComponent($key, $date, PartialDateFormatInterface $format) {
    $value = isset($date[$key]) && strlen($date[$key]) ? $date[$key] : FALSE;
    if (!$value) {
      return ''; //if component value is missing, return an empty string.
    }
    $keyFormat = $format->getComponent($key)['format'];

    // Hide year designation if no valid year.
    $year_designation = $format->getYearDesignation();
    if (empty($date['year'])) {
      $year_designation = '';
    }

    // If dealing with 12 hour times, recalculate the value.
    if ($keyFormat == 'h' || $keyFormat == 'g') {
      if ($value > 12) {
        $value -= 12;
      }
      elseif ($value == 0) {
        $value = '12';
      }
    }
    // Add suffixes for year and time formats
    $suffix = '';
    switch ($keyFormat) {
      case 'd-S':
      case 'j-S':
        $suffix = '<sup>' . DateTools::ordinalSuffix($value) . '</sup>';
        break;

      case 'y-ce':
      case 'Y-ce':
        $suffix = partial_date_year_designation_decorator($value, $year_designation);
        if (!empty($suffix) && !empty($value)) {
          $value = abs($value);
        }
        break;
    }

    switch ($keyFormat) {
      case 'y-ce':
      case 'y':
        return (strlen($value) > 2 ?  substr($value, - 2) : $value) . $suffix;

      case 'F':
        return DateTools::monthNames($value) . $suffix;

      case 'M':
        return DateTools::monthAbbreviations($value) . $suffix;

      // Numeric representation of the day of the week  0 (for Sunday) through 6 (for Saturday)
      case 'w':
        if (!empty($date['year']) && !empty($date['month'])) {
          return DateTools::dayOfWeek($date['year'], $date['month'], $value) . $suffix;
        }
        return '';

      // A full textual representation of the day of the week.
      case 'l':
        // A textual representation of a day, three letters.
      case 'D':
        if (!empty($date['year']) && !empty($date['month'])) {
          $day = DateTools::dayOfWeek($date['year'], $date['month'], $value);
          if ($keyFormat == 'D') {
            return DateTools::weekdayAbbreviations($day, 3) . $suffix;
          } else {
            return DateTools::weekdayNames($day) . $suffix;
          }
        }
        return '';

      case 'n':
      case 'j':
      case 'j-S':
      case 'g':
      case 'G':
        return intval($value) . $suffix;

      case 'd-S':
      case 'd':
      case 'h':
      case 'H':
      case 'i':
      case 's':
      case 'm':
        return sprintf('%02s', $value) . $suffix;

      case 'Y-ce':
      case 'Y':
      case 'e':
        return $value . $suffix;

      case 'T':
        try {
          $tz = new \DateTimeZone($value);
          $transitions = $tz->getTransitions();
          return $transitions[0]['abbr']  . $suffix;
        }
        catch (\Exception $e) {}
        return '';


      // Todo: implement
      // Year types
      // ISO-8601 year number
      case 'o':

        // Day types
        // The day of the year
      case 'z':
        // ISO-8601 numeric representation of the day of the week
      case 'N':

        // Timezone offsets
        // Whether or not the date is in daylight saving time
      case 'I':
        // Difference to Greenwich time (GMT) in hours
      case 'O':
        // Difference to Greenwich time (GMT) with colon between hours and minutes
      case 'P':
        // Timezone offset in seconds
      case 'Z':

      default:
        return '';
    }
  }

}
