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
require_once 'DbExpression.php';
require_once 'DbQueryBuilder.php';

abstract class Model {

  private $_dbh;
  private $_error_info;
  private $_queryBuilder; // DbQueryBuilder instance

  private $_is_new_instance = true;
  private $_a = array(); // attributes
  private $_c = array(); // changed attributes
  /**
   * @TODO primary key may be composite, e.g. array('id1', 'id2')
   * @TODO We should be able to do something like 
   * @TODO Model::getByPK(array('id1' => 10, 'id2' => 20)) or just array(10,20)
   */
  protected $_pk = 'id';
  protected $_schema = 'id';
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
    $this->_queryBuilder = new DbQueryBuilder();
    $this->_queryBuilder->setTable($this->_table)->setSchema($this->_schema);

    // we need table 
    if (empty($this->_table)) {
      throw new ModelException('Model is not associated with any database table');
    }
    if (empty($this->_pk)) {
      throw new ModelException('Missing table primary key');
    }
  }

  protected function resolvers() {
    return array();
  }

  protected function applyResolvers() {
    $resolvers = $this->resolvers();
    foreach ($resolvers as $resolver) {
      $this->$resolver();
    }
  }

  public function save() {

    $this->applyResolvers();


    if ($this->isNewRecord()) {

      $query = $this->_queryBuilder->insert($this->_a);
      $statement = $this->_dbh->prepare($query);
      $result = $statement->execute($this->_queryBuilder->getBindParams());
      echo $query;
      var_export($this->_queryBuilder->getBindparams());

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
    if (!$result) {
      $this->_error_info = $this->_dbh->errorInfo();
    }
    return $result;
  }

  public function getErrorInfo() {
    return $this->_error_info;
  }

  public function isNewRecord() {
    return !isset($this->_a[$this->_pk]);
  }

  // get an attribute
  public function __get($name) {
    $resolvers = $this->resolvers();
    if (isset($resolvers[$name])) {
      $resolver = $resolvers[$name];
      $this->$resolver();
    }
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

  public function __isset($name) {
    return isset($this->_a[$name]);
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
