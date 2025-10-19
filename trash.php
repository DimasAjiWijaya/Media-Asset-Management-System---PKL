<?php
require_once 'functions.php';
require_login();

// soft delete
if(isset($_GET['soft_delete'])){
    $id = (int)$_GET['soft_delete'];
    mysqli_query($conn, "UPDATE media SET is_deleted=1, deleted_at=NOW() WHERE id=$id");
    set_flash('Dipindahkan ke Trash');
    redirect('list_media.php');
}

// restore
if(isset($_GET['restore'])){
    $id = (int)$_GET['restore'];
    mysqli_query($conn, "UPDATE media SET is_deleted=0, deleted_at=NULL WHERE id=$id");
    set_flash('Dipulihkan');
    redirect('trash.php');
}

// delete permanent
if(isset($_GET['delete_perm'])){
    $id = (int)$_GET['delete_perm'];
    // ambil path untuk hapus fisik
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_path FROM media WHERE id=$id"));
    if($r && file_exists(__DIR__ . '/' . $r['file_path'])) @unlink(__DIR__ . '/' . $r['file_path']);
    // hapus versi
    mysqli_query($conn, "DELETE FROM media_versions WHERE media_id=$id");
    mysqli_query($conn, "DELETE FROM media WHERE id=$id");
    set_flash('Dihapus permanen');
    redirect('trash.php');
}

// tampilkan trash
$res = mysqli_query($conn, "SELECT m.*, c.name AS category_name, u.username FROM media m LEFT JOIN categories c ON c.id=m.category_id LEFT JOIN users u ON u.id=m.uploaded_by WHERE m.is_deleted=1 ORDER BY m.deleted_at DESC");
?>
<!doctype html><html><head><meta charset="utf-8"><title>Trash</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body style="background-color: #DDE6ED;">
<div class="container mt-4">
  <h4>Trash</h4>
  <?php if($m = get_flash()): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
  <table class="table table-sm">
    <thead><tr><th>#</th><th>Judul</th><th>Deleted At</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php while($r = mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?= e($r['id']) ?></td>
          <td><?= e($r['title']) ?></td>
          <td><?= e($r['deleted_at']) ?></td>
          <td>
            <a class="btn btn-sm btn-success" href="trash.php?restore=<?= e($r['id']) ?>">Restore</a>
            <a class="btn btn-sm btn-danger" href="trash.php?delete_perm=<?= e($r['id']) ?>" onclick="return confirm('Hapus permanen?')">Hapus Permanen</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
    <div class="d-flex justify-content-end gap-2">
        <a href="dashboard.php" class="btn btn-primary">Kembali</a>
    </div>
</div>
</body></html>
