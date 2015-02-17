<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

$target = @$_POST["target"];

if ($target == "") {
    $target = "localhost";
}

$dump = exec_fruitywifi(BIN_NMAP." -sS '$target' -p1-1024 -oN logs/".gmdate("Ymd-H-i-s").".log");

//Return results (array)
echo json_encode($dump);
?>
