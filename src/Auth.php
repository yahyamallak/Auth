<?php

namespace Yahya\Auth;

class Auth
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function register($name, $password, $email)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, password, email) VALUES (?, ?, ?)";
        
        $stmt = $this->db->query($sql, [$name, $hashedPassword, $email]);

        if($stmt) {
            $userId = $this->db->lastInsertId();
            $this->loginTheUser($userId);
            return true;
        }


        return false;
    }

    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $user = $this->db->query($sql, [$email])->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $this->loginTheUser($user['id']);
            return true;
        }
        return false;
    }

    private function loginTheUser($id) {
        Session::set('user', $id);
    }

    
    public function isLoggedIn()
    {
        return Session::get('user') !== null;
    }
    
    public function logout()
    {
        Session::destroy();
    }
}
