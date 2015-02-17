<?
include_once "config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";


if (isset($_POST['action'])) {
    if ($_POST['action'] == "halt") {
        exec_fruitywifi('halt');
        echo "Poweroff...\n";
    } elseif ($_POST['action'] == "reboot") {
        exec_fruitywifi('reboot');
        echo "Rebooting...\n";
    } elseif ($_POST['action'] == "apt-update") {
        exec_fruitywifi('apt-get update > '.LOGPATH.'/install.txt &');
        header('Location: '.WEBPATH.'/page_system.php?log');
    } elseif ($_POST['action'] == "apt-upgrade") {
        exec_fruitywifi('apt-get upgrade -y >'.LOGPATH.'/install.txt &');
        header('Location: '.WEBPATH.'/page_system.php?log');
    }


    exit;
}

if(isset($_REQUEST['log'])) {
?>
<m-eta http-equiv="refresh" content="1; url=page_system.php?action=log">
<script src="<?=WEBPATH?>/js/jquery.js"></script>
<script src="<?=WEBPATH?>/js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?=WEBPATH?>/css/jquery-ui.css" />
<link rel="stylesheet" href="<?=WEBPATH?>/css/style.css" />

<div class="rounded-top" align="left"> &nbsp; <b>...</b> </div>
<div id="log" class="module-content" style="font-family: courier; overflow: auto; height:500px;"></div>

<script>
function getLogs(service, path) {
    var refInterval = setInterval(function() {
	$.ajax({
	    type: 'POST',
	    url: 'scripts/status_logs_install.php',
	    //data: $(this).serialize(),
	    data: 'service='+service+'&path='+path,
	    dataType: 'json',
	    success: function (data) {
		//console.log(data);
		$('#log').html('');
		$.each(data, function (index, value) {
		    $("#log").append( value ).append("<br>");
		    if (value == "..DONE..") {
    			setTimeout(function() {
    			    window.location = "./page_system.php?done";
    			}, 2000);
		    }
		});
	    }
	});
    },2000);
}
getLogs("", "")
</script>

<?
    exit;
}
?>


<!DOCTYPE html>
<link href="css/style.css" rel="stylesheet" type="text/css">
<? include_once WWWPATH."/includes/menu.php"; ?>
<m-eta name="viewport" content="initial-scale=1.0, width=device-width" />

<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>

<br/>

<div class="rounded-top" align="center"> ifconfig </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <pre><? passthru('ifconfig -a'); ?></pre>
</div>


<div class="rounded-top" align="center"> iwconfig </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <pre><? passthru('iwconfig'); ?></pre>
</div>


<div class="rounded-top" align="center"> Uptime </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <pre><? passthru('uptime'); ?></pre>
</div>

<div class="rounded-top" align="center"> Disk usage </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <pre><? passthru('df -h'); ?></pre>
</div>

<div class="rounded-top" align="center"> Memory usage </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <pre><? passthru('free -m'); ?></pre>
</div>

<div class="rounded-top" align="center"> USB devices </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <pre><? passthru('lsusb'); ?></pre>
</div>

<div class="rounded-top" align="center"> System </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">
    <table cellpadding="1" cellspacing="1">
        <tr>
    	<td>
    	    <form action="page_system.php" method="post">
                <input type="hidden" name="action" value="halt" />
                <input type="submit" value="Poweroff" />
    	    </form>
    	</td>
        </tr>
        <tr>
    	<td>
    	    <form action="page_system.php" method="post">
                <input type="hidden" name="action" value="reboot" />
                <input type="submit" value="Reboot" />
    	    </form>
    	</td>
        </tr>
        <tr>
    	<td>
    	    <form action="page_system.php" method="post">
                <input type="hidden" name="action" value="apt-update" />
                <input type="submit" value="apt-get update" />
    	    </form>
    	</td>
        </tr>
        <tr>
    	<td>
    	    <form action="page_system.php" method="post">
                <input type="hidden" name="action" value="apt-upgrade" />
                <input type="submit" value="apt-get upgrade" />
    	    </form>
    	</td>
        </tr>
    </table>
</div>