<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesComponent.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\rules\Context\ContextDefinitionInterface;
use Drupal\rules\Entity\ReactionRuleConfig;

/**
 * Handles executable Rules components.
 */
class RulesComponent {

  /**
   * The rules execution state.
   *
   * @var \Drupal\rules\Engine\ExecutionStateInterface
   */
  protected $state;

  /**
   * Definitions for the context used by the component.
   *
   * @var \Drupal\rules\Context\ContextDefinitionInterface[]
   */
  protected $contextDefinitions = [];

  /**
   * List of context names that is provided back to the caller.
   *
   * @var string[]
   */
  protected $providedContext = [];

  /**
   * The expression.
   *
   * @var \Drupal\rules\Engine\ExpressionInterface
   */
  protected $expression;

  /**
   * Constructs the object.
   *
   * @param \Drupal\rules\Engine\ExpressionInterface $expression
   *   The expression of the component.
   *
   * @return static
   */
  public static function create(ExpressionInterface $expression) {
    return new static($expression);
  }

  /**
   * Constructs the object.
   *
   * @param \Drupal\rules\Engine\ExpressionInterface $expression
   *   The expression of the component.
   */
  protected function __construct(ExpressionInterface $expression) {
    $this->state = ExecutionState::create();
    $this->expression = $expression;
  }

  /**
   * Gets the expression of the component.
   *
   * @return \Drupal\rules\Engine\ExpressionInterface
   *   The expression.
   */
  public function getExpression() {
    return $this->expression;
  }

  /**
   * Gets the execution state.
   *
   * @return \Drupal\rules\Engine\ExecutionStateInterface
   *   The execution state for this component.
   */
  public function getState() {
    return $this->state;
  }

  /**
   * Adds a context definition.
   *
   * @param string $name
   *   The name of the context to add.
   * @param \Drupal\rules\Context\ContextDefinitionInterface $definition
   *   The definition to add.
   *
   * @return $this
   */
  public function addContextDefinition($name, ContextDefinitionInterface $definition) {
    $this->contextDefinitions[$name] = $definition;
    return $this;
  }

  /**
   * Gets definitions for the context used by this component.
   *
   * @return \Drupal\rules\Context\ContextDefinitionInterface[]
   *   The array of context definitions, keyed by context name.
   */
  public function getContextDefinitions() {
    return $this->contextDefinitions;
  }

  /**
   * Adds the configured context definitions from the config entity.
   *
   * Example: for a reaction rule config all context definitions of the event
   * will be added.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface $rules_config
   *   The config entity.
   *
   * @return $this
   */
  public function addContextDefinitionsFrom(ConfigEntityInterface $rules_config) {
    if ($rules_config instanceof ReactionRuleConfig) {
      $event_name = $rules_config->getEvent();
      // @todo Use setter injection for the service.
      $event_definition = \Drupal::service('plugin.manager.rules_event')->getDefinition($event_name);
      foreach ($event_definition['context'] as $context_name => $context_definition) {
        $this->addContextDefinition($context_name, $context_definition);
      }
    }
    return $this;
  }

  /**
   * Marks the given context to be provided back to the caller.
   *
   * @param string $name
   *   The name of the context to provide.
   *
   * @return $this
   */
  public function provideContext($name) {
    $this->providedContext[] = $name;
    return $this;
  }

  /**
   * Returns the names of context that is provided back to the caller.
   *
   * @return string[]
   *   The names of the context that is provided back.
   */
  public function getProvidedContext() {
    return $this->providedContext;
  }

  /**
   * Sets the value of a context.
   *
   * @param string $name
   *   The name.
   * @param mixed $value
   *   The context value.
   *
   * @throws \LogicException
   *   Thrown if the passed context is not defined.
   *
   * @return $this
   */
  public function setContextValue($name, $value) {
    if (!isset($this->contextDefinitions[$name])) {
      throw new \LogicException("The specified context '$name' is not defined.");
    }
    $this->state->setVariable($name, $this->contextDefinitions[$name], $value);
    return $this;
  }

  /**
   * Executes the component with the previously set context.
   *
   * @return mixed[]
   *   The array of provided context values, keyed by context name.
   *
   * @throws \Drupal\rules\Exception\RulesEvaluationException
   *   Thrown if the Rules expression triggers errors during execution.
   */
  public function execute() {
    $this->expression->executeWithState($this->state);
    $this->state->autoSave();
    $result = [];
    foreach ($this->providedContext as $name) {
      $result[$name] = $this->state->getVariableValue($name);
    }
    return $result;
  }

  /**
   * Executes the component with the given values.
   *
   * @param mixed[] $arguments
   *   The array of arguments; i.e., an array keyed by name of the defined
   *   context and the context value as argument.
   *
   * @return mixed[]
   *   The array of provided context values, keyed by context name.
   *
   * @throws \LogicException
   *   Thrown if the context is not defined.
   * @throws \Drupal\rules\Exception\RulesEvaluationException
   *   Thrown if the Rules expression triggers errors during execution.
   */
  public function executeWithArguments(array $arguments) {
    $this->state = ExecutionState::create();
    foreach ($arguments as $name => $value) {
      $this->setContextValue($name, $value);
    }
    return $this->execute();
  }

  /**
   * Verifies that the given expression is valid with the defined context.
   *
   * @return \Drupal\rules\Engine\IntegrityViolationList
   *   A list object containing \Drupal\rules\Engine\IntegrityViolation objects.
   */
  public function checkIntegrity() {
    $data_definitions = [];
    foreach ($this->contextDefinitions as $name => $context_definition) {
      $data_definitions[$name] = $context_definition->getDataDefinition();
    }

    $metadata_state = ExecutionMetadataState::create($data_definitions);
    return $this->expression->checkIntegrity($metadata_state);
  }

  /**
   * Returns autocomplete results for the given partial selector.
   *
   * Example: "node.uid.e" will return ["node.uid.entity"].
   *
   * @param string $partial_selector
   *   The partial data selector.
   * @param type $expression_uuid
   *   The UUID of the expression in which the autocompletion will be executed.
   *   All variables in the exection metadata state up to that point are
   *   available.
   *
   * @return string[]
   *   An array of autocomplete suggestions.
   */
  public function autocomplete($partial_selector, $expression_uuid) {
    $data_definitions = [];
    foreach ($this->contextDefinitions as $name => $context_definition) {
      $data_definitions[$name] = $context_definition->getDataDefinition();
    }

    // We use the integrity check to populate the execution metadata state with
    // available variables.
    $metadata_state = ExecutionMetadataState::create($data_definitions);
    $this->expression->checkIntegrity($metadata_state, $expression_uuid);

    return $metadata_state->autocomplete($partial_selector);
  }

}
