<?php
require_once 'db.php';
/**
 * hello, dolly
 */
class User() {

  protected $table = 'test.users';

  private $_is_new_instance = true;
  private $_a = array(); // attributes
  private $_c = array(); // changed attributes

  protected $safe_attributes = array(
);

  public function __construct() {
    global $dbh;
    $this->dbh = $dbh;
  }

  public function registeredDbCriteria() {
    // returns db criteria
    return;
  }

  public function save() {
    if ($this->isNewRecord()) {
      $keys = implode(',', array_keys($this->_a));
      $values = array_map(create_function('$k', 'return ":".$k;'), $this->_a);
      $sql = 'INSERT INTO '.$this->table.' ('.$keys.') VALUES ('.$values.')';
      $statement = $this->dbh->prepare($sql)->execute($this->_a);
      $statement->execute($this->_a);
      $this->_a['id'] = $this->dbh->lastInsertId();
    }
    else { // update
      $values = array_map(create_function('$k,$v', 'return $k."=:".$v;'), array_keys($this->_a), $this->_a);
    }
  }


  public function validate() {

  }

  public function isNewRecord() {
    return isset($this->_a['id']);
  }

  // get an attribute
  public function __get($name) {
    if (method_exists($this, 'get'.ucfirst($name)) {
      $method = 'get'.ucfirst($name);
      return $this->$method();
    }
    if (isset($this->_a[$name])) {
      return $this->_a[$name];
    }
    else {
      return NULL;
    }
  }

  public function __call($fn, $args) {
  }

  // data getters 
  public function getAll($conditions = NULL) {
  }

}
