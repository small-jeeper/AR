<?php
/**
 * @author Kirill "Nemoden" K 
 * $ Date: Thu 22 Mar 2012 05:44:34 PM VLAT $
 */

require_once 'PHPUnit/Autoload.php';
require_once '../ModelClass.php';
require_once '../DbExpression.php';
require_once '../DbDateTime.php';


/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class ModelTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {
    global $dbh;
    $create_database = <<<SQL
create database if not exists `test` default character set = utf8 default collate = utf8_general_ci;
SQL;

    $create_table = <<<SQL
create table if not exists `test`.`model_test` (
  `id` int(11) unsigned not null auto_increment,
  `string` varchar(255) not null default '',
  `date` datetime not null default '0000-00-00 00:00:00',
  `timestamp` timestamp,
  `decimal` decimal(10,2) not null default 0.0,
  `float` float not null default 0,
  `int` int(11) unsigned not null default '0',
  PRIMARY KEY `id` (`id`)
) ENGINE=InnoDB COMMENT='testing active record';
SQL;
    try {
      $dbh->beginTransaction();
      $res1 = $dbh->exec($create_database);
      $res2 = $dbh->exec(str_replace(PHP_EOL, '', $create_table));
      if ($dbh->errorCode()) {
        list($sql_error, $driver_error, $error_message) = $dbh->errorInfo();
        echo $error_message;
      }
      $dbh->commit();
    }
    catch (Exception $e) {
      echo $e->getMessage();
    }
    catch (PDOException $e) {
      echo $e->getMessage();
    }
  }

  public static function tearDownAfterClass() {
    global $dbh;
#    $res = $dbh->exec('drop table `model_test`');
  }

  public function setUp() {
  }

  public function tearDown() {
  }

  public function testCreateModel() {
    $model= new ModelClass();
    $model->string = 'lorem ipsum dolor'; 
    $model->int = 10; 
    $model->date = new DbExpression('NOW() + INTERVAL 10 HOUR'); 
    $model->timestamp = new DbExpression('NOW()'); 
    $model->decimal = 10.03; 
    $model->float = 10/3; 
    $save_response = $model->save();
    $this->assertTrue($save_response);
    $this->assertEquals(10, $model->int);
  }

  public function testCreateModel2() {
    $model= new ModelClass();
    $model->string = "The name of the song was: \"Qui pense a l'amour, a l'amour\""; 
    $model->int = 100000; 
    $model->date = new DbDateTime('NOW - 100 YEAR + 2 HOUR');
    $model->timestamp = new DbExpression('NOW()'); 
    $model->decimal = 10.03; 
    $model->float = 10/3; 
    $save_response = $model->save();
    $this->assertTrue($save_response);
    $this->assertEquals(100000, $model->int);
  }
}
