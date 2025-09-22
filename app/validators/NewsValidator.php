<?php

class NewsValidator
{
    private $data;
    private $errors = [];
    private static $fields = ['title', 'details', 'order_no'];

    public function __construct($post_data)
    {
        $this->data = $post_data;
    }

    public function validateForm()
    {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                trigger_error("$field is not present in data");
                return;
            }
        }

        $this->validateTitle();
        $this->validateDetails();
        $this->validateContent();
        $this->validateOrderNo();
        return $this->errors;
    }

    private function validateTitle()
    {
        $val = trim($this->data['title']);

        if (empty($val)) {
            $this->addError('title', 'Başlık alanı gerekli');
        } elseif (mb_strlen($val) > 255) {
            $this->addError('title', 'Başlık 150 karakterden uzun olamaz');
        } elseif (mb_strlen($val) < 3) {
            $this->addError('title', 'Başlık en az 3 karakter olmalıdır');
        }
    }

    private function validateDetails()
    {
        $val = trim($this->data['details']);

        if (empty($val)) {
            $this->addError('details', 'Açıklama alanı gerekli');
        } elseif (mb_strlen($val) < 10) {
            $this->addError('details', 'Açıklama en az 10 karakter olmalıdır');
        } elseif (mb_strlen($val) > 200) {
            $this->addError('details', 'Açıklama en az 200 karakterden fazla olamaz');
        }

    }
    private function validateContent()
    {
        $val = trim($this->data['content']);

        if (empty($val)) {
            $this->addError('content', 'İçerik alanı gerekli');
        } elseif (mb_strlen($val) < 10) {
            $this->addError('content', 'İçerik en az 10 karakter olmalıdır');
        }
    }

    private function validateOrderNo()
    {
        $val = trim($this->data['order_no']);

        if (!is_numeric($val)) {
            $this->addError('order_no', 'Sıralama sadece sayı olmalıdır');
        } elseif ($val < 0) {
            $this->addError('order_no', 'Sıralama negatif olamaz');
        }
    }

    private function validateImage($file)
    {
        // Check for upload errors first

        // echo 'upload_max_filesize: ' . ini_get('upload_max_filesize');


        if ($file['size'] > 2 * 1024 * 1024 || ($file['error'] == 1)) {
            return 'Resim boyutu 2 MB\'tan küçük olmalı';
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return 'Geçersiz resim formatı. Sadece JPG ve PNG dosyaları kabul edilir.';
        }

        // Verify file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 'Geçersiz resim formatı. Sadece JPG ve PNG dosyaları kabul edilir.';
        }

        // Check file size (max 5MB)


        return "";
    }
    public function validateFrontpageImage($file)
    {
        $imageValidatorError = "";
        if (empty($file['name'])) {
            $this->addError('frontpage_image', 'Ön resim boş olamaz.');
            return $this->errors; // Optional file
        }


        $imageValidatorError = $this->validateImage($file);

        if (!$imageValidatorError == "") {
            $this->addError('frontpage_image', $imageValidatorError);
            return $this->errors; // Optional file
        }


        return false;
    }

    public function validateGalleryImages($files)
    {
        if (empty($files['name'][0])) {
            return true; // Optional files
        }

        $max_files = 3;

        if (count($files['name']) > $max_files) {
            $this->addError('gallery', 'En fazla 3 resim yüklenebilir');
            return $this->errors;
        }

        $imageValidatorError = "";
        for ($i = 0; $i < count($files['name']); $i++) {
               $tempFile = [
                            'name' =>$files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
            $imageValidatorError = $this->validateImage($tempFile);

            if (!$imageValidatorError == "") {

                $this->addError('gallery', "Galeri: ".$imageValidatorError);
                return $this->errors; // Optional file
            }
        }

        return false;
    }

    private function addError($key, $val)
    {
        $this->errors[$key] = $val;
    }
}
