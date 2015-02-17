<?

//Set no caching
header("Expires: Mon, 1 Jan 1900 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "/usr/share/fruitywifi/www/modules/captive/includes/options_config.php";
include "/usr/share/fruitywifi/www/includes/functions.php";

print_header($portal_name);

$site_name = "Free Wifi Zone";
# AUP
$aup = file_get_contents('includes/aup.txt');

?>

<? if (isset($_POST['step']) and $_POST['step'] == "") { ?>

<script>
function validateForm() {

    <? if ($check_name == "1") { ?>
    var x=document.forms['access']['name'].value;
    if (x==null || x=="") {
        alert("Name must be filled out");
        return false;
    }
    <? } ?>

    <? if ($check_email == "1") { ?>
    var x=document.forms['access']['email'].value;
    var atpos=x.indexOf("@");
    var dotpos=x.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
    {
        alert("Not a valid e-mail address");
        return false;
    }
    <? } ?>

}
</script>

<h1>Welcome to <?=$portal_name;?></h1>

  To access you must first enter your details:<br><br>
  <form name="access" action="index.php" method='POST' onsubmit="return validateForm();" autocomplete="off">
    <div style="background-color:#304E87;width:300px; height:20px;" align="left">&nbsp;&nbsp;<img src="includes/wifi-zone.png" height="34px"></div>
    <div style="background-color:#EFEFEF;width:300px" align="center">
  <table border=0 cellpadding=5 cellspacing=0 width="220px" style="background-color:#EFEFEF">
    <tr>
        <td height="10px"></td>
        <td></td>
    </tr>
    <tr>
        <td nowrap>Your Name:</td>
        <td><input type='text' name='name'></td>
    </tr>
    <tr>
        <td>Email:</td>
        <td><input class="input" type='text' name='email'></td>
    </tr>
    <tr>
        <td></td>
        <td><input type='submit' name='next' value='Next >>'></td>
    </tr>
    <tr>
        <td height="10px"></td>
        <td></td>
    </tr>
  </table>
  </div>
  <input type="hidden" name="step" value="2">
  </form>
  <br>
  <b>Note:</b> This system and all connected computers are for UNCLASSIFIED material only
  <br><br>
<? } ?>

<?php

// capture their IP address
$ip = $_SERVER['REMOTE_ADDR'];

// this is the path to the arp command used to get user MAC address
// from it's IP address in linux environment.
$arp = "/usr/sbin/arp";

// execute the arp command to get their mac address
$output = exec_fruitywifi("$arp -an " . $ip);
//preg_match('/..:..:..:..:..:../',$output , $matches);
if(preg_match("/[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}/", $output[0], $macs)) {
	$mac = @$macs[0];
}

// if MAC Address couldn't be identified.
if( $mac === NULL) {
  var_dump($mac);
  echo "Access Denied.";
  exit;
}

if (isset($_POST['step']) and $_POST['step'] == "2") {

$name = htmlentities($_POST['name']);
$email = htmlentities($_POST['email']);

?>
<h1>Welcome to <?php echo $site_name;?></h1>
You must agree to the Acceptable Use Policy:<br><br>

<form action="process.php" method="POST">

    <table border=0 cellpadding=5 cellspacing=0 width="100%">
        <tr>
            <td><textarea name='aup' readonly rows='10' style="width:100%;"><?php echo $aup; ?></textarea></td>
        </tr>
        <tr>
            <td align=center height='23'><input type='submit' name='agree' value='I agree to the Acceptable Use Policy'></td>
        </tr>
    </table>
    <input type="hidden" name="mac" value="<?php echo $mac; ?>" />
    <input type="hidden" name="ip" value="<?php echo $ip; ?>" />
    <input type="hidden" name="name" value="<?php echo $name; ?>" />
    <input type="hidden" name="email" value="<?php echo $email; ?>" />
</form>

<? } ?>

<? print_footer(); ?>

<?
// Function to print page header
function print_header($portal_name) {

  ?>
    <html>
    <head><title>Welcome to <?php echo $portal_name;?></title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <meta name = "viewport" content = "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width /">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <LINK rel="stylesheet" type="text/css" href="includes/style.css">
    </head>

  <body bgcolor=#FFFFFF text=000000>
  <?php
}

// Function to print page footer
function print_footer() {
  echo "</body>";
  echo "</html>";

}
?>