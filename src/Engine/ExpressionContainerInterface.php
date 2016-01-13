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
 * ActionExpressionContainerInterface or ConditionExpressionContainerInterface
 * in order to denote whether it contains action or condition expressions.
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

  /**
   * Looks up the expression by UUID in this container.
   *
   * @param string $uuid
   *   The UUID of the expression.
   *
   * @return \Drupal\rules\Engine\ExpressionInterface|false
   *   The expression object or FALSE if not expression object with that UUID
   *   could be found.
   */
  public function getExpression($uuid);

  /**
   * Deletes an expression indentified by the specified UUID in the container.
   *
   * @param string $uuid
   *   The UUID of the expression.
   *
   * @return bool
   *   TRUE if an expression was deleted, FALSE if no expression with that UUID
   *   was found.
   */
  public function deleteExpression($uuid);

  /**
   * Verifies that the expression is valid up until a nested expression.
   *
   * @param string $uuid
   *   The UUID of the nested expression until the integrity check should be
   *   performed. Once the nested expression is reached the integrity check
   *   stops.
   * @param \Drupal\rules\Engine\ConfigurationStateInterface $config_state
   *   The configuration state used to hold available data definitions of
   *   variables.
   *
   * @return bool
   *   TRUE if the UUID was found and integrity checking has been done up to
   *   that point. FALSE otherwise.
   *
   * @throws \Drupal\rules\Exception\IntegrityException
   */
  public function integrityCheckUntil($uuid, ConfigurationStateInterface $config_state);

}
