<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ConfigurationState.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Language\LanguageInterface;
use Drupal\rules\Exception\RulesEvaluationException;

/**
 * The state used during configuration time holding data definitions.
 */
class ConfigurationState {

  /**
   * The known data definitions.
   *
   * @var \Drupal\Core\TypedData\DataDefinitionInterface
   */
  protected $dataDefinitions = [];

  /**
   * Creates the object.
   *
   * @param \Drupal\Core\TypedData\DataDefinitionInterface[] $data_definitions
   *   (optional) Data definitions to initialize this state with.
   *
   * @return static
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
  public function addDataDefinition($name, \Drupal\Core\TypedData\DataDefinitionInterface $definition) {
    $this->dataDefinitions[$name] = $definition;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDataDefinition($name) {
    if (!array_key_exists($name, $this->dataDefinitions)) {
      throw new RulesEvaluationException("Unable to get variable $name, it is not defined.");
    }
    return $this->variables[$name];
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
