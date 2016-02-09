<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesAction\RulesComponentAction.
 */

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\rules\Engine\RulesComponent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a generic 'Execute Rules component' action.
 *
 * @RulesAction(
 *   id = "rules_component",
 *   deriver = "Drupal\rules\Plugin\RulesAction\RulesComponentActionDeriver"
 * )
 *
 * @todo: Add access callback information from Drupal 7.
 */
class RulesComponentAction extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The storage of rules components.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The ID of the rules component config entity.
   *
   * @var string
   */
  protected $componentId;

  /**
   * Constructs an EntityCreate object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityStorageInterface $storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storage = $storage;
    $this->componentId = $plugin_definition['component_id'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('rules_component')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $rules_component = $this->storage->load($this->componentId);
    $expression = $rules_component->getExpression();

    // Setup an isolated execution state for this expression and pass on the
    // necessary context.
    $rules_component = RulesComponent::create($expression);
    foreach ($this->getContextDefinitions() as $context_name => $context_definition) {
      $rules_component->addContextDefinition($context_name, $context_definition);
      $rules_component->setContextValue($context_name, $this->getContextValue($context_name));
    }
    $rules_component->execute();
  }

}
