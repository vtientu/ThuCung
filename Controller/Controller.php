<?php
// Đường dẫn MVC cũ được giữ để tương thích; mọi thao tác mới dùng controller có CSRF.
header('Location: ../index.php', true, 303);
exit;
