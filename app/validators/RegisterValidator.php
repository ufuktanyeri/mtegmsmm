<?php

class RegisterValidator {
    private $data;
    private $errors = [];
    private static $fields = ['username', 'password', 'email'];

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

        $this->validateUsername();
        $this->validatePassword();
        $this->validateEmail();

        return $this->errors;
    }

    private function validateUsername() {
        $val = trim($this->data['username']);

        if (empty($val)) {
            $this->addError('username', 'Kullanıcı adı gereklidir');
        }
    }

    private function validatePassword() {
        $val = trim($this->data['password']);

        if (empty($val)) {
            $this->addError('password', 'Parola gereklidir');
        } elseif (strlen($val) < 8) {
            $this->addError('password', 'Parola en az 8 karakter olmalıdır');
        }
    }

    private function validateEmail() {
        $val = trim($this->data['email']);

        if (empty($val)) {
            $this->addError('email', 'E-posta gereklidir');
        } elseif (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Geçersiz e-posta formatı');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
