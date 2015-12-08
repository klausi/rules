<?php

/**
 * @file
 * Contains \Drupal\rules\Form\DeleteElementForm.
 */

namespace Drupal\rules\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Entity\ReactionRule;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Removes an element from a rule.
 */
class DeleteElementForm extends ConfirmFormBase {

  /**
   * The reaction rule the element is deleted from.
   *
   * @var \Drupal\rules\Entity\ReactionRule
   */
  protected $rule;

  /**
   * The index of the element in the rule.
   *
   * @var int
   */
  protected $index;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_delete_element';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ReactionRule $rules_reaction_rule = NULL, $index = NULL) {
    $this->rule = $rules_reaction_rule;
    $this->index = $index;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $expression = $this->rule->getExpression();
    $conditions = $expression->getConditions()->getIterator();
    if (!isset($conditions[$this->index])) {
      $actions = $expression->getActions()->getIterator();
      if (!isset($actions[$this->index - count($conditions)])) {
        throw new NotFoundHttpException();
      }
      $element = $actions[$this->index - count($conditions)];
    }
    else {
      $element = $conditions[$this->index];
    }
    return $this->t('Are you sure you want to delete %title from %rule?', [
      '%title' => $element->getLabel(),
      '%rule' => $this->rule->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->rule->urlInfo();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $expression = $this->rule->getExpression();
    $expression->deleteExpressionAt($this->index);
    // Set the expression again so that the config is copied over to the
    // config entity.
    $this->rule->setExpression($expression);
    $this->rule->save();

    drupal_set_message($this->t('Your changes have been saved.'));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
