<?php
require_once 'db.php';
/**
 * hello, dolly
 */
class User {

  protected $_table = 'test.users';
  protected $_pk = 'id';
  private $_dbh;

  private $_is_new_instance = true;
  private $_a = array(); // attributes
  private $_c = array(); // changed attributes

  protected $attributes = array(
  );

  public function __construct() {
    global $dbh;
    $this->_dbh = $dbh;
  }

  public function registeredDbCriteria() {
    // returns db criteria
    return;
  }

  public function save() {
    if ($this->isNewRecord()) {
      $keys = array_keys($this->_a);
      $values = array_map(create_function('$k', 'return ":".$k;'), array_keys($this->_a));
      $sql = 'INSERT INTO '.$this->_table.' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
      $statement = $this->_dbh->prepare($sql);
      echo $sql;
      $result = $statement->execute($this->_a);
      if ($result) { 
        $this->_a[$this->_pk] = $this->_dbh->lastInsertId();
      }
    }
    else { // update
      $values = array_map(create_function('$k,$v', 'return $k."=:".$v;'), array_keys($this->_a), $this->_a);
      $sql = 'UPDATE '.$this->_table.' SET '.implode(', ', $values).' WHERE '.$this->_pk.' = '.$this->_a[$this->_pk];
      $statement = $this->db->prepare($sql);
      $result = $statement->execute($this->_a);
    }
    return $result;
  }

  public function isNewRecord() {
    return !isset($this->_a[$this->_pk]);
  }

  // get an attribute
  public function __get($name) {
    if (method_exists($this, 'get'.ucfirst($name))) {
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

  public function __set($name, $value) {
    if ($name[0] != '_') {
      $this->_a[$name] = $value;
    }
  }

  public function getName() {
    return 'Name: '.$this->_a['name'];
  }

  public function __call($fn, $args) {
  }

  // data getters 
  public function getAll($conditions = NULL) {
  }

  public function getByPK($pk) {
    $sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->_pk.' = :pk';
    $statement = $this->_dbh->prepare($sql);
    $restul = $statement->execute(array(':pk' => $pk));
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    foreach ($data as $column_name =>  $value) {
      $this->_a[$column_name] = $value;
    }
    return $this;
  }

}

echo str_repeat(PHP_EOL, 5);
$user = new User();
$user = $user->getByPK(1);
echo $user->name;
echo str_repeat(PHP_EOL, 2);

echo str_repeat(PHP_EOL, 2);
echo 'test create user'.PHP_EOL; 
$user = new User();
$user->name = 'Fedor';
$user->email = 'emelayanenko@name.com';
var_export($user->save());
var_export($user->name);
echo str_repeat(PHP_EOL, 5);
