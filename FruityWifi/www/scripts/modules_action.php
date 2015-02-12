<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

exit;






$action = @$_GET['action'];
$module = @$_GET['module'];
$version = @$_GET['version'];

if (!isset($action) or $action == "") {
    header('Location: ../page_modules.php');
    exit;
}

if (!isset($module) or $module == "") {
    header('Location: ../page_modules.php');
    exit;
}

if ($action == "install") {
    //exec_fruitywifi("git clone https://github.com/xtr4nge/module_$module.git /usr/share/fruitywifi/www/modules/$module");

    exec_fruitywifi("wget https://github.com/xtr4nge/module_$module/archive/v$version.zip -O /usr/share/fruitywifi/www/modules/module_$module-$version.zip");
    exec_fruitywifi("unzip /usr/share/fruitywifi/www/modules/module_$module-$version.zip -d /usr/share/fruitywifi/www/modules/");
    exec_fruitywifi("rm /usr/share/fruitywifi/www/modules/module_$module-$version.zip");
    exec_fruitywifi("mv /usr/share/fruitywifi/www/modules/module_$module-$version /usr/share/fruitywifi/www/modules/$module");

    $output[0] = "mod-installed";
    echo json_encode($output);
    exit;
}

if ($action == "remove") {
    exec_fruitywifi("apt-get -y remove fruitywifi-module-$module");
    exec_fruitywifi("rm -R /usr/share/fruitywifi/www/modules/$module");

    $output[0] = "removed";
    echo json_encode($output);
    exit;
}

if ($action == "install-deb") {
    exec_fruitywifi("apt-get -y install fruitywifi-module-$module");

    $output[0] = "deb-mod-installed";
    echo json_encode($output);
    exit;
}

if ($action == "remove-deb") {
    exec_fruitywifi("apt-get -y remove fruitywifi-module-$module");

    exec_fruitywifi("rm -R /usr/share/fruitywifi/www/modules/$module");

    $output[0] = "remove-deb";
    echo json_encode($output);
    exit;
}

if (isset($_GET['show'])) {
    header('Location: ../page_modules.php?show');
    exit;
} else {
    header('Location: ../page_modules.php');
}

if (isset($_GET['show-deb'])) {
    header('Location: ../page_modules.php?show-deb');
} else {
    header('Location: ../page_modules.php');
}

?>