<?php
require_once 'functions.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT m.*, c.name AS category_name, u.username FROM media m LEFT JOIN categories c ON c.id=m.category_id LEFT JOIN users u ON u.id=m.uploaded_by WHERE m.id=$id AND m.is_deleted=0"));
if(!$row) die('Media tidak ditemukan');

$versions = mysqli_query($conn, "SELECT v.*, u.username FROM media_versions v LEFT JOIN users u ON u.id=v.uploaded_by WHERE v.media_id=$id ORDER BY v.id DESC");
?>
<!doctype html><html><head><meta charset="utf-8"><title>Lihat Media</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container mt-4">
  <a href="list_media.php" class="btn btn-sm btn-secondary mb-2">← Kembali</a>
  <div class="row g-3">
    <div class="col-md-7">
      <div class="card p-3">
        <?php if($row['file_type']==='image'): ?>
          <img src="<?= e($row['file_path']) ?>" class="img-fluid rounded">
        <?php elseif($row['file_type']==='video'): ?>
          <video src="<?= e($row['file_path']) ?>" class="w-100" controls></video>
        <?php else: ?>
          <p>Dokumen: <a href="<?= e($row['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Download / Buka</a></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card p-3">
        <h5><?= e($row['title']) ?></h5>
        <p class="text-muted"><?= e($row['description']) ?></p>
        <dl class="row">
          <dt class="col-4">Kategori</dt><dd class="col-8"><?= e($row['category_name']) ?></dd>
          <dt class="col-4">Tags</dt><dd class="col-8"><?= e($row['tags']) ?></dd>
          <dt class="col-4">Uploader</dt><dd class="col-8"><?= e($row['username']) ?></dd>
          <dt class="col-4">Waktu</dt><dd class="col-8"><?= e($row['uploaded_at']) ?></dd>
          <dt class="col-4">Path</dt><dd class="col-8"><code><?= e($row['file_path']) ?></code></dd>
        </dl>
        <div class="d-flex gap-2">
          <a class="btn btn-warning btn-sm" href="edit_media.php?id=<?= e($row['id']) ?>">Edit / Versi Baru</a>
          <a class="btn btn-danger btn-sm" href="trash.php?soft_delete=<?= e($row['id']) ?>" onclick="return confirm('Pindahkan ke trash?')">Trash</a>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h6>Riwayat Versi</h6>
          <?php if(mysqli_num_rows($versions) == 0): ?>
            <p class="text-muted">Belum ada versi.</p>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php while($v = mysqli_fetch_assoc($versions)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <small><?= e($v['uploaded_at']) ?> oleh <?= e($v['username']) ?></small>
                    <div><code><?= e($v['file_path']) ?></code></div>
                  </div>
                  <a href="<?= e($v['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>
</body></html>
