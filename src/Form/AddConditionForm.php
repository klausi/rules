<?php

/**
 * @file
 * Contains \Drupal\rules\Form\AddConditionForm.
 */

namespace Drupal\rules\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Condition\ConditionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddConditionForm extends FormBase {

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\rules\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ConditionManager $condition_manager) {
    $this->conditionManager = $condition_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.condition'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $condition_name = $form_state->getValue('condition');

    // Step 1 of the multistep form.
    if (!$condition_name) {
      $condition_definitions = $this->conditionManager->getGroupedDefinitions();
      $options = [];
      foreach ($condition_definitions as $group => $definitions) {
        foreach ($definitions as $id => $definition) {
          $options[$group][$id] = $definition['label'];
        }
      }

      $form['condition'] = [
        '#type' => 'select',
        '#title' => $this->t('Condition'),
        '#options' => $options,
        '#required' => TRUE,
        '#empty_value' => $this->t('- Select -'),
      ];
      $form['continue'] = [
        '#type' => 'submit',
        '#value' => $this->t('Continue'),
      ];

      return $form;
    }

    /** @var \Drupal\rules\Core\RulesConditionInterface $condition */
    $condition = $this->conditionManager->createInstance($condition_name);

    // Step 2 of the form.
    $form['summary'] = [
      '#markup' => $condition->summary(),
    ];

    $context_defintions = $condition->getContextDefinitions();

    foreach ($context_defintions as $context_name => $context_definition) {
      $form['context'][$context_name] = [
        '#type' => 'textfield',
        '#title' => $context_definition->getLabel(),
        '#description' => $context_definition->getDescription(),
        '#required' => $context_definition->isRequired(),
      ];
    }

    $form['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_reaction_condition_add';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

}
