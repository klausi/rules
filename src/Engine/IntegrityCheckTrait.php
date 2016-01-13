<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\IntegrityCheckTrait.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Plugin\ContextAwarePluginInterface as CoreContextAwarePluginInterface;
use Drupal\rules\Exception\IntegrityException;

/**
 * Provides shared integrity checking methods for conditions and actions.
 */
trait IntegrityCheckTrait {

  /**
   * Performs the integrity check.
   *
   * @param CoreContextAwarePluginInterface $plugin
   *   The plugin with its defined context.
   * @param \Drupal\rules\Engine\ConfigurationStateInterface $config_state
   *   The current configuration state with all defined variables that are
   *   available.
   *
   * @throws \Drupal\rules\Exception\IntegrityException
   */
  protected function doIntegrityCheck(CoreContextAwarePluginInterface $plugin, ConfigurationStateInterface $config_state) {
    $context_definitions = $plugin->getContextDefinitions();
    foreach ($context_definitions as $name => $definition) {
      // Check if a data selector is configured that maps to the state.
      if (isset($this->configuration['context_mapping'][$name])) {
        $data_definition = $config_state->applyDataSelector($this->configuration['context_mapping'][$name]);

        if ($data_definition === NULL) {
          throw new IntegrityException('Data selector ' . $this->configuration['context_mapping'][$name] . " for context $name is invalid.");
        }
      }
    }
  }

}
