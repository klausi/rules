<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\DataSet.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Component\Utility\String;

/**
 * Provides a 'Data set' action.
 *
 * @Action(
 *   id = "rules_data_set",
 *   label = @Translation("Set data"),
 *   category = @Translation("Data"),
 *   context = {
 *     "original_value" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("Specifies the data to be modified using a data selector, e.g. 'node:author:name'.")
 *     ),
 *     "replacement_value" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("The new value to set for the specified data.")
 *     )
 *   },
 *   provides = {
 *     "result" = @ContextDefinition("any",
 *        label = @Translation("Result")
 *      )
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
   *
   * @todo Add selector to save data exception.
   */
  public function execute() {
    $original_value = $this->getContext('original_value');
    $replacement_value = $this->getContext('replacement_value');

    // Both values are equal.
    if ($original_value->getContextValue() === $replacement_value->getContextValue()) {
      $this->setProvidedValue('result', $original_value->getContextValue());
    }

    // Primitives of same type.
    if (is_scalar($original_value->getContextValue()) && is_scalar($replacement_value->getContextValue()) && gettype($original_value->getContextValue()) == gettype($replacement_value->getContextValue())) {
      $this->setProvidedValue('result', $original_value->getContextValue());
    }

    // TypedDataManager values of same type.
    elseif ($original_value->getContextDefinition() == $replacement_value->getContextDefinition()) {
      $this->setProvidedValue('result', $original_value->setContextValue($replacement_value->getContextValue()));
    }


    // Cannot evaluate.
    else {
      $this->setProvidedValue('result', FALSE);
    }

  }

}
