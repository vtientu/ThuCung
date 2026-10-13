<?php
require __DIR__ . '/Controller/Bootstrap.php';
try {
    require __DIR__ . '/Controller/StoreController.php';
    require __DIR__ . '/View/Store.php';
} catch (PDOException $error) {
    database_error($error);
}
