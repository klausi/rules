<?php

/**
 * @file
 * Rules controller.
 */

namespace Drupal\rules\Controller;

use Drupal\rules\Ui\RulesUiHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles autocompletion of data selectors.
 */
class AutocompleteController {

  /**
   * Returns a JSON list of autocomplete suggestions for data selectors.
   *
   * @param RulesUiHandlerInterface $rules_ui_handler
   *   The UI handler.
   * @param string $uuid
   *   The UUID of the expression in which the autocomplete is triggered.
   * @param Request $request
   *   The request object providing the autocomplete query parameter.
   *
   * @return JsonResponse
   *   The JSON results.
   *
   * @throws NotFoundHttpException
   *   If the expression with the given UUID could not be found.
   */
  public function autocomplete(RulesUiHandlerInterface $rules_ui_handler, $uuid, Request $request) {
    $component = $rules_ui_handler->getComponent();
    $nested_expression = $component->getExpression()->getExpression($uuid);
    if ($nested_expression === FALSE) {
      throw new NotFoundHttpException();
    }

    $string = $request->query->get('q');
    $results = $component->autocomplete($string, $nested_expression);

    // @todo the API should return the formatted results.
    $results = array_map(function ($value) {
      return ['value' => $value, 'label' => $value];
    }, $results);

    return new JsonResponse($results);
  }

}
