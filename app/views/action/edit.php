<?php
$pageTitle = 'Faaliyet Düzenle';
$breadcrumb = [
    [
        'url' => 'index.php?url=action/index&aimid=' . $aimid,
        'title' => 'Faaliyetler'
    ],
];
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
                                <option value="<?php echo $objective->getId(); ?>" <?php echo $objective->getId() == $action->getObjectiveId() ? 'selected' : ''; ?>><?php echo htmlspecialchars($objective->getObjectiveTitle()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="actionTitle">Faaliyet Başlığı</label>
                        <input type="text" class="form-control" id="actionTitle" name="actionTitle" value="<?php echo htmlspecialchars($action->getActionTitle()); ?>" required maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="actionDesc">Faaliyet Açıklaması</label>
                        <textarea class="form-control" id="actionDesc" name="actionDesc" required><?php echo htmlspecialchars($action->getActionDesc()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="actionResponsible">Faaliyet Sorumlusu</label>
                        <input type="text" class="form-control" id="actionResponsible" name="actionResponsible" value="<?php echo htmlspecialchars($action->getActionResponsible()); ?>" required>
                    </div>
                 
                    <div class="form-group">
                        <label for="actionStatus">Faaliyet Durumu</label>
                        <select id="actionStatus" name="actionStatus" class="form-control" required>
                            <option value="0" <?php echo $action->getActionStatus() == 0 ? 'selected' : ''; ?>>Devam Ediyor</option>
                            <option value="1" <?php echo $action->getActionStatus() == 1 ? 'selected' : ''; ?>>Tamamlandı</option>
                        </select>
                    </div>
                    <div class="row">
                    <div class="col-md-6 col-sm-12">

                    <div class="form-group">
                        <label for="dateStart">Başlangıç Tarihi</label>
                        <input type="date" class="form-control" id="dateStart" name="dateStart" value="<?php echo htmlspecialchars($action->getDateStart()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dateEnd">Bitiş Tarihi</label>
                        <input type="date" class="form-control" id="dateEnd" name="dateEnd" value="<?php echo htmlspecialchars($action->getDateEnd()); ?>" required>
                    </div>
                    </div>

                    <div class="col-md-6 col-sm-12">

                    <div class="form-group">
                        <label for="periodic">Periyodik</label>
                        <select id="periodic" name="periodic" class="form-control" required>
                            <option value="0" <?php echo $action->getPeriodic() == 0 ? 'selected' : ''; ?>>Hayır</option>
                            <option value="1" <?php echo $action->getPeriodic() == 1 ? 'selected' : ''; ?>>Evet</option>
                        </select>
                    </div>
                    <div class="form-group" id="periodTypeGroup" style="display: none;">
                        <label for="periodType">Periyot Türü</label>
                        <select id="periodType" name="periodType" class="form-control">
                            <option value="1" <?php echo $action->getPeriodType() == 1 ? 'selected' : ''; ?>>Haftalık</option>
                        </select>
                    </div>
                    <div class="form-group" id="periodTimeGroup" style="display: none;">
                        <label for="periodTime">Periyot Zamanı</label>
                        <select id="periodTime" name="periodTime" class="form-control">
                            <option value="1" <?php echo $action->getPeriodTime() == 1 ? 'selected' : ''; ?>>Pazartesi</option>
                            <option value="2" <?php echo $action->getPeriodTime() == 2 ? 'selected' : ''; ?>>Salı</option>
                            <option value="3" <?php echo $action->getPeriodTime() == 3 ? 'selected' : ''; ?>>Çarşamba</option>
                            <option value="4" <?php echo $action->getPeriodTime() == 4 ? 'selected' : ''; ?>>Perşembe</option>
                            <option value="5" <?php echo $action->getPeriodTime() == 5 ? 'selected' : ''; ?>>Cuma</option>
                            <option value="6" <?php echo $action->getPeriodTime() == 6 ? 'selected' : ''; ?>>Cumartesi</option>
                            <option value="7" <?php echo $action->getPeriodTime() == 7 ? 'selected' : ''; ?>>Pazar</option>
                        </select>
                    </div>
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

    // Trigger change event on page load to set initial visibility
    document.getElementById('periodic').dispatchEvent(new Event('change'));
</script>
