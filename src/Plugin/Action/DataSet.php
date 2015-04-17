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
use Drupal\rules\Exception\RulesEvaluationException;

/**
 * Provides a 'Data set' action.
 *
 * @Action(
 *   id = "rules_data_set",
 *   label = @Translation("Set data"),
 *   category = @Translation("Data"),
 *   context = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("Specifies the data to be modified using a data selector, e.g. 'node:author:name'.")
 *     ),
 *     "value" = @ContextDefinition("any",
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
    $data = $this->getContextValue('data');
    $value = $this->getContextValue('value');

////    $result = $value;
//    if ($data instanceof Entity && $value instanceof Entity) {
//
//    }

//    // This shoudn't work. And probably doesn't.
//    if ($data instanceof Entity && $value instanceof Entity) {
//      try {
//        // Update the value first then save changes, if possible.
//        $data->set($value);
//      }
//      catch (EntityStorageException $e) {
//        throw new RulesEvaluationException('Unable to modify data "@selector": ' . $e->getMessage(), array('@selector' => $settings['data:select']));
//      }
//      // Save changes if a property of a variable has been changed.
//      if (strpos($element->settings['data:select'], ':') !== FALSE) {
//        $info = $wrapper->info();
//        // We always have to save the changes in the parent entity. E.g. when the
//        // node author is changed, we don't want to save the author but the node.
//        $state->saveChanges(implode(':', explode(':', $settings['data:select'], -1)), $info['parent']);
//      }
//      try {
//
//      }
//      $data->set($value);
//    }
//    else {
//      // A not wrapped variable (e.g. a number) is being updated. Just overwrite
//      // the variable with the new value.
//      return array('data' => $value);
//      $this->setProvidedValue('data', $value);
//    }

    // Set a variable if of equal type.
    if (gettype($data) === gettype($value)) {
      $result = $value;
    }
    else {
      throw new RulesEvaluationException('Types are not equal');
    }

    // Defaults to FALSE.
    if (isset($result) && $result !== FALSE) {
      $this->setProvidedValue('result', $result);
    }
    else {
      throw new RulesEvaluationException('Could not finish operation.');
    }

  }

}
