<?
$caSetup = false;
include_once("./include/common.php");
@include_once("./config/configuration.php");
if (!$caSetup) {
	printHeader('First time CA setup');
	if(!isset($_REQUEST['stage'])) $_REQUEST['stage']="";

	switch ($_REQUEST['stage']) {
		case "":
			include_once("./modules/setup/intro.php");
			break;
		case "create":
			include_once("./modules/setup/create.php");
			break;
		default:
			print "Unknown setup option: " . htmlspecialchars($_REQUEST['stage']);
			break;
	}

	printFooter();
	exit();
}

switch ($_REQUEST['area']) {
	case "main":
	case "":
		switch ($_REQUEST['stage']) {
			case "":
				printHeader("{$config['orgName']} Certificate Authority");
				include_once("./modules/main/welcome.php");
				printFooter();
				break;

			case "about":
				printHeader("About PHP-CA");
				include_once("./modules/main/about.php");
				printFooter();
				break;

			case "help":
				printHeader("PHP-CA Help");
				include_once("./modules/main/help.php");
				printFooter();
				break;

			case "trust":
				include_once("./modules/main/trust.php");
				break;

                        case "getCert":
                                include_once("./modules/main/getCert.php");
                                break;

                       	case "getKey":
                                include_once("./modules/main/getKey.php");
                                break;
			default:
				printHeader("Certificate application and issue");
				print "Unknown application option: " . htmlspecialchars($_REQUEST['stage']);
				printFooter();
				break;
		}
		break;

	case "csr":
	        switch ($_REQUEST['stage']) {
	                case "":
				printHeader('Certificate Signing Request');
	                        include_once("./modules/csr/options.php");
				printFooter();
                        break;
	                case "create":
				printHeader('Certificate Signing Request');
	                        include_once("./modules/csr/createCSR.php");
				printFooter();
			break;
	                default:
                               	printHeader("Certificate application and issue");
                               	print "Unknown application option: " . htmlspecialchars($_REQUEST['stage']);
                               	printFooter();
                        break;
	        }
		break;

	case "apply":
		switch ($_REQUEST['stage']) {
			case "":
				printHeader("Certificate application and issue");
				include_once("./modules/apply/emailConfirm.php");
				printFooter();
				break;
				
			case "enterKey":
				printHeader("Certificate application and issue");
				include_once("./modules/apply/enterKey.php");
				printFooter();
				break;
				
			case "issueCert":
				include_once("./modules/apply/issueCert.php");
				break;
				
			case "signCert":
				include_once("./modules/apply/signCert.php");
				break;
				
			case "fetchSpkac":
				include_once("./modules/apply/fetchSpkac.php");
				break;
				
			case "fetchPem":
				include_once("./modules/apply/fetchPem.php");
				break;
				
			default:
				printHeader("Certificate application and issue");
				print "Unknown application option: " . htmlspecialchars($_REQUEST['stage']);
				printFooter();
				break;
		}
		break;

	case 'admin':
		switch ($_REQUEST['stage']) {
			case "":
				printHeader('CA Administration');
				include_once("./modules/admin/options.php");
				printFooter();
			break;

			case "renewCert":
				printHeader('CA Certification Reissue');
				include_once("./modules/admin/renewCert.php");
				printFooter();
			break;

			case "certSign":
				printHeader('Certificate Signing');
				include_once("./modules/admin/certSign.php");
				printFooter();
			break;

			default:
				printHeader("Authority administration");
				print "Unknown administration option: " . htmlspecialchars($_REQUEST['stage']);
				printFooter();
			break;
		}
		break;
	
	default:
		printHeader("Unknown area");
		print "Unknown area: " . htmlspecialchars($_REQUEST['area']);
		printFooter();
		break;
}


?>
