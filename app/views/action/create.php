<?php
$title = 'Yeni Faaliyet Ekle';
$page_title = 'Yeni Faaliyet Ekle';
$breadcrumb = [
    [
        'url' => 'index.php?url=action/index&aimid=' . $aimid,
        'title' => 'Faaliyetler'
    ],
];
ob_start();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
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
                        <label for="objectiveId">Hedef Seç</label>
                        <select id="objectiveId" name="objectiveId" class="form-control" required>
                            <?php foreach ($objectives as $objective): ?>
                                <option value="<?php echo $objective->getId(); ?>"><?php echo htmlspecialchars($objective->getObjectiveTitle()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="actionTitle">Faaliyet Başlığı</label>
                        <input type="text" class="form-control" id="actionTitle" name="actionTitle" required maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="actionDesc">Faaliyet Açıklaması</label>
                        <textarea class="form-control" id="actionDesc" name="actionDesc" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="actionResponsible">Faaliyet Sorumlusu</label>
                        <input type="text" class="form-control" id="actionResponsible" name="actionResponsible" required>
                    </div>
                    <div class="form-group">
                        <label for="actionStatus">Faaliyet Durumu</label>
                        <select id="actionStatus" name="actionStatus" class="form-control" required>
                            <option value="0">Devam Ediyor</option>
                            <option value="1">Tamamlandı</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group w-80">
                                <label for="dateStart">Başlangıç Tarihi</label>
                                <input type="date" class="form-control" id="dateStart" name="dateStart" required>
                            </div>
                            <div class="form-group w-80">
                                <label for="dateEnd">Bitiş Tarihi</label>
                                <input type="date" class="form-control" id="dateEnd" name="dateEnd" required>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="periodic">Periyodik</label>
                                <select id="periodic" name="periodic" class="form-control" required>
                                    <option value="0">Hayır</option>
                                    <option value="1">Evet</option>
                                </select>
                            </div>
                            <div class="form-group" id="periodTypeGroup" style="display: none;">
                                <label for="periodType">Periyot Türü</label>
                                <select id="periodType" name="periodType" class="form-control">
                                    <option value="1">Haftalık</option>
                                </select>
                            </div>
                            <div class="form-group" id="periodTimeGroup" style="display: none;">
                                <label for="periodTime">Periyot Zamanı</label>
                                <select id="periodTime" name="periodTime" class="form-control">
                                    <option value="1">Pazartesi</option>
                                    <option value="2">Salı</option>
                                    <option value="3">Çarşamba</option>
                                    <option value="4">Perşembe</option>
                                    <option value="5">Cuma</option>
                                    <option value="6">Cumartesi</option>
                                    <option value="7">Pazar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
    document.getElementById('periodic').addEventListener('change', function() {
        var periodTypeGroup = document.getElementById('periodTypeGroup');
        var periodTimeGroup = document.getElementById('periodTimeGroup');
        if (this.value == '1') {
            periodTypeGroup.style.display = 'block';
            periodTimeGroup.style.display = 'block';
        } else {
            periodTypeGroup.style.display = 'none';
            periodTimeGroup.style.display = 'none';
        }
    });
</script>