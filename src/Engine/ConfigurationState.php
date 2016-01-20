<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ConfigurationState.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\rules\Entity\ReactionRuleConfig;

/**
 * The state used during configuration time holding data definitions.
 */
class ConfigurationState implements ConfigurationStateInterface {

  /**
   * The known data definitions.
   *
   * @var \Drupal\Core\TypedData\DataDefinitionInterface
   */
  protected $dataDefinitions = [];

  /**
   * {@inheritdoc}
   */
  public static function create($data_definitions = []) {
    return new static($data_definitions);
    // @todo Initialize the global "site" variable.
  }

  public static function createFromConfig(ConfigEntityInterface $rules_config) {
    $state = static::create();
    if ($rules_config instanceof ReactionRuleConfig) {
      $event_name = $rules_config->getEvent();
      $event_definition = \Drupal::service('plugin.manager.rules_event')->getDefinition($event_name);
      foreach ($event_definition['context'] as $context_name => $context_definition) {
        $state->addDataDefinition($context_name, $context_definition->getDataDefinition());
      }
    }
    return $state;
  }

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\TypedData\DataDefinitionInterface[] $data_definitions
   *   (optional) Data definitions to initialize this state with.
   */
  protected function __construct($data_definitions) {
    $this->dataDefinitions = $data_definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function addDataDefinition($name, DataDefinitionInterface $definition) {
    $this->dataDefinitions[$name] = $definition;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDataDefinition($name) {
    // @todo do we need this?
  }

  /**
   * {@inheritdoc}
   */
  public function hasDataDefinition($name) {
    return array_key_exists($name, $this->dataDefinitions);
  }

  /**
   * {@inheritdoc}
   */
  public function applyDataSelector($selector, $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED) {
    $parts = explode(':', $selector, 2);

    if (count($parts) == 1 && isset($this->dataDefinitions[$parts[0]])) {
      return $this->dataDefinitions[$parts[0]];
    }
    return NULL;
  }

}
