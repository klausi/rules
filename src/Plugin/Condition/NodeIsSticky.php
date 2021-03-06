<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Condition\NodeIsSticky.
 */

namespace Drupal\rules\Plugin\Condition;

use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Engine\RulesConditionBase;

/**
 * Provides a 'Node is sticky' condition.
 *
 * @Condition(
 *   id = "rules_node_is_sticky",
 *   label = @Translation("Node is sticky")
 * )
 *
 * @todo: Add access callback information from Drupal 7.
 * @todo: Add group information from Drupal 7.
 */
class NodeIsSticky extends RulesConditionBase {

  /**
   * {@inheritdoc}
   */
  public static function contextDefinitions() {
    $contexts['node'] = ContextDefinition::create('entity:node')
      ->setLabel(t('Node'));

    return $contexts;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('Node is sticky');
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $node = $this->getContextValue('node');
    return $node->isSticky();
  }

}
