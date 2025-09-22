<?php
require_once 'BaseController.php';

class ContactController extends BaseController {
    public function index() {
        // Basit iletisim sayfasi
        $this->render('contact/index', [ 'title' => 'İletişim', 'page_title' => 'İletişim' ]);
    }
    public function send() {
        // Form gelecekte eklenecek; simdilik yönlendirme
        header('Location: index.php?url=contact/index');
        exit();
    }
}
