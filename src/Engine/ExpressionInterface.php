<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ExpressionInterface.
 */

namespace Drupal\rules\Engine;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Executable\ExecutableInterface;

/**
 * Defines the interface for Rules expressions.
 *
 * @see \Drupal\rules\Engine\ExpressionManager
 */
interface ExpressionInterface extends ExecutableInterface, ConfigurablePluginInterface {

  /**
   * Execute the expression with a given Rules state.
   *
   * Note that this does not auto-save any changes.
   *
   * @param \Drupal\rules\Engine\ExecutionStateInterface $state
   *   The state with all the execution variables in it.
   *
   * @return null|bool
   *   The expression may return a boolean value after execution, this is used
   *   by conditions that return their evaluation result.
   *
   * @throws \Drupal\rules\Exception\RulesEvaluationException
   *   Thrown if the Rules expression triggers errors during execution.
   */
  public function executeWithState(ExecutionStateInterface $state);

  /**
   * Returns the form handling class for this expression.
   *
   * @return \Drupal\rules\Form\Expression\ExpressionFormInterface|null
   *   The form handling object if there is one, NULL otherwise.
   */
  public function getFormHandler();

  /**
   * Returns the root expression if this expression is nested.
   *
   * @return \Drupal\rules\Engine\ExpressionInterface
   *   The root expression or $this if the expression is the root element
   *   itself.
   */
  public function getRoot();

  /**
   * Set the root expression for this expression if it is nested.
   *
   * @param \Drupal\rules\Engine\ExpressionInterface $root
   *   The root expression object.
   */
  public function setRoot(ExpressionInterface $root);

  /**
   * Gets the config entity ID this expression is associatedd with.
   *
   * @return string|null
   *   The config entity ID or NULL if the expression is not associated with a
   *   config entity.
   */
  public function getConfigEntityId();

  /**
   * Sets the config entity this expression is associated with.
   *
   * @param string $id
   *   The config entity ID.
   */
  public function setConfigEntityId($id);

  /**
   * The label of this expression element that can be shown in the UI.
   *
   * @return string
   *   The label for display.
   */
  public function getLabel();

  /**
   * Verifies that this expression is configured correctly.
   *
   * Example: all variable names used in the expression are available.
   *
   * @param \Drupal\rules\Engine\ExecutionMetadataStateInterface $metadata_state
   *   The configuration state used to hold available data definitions of
   *   variables.
   *
   * @return \Drupal\rules\Engine\IntegrityViolationList
   *   A list object containing \Drupal\rules\Engine\IntegrityViolation objects.
   */
  public function checkIntegrity(ExecutionMetadataStateInterface $metadata_state);

  /**
   * Returns the UUID of this expression if it is nested in another expression.
   *
   * @return string|null
   *   The UUID if this expression is nested or NULL if it does not have a UUID.
   */
  public function getUuid();

  /**
   * Sets the UUID of this expression in an expression tree.
   *
   * @param string $uuid
   *   The UUID to set.
   */
  public function setUuid($uuid);

  public function prepareExecutionMetadataState(ExecutionMetadataStateInterface $metadata_state);

}
