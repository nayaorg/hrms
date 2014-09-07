<div id="sbg-attendance-entry" style="height:auto;width:570px;margin: 5px 5px 5px 5px;">
	<div id="attendance-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#attendance-general">Entry</a></li>
			<span class="sbg-tab-title">Attendance Entry</span>
		</ul>
		<div id="attendance-general" style="height:auto;">
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
						<input type="hidden" id="shiftType"/>
						<input type="hidden" id="shiftGroupID"/>
						<span type="hidden" id="limit_before"/>
						<span type="hidden" id="limit_after"/>
						<span type="hidden" id="tolerance"/>
					</td>
					
					<td style="text-align:right;"><span>Shift Hour : </span></td>
					<td><input type="text" style="width:40px" id="inpShiftStart" value="--:--" disabled="disabled"/> - <input type="text" style="width:40px" id="inpShiftEnd" value="--:--" disabled="disabled"/>
						<span id="shiftHourMsg" style="color:red;display:none;padding-top:5px"></span>
					</td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Date : </span></td>
					<td><input type="text" id="txtDateAttendance" onchange="getShiftHour()" size="12"/></td>
					<td style="text-align:right;"><span>Over Time : </span></td>
					<td><input type="text" style='width:50px' id="inpOTStart" disabled="disabled" /> / <input type="text" style='width:50px' disabled="disabled"  id="inpOTEnd" disabled="disabled" /></td>
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
					<td style="text-align:right;"><span>Late In : </span></td>
					<td><input type="text" style='width:50px' disabled="disabled"  id="inpLateIn" /> minutes</td>
					
				</tr>
                <tr>
					<td style="text-align:right;"><span>Break : </span></td>
					<td>
						<select id="cboBreakStartHour" onchange="recalculateTime()">
						</select>:	
						<select id="cboBreakStartMinute" onchange="recalculateTime()">
						</select> - 
						<select id="cboBreakEndHour" onchange="recalculateTime()">
						</select>:	
						<select id="cboBreakEndMinute" onchange="recalculateTime()">
						</select>			
					</td>
					<td style="text-align:right;"><span>Early Out : </span></td>
					<td><input type="text" style='width:50px' disabled="disabled"  id="inpEarlyOut" /> minutes</td>
					
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
			<span id="attendance_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnAttendanceAdd" type="button" value="Add"></input>
			<input id="btnAttendanceClear" type="button" value="Clear"></input>
			<input id="btnAttendanceUpdate" type="button" value="Update"></input>
			<input id="btnAttendanceHide" type="button" value="Hide"></input>
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
					<input id="btnAttendanceApply" type="button" value="Get Attendance Record" onclick="getList()"></input>
					<input id="btnAttendancePrint" type="button" value="Print"></input>
				</td>
				<td>
					<div class="sbg-entry-command">
						<input id="btnAttendanceInputControl" type="button" value="New Entry"></input>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-attendance-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">Emp ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 100px;">Date</td>
                <td style="width: 100px;">Project</td>
                <td style="width: 50px;">Start Time</td>
                <td style="width: 50px;">End Time</td>
                <td style="width: 50px;">Break Start</td>
                <td style="width: 50px;">Break End</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-attendance-data" style="overflow: auto;">
            <table id="sbg-attendance-table" cellspacing="0" cellpadding="5" class="data">
			<tr>
                <td style="width:50px;height:1px"></td>
                <td style="width:150px; display"></td>
                <td style="width:100px"></td>
                <td style="width:100px"></td>
                <td style="width:50px"></td>
                <td style="width:50px"></td>
                <td style="width:50px"></td>
                <td style="width:50px"></td>
                <td style="width:25px"></td>
                <td style="width:25px"></td>
            </tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var attendance_url = "<?php echo Util::convertLink("Attendance") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/attendance.min.js") ; ?>
</script>