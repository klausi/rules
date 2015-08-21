<?php

/**
 * @file
 * Contains \Drupal\rules\Context\ContextDefinitionInterface.
 */

namespace Drupal\rules\Context;

use \Drupal\Core\Plugin\Context\ContextDefinitionInterface as ContextDefinitionInterfaceCore;

/**
 * Context definition information required by Rules.
 *
 * The core interface is extended to add properties that are necessary for
 * Rules.
 */
interface ContextDefinitionInterface extends ContextDefinitionInterfaceCore {

  /**
   * Constants for the context parameter restriction mode.
   *
   * @see ::getParameterRestriction()
   */
  const PARAMETER_RESTRICTION_INPUT = 'input';
  const PARAMETER_RESTRICTION_SELECTOR = 'selector';

  /**
   * Determines if the context value is allowed to be NULL.
   *
   * @return bool
   *   TRUE if NULL values are allowed, FALSE otherwise.
   */
  public function isAllowedNull();

  /**
   * Sets the "allow NULL value" behavior.
   *
   * @param bool $null_allowed
   *   TRUE if NULL values should be allowed, FALSE otherwise.
   *
   * @return $this
   */
  public function setAllowNull($null_allowed);

  /**
   * Determines if this context has a parameter restriction.
   *
   * @return string|null
   *   Either PARAMETER_RESTRICTION_INPUT for context parameters that are only
   *   allowed to be provided as input values, PARAMETER_RESTRICTION_SELECTOR
   *   for context parameters that must be provided as data selectors or NULL if
   *   there is no restriction for this context.
   */
  public function getParameterRestriction();

  /**
   * Sete the parameter restriction mode for this context.
   *
   * @param string|null $restriction
   *   Either PARAMETER_RESTRICTION_INPUT for context parameters that are only
   *   allowed to be provided as input values, PARAMETER_RESTRICTION_SELECTOR
   *   for context parameters that must be provided as data selectors or NULL if
   *   there is no restriction for this context.
   */
  public function setParameterRestriction($restriction);

}
