<?php


namespace Drupal\partial_date\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Description of PartialDateFormatListBuilder
 *
 * @author CosminFr
 */
class PartialDateFormatListBuilder extends ConfigEntityListBuilder {
  
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Partial date format');
    $header['id'] = $this->t('Machine name');
    //TODO add more...
    return $header + parent::buildHeader();
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    //TODO add more...
    return $row + parent::buildRow($entity);
  }
}
