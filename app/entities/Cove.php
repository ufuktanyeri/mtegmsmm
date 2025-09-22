<?php

class Cove {
    private $id;
    private $name;
    private $city;
    private $district;
    private $address;
    private $fields = [];

    public function __construct($id, $coveName, $city=null, $district=null, $address=null) {
        $this->id = $id;
        $this->name = $coveName;
        $this->city = $city;
        $this->district = $district;
        $this->address = $address;
    }

    public function getId() {
        return $this->id;
    }

    public function getCoveName() {
        return $this->name;
    }

    public function getName() {
        return $this->name;
    }

    public function getCity() {
        return $this->city;
    }

    public function getDistrict() {
        return $this->district;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getFields() {
        return $this->fields;
    }

    public function setFields($fields) {
        $this->fields = $fields;
    }

    // Yeni eklenen method - city ve district'i birleştiriyor
    public function getCityDistrict() {
        if ($this->city && $this->district) {
            return $this->city . '/' . $this->district;
        } elseif ($this->city) {
            return $this->city;
        } elseif ($this->district) {
            return $this->district;
        } else {
            return 'Bilinmeyen Bölge';
        }
    }

    // Setter metodları da ekleyebilirsiniz (isteğe bağlı)
    public function setCity($city) {
        $this->city = $city;
    }

    public function setDistrict($district) {
        $this->district = $district;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setName($name) {
        $this->name = $name;
    }
}
?>