<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."/includes/login_check.php";
//require_once WWWPATH."/includes/filter_getpost.php";
//include_once WWWPATH."/includes/functions.php";

exec("find ./ -name '_info_.php' | sort", $output);

for ($i=0; $i < count($output); $i++) {
    //echo $output[$i]."<br>";
    include $output[$i];
    $module_path = str_replace("_info_.php","",$output[$i]);
    //echo $module_path;
    //echo "$name - $version <br>";
    echo "<a href='$module_path'>$mod_name.$mod_version</a><br>";
}
?>