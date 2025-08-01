# Roles and Permissions System

This document explains the roles and permissions system implemented in the Music Platform MVP.

## Overview

The system uses a role-based access control (RBAC) approach where:

1. **Users** are assigned a **Role** (e.g., guest, user, editor, admin)
2. **Roles** are granted specific **Permissions** (e.g., view_content, create_playlist)
3. Access to resources is controlled by checking if a user has the required permission

This approach provides flexibility and scalability for managing access control across the application.

## Database Structure

The system uses the following database tables:

### Roles Table

Stores the available roles in the system.

```sql
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

### Permissions Table

Stores the available permissions in the system.

```sql
CREATE TABLE permissions (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

### Role Permissions Table

Maps roles to permissions (many-to-many relationship).

```sql
CREATE TABLE role_permissions (
    role_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id INTEGER NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (role_id, permission_id)
);
```

### Users Table

The existing users table has been extended with a `role_id` column that references the roles table.

```sql
ALTER TABLE users ADD COLUMN role_id INTEGER REFERENCES roles(id);
```

## Default Roles

The system comes with the following default roles:

1. **Guest**: Unauthenticated users with limited access
2. **User**: Standard authenticated users
3. **Editor**: Users who can edit content but not manage users
4. **Admin**: Users with full administrative access

## Default Permissions

The system includes the following default permissions:

- **view_content**: Can view public content
- **create_playlist**: Can create playlists
- **edit_playlist**: Can edit own playlists
- **delete_playlist**: Can delete own playlists
- **purchase_track**: Can purchase tracks
- **download_track**: Can download purchased tracks
- **create_blog**: Can create blog posts
- **edit_blog**: Can edit blog posts
- **delete_blog**: Can delete blog posts
- **manage_users**: Can manage user accounts
- **manage_content**: Can manage all content
- **manage_system**: Can manage system settings

## Role-Permission Assignments

By default, permissions are assigned to roles as follows:

### Guest
- view_content

### User
- view_content
- create_playlist
- edit_playlist
- delete_playlist
- purchase_track
- download_track

### Editor
- All User permissions
- create_blog
- edit_blog
- delete_blog
- manage_content

### Admin
- All permissions

## Usage in Code

### Checking Permissions

To check if a user has a specific permission:

```php
// In a controller method
if ($user->hasPermission('create_playlist')) {
    // Allow the user to create a playlist
}
```

### Requiring Permissions

To require a specific permission to access a resource:

```php
// In a controller method
AccessControl::requirePermission('manage_users');
```

### Checking Roles

To check if a user has a specific role:

```php
// In a controller method
if ($user->hasRole('admin')) {
    // Allow admin-only functionality
}
```

### Requiring Roles

To require a specific role to access a resource:

```php
// In a controller method
AccessControl::requireRole('admin');
```

## Extending the System

### Adding New Roles

To add a new role:

```php
$role = new Role([
    'name' => 'moderator',
    'description' => 'Can moderate content but not manage users'
]);
$role->save();
```

### Adding New Permissions

To add a new permission:

```php
$permission = new Permission([
    'name' => 'moderate_comments',
    'description' => 'Can moderate user comments'
]);
$permission->save();
```

### Assigning Permissions to Roles

To assign a permission to a role:

```php
$role = Role::findByName('moderator');
$permission = Permission::findByName('moderate_comments');
$role->addPermission($permission->id);
```

## Conclusion

This roles and permissions system provides a flexible and scalable way to manage access control in the Music Platform MVP. It can be easily extended to support additional roles and permissions as the application grows.