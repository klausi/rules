<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Engine\ComponentActionTest.
 */

namespace Drupal\Tests\rules\Integration\Engine;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
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

    $storage = $this->prophesize(ConfigEntityStorageInterface::class);
    $storage->loadMultiple(NULL)->willReturn([$rules_config->id() => $rules_config]);
    $this->entityTypeManager->getStorage('rules_component')->willReturn($storage->reveal());

    $definition = $this->actionManager->getDefinition('rules_component:test_rule');
    $this->assertEquals('Components', $definition['category']);
    $this->assertEquals('Rule: Test rule', (string) $definition['label']);
  }

}
