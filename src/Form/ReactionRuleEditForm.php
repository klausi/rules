<?php

/**
 * @file
 * Contains \Drupal\rules\Form\ReactionRuleEditForm.
 */

namespace Drupal\rules\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Engine\RulesEventManager;
use Drupal\user\SharedTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to edit a reaction rule.
 */
class ReactionRuleEditForm extends RulesComponentFormBase {

  use TempStoreTrait;

  /**
   * The event plugin manager.
   *
   * @var \Drupal\rules\Engine\RulesEventManager
   */
  protected $eventManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new object of this class.
   */
  public function __construct(RulesEventManager $event_manager, DateFormatterInterface $date_formatter) {
    $this->eventManager = $event_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.rules_event'), $container->get('date.formatter'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    if ($this->isLocked()) {
      $lock = $this->getLockMetaData();
      $username = array(
        '#theme' => 'username',
        '#account' => $this->entityManager->getStorage('user')->load($lock->owner),
      );
      $lock_message_substitutions = array(
        '@user' => drupal_render($username),
        '@age' => $this->dateFormatter->formatTimeDiffSince($lock->updated),
        //':url' => $view->url('break-lock-form'),
        ':url' => 'example',
      );
      $form['locked'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('rules-locked', 'messages', 'messages--warning')),
        '#children' => $this->t('This rule is being edited by user @user, and is therefore locked from editing by others. This lock is @age old. Click here to <a href=":url">break this lock</a>.', $lock_message_substitutions),
        '#weight' => -10,
      );
    }
    else {
      $form['changed'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('rules-changed', 'messages', 'messages--warning')),
        '#children' => $this->t('You have unsaved changes.'),
        '#weight' => -10,
      );
      if (!$this->isEdited()) {
        $form['changed']['#attributes']['class'][] = 'js-hide';
      }
    }

    $event_name = $this->entity->getEvent();
    $event_definition = $this->eventManager->getDefinition($event_name);
    $form['event']['#markup'] = $this->t('Event: @label (@name)', [
      '@label' => $event_definition['label'],
      '@name' => $event_name,
    ]);
    $form_handler = $this->entity->getExpression()->getFormHandler();
    $form = $form_handler->form($form, $form_state);
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    // Also remove the temporarily stored rule, it has been persisted now.
    $this->deleteFromTempStore();

    drupal_set_message($this->t('Reaction rule %label has been updated.', ['%label' => $this->entity->label()]));
  }

  /**
   * Title callback: also display the rule label.
   */
  public function getTitle($rules_reaction_rule) {
    return $this->t('Edit reaction rule "@label"', ['@label' => $rules_reaction_rule->label()]);
  }

  protected function getRuleConfig() {
    return $this->entity;
  }

}
