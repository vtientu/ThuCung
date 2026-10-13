<?php
header('Location: ../admin.php?section=products&edit=' . (int) ($_GET['SUA'] ?? 0), true, 303);
exit;
