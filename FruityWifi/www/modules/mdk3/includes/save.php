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


// MODE Beacon Flood Mode [B]
if (isset($_POST['type']) and $_POST['type'] == "mode_b") {

    $tmp = array_keys($mode_b);
    for ($i=0; $i< count($tmp); $i++) {
        //echo $tmp[$i]."<br>";
        exec_fruitywifi("/bin/sed -i 's/mode_b\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_b\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 0;/g' options_config.php");
        //echo $exec."<br>";

    }

    $tmp = $_POST['options'];
    for ($i=0; $i< count($tmp); $i++) {
        //echo $tmp[$i]."<br>";
        exec_fruitywifi("/bin/sed -i 's/mode_b\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_b\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php");
        //echo $exec."<br>";

    }

    $opt_f = $_POST['opt_f'];
    if ($opt_f != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_b\\[\\\"f\\\"\\]\\[2\\].*/mode_b\\[\\\"f\\\"\\]\\[2\\] = \\\"".$opt_f."\\\";/g' options_config.php");
    }

    $opt_v = $_POST['opt_v'];
    if ($opt_v != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_b\\[\\\"v\\\"\\]\\[2\\].*/mode_b\\[\\\"v\\\"\\]\\[2\\] = \\\"".$opt_v."\\\";/g' options_config.php");
    }

    $opt_n = $_POST['opt_n'];
    if ($opt_n != "") {
        $exec = "/bin/sed -i 's/mode_b\\[\\\"n\\\"\\]\\[2\\].*/mode_b\\[\\\"n\\\"\\]\\[2\\] = \\\"".$opt_n."\\\";/g' options_config.php";
        //exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output); //DEPRECATED
        exec_fruitywifi($exec);
    }

    $opt_c = $_POST['opt_c'];
    if ($opt_c != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_b\\[\\\"c\\\"\\]\\[2\\].*/mode_b\\[\\\"c\\\"\\]\\[2\\] = \\\"".$opt_c."\\\";/g' options_config.php");
    }

    $opt_s = $_POST['opt_s'];
    if ($opt_s != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_b\\[\\\"s\\\"\\]\\[2\\].*/mode_b\\[\\\"s\\\"\\]\\[2\\] = \\\"".$opt_s."\\\";/g' options_config.php");
    }

    header('Location: ../index.php?tab=1');
    exit;

}


// MODE Authentication DoS mode [A]
if (isset($_POST['type']) and $_POST['type'] == "mode_a") {

    $mode = "a";

    // CLEAR CHECKBOX
    $tmp = array_keys($mode_a);
    for ($i=0; $i< count($tmp); $i++) {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 0;/g' options_config.php");
        //echo $exec."<br>";
    }

    // SAVE CHECKBOX
    $tmp = $_POST['options'];
    for ($i=0; $i< count($tmp); $i++) {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php");
        //echo $exec."<br>";
    }

    // SAVE VALUES
    $opt = "a";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "i";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "c";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "s";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    header('Location: ../index.php?tab=2');
    exit;

}


// MODE Deauthentication / Disassociation Amok Mode [D]
if (isset($_POST['type']) and $_POST['type'] == "mode_d") {

    $mode = "d";

    // CLEAR CHECKBOX
    $tmp = array_keys($mode_d);
    for ($i=0; $i< count($tmp); $i++) {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 0;/g' options_config.php");
        //echo $exec."<br>";
    }

    // SAVE CHECKBOX
    $tmp = $_POST['options'];
    for ($i=0; $i< count($tmp); $i++) {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_".$mode."\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php");
        //echo $exec."<br>";
    }

    // SAVE VALUES
    $opt = "w";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "b";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "d";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "c";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    $opt = "s";
    $opt_value = $_POST["opt_".$opt];
    if ($opt != "") {
        exec_fruitywifi("/bin/sed -i 's/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\].*/mode_".$mode."\\[\\\"".$opt."\\\"\\]\\[2\\] = \\\"".$opt_value."\\\";/g' options_config.php");
    }

    header('Location: ../index.php?tab=3');
    exit;

}

// START SAVE LISTS
if (isset($_POST['type']) and $_POST['type'] == "templates" and isset($_POST['action'])) {
	if ($_POST['action'] == "save") {

		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
				$template_path = "$mod_path/includes/templates";
                exec_fruitywifi(BIN_ECHO." '$newdata' | base64 --decode > $template_path/$tempname");
    		}
    	}

	} elseif ($_POST['action'] == "add_rename") {

		if ($new_rename == "0") {
			//CREATE NEW TEMPLATE
			if ($new_rename_file != "") {
				$template_path = "$mod_path/includes/templates";
                touch("$template_path/$new_rename_file");
				$tempname=$new_rename_file;
			}
		} else {
			//RENAME TEMPLATE
			$template_path = "$mod_path/includes/templates";
            rename("$template_path/$new_rename", "$template_path/$new_rename_file");
			$tempname=$new_rename_file;
		}

	} elseif ($_POST['action'] == "delete") {
		if ($new_rename != "0") {
			//DELETE TEMPLATE
			$template_path = "$mod_path/includes/templates";
            unlink($template_path/$new_rename);
		}
	}
	header("Location: ../index.php?tab=4&tempname=$tempname");
	exit;
}

header('Location: ../index.php');

?>