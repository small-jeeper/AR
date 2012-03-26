<?

require_once 'PHPUnit/Autoload.php';
require_once '../DbExpression.php';
require_once '../DbQueryBuilder.php';
require_once '../DbCriteria.php';

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class DbQueryBuilderTest extends PHPUnit_Framework_TestCase {

  private $queryBuilder;

  /**
   * each time create new DbQueryBuilder
   */
  public function setUp() {
    $this->queryBuilder = new DbQueryBuilder();
  }

  public function testCreateInsertQuery() {
    $params = array(
      'name' => 'Kirill',
      'date' => new DbExpression('NOW()'),
      'value' => 10,
    );
    $this->queryBuilder = new DbQueryBuilder();
    $query = $this->queryBuilder->setSchema('test')->setTable('test')->insert($params);
    $this->assertEquals('INSERT INTO `test`.`test` (`name`, `date`, `value`) VALUES (:name, NOW(), :value)', $query); 
    $this->assertEquals(array(':name' => 'Kirill', ':value' => '10'), $this->queryBuilder->getBindParams()); 
  }

  public function testUpdateQuery() {
    $criteria = new DbCriteria();
    $criteria->conditions = array('id = :id');
    $update_fields = array();
    $query = $this->queryBuilder->update($update_fields, $criteria);
    $this->markTestIncomplete();
  }

  public function testSelectQuery() {
    $this->markTestIncomplete();
  }

  public function testJoinQuery() {
    $this->markTestIncomplete();
  }

}
