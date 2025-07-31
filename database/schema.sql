-- Users (for login & role)
CREATE TABLE IF NOT EXISTS users (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     email TEXT NOT NULL UNIQUE,
                                     password_hash TEXT NOT NULL,
                                     role TEXT NOT NULL DEFAULT 'guest',
                                     created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                     updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Artists
CREATE TABLE IF NOT EXISTS artists (
                                       id INTEGER PRIMARY KEY AUTOINCREMENT,
                                       name TEXT NOT NULL,
                                       bio TEXT,
                                       avatar TEXT,
                                       artist_of_week INTEGER NOT NULL DEFAULT 0,
                                       created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                       updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Albums
CREATE TABLE IF NOT EXISTS albums (
                                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                                      artist_id INTEGER NOT NULL REFERENCES artists(id) ON DELETE CASCADE,
                                      title TEXT NOT NULL,
                                      cover TEXT,
                                      release_date TEXT,
                                      created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Tracks
CREATE TABLE IF NOT EXISTS tracks (
                                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                                      artist_id INTEGER NOT NULL REFERENCES artists(id) ON DELETE CASCADE,
                                      album_id INTEGER REFERENCES albums(id) ON DELETE SET NULL,
                                      title TEXT NOT NULL,
                                      artwork TEXT,
                                      file_path TEXT,
                                      bpm INTEGER,
                                      key_signature TEXT,
                                      featured INTEGER NOT NULL DEFAULT 0,
                                      preview_duration INTEGER NOT NULL DEFAULT 30,
                                      created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Genres
CREATE TABLE IF NOT EXISTS genres (
                                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                                      name TEXT NOT NULL UNIQUE,
                                      created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Moods
CREATE TABLE IF NOT EXISTS moods (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     name TEXT NOT NULL UNIQUE,
                                     created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                     updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Instruments
CREATE TABLE IF NOT EXISTS instruments (
                                           id INTEGER PRIMARY KEY AUTOINCREMENT,
                                           name TEXT NOT NULL UNIQUE,
                                           created_at TEXT NOT NULL DEFAULT (datetime('now')),
                                           updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Pivot track ↔ genre
CREATE TABLE IF NOT EXISTS track_genres (
                                            track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                            genre_id INTEGER NOT NULL REFERENCES genres(id) ON DELETE CASCADE,
                                            PRIMARY KEY (track_id, genre_id)
);

-- Pivot track ↔ mood
CREATE TABLE IF NOT EXISTS track_moods (
                                           track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                           mood_id INTEGER NOT NULL REFERENCES moods(id) ON DELETE CASCADE,
                                           PRIMARY KEY (track_id, mood_id)
);

-- Pivot track ↔ instrument
CREATE TABLE IF NOT EXISTS track_instruments (
                                                 track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                                 instrument_id INTEGER NOT NULL REFERENCES instruments(id) ON DELETE CASCADE,
                                                 PRIMARY KEY (track_id, instrument_id)
);