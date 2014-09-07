<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head><title>SBG Human Resource Management System Configuration</title>
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Expires' content='Sat, 01 Jan 2000 06:00:00 GMT'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate, max-age=0'>
<link type="text/css" href="css/redmond/jquery-ui.css" rel="stylesheet" />
<link type="text/css" href="css/sbg-hrms.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="js/sbg-util.js"></script>
</head>
<body>
<form id="sbg-config" name="sbg-config" enctype="multipart/form-data" method="post" action="">
<div>
<div id="sbg-config-entry" style="width:610px;margin:0 auto">
	<div style="font-size: 20px;margin-top:10px;margin-bottom:10px">SBG Human Resource Management System Configuration</div>
	<div id="config-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#config-db">Database</a></li>
			<span class="sbg-tab-title"></span>
		</ul>
		<div id="config-db" style="height:180px;">
			<table>
				<tr>
					<td style="text-align:right;width:150px:"><span>Server : </span></td>
					<td style="width:450px"><input type="text" maxlength="50" size="50" id="txtConfigServer" value="<?php echo $this->getServer() ;?>" />
					<span id="config_err_server" style="color:Red;display:none">*</span></td>
					
				</tr>
				<tr>
					<td style="text-align:right;"><span>Database Type : </span></td>
					<td><select id="cobConfigDbType"><?php echo $this->getTypeList() ; ?></select></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Database Name : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtConfigDbName" value="<?php echo $this->getDbName() ;?>" />
					<span id="config_err_name" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Port No : </span></td>
					<td><input type="text" maxlength="5" size="10" id="txtConfigDbPort" value="<?php echo $this->getDbPort() ;?>" />
					<span>(blank for default port no)</span>
					<span id="config_err_port" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>User Name : </span></td>
					<td><input type="text" maxlength="20" size="15" id="txtConfigDbUser" value="<?php echo $this->getDbUser() ;?>" />
					<span id="config_err_user" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Password : </span></td>
					<td><input type="password" maxlength="15" size="15" id="txtConfigDbPwd" value="<?php echo $this->getDbPwd() ;?>" />
					<span id="config_err_pwd" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:right">
						<input id="btnConfigTest" type="button" value="Test Connection"></input>
						<input id="btnConfigCreate" type="button" value="Create"></input>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="config_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnConfigSave" type="button" value="Save"></input>
		</div>
	</div>
</div>
</div>
<div id="sbg-progress" style="display:none">
	<div class="sbg-progress"></div>
	<div class="sbg-progress-box ui-corner-all">
		<div style="font-size:1.5em">
			<img src="image/progress.gif"></img>&nbsp;&nbsp;Please wait .....
		</div>
		<div id="sbg-progress-mesg" class="sbg-progress-mesg"></div>
	</div>
</div>
</form>
<script type="text/javascript">
<?php include (PATH_CODE . "js/admin/config.min.js") ; ?>
</script>
</body>
</html>