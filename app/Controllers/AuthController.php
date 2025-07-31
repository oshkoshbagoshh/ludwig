<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;

/**
 * Authentication Controller
 * 
 * Handles user registration, login, logout, and password reset.
 */
class AuthController extends Controller
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
     * Display the registration form
     * 
     * @return void
     */
    public function registerPage(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $this->render('auth/register', [
            'title' => 'Register',
        ]);
    }
    
    /**
     * Process the registration form
     * 
     * @return void
     */
    public function register(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        // If not a POST request, redirect to registration page
        if (!$this->isPost()) {
            $this->redirect('/auth/register');
        }
        
        // Get form data
        $email = $this->sanitize($this->getParam('email', ''));
        $password = $this->getParam('password', '');
        $confirmPassword = $this->getParam('confirm_password', '');
        $firstName = $this->sanitize($this->getParam('first_name', ''));
        $lastName = $this->sanitize($this->getParam('last_name', ''));
        
        // Validate form data
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (empty($firstName)) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($lastName)) {
            $errors['last_name'] = 'Last name is required';
        }
        
        // If there are errors, redisplay the form with errors
        if (!empty($errors)) {
            $this->render('auth/register', [
                'title' => 'Register',
                'errors' => $errors,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
            return;
        }
        
        // Register the user
        $user = User::register($email, $password, [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // If registration failed, redisplay the form with an error
        if (!$user) {
            $this->render('auth/register', [
                'title' => 'Register',
                'errors' => ['email' => 'Email already exists'],
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
            return;
        }
        
        // Log the user in
        $_SESSION['user_id'] = $user->id;
        
        // Redirect to home page
        $this->redirect('/');
    }
    
    /**
     * Display the login form
     * 
     * @return void
     */
    public function loginPage(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $this->render('auth/login', [
            'title' => 'Login',
        ]);
    }
    
    /**
     * Process the login form
     * 
     * @return void
     */
    public function login(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        // If not a POST request, redirect to login page
        if (!$this->isPost()) {
            $this->redirect('/auth/login');
        }
        
        // Get form data
        $email = $this->sanitize($this->getParam('email', ''));
        $password = $this->getParam('password', '');
        $rememberMe = (bool) $this->getParam('remember_me', false);
        
        // Validate form data
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        // If there are errors, redisplay the form with errors
        if (!empty($errors)) {
            $this->render('auth/login', [
                'title' => 'Login',
                'errors' => $errors,
                'email' => $email,
            ]);
            return;
        }
        
        // Authenticate the user
        $user = User::authenticate($email, $password);
        
        // If authentication failed, redisplay the form with an error
        if (!$user) {
            $this->render('auth/login', [
                'title' => 'Login',
                'errors' => ['auth' => 'Invalid email or password'],
                'email' => $email,
            ]);
            return;
        }
        
        // Log the user in
        $_SESSION['user_id'] = $user->id;
        
        // If remember me is checked, set a cookie
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + 60 * 60 * 24 * 30; // 30 days
            
            // Store the token in the database
            $query = "
                INSERT INTO remember_tokens (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)
            ";
            
            $this->db->execute($query, [
                ':user_id' => $user->id,
                ':token' => $token,
                ':expires_at' => date('Y-m-d H:i:s', $expires),
            ]);
            
            // Set the cookie
            setcookie('remember_token', $token, $expires, '/', '', false, true);
        }
        
        // Redirect to home page
        $this->redirect('/');
    }
    
    /**
     * Log the user out
     * 
     * @return void
     */
    public function logout(): void
    {
        // Clear the session
        session_unset();
        session_destroy();
        
        // Clear the remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            // Delete the token from the database
            $query = "
                DELETE FROM remember_tokens
                WHERE token = :token
            ";
            
            $this->db->execute($query, [
                ':token' => $_COOKIE['remember_token'],
            ]);
            
            // Delete the cookie
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Redirect to home page
        $this->redirect('/');
    }
    
    /**
     * Display the password reset request form
     * 
     * @return void
     */
    public function forgotPasswordPage(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $this->render('auth/forgot_password', [
            'title' => 'Forgot Password',
        ]);
    }
    
    /**
     * Process the password reset request form
     * 
     * @return void
     */
    public function forgotPassword(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        // If not a POST request, redirect to forgot password page
        if (!$this->isPost()) {
            $this->redirect('/auth/forgot-password');
        }
        
        // Get form data
        $email = $this->sanitize($this->getParam('email', ''));
        
        // Validate form data
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // If there are errors, redisplay the form with errors
        if (!empty($errors)) {
            $this->render('auth/forgot_password', [
                'title' => 'Forgot Password',
                'errors' => $errors,
                'email' => $email,
            ]);
            return;
        }
        
        // Find the user by email
        $user = User::findByEmail($email);
        
        // If user not found, still show success message for security
        if (!$user) {
            $this->render('auth/forgot_password_success', [
                'title' => 'Password Reset Email Sent',
            ]);
            return;
        }
        
        // Generate a password reset token
        $token = bin2hex(random_bytes(32));
        $expires = time() + 60 * 60; // 1 hour
        
        // Store the token in the database
        $query = "
            INSERT INTO password_resets (email, token, expires_at)
            VALUES (:email, :token, :expires_at)
        ";
        
        $this->db->execute($query, [
            ':email' => $email,
            ':token' => $token,
            ':expires_at' => date('Y-m-d H:i:s', $expires),
        ]);
        
        // Send the password reset email
        // This would normally use a mailer service, but for now we'll just show the token
        $this->render('auth/forgot_password_success', [
            'title' => 'Password Reset Email Sent',
            'token' => $token, // In a real app, this would be sent via email
        ]);
    }
    
    /**
     * Display the password reset form
     * 
     * @param string $token The password reset token
     * @return void
     */
    public function resetPasswordPage(string $token): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        // Validate the token
        $query = "
            SELECT * FROM password_resets
            WHERE token = :token
            AND expires_at > :now
            LIMIT 1
        ";
        
        $result = $this->db->fetch($query, [
            ':token' => $token,
            ':now' => date('Y-m-d H:i:s'),
        ]);
        
        // If token is invalid or expired, show an error
        if (!$result) {
            $this->render('auth/reset_password_error', [
                'title' => 'Invalid or Expired Token',
            ]);
            return;
        }
        
        $this->render('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token,
        ]);
    }
    
    /**
     * Process the password reset form
     * 
     * @return void
     */
    public function resetPassword(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        // If not a POST request, redirect to home
        if (!$this->isPost()) {
            $this->redirect('/');
        }
        
        // Get form data
        $token = $this->getParam('token', '');
        $password = $this->getParam('password', '');
        $confirmPassword = $this->getParam('confirm_password', '');
        
        // Validate form data
        $errors = [];
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // If there are errors, redisplay the form with errors
        if (!empty($errors)) {
            $this->render('auth/reset_password', [
                'title' => 'Reset Password',
                'token' => $token,
                'errors' => $errors,
            ]);
            return;
        }
        
        // Validate the token
        $query = "
            SELECT * FROM password_resets
            WHERE token = :token
            AND expires_at > :now
            LIMIT 1
        ";
        
        $result = $this->db->fetch($query, [
            ':token' => $token,
            ':now' => date('Y-m-d H:i:s'),
        ]);
        
        // If token is invalid or expired, show an error
        if (!$result) {
            $this->render('auth/reset_password_error', [
                'title' => 'Invalid or Expired Token',
            ]);
            return;
        }
        
        // Find the user by email
        $user = User::findByEmail($result['email']);
        
        // If user not found, show an error
        if (!$user) {
            $this->render('auth/reset_password_error', [
                'title' => 'User Not Found',
            ]);
            return;
        }
        
        // Update the user's password
        $user->setPassword($password);
        $user->save();
        
        // Delete the token
        $query = "
            DELETE FROM password_resets
            WHERE token = :token
        ";
        
        $this->db->execute($query, [
            ':token' => $token,
        ]);
        
        // Show success message
        $this->render('auth/reset_password_success', [
            'title' => 'Password Reset Successful',
        ]);
    }
}