<?
include_once "config/config.php";

include_once WWWPATH."/includes/users.php";

session_start();
session_regenerate_id(true);

$user = $_POST['user'];
$pass = $_POST['pass'];

if ($users[$user] == md5(PASSWORDSALT.$pass)) {
    $_SESSION['user_id'] = $user;
    header('Location: page_status.php');
} else {
    header('Location: index.php?error=1');
}
?>