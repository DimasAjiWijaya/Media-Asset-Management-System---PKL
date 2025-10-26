_________________________________________
No |    Username   |       Role         |  
-----------------------------------------      
1. |     Admin     |      admin123      |
2. |      user     |       12345        |
-----------------------------------------

cara bikin user baru

1. buat file password.php -> salin code di bawah -> generate di browser -> lalu salin hasil generate contoh hasil = $2y$10$xyz....
    
    <?php
    echo password_hash('123', PASSWORD_DEFAULT);
    ?>

    ganti password di bagian (123)

2. lalu insert sql di phpmyadmin

    INSERT INTO users (username, password, role) 
    VALUES ('admin', 'HASH_YANG_KAMU_COPY', 'admin');

    di baris VALUES ubah username, password yang tadi di generate password.php, dan role (hanya ada admin dan user)
