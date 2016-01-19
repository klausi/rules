<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\IntegrityViolation.
 */

namespace Drupal\rules\Engine;

class IntegrityViolation {

  protected $message;

  protected $contextName;

  protected $uuid;

  public function setMessage($message) {
    $this->message = $message;
  }

  public function getMessage() {
    return $this->message;
  }

  public function getContextName() {
    return $this->contextName;
  }

  public function setContextName($context_name) {
    $this->contextName = $context_name;
  }

  public function getUuid() {
    return $this->uuid;
  }

  public function setUuid($uuid) {
    $this->uuid = $uuid;
  }

}
