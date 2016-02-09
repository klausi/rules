<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Engine\ComponentActionTest.
 */

namespace Drupal\Tests\rules\Integration\Engine;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Engine\RulesComponent;
use Drupal\rules\Entity\RulesComponentConfig;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * Tests for exposing Rules components as action plugins.
 *
 * @group rules
 */
class ComponentActionTest extends RulesEntityIntegrationTestBase {

  /**
   * Tests that a rule can be used as action.
   */
  public function testActionAvailable() {
    $rule = $this->rulesExpressionManager->createRule();

    $rules_config = new RulesComponentConfig([
      'id' => 'test_rule',
      'label' => 'Test rule',
    ], 'rules_component');
    $rules_config->setExpression($rule);

    $this->prophesizeStorage([$rules_config]);

    $definition = $this->actionManager->getDefinition('rules_component:test_rule');
    $this->assertEquals('Components', $definition['category']);
    $this->assertEquals('Rule: Test rule', (string) $definition['label']);
  }

  /**
   * Tests that the execution of the action invokes the Rules component.
   */
  public function testExecute() {
    // Set up a rules component that will just save an entity.
    $nested_rule = $this->rulesExpressionManager->createRule();
    $nested_rule->addAction('rules_entity_save', ContextConfig::create()
      ->map('entity', 'entity')
    );

    $rules_config = new RulesComponentConfig([
      'id' => 'test_rule',
      'label' => 'Test rule',
    ], 'rules_component');
    $rules_config->setExpression($nested_rule);
    $rules_config->setContextDefinitions(['entity' => ContextDefinition::create('entity')]);

    $this->prophesizeStorage([$rules_config]);

    // Invoke the rules component in another rule.
    $rule = $this->rulesExpressionManager->createRule();
    $rule->addAction('rules_component:test_rule', ContextConfig::create()
      ->map('entity', 'entity')
    );

    // The call to save the entity means that the action was executed.
    $entity = $this->prophesizeEntity(EntityInterface::class);
    $entity->save()->shouldBeCalledTimes(1);

    RulesComponent::create($rule)
      ->addContextDefinition('entity', ContextDefinition::create('entity'))
      ->setContextValue('entity', $entity->reveal())
      ->execute();
  }

  /**
   * Prepares a mocked entity storage that returns the provided Rules configs.
   *
   * @param RulesComponentConfig[] $rules_configs
   *   The Rules componentn config entities that should be returned.
   */
  protected function prophesizeStorage($rules_configs) {
    $storage = $this->prophesize(ConfigEntityStorageInterface::class);
    $keyed_configs = [];

    foreach ($rules_configs as $rules_config) {
      $keyed_configs[$rules_config->id()] = $rules_config;
      $storage->load($rules_config->id())->willReturn($rules_config);
    }

    $storage->loadMultiple(NULL)->willReturn($keyed_configs);
    $this->entityTypeManager->getStorage('rules_component')->willReturn($storage->reveal());
  }

}
