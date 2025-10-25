<?php
// functions.php
session_start();
require_once __DIR__ . '/koneksi.php';

define('MAX_FILE_SIZE', 64 * 1024 * 1024); // 64MB

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function is_logged_in(){ return !empty($_SESSION['user_id']); }
function require_login(){ if(!is_logged_in()){ header('Location: login.php'); exit; } }
function current_user_id(){ return $_SESSION['user_id'] ?? null; }

function set_flash($msg){ $_SESSION['flash'] = $msg; }
function get_flash(){ if(isset($_SESSION['flash'])){ $m = $_SESSION['flash']; unset($_SESSION['flash']); return $m; } return null; }

function map_ext_to_type($ext){
    $ext = strtolower($ext);
    $images = ['jpg','jpeg','png','webp','gif'];
    $videos = ['mp4','webm','ogg'];
    $docs   = ['pdf','csv','xls','xlsx','txt','doc','docx'];
    if(in_array($ext, $images)) return 'image';
    if(in_array($ext, $videos)) return 'video';
    return 'document';
}

/**
 * Save uploaded file.
 * Returns [relative_path, file_type] or throws Exception.
 */
function save_upload($file){
    if(!isset($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE){
        throw new Exception('File tidak diunggah');
    }
    if(($file['error'] ?? 0) !== UPLOAD_ERR_OK){
        throw new Exception('Error upload code: ' . ($file['error'] ?? 'unknown'));
    }
    if($file['size'] > MAX_FILE_SIZE) throw new Exception('File terlalu besar (max 64MB)');

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $type = map_ext_to_type($ext);
    $sub = $type === 'image' ? 'images' : ($type === 'video' ? 'videos' : 'docs');

    $uploadDir = __DIR__ . '/uploads/' . $sub;
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = $uploadDir . DIRECTORY_SEPARATOR . $newName;
    if(!move_uploaded_file($file['tmp_name'], $dest)){
        throw new Exception('Gagal memindahkan file ke folder uploads');
    }

    // relative path for saving in DB (use forward slash)
    $relative = 'uploads/' . $sub . '/' . $newName;
    return [$relative, $type];
}

/** Simple redirect helper */
function redirect($url){
    header('Location: ' . $url);
    exit;
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

function require_role($roles) {
    $role = current_role();
    if (!$role || !in_array($role, (array)$roles)) {
        header('Location: dashboard.php');
        exit;
    }
}

function is_admin() {
    return (current_role() === 'admin');
}

function is_user() {
    return (current_role() === 'user');
}

?>
