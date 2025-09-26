<?php
$pageTitle = 'Roller';
?>
<div class="row">
    <div class="col-md-12">
        <div class="card w-100">
            <div class="card-header">
                <h3 class="card-title"><a href="index.php?url=user/createRole">Yeni Rol Ekle</a></h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rol Adı</th>
                            <th>Açıklama</th>
                            <th>Ebeveyn Rol</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($role->getId()); ?></td>
                                <td><?php echo htmlspecialchars($role->getRoleName()); ?></td>
                                <td><?php echo htmlspecialchars($role->getDescription()); ?></td>
                                <td>
                                    <?php echo $role->getParentRole() ? htmlspecialchars($role->getParentRole()->getRoleName()) : 'Yok'; ?>
                                </td>
                                <td>
                                    <a href="index.php?url=user/editRole&id=<?php echo $role->getId(); ?>" class="btn btn-warning">Düzenle</a>
                                    <button class="btn btn-danger" 
                                            data-toggle="modal" 
                                            data-target="#deleteModal" 
                                            data-id="<?php echo htmlspecialchars($role->getId()); ?>" 
                                            data-name="<?php echo htmlspecialchars($role->getRoleName()); ?>" 
                                            data-csrf="<?php echo htmlspecialchars($csrfToken); ?>">
                                        Sil
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Sil</a>
            </div>
        </div>
    </div>
</div>
<?php
?>
<script>
    // Ensure the modal is correctly targeting the button and fetching data attributes
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var roleId = button.data('id'); // Extract info from data-* attributes
        var roleName = button.data('name');
        var csrfToken = button.data('csrf');
        var modal = $(this);

        // Update the modal content with the role information
        modal.find('#deleteMessage').text('"' + roleName + '" adlı rolü silmek istediğinizden emin misiniz?');
        modal.find('#confirmDeleteButton').attr('href', 'index.php?url=user/deleteRole&id=' + roleId + '&csrf_token=' + csrfToken);
    });
</script>

