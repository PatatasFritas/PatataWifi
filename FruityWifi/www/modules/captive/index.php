<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include_once "includes/options_config.php";

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWifi</title>
<script src="<?=WEBPATH?>/js/jquery.js"></script>
<script src="<?=WEBPATH?>/js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?=WEBPATH?>/css/jquery-ui.css" />
<link rel="stylesheet" href="<?=WEBPATH?>/css/style.css" />

<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include WWWPATH."/includes/menu.php"; ?>
<br/>
<?

$logfile = @$_GET['logfile'];
$action = @$_GET['action'];

// DELETE LOG
if ($logfile != "" and $action=="delete") {
    if(file_exists($mod_logs_history.$logfile.".log")) {
        if(unlink($mod_logs_history.$logfile.".log")) {
            echo "Eliminado ".$mod_logs_history.$logfile.".log<br/>";
        } else {
            echo "Error al eliminar ".$mod_logs_history.$logfile.".log<br/>";
        }
    }
}


?>

<div class="rounded-top" align="left"> &nbsp; <b>Captive</b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;version <?=$mod_version?><br/>
    <?
    if (file_exists(BIN_CONNTRACK)) {
        echo "conntrack <font style='color:lime'>installed</font><br/>";
    } else {
		echo "conntrack <a href='includes/module_action.php?install=install_captive' style='color:red'>install</a><br/>";
    }

    if (file_exists("/var/www/site/captive")) {
        echo "&nbsp; Captive <font style='color:lime'>installed</font><br/>";
    } else {
        echo "&nbsp; Captive <a href='includes/module_action.php?service=install_portal' style='color:red'>install</a><br/>";
    }


    $ismoduleup = exec_fruitywifi(BIN_IPTABLES." -t mangle -L|grep -iEe 'internet.+anywhere'");
    if (isset($ismoduleup[0]) and $ismoduleup[0] != "") {
        echo "&nbsp; Captive  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=captive&action=stop&page=module\"><b>stop</b></a>";
    } else {
        echo "&nbsp; Captive  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=captive&action=start&page=module\"><b>start</b></a>";
    }
    ?>

</div>

<br/>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">

    <div id="result" class="module">
        <ul>
            <li><a href="#result-1">Output</a></li>
            <li><a href="#result-2">Users</a></li>
            <li><a href="#result-3">Options</a></li>
            <li><a href="#result-4">History</a></li>
        </ul>
        <div id="result-1">
            <form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="index.php">
            <?
                if ($action == "view" and $logfile != "" and file_exists($mod_logs_history.$logfile.".log")) {
                    $filename = $mod_logs_history.$logfile.".log";
                    echo("<b>$logfile.log</b> <a href=\"./\">close</a><br/>");
                } else {
                    $filename = $mod_logs;
                    echo '<input type="submit" value="refresh" /><br/><br/>';
                }

                $data = open_file($filename);

                // REVERSE
                //$data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));

            ?>
            <textarea id="output" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
            <input type="hidden" name="type" value="logs">
            </form>

        </div>

        <!-- USERS -->

        <div id="result-2" class="module-options">
            <form action="index.php" method="GET">
                <input type="hidden" name="tab" value="1">
                <input type="submit" value="refresh">
            </form>
            <br/><br/>
            <div class="module-options" s-tyle="background-color:#000; border:1px dashed; padding:5px">
            <?

            //$filename = "/var/www/site/captive/admin/users";
            $filename = $file_users;

			$output = exec_fruitywifi(BIN_CAT." $filename");
            ?>

            <table border='0'>
            <tr>
                <td></td>
                <td style="padding-right:10px"><b>Name</b></td>
                <td style="padding-right:10px"><b>Email</b></td>
                <td style="padding-right:10px"><b>IP address</b></td>
                <td style="padding-right:10px"><b>MAC address</b></td>
                <td style="padding-right:10px"><b>Start</b></td>
            </tr>
            <?
            for ($i=0; $i < count($output); $i++) {
                $row = explode("|", $output[$i]);
                if (isset($row[4]) and $row[4] != "") {
            ?>
                <tr>
                    <td style="padding-right:5px"><a href="includes/module_action.php?service=users&action=delete&mac=<?=$row[3]?>">Delete</a></td>
                    <td style="background-color:#222; padding-right:10px"><?=$row[0];?></td>
                    <td style="background-color:#222; padding-right:10px"><?=$row[1];?></td>
                    <td style="background-color:#222; padding-right:10px"><?=$row[2];?></td>
                    <td style="background-color:#222; padding-right:10px"><?=$row[3];?></td>
                    <td style="background-color:#222; padding-right:10px"><?=$row[4];?></td>
                </tr>
            <?  }
            }
            ?>
            </table>
            </div>

            <br/>

        </div>

        <!-- OPTIONS -->

        <div id="result-3" class="module-options">
            <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
            <input type="submit" value="save">
            <br/><br/>

            <div class="module-options" s-tyle="background-color:#000; border:1px dashed;">
            <table>
                <!-- // OPTION validate Name -->
                <tr>
                    <? $opt = "name"; ?>
                    <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($portal[$opt][0] == "1") echo "checked" ?> ></td>
                    <td></td>
                    <td nowrap> Validate Name (javascript)</td>
                </tr>
                <!-- // OPTION Validate Email -->
                <tr>
                    <? $opt = "email"; ?>
                    <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($portal[$opt][0] == "1") echo "checked" ?> ></td>
                    <td></td>
                    <td nowrap> Validate Email (javascript)</td>
                </tr>
            </table>
            </div>

            <input type="hidden" name="type" value="portal">
            </form>
            <br/>
            <?
                $filename = "$mod_path/includes/mode_d.txt";

                $data = open_file($filename);

            ?>

        </div>

        <!-- HISTORY -->

        <div id="result-4" class="history">
            <br/><br/>
            <table border="0">
            <?
            $logs = glob($mod_logs_history.'*.log');
            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                ?>
                <tr>
                  <td><a href='?logfile=<?=$filename?>&action=delete&tab=1'><b>x</b></a></td>
                  <td><?=$filename?></td>
                  <td><?=filesize($logs[$i])?></td>
                  <td><a href='?logfile=<?=$filename?>&action=view'><b>view</b></a></td>
                </tr>
                <?
            }
            ?>
            </table>
        </div>

    </div>

    <?
    if (isset($_REQUEST['tab']) and is_numeric($_REQUEST['tab'])) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: ".($_REQUEST['tab'] - 1)." });";
        echo "</script>";
    }
    ?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

</body>
</html>
