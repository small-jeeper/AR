<?php
/**
 * @author Kirill "Nemoden" K 
 * $ Date: Thu 22 Mar 2012 05:44:34 PM VLAT $
 */

require_once 'PHPUnit/Autoload.php';
require_once '../ModelClass.php';
require_once '../DbExpression.php';
require_once '../DbDateTime.php';
require_once '../DbDate.php';
require_once 'Person.php';


/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class ModelTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {
  }

  public static function tearDownAfterClass() {
  }

  public function setUp() {
  }

  public function tearDown() {
  }

  public function testCreateModel() {

    $person = new Person();
    $person->name = 'Kirill';
    $person->address = 'Nei\'buta str.';
    $person->birthday = new DbExpression('NOW()');
    $person->created_at = new DbDateTime('26 Mar 2012');
    $person->save();

    $this->assertEquals(53, $person->age);
    $person->birthday = new DbDate('30 Dec 1986');
    $this->assertEquals(25, $person->age);
    $save_response = $person->save();
    $this->assertTrue($save_response);
  }

}
