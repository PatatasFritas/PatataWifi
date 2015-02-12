<?
session_start();
if (!isset($_SESSION['user_id']) and $_SERVER['REMOTE_ADDR']!='127.0.0.1') {
    header('Location: /logout.php');
    exit;
}
?>