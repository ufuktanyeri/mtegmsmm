<?php

require_once 'BaseController.php';
require_once APP_PATH . 'models/NewsModel.php';
require_once APP_PATH . 'models/GalleryModel.php';
require_once APP_PATH . 'entities/Permission.php';
require_once APP_PATH . 'validators/NewsValidator.php';

class NewsController extends BaseController
{
    protected function checkControllerPermission($perm = 'news.manage')
    {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission && $permission->getPermissionName() === $perm) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            header('Location: index.php?url=home/error');
            exit();
        }
    }

    public function index()
    {
        $this->checkControllerPermission();
        $model = new NewsModel();
        $news = $model->getNewsAll();
        //var_dump($news);

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'İçerik Yönetimi', 'url' => ''],
            ['title' => 'Haber Yönetimi', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['title'] = 'Haberler';
        $this->data['news'] = $news;

        $this->render('news/index', $this->data);
    }

    public function details($params)
    {
        $this->checkControllerPermission();
        $id = htmlspecialchars($params['id']);
        $model = new NewsModel();
        $galleryModel = new GalleryModel();

        $news = $model->getNewsById($id);
        $gallery = $galleryModel->getGalleryByNewsId($id);

        if (!$news) {
            header('Location: index.php?url=news/index&error=Haber bulunamadı');
            exit();
        }

        $this->render('news/details', ['title' => 'Haber Detayı', 'news' => $news, 'gallery' => $gallery]);
    }


    private function sanitizeFilename($filename)
    {
        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);
        // Remove special characters and keep only alphanumeric, dots, dashes and underscores
        $filename = preg_replace('/[^A-Za-z0-9\.\-\_]/', '', $filename);
        // Convert to lowercase
        $filename = strtolower($filename);
        return $filename;
    }

    public function create()
    {
        $this->checkControllerPermission();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $errors = 'Geçersiz CSRF token';
                $this->render('news/create', [
                    'title' => 'Yeni Haber',
                    'errors' => [$errors],
                    'csrfToken' => $csrfToken,
                    // preserve form values
                    'form' => $_POST
                ]);
                return;
            }
            $validator = new NewsValidator($_POST);
            $errors = $validator->validateForm(); // Örnek: dizi döndürüyor

            if (isset($_FILES['frontpage_image'])) {
                $imageErrors = $validator->validateFrontpageImage($_FILES['frontpage_image']);
                if (!empty($imageErrors)) {
                    $errors = array_merge($errors, $imageErrors);
                }
            }

            if (isset($_FILES['gallery']) && $_FILES['gallery']['name'][0] !== '') {
                $galleryErrors = $validator->validateGalleryImages($_FILES['gallery']);
                if (!empty($galleryErrors)) {
                    $errors = array_merge($errors, $galleryErrors);
                }
            }

            if (!empty($errors)) {
                $this->render('news/create', [
                    'title' => 'Yeni Haber',
                    'errors' => $errors,
                    'csrfToken' => $_POST['csrf_token'],
                    'form' => $_POST
                ]);
                return;
            }


            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);
            $content = $_POST['content'];
            $headline = isset($_POST['headline']) ? 1 : 0;
            $state = isset($_POST['state']) ? 1 : 0;
            $orderNo = filter_input(INPUT_POST, 'order_no', FILTER_SANITIZE_NUMBER_INT);

            // Handle frontpage image
            $frontpage_image = null;
            $imageValidateError = "";
            if (isset($_FILES['frontpage_image'])) {
                /*
                $imageValidateError=$this->validateImage($_FILES['frontpage_image']);

                 var_dump($imageValidateError);

                if (!($imageValidateError == "")) {
                   // $errors = 'Geçersiz resim formatı. Sadece JPG ve PNG dosyaları kabul edilir.';
                    $this->render('news/create', ['title' => 'Yeni Haber', 'errors' => [$imageValidateError], 'csrfToken' => $csrfToken]);
                    return;
                }
                */

                $uploadDir = 'uploads/news/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = uniqid() . '_' . $this->sanitizeFilename($_FILES['frontpage_image']['name']);
                $filePath = $uploadDir . $fileName;

                // Klasörün yazılabilir olup olmadığını kontrol et
              /*  if (!is_writable($uploadDir)) {
                    $errorMsg = 'Yükleme klasörüne yazılamıyor: ' . $uploadDir;
                    $this->render('news/create', [
                        'title' => 'Yeni Haber',
                        'errors' => [$errorMsg],
                        'csrfToken' => $csrfToken,
                        'form' => $_POST
                    ]);
                    return;
                }
*/
                $uploadError = $_FILES['frontpage_image']['error'];
                if ($uploadError !== UPLOAD_ERR_OK) {
                    $errorMsg = 'Dosya yükleme hatası';
                    switch ($uploadError) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $errorMsg .= ': Dosya boyutu çok büyük.';
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $errorMsg .= ': Dosya kısmen yüklendi.';
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $errorMsg .= ': Dosya seçilmedi.';
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $errorMsg .= ': Geçici klasör eksik.';
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $errorMsg .= ': Diske yazılamadı.';
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $errorMsg .= ': PHP uzantısı yüklemeyi durdurdu.';
                            break;
                        default:
                            $errorMsg .= '.';
                    }
                    $this->render('news/create', [
                        'title' => 'Yeni Haber',
                        'errors' => [$errorMsg],
                        'csrfToken' => $csrfToken,
                        'form' => $_POST
                    ]);
                    return;
                }

                if (!move_uploaded_file($_FILES['frontpage_image']['tmp_name'], $filePath)) {
                    $errorMsg = 'Dosya yüklenemedi. Hedef klasöre yazılamıyor olabilir veya başka bir hata oluştu.';
                    $this->render('news/create', [
                        'title' => 'Yeni Haber',
                        'errors' => [$errorMsg],
                        'csrfToken' => $csrfToken,
                        'form' => $_POST
                    ]);
                    return;
                }
                $frontpage_image = $filePath;
            }

            $model = new NewsModel();
            $newsId = $model->createNews($title, $details, $content, $frontpage_image, $_SESSION['user_id'], $state, $headline, $orderNo);

            // Handle file uploads
            if ($newsId && isset($_FILES['gallery'])) {
                $galleryModel = new GalleryModel();
                $uploadDir = 'uploads/gallery/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                for ($i = 0; $i < count($_FILES['gallery']['tmp_name']); $i++) {
                    if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                        $tempFile = [
                            'name' => $_FILES['gallery']['name'][$i],
                            'type' => $_FILES['gallery']['type'][$i],
                            'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                            'error' => $_FILES['gallery']['error'][$i],
                            'size' => $_FILES['gallery']['size'][$i]
                        ];
                        /*
                        if (!$this->validateImage($tempFile)) {
                            continue; // Skip invalid files
                        }*/


                        $fileName = uniqid() . '_' . $this->sanitizeFilename($_FILES['gallery']['name'][$i]);
                        $filePath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $filePath)) {
                            $galleryModel->createGalleryItem($newsId, $_SESSION['user_id'], $filePath);
                        }
                    }
                }
            }

            header('Location: index.php?url=news/index');
            exit();
        }

        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;
        $this->render('news/create', ['title' => 'Yeni Haber', 'csrfToken' => $csrfToken]);
    }

    public function edit($params)
    {
        $this->checkControllerPermission();
        $id = htmlspecialchars($params['id']);
        $model = new NewsModel();
        $galleryModel = new GalleryModel();

        $news = $model->getNewsById($id);
        $gallery = $galleryModel->getGalleryByNewsId($id);

        if (!$news) {
            header('Location: index.php?url=news/index&error=Haber bulunamadı');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $errors = 'Geçersiz CSRF token';
                $this->render('news/edit', ['title' => 'Haber Düzenle', 'news' => $news, 'gallery' => $gallery, 'errors' => [$errors], 'csrfToken' => $csrfToken]);
                return;
            }

            $validator = new NewsValidator($_POST);
            $errors = $validator->validateForm();

            $hasNewImage = isset($_FILES['frontpage_image']) && $_FILES['frontpage_image']['error'] !== UPLOAD_ERR_NO_FILE;
            $hasOldImage = $news->getFrontpageImage() !== "";

            if ($hasNewImage || (!$hasNewImage && !$hasOldImage)) {
                $imageErrors = $validator->validateFrontpageImage($_FILES['frontpage_image']);
                if (!empty($imageErrors)) {
                    $errors = array_merge($errors, $imageErrors);
                }
            }

            // --- GALERİ RESİM SAYISI KONTROLÜ ---
            $existingGalleryCount = is_array($gallery) ? count($gallery) : 0;
            $newGalleryCount = (isset($_FILES['gallery']) && $_FILES['gallery']['name'][0] !== '') ? count($_FILES['gallery']['name']) : 0;
            if ($existingGalleryCount >= 3 && $newGalleryCount > 0) {
                $errors[] = 'Zaten 3 veya daha fazla galeri resminiz var. Yeni resim ekleyemezsiniz.';
            } elseif ($existingGalleryCount + $newGalleryCount > 3) {
                $errors[] = 'Toplam galeri resmi sayısı 3\'ü geçemez.';
            }
            // --- SONU ---

            if (isset($_FILES['gallery']) && $_FILES['gallery']['name'][0] !== '') {
                $galleryErrors = $validator->validateGalleryImages($_FILES['gallery']);
                if (!empty($galleryErrors)) {
                    $errors = array_merge($errors, $galleryErrors);
                }
            }

            if (!empty($errors)) {
                $this->render('news/edit', [
                    'title' => 'Haber Düzenle',
                    'news' => $news,
                    'gallery' => $gallery,
                    'errors' => $errors,
                    'csrfToken' => $_POST['csrf_token']
                ]);
                return;
            }

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);
            $content = $_POST['content'];
            $headline = isset($_POST['headline']) ? 1 : 0;
            $state = isset($_POST['state']) ? 1 : 0;
            $orderNo = filter_input(INPUT_POST, 'order_no', FILTER_SANITIZE_NUMBER_INT);

            // Handle frontpage image
            $frontpage_image = $news->getFrontpageImage();
            if (isset($_FILES['frontpage_image'])) {

                /*  if (!$this->validateImage($_FILES['frontpage_image'])) {
                    $errors = 'Geçersiz resim formatı. Sadece JPG ve PNG dosyaları kabul edilir.';
                    $this->render('news/edit', [
                        'title' => 'Haber Düzenle',
                        'news' => $news,
                        'gallery' => $gallery,
                        'errors' => [$errors],
                        'csrfToken' => $csrfToken
                    ]);
                    return;
                }*/

                $uploadDir = 'uploads/news/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = uniqid() . '_' . $this->sanitizeFilename($_FILES['frontpage_image']['name']);
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['frontpage_image']['tmp_name'], $filePath)) {
                    // Delete old image if exists
                    if ($frontpage_image && file_exists($frontpage_image)) {
                        unlink($frontpage_image);
                    }
                    $frontpage_image = $filePath;
                }
            }

            // --- EKLENECEK: Haber güncelleme işlemi ---
            $model->updateNews(
                $id,
                $title,
                $details,
                $content,
                $frontpage_image,
                $_SESSION['user_id'],
                $state,
                $headline,
                $orderNo
            );
            // --- SONU ---

            // Handle gallery images
            if (isset($_FILES['gallery'])) {
                $galleryModel = new GalleryModel();
                $uploadDir = 'uploads/gallery/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                for ($i = 0; $i < count($_FILES['gallery']['tmp_name']); $i++) {
                    if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                        /* $tempFile = [
                            'name' => $_FILES['gallery']['name'][$i],
                            'type' => $_FILES['gallery']['type'][$i],
                            'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                            'error' => $_FILES['gallery']['error'][$i],
                            'size' => $_FILES['gallery']['size'][$i]
                        ];

                        if (!$this->validateImage($tempFile)) {
                            continue; // Skip invalid files
                        }
*/
                        $fileName = uniqid() . '_' . $this->sanitizeFilename($_FILES['gallery']['name'][$i]);
                        $filePath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $filePath)) {
                            $galleryModel->createGalleryItem($id, $_SESSION['user_id'], $filePath);
                        }
                    }
                }
            }

            header('Location: index.php?url=news/index');
            exit();
        }

        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;
        $this->render('news/edit', [
            'title' => 'Haber Düzenle',
            'news' => $news,
            'gallery' => $gallery,
            'csrfToken' => $csrfToken
        ]);
    }

    public function delete($params)
    {
        $this->checkControllerPermission();
        $id = htmlspecialchars($params['id']);

        // Validate CSRF token
        $csrfToken = $_GET['csrf_token'];
        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            header('Location: index.php?url=news/index&error=Geçersiz CSRF token');
            exit();
        }

        $model = new NewsModel();
        $news = $model->getNewsById($id);

        if ($news) {
            // Delete gallery items first
            $galleryModel = new GalleryModel();
            $gallery = $galleryModel->getGalleryByNewsId($id);

            foreach ($gallery as $item) {
                if (file_exists($item->getPath())) {
                    unlink($item->getPath());
                }
            }

            $galleryModel->deleteGalleryByNewsId($id, $_SESSION['user_id']);
            if (file_exists($news->getFrontpageImage())) {
                    unlink($news->getFrontpageImage());
                }

            $model->deleteNews($id, $_SESSION['user_id']);
        }

        header('Location: index.php?url=news/index');
        exit();
    }

    public function deleteGalleryImage()
    {
        $this->checkControllerPermission();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            exit;
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz CSRF token']);
            exit;
        }

        $galleryModel = new GalleryModel();
        $galleryItem = $galleryModel->getGalleryItemById($id, $_SESSION['user_id']);
        if (!$galleryItem) {
            echo json_encode(['success' => false, 'message' => 'Resim bulunamadı']);
            exit;
        }

        // Dosyayı sil
        if ($path && file_exists($path)) {
            unlink($path);
        }
        // Veritabanından sil
        $galleryModel->deleteGalleryItem($id, $_SESSION['user_id']);

        echo json_encode(['success' => true]);
        exit;
    }

    public function updateOrder()
    {
        $this->checkControllerPermission();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            exit;
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz CSRF token']);
            exit;
        }
        $orderArr = $_POST['order'] ?? [];
        if (!is_array($orderArr)) {
            echo json_encode(['success' => false, 'message' => 'Veri hatası']);
            exit;
        }
        $model = new NewsModel();
        foreach ($orderArr as $item) {
            $id = intval($item['id']);
            $orderNo = intval($item['order_no']);
            $model->updateNewsOrderNo($id, $orderNo, $_SESSION['user_id']);
        }
        echo json_encode(['success' => true]);
        exit;
    }
}
