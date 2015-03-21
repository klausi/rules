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
 * @Action(
 *   id = "rules_data_set",
 *   label = @Translation("Set data"),
 *   category = @Translation("Data"),
 *   context = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Value")
 *     ),
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Value")
 *     )
 *   },
 *   provides = {
 *     "conversion_result" = @ContextDefinition("any",
 *        label = @Translation("Conversion result")
 *      )
 *   }
 * )
 * @todo Add various input restrictions.
 */
class DataSet extends RulesActionBase {

  /**
   * Executes the plugin.
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
