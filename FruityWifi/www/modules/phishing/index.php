<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

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

$logfile = @$_GET["logfile"];
$action = @$_GET["action"];

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

<div class="rounded-top" align="left"> &nbsp; <b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;version <?=$mod_version?><br>
    <?

    $ismoduleup = exec_fruitywifi($mod_isup);
    if (isset($ismoduleup[0]) and $ismoduleup[0] != "") {
        echo "&nbsp;$mod_alias  <font color='lime'><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=$mod_name&action=stop&page=module'><b>stop</b></a>";
    } else {
        echo "&nbsp;$mod_alias  <font color='red'><b>disabled</b></font>. | <a href='includes/module_action.php?service=$mod_name&action=start&page=module'><b>start</b></a>";
    }
    ?>

</div>

<br>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">


    <div id="result" class="module">
        <ul>
            <li><a href="#result-1">Output</a></li>
            <li><a href="#result-2">Users</a></li>
            <li><a href="#result-3">History</a></li>
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
            <br>
            <div class="module-options" s-tyle="background-color:#000; border:1px dashed; padding:5px">
            <?

            $filename = $file_users;
            $data = open_file($filename);
            $output = explode("\n", $data);

            ?>

            <table border='0'>
            <tr>
                <td></td>
                <td style="padding-right:10px"><b>Date</b></td>
                <td style="padding-right:10px"><b>Host</b></td>
                <td style="padding-right:10px"><b>User</b></td>
                <td style="padding-right:10px"><b>Pass</b></td>
            </tr>
            <?
            //for ($i=0; $i < count($output); $i++) {
            foreach ($output as $key=>$line) {
                if(substr_count($line, "--")<3) continue;
                $row = explode("--", $line);
                if ($row[1] != "") {
            ?>
                <tr>
                    <td style="padding-right:5px"><a href="includes/module_action.php?service=users&action=delete&id_data=<?=($key + 1)?>" style="color:#000">Delete</a></td>
                    <td style="background-color:#DDD; padding-right:10px"><?=$row[0];?></td>
                    <td style="background-color:#DDD; padding-right:10px"><?=$row[1];?></td>
                    <td style="background-color:#DDD; padding-right:10px"><?=$row[2];?></td>
                    <td style="background-color:#DDD; padding-right:10px"><?=$row[3];?></td>
                </tr>
            <?  }
            }
            ?>
            </table>
            </div>

            <br>

        </div>

        <!-- HISTORY -->

        <div id="result-3" class="history">
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
        echo "$( '#result' ).tabs({ active: ".($_REQUEST['tab'])." });";
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
