<?php

/**
 * @file
 * Rules controller.
 */

namespace Drupal\rules\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Handles autocompletion of data selectors.
 */
class AutocompleteController extends ControllerBase {

  public function autocomplete() {
    $result = [['value' => 'test', 'label' => 'Test']];
    return new JsonResponse($result);
  }

}
