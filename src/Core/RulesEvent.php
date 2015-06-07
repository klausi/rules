<?php

/**
 * @file
 * Contains \Drupal\rules\Core\RulesEvent.
 */

namespace Drupal\rules\Core;

use Drupal\Core\Plugin\ContextAwarePluginBase;

/**
 * Base class for rules actions.
 */
class RulesEvent extends ContextAwarePluginBase implements RulesEventInterface {

  /**
   * {@inheritdoc}
   */
  public function refineContextDefinitions() {
    // Events do not refine context definitions, so do nothing here.
  }

}
