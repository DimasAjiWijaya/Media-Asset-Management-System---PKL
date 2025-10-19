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

$res = mysqli_query($conn, "SELECT m.id, m.title, m.description, m.file_type, c.name as category, m.tags, u.username, m.uploaded_at FROM media m LEFT JOIN categories c ON c.id=m.category_id LEFT JOIN users u ON u.id=m.uploaded_by $where ORDER BY m.id DESC");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=media_export_' . date('Ymd_His') . '.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID','Title','Description','File Type','Category','Tags','Uploader','Uploaded At']);
while($r = mysqli_fetch_assoc($res)){
    fputcsv($out, [$r['id'],$r['title'],$r['description'],$r['file_type'],$r['category'],$r['tags'],$r['username'],$r['uploaded_at']]);
}
fclose($out);
exit;
