<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "includes/options_config.php";

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

$newdata = @$_POST['newdata'];
$logfile = @$_GET['logfile'];
$action = @$_GET['action'];
$tempname = @$_GET['tempname'];
$service = @$_POST['service'];

// DELETE LOG
if ($logfile != "" and $action == "delete") {
    exec_fruitywifi(BIN_RM." ".$mod_logs_history.$logfile.".log");
}




?>

<div class="rounded-top" align="left"> &nbsp; <b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;version <?=$mod_version?><br>
    <?
    if (file_exists( BIN_AUTOSSH )) {
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    }
    ?>

    <?
    $ismoduleup = exec("ps auxww | grep $mod_name | grep -v -e 'grep $mod_name'");
    if ($ismoduleup != "") {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=autossh&action=stop&page=module\"><b>stop</b></a>";
        echo "<input type='hidden' name='action' value='stop'>";
        echo "<input type='hidden' name='page' value='module'>";
    } else {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=autossh&action=start&page=module\"><b>start</b></a>";
        echo "<input type='hidden' name='action' value='start'>";
        echo "<input type='hidden' name='page' value='module'>";
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
            <li><a href="#result-1">Setup</a></li>
            <li><a href="#result-2">About</a></li>
        </ul>

        <!-- SETUP -->

        <div id="result-1">
            <form method="POST" autocomplete="off" action="includes/save.php">
                <div class="module-options">
                    <table>
                        <tr>
                            <td>User: </td>
                            <td><input name="autossh_user" value="<?=$autossh_user?>"></td>
                        </tr>
                        <tr>
                            <td>Host: </td>
                            <td><input name="autossh_host" value="<?=$autossh_host?>"></td>
                        </tr>
                        <tr>
                            <td>Port: </td>
                            <td><input name="autossh_port" value="<?=$autossh_port?>"></td>
                        </tr>
                        <tr>
                            <td>Listen: </td>
                            <td><input name="autossh_listen" value="<?=$autossh_listen?>"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" value="save">
                                <input name="type" type="hidden" value="settings">
                            </td>
                        </tr>
                    </table>
                </div>
            </form>

            <br>

	    <form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="includes/module_action.php">
            <div class="general" style="display:inline;">Public Key </div><input type="submit" value="generate">
            <br><br>
            <?
                if ($logfile != "" and $action == "view") {
                    $filename = $mod_logs_history.$logfile.".log";
                } else {
                    $filename = $mod_logs;
                }

		$filename = "./includes/id_rsa.pub";
                $data = open_file($filename);

            ?>
            <textarea id="output" class="module-content" style="font-family: courier; height: 100px"><?=htmlspecialchars($data)?></textarea>
            <input type="hidden" name="ssh_cert" value="gen_certificate">
            </form>

        </div>

        <!-- ABOUT -->

        <div id="result-2" class="history">
            <? include "includes/about.php"; ?>
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