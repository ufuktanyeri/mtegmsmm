<?php

class Pepper {
    private $pepper;

    public function __construct() {
        $this->pepper = 'seastar'; // Replace with your actual pepper string
    }

    public function hashPassword($password) {
        return password_hash($password . $this->pepper, PASSWORD_BCRYPT);
    }

    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password . $this->pepper, $hashedPassword);
    }
}
?>
