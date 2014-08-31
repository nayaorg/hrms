<div class="ui-widget-content ui-corner_all" style="width:500px;margin: 5px 5px 5px 5px;">
	<div class="ui-widget-header" style="font-size:1.5em;height:30px;padding-left:10px">Change Password</div>
	<table>
		<tr style="height:30px;">
			<td style="width:150px;padding-left:10px"><span style="font-size:1.2em">User Name :</span></td>
			<td><span style="font-size:1.2em"><?php echo $this->getUserName() ; ?></span></td>
		</tr>
		<tr style="height:30px;">
			<td style="width:80px;padding-left:10px"><span style="font-size:1.2em">Full Name :</span></td>
			<td><span style="font-size:1.2em"><?php echo $this->getFullName() ; ?></span></td>
		</tr>
		<tr style="height:30px;">
			<td style="padding-left:10px;"><span style="font-size:1.2em">Old Password :</span></td>
			<td><input type="Password" id="txtChangePwdOld" maxlength="10" size="20"></input></td>
		</tr>
		<tr style="height:30px;">
			<td style="padding-left:10px;"><span style="font-size:1.2em">New Password :</span></td>
			<td><input type="Password" id="txtChangePwdNew" maxlength="10" size="20"></input></td>
		</tr>
		<tr style="height:30px;">
			<td style="padding-left:10px;"><span style="font-size:1.2em">Confirm Password :</span></td>
			<td><input type="Password" id="txtChangePwdConfirm" maxlength="10" size="20"></input></td>
		</tr>
	</table>
	
</div> 
<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;margin-left:5px;width:500px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="changepwd_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnChangePwd" type="button" value="Change"></input>
		</div>
	</div>
<script type="text/javascript">
var changepwd_url = "<?php echo Util::convertLink("ChangePwd") ; ?>" ;
<?php include (PATH_CODE . "js/general/changepwd.min.js") ; ?>
</script>