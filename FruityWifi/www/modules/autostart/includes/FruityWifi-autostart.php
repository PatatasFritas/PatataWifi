<?
// OPTIONS
//$srv_port = "8443";
$srv_https = "on";
$srv_dir = "/usr/share/fruitywifi/www/modules/autostart/includes";
//$web_path = "/FruityWifi";
$web_path = "";
$logs = "/usr/share/fruitywifi/logs/autostart.log";

$protocol = ($srv_https == "on")?'https':'http';
$srv_port = ($srv_https == "on")?8443:8000;

// Login and store cookie
/*

//$post_data = 'user=admin&pass=admin';
$post_data = "";
$agent = "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.1.7) Gecko/20100105 Shiretoko/3.5.7";
$login_url = "$protocol://localhost$web_path/login.php";

$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_URL, $login_url );
curl_setopt($ch, CURLOPT_PORT, $srv_port );
curl_setopt($ch, CURLOPT_POST, 1 );
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt($ch, CURLOPT_COOKIEJAR, '/usr/share/fruitywifi/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
#curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

//Execute the action to login
$output = curl_exec($ch);
*/


include "$srv_dir/options_config.php";

exec("echo '----------\n".date('Y-m-d H:i:s')." (Autostart)' >> $logs"); //LOG

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_PORT, $srv_port );

foreach ($opt_responder as $opt=>$option) {

    if ($option[0] == 1) {

		sleep(5);
        echo "Executing ".$option[2]."\n";
        // EXEC CURL
        $url = $protocol."://localhost$web_path".$option[3];
        echo $url."\n";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_exec($ch);
        echo "\n";

        exec("echo '- (enabled) ".$option[2]." ' >> $logs"); //LOG
    }
}

?>