<?php
$pageTitle = 'Amaç Düzenle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=aim/index',
        'title' => 'Amaçlar'
    ],
];
?>
<!-- Bootstrap 5 Duallistbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-duallistbox@4.0.2/dist/bootstrap-duallistbox.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Amaç Düzenle</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="index.php?url=objective/list&aimid=<?php echo $aim->getId(); ?>"  role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Hedefler</a>
                  </li>
                   <li class="nav-item">
                    <a class="nav-link" href="index.php?url=action/index&aimid=<?php echo $aim->getId(); ?>"  role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Faaliyetler</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="index.php?url=indicator/index&aimid=<?php echo $aim->getId(); ?>"   role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Performans Göstergeleri</a>
                  </li>
                 
                </ul>
              </div>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="aimTitle">Amaç Başlığı</label>
                        <input type="text" class="form-control" id="aimTitle" name="aimTitle" value="<?php echo htmlspecialchars($aim->getAimTitle()); ?>" required maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="aimDesc">Amaç Açıklaması</label>
                        <textarea class="form-control" id="aimDesc" name="aimDesc" required><?php echo htmlspecialchars($aim->getAimDesc()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="aimResult">Amaç Sonucu</label>
                        <textarea class="form-control" id="aimResult" name="aimResult"><?php echo htmlspecialchars($aim->getAimResult()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="regulations">Mevzuat Seç</label>
                        <select class="duallistbox" multiple="multiple" name="regulations[]">
                            <?php 
                            foreach ($regulations as $regulation): ?>
                                <option value="<?php echo $regulation->getId(); ?>" <?php echo in_array($regulation->getId(), array_column($aim->getRegulations(), 'id')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($regulation->getRegulationSource() . ' - ' . $regulation->getRegulationDesc()); ?></option>
                            <?php endforeach; ?>
                        </select>                     
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
?>
<!-- Bootstrap Duallistbox -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-duallistbox@4.0.2/dist/jquery.bootstrap-duallistbox.min.js"></script>
<script>
    $(function () {
        $('.duallistbox').bootstrapDualListbox(
            {
                nonSelectedListLabel: 'Tüm Mevzuatlar',
                selectedListLabel: 'İlgili Mevzuatlar',
                filterTextClear: 'Tüm Mevzuatlar',
                filterPlaceHolder: 'Mevzuatları filtrele',
                infoTextEmpty : 'Liste boş',
                infoText: 'Toplam {0}',
                selectorMinimalHeight: 350,
                infoTextFiltered: '<span class="badge badge-warning">Filtrelenen</span> {0} <span class="badge badge-warning">Toplam</span> {1}',


            }
        );
    });
</script>
