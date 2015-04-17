<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\DataSet.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\Core\Entity\Entity;
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
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("Specifies the data to be modified using a data selector, e.g. "node:author:name".")
 *     ),
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("The new value to set for the specified data.")
 *     )
 *   },
 *   provides = {
 *     "conversion_result" = @ContextDefinition("any",
 *        label = @Translation("Conversion result")
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
    $data = $this->getContextValue('value');
    $value = $this->getContextValue('value');

    // This shoudn't work. And probably doesn't.
    if ($data instanceof Entity) {
      $data->set($value);
    }
    else {
      // A not wrapped variable (e.g. a number) is being updated. Just overwrite
      // the variable with the new value.
      return array('data' => $value);
      $this->setProvidedValue('data', $value);
    }
  }

}
