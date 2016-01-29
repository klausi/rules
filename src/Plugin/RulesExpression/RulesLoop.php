<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesExpression\RulesLoop.
 */

namespace Drupal\rules\Plugin\RulesExpression;

use Drupal\rules\Engine\ActionExpressionContainer;
use Drupal\rules\Engine\ExecutionStateInterface;

/**
 * Holds a set of actions that are executed over the iteration of a list.
 *
 * @RulesExpression(
 *   id = "rules_loop",
 *   label = @Translation("Action set"),
 *   form_class = "\Drupal\rules\Form\Expression\ActionSetForm"
 * )
 */
class RulesLoop extends ActionExpressionContainer {

  /**
   * {@inheritdoc}
   */
  public function executeWithState(ExecutionStateInterface $state) {
    $list_data = $state->getVariable($this->configuration['list']);
    foreach ($list_data as $item) {
      // @todo The loop should specify a variable name for the list item so that
      // nested loops work.
      $state->addVariableData('list_item', $item);
      foreach ($this->actions as $action) {
        $action->executeWithState($state);
      }
    }
    // After the loop the list item is out of scope and cannot be used by any
    // following actions.
    $state->deleteVariable('list_item');
  }

}
