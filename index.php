<?php
/**
 * Root Index - Redirect to wwwroot
 * Bu dosya sadece wwwroot/index.php'ye yönlendirme yapar
 */

// wwwroot/index.php'ye yönlendir
header("Location: wwwroot/index.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();