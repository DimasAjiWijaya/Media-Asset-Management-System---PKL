<?php
require_once 'functions.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$m = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM media WHERE id=$id AND is_deleted=0"));
if(!$m) die('Tidak ditemukan');

$catRes = mysqli_query($conn, "SELECT id,name FROM categories ORDER BY name ASC");

$error = null;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $category = (int)($_POST['category'] ?? 0);
    $tags = trim($_POST['tags'] ?? '');

    if(!$title){ $error = 'Judul wajib'; }
    else {
        // jika ada file baru -> simpan versi lama ke media_versions, lalu update file_path
        if(isset($_FILES['file']) && ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE){
            try {
                // simpan versi lama
                $oldPath = $m['file_path'];
                $oldType = $m['file_type'];
                $stmtv = mysqli_prepare($conn, "INSERT INTO media_versions (media_id, file_path, file_type, uploaded_by) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtv, 'issi', $m['id'], $oldPath, $oldType, current_user_id());
                mysqli_stmt_execute($stmtv);

                // simpan file baru
                list($newRel, $newType) = save_upload($_FILES['file']);
                $stmt = mysqli_prepare($conn, "UPDATE media SET title=?, description=?, category_id=?, tags=?, file_path=?, file_type=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'ssisssi', $title, $desc, $category, $tags, $newRel, $newType, $m['id']);
                mysqli_stmt_execute($stmt);
            } catch (Exception $e){
                $error = $e->getMessage();
            }
        } else {
            // hanya update metadata
            $stmt = mysqli_prepare($conn, "UPDATE media SET title=?, description=?, category_id=?, tags=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssisi', $title, $desc, $category, $tags, $m['id']);
            mysqli_stmt_execute($stmt);
        }

        if(!$error){
            set_flash('Perubahan disimpan');
            redirect('view_media.php?id=' . $m['id']);
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit Media</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container mt-4">
  <a href="view_media.php?id=<?= e($m['id']) ?>" class="btn btn-sm btn-secondary mb-2">← Kembali</a>
  <h4>Edit Media</h4>
  <?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-2"><label>Judul</label><input name="title" class="form-control" value="<?= e($m['title']) ?>" required></div>
    <div class="mb-2"><label>Kategori</label>
      <select name="category" class="form-select">
        <option value="0">-- Pilih --</option>
        <?php while($c = mysqli_fetch_assoc($catRes)): ?>
          <option value="<?= e($c['id']) ?>" <?= $m['category_id']==$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-2"><label>Deskripsi</label><textarea name="description" class="form-control"><?= e($m['description']) ?></textarea></div>
    <div class="mb-2"><label>Tags</label><input name="tags" class="form-control" value="<?= e($m['tags']) ?>"></div>
    <div class="mb-3"><label>Ganti File (opsional)</label><input type="file" name="file" class="form-control"></div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
</body></html>
