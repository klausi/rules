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
    // Use a configured list item variable name, otherwise fall back to just
    // 'list_item' as variable name.
    $list_item_name = isset($this->configuration['list_item']) ? $this->configuration['list_item'] : 'list_item';

    foreach ($list_data as $item) {
      $state->addVariableData($list_item_name, $item);
      foreach ($this->actions as $action) {
        $action->executeWithState($state);
      }
    }
    // After the loop the list item is out of scope and cannot be used by any
    // following actions.
    $state->deleteVariable($list_item_name);
  }

}
