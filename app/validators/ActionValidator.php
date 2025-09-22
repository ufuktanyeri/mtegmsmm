<?php

class ActionValidator {
    private $data;
    private $errors = [];
    private static $fields = ['actionTitle', 'actionDesc', 'actionResponsible', 'actionStatus', 'dateStart', 'dateEnd', 'periodic', 'periodType', 'periodTime'];

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

        $this->validateActionTitle();
        $this->validateActionDesc();
        $this->validateActionResponsible();
        $this->validateActionStatus();
        $this->validateDateStart();
        $this->validateDateEnd();
        $this->validatePeriodic();
        $this->validatePeriodType();
        $this->validatePeriodTime();
        return $this->errors;
    }

    private function validateActionTitle() {
        $val = trim($this->data['actionTitle']);

        if (empty($val)) {
            $this->addError('actionTitle', 'Faaliyet Başlığı gerekli');
        } elseif (mb_strlen($val) > 250) {
            $this->addError('actionTitle', 'Faaliyet Başlığı en fazla 250 karakter olabilir');
        }
    }

    private function validateActionDesc() {
        $val = trim($this->data['actionDesc']);

        if (empty($val)) {
            $this->addError('actionDesc', 'Faaliyet Açıklaması gerekli');
        }
    }

    private function validateActionResponsible() {
        $val = trim($this->data['actionResponsible']);

        if (empty($val)) {
            $this->addError('actionResponsible', 'Faaliyet Sorumlusu gerekli');
        }
    }

    private function validateActionStatus() {
        if (!isset($this->data['actionStatus']) || filter_var($this->data['actionStatus'], FILTER_VALIDATE_INT) === false) {
            $this->addError('actionStatus', 'Faaliyet Durumu geçerli bir tam sayı olmalıdır.');
        }
    }

    private function validateDateStart() {
        $val = trim($this->data['dateStart']);

        if (empty($val)) {
            $this->addError('dateStart', 'Başlangıç Tarihi gerekli');
        }
    }

    private function validateDateEnd() {
        $val = trim($this->data['dateEnd']);

        if (empty($val)) {
            $this->addError('dateEnd', 'Bitiş Tarihi gerekli');
        }
    }

    private function validatePeriodic() {
        if (!isset($this->data['periodic']) || filter_var($this->data['periodic'], FILTER_VALIDATE_INT) === false) {
            $this->addError('periodic', 'Periyodik geçerli bir tam sayı olmalıdır.');
        }
    }

    private function validatePeriodType() {
        $val = trim($this->data['periodType']);

        if (empty($val)) {
            $this->addError('periodType', 'Periyot Türü gerekli');
        }
    }

    private function validatePeriodTime() {
        $val = trim($this->data['periodTime']);

        if (empty($val)) {
            $this->addError('periodTime', 'Periyot Zamanı gerekli');
        }
    }   


    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
