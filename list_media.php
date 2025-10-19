<?php
require_once 'functions.php';
require_login();

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['category'] ?? 0);
$tag = trim($_GET['tag'] ?? '');

$where = "WHERE is_deleted=0";
if($q !== '') $where .= " AND (title LIKE '%" . mysqli_real_escape_string($conn, $q) . "%' OR description LIKE '%" . mysqli_real_escape_string($conn, $q) . "%')";
if($cat) $where .= " AND category_id = " . $cat;
if($tag !== '') $where .= " AND tags LIKE '%" . mysqli_real_escape_string($conn, $tag) . "%'";

$res = mysqli_query($conn, "SELECT m.*, c.name AS category_name, u.username FROM media m LEFT JOIN categories c ON c.id=m.category_id LEFT JOIN users u ON u.id=m.uploaded_by $where ORDER BY m.id DESC");
$catRes = mysqli_query($conn, "SELECT id,name FROM categories ORDER BY name ASC");
?>
<!doctype html><html><head><meta charset="utf-8"><title>Daftar Media</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body style="background-color: #DDE6ED;">
<div class="container mt-4">
  <h4>Daftar Media</h4>
  <form class="row g-2 mb-3">
    <div class="col-md-3"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Cari judul/deskripsi"></div>
    <div class="col-md-3">
      <select name="category" class="form-select">
        <option value="0">Semua Kategori</option>
        <?php mysqli_data_seek($catRes,0); while($c = mysqli_fetch_assoc($catRes)): ?>
          <option value="<?= e($c['id']) ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3"><input class="form-control" name="tag" value="<?= e($tag) ?>" placeholder="Tag"></div>
    <div class="col-md-3 d-grid"><button class="btn btn-primary">Filter</button></div>
  </form>

  <?php if($m = get_flash()): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>#</th><th>Preview</th><th>Judul</th><th>Kategori</th><th>Tags</th><th>Uploader</th><th>Waktu</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($res)): ?>
          <tr>
            <td><?= e($r['id']) ?></td>
            <td style="width:120px;">
              <?php if($r['file_type']==='image'): ?>
                <img src="<?= e($r['file_path']) ?>" style="max-width:100px; max-height:70px;">
              <?php elseif($r['file_type']==='video'): ?>
                <video src="<?= e($r['file_path']) ?>" style="max-width:120px; max-height:70px;" muted></video>
              <?php else: ?>
                <span class="badge bg-secondary">DOC</span>
              <?php endif; ?>
            </td>
            <td><?= e($r['title']) ?></td>
            <td><?= e($r['category_name']) ?></td>
            <td><?= e($r['tags']) ?></td>
            <td><?= e($r['username']) ?></td>
            <td><?= e($r['uploaded_at']) ?></td>
            <td>
              <a class="btn btn-sm btn-primary" href="view_media.php?id=<?= e($r['id']) ?>">Lihat</a>
              <a class="btn btn-sm btn-warning" href="edit_media.php?id=<?= e($r['id']) ?>">Edit</a>
              <a class="btn btn-sm btn-danger" href="trash.php?soft_delete=<?= e($r['id']) ?>" onclick="return confirm('Pindahkan ke trash?')">Trash</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-between gap-2">
    <a href="export.php?<?= http_build_query($_GET) ?>" class="btn btn-outline-secondary">Export CSV (hasil filter)</a>
    <a href="dashboard.php" class="btn btn-primary">Kembali</a>
  </div>
</div>
</body></html>
