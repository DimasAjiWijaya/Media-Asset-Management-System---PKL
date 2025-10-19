<?php
require_once 'functions.php';
require_login();

// Hitung total file
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM media"))['c'];

// Hitung per tipe
$images = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM media WHERE file_type='image'"))['c'];
$videos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM media WHERE file_type='video'"))['c'];
$docs   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM media WHERE file_type='document'"))['c'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Media Asset</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .tmb {
      text-align: center;
      padding: 15px;
      margin-bottom: 10px;
      background-color: white;
      color: white;
      box-shadow: 0 2px 5px #00000020;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    .tmb a {
      text-decoration: none;
      color: black;
      font-size: 18px;
    }
    .tmb:hover {
      background-color: #526D82;
      text-decoration: none;
    }
    .tmb:hover a {
      color: white;
      text-decoration: none;
    }
  </style>
</head>
<body style="background-color: #DDE6ED;">
  <nav class="navbar navbar-dark" style="background-color: #27374D">
  <div class="container-fluid">
    <span class="navbar-brand">Media Asset</span>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
  <div class="container mt-4">
    <h3>Dashboard</h3>
    <div class="row text-center">
      <div class="col-md-3">
        <div class="card mb-3" style="box-shadow: 1px 1px 5px #9db2bfff">
          <div class="card-body">
            <h4><?php echo $total; ?></h4>
            <p>Total</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card mb-3" style="box-shadow: 1px 1px 5px #9db2bfff">
          <div class="card-body">
            <h4><?php echo $images; ?></h4>
            <p>Images</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card mb-3" style="box-shadow: 1px 1px 5px #9db2bfff">
          <div class="card-body">
            <h4><?php echo $videos; ?></h4>
            <p>Videos</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card mb-3" style="box-shadow: 1px 1px 5px #9db2bfff">
          <div class="card-body">
            <h4><?php echo $docs; ?></h4>
            <p>Docs</p>
          </div>
        </div>
      </div>
    </div>

    <hr>
    <div class="grup1">
      <div class="tmb"><a href="upload.php">📤 Upload Media</a></div>
      <div class="tmb"><a href="list_media.php">📂 Daftar Media</a></div>
      <div class="tmb"><a href="categories.php">📝 Kelola Kategori</a></div>
      <div class="tmb"><a href="trash.php">🗑️ Trash</a></div>
    </div>
  </div>
</body>
</html>
