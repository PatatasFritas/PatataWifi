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

<div class="rounded-top" align="left"> &nbsp; <b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;version <?=$mod_version?><br>
    <?
    if (file_exists("includes/whatsapp_discover") and fileperms("includes/whatsapp_discover") & 0x0040) {
        echo "$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
		echo "$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    }
    ?>

    <?
    $ismoduleup = exec("$mod_isup");
    if ($ismoduleup != "") {
        echo "$mod_alias  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=whatsapp&action=stop&page=module\"><b>stop</b></a>";
    } else {
        echo "$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=whatsapp&action=start&page=module\"><b>start</b></a>";
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
            <li><a href="#result-2">History</a></li>
        </ul>

        <!-- OUTPUT -->

        <div id="result-1">
            <form id="formLogs" name="formLogs" method="POST" autocomplete="off" action="index.php">
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
                $data_array = explode("\n", $data);
                $data = implode("\n",array_reverse($data_array));

            ?>

            <textarea id="output" class="module-content" readonly="readonly" style="height:400px;font-family: courier;"><?=htmlspecialchars($data)?></textarea>

            <input type="hidden" name="type" value="logs" />
            </form>

        </div>

        <!-- HISTORY -->

        <div id="result-2" class="history">
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

    <div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
        Loading...
    </div>

    <script>
    $('#formLogs').submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'includes/ajax.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                $('#output').html('');
                $.each(data, function (index, value) {
                    $("#output").append( value ).append("\n");
                });
                $('#loading').hide();
            }
        });
        $('#output').html('');
        $('#loading').show()
    });
    $('#loading').hide();
    </script>

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