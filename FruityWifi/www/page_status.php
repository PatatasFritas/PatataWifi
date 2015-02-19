<?
include_once "config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include_once WWWPATH."/includes/menu.php";
?>

<meta name="viewport" content="initial-scale=0.50, width=device-width" />
<br/>

<div class="rounded-top" align="center"> Services </div>
<div class="rounded-bottom">
<?
//! Wireless

//$iswlanup = exec("/sbin/ifconfig wlan0 | grep UP | awk '{print $1}'");

if ($ap_mode == "1") {
    $iswlanup = exec("ps auxww | grep hostapd | grep -v -e grep");
} elseif ($ap_mode == "2") {
    $iswlanup = exec("ps auxww | grep airbase | grep -v -e grep");
} elseif ($ap_mode == "3") {
    $iswlanup = exec("ps auxww | grep hostapd | grep -v -e grep");
} elseif ($ap_mode == "4") {
    $iswlanup = exec("ps auxww | grep hostapd | grep -v -e grep");
}

$hostapd_modes = array(1 => "Hostapd", 3 => "Hostapd-Mana", 4 => "Hostapd-Karma", 2 => "Airbase-ng");

echo "<div style='text-align:left;'>";
//echo "<div style='border:0px; display:inline-block; width:84px; text-align:right;'>Wireless</div> ";
echo "<div style='border:0px; display:inline-block; width:170px; text-align:right;'>Wireless (".$hostapd_modes[$ap_mode].")</div> ";
if ($iswlanup != "") {
?>
    <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:lime;'>enabled.</div>
    <div style='border:0px; display:inline-block;'>|</div>
    <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'>
        <a href='scripts/status_wireless.php?service=wireless&action=stop' class='div-a'>stop</a>
    </div>
<?
} else {
?>
    <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:red;'>disabled.</div>
    <div style='border:0px; display:inline-block;'>|</div>
    <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'>
        <a href='scripts/status_wireless.php?service=wireless&action=start' class='div-a'>start</a>
    </div>
<?
}
echo "</div>";

//! dnsmasq

echo "<div style='text-align:left;'>";
echo "<div style='border:0px; display:inline-block; width:170px; text-align:right;'>dnsmasq</div> ";
$isdnsmasqup = exec("ps auxww | grep \"/dnsmasq\" | grep -v -e grep");
if ($isdnsmasqup != "") {
?>
    <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:lime;'>enabled.</div>
    <div style='border:0px; display:inline-block;'>|</div>
    <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'></div>
<?
} else {
?>
    <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:red;'>disabled.</div>
    <div style='border:0px; display:inline-block;'>|</div>
    <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'></div>
<?
}
echo "</div>";

//! Services

exec("find ./modules -name '_info_.php' | sort", $output);
if (count($output) > 0) {

    for ($i=0; $i < count($output); $i++) {
        echo "<div style='text-align:left;'>";

            $mod_panel = "";
            $mod_alias = "";
            $mod_type = "";
            if ($output[$i] != "") {
                include $output[$i];
                $module_path = str_replace("_info_.php","",$output[$i]);

                if ($mod_panel == "show" and $mod_type == "service") {

                    if ($mod_alias != "") {
                        echo "<div style='border:0px; display:inline-block; width:170px; text-align:right;'>$mod_alias</div> ";
                    } else {
                        echo "<div style='border:0px; display:inline-block; width:170px; text-align:right;'>$mod_name</div> ";
                    }
                    if($mod_installed=="0") {
                    ?>
                        <div style='border:0px; display:inline-block; width:63px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;width:36px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;font-weight:bold; width:36px;'>
                            <a href='modules/<?=$mod_name?>/includes/module_action.php?install=install_<?=$mod_name?>'>install</a>
                        </div>
                    <?
                    } elseif($mod_isup != "") {
                        $isModuleUp = exec($mod_isup);
                        if ($isModuleUp != "") {
                        ?>
                            <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:lime;'>enabled.</div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'>
                                <a href='modules/<?=$mod_name?>/includes/module_action.php?service=<?=$mod_name?>&action=stop&page=status'>stop</a>
                            </div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px;'>
                                <a href='modules/<?=$mod_name?>/'>view</a>
                            </div>

                        <?
                        } else {
                        ?>
                            <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:red;'>disabled.</div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'>
                                <a href='modules/<?=$mod_name?>/includes/module_action.php?service=<?=$mod_name?>&action=start&page=status'>start</a>
                            </div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px;'>
                                <a href='modules/<?=$mod_name?>/'>edit</a>
                            </div>
                        <?
                        }

                    } else {
                    ?>
                        <div style='border:0px; display:inline-block; width:63px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;width:36px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;font-weight:bold; width:36px;'>
                            <a href='modules/<?=$mod_name?>/'>edit</a>
                        </div>
                    <?
                    }
                }
                $mod_installed[$i] = $mod_name;
            }
        echo "</div>";
    }


}
?>
</div>

<br/>
<?
// ------------- External Modules --------------
//exec("find ./modules -name '_info_.php' | sort",$output); // replaced with previous output array

//print count($output);
//if (count($output) > 0) {
?>
<div class="rounded-top" align="center"> Modules </div>
<div class="rounded-bottom">

    <?
    if (count($output) > 0) {

        for ($i=0; $i < count($output); $i++) {
        echo "<div style='text-align:left;'>";

            $mod_panel = "";
            $mod_alias = "";
            $mod_type = "";
            if ($output[$i] != "") {
                include $output[$i];
                $module_path = str_replace("_info_.php","",$output[$i]);

                if ($mod_panel == "show" and $mod_type != "service") {

                    if ($mod_alias != "") {
                        echo "<div style='border:0px; display:inline-block; width:84px; text-align:right;'>$mod_alias</div> ";
                    } else {
                        echo "<div style='border:0px; display:inline-block; width:84px; text-align:right;'>$mod_name</div> ";
                    }

                    if($mod_installed=="0") {
                    ?>
                        <div style='border:0px; display:inline-block; width:63px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;width:36px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;font-weight:bold; width:36px;'>
                            <a href='modules/<?=$mod_name?>/includes/module_action.php?install=install_<?=$mod_name?>'>install</a>
                        </div>
                    <?
                    } elseif($mod_isup != "") {
                        $isModuleUp = exec($mod_isup);
                        if ($isModuleUp != "") {
                        ?>
                            <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:lime;'>enabled.</div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'>
                                <a href='modules/<?=$mod_name?>/includes/module_action.php?service=<?=$mod_name?>&action=stop&page=status'>stop</a>
                            </div>

                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px;'>
                                <a href='modules/<?=$mod_name?>/'>view</a>
                            </div>
                        <?
                        } else {
                        ?>
                            <div style='border:0px; display:inline-block; width:63px; font-weight:bold; color:red;'>disabled.</div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px; visibility:visible;'>
                                <a href='modules/<?=$mod_name?>/includes/module_action.php?service=<?=$mod_name?>&action=start&page=status'>start</a>
                            </div>
                            <div style='border:0px; display:inline-block;'>|</div>
                            <div style='display:inline-block;font-weight:bold; width:36px;'>
                                <a href='modules/<?=$mod_name?>/'>edit</a>
                            </div>
                        <?
                        }
                    } else {
                    ?>
                        <div style='border:0px; display:inline-block; width:63px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;width:36px;'></div>
                        <div style='border:0px; display:inline-block;'>|</div>
                        <div style='display:inline-block;font-weight:bold; width:36px;'>
                            <a href='modules/<?=$mod_name?>/'>edit</a>
                        </div>
                    <?
                    }

                }
                $mod_installed[$i] = $mod_name;
            }
            echo "</div>";
        }
    } else {
        echo "<div>No modules have been installed.<br/>Install them from the <a href='page_modules.php'><b>Available Modules</b></a> list.</div>";
    }
    ?>

</div>

<br/>

<div class="rounded-top" align="center"> Interfaces/IP </div>
<div class="rounded-bottom">
    <?

    $ifaces = exec("/sbin/ifconfig -a | cut -c 1-8 | sort | uniq -u |grep -v lo|sed ':a;N;$!ba;s/\\n/|/g'");
    $ifaces = str_replace(" ","",$ifaces);
    $ifaces = explode("|", $ifaces);

    for ($i = 0; $i < count($ifaces); $i++) {
        if (strpos($ifaces[$i], "mon") === false) {
            echo $ifaces[$i] . ": ";
            $ip = exec("/sbin/ifconfig $ifaces[$i] | grep 'inet addr:' | cut -d: -f2 |awk '{print $1}'");
            echo $ip . "<br/>";
        }
    }

    if (isset($_GET['reveal_public_ip']) and $_GET['reveal_public_ip'] == 1) {
        echo "public: " . exec("curl ident.me");
    } else {
        echo "public: <a href='page_status.php?reveal_public_ip=1'>reveal ip</a>";
    }

    ?>
</div>

<br/>

<div class="rounded-top" align="center"> Stations </div>
<div class="rounded-bottom" style="width:500px">
    <?
    $filename = LOGPATH."/dhcp.leases";
    if (file_exists($filename) and 0 < filesize($filename) ) {
        $rs = fopen($filename, "r");
        //$data = file_get_contents($filename);
        //$stations2 = null;
        while (($line = fgets($rs, 4096)) !== false) {
            if(substr_count($line, " ")>=3) {
                list($time, $mac, $ip, $host) = explode(" ", $line);
                $stationmac = preg_replace("/.*(.{2}):(.{2}):(.{2}):(.{2}):(.{2}):(.{2}).*/", '\1\2\3\4\5\6', $mac);
                $stations2[$stationmac]['mac'] = $mac;
                $stations2[$stationmac]['ip'] = $ip;
                $stations2[$stationmac]['host'] = $host;
            }
        }
        fclose($rs);
    }

    exec("/sbin/iw dev $io_in_iface station dump |grep Stat", $stations);
    for ($i=0; $i < count($stations); $i++) {
        $station = trim(str_replace("Station", "", $stations[$i]));
        $stationmac = preg_replace("/.*(.{2}):(.{2}):(.{2}):(.{2}):(.{2}):(.{2}).*/", '\1\2\3\4\5\6', $station);
        if(isset($stations2[$stationmac])) {
            echo $stations2[$stationmac]['mac']." | ".$stations2[$stationmac]['ip']." | ".$stations2[$stationmac]['host'];
        } else {
            echo $station;
        }

        // Karma Kick Ban
        if($ap_mode==3 or $ap_mode==4) {
            echo "
            <a href=\"scripts/status_wireless.php?service=wireless&action=kick&station=".$stationmac."\">!k</a>
            <a href=\"scripts/status_wireless.php?service=wireless&action=ban&station=".$stationmac."\">!b</a>";
        }
        echo "<br/>\n";
    }

    ?>
</div>

<br/>

<div class="rounded-top" align="center"> DHCP </div>
<div class="rounded-bottom">
    <?
    $filename = LOGPATH."/dhcp.leases";

    if (file_exists($filename) and 0 < filesize($filename) ) {
        $data = file_get_contents($filename);
        $data = explode("\n",$data);
    ?>
    <table border="1" style="border-collapse:collapse" width="100%">
    <tr>
    <th>IP</th>
    <th>MAC</th>
    <th>Hostname</th>
    </tr>
    <?

        for ($i=0; $i < count($data); $i++) {
            if(substr_count($data[$i], " ")>=3) {
            $tmp = explode(" ", $data[$i]);
                //echo $tmp[2] . " " . $tmp[1] . " " . $tmp[3] . "<br/>";
                echo "<tr>";
                echo "<td>", $tmp[2], "</td>";
                $if_online = false;
                foreach ($stations as $station) {
                    if (strpos($station, $tmp[1])!==false)
                        $if_online = true;
                }
                if ($if_online)
                    echo "<td><font color=\"blue\"><b>", $tmp[1], "</b></font></td>";
                else
                    echo "<td>", $tmp[1], "</td>";
                echo "<td>", $tmp[3], "</td>";
                echo "</tr>";
            }
        }
    }

    ?>
</div>