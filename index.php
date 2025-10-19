<?php
require_once 'functions.php';
if(is_logged_in()) redirect('dashboard.php');

$error = null;
if(isset($_POST['login'])){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $hash, $role);
    if(mysqli_stmt_fetch($stmt)){
        if(password_verify($password, $hash)){
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            redirect('dashboard.php');
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Efek kaca (Glassmorphism) */
    .glass {
      background: rgba(255, 255, 255, 0.15); /* putih transparan */
      backdrop-filter: blur(12px);           /* blur */
      -webkit-backdrop-filter: blur(12px);   /* Safari */
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 4px 30px rgba(255, 255, 255, 0.4);
    }

    body {
      background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                  url('img/Aski.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .btn-hvr {
      background-color: #00000036;
      color: white;
      border: none;
      width: 100%;
      padding: 10px 20px;
      border-radius: 5px;
      transition: background-color 0.3s, transform 0.3s;
    }

    .btn-hvr:hover {
      background-color: #0000008c;
      transform: scale(1.01);
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        
        <!-- Card login dengan efek kaca -->
        <div class="card glass text-white">
          <div class="card-body">
            <h4 class="card-title mb-3 text-center">Login</h4>

            <?php if(isset($error) && $error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn-hvr" name="login">Login</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>

