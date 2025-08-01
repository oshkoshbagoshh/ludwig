-- Permissions System
-- Tables for role-based access control

-- Roles Table (extends the existing role column in users table)
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Insert default roles
INSERT INTO roles (name, description) 
VALUES 
    ('guest', 'Unauthenticated user with limited access'),
    ('user', 'Standard authenticated user'),
    ('editor', 'Can edit content but not manage users'),
    ('admin', 'Full administrative access')
ON CONFLICT (name) DO NOTHING;

-- Permissions Table
CREATE TABLE IF NOT EXISTS permissions (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Insert default permissions
INSERT INTO permissions (name, description)
VALUES
    ('view_content', 'Can view public content'),
    ('create_playlist', 'Can create playlists'),
    ('edit_playlist', 'Can edit own playlists'),
    ('delete_playlist', 'Can delete own playlists'),
    ('purchase_track', 'Can purchase tracks'),
    ('download_track', 'Can download purchased tracks'),
    ('create_blog', 'Can create blog posts'),
    ('edit_blog', 'Can edit blog posts'),
    ('delete_blog', 'Can delete blog posts'),
    ('manage_users', 'Can manage user accounts'),
    ('manage_content', 'Can manage all content'),
    ('manage_system', 'Can manage system settings')
ON CONFLICT (name) DO NOTHING;

-- Role Permissions (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id INTEGER NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (role_id, permission_id)
);

-- Assign default permissions to roles
-- Guest permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'guest' AND p.name = 'view_content'
ON CONFLICT DO NOTHING;

-- User permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'user' AND p.name IN (
    'view_content', 'create_playlist', 'edit_playlist', 
    'delete_playlist', 'purchase_track', 'download_track'
)
ON CONFLICT DO NOTHING;

-- Editor permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'editor' AND p.name IN (
    'view_content', 'create_playlist', 'edit_playlist', 
    'delete_playlist', 'purchase_track', 'download_track',
    'create_blog', 'edit_blog', 'delete_blog', 'manage_content'
)
ON CONFLICT DO NOTHING;

-- Admin permissions (all permissions)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'admin'
ON CONFLICT DO NOTHING;

-- Update users table to reference roles table
-- This will maintain backward compatibility while adding the foreign key constraint
ALTER TABLE users ADD COLUMN IF NOT EXISTS role_id INTEGER REFERENCES roles(id);

-- Update existing users to have the correct role_id based on their role string
UPDATE users SET role_id = r.id
FROM roles r
WHERE users.role = r.name AND users.role_id IS NULL;