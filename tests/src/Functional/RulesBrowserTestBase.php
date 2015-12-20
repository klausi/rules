<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Functional\RulesBrowserTestBase.
 */

namespace Drupal\Tests\rules\Functional;

use Drupal\simpletest\BrowserTestBase;

/**
 * Has some additional helper methods to make test code more readable.
 */
abstract class RulesBrowserTestBase extends BrowserTestBase {

  /**
   * Finds link with specified locator.
   *
   * @param string $locator
   *   link id, title, text or image alt.
   *
   * @return NodeElement|null
   */
  public function findLink($locator) {
    return $this->getSession()->getPage()->findLink($locator);
  }

  /**
   * Finds field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   input id, name or label.
   *
   * @return NodeElement|null
   */
   public function findField($locator) {
     return $this->getSession()->getPage()->findField($locator);
   }

  /**
   * Finds button (input[type=submit|image|button|reset], button) with specified locator.
   *
   * @param string $locator
   *   button id, value or alt.
   *
   * @return NodeElement|null
   */
   public function findButton($locator) {
     return $this->getSession()->getPage()->findButton($locator);
   }

}
