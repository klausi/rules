<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\ContextHandlerTraitTest.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinitionInterface;
use Drupal\rules\Context\ContextHandlerTrait;
use Drupal\rules\Engine\RulesStateInterface;

/**
 * @coversDefaultClass \Drupal\rules\Context\ContextHandlerTrait
 * @group rules
 */
class ContextHandlerTraitTest extends RulesUnitTestBase {

  /**
   * The mocked condition manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * The condition object being tested.
   *
   * @var \Drupal\rules\Plugin\RulesExpression\RulesCondition
   */
  protected $condition;

  /**
   * Tests that a missing required context triggers an exception.
   *
   * @covers ::mapContext
   * @expectedException \Drupal\rules\Exception\RulesEvaluationException
   * @expectedExceptionMessage Required context test is missing for plugin testplugin.
   */
  public function testMissingContext() {
    // Set 'getContextValue' as mocked method.
    $trait = $this->getMockForTrait(ContextHandlerTrait::class, [], '', TRUE, TRUE, TRUE, ['getContextValue']);
    $context_definition = $this->prophesize(ContextDefinitionInterface::class);

    // Let the trait work with an empty configuration.
    $trait->configuration = ContextConfig::create()->toArray();

    // Make the context required in the definition.
    $context_definition->isRequired()->willReturn(TRUE)->shouldBeCalled(1);

    $plugin = $this->prophesize(ContextAwarePluginInterface::class);
    $plugin->getContextDefinitions()
      ->willReturn(['test' => $context_definition->reveal()])
      ->shouldBeCalled(1);
    $plugin->getPluginId()->willReturn('testplugin')->shouldBeCalled(1);

    $state = $this->prophesize(RulesStateInterface::class);

    // Make the 'mapContext' method visible.
    $reflection = new \ReflectionClass($trait);
    $method = $reflection->getMethod('mapContext');
    $method->setAccessible(TRUE);
    $method->invokeArgs($trait, [$plugin->reveal(), $state->reveal()]);
  }

}
