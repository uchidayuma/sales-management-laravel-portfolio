<?php
try {
    $fp = fsockopen('127.0.0.1', 9000, $errno, $errstr, 5);
    if (!$fp) {
        exit(1);
    }
    fclose($fp);
    exit(0);
} catch (Exception $e) {
    exit(1);
}
