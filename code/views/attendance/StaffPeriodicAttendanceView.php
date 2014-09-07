<div id="staff-periodic-attendance-title" style="padding-top:10px;padding-bottom:10px">
	<font size="3" ><center>Staff Periodic Attendance Report</center></font>
</div>
<div style="margin-top:5px">
	<div id="sbg-staff-periodic-attendance-option" class="ui-widget-content ui-corner-all" style="height:auto;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<table>
			<tr>
				<td style="vertical-align:top">
					<span>Date: </span><input type="text" id="txtDateReportBegin" size="10"/> - <input type="text" id="txtDateReportEnd" size="10"/>&nbsp; &nbsp; &nbsp;
				</td>
				<td style="vertical-align:top">
					<span>Dept. : </span>
					<select id="cobDepartment"><?php echo $this->getDepartment() ; ?></select> &nbsp; &nbsp; &nbsp;
				</td>
				<td style="vertical-align:top">
					<div>
						<input id="btnStaffPeriodicAttendanceView" type="button" value="View"></input>
						<input id="btnStaffPeriodicAttendancePrint" type="button" value="Print"></input>
						<input id="btnStaffPeriodicAttendanceExport" type="button" value="Export"></input>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-staff-periodic-attendance-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 30px;" onclick='sort_table(0)'>ID</td>
                <td style="width: 100px;" onclick='sort_table(1)'>Name</td>
                <td style="width: 80px;" onclick='sort_table(2)'>Date</td>
                <td style="width: 50px;" onclick='sort_table(3)'>In</td>
				<td style="width: 50px;" onclick='sort_table(4)'>Late In</td>
				<td style="width: 50px;" onclick='sort_table(5)'>Out</td>
				<td style="width: 50px;" onclick='sort_table(6)'>Early Out</td>
				<td style="width: 50px;" onclick='sort_table(7)'>Break From</td>
				<td style="width: 50px;" onclick='sort_table(8)'>Break To</td>
				<td style="width: 30px;" onclick='sort_table(9)'>O/T</td>
				<td style="width: 80px;" onclick='sort_table(10)'>Rmks.</td>
                </tr>
			</table>
		</div>
        <div id="sbg-staff-periodic-attendance-data" style="overflow: auto;">
            <table id="sbg-staff-periodic-attendance-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width: 30px;height:1px"></td>
				<td style="width:100px"></td>
				<td style="width:50px"></td>
				<td style="width: 50px"></td>
				<td style="width: 50px"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 30px;"></td>
				<td style="width: 80px;"></td>
			</tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var staff_periodic_attendance_url = "<?php echo Util::convertLink("StaffPeriodicAttendance") ; ?>" ;
<?php 
	include (PATH_CODE . "js/attendance/staffperiodicattendance.min.js") ; 
?>
</script>