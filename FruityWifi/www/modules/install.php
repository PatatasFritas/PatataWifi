<!DOCTYPE html>
<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."includes/login_check.php";
require_once WWWPATH."includes/filter_getpost.php";
include_once WWWPATH."includes/functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["module"], "../msg.php", $regex_extra);
}

$module = $_GET['module'];
?>
<m-eta http-equiv="refresh" content="1; url=install.php?module=<?=$module?>">
<script src="<?=WEBPATH?>js/jquery.js"></script>
<script src="<?=WEBPATH?>js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?=WEBPATH?>css/jquery-ui.css" />
<link rel="stylesheet" href="<?=WEBPATH?>css/style.css" />

<div class="rounded-top" align="left"> &nbsp; <b>Installing...</b> </div>
<div id="log" class="module-content" style="font-family: courier; overflow: auto; height:500px;"></div>

<script>
function getLogs(service, path) {
    var refInterval = setInterval(function() {
	$.ajax({
	    type: 'POST',
	    url: '../scripts/status_logs_install.php',
	    //data: $(this).serialize(),
	    data: 'service='+service+'&path='+path,
	    dataType: 'json',
	    success: function (data) {
		console.log(data);
		$('#log').html('');
		$.each(data, function (index, value) {
		    $("#log").append( value ).append("<br>");
		    if (value == "..DONE..") {
			setTimeout(function() {
			    window.location = "./<?=$module?>";
			}, 2000);
		    }
		});
	    }
	});
    },2000);
}
getLogs("", "")
</script>