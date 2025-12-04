<?php
session_start();
unset($_SESSION['id_user']);
unset($_SESSION['loggedin']);
session_destroy();

if(isset($_COOKIE['NIM']) && isset($_COOKIE['password'])) {
    unset($_COOKIE['NIM']);
    unset($_COOKIE['password']);
    setcookie('NIM', '', time() - 86400 * 30, '/');
    setcookie('password', '', time() - 86400 * 30, '/');
}
header('Location: index.php');
exit();
?>