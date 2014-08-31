<?php
//header("Expires: Sat, 01 Jan 2000 06:00:00 GMT");    
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");    
//header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");    
//header("Cache-Control: post-check=0, pre-check=0", false);    
//header("Pragma: no-cache");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head><title>SBG HRMS Sigin</title>
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Expires' content='Sat, 01 Jan 2000 06:00:00 GMT'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate, max-age=0'>
<link type="text/css" href="css/redmond/jquery-ui.css" rel="Stylesheet" />
<link type="text/css" href="css/sbg-hrms.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="js/sbg-util.min.js"></script>
</head>
<body><form id="sbg-main" name="sbg-main" method="post" action="">
<div id="sbg-content" class="sbg-content" style="height:100%">
	<div class="sbg-login-header-bar">
		<div class="sbg-login-header sbg-login-content clearfix">
			<img class="logo" src="image/SBG-HRMS.png"></img>
		</div>
	</div>
	<div class="sbg-login-content clearfix" style="width:800px;height:400px;background-image:url(image/bg.jpg)">
		<div class="sbg-login ui-widget-content ui-corner-all">
			<div style="font-size:30px;margin-left:10px;margin-top:20px">Signin</div>
			<div style="padding-top:5px ;">
				<div class="sbg-login-label">User Name : </div>
				<div>
					<input name="txtName" type="text" maxlength="12" id="txtName" />
					<span id="err_name" style="color:Red;display:none">*</span>
				</div>
				<div class="sbg-login-label">Password : </div>
				<div>
					<input name="txtPwd" type="password" maxlength="10" id="txtPwd" />
					<span id="err_pwd" style="color:Red;display:none">*</span>
				</div>
				<div style="padding-top:10px;text-align:center">
					<input type="button" id="btnLogin" name="btnLogin" value="Login" />
				</div>
				<div id="err" style="color:red;padding: 10px 10px 10px 10px;">
					<span id="err_mesg"></span>
					<span id="err_summary"></span>
				</div>
			</div>
		</div>
		
	</div>
	<div class="sbg-login-footer-bar">
		<div class="sbg-login-footer sbg-login-content clearfix">
			<ul>
				<li>© <?php date('Y');?> Strategic Business Group Pte Ltd&nbsp;|&nbsp;</li>
				<li><a href="#">Terms &amp; Privacy</a></li>
				<li>&nbsp;|&nbsp;<a href="javascript:showReport('http://www.strategic.asia')">Contact Us</a></li>
			</ul>
		</div>
	</div>
</div>
<div id="sbg-pwd" title="Create Password">
	<div style="margin-left:10px;margin-top:10px;margin-bottom:3px">New Password</div>
	<div style="margin-left:10px"><input type="password" id="txtPwd1" maxlength="10" size="20" /></div>
	<div style="margin-left:10px;margin-top:5px;margin-bottom:3px">Confirm Password</div>
	<div style="margin-left:10px;"><input type="password" id="txtPwd2" maxlength="10" size="20" /></div>
</div>
</form>
<script type="text/javascript">
var login_url =  "<?php echo Util::convertLink("Login") ; ?>" ;
<?php include (PATH_CODE . "js/general/login.min.js") ; ?>
</script>
</body>
</html>
