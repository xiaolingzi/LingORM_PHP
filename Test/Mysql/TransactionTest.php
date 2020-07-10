<?php
namespace LingORM\Test\Mysql;

use LingORM\Test\Entity\FirstTableEntity;
use PHPUnit\Framework\TestCase;

require_once "Base.php";

class TransactionTest extends TestCase
{
    public function testCommit()
    {
        $db = (new Base())->db();
        $db->begin();

        $entity = new FirstTableEntity();
        $entity->firstName = "my name";
        $entity->firstNumber = 1001;
        $entity->firstTime = "2016-09-01";

        $id = $db->insert($entity);
        $this->assertGreaterThan(0, $id);

        $db->commit();

        $db2 = (new Base())->db();
        $table = $db2->createTable(new FirstTableEntity());
        $entity = $db2->table($table)->where($table->id->eq($id))->first();
        $this->assertNotNull($entity);

        $entity->id = $id;
        $affectedRows = $db2->delete($entity);
        $this->assertEquals(1, $affectedRows);
    }

    public function testRollback()
    {
        $db = (new Base())->db();
        $db->begin();

        $entity = new FirstTableEntity();
        $entity->firstName = "my name";
        $entity->firstNumber = 1001;
        $entity->firstTime = "2016-09-01";

        $id = $db->insert($entity);
        $this->assertGreaterThan(0, $id);

        $db->rollback();
        $table = $db->createTable(new FirstTableEntity());
        $entity = $db->table($table)->where($table->id->eq($id))->first();
        $this->assertNull($entity);
    }
}