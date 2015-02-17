<?

//!Wireless
$opt_responder['S1'][0] = 1;
$opt_responder['S1'][1] = 1;
$opt_responder['S1'][2] = "Wireless";
$opt_responder['S1'][3] = "/scripts/status_wireless.php?service=wireless&action=start";

//!Karma
$opt_responder['S2'][0] = 1;
$opt_responder['S2'][1] = 2;
$opt_responder['S2'][2] = "Karma";
//$opt_responder['S2'][3] = "/scripts/status_karma.php?service=karma&action=start";
$opt_responder['S2'][3] = "/modules/karma/includes/module_action.php?service=karma&action=start";

//!Phishing
$opt_responder['S3'][0] = 0;
$opt_responder['S3'][1] = 3;
$opt_responder['S3'][2] = "Phishing";
$opt_responder['S3'][3] = "/scripts/status_phishing.php?service=phishing&action=start";

//! ngrep
$opt_responder['M1'][0] = 0;
$opt_responder['M1'][1] = 0;
$opt_responder['M1'][2] = "ngrep";
$opt_responder['M1'][3] = "/modules/ngrep/includes/module_action.php?service=ngrep&action=start&page=status";

//!SSLStrip
$opt_responder['M2'][0] = 1;
$opt_responder['M2'][1] = 0;
$opt_responder['M2'][2] = "SSLStrip";
$opt_responder['M2'][3] = "/modules/sslstrip/includes/module_action.php?service=sslstrip&action=start&page=status";

//!DNSSpoof
$opt_responder['M3'][0] = 0;
$opt_responder['M3'][1] = 0;
$opt_responder['M3'][2] = "DNSSpoof";
$opt_responder['M3'][3] = "/modules/dnsspoof/includes/module_action.php?service=dnsspoof&action=start&page=status";

//!MDK3
$opt_responder['M4'][0] = 0;
$opt_responder['M4'][1] = 0;
$opt_responder['M4'][2] = "MDK3";
$opt_responder['M4'][3] = "/modules/mdk3/includes/module_action.php?service=mdk3&action=start&page=status";

//!Squid3
$opt_responder['M5'][0] = 0;
$opt_responder['M5'][1] = 0;
$opt_responder['M5'][2] = "Squid3";
$opt_responder['M5'][3] = "/modules/squid3/includes/module_action.php?service=squid3&action=start&page=status";

//!Kismet
$opt_responder['M6'][0] = 0;
$opt_responder['M6'][1] = 0;
$opt_responder['M6'][2] = "Kismet";
$opt_responder['M6'][3] = "/modules/kismet/includes/module_action.php?service=kismet&action=start&page=status";

//!Captive
$opt_responder['M7'][0] = 0;
$opt_responder['M7'][1] = 0;
$opt_responder['M7'][2] = "Captive";
$opt_responder['M7'][3] = "/modules/captive/includes/module_action.php?service=captive&action=start&page=status";

//!URLSnarf
$opt_responder['M8'][0] = 1;
$opt_responder['M8'][1] = 0;
$opt_responder['M8'][2] = "URLSnarf";
$opt_responder['M8'][3] = "/modules/urlsnarf/includes/module_action.php?service=urlsnarf&action=start&page=status";

//!Responder
$opt_responder['M9'][0] = 0;
$opt_responder['M9'][1] = 0;
$opt_responder['M9'][2] = "Responder";
$opt_responder['M9'][3] = "/modules/responder/includes/module_action.php?service=responder&action=start&page=status";

//!Tcpdump
$opt_responder['M10'][0] = 0;
$opt_responder['M10'][1] = 0;
$opt_responder['M10'][2] = "Tcpdump";
$opt_responder['M10'][3] = "/modules/tcpdump/includes/module_action.php?service=responder&action=start&page=status";

//!Ettercap
$opt_responder['M11'][0] = 0;
$opt_responder['M11'][1] = 0;
$opt_responder['M11'][2] = "Ettercap";
$opt_responder['M11'][3] = "/modules/ettercap/includes/module_action.php?service=responder&action=start&page=status";

//!Autossh
$opt_responder['M12'][0] = 0;
$opt_responder['M12'][1] = 0;
$opt_responder['M12'][2] = "Autossh";
$opt_responder['M12'][3] = "/modules/autossh/includes/module_action.php?service=responder&action=start&page=status";

//!Whatsapp
$opt_responder['M13'][0] = 1;
$opt_responder['M13'][1] = 0;
$opt_responder['M13'][2] = "Whatsapp";
$opt_responder['M13'][3] = "/modules/whatsapp/includes/module_action.php?service=whatsapp&action=start&page=status";


?>