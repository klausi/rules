<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesExpression\RulesLoop.
 */

namespace Drupal\rules\Plugin\RulesExpression;

use Drupal\rules\Engine\ActionExpressionContainer;
use Drupal\rules\Engine\ExecutionMetadataStateInterface;
use Drupal\rules\Engine\ExecutionStateInterface;
use Drupal\rules\Engine\IntegrityViolationList;

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

  /**
   * {@inheritdoc}
   */
  public function checkIntegrity(ExecutionMetadataStateInterface $metadata_state) {
    $violation_list = new IntegrityViolationList();

    if (empty($this->configuration['list'])) {
      $violation_list->addViolationWithMessage($this->t('List variable is missing.'));
    }
    elseif (!$metadata_state->hasDataDefinition($this->configuration['list'])) {
      $violation_list->addViolationWithMessage($this->t('List variable %list does not exist.', [
        '%list' => $this->configuration['list'],
      ]));
    }

    $list_item_name = isset($this->configuration['list_item']) ? $this->configuration['list_item'] : 'list_item';
    if ($metadata_state->hasDataDefinition($list_item_name)) {
      $violation_list->addViolationWithMessage($this->t('List item name %name conflicts with an existing variable.', [
        '%name' => $list_item_name,
      ]));
    }

    return $violation_list;
  }

}
