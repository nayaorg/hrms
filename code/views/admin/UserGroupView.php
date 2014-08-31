<div id="sbg-usrgrp-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="usrgrp-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#usrgrp-general">General</a></li>
			<li><a href="#usrgrp-admin">Admin</a></li>
			<li><a href="#usrgrp-hr">H/R</a></li>
			<li><a href="#usrgrp-payroll">Payroll</a></li>
			<span class="sbg-tab-title">User Group</span>
		</ul>
		<div id="usrgrp-general" style="height:150px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtUsrGrpId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtUsrGrpDesc" /><span id="usrgrp_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
			</table>
		</div>
		<div id="usrgrp-admin" style="height:150px;">
			<table>
				<tr style="height:28px">
					<td style="padding-left:10px;width:200px"><input id="chkAdmSet" type="checkbox" /><label for="chkAdmSet">System Setting</label></td>
					<td style="width:200px;"></td>
					<td style="width:200px"></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkAdmCoy" type="checkbox" /><label for="chkAdmCoy">Company Profile</label></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkAdmUser" type="checkbox" /><label for="chkAdmUser">User Management</label></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkAdmGroup" type="checkbox" /><label for="chkAdmGroup">User Group</label></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkAdmReset" type="checkbox" /><label for="chkAdmReset">Reset Password</label></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div id="usrgrp-hr" style="height:150px;">
			<table>
				<tr style="height:28px">
					<td style="padding-left:10px;width:200px"><input id="chkHrEmp" type="checkbox" /><label for="chkHrEmp">Employee Profile</label></td>
					<td style="padding-left:10px;width:200px"><input id="chkHrRace" type="checkbox" /><label for="chkHrRace">Race</label></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkHrDept" type="checkbox" /><label for="chkHrDept">Department</label></td>
					<td style="padding-left:10px"><input id="chkHrPermit" type="checkbox" /><label for="chkHrPermit">Work Permit</label></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkHrType" type="checkbox" /><label for="chkHrType">Employee Type</label></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkHrJob" type="checkbox" /><label for="chkHrJob">Job Title</label></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkHrNat" type="checkbox" /><label for="chkHrNat">Nationality</label></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div id="usrgrp-payroll" style="height:150px;">
			<table>
				<tr style="height:28px">
					<td style="padding-left:10px;width:200px"><input id="chkPayBank" type="checkbox" /><label for="chkPayBank">Bank</label></td>
					<td style="padding-left:10px;width:200px"><input id="chkPayEntry" type="checkbox" /><label for="chkPayEntry">Pay Entry</label></td>
					<td style="padding-left:10px;width:200px"><input id="chkIncomeYear" type="checkbox" /><label for="chkIncomeYear">Yearly Income Report</label></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkPayCpf" type="checkbox" /><label for="chkPayCpf">CPF Type</label></td>
					<td style="padding-left:10px"><input id="chkPayList" type="checkbox" /><label for="chkPayList">Pay Listing</label></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkPayType" type="checkbox" /><label for="chkPayType">Pay Type</label></td>
					<td style="padding-left:10px"><input id="chkPaySlip" type="checkbox" /><label for="chkPaySlip">Print Pay Slip</label></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkPayEmp" type="checkbox" /><label for="chkPayEmp">Employee Pay Setup</label></td>
					<td style="padding-left:10px"><input id="chkCpfList" type="checkbox" /><label for="chkCpfList">CPF Listing</label></td>
					<td></td>
				</tr>
				<tr style="height:28px">
					<td style="padding-left:10px"><input id="chkPayCreate" type="checkbox" /><label for="chkPayCreate">Create Pay Slip</label></td>
					<td style="padding-left:10px"><input id="chkCpfEntry" type="checkbox" /><label for="chkCpfCreate">CPF Entry</label></td>
					<td></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="usrgrp_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnUsrGrpAdd" type="button" value="Add"></input>
			<input id="btnUsrGrpClear" type="button" value="Clear"></input>
			<input id="btnUsrGrpUpdate" type="button" value="Update"></input>
			<input id="btnUsrGrpPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-usrgrp-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-usrgrp-data" style="overflow: auto;">
            <table id="sbg-usrgrp-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var usrgrp_url = "<?php echo Util::convertLink("UserGroup") ; ?>" ;
<?php include (PATH_CODE . "js/admin/usergroup.min.js") ; ?>
</script>