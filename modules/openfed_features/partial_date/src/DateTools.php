<?php

/*
 * Static "stand-alone" date related functions.
 *
 * Note: It would be great to move these to the date_api.
 *
 */

namespace Drupal\partial_date;

use Drupal\Component\Utility\Unicode;

/**
 * Description of DateTools
 *
 * @author CosminFr
 */
class DateTools {

  /**
   * The minimum year that the module supports.
   *
   * Modifying this will alter how new timestamps are stored in the database.
   *
   * Setting to the something huge like 999999999999 should be OK, which would
   * be needed for things like the scienific age of the universe. +/- 2000 years
   * do not calculate leap years etc, and use a fixed number of seconds per year
   * based on the length of the tropical year in 2000.
   *
   * @var int
   */
  const YEAR_MIN = -999999999999;

  /**
   * The maximum year that the module supports.
   *
   * Modifying this will alter how new timestamps are stored in the database.
   *
   * @var int
   */
  const YEAR_MAX = 999999999999;

  /**
   * The number of seconds for a tropical year in 2000.
   *
   * Outside of the 1AD to 3999AD, leap years are ignored and a set number of
   * seconds per year are used to calculate the number seconds per year for the
   * timestamp estimations. This is a float column, so the percision of this
   * should be calculated to decide if this can be reduced even more.
   *
   * @var int
   */
  const SECONDS_PER_YEAR = 31556925;

  /**
   * Returns true, if given $year is a leap year.
   *
   * @param  integer $year
   * @return boolean true, if year is leap year
   */
  public static function isLeapYear($year) {
    if (empty($year)) {
      return FALSE;
    }
    if ($year < 1582) {
      // pre Gregorio XIII - 1582
      return $year % 4 == 0;
    } else {
      // post Gregorio XIII - 1582
      return (($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0);
    }
  }

  /**
   * Maps out the valid month ranges for a given year.
   *
   * @param int $year
   * @return array
   *   Note, there is no array index.
   */
  public static function monthMatrix($year = NULL) {
    if ($year && static::isLeapYear($year)) {
      return array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    }
    return array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  }

  public static function lastDayOfMonth($month, $year = NULL) {
    $matrix = self::monthMatrix($year);
    return $matrix[$month];
  }

  /**
   * Returns a translated array of month names.
   */
  public static function monthNames($month = NULL) {
    static $month_names;
    if (empty($month_names)) {
      $month_names = array(
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May',
        6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September',
        10 => 'October', 11 => 'November', 12 => 'December');

      foreach ($month_names as $key => $month_name) {
        $month_names[$key] = t($month_name, array(), array('context' => 'datetime'));
      }
    }
    if ($month) {
      return $month_names[$month];
    }
    return $month_names;
  }

  /**
   * Returns a translated array of short month names.
   */
  public static function monthAbbreviations($month) {
    static $month_names;
    if (empty($month_names)) {
      $month_names = array(
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
      foreach ($month_names as $key => $month_name) {
        $month_names[$key] = t($month_name, array(), array('context' => 'datetime'));
      }
    }
    if ($month) {
      return $month_names[$month];
    }
    return $month_names;
  }

  /**
   * Returns a translated array of weekday names.
   */
  public static function weekdayNames($week_day_number) {
    static $day_names;
    if (empty($day_names)) {
      $day_names = array(
        0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday');

      foreach ($day_names as $key => $day_name) {
        $day_names[$key] = t($day_name, array(), array('context' => 'datetime'));
      }
    }
    if ($week_day_number) {
      return $day_names[$week_day_number];
    }
    return $day_names;
  }

  /**
   * Returns a translated array of weekday names.
   */
  public static function weekdayAbbreviations($week_day_number, $length = 3) {
    $name = STATIC::weekdayNames($week_day_number);
    if (mb_strlen($name) > $length) {
      return mb_substr($name, 0, $length);
    }
    return $name;
  }

  /**
   * Gets day of week, 0 = Sunday through 6 = Saturday.
   *
   * Pope Gregory removed 10 days - October 5 to October 14 - from the year 1582
   * and proclaimed that from that time onwards 3 days would be dropped from the
   * calendar every 400 years.
   *
   * Thursday, October 4, 1582 (Julian) was followed immediately by Friday,
   * October 15, 1582 (Gregorian).
   *
   * @see PEAR::Date_Calc
   */
  public static function dayOfWeek($year, $month, $day) {
    $greg_correction = 0;
    if ($year < 1582 || ($year == 1582 && ($month < 10 || ($month == 10 && $day < 15)))) {
      $greg_correction = 3;
    }

    if ($month > 2) {
      $month -= 2;
    }
    else {
      $month += 10;
      $year--;
    }

    $result = floor((13 * $month - 1) / 5) +
           $day + ($year % 100) +
           floor(($year % 100) / 4) +
           floor(($year / 100) / 4) - 2 *
           floor($year / 100) + 77 + $greg_correction;

    return $result - 7 * floor($result / 7);
  }

  /**
   * Returns a translated ordinal suffix for a given day of the month.
   */
  public static function ordinalSuffix($day) {
    if (empty($day)) {
      return '';
    }
    static $suffixes;
    if (empty($suffixes)) {
      $suffixes = array(
        'st' => t('st', array(), array('context' => 'datetime')),
        'nd' => t('nd', array(), array('context' => 'datetime')),
        'rd' => t('rd', array(), array('context' => 'datetime')),
        'th' => t('th', array(), array('context' => 'datetime')),
      );
    }
    switch (($day = abs($day)) % 100) {
      case 11:
      case 12:
      case 13:
        return $suffixes['th'];
      default:
        switch ($day % 10) {
          case 1:
            return $suffixes['st'];
          case 2:
            return $suffixes['nd'];
          case 3:
            return $suffixes['rd'];
          default:
            return $suffixes['th'];
        }
    }
  }


}
