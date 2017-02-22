<?php

namespace Drupal\partial_date\Entity;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the FormatType config entity.
 * 
 * @ConfigEntityType(
 *   id = "partial_date_format",
 *   label = @Translation("Partial date format"),
 *   handlers = {
 *     "list_builder" = "Drupal\partial_date\Controller\PartialDateFormatListBuilder",
 *     "form" = {
 *        "add" = "Drupal\partial_date\Form\PartialDateFormatEditForm",
 *        "edit" = "Drupal\partial_date\Form\PartialDateFormatEditForm",
 *        "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "format",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "collection" = "/admin/config/regional/partial-date-format",
 *     "add-form" = "/admin/config/regional/partial-date-format/add",
 *     "edit-form" = "/admin/config/regional/partial-date-format/manage/{partial_date_format}",
 *     "delete-form" = "/admin/config/regional/partial-date-format/manage/{partial_date_format}/delete",
 *   },
 * )
 *
 * @author CosminFr
 */
class PartialDateFormat extends ConfigEntityBase implements PartialDateFormatInterface {

  /**
   * @var string
   */
  protected $id;
  
  /**
   * @var string
   */
  protected $meridiem = 'a';
  
  /**
   * @var string
   * This controls how year designation is handled: 1BC = 1BCE = -1 and 1AD = 1CE = 1.
   */
  protected $year_designation = 'ce';
  
  /**
   * @var array
   */
  protected $display = [
    'year' => 'estimate_label',
    'month' => 'estimate_label',
    'day' => 'estimate_label',
    'hour' => 'estimate_label',
    'minute' => 'estimate_label',
    'second' => 'none',
    'timezone' => 'none',
  ];
  
  /**
   * @var array
   */
  protected $components = array(
    'year' => array('format' => 'y-ce', 'empty' => '', 'weight' => 0), 
    'month' => array('format' => 'm', 'empty' => '', 'weight' => 1),
    'day' => array('format' => 'j', 'empty' => '', 'weight' => 2),
    'hour' => array('format' => 'H', 'empty' => '', 'weight' => 3),
    'minute' => array('format' => 'i', 'empty' => '', 'weight' => 4),
    'second' => array('format' => 's', 'empty' => '', 'weight' => 5),
    'timezone' => array('format' => 'T', 'empty' => '', 'weight' => 6),
    'approx' => array('value' => '', 'weight'=> -1),
    'c1' => array('value' => '', 'weight'=> 7),
    'c2' => array('value' => '', 'weight'=> 8),
    'c3' => array('value' => '', 'weight'=> 9),
  );

  /**
   * @var array
   * An array with specific separators.
   */
  protected $separator = [
    'date' => '/',
    'time' => ':',
    'datetime' => ' ',
    'range' => ' to ',
    'other' => ' ',
  ];

  /**
   * {@inheritdoc}
   */
  public function getMeridiem() {
    return $this->get('meridiem');
  }

   /**
    * {@inheritdoc}
    */
   public function getYearDesignation() {
     return $this->get('year_designation');
   }

    /**
     * {@inheritdoc}
     */
    public function getDisplay($component) {
      assert('in_array($component, ["year", "month", "day", "hour", "minute", "second", "timezone"], TRUE)');
      return $this->get('display')[$component];
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent($component_name) {
      assert('in_array($component, ["year", "month", "day", "hour", "minute", "second", "timezone", "approx", "c1", "c2", "c3"], TRUE)');
      return $this->get('components')[$component_name];
    }

    /**
     * {@inheritdoc}
     */
    public function getComponents() {
      $components = $this->get('components');
      uasort($components, [SortArray::class, 'sortByWeightElement']);
      return $components;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparator($component) {
      assert('in_array($component, ["date", "time", "datetime", "range", "other"], TRUE)');
      return $this->get('separator')[$component];
    }

}
