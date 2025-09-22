<?php
// app/Views/BaseView.php
// DEPRECATED: Bu sınıf geriye dönük uyumluluk için korunmuştur.
// Yeni kodlarda UnifiedViewService kullanın.

require_once __DIR__ . '/../services/UnifiedViewService.php';

use App\Services\UnifiedViewService;

class BaseView
{
    /**
     * @deprecated UnifiedViewService::render() kullanın
     */
    public function render($view, $data = [])
    {
        // Tüm render işlemlerini UnifiedViewService'e yönlendir
        try {
            UnifiedViewService::render($view, $data);
        } catch (Exception $e) {
            echo "View render hatası: " . htmlspecialchars($e->getMessage());
            error_log("BaseView render error: " . $e->getMessage());
        }
    }
}
