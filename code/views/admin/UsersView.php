<div id="sbg-user-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="user-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#user-general">General</a></li>
			<span class="sbg-tab-title">User Maintenance</span>
		</ul>
		<div id="user-general">
			<table>
				<tr>
					<td style="text-align:right;width:140px"><span>User ID : </span></td>
					<td style="width:460px"><input type="text" maxlength="12" size="12" id="txtUserId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>User Name : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtUserName" /><span id="user_err_name" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Full Name : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtUserFull" /><span id="user_err_full" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>E-mail : </span></td>
					<td><input type="text" maxlength="80" size="60" id="txtUserEmail" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Group : </span></td>
					<td><select id="cobUserGroup"><?php echo $this->getUserGroup() ; ?></select>
						<span id="user_err_group" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Start Date : </span></td>
					<td><input type="text" value="" maxlength="10" size="12" id="txtUserStart" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Expiry Date : </span></td>
					<td><input type="text" maxlength="10" size="12" id="txtUserExpiry" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="user_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnUserAdd" type="button" value="Add"></input>
			<input id="btnUserClear" type="button" value="Clear"></input>
			<input id="btnUserUpdate" type="button" value="Update"></input>
			<input id="btnUserPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>
<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-user-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Name</td>
                <td style="width: 300px;">Full Name</td>
				<td style="width: 200px;">Group</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-user-data" style="overflow: auto;">
            <table id="sbg-user-table" cellspacing="0" cellpadding="5" class="data">
				<tr>
				<td style="width:50px;height:1px"></td>
				<td style="width:100px"></td>
				<td style="width:300px"></td>
				<td style="width:200px"></td>
				<td style="width:25px"></td>
				<td style="width:25px"></td>
				</tr>
				<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var user_url = "<?php echo Util::convertLink("Users") ; ?>" ;
<?php include (PATH_CODE . "js/admin/user.min.js") ; ?>
</script>