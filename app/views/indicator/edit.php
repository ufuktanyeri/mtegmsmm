<?php
$pageTitle = 'Gösterge Düzenle';
$objectives = $objectives ?? [];
$indicatorTypes = $indicatorTypes ?? [];
$fields = $fields ?? [];
$aimid = $aimid ?? null;
$csrfToken = $csrfToken ?? ($_SESSION['csrf_token'] ?? '');
$breadcrumb = [
    [
        'url' => 'index.php?url=indicator/index&aimid=' . $aimid,
        'title' => 'Göstergeler'
    ],
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
            </div>
            <form action="" method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>" />
                <?php if ($aimid): ?><input type="hidden" name="aimid" value="<?php echo (int)$aimid; ?>" /><?php endif; ?>
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
                            <option value="">Hedef Seç</option>
                            <?php foreach ($objectives as $objective): ?>
                                <option value="<?php echo $objective->getId(); ?>" <?php echo $objective->getId() == $indicator->getObjectiveId() ? 'selected' : ''; ?>><?php echo htmlspecialchars($objective->getObjectiveTitle()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!empty($indicatorTypes)): ?>
                    <div class="form-group">
                        <label for="indicatorTypeId">Gösterge Türü</label>
                        <select id="indicatorTypeId" name="indicatorTypeId" class="form-control" required>
                            <option value="">Gösterge Türü Seç</option>
                            <?php foreach ($indicatorTypes as $indicatorType): ?>
                                <option value="<?php echo $indicatorType->getId(); ?>" <?php echo $indicatorType->getId() == $indicator->getIndicatorTypeId() ? 'selected' : ''; ?>><?php echo htmlspecialchars($indicatorType->getIndicatorTitle()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="indicatorTitle">Performans Göstergesi Başlığı</label>
                        <input type="text" class="form-control" id="indicatorTitle" name="indicatorTitle" value="<?php echo htmlspecialchars($indicator->getIndicatorTitle()); ?>" required maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="indicatorDesc">Performans Göstergesi Açıklaması</label>
                        <textarea class="form-control" id="indicatorDesc" name="indicatorDesc" required><?php echo htmlspecialchars($indicator->getIndicatorDesc()); ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="target">Hedeflenen Miktar</label>
                            <input type="number" step="0.01" class="form-control" id="target" name="target" value="<?php echo htmlspecialchars($indicator->getTarget()); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="completed">Tamamlanan Miktar</label>
                            <input type="number" step="0.01" class="form-control" id="completed" name="completed" value="<?php echo htmlspecialchars($indicator->getCompleted()); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="indicatorStatus">Gösterge Durumu</label>
                        <select id="indicatorStatus" name="indicatorStatus" class="form-control" required>
                            <option value="0" <?php echo $indicator->getIndicatorStatus() == 0 ? 'selected' : ''; ?>>Devam Ediyor</option>
                            <option value="1" <?php echo $indicator->getIndicatorStatus() == 1 ? 'selected' : ''; ?>>Tamamlandı</option>
                        </select>
                    </div>
                    <?php if (!empty($fields)): ?>
                    <div class="form-group">
                        <label for="fieldId">Alan Seç</label>
                        <select id="fieldId" name="fieldId" class="form-control" required>
                            <option value="0">Hepsi</option>
                            <?php foreach ($fields as $field): ?>
                                <option value="<?php echo $field->getId(); ?>" <?php echo $field->getId() == $indicator->getFieldId() ? 'selected' : ''; ?>><?php echo htmlspecialchars($field->getFieldName()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
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
