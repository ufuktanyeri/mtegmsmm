<?php
/**
 * UnifiedViewService - Merkezi View Rendering Sistemi
 *
 * Bu servis tüm view rendering işlemlerini merkezi bir yerden yönetir.
 * BaseView ve BaseController'daki ikili sistem karmaşıklığını ortadan kaldırır.
 *
 * @author Claude AI Assistant
 * @version 1.0.0
 */

namespace App\Services;

class UnifiedViewService
{
    /**
     * Layout türleri
     */
    const LAYOUT_NONE = 'none';           // Layout kullanmaz
    const LAYOUT_UNIFIED = 'unified';      // unified.php layout (default)
    const LAYOUT_UNIFIED_MAIN = 'unified_main';  // unified_main.php layout (with sidebar)
    const LAYOUT_PUBLIC = 'public';        // public.php layout (minimal)

    /**
     * View konfigürasyonları
     * Her view için hangi layout kullanılacağını belirtir
     */
    private static $viewConfigs = [
        // Layout kullanmayan sayfalar (sadece authentication)
        'user/register' => self::LAYOUT_NONE,
        'user/captcha' => self::LAYOUT_NONE,

        // Public layout kullanan sayfalar
        'user/login' => self::LAYOUT_PUBLIC,
        'user/main' => self::LAYOUT_PUBLIC,
        'user/haberler' => self::LAYOUT_PUBLIC,
        'user/haberlist' => self::LAYOUT_PUBLIC,
        'home/smmnetwork' => self::LAYOUT_PUBLIC,

        // Unified layout kullanan sayfalar (default olarak zaten unified kullanılıyor)
        // 'home/index' => self::LAYOUT_UNIFIED, // Artık default unified kullanıyor

        // Varsayılan olarak unified layout kullanacak yeni sistem
        // Diğer tüm sayfalar için default
    ];

    /**
     * Layout dosya yolları
     */
    private static $layoutPaths = [
        self::LAYOUT_UNIFIED => '/layouts/unified.php',
        self::LAYOUT_UNIFIED_MAIN => '/layouts/unified_main.php',
        self::LAYOUT_PUBLIC => '/layouts/public.php'
    ];

    /**
     * View render et
     *
     * @param string $view View dosya yolu (örn: 'user/login')
     * @param array $data View'a gönderilecek veriler
     * @param array $options Ek seçenekler (layout override vb.)
     * @return void
     * @throws \Exception
     */
    public static function render(string $view, array $data = [], array $options = []): void
    {
        // Veri değişkenlerini ayıkla
        extract($data);

        // View dosyasının yolunu oluştur
        $viewFile = dirname(__DIR__) . "/views/{$view}.php";

        // View dosyası kontrolü
        if (!file_exists($viewFile)) {
            throw new \Exception("View dosyası bulunamadı: {$view}");
        }

        // Layout türünü belirle
        $layoutType = self::determineLayout($view, $options);

        // Layout kullanmıyorsa doğrudan render et
        if ($layoutType === self::LAYOUT_NONE) {
            require $viewFile;
            return;
        }

        // View'ı bir buffer'a render et
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Eğer view kendi içinde unified layout include ediyorsa
        // (eski sistem uyumluluğu için)
        if (strpos($content, 'unified.php') !== false) {
            // View zaten kendi layout'unu yönetiyor, müdahale etme
            echo $content;
            return;
        }

        // Layout dosyasını yükle
        self::loadLayout($layoutType, $content, $data);
    }

    /**
     * Hangi layout kullanılacağını belirle
     *
     * @param string $view View adı
     * @param array $options Seçenekler
     * @return string Layout türü
     */
    private static function determineLayout(string $view, array $options): string
    {
        // Options'da layout override varsa kullan
        if (isset($options['layout'])) {
            return $options['layout'];
        }

        // View için özel konfigürasyon varsa kullan
        if (isset(self::$viewConfigs[$view])) {
            return self::$viewConfigs[$view];
        }

        // contact/index özel durumu - kendi ob_start yapısı var
        if ($view === 'contact/index') {
            return self::LAYOUT_NONE;
        }

        // Varsayılan: unified layout
        return self::LAYOUT_UNIFIED;
    }

    /**
     * Layout dosyasını yükle
     *
     * @param string $layoutType Layout türü
     * @param string $content View içeriği
     * @param array $data View verileri
     * @throws \Exception
     */
    private static function loadLayout(string $layoutType, string $content, array $data): void
    {
        // Layout dosya yolunu al
        $layoutPath = self::$layoutPaths[$layoutType] ?? null;

        if (!$layoutPath) {
            throw new \Exception("Geçersiz layout türü: {$layoutType}");
        }

        // Tam dosya yolu
        $layoutFile = dirname(__DIR__) . '/views' . $layoutPath;

        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout dosyası bulunamadı: {$layoutPath}");
        }

        // Data değişkenlerini tekrar extract et (layout içinde kullanılabilsin)
        extract($data);

        // Layout'u yükle
        require $layoutFile;
    }

    /**
     * View'ın var olup olmadığını kontrol et
     *
     * @param string $view View adı
     * @return bool
     */
    public static function viewExists(string $view): bool
    {
        $viewFile = dirname(__DIR__) . "/views/{$view}.php";
        return file_exists($viewFile);
    }

    /**
     * Belirli bir view için layout override tanımla
     * (Runtime'da dinamik olarak değiştirmek için)
     *
     * @param string $view View adı
     * @param string $layout Layout türü
     */
    public static function setViewLayout(string $view, string $layout): void
    {
        self::$viewConfigs[$view] = $layout;
    }

    /**
     * Yeni bir custom layout ekle
     *
     * @param string $name Layout adı
     * @param string $path Layout dosya yolu
     */
    public static function registerLayout(string $name, string $path): void
    {
        self::$layoutPaths[$name] = $path;
    }

    /**
     * JSON response döndür (AJAX istekleri için)
     *
     * @param array $data Dönülecek veri
     * @param int $statusCode HTTP status kodu
     */
    public static function json(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect yap
     *
     * @param string $url Yönlendirilecek URL
     * @param int $statusCode HTTP status kodu
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        // BASE_URL tanımlı mı kontrol et
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';

        // URL tam URL değilse base URL ekle
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = $baseUrl . ltrim($url, '/');
        }

        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Partial view render et (component'ler için)
     *
     * @param string $partial Partial view adı
     * @param array $data Veri
     * @return string Render edilmiş içerik
     */
    public static function partial(string $partial, array $data = []): string
    {
        extract($data);

        $partialFile = dirname(__DIR__) . "/views/components/{$partial}.php";

        if (!file_exists($partialFile)) {
            return "<!-- Partial bulunamadı: {$partial} -->";
        }

        ob_start();
        require $partialFile;
        return ob_get_clean();
    }
}