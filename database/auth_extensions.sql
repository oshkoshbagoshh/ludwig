-- Authentication Extensions
-- Additional tables for authentication functionality

-- Remember Me Tokens
CREATE TABLE IF NOT EXISTS remember_tokens (
                                               id SERIAL PRIMARY KEY,
                                               user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                                               token TEXT NOT NULL UNIQUE,
                                               expires_at TIMESTAMPTZ NOT NULL,
                                               created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Password Reset Tokens
CREATE TABLE IF NOT EXISTS password_resets (
                                               id SERIAL PRIMARY KEY,
                                               email TEXT NOT NULL,
                                               token TEXT NOT NULL UNIQUE,
                                               expires_at TIMESTAMPTZ NOT NULL,
                                               created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Add additional fields to users table if they don't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS first_name TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_name TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMPTZ;
