<?php
require_once 'functions.php';
require_login();

// ambil kategori
$catRes = mysqli_query($conn, "SELECT id,name FROM categories ORDER BY name ASC");

$error = null;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = (int)($_POST['category'] ?? 0);
    $tags = trim($_POST['tags'] ?? '');

    if(!$title){
        $error = 'Judul wajib diisi';
    } else {
        try {
            // Simpan file ke folder uploads
            list($relative, $dummy) = save_upload($_FILES['file']); 
            
            // Deteksi tipe file berdasarkan extension
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $type = 'image';
            } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) {
                $type = 'video';
            } elseif (in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'])) {
                $type = 'document';
            } else {
                $type = 'other';
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO media (title, description, file_path, file_type, category_id, tags) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssssss', $title, $description, $relative, $type, $category, $tags);
            mysqli_stmt_execute($stmt);

            set_flash('Upload berhasil');
            redirect('list_media.php');
        } catch (Exception $e){
            $error = $e->getMessage();
        }
    }
}
?>

<!doctype html><html><head><meta charset="utf-8"><title>Upload</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body style="background-color: #DDE6ED;">
<div class="container mt-4">
  <h4>Upload Media</h4>
  <?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-2"><label>Judul</label><input name="title" class="form-control" style="box-shadow: 2px 2px 5px #9DB2BF"required></div>
    <div class="mb-2"><label>Kategori</label>
      <select name="category" class="form-select" style="box-shadow: 2px 2px 5px #9DB2BF">
        <option value="">-- Pilih --</option>
        <?php while($c = mysqli_fetch_assoc($catRes)): ?>
          <option value="<?= e($c['id']) ?>"><?= e($c['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-2"><label>Deskripsi</label><textarea name="description" class="form-control" rows="3" style="box-shadow: 2px 2px 5px #9DB2BF"></textarea></div>
    <div class="mb-2"><label>Tags (pisahkan koma)</label><input name="tags" class="form-control" style="box-shadow: 2px 2px 5px #9DB2BF"></div>
    <div class="mb-2"><label>File</label><input type="file" name="file" class="form-control" style="box-shadow: 2px 2px 5px #9DB2BF" required>
      <div class="form-text">Images: jpg/png/webp/gif. Videos: mp4/webm/ogg. Docs: pdf/csv/xls/xlsx/doc/docx (max 64MB).</div>
    </div>
    <div class="d-flex justify-content-between gap-2">
        <button class="btn btn-primary">Upload</button>
        <a href="dashboard.php" class="btn btn-primary">Kembali</a>
    </div>
  </form>
</div>
</body></html>
