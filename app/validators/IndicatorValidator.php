<?php

class IndicatorValidator {
    private $data;
    private $errors = [];
    private static $fields = ['indicatorTypeId', 'indicatorTitle', 'indicatorDesc', 'target', 'completed', 'indicatorStatus', 'fieldId'];

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

        $this->validateIndicatorTypeId();
        $this->validateIndicatorTitle();
        $this->validateIndicatorDesc();
        $this->validateTarget();
        $this->validateCompleted();
        $this->validateIndicatorStatus();
        $this->validateFieldId();
        return $this->errors;
    }

    private function validateIndicatorTypeId() {
        $val = trim($this->data['indicatorTypeId']);

        if (empty($val)) {
            $this->addError('indicatorTypeId', 'Gösterge Türü gerekli');
        }
    }

    private function validateIndicatorTitle() {
        $val = trim($this->data['indicatorTitle']);

        if (empty($val)) {
            $this->addError('indicatorTitle', 'Gösterge Başlığı gerekli');
        } elseif (mb_strlen($val) > 250) {
            $this->addError('indicatorTitle', 'Gösterge Başlığı en fazla 250 karakter olabilir');
        }
    }

    private function validateIndicatorDesc() {
        $val = trim($this->data['indicatorDesc']);

        if (empty($val)) {
            $this->addError('indicatorDesc', 'Gösterge Açıklaması gerekli');
        }
    }

    private function validateTarget() {
        $val = trim($this->data['target']);

        if (empty($val)) {
            $this->addError('target', 'Hedef gerekli');
        }
    }

    private function validateCompleted() {
        if (!isset($this->data['completed']) || filter_var($this->data['completed'], FILTER_VALIDATE_INT) === false) {
            $this->addError('completed', 'Tamamlanma Durumu geçerli bir tam sayı olmalıdır.');
        }
    }

    private function validateIndicatorStatus() {
        if (!isset($this->data['indicatorStatus']) || filter_var($this->data['indicatorStatus'], FILTER_VALIDATE_INT) === false) {
            $this->addError('indicatorStatus', 'Gösterge Durumu geçerli bir tam sayı olmalıdır.');
        }
    }

    private function validateFieldId() {
        if (!isset($this->data['fieldId']) || filter_var($this->data['fieldId'], FILTER_VALIDATE_INT) === false) {
            $this->addError('fieldId', 'Alan ID geçerli bir tam sayı olmalıdır.');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
