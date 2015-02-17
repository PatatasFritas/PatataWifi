<?

$autossh_user = "reverse";
$autossh_host = "192.168.0.240";
$autossh_port = "22";
$autossh_listen = "2222";

// autossh options
$mode_options["M"][0] = 0;
$mode_options["o"][0] = 0;
$mode_options["q"][0] = 1;

$mode_options["M"][1] = 1;
$mode_options["o"][1] = 2;
$mode_options["q"][1] = 3;

$mode_options["M"][2] = "ARP";
$mode_options["o"][2] = "";
$mode_options["q"][2] = "";

?>