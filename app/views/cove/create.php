<?php
$pageTitle = 'Yeni SMM Ekle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=cove/index',
        'title' => 'Merkezler'
    ],
];
?>
<div class=row>

<div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
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
                    <label for="inputName">Merkez Adı</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Alan adını giriniz">
                  </div>
                  <div class="form-group">
                    <label for="inputName">Şehir</label>
                    <input type="text" class="form-control" name="city" id="city" placeholder="Alan adını giriniz">
                  </div>
                  <div class="form-group">
                    <label for="inputName">İlçe</label>
                    <input type="text" class="form-control" name="district" id="district" placeholder="İlçe adını giriniz">
                  </div>
                  <div class="form-group">
                    <label for="inputName">Adres</label>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Alan adını giriniz">
                  </div>
                  <div>
            <label>Fields:</label>
            <?php foreach ($fields as $field): ?>
                <div>
                    <input type="checkbox" id="field_<?php echo $field->getId(); ?>" name="fields[]" value="<?php echo $field->getId(); ?>">
                    <label for="field_<?php echo $field->getId(); ?>"><?php echo htmlspecialchars($field->getFieldName()); ?></label>
                </div>
            <?php endforeach; ?>
        </div>
                  </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">SMM Ekle</button>
                </div>
              </form>
            </div>
                </div>

</div>

                <?php
?>
