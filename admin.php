<?php
require __DIR__ . '/Controller/Bootstrap.php';
try {
    require __DIR__ . '/Controller/AdminController.php';
    require __DIR__ . '/View/Admin.php';
} catch (PDOException $error) {
    database_error($error);
}
