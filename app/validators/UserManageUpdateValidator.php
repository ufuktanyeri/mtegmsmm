<?php

class UserManageUpdateValidator {
    private $data;
    private $errors = [];
    private static $fields = ['realname', 'username', 'password', 'email', 'cove', 'role'];

    public function __construct($post_data) {
        $this->data = $post_data;
    }

    public function validateForm() {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                trigger_error("$field is not present in data");
                return;
            }
        }

        $this->validateRealname();
        $this->validateUsername();
        $this->validatePassword();
        $this->validateEmail();
        $this->validateCove();
        $this->validateRole();

        return $this->errors;
    }

    private function validateRealname() {
        $val = trim($this->data['realname']);

        if (empty($val)) {
            $this->addError('realname', 'Ad Soyad gerekli');
        }
       

    }
    private function validateUsername() {
        $val = trim($this->data['username']);

        if (empty($val)) {
            $this->addError('username', 'Kullanıcı adı gerekli');
        }
       

    }

    private function validatePassword() {
        $val = trim($this->data['password']);

        /*
        if (empty($val)) {
            $this->addError('password', 'Parola gerekli');
        }
            */
        if (!empty($val) && strlen($val)<8) {
            $this->addError('username', 'Paralo en az 8 karakter olmalıdır');
        }
    }

    private function validateEmail() {
        $val = trim($this->data['email']);

        if (empty($val)) {
            $this->addError('email', 'Eposta gerekli');
        } elseif (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Email geçerli formatta değil');
        }
    }

    private function validateCove() {
        $val = trim($this->data['cove']);

        if (empty($val)) {
            $this->addError('cove', 'Merkez gerekli');
        }
    }

    private function validateRole() {
        $val = trim($this->data['role']);

        if (empty($val)) {
            $this->addError('role', 'Rol gerekli');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
