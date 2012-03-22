<?php
/**
 * @author Kirill "Nemoden" K 
 * $ Date: Thu 22 Mar 2012 05:30:34 PM VLAT $
 */

/**
 * hello, dolly
 */

require_once 'db.php';
require_once 'ModelException.php';

abstract class Model {

  private $_dbh;

  private $_is_new_instance = true;
  private $_a = array(); // attributes
  private $_c = array(); // changed attributes
  /**
   * @TODO primary key may be composite, e.g. array('id1', 'id2')
   * @TODO We should be able to do something like 
   * @TODO Model::getByPK(array('id1' => 10, 'id2' => 20)) or just array(10,20)
   */
  protected $_pk = 'id';
  protected $_table = 'id';

  /**
   * temporary solution for date fields
   * we dont bind such fields in prepare
   * @TODO create DbDateTime/DbDate classes to manipulate dates, e.g. new DbDateTime('now')
   */
  protected $datefields = array(
    'date' => 'datetime',
  );

  public function __construct() {
    global $dbh;
    $this->_dbh = $dbh;

    // we need table 
    if (empty($this->_table)) {
      throw new ModelException('Model is not associated with any database table');
    }
    if (empty($this->_pk)) {
      throw new ModelException('Missing table primary key');
    }
  }

  private function formatValues($v) {
    if (!isset($this->datefields[$v])) {
      return ':'.$v; // bind
    }
    else {
      return $v; // as is e.g. NOW()
    }
  }
  private function formatKeys($k) {
    return '`'.$k.'`';
  }
  public function save() {
    if ($this->isNewRecord()) {
      $keys = array_keys($this->_a);
      $values = array_map(create_function('$k', 'return ":".$k;'), array_keys($this->_a));
      $keys = implode(', ', array_map(array($this, 'formatKeys'), $keys));
      $values = implode(', ', array_map(array($this, 'formatValues'), array_keys($this->_a)));
      $sql = 'INSERT INTO '.$this->_table.' ('.$keys.') VALUES ('.$values.')';
      $statement = $this->_dbh->prepare($sql);
      $result = $statement->execute($this->_a);
      echo 'params: ';
      echo $statement->debugDumpParams();
      echo 'error info: ';
      echo var_export($statement->errorInfo(),1);
      echo '<<<<<<<<';
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
