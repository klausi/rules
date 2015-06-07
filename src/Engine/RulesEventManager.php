<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesEventManager.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\Core\Plugin\Factory\ContainerFactory;

/**
 * Plugin manager for Rules events that can be triggered.
 *
 * Rules events are primarily defined in *.rules.events.yml files.
 *
 * @see \Drupal\rules\Core\RulesEventInterface
 */
class RulesEventManager extends DefaultPluginManager {

  /**
   * Provides some default values for the definition of all Rules event plugins.
   *
   * @var array
   */
  protected $defaults = [
    'class' => '\Drupal\rules\Core\RulesEvent',
  ];

  /**
   * {@inheritdoc}
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->alterInfo('rules_event');
    $this->discovery = new YamlDiscovery('rules.events', $module_handler->getModuleDirectories());
    $this->factory = new ContainerFactory($this, 'Drupal\rules\Core\RulesEventInterface');
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);
    if (!isset($definition['context'])) {
      return;
    }
    // Convert the flat context arrays into ContextDefinition objects.
    // @todo This code should be removed and we should pass this off to some
    //   annotation reader code that converts plugin defintion parts into
    //   objects.
    foreach ($definition['context'] as $context_name => $values) {
      // We want to call the type key "type", not "value".
      if (!isset($values['value'])) {
        $values['value'] = $values['type'];
      }
      $context_annoation = new \Drupal\Core\Annotation\ContextDefinition($values);
      $definition['context'][$context_name] = $context_annoation->get();
    }
  }

}
