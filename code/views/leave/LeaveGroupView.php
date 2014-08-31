<div id="sbg-leavegroup-entry" style="margin: 5px 5px 5px 5px;width:610px;">
	<div id="leavegroup-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#leavegroup-general">General</a></li>
			<li><a href="#leavegroup-annual">Annual Leave</a></li>
			<li><a href="#leavegroup-sick">Sick Leave</a></li>
			<li><a href="#leavegroup-other">Other Leave</a></li>
			<span class="sbg-tab-title">Leave Group Master</span>
		</ul>
		<div id="leavegroup-general" style="height:180px">
			<table>
				<tr>
					<td style="text-align:right;width:130px;"><span>ID : </span></td>
					<td style="width:470px"><input type="text" maxlength="10" size="10" id="txtLeaveGroupId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtLeaveGroupDesc" /><span id="leavegroup_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref : </span></td>
					<td><input type="text" maxlength="20" size="25" id="txtLeaveGroupRef" /></td>
				</tr>
			</table>
		</div>
		<div id="leavegroup-annual" style="height:180px">
			<table>
				<tr>
					<td style="width:400px" colspan="4">Leave Type :<select id="cobLeaveGroupAnnual"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td style="width:100px">Year of Service</td>
					<td style="width:100px">Days Entitle</td>
					<td style="width:100px">Year of Service</td>
					<td style="width:100px">Days Entitle</td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupAnnualLen1" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay1" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualLen6" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay6" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupAnnualLen2" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay2" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualLen7" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay7" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupAnnualLen3" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay3" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualLen8" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay8" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupAnnualLen4" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay4" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualLen9" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay9" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupAnnualLen5" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay5" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualLen10" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupAnnualDay10" type="text" maxlength="10" size="15"></input></td>
				</tr>
			</table>
		</div>
		<div id="leavegroup-sick" style="height:180px">
			<table>
				<tr>
					<td style="width:400px" colspan="4">Leave Type :<select id="cobLeaveGroupSick"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td style="width:100px">Year of Service</td>
					<td style="width:100px">Days Entitle</td>
					<td style="width:100px">Year of Service</td>
					<td style="width:100px">Days Entitle</td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupSickLen1" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay1" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickLen6" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay6" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupSickLen2" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay2" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickLen7" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay7" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupSickLen3" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay3" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickLen8" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay8" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupSickLen4" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay4" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickLen9" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay9" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><input id="txtLeaveGroupSickLen5" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay5" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickLen10" type="text" maxlength="10" size="15"></input></td>
					<td><input id="txtLeaveGroupSickDay10" type="text" maxlength="10" size="15"></input></td>
				</tr>
			</table>
		</div>
		<div id="leavegroup-other" style="height:180px">
			<table>
				<tr>
					<td style="width:400px"><select id="cobLeaveGroupOther1"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td><select id="cobLeaveGroupOther2"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td><select id="cobLeaveGroupOther3"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td><select id="cobLeaveGroupOther4"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td><select id="cobLeaveGroupOther5"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
				<tr>
					<td><select id="cobLeaveGroupOther6"><?php echo $this->getLeaveType() ; ?></select></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="leavegroup_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnLeaveGroupAdd" type="button" value="Add"></input>
			<input id="btnLeaveGroupClear" type="button" value="Clear"></input>
			<input id="btnLeaveGroupUpdate" type="button" value="Update"></input>
			<input id="btnLeaveGroupPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-leavegroup-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 200px;">Description</td>
				<td style="width: 100px;">Ref</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-leavegroup-data" style="overflow: auto;">
            <table id="sbg-leavegroup-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:200px"></td>
				<td style="width:100px"></td>
				<td style="width:25px"></td>
				<td style="width:25px"></td>
			</tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var leavegroup_url = "<?php echo Util::convertLink("LeaveGroup") ; ?>" ;
<?php include (PATH_CODE . "js/leave/leavegroup.min.js") ; ?>
</script>