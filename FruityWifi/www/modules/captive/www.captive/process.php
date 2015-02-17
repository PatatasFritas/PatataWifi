<?
include "/usr/share/fruitywifi/www/config/config.php";
include "/usr/share/fruitywifi/www/includes/functions.php";
include "/usr/share/fruitywifi/www/modules/captive/_info_.php";

if( isset( $_POST['ip'] ) && isset( $_POST['mac'] ) ) {
    $ip = htmlentities($_POST['ip']);
    $mac = strtoupper(htmlentities($_POST['mac']));
    $name = htmlentities($_POST["name"]);
    $email = htmlentities($_POST["email"]);

	//Filter command injection
    $name = str_replace("'", "", $name);
    $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
    $email = str_replace("'", "", $email);
    $email = preg_replace("/[^A-Za-z0-9_-@ ]/", '', $email);

    exec_fruitywifi(BIN_IPTABLES." -I internet 1 -t mangle -m mac --mac-source $mac -j RETURN");

    exec_fruitywifi("includes/rmtrack " . $ip);
    sleep(1); // allowing rmtrack to be executed

    // OK, redirection bypassed.
    // Show the logged in message or directly redirect to other website

    // STORE USER
    //exec_fruitywifi(BIN_ECHO." '$name|$email|$ip|$mac|".date("Y-m-d h:i:s")."' >> $file_users ");

    // ADD TO LOGS
    exec_fruitywifi(BIN_ECHO." '$name|$email|$ip|$mac|".date("Y-m-d h:i:s")."' >> $mod_logs ");

    //print_r($_SERVER);
    //exit;

    //header('Location: ' . $_SERVER["HTTP_ORIGIN"]);
    //header('Location: http://10.0.0.1/site/captive/welcome.php?site='.$_SERVER["HTTP_ORIGIN"]);
    //header('Location: http://10.0.0.1/site/captive/welcome.php');
    header('Location: /site/captive/welcome.php');
    exit;
    //echo "User logged in.";
    exit;

} else {
    //echo "Access Denied";
    exit;
}
?>