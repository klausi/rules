<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ExpressionContainerInterface.
 */

namespace Drupal\rules\Engine;

use Drupal\rules\Context\ContextConfig;

/**
 * Defines a common interface for expressions containing other expressions.
 *
 * Usually expression containers also implement the
 * ActionExpressionContainerInterface or ConditionExpressionContainerInterface in order
 * to denote whether it contains action or condition expressions.
 */
interface ExpressionContainerInterface extends ExpressionInterface, \IteratorAggregate {

  /**
   * Creates and adds an expression.
   *
   * @param string $plugin_id
   *   The id of the expression plugin to add.
   * @param \Drupal\rules\Context\ContextConfig $config
   *   (optional) The configuration for the specified plugin.
   *
   * @throws \Drupal\rules\Exception\InvalidExpressionException
   *   Thrown if the wrong expression is passed; e.g. if a condition expression
   *   is added to an action expression container.
   *
   * @return $this
   */
  public function addExpression($plugin_id, ContextConfig $config = NULL);

  /**
   * Adds an expression object.
   *
   * @param \Drupal\rules\Engine\ExpressionInterface $expression
   *   The expression object.
   *
   * @throws \Drupal\rules\Exception\InvalidExpressionException
   *   Thrown if the wrong expression is passed; e.g. if a condition expression
   *   is added to an action expression container.
   *
   * @return $this
   */
  public function addExpressionObject(ExpressionInterface $expression);

}
