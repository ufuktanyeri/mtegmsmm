<?php
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">İçerik Yönetimi</div>
                <h2 class="page-title">
                    <i class="ti ti-file-x me-2"></i>
                    Belge Sil
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="<?php echo BASE_URL; ?>index.php?url=documentstrategy/index" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-status-top bg-danger"></div>
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Silme Onayı
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-alert-circle me-2"></i>
                                    </div>
                                    <div>
                                        <strong>Uyarı!</strong> Bu işlem geri alınamaz.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (isset($document)): ?>
                        <div class="mb-3">
                            <p class="text-body-secondary mb-2">Silinecek Belge:</p>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-color: #dc3545; color: white;">
                                    <i class="ti ti-file-pdf"></i>
                                </span>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($document->getDocumentTitle()); ?></div>
                                    <div class="text-body-secondary small"><?php echo htmlspecialchars($document->getDocumentDesc()); ?></div>
                                </div>
                            </div>
                        </div>

                        <p class="text-body-secondary">
                            <strong>"<?php echo htmlspecialchars($document->getDocumentTitle()); ?>"</strong> belgesini 
                            silmek istediğinizden emin misiniz?
                        </p>
                        <?php else: ?>
                        <p class="text-body-secondary">Belge bulunamadı.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent mt-auto">
                        <?php if (isset($document)): ?>
                        <form action="" method="post" class="d-flex gap-2">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">
                            <button type="submit" name="confirm" value="yes" class="btn btn-danger flex-fill">
                                <i class="ti ti-trash me-1"></i>
                                Evet, Sil
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?url=documentstrategy/index" class="btn btn-secondary flex-fill">
                                <i class="ti ti-x me-1"></i>
                                Hayır, İptal Et
                            </a>
                        </form>
                        <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>index.php?url=documentstrategy/index" class="btn btn-secondary w-100">
                            <i class="ti ti-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
?>