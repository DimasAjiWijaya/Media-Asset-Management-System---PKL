<?php
require_once 'functions.php';
require_login();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])){
    $name = trim($_POST['name'] ?? '');
    if($name){
        $nameEsc = mysqli_real_escape_string($conn, $name);
        mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$nameEsc')");
        set_flash('Kategori ditambahkan');
        redirect('categories.php');
    }
}

if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    set_flash('Kategori dihapus');
    redirect('categories.php');
}

$res = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<!doctype html><html><head><meta charset="utf-8"><title>Kategori</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body style="background-color: #DDE6ED;">
<div class="container mt-4">
  <h4>Kelola Kategori</h4>
  <?php if($m = get_flash()): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
  <div class="row">
    <div class="col-md-4">
      <form method="post">
        <div class="mb-2"><input name="name" class="form-control" placeholder="Nama kategori" required></div>
        <button name="add" class="btn btn-primary">Tambah</button>
      </form>
    </div>
    <div class="col-md-8">
      <table class="table table-sm">
        <thead><tr><th>#</th><th>Nama</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php while($r = mysqli_fetch_assoc($res)): ?>
            <tr>
              <td><?= e($r['id']) ?></td>
              <td><?= e($r['name']) ?></td>
              <td><a href="categories.php?delete=<?= e($r['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <div class="d-flex justify-content-end gap-2">
        <a href="dashboard.php" class="btn btn-primary">Kembali</a>
    </div>
  </div>
</div>
</body></html>
