<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
	<script type="text/javascript" src="../js/jquery-1.6.2.js"></script>
	<script type="text/javascript" src="scripts/expand.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
		    // --- first section initially expanded:
		    $("h2.expand").toggler({initShow: "div.collapse:first"});
		    $("#content").expandAll({trigger: "h2.expand", ref: "div.demo",  speed: 300, oneSwitch: false});
		});
	</script>
	<!--<![endif]-->
	<title>Webdelib check</title>
	<link rel="stylesheet" type="text/css" href="css/check.css" />
	<link rel="stylesheet" type="text/css" href="css/effect.css" />
	<div id="headerLogoContainer">
		<img alt="" src="../img/logoAsalae60.png">
	</div>
</head> 
<body>
