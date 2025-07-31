<?php

use PHPUnit\Framework\TestCase;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\User;
use App\Core\Database;

/**
 * Playlist Model Test
 * 
 * Tests the Playlist model functionality.
 */
class PlaylistModelTest extends TestCase
{
    /**
     * @var \PDO The PDO instance for testing
     */
    private $pdo;
    
    /**
     * @var Database The database instance for testing
     */
    private $db;
    
    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an in-memory SQLite database for testing
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Create the database instance with the test PDO
        $this->db = $this->createMock(Database::class);
        $this->db->method('getConnection')->willReturn($this->pdo);
        
        // Create test tables
        $this->createTestTables();
        
        // Insert test data
        $this->insertTestData();
    }
    
    /**
     * Create test tables
     */
    private function createTestTables(): void
    {
        // Create users table
        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                first_name TEXT,
                last_name TEXT,
                role TEXT NOT NULL DEFAULT 'user',
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create artists table
        $this->pdo->exec("
            CREATE TABLE artists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                bio TEXT,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create tracks table
        $this->pdo->exec("
            CREATE TABLE tracks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                artist_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                file_path TEXT,
                duration INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (artist_id) REFERENCES artists(id)
            )
        ");
        
        // Create playlists table
        $this->pdo->exec("
            CREATE TABLE playlists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                description TEXT,
                cover_image TEXT,
                is_public INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
        
        // Create playlist_tracks table
        $this->pdo->exec("
            CREATE TABLE playlist_tracks (
                playlist_id INTEGER NOT NULL,
                track_id INTEGER NOT NULL,
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (playlist_id, track_id),
                FOREIGN KEY (playlist_id) REFERENCES playlists(id),
                FOREIGN KEY (track_id) REFERENCES tracks(id)
            )
        ");
    }
    
    /**
     * Insert test data
     */
    private function insertTestData(): void
    {
        // Insert test users
        $this->pdo->exec("
            INSERT INTO users (id, email, password_hash, first_name, last_name) VALUES
            (1, 'user1@example.com', 'password_hash_1', 'John', 'Doe'),
            (2, 'user2@example.com', 'password_hash_2', 'Jane', 'Smith')
        ");
        
        // Insert test artists
        $this->pdo->exec("
            INSERT INTO artists (id, name) VALUES
            (1, 'Test Artist 1'),
            (2, 'Test Artist 2')
        ");
        
        // Insert test tracks
        $this->pdo->exec("
            INSERT INTO tracks (id, artist_id, title, duration) VALUES
            (1, 1, 'Test Track 1', 180),
            (2, 1, 'Test Track 2', 240),
            (3, 2, 'Test Track 3', 300),
            (4, 2, 'Test Track 4', 200)
        ");
        
        // Insert test playlists
        $this->pdo->exec("
            INSERT INTO playlists (id, user_id, name, description, is_public) VALUES
            (1, 1, 'Test Playlist 1', 'Description for playlist 1', 1),
            (2, 1, 'Test Playlist 2', 'Description for playlist 2', 0),
            (3, 2, 'Test Playlist 3', 'Description for playlist 3', 1)
        ");
        
        // Insert test playlist_tracks
        $this->pdo->exec("
            INSERT INTO playlist_tracks (playlist_id, track_id, sort_order) VALUES
            (1, 1, 1),
            (1, 2, 2),
            (2, 3, 1),
            (3, 1, 1),
            (3, 3, 2),
            (3, 4, 3)
        ");
    }
    
    /**
     * Test that a playlist can be found by ID
     */
    public function testFindPlaylistById()
    {
        // Mock the database fetch method
        $this->db->method('fetch')->willReturn([
            'id' => 1,
            'user_id' => 1,
            'name' => 'Test Playlist 1',
            'description' => 'Description for playlist 1',
            'is_public' => 1,
            'created_at' => '2023-01-01 00:00:00',
            'updated_at' => '2023-01-01 00:00:00'
        ]);
        
        $playlist = new Playlist([], $this->db);
        $playlist = $playlist->find(1);
        
        $this->assertInstanceOf(Playlist::class, $playlist);
        $this->assertEquals(1, $playlist->id);
        $this->assertEquals('Test Playlist 1', $playlist->name);
        $this->assertEquals(1, $playlist->is_public);
    }
    
    /**
     * Test that a playlist can get its tracks
     */
    public function testPlaylistCanGetTracks()
    {
        // Mock the database fetchAll method
        $this->db->method('fetchAll')->willReturn([
            [
                'id' => 1,
                'artist_id' => 1,
                'title' => 'Test Track 1',
                'duration' => 180,
                'sort_order' => 1
            ],
            [
                'id' => 2,
                'artist_id' => 1,
                'title' => 'Test Track 2',
                'duration' => 240,
                'sort_order' => 2
            ]
        ]);
        
        $playlist = new Playlist([
            'id' => 1,
            'user_id' => 1,
            'name' => 'Test Playlist 1'
        ], $this->db);
        
        $tracks = $playlist->tracks();
        
        $this->assertIsArray($tracks);
        $this->assertCount(2, $tracks);
        $this->assertInstanceOf(Track::class, $tracks[0]);
        $this->assertEquals('Test Track 1', $tracks[0]->title);
        $this->assertEquals(1, $tracks[0]->sort_order);
    }
    
    /**
     * Test that a track can be added to a playlist
     */
    public function testAddTrackToPlaylist()
    {
        // Mock the Track::find method
        $track = new Track([
            'id' => 4,
            'artist_id' => 2,
            'title' => 'Test Track 4'
        ], $this->db);
        
        // Mock the database fetch and execute methods
        $this->db->method('fetch')->willReturn(['max_order' => 2]);
        $this->db->expects($this->once())->method('execute');
        
        $playlist = new Playlist([
            'id' => 1,
            'user_id' => 1,
            'name' => 'Test Playlist 1'
        ], $this->db);
        
        $result = $playlist->addTrack(4);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test that a track can be removed from a playlist
     */
    public function testRemoveTrackFromPlaylist()
    {
        // Mock the database execute method
        $this->db->expects($this->once())->method('execute');
        
        $playlist = new Playlist([
            'id' => 1,
            'user_id' => 1,
            'name' => 'Test Playlist 1'
        ], $this->db);
        
        $result = $playlist->removeTrack(2);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test that the track count is correct
     */
    public function testTrackCount()
    {
        // Mock the database fetch method
        $this->db->method('fetch')->willReturn(['count' => 2]);
        
        $playlist = new Playlist([
            'id' => 1,
            'user_id' => 1,
            'name' => 'Test Playlist 1'
        ], $this->db);
        
        $count = $playlist->trackCount();
        
        $this->assertEquals(2, $count);
    }
    
    /**
     * Test that the total duration is calculated correctly
     */
    public function testTotalDuration()
    {
        // Mock the database fetch method
        $this->db->method('fetch')->willReturn(['total_duration' => 420]);
        
        $playlist = new Playlist([
            'id' => 1,
            'user_id' => 1,
            'name' => 'Test Playlist 1'
        ], $this->db);
        
        $duration = $playlist->totalDuration();
        
        $this->assertEquals(420, $duration);
    }
    
    /**
     * Test that the formatted duration is correct
     */
    public function testFormattedDuration()
    {
        // Mock the totalDuration method
        $playlist = $this->getMockBuilder(Playlist::class)
            ->setConstructorArgs([[
                'id' => 1,
                'user_id' => 1,
                'name' => 'Test Playlist 1'
            ], $this->db])
            ->onlyMethods(['totalDuration'])
            ->getMock();
        
        $playlist->method('totalDuration')->willReturn(420);
        
        $formatted = $playlist->formattedDuration();
        
        $this->assertEquals('7:00', $formatted);
    }
    
    /**
     * Test that public playlists can be retrieved
     */
    public function testGetPublicPlaylists()
    {
        // Mock the database fetchAll method
        $this->db->method('fetchAll')->willReturn([
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'Test Playlist 1',
                'is_public' => 1
            ],
            [
                'id' => 3,
                'user_id' => 2,
                'name' => 'Test Playlist 3',
                'is_public' => 1
            ]
        ]);
        
        $playlists = Playlist::getPublic();
        
        $this->assertIsArray($playlists);
        $this->assertCount(2, $playlists);
        $this->assertInstanceOf(Playlist::class, $playlists[0]);
        $this->assertEquals('Test Playlist 1', $playlists[0]->name);
    }
    
    /**
     * Test that playlists can be searched
     */
    public function testSearchPlaylists()
    {
        // Mock the database fetchAll method
        $this->db->method('fetchAll')->willReturn([
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'Test Playlist 1',
                'is_public' => 1
            ]
        ]);
        
        $playlists = Playlist::search('Test');
        
        $this->assertIsArray($playlists);
        $this->assertCount(1, $playlists);
        $this->assertInstanceOf(Playlist::class, $playlists[0]);
        $this->assertEquals('Test Playlist 1', $playlists[0]->name);
    }
}