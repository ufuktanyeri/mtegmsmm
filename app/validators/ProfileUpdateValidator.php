<?php

class ProfileUpdateValidator {
    private $data;
    private $errors = [];
    private static $fields = ['realname', 'username', 'email', 'password'];

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
        $this->validateEmail();
        $this->validatePassword();

        return $this->errors;
    }

    private function validateRealname() {
        $val = trim($this->data['realname']);

        if (empty($val)) {
            $this->addError('realname', 'Gerçek isim gerekli');
        }
    }

    private function validateUsername() {
        $val = trim($this->data['username']);

        if (empty($val)) {
            $this->addError('username', 'Kullanıcı adı gerekli');
        } elseif (!preg_match('/^[a-zA-Z0-9\W_]{6,30}$/', $val)) {
            $this->addError('username', 'Kullanıcı adı 6-30 karakter olmalı ve en az bir küçük harf, büyük harf, rakam veya sembol içermelidir');
        }
    }

    private function validateEmail() {
        $val = trim($this->data['email']);

        if (empty($val)) {
            $this->addError('email', 'E-posta gerekli');
        } elseif (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'E-posta geçersiz');
        }
    }

    private function validatePassword() {
        $val = trim($this->data['password']);

        if (!empty($val) && strlen($val) < 6) {
            $this->addError('password', 'Parola en az 6 karakter olmalı');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
