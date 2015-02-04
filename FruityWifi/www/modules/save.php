<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."includes/login_check.php";
require_once WWWPATH."includes/filter_getpost.php";
include_once WWWPATH."includes/functions.php";

$type = @$_POST['type'];
$action = @$_POST['action'];
$newdata = html_entity_decode(trim(@$_POST["newdata"]));
$newdata = base64_encode($newdata);

$mod_name = @$_POST['mod_name'];

if ($type == "save_show" and $mod_name != "") {

	if ($action != "checked") {
		exec_fruitywifi("/bin/sed -i 's/^\\\$mod_panel=.*/\\\$mod_panel=\\\"show\\\";/g' $mod_name/_info_.php");
	} else {
		exec_fruitywifi("/bin/sed -i 's/^\\\$mod_panel=.*/\\\$mod_panel=\\\"\\\";/g' $mod_name/_info_.php");
	}
}

header('Location: ../page_modules.php');
exit;
?>