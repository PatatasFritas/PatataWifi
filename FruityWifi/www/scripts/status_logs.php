<?
include_once dirname(__FILE__)."/../config/config.php";

//require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

$service = @$_POST['service'];
$path = @$_POST['path'];

exec("tail -n 5 '".LOGPATH."$path'", $output);

for ($i=0; $i < count($output); $i++) {
    $output[$i] = htmlentities($output[$i], ENT_QUOTES, "UTF-8");
}

echo json_encode($output);
?>