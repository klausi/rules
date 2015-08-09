<?php

/**
 * @file
 * Contains \Drupal\rules\Core\RulesPluginTrait.
 */

namespace Drupal\rules\Core;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;

/**
 * Offers common methods for context plugin implementers.
 */
trait ExecutablePluginTrait {

  /**
   * Get the translated label from the plugin definition.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the label is not defined for the plugin.
   *
   * @return string
   *   The label of the plugin.
   */
  protected function getLabelValue() {
    $definition = $this->getPluginDefinition();
    if (empty($definition['label'])) {
      throw new InvalidPluginDefinitionException('The label is not defined for plugin ' . $this->getPluginId() . ' (' . __CLASS__ . ').');
    }
    return $definition['label'];
  }

  /**
   * Get the translated summary from the label annotation.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if a summary was not set.
   *
   * @return string
   *   The summary of the plugin.
   */
  public function summary() {
    return $this->getLabelValue();
  }

}
