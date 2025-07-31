-- Users (for login & role)
CREATE TABLE IF NOT EXISTS users (
                                     id SERIAL PRIMARY KEY,
                                     email TEXT NOT NULL UNIQUE,
                                     password_hash TEXT NOT NULL,
                                     role TEXT NOT NULL DEFAULT 'guest',
                                     created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                     updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Artists
CREATE TABLE IF NOT EXISTS artists (
                                       id SERIAL PRIMARY KEY,
                                       name TEXT NOT NULL,
                                       bio TEXT,
                                       avatar TEXT,
                                       artist_of_week BOOLEAN NOT NULL DEFAULT FALSE,
                                       created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                       updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Albums
CREATE TABLE IF NOT EXISTS albums (
                                      id SERIAL PRIMARY KEY,
                                      artist_id INTEGER NOT NULL REFERENCES artists(id) ON DELETE CASCADE,
                                      title TEXT NOT NULL,
                                      cover TEXT,
                                      release_date DATE,
                                      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                      updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Tracks
CREATE TABLE IF NOT EXISTS tracks (
                                      id SERIAL PRIMARY KEY,
                                      artist_id INTEGER NOT NULL REFERENCES artists(id) ON DELETE CASCADE,
                                      album_id INTEGER REFERENCES albums(id) ON DELETE SET NULL,
                                      title TEXT NOT NULL,
                                      artwork TEXT,
                                      file_path TEXT,
                                      bpm INTEGER,
                                      key_signature TEXT,
                                      featured BOOLEAN NOT NULL DEFAULT FALSE,
                                      preview_duration INTEGER NOT NULL DEFAULT 30,
                                      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                      updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Genres
CREATE TABLE IF NOT EXISTS genres (
                                      id SERIAL PRIMARY KEY,
                                      name TEXT NOT NULL UNIQUE,
                                      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                      updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Moods
CREATE TABLE IF NOT EXISTS moods (
                                     id SERIAL PRIMARY KEY,
                                     name TEXT NOT NULL UNIQUE,
                                     created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                     updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Instruments
CREATE TABLE IF NOT EXISTS instruments (
                                           id SERIAL PRIMARY KEY,
                                           name TEXT NOT NULL UNIQUE,
                                           created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                           updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
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
