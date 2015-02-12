<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

$service = @$_POST['service'];
$path = @$_POST['path'];

$path = LOGPATH."/install.txt";

$output = exec_fruitywifi("cat $path");

for ($i=0; $i < count($output); $i++)
{
	$output[$i] = htmlentities($output[$i], ENT_QUOTES, "UTF-8");
}

echo json_encode(array_reverse($output));
?>