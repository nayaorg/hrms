<div id="sbg-timesheet-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="timesheet-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#timesheet-general">General</a></li>
			<span class="sbg-tab-title">Timesheet Master</span>
		</ul>
		<div id="timesheet-general">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtTSId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Emp ID : </span></td>
					<td><select id="cobTSEmp"><?php echo $this->getEmpList() ; ?></select>
						<span id="timesheet_err_project" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtTSDesc" /><span id="timesheet_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref No : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtTSRef" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Project : </span></td>
					<td><select id="cobTSProj"><?php echo $this->getProjectList() ; ?></select>
						<span id="timesheet_err_project" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Activity : </span></td>
					<td><select id="cobTSAct"><?php echo $this->getActivityList() ; ?></select>
						<span id="timesheet_err_activity" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Start Date : </span></td>
					<td><input type="text" value="" maxlength="10" size="12" id="txtTSStart" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>End Date : </span></td>
					<td><input type="text" maxlength="10" size="12" id="txtTSExpiry" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Billable : </span></td>
					<td><input type="checkbox" value="" id="cbBillable">
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="timesheet_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnTSAdd" type="button" value="Add"></input>
			<input id="btnTSClear" type="button" value="Clear"></input>
			<input id="btnTSUpdate" type="button" value="Update"></input>
			<input id="btnTSPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>
<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-timesheet-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Description</td>
                <td style="width: 300px;">Project</td>
				<td style="width: 200px;">Activity</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-timesheet-data" style="overflow: auto;">
            <table id="sbg-timesheet-table" cellspacing="0" cellpadding="5" class="data">
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
var timesheet_url = "<?php echo Util::convertLink("Timesheet") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/timesheet.min.js") ; ?>
</script>