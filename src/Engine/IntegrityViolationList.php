<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\IntegrityViolationList.
 */

namespace Drupal\rules\Engine;

/**
 *
 */
class IntegrityViolationList implements \IteratorAggregate {

  /**
   * @var \Drupal\rules\Engine\IntegrityViolation[]
   */
  protected $violations = array();

  /**
   * Constructor.
   *
   * @param \Drupal\rules\Engine\IntegrityViolation[] $violations
   *   The violations to add to the list
   */
  public function __construct(array $violations = array()) {

    foreach ($violations as $violation) {
      $this->add($violation);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function add(IntegrityViolation $violation) {
    $this->violations[] = $violation;
  }

  /**
   * {@inheritdoc}
   */
  public function addAll(IntegrityViolationList $otherList) {
    foreach ($otherList as $violation) {
      $this->violations[] = $violation;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new \ArrayIterator($this->violations);
  }

}
