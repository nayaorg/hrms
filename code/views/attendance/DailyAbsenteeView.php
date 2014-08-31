<div id="daily-absentee-title" style="padding-top:10px;padding-bottom:10px">
	<font size="3" ><center>Daily Absentee Report</center></font>
</div>
<div style="margin-top:5px">
	<div id="sbg-daily-absentee-option" class="ui-widget-content ui-corner-all" style="height:auto;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Date : &nbsp;&nbsp;</span>
		<input type="text" id="txtDateReportStart" size="10"/> - 
		<input type="text" id="txtDateReportEnd" size="10"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Dept. :&nbsp;&nbsp;</span><select id="cobDept"><?php echo $this->getDepartment() ; ?></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input id="btnDailyAbsenteeView" type="button" value="View"></input>
		<input id="btnDailyAbsenteePrint" type="button" value="Print"></input>
		<input id="btnDailyAbsenteeExport" type="button" value="Export"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-daily-absentee-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 100px;" onclick='sort_table(0)'>Date</td>
                <td style="width: 50px;" onclick='sort_table(1)'>ID</td>
                <td style="width: 150px;" onclick='sort_table(2)'>Name</td>
                <td style="width: 200px;" onclick='sort_table(3)'>Remarks</td>
                </tr>
			</table>
		</div>
        <div id="sbg-daily-absentee-data" style="overflow: auto;">
            <table id="sbg-daily-absentee-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:100px;height:1px"></td>
				<td style="width:50px"></td>
				<td style="width:150px"></td>
				<td style="width:200px"></td>
			</tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var daily_absentee_url = "<?php echo Util::convertLink("DailyAbsentee") ; ?>" ;
<?php 
	include (PATH_CODE . "js/attendance/dailyabsentee.min.js") ; 
?>
</script>