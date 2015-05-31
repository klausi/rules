<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\ExpressionBase
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Plugin\ContextAwarePluginBase;
use Drupal\rules\Context\ContextProviderTrait;

/**
 * Base class for rules actions.
 */
abstract class ExpressionBase extends ContextAwarePluginBase implements ExpressionInterface {

  use ContextProviderTrait;

  /**
   * The plugin configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * Executes a rules expression.
   */
  public function execute() {
    $contexts = $this->getContexts();
    $state = new RulesState($contexts);
    $result = $this->executeWithState($state);
    // Save specifically registered variables in the end after execution.
    $state->autoSave();
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function refineContextDefinitions() {
    // Do not refine anything by default.
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return [
      'id' => $this->getPluginId(),
    ] + $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

}
