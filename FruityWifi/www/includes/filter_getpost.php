<?
require_once dirname(__FILE__) . "/../config/config.php";
include_once dirname(__FILE__) . "/functions.php";

//if ($regex == 1) {
	foreach ($_POST as $id => $data) {
    	if(!is_array($_POST[$id]))
		    regex_standard($_POST[$id], WEBPATH."/msg.php", $regex_extra);
	}
	foreach ($_GET as $id => $data) {
		regex_standard($_GET[$id], WEBPATH."/msg.php", $regex_extra);
	}
//}
?>