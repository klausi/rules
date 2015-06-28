<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesAction\DataSet.
 */

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a 'Data set' action.
 *
 * @RulesAction(
 *   id = "rules_data_set",
 *   label = @Translation("Set a data value"),
 *   category = @Translation("Data"),
 *   context = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Data"),
 *       description = @Translation("Specifies the data to be modified using a data selector, e.g. 'node:author:name'.")
 *     ),
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("The new value to set for the specified data.")
 *     )
 *   }
 * )
 * @todo Add various input restrictions: selector on 'data'.
 * @todo Add 'wrapped' on 'data'.
 * @todo 'allow NULL' and 'optional' for both 'data' and 'value'.
 *
 * @todo Use TypedDataManager to compare value types.
 * @todo Save parent (entity where fields belong to) if set.
 */
class DataSet extends RulesActionBase {

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('Set a data value.');
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $typed_data = $this->getContext('data')->getContextData();
    $typed_data->setValue($this->getContextValue('value'));
  }

}
