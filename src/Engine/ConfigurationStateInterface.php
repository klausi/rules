<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ConfigurationStateInterface.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;

/**
 * The state used during configuration time holding data definitions.
 */
interface ConfigurationStateInterface {

  /**
   * Creates the object.
   *
   * @param \Drupal\Core\TypedData\DataDefinitionInterface[] $data_definitions
   *   (optional) Data definitions to initialize this state with.
   *
   * @return static
   */
  public static function create($data_definitions = []);

  /**
   * Adds a data definition to the configuration state.
   *
   * @param string $name
   *   Variable name of the data definition.
   * @param \Drupal\Core\TypedData\DataDefinitionInterface $definition
   *   The data definition that represents the variable.
   */
  public function addDataDefinition($name, DataDefinitionInterface $definition);

  /**
   * Retrieve a data definition in this configuration state.
   *
   * @param string $name
   *   The variable name to get the data definition for.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   The data definition.
   */
  public function getDataDefinition($name);

  /**
   * Checks if the variable with the given name is present in the state.
   *
   * @param string $name
   *   The variable name.
   *
   * @return bool
   *   TRUE if the config state has that variable, FALSE otherwise.
   */
  public function hasDataDefinition($name);

  /**
   * Applies a data selector and returns the corresponding data definition.
   *
   * @param string $selector
   *   The selector, example: node:title:value
   * @param string $langcode
   *   The langauge code.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface|null
   *   A data definition if the selector could be applied, NULL otherwise.
   */
  public function applyDataSelector($selector, $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED);

}
