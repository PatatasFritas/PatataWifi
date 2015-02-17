<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

$tempname = @$_POST['tempname'];
$newdata = html_entity_decode(trim(@$_POST['newdata']));
$newdata = base64_encode($newdata);
$new_rename = @$_POST['new_rename'];
$new_rename_file = @$_POST['new_rename_file'];


// SAVE ngrep options
if (isset($_POST['type']) and $_POST['type'] == "mode_ngrep") {


    foreach ($mode_ngrep as $key=>$option) {
        $output = exec_fruitywifi(BIN_SED." -i 's/mode_ngrep\\[\\\"".$key."\\\"\\]\\[0\\].*/mode_ngrep\\[\\\"".$key."\\\"\\]\\[0\\] = 0;/g' options_config.php");
    }

    $tmp = @$_POST['options'];
    for ($i=0; $i< count($tmp); $i++) {
        exec_fruitywifi(BIN_SED." -i 's/mode_ngrep\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_ngrep\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php");
    }

    header('Location: ../index.php?tab=1');
    exit;

}

// START SAVE LISTS
if (isset($_POST['type']) and $_POST['type'] == "templates" and isset($_POST['action'])) {
	if ($_POST['action'] == "save") {

		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") {
    			$newdata = ereg_replace(13,  "", $newdata);
				$template_path = "$mod_path/includes/templates";
                exec_fruitywifi(BIN_ECHO." '$newdata' | base64 --decode > $template_path/$tempname");
    		}
    	}

	} elseif ($_POST['action'] == "add_rename") {

		if ($new_rename == "0") {
			//CREATE NEW TEMPLATE
			if ($new_rename_file != "") {
				$template_path = "$mod_path/includes/templates";
                exec_fruitywifi(BIN_TOUCH." $template_path/$new_rename_file");

				$tempname=$new_rename_file;
			}
		} else {
			//RENAME TEMPLATE
			$template_path = "$mod_path/includes/templates";
            exec_fruitywifi(BIN_MV." $template_path/$new_rename $template_path/$new_rename_file");

			$tempname=$new_rename_file;
		}

	} elseif ($_POST['action'] == "delete") {

		if ($new_rename != "0") {
			//DELETE TEMPLATE
			$template_path = "$mod_path/includes/templates";
            unlink($template_path/$new_rename);
		}

	}
	header("Location: ../index.php?tab=2&tempname=$tempname");
	exit;
}

header('Location: ../index.php');

?>