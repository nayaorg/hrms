<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head><title>SBG Human Resource Management System</title>
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Expires' content='Sat, 01 Jan 2000 06:00:00 GMT'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate, max-age=0'>
<link type="text/css" href="css/redmond/jquery-ui.css" rel="Stylesheet" />
<link type="text/css" href="css/sbg-hrms.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="js/sbg-util.min.js"></script>
<script type="text/javascript" src="js/sbg-upload.js"></script>
</head>
<body>
<form id="sbg-main" name="sbg-main" enctype="multipart/form-data" method="post" action="">
<div id="sbg-top-panel" class="sbg-top-panel">
	<div class="sbg-logo">
		<img src="image/logo.png" alt="" style="width:200px;height:100px;"></img>
	</div>
	<div class="sbg-header">
		<div class="company" style="font-size: 2.0em"><?php echo $this->getOrganizationName() ; ?>
		</div>
		<div class="date" style="text-align:right">
			<div id="dte"></div>
			<div style="margin-top:5px"><?php echo $this->getFullName() ;?></div>
		</div>
	</div>
	<div class="sbg-menu-top" id="sbg-menu-top">
		<div id="mainmenu" class="mainmenu">
			<?php echo $this->getTopMenu() ; ?>
		</div>
		<div id="sidemenu" class="sidemenu">
			<?php echo $this->getSideMenu() ; ?>
		</div>
	</div>
</div>
<div id="sbg-left-panel" class="sbg-left-panel">
	<div id="sbg-menu-box" class="sbg-menu-box">
		<?php echo $this->getMenuItem() ; ?>
	</div>
</div>

<div id="sbg-center-panel" class="ui-widget-content sbg-center-panel"></div>
<div id="sbg-progress" style="display:none">
	<div class="sbg-progress"></div>
	<div class="sbg-progress-box ui-corner-all">
		<div style="font-size:1.5em">
			<img src="image/progress.gif"></img>&nbsp;&nbsp;Please wait .....
		</div>
		<div id="sbg-progress-mesg" class="sbg-progress-mesg"></div>
	</div>
</div>
<div id="sbg-dialog" class="sbg-dialog" title="System Message"><div id="sbg-dialog-mesg" class="sbg-dialog-mesg"></div></div>
<div id="sbg-tips" class="sbg-tips ui-state-default ui-corner-all"></div>
<div id="sbg-confirm" class="sbg-dialog" title="System Message"><div id="sbg-confirm-mesg" class="sbg-dialog-mesg"></div></div>
<div id="sbg-popup-box" class="ui-widget-content ui-corner-all sbg-popup-box"></div>
</form>
<script type="text/javascript">
var login_url = "<?php echo Util::convertLink("Login") ; ?>" ;
<?php include (PATH_CODE . "js/general/main.min.js") ; ?>
</script>
 
</body>
</html>
