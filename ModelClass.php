<?php
/**
 * @author Kirill "Nemoden" K 
 * $ Date: Thu 22 Mar 2012 06:11:30 PM VLAT $
 */
require_once 'Model.php';

class ModelClass extends Model {
  protected $_schema = 'test';
  protected $_table = 'model_test';
  protected $_pk = 'id';
}
