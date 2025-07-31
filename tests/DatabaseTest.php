<?php
use PHPUnit\Framework\TestCase;
use App\Database;

class DatabaseTest extends TestCase
{
    public function testConnectionReturnsPDO()
    {
        $db = new Database();
        $this->assertInstanceOf(PDO::class, $db->getConnection());
    }
}
