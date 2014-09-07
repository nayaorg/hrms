
	<div id="daily-attendance-title" style="padding-top:10px;padding-bottom:10px">
		<font size="3" ><center>Daily Attendance Report</center></font>
	</div>
	<div id="sbg-daily-attendance-option" class="ui-widget-content ui-corner-all" style="height:auto;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<div id="daily-attendance-general" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right"><span>Date :</span></td>
					<td><input type="text" id="txtDateReport" size="10"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>
						<span>Dept. :</span><select id="cobDept"><?php echo $this->getDepartment() ; ?></select>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td>
						<div>
							<input id="btnDailyAttendanceView" type="button" value="View"></input>
							<input id="btnDailyAttendancePrint" type="button" value="Print"></input>
							<input id="btnDailyAttendanceExport" type="button" value="Export"></input>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-daily-attendance-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;" onclick='sort_table(0)'>ID</td>
                <td style="width: 150px;" onclick='sort_table(1)'>Name</td>
                <td style="width: 50px;" onclick='sort_table(2)'>In</td>
				<td style="width: 50px;" onclick='sort_table(3)'>Late In</td>
				<td style="width: 50px;" onclick='sort_table(4)'>Out</td>
				<td style="width: 50px;" onclick='sort_table(5)'>Early Out</td>
				<td style="width: 50px;" onclick='sort_table(6)'>Break From</td>
				<td style="width: 50px;" onclick='sort_table(7)'>Break To</td>
				<td style="width: 50px;" onclick='sort_table(8)'>Overtime</td>
				<td style="width: 80px;" onclick='sort_table(9)'>Remarks</td>
                </tr>
			</table>
		</div>
        <div id="sbg-daily-attendance-data" style="overflow: auto;">
            <table id="sbg-daily-attendance-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:150px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 50px;"></td>
				<td style="width: 56px;"></td>
				<td style="width: 80px;"></td>
			</tr>
            </table>
        </div>
	</div>
<script type="text/javascript">
var daily_attendance_url = "<?php echo Util::convertLink("DailyAttendance") ; ?>" ;
<?php 
	include (PATH_CODE . "js/attendance/dailyattendance.min.js") ; 
?>
</script>