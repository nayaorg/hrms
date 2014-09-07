<div id="sbg-overtime-entry" style="height:auto;width:610px;margin: 5px 5px 5px 5px;">
	<div id="overtime-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#overtime-general">Entry</a></li>
			<span class="sbg-tab-title">Over Time Entry</span>
		</ul>
		<div id="overtime-general" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;"><span>Dept : </span></td>
					<td>
						<select id="cobDept"><?php echo $this->getDepartment() ; ?></select>
					</td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Emp. ID : </span></td>
					<td>
						<select id="cobEmpId"></select>
						<input type="hidden" id="limit_before"/>
						<input type="hidden" id="limit_after"/>
						<input type="hidden" id="tolerance"/>
						<input type="hidden" id="inpShiftStart"/>
						<input type="hidden" id="inpShiftEnd"/>
						<input type="hidden" id="inpLateIn"/>
						<input type="hidden" id="inpEarlyOut"/>
						<input type="hidden" id="overtimeId"/>
						<span id="overtime_emp_id" style="color:red;display:none;padding-left:5px"></span>
					</td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Date : </span></td>
					<td><input type="text" id="txtDateOver"/></td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Time : </span></td>
					<td>
						<select id="cboTimeStartHour" onchange="recalculateTime()">
						</select>:	
						<select id="cboTimeStartMinute" onchange="recalculateTime()">
						</select> - 
						<select id="cboTimeEndHour" onchange="recalculateTime()">
						</select>:	
						<select id="cboTimeEndMinute" onchange="recalculateTime()">
						</select>
					</td>
					<td>O/T :</td>
					<td>
						<input type="text" id="txtOTHour" disabled="disabled" value="0"/>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtOverTimeDesc" /><span id="overtime_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Project : </span></td>
					<td colspan="3">
						<select id="cobProject"><?php echo $this->getProjectList() ; ?></select>
						
						<span id="sg_err_project" style="color:red;display:none;padding-left:5px">*</span>
						<input type="hidden" id="seq_number"/>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="overtime_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnOverTimeAdd" type="button" value="Add"></input>
			<input id="btnOverTimeClear" type="button" value="Clear"></input>
			<input id="btnOverTimeUpdate" type="button" value="Update"></input>
			<input id="btnOverTimeHide" type="button" value="Hide"></input>
		</div>
	</div>
</div>

<div>
	<div class="ui-widget-content ui-corner-all" style="height:50px;margin: 5px 5px 5px 5px;">
		<table width="100%">
			<tr>
				<td>
					<span>Period : </span>
					<input type="text" style="width:80px" id="txtPeriodStart" value="<?=date('d/m/Y')?>"/> - 
					<input type="text" style="width:80px" id="txtPeriodEnd" value="<?=date('d/m/Y')?>"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input id="btnOvertimeApply" type="button" value="Get Overtime Record" onclick="getList()"></input>
					<input id="btnOverTimePrint" type="button" value="Print"></input>
				</td>
				<td style="text-align:right;" >
					<div class="sbg-entry-command">
						<input id="btnOvertimeInputControl" type="button" value="New Entry"></input>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-overtime-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 30px;">E.ID</td>
                <td style="width: 100px;">Name</td>
                <td style="width: 80px;">Date</td>
                <td style="width: 20px;display: none">Project ID</td>
                <td style="width: 80px;">Project</td>
                <td style="width: 20px;display: none">Overtime ID</td>
                <td style="width: 50px;">Start Time</td>
                <td style="width: 50px;">End Time</td>
                <td style="width: 50px;">O/T</td>
                <td style="width: 150px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-overtime-data" style="overflow: auto;">
            <table id="sbg-overtime-table" cellspacing="0" cellpadding="5" class="data">
			<tr>
                <td style="width:30px;height:1px"></td>
                <td style="width:100px"></td>
                <td style="width:80px"></td>
                <td style="width:20px;display: none"></td>
                <td style="width:80px"></td>
                <td style="width:20px;display: none"></td>
                <td style="width:50px"></td>
                <td style="width:50px"></td>
                <td style="width:50px"></td>
                <td style="width:150px"></td>
                <td style="width:25px"></td>
                <td style="width:25px"></td>
            </tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var overtime_url = "<?php echo Util::convertLink("OverTime") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/overtime.min.js") ; ?>
</script>