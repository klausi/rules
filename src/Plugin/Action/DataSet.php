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
 *     "original" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("Specifies the data to be modified using a data selector, e.g. 'node:author:name'.")
 *     ),
 *     "replacement" = @ContextDefinition("any",
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
    $original = $this->getContext('original');
    $original_value = $original->getContextValue();

    $replacement = $this->getContext('replacement');
    $replacement_value = $replacement->getContextValue();


    // Both values are equal.
    if ($original_value === $replacement_value) {
      $this->setProvidedValue('result', $original_value);
    }


    // Primitives.
    elseif (is_scalar($original_value) && is_scalar($replacement_value)) {

      // Primitives of same type.
      if (gettype($original_value) == gettype($replacement_value)) {
        $this->setProvidedValue('result', $original->setContextValue($replacement_value));
      }
      else {
        $this->setProvidedValue('result', FALSE);
      }
    }


    // TypedDataManager values of same type.
    // @todo: make this work and tested.
    elseif ('TypedDataManager' == get_class($original) && 'TypedDataManager' == get_class($replacement)) {
      $this->setProvidedValue('result', $original->setContextValue($replacement_value));
    }


    // Cannot evaluate.
    else {
      $this->setProvidedValue('result', FALSE);
    }

  }

}
