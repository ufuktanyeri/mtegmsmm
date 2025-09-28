<?php
// Script to add breadcrumb to FieldController index() method

$file = 'C:\xampp\htdocs\mtegmsmm\app\controllers\FieldController.php';
$content = file_get_contents($file);

// Find the index() method and update it
$oldPattern = '    public function index() {
        $this->checkControllerPermission();
        $fieldModel = new FieldModel();
        $fields = $fieldModel->getAllFields();
        $this->render(\'field/index\', [\'title\' => \'Fields\', \'fields\' => $fields]);
    }';

$newContent = '    public function index() {
        $this->checkControllerPermission();
        $fieldModel = new FieldModel();
        $fields = $fieldModel->getAllFields();

        // Breadcrumb data
        $this->data[\'breadcrumb\'] = [
            [\'title\' => \'Sistem Yönetimi\', \'url\' => \'\'],
            [\'title\' => \'SMM Alanları\', \'url\' => \'\']
        ];

        // Add other data to $this->data
        $this->data[\'title\'] = \'Fields\';
        $this->data[\'fields\'] = $fields;

        $this->render(\'field/index\', $this->data);
    }';

$updatedContent = str_replace($oldPattern, $newContent, $content);

if ($updatedContent !== $content) {
    file_put_contents($file, $updatedContent);
    echo "Successfully updated FieldController index() method with breadcrumb!\n";
} else {
    echo "Could not find the exact pattern to replace. Content may have changed.\n";
}
?>