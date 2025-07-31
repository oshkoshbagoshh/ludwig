<?php

use PHPUnit\Framework\TestCase;
use App\Core\Database;

/**
 * Database Test
 * 
 * Tests the Database class functionality.
 */
class DatabaseTest extends TestCase
{
    /**
     * Test that the database connection returns a PDO instance
     */
    public function testConnectionReturnsPDO()
    {
        $db = new Database();
        $this->assertInstanceOf(\PDO::class, $db->getConnection());
    }
    
    /**
     * Test that the database can execute a query
     */
    public function testCanExecuteQuery()
    {
        $db = new Database();
        
        // Create a test table
        $db->execute("
            CREATE TABLE IF NOT EXISTS test_table (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // Insert a test record
        $result = $db->execute("
            INSERT INTO test_table (name) VALUES (:name)
        ", [':name' => 'Test Name']);
        
        $this->assertEquals(1, $result);
        
        // Query the test record
        $record = $db->fetch("
            SELECT * FROM test_table WHERE name = :name
        ", [':name' => 'Test Name']);
        
        $this->assertIsArray($record);
        $this->assertEquals('Test Name', $record['name']);
        
        // Clean up
        $db->execute("DROP TABLE IF EXISTS test_table");
    }
    
    /**
     * Test that the database can fetch all records
     */
    public function testCanFetchAllRecords()
    {
        $db = new Database();
        
        // Create a test table
        $db->execute("
            CREATE TABLE IF NOT EXISTS test_table (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // Insert test records
        $db->execute("INSERT INTO test_table (name) VALUES (:name1)", [':name1' => 'Name 1']);
        $db->execute("INSERT INTO test_table (name) VALUES (:name2)", [':name2' => 'Name 2']);
        $db->execute("INSERT INTO test_table (name) VALUES (:name3)", [':name3' => 'Name 3']);
        
        // Fetch all records
        $records = $db->fetchAll("SELECT * FROM test_table ORDER BY id");
        
        $this->assertIsArray($records);
        $this->assertCount(3, $records);
        $this->assertEquals('Name 1', $records[0]['name']);
        $this->assertEquals('Name 2', $records[1]['name']);
        $this->assertEquals('Name 3', $records[2]['name']);
        
        // Clean up
        $db->execute("DROP TABLE IF EXISTS test_table");
    }
    
    /**
     * Test that the database can insert a record and return the ID
     */
    public function testCanInsertAndReturnId()
    {
        $db = new Database();
        
        // Create a test table
        $db->execute("
            CREATE TABLE IF NOT EXISTS test_table (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // Insert a test record and get the ID
        $id = $db->insert("
            INSERT INTO test_table (name) VALUES (:name)
        ", [':name' => 'Test Name']);
        
        $this->assertNotEmpty($id);
        
        // Query the test record by ID
        $record = $db->fetch("
            SELECT * FROM test_table WHERE id = :id
        ", [':id' => $id]);
        
        $this->assertIsArray($record);
        $this->assertEquals('Test Name', $record['name']);
        
        // Clean up
        $db->execute("DROP TABLE IF EXISTS test_table");
    }
    
    /**
     * Test that the database can handle transactions
     */
    public function testCanHandleTransactions()
    {
        $db = new Database();
        
        // Create a test table
        $db->execute("
            CREATE TABLE IF NOT EXISTS test_table (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // Start a transaction
        $db->beginTransaction();
        
        // Insert test records
        $db->execute("INSERT INTO test_table (name) VALUES (:name1)", [':name1' => 'Name 1']);
        $db->execute("INSERT INTO test_table (name) VALUES (:name2)", [':name2' => 'Name 2']);
        
        // Commit the transaction
        $db->commit();
        
        // Verify the records were inserted
        $records = $db->fetchAll("SELECT * FROM test_table ORDER BY id");
        $this->assertCount(2, $records);
        
        // Start another transaction
        $db->beginTransaction();
        
        // Insert another record
        $db->execute("INSERT INTO test_table (name) VALUES (:name3)", [':name3' => 'Name 3']);
        
        // Roll back the transaction
        $db->rollBack();
        
        // Verify the record was not inserted
        $records = $db->fetchAll("SELECT * FROM test_table ORDER BY id");
        $this->assertCount(2, $records);
        
        // Clean up
        $db->execute("DROP TABLE IF EXISTS test_table");
    }
}
