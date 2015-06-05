<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesEventManager.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;

/**
 * Plugin manager for Rules events that can be triggered.
 *
 * Rules events are primarily defined in *.rules.events.yml files.
 *
 * @see \Drupal\rules\Core\RulesEventInterface
 */
class RulesEventManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->alterInfo('rules_event');
    $this->discovery = new YamlDiscovery('rules.events', $module_handler->getModuleDirectories());
    $this->factory = new ContainerFactory($this, 'Drupal\rules\Core\RulesEventInterface');
    $this->moduleHandler = $module_handler;
  }

}
