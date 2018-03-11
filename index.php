<?php
require_once "config.php";
Model::setDataBaseDir(DOCUMENT_ROOT . "/" . APP_ROOT . "/db/" . DB_NAME);
$dispatcher = new Dispatcher(DOCUMENT_ROOT, APP_ROOT);
$dispatcher->dispatch();
?>
