<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesAction\RulesComponentActionDeriver.
 */

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derives Rules component action plugin definitions from config entities.
 *
 * @see RulesComponentAction
 */
class RulesComponentActionDeriver extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

  /**
   * The config entity storage that holds Rules components.
   *
   * @var EntityStorageInterface
   */
  protected $storage;

  /**
   * Contructor.
   */
  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static($container->get('entity_type.manager')->getStorage('rules_component'));
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $rules_components = $this->storage->loadMultiple();
    foreach ($rules_components as $rules_component) {

      $this->derivatives[$rules_component->id()] = [
        'label' => $this->t('@expression_type: @label', [
          '@expression_type' => $rules_component->getExpression()->getLabel(),
          '@label' => $rules_component->label(),
        ]),
        'category' => $this->t('Components'),
        'component_id' => $rules_component->id(),
        'context' => [],
        'provides' => [],
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
