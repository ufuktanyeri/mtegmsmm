<?php

class LoginValidator {
    private $data;
    private $errors = [];
    private static $fields = ['username', 'password'];

    public function __construct($post_data) {
        $this->data = $post_data;
    }

    public function validateForm() {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                trigger_error("$field is not present in data");
                return $this->errors;
            }
        }

        $this->validateUsername();
        $this->validatePassword();

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
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
