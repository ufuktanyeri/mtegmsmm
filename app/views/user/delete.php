<?php
$page_title = $title ?? 'Kullanıcı Sil';
$hidePageHeader = true;
ob_start();
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Sistem Yönetimi</div>
                <h2 class="page-title">
                    <i class="ti ti-user-x me-2"></i>
                    Kullanıcı Sil
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="index.php?url=user/manage" class="btn btn-outline-secondary">
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
                        
                        <div class="mb-3">
                            <p class="text-muted mb-2">Silinecek kullanıcı:</p>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-color: #dc3545; color: white;">
                                    <?php echo strtoupper(substr($user->getUsername(), 0, 2)); ?>
                                </span>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($user->getRealname() ?? $user->getUsername()); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($user->getUsername()); ?></div>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted">
                            <strong>"<?php echo htmlspecialchars($user->getUsername()); ?>"</strong> kullanıcısını 
                            silmek istediğinizden emin misiniz?
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent mt-auto">
                        <form action="" method="post" class="d-flex gap-2">
                            <button type="submit" name="confirm" value="yes" class="btn btn-danger flex-fill">
                                <i class="ti ti-trash me-1"></i>
                                Evet, Sil
                            </button>
                            <button type="submit" name="confirm" value="no" class="btn btn-secondary flex-fill">
                                <i class="ti ti-x me-1"></i>
                                Hayır, İptal Et
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
