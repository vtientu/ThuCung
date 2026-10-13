<?php
require __DIR__ . '/Controller/Bootstrap.php';
try {
    require __DIR__ . '/Controller/AuthController.php';
    require __DIR__ . '/View/Auth.php';
} catch (PDOException $error) {
    database_error($error);
}
