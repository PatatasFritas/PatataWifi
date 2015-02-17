<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

if (isset($_POST['type']) and $_POST['type'] == "opt_responder") {

    foreach ($opt_responder as $key=>$option) {
        exec_fruitywifi("/bin/sed -i 's/opt_responder\\[\\\"".$key."\\\"\\]\\[0\\].*/opt_responder\\[\\\"".$key."\\\"\\]\\[0\\] = 0;/g' options_config.php");
        exec_fruitywifi("/bin/sed -i 's/^".$key.".*/".$key." = Off/g' Responder-master/Responder.conf");
    }

    $tmp = @$_POST["options"];
    for ($i=0; $i<count($tmp); $i++) {

        exec_fruitywifi("/bin/sed -i 's/opt_responder\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/opt_responder\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php");

        exec_fruitywifi("/bin/sed -i 's/^".$tmp[$i].".*/".$tmp[$i]." = On/g' Responder-master/Responder.conf");
    }

    header('Location: ../index.php?tab=1');
    exit;
}

header('Location: ../index.php');

?>