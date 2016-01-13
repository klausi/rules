<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ConfigurationState.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;

/**
 * The state used during configuration time holding data definitions.
 *
 * @todo create interface for this.
 * @todo move integrityCheckUntil() to expression container interface.
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
