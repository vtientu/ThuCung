<?php
header('Location: ../index.php?page=product&id=' . (int) ($_GET['id'] ?? 0));
exit;
