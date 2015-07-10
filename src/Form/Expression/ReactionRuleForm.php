<?php

/**
 * @file
 * Contains \Drupal\rules\Form\Expression\ReactionRuleForm.
 */

namespace Drupal\rules\Form\Expression;

use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Plugin\RulesExpression\RuleInterface;

class ReactionRuleForm implements ExpressionFormInterface {

  /**
   * The rule expression object this form is for.
   *
   * @var \Drupal\rules\Plugin\RulesExpression\RuleInterface
   */
  protected $rule;

  /**
   * Creates a new object of this class.
   */
  public function __construct(RuleInterface $rule) {
    $this->rule = $rule;
  }
  
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    //$form['event']['#markup'] = 'hello';
    return $form;
  }

}
