<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\AccessControl;
use App\Models\User;

/**
 * Admin Controller
 * 
 * Handles admin-only functionality.
 */
class AdminController extends Controller
{
    /**
     * Constructor
     * 
     * @param Database|null $db Optional database instance
     */
    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }
    
    /**
     * Display the admin dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        // Require admin role to access this page
        AccessControl::requireRole('admin');
        
        // Get all users
        $query = "
            SELECT *
            FROM users
            ORDER BY created_at DESC
        ";
        
        $users = $this->db->fetchAll($query);
        
        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'users' => $users
        ]);
    }
    
    /**
     * Display the user management page
     * 
     * @return void
     */
    public function users(): void
    {
        // Require manage_users permission to access this page
        AccessControl::requirePermission('manage_users');
        
        // Get all users
        $query = "
            SELECT *
            FROM users
            ORDER BY created_at DESC
        ";
        
        $users = $this->db->fetchAll($query);
        
        $this->render('admin/users', [
            'title' => 'User Management',
            'users' => $users
        ]);
    }
    
    /**
     * Display the content management page
     * 
     * @return void
     */
    public function content(): void
    {
        // Require manage_content permission to access this page
        AccessControl::requirePermission('manage_content');
        
        $this->render('admin/content', [
            'title' => 'Content Management'
        ]);
    }
    
    /**
     * Display the system settings page
     * 
     * @return void
     */
    public function settings(): void
    {
        // Require manage_system permission to access this page
        AccessControl::requirePermission('manage_system');
        
        $this->render('admin/settings', [
            'title' => 'System Settings'
        ]);
    }
}