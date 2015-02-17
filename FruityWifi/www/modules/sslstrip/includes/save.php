<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
//require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

// Checking all POST & GET variables except $_POST['newdata']
if ($regex == 1) {
	regex_standard(@$_POST['type'], "../../../msg.php", $regex_extra);
	regex_standard(@$_POST['tempname'], "../../../msg.php", $regex_extra);
	regex_standard(@$_POST['action'], "../../../msg.php", $regex_extra);
	regex_standard(@$_GET['mod_action'], "../../../msg.php", $regex_extra);
	regex_standard(@$_GET['mod_service'], "../../../msg.php", $regex_extra);
	regex_standard(@$_POST['new_rename'], "../../../msg.php", $regex_extra);
	regex_standard(@$_POST['new_rename_file'], "../../../msg.php", $regex_extra);
}

$tempname = @$_POST['tempname'];
$action = @$_POST['action'];
$mod_action = @$_GET['mod_action'];
$new_rename = @$_POST['new_rename'];
$new_rename_file = @$_POST['new_rename_file'];

//$newdata unfiltered by regex
$newdata = html_entity_decode(trim(@$_POST['newdata']));
$newdata = base64_encode($newdata);

//! Inject
if (isset($_POST['type']) and $_POST['type'] == "inject") {

    if ($newdata != "") {
        $newdata = ereg_replace(13,  "", $newdata);
        exec_fruitywifi(BIN_ECHO." '$newdata' | base64 --decode > $mod_path/includes/inject.txt");
        exec_fruitywifi(BIN_DOS2UNIX." $mod_path/includes/inject.txt");
    }

    header('Location: ../index.php?tab=2');
    exit;
}

//! Tamperer
if (isset($_POST['type']) and $_POST['type'] == "tamperer") {

    if ($newdata != "") {
        $newdata = ereg_replace(13,  "", $newdata);
        exec_fruitywifi(BIN_ECHO." '$newdata' | base64 --decode > $mod_path/includes/app_cache_poison/config.ini");
        exec_fruitywifi(BIN_DOS2UNIX." $mod_path/includes/app_cache_poison/config.ini");
    }

    header('Location: ../index.php?tab=3');
    exit;

}

//! Templates
if (isset($_POST['type']) and $_POST['type'] == "templates") {
	if ($action == "save") {

		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
				$template_path = "$mod_path/includes/app_cache_poison/templates";
                exec_fruitywifi(BIN_ECHO." '$newdata' | base64 --decode > $template_path/$tempname");
                exec_fruitywifi(BIN_DOS2UNIX." $template_path/$tempname");

    		}
    	}

	} else if ($action == "add_rename") {

		if ($new_rename == "0") {
			//CREATE NEW TEMPLATE
			if ($new_rename_file != "") {
				$template_path = "$mod_path/includes/app_cache_poison/templates";
                //exec_fruitywifi(BIN_TOUCH." $template_path/$new_rename_file");
                touch("$template_path/$new_rename_file");
				$tempname=$new_rename_file;
			}
		} else {
			//RENAME TEMPLATE
			$template_path = "$mod_path/includes/app_cache_poison/templates";
            //exec_fruitywifi(BIN_MV." $template_path/$new_rename $template_path/$new_rename_file");
            rename("$template_path/$new_rename", "$template_path/$new_rename_file");
			$tempname=$new_rename_file;
		}

	} else if ($action == "delete") {
		if ($new_rename != "0") {
			//DELETE TEMPLATE
			$template_path = "$mod_path/includes/app_cache_poison/templates";
            //exec_fruitywifi(BIN_RM." $template_path/$new_rename");
            unlink($template_path/$new_rename);
		}
	}
	header("Location: ../index.php?tab=4&tempname=$tempname");
	exit;
}

//! Filters
if (isset($_POST['type']) and $_POST['type'] == "filters") {
	if ($action == "save") {

		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
				$template_path = "$mod_path/includes/filters/resources/";
                exec_fruitywifi(BIN_ECHO." '$newdata' | base64 --decode > $template_path/$tempname");
                exec_fruitywifi(BIN_DOS2UNIX." $template_path/$tempname");
    		}
    	}

	}
	header("Location: ../index.php?tab=5&tempname=$tempname");
	exit;
}



if(isset($_GET['mod_service'])) {
    if($_GET['mod_service'] == "mod_sslstrip_inject") {
        exec_fruitywifi(BIN_SED." -i 's/mod_sslstrip_inject=.*/mod_sslstrip_inject=".$mod_action.";/g' ../_info_.php");
    }

    if($_GET['mod_service'] == "mod_sslstrip_tamperer") {
        exec_fruitywifi(BIN_SED." -i 's/mod_sslstrip_tamperer=.*/mod_sslstrip_tamperer=".$mod_action.";/g' ../_info_.php");
    }

    if($_GET['mod_service'] == "mod_sslstrip_filter") {
        exec_fruitywifi(BIN_SED." -i 's/mod_sslstrip_filter=.*/mod_sslstrip_filter=\\\"".$mod_action."\\\";/g' ../_info_.php");
    }
}

header('Location: ../index.php');

?>