<?php
/**
 * @author Kirill "Nemoden" K 
 * $ Date: Mon 26 Mar 2012 01:39:00 PM VLAST $
 */
require_once '../Model.php';
require_once '../exceptions/ResolverException.php';

class Person extends Model {
  protected $_table = 'people';
  protected $_schema = 'test';
  protected $_pk = 'id';

  protected function resolvers() {
    return array('age' => 'resolveAge');
  }

  protected function resolveAge() {
    if (!isset($this->birthday) || (!($this->birthday instanceof DateTime))) {
      throw new ResolverException('Birthday field is not set or not DateTime instance');
    }
    $now = new DateTime();
    $interval = date_diff($now, $this->birthday);
    $this->age =  $interval->y;
  }

}

