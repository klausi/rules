<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ConfigurationState.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\TypedData\ComplexDataInterface;
use Drupal\Core\TypedData\DataReferenceInterface;
use Drupal\Core\TypedData\ListInterface;
use Drupal\Core\TypedData\TranslatableInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\TypedData\TypedDataTrait;
use Drupal\rules\Context\ContextDefinitionInterface;
use Drupal\rules\Exception\RulesEvaluationException;

/**
 * The state used during configuration time holding data definitions.
 */
class ConfigurationState {

  use TypedDataTrait;

  /**
   * The known data definitions.
   *
   * @var \Drupal\Core\TypedData\DataDefinitionInterface
   */
  protected $dataDefinitions = [];

  /**
   * Creates the object.
   *
   * @param \Drupal\Core\TypedData\DataDefinitionInterface[] $dataDefinitions
   *   (optional) Variables to initialize this state with.
   *
   * @return static
   */
  public static function create($dataDefinitions = []) {
    return new static($dataDefinitions);
    // @todo Initialize the global "site" variable.
  }

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\TypedData\TypedDataInterface[] $dataDefinitions
   *   (optional) Variables to initialize this state with.
   */
  protected function __construct($dataDefinitions) {
    $this->variables = $dataDefinitions;
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
    $typed_data = $this->getVariable($parts[0]);

    if (count($parts) == 1) {
      return $typed_data;
    }
    $current_selector = $parts[0];
    foreach (explode(':', $parts[1]) as $name) {
      // If the current data is just a reference then directly dereference the
      // target.
      if ($typed_data instanceof DataReferenceInterface) {
        $typed_data = $typed_data->getTarget();
        if ($typed_data === NULL) {
          throw new RulesEvaluationException("Unable to apply data selector $current_selector. The specified reference is NULL.");
        }
      }

      // Make sure we are using the right language.
      if ($typed_data instanceof TranslatableInterface) {
        if ($typed_data->hasTranslation($langcode)) {
          $typed_data = $typed_data->getTranslation($langcode);
        }
        // @todo What if the requested translation does not exist? Currently
        // we just ignore that and continue with the current object.
      }

      // If this is a list but the selector is not an integer, we forward the
      // selection to the first element in the list.
      if ($typed_data instanceof ListInterface && !ctype_digit($name)) {
        $typed_data = $typed_data->offsetGet(0);
      }

      $current_selector .= ":$name";

      // Drill down to the next step in the data selector.
      if ($typed_data instanceof ListInterface || $typed_data instanceof ComplexDataInterface) {
        try {
          $typed_data = $typed_data->get($name);
        }
        catch (\InvalidArgumentException $e) {
          // In case of an exception, re-throw it.
          throw new RulesEvaluationException("Unable to apply data selector $current_selector: " . $e->getMessage());
        }
      }
      else {
        throw new RulesEvaluationException("Unable to apply data selector $current_selector. The specified variable is not a list or a complex structure: $name.");
      }
    }

    return $typed_data;
  }

}
