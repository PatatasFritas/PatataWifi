<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

$type = @$_POST['type'];

if ($type == "opt_responder") {

	// Set all to 0
    $tmp = array_keys($opt_responder);
    for ($i=0; $i< count($tmp); $i++) {
        $output = exec_fruitywifi("/bin/sed -i \\\"s/opt_responder\\['".$tmp[$i]."'\\]\\[0\\].*/opt_responder\\['".$tmp[$i]."'\\]\\[0\\] = 0;/g\\\" options_config.php");

    }

	// Set to 1 values in POST
    $tmp = $_POST['options'];
    for ($i=0; $i< count($tmp); $i++) {
        exec_fruitywifi("/bin/sed -i \\\"s/opt_responder\\['".$tmp[$i]."'\\]\\[0\\].*/opt_responder\\['".$tmp[$i]."'\\]\\[0\\] = 1;/g\\\" options_config.php");

    }

    header('Location: ../index.php?tab=1');
    exit;

}

header('Location: ../index.php');
?>