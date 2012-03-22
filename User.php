<?php
/**
 * @author Kirill "Nemoden" K 
 * $ Date: Thu 22 Mar 2012 05:33:29 PM VLAT $
 */

require_once 'Model.php';
class User extends Model {

  protected $_table = 'test.users';

  public function registeredDbCriteria() {
    // returns db criteria
    return;
  }

  public function getName() {
    return 'Test name getter: '.$this->_a['name'];
  }

}
