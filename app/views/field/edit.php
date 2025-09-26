<?php
$pageTitle = 'Alan Düzenle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=field/index',
        'title' => 'SMM Alanları'
    ],
];
?>

<div class="row">

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
                    <label for="inputName">Alan Adı</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($field->getFieldName()); ?>" placeholder="Alan adını giriniz">
                  </div>                 
                
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
              </form>
            </div>
                </div>
              
                </div>

                <?php
?>
