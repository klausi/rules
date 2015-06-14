<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesExpression\ReactionRule.
 */

namespace Drupal\rules\Plugin\RulesExpression;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Engine\ExpressionBase;
use Drupal\rules\Engine\ActionExpressionContainerInterface;
use Drupal\rules\Engine\ActionExpressionInterface;
use Drupal\rules\Engine\ConditionExpressionContainerInterface;
use Drupal\rules\Engine\ConditionExpressionInterface;
use Drupal\rules\Engine\ExpressionInterface;
use Drupal\rules\Engine\ExpressionPluginManager;
use Drupal\rules\Engine\RulesStateInterface;
use Drupal\rules\Exception\InvalidExpressionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rules\Engine\RulesEventManager;

/**
 * Provides a rule, executing actions when conditions are met.
 *
 * Actions added to a rule can also be rules themselves, so it is possible to
 * nest several rules into one rule. This is the functionality of so called
 * "rule sets" in Drupal 7.
 *
 * @RulesExpression(
 *   id = "rules_reaction_rule",
 *   label = @Translation("A reaction rule triggering on events")
 * )
 */
class ReactionRule extends Rule {

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\rules\Engine\ExpressionPluginManager $expression_manager
   *   The rules expression plugin manager.
   * @param \Drupal\rules\Engine\RulesEventManager $event_manager
   *   The Rules event manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ExpressionPluginManager $expression_manager, RulesEventManager $event_manager) {
    // @todo Reaction rules should also work with multiple events.
    if (isset($configuration['event'])) {
      $event_definition = $event_manager->getDefinition($configuration['event']);
      if (!empty($event_definition['context'])) {
        $plugin_definition['context'] = $event_definition['context'];
      }
    }

    parent::__construct($configuration, $plugin_id, $plugin_definition, $expression_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.rules_expression'),
      $container->get('plugin.manager.rules_event')
    );
  }

}
