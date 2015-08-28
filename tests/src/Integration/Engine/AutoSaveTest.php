<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Engine\AutoSaveTest.
 */

namespace Drupal\Tests\rules\Integration\Engine;

use Drupal\Core\Entity\EntityInterface;
use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * Test auto saving of variables after Rules execution.
 *
 * @group rules
 */
class AutoSaveTest extends RulesEntityIntegrationTestBase {

  /**
   * Tests auto saving after an action execution.
   */
  public function testActionAutoSave() {
    $rule = $this->rulesExpressionManager->createRule([
      'context_definitions' => [
        'entity' => ContextDefinition::create('entity')->toArray()
      ],
    ]);
    // Just leverage the entity save action, which by default uses auto-saving.
    $rule->addAction('rules_entity_save', ContextConfig::create()
      ->map('entity', 'entity')
    );

    $entity = $this->prophesize(EntityInterface::class);
    $entity->save()->shouldBeCalledTimes(1);
    // Wed don't care about the cache methods, but they will be called so we
    // have to mock them.
    $entity->getCacheContexts()->willReturn([]);
    $entity->getCacheTags()->willReturn([]);
    $entity->getCacheMaxAge()->willReturn(-1);

    $rule->setContextValue('entity', $entity->reveal());
    $rule->execute();
  }

}
