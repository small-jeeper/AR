<?php

require_once 'DbExpression.php';
class DbQueryBuilder {

  private $_schema;
  private $_table;
  private $_bind_params = array();

  public function setSchema($schema) {
    $this->_schema = sprintf('`%s`', (string)$schema);
    return $this;
  }

  public function setTable($table) {
    $this->_table = sprintf('`%s`', (string)$table);
    return $this;
  }

  public function insert($params) {
    $template = 'INSERT INTO %s (%s) VALUES (%s)';
    $dest = implode('.', array($this->_schema, $this->_table));
    $keys = $values = array();
    foreach ($params as $key => $param) {
      $keys[] = sprintf('`%s`', $key);
      if ($param instanceof DbExpression) {
        $values[] = $param;
      }
      else {
        $placeholder = sprintf(':%s', $key);
        $values[] = sprintf(':%s', $key);
        $this->_bind_params[$placeholder] = $param;
      }
    }
    return sprintf($template, $dest, implode(', ', $keys), implode(', ', $values));
  }

  public function update() {
  }

  public function getBindParams() {
    return $this->_bind_params;
  }
}
