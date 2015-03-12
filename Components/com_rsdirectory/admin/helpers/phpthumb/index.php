<?php
if (!file_exists('phpthumb.config.php')) {
        if (file_exists('phpthumb.config.php.default')) {
                echo 'WARNING! "phpthumb.config.php.default" MUST be renamed to "phpthumb.config.php"';
        } else {
                echo 'WARNING! "phpthumb.config.php" should exist but does not';
        }
        exit;
}
header('Location: ./demo/');
?>