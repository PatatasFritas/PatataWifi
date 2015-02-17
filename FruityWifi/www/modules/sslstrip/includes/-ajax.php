<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

$newdata = $_POST['newdata'];

if (isset($_POST['type']) and $_POST['type'] == "logs") {

    $filename = $mod_logs;
    if(file_exists($filename) and filesize($filename)>0) {
        $fh = fopen($filename, "r") or die("Could not open file.");
        $data = fread($fh, filesize($filename)) or die("Could not read file.");
        fclose($fh);
        $dump = explode("\n", $data);

        if($dump[count($dump)-1]=="") unset($dump[count($dump)-1]);

        echo json_encode(array_reverse($dump));
    } else {
        echo json_encode(array(""));
    }

}

if (isset($_POST['type']) and $_POST['type'] == "inject") {

    if (isset($_POST['newdata']) and $_POST['newdata'] != "") {
        $newdata = ereg_replace(13,  "", $newdata);
        //exec_fruitywifi("/bin/echo '$newdata' > $mod_path/includes/inject.txt");
        exec_log("/bin/echo '$newdata' > $mod_path/includes/inject.txt");
    }

    //$dump = exec_fruitywifi("cat $mod_path/includes/inject.txt");
    $dump = readfile("$mod_path/includes/inject.txt");

    echo json_encode($dump);

}
?>
