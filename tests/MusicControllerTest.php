<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\MusicController;
use App\Core\Database;

/**
 * Music Controller Test
 * 
 * Tests the MusicController class functionality.
 */
class MusicControllerTest extends TestCase
{
    /**
     * @var \PDO The PDO instance for testing
     */
    private $pdo;
    
    /**
     * @var MusicController The controller instance
     */
    private $controller;
    
    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an in-memory SQLite database for testing
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Create test tables
        $this->createTestTables();
        
        // Insert test data
        $this->insertTestData();
        
        // Create the controller with the test database
        $this->controller = new MusicController($this->pdo);
    }
    
    /**
     * Create test tables
     */
    private function createTestTables(): void
    {
        // Create artists table
        $this->pdo->exec("
            CREATE TABLE artists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                bio TEXT,
                avatar TEXT,
                artist_of_week INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create albums table
        $this->pdo->exec("
            CREATE TABLE albums (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                artist_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                cover TEXT,
                release_date TEXT,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (artist_id) REFERENCES artists(id)
            )
        ");
        
        // Create tracks table
        $this->pdo->exec("
            CREATE TABLE tracks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                artist_id INTEGER NOT NULL,
                album_id INTEGER,
                title TEXT NOT NULL,
                artwork TEXT,
                file_path TEXT,
                bpm INTEGER,
                key_signature TEXT,
                featured INTEGER NOT NULL DEFAULT 0,
                preview_duration INTEGER NOT NULL DEFAULT 30,
                duration INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (artist_id) REFERENCES artists(id),
                FOREIGN KEY (album_id) REFERENCES albums(id)
            )
        ");
        
        // Create genres table
        $this->pdo->exec("
            CREATE TABLE genres (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create track_genres table
        $this->pdo->exec("
            CREATE TABLE track_genres (
                track_id INTEGER NOT NULL,
                genre_id INTEGER NOT NULL,
                PRIMARY KEY (track_id, genre_id),
                FOREIGN KEY (track_id) REFERENCES tracks(id),
                FOREIGN KEY (genre_id) REFERENCES genres(id)
            )
        ");
        
        // Create moods table
        $this->pdo->exec("
            CREATE TABLE moods (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create track_moods table
        $this->pdo->exec("
            CREATE TABLE track_moods (
                track_id INTEGER NOT NULL,
                mood_id INTEGER NOT NULL,
                PRIMARY KEY (track_id, mood_id),
                FOREIGN KEY (track_id) REFERENCES tracks(id),
                FOREIGN KEY (mood_id) REFERENCES moods(id)
            )
        ");
    }
    
    /**
     * Insert test data
     */
    private function insertTestData(): void
    {
        // Insert test artists
        $this->pdo->exec("
            INSERT INTO artists (id, name, bio, artist_of_week) VALUES
            (1, 'Test Artist 1', 'Bio for artist 1', 0),
            (2, 'Test Artist 2', 'Bio for artist 2', 1)
        ");
        
        // Insert test albums
        $this->pdo->exec("
            INSERT INTO albums (id, artist_id, title, release_date) VALUES
            (1, 1, 'Test Album 1', '2023-01-01'),
            (2, 2, 'Test Album 2', '2023-02-01')
        ");
        
        // Insert test tracks
        $this->pdo->exec("
            INSERT INTO tracks (id, artist_id, album_id, title, featured, duration) VALUES
            (1, 1, 1, 'Test Track 1', 0, 180),
            (2, 1, 1, 'Test Track 2', 1, 240),
            (3, 2, 2, 'Test Track 3', 1, 300)
        ");
        
        // Insert test genres
        $this->pdo->exec("
            INSERT INTO genres (id, name) VALUES
            (1, 'Rock'),
            (2, 'Pop'),
            (3, 'Jazz')
        ");
        
        // Insert test track_genres
        $this->pdo->exec("
            INSERT INTO track_genres (track_id, genre_id) VALUES
            (1, 1),
            (1, 2),
            (2, 2),
            (3, 3)
        ");
        
        // Insert test moods
        $this->pdo->exec("
            INSERT INTO moods (id, name) VALUES
            (1, 'Happy'),
            (2, 'Sad'),
            (3, 'Energetic')
        ");
        
        // Insert test track_moods
        $this->pdo->exec("
            INSERT INTO track_moods (track_id, mood_id) VALUES
            (1, 1),
            (2, 3),
            (3, 2)
        ");
    }
    
    /**
     * Test that listSongs returns an array
     */
    public function testListSongsReturnsArray()
    {
        $result = $this->controller->listSongs();
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }
    
    /**
     * Test that listSongs with filters returns filtered results
     */
    public function testListSongsWithFilters()
    {
        // Test filtering by artist_id
        $result = $this->controller->listSongs(['artist_id' => 1]);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Test Track 1', $result[0]['title']);
        $this->assertEquals('Test Track 2', $result[1]['title']);
        
        // Test filtering by album_id
        $result = $this->controller->listSongs(['album_id' => 2]);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Test Track 3', $result[0]['title']);
        
        // Test filtering by genre_id
        $result = $this->controller->listSongs(['genre_id' => 2]);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        // Test filtering by featured
        $result = $this->controller->listSongs(['featured' => 1]);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
    
    /**
     * Test that getSong returns the correct song
     */
    public function testGetSong()
    {
        $result = $this->controller->getSong(1);
        $this->assertIsArray($result);
        $this->assertEquals('Test Track 1', $result['title']);
        $this->assertEquals(1, $result['artist_id']);
        $this->assertEquals(1, $result['album_id']);
        
        // Test that genres are included
        $this->assertArrayHasKey('genres', $result);
        $this->assertIsArray($result['genres']);
        $this->assertCount(2, $result['genres']);
        
        // Test that moods are included
        $this->assertArrayHasKey('moods', $result);
        $this->assertIsArray($result['moods']);
        $this->assertCount(1, $result['moods']);
    }
    
    /**
     * Test that getFeaturedTracks returns featured tracks
     */
    public function testGetFeaturedTracks()
    {
        $result = $this->controller->getFeaturedTracks();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        // Test with limit
        $result = $this->controller->getFeaturedTracks(1);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }
    
    /**
     * Test that getPreviewDuration returns the correct duration
     */
    public function testGetPreviewDuration()
    {
        // Test for unauthenticated user
        $result = $this->controller->getPreviewDuration(false);
        $this->assertEquals(10, $result);
        
        // Test for authenticated user
        $result = $this->controller->getPreviewDuration(true);
        $this->assertEquals(0, $result);
    }
}
