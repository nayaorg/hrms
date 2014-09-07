<div id="sbg-timecard-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="timecard-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#timecard-general">General</a></li>
			<span class="sbg-tab-title">Time Card Master</span>
		</ul>
		<div id="timecard-general" style="height:200px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtTimeCardId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtTimeCardDesc" /><span id="timecard_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref No : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtTimeCardRef" /></td>
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
				</tr>
                <tr>
					<td style="text-align:right;"><span>Tolerance : </span></td>
					<td><input type="number" min="0" max="999" id="inpTolerance" /></td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Break Time : </span></td>
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
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="timecard_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnTimeCardAdd" type="button" value="Add"></input>
			<input id="btnTimeCardClear" type="button" value="Clear"></input>
			<input id="btnTimeCardUpdate" type="button" value="Update"></input>
			<input id="btnTimeCardPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-timecard-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
                <td style="width: 100px;">Ref No</td>
                <td style="width: 100px;">Start Time</td>
                <td style="width: 100px;">End Time</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-timecard-data" style="overflow: auto;">
            <table id="sbg-timecard-table" cellspacing="0" cellpadding="5" class="data">
			<tr>
            	<td style="width:50px;height:1px"></td>
                <td style="width:400px"></td>
                <td style="width:100px"></td>
                <td style="width:100px"></td>
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
var timecard_url = "<?php echo Util::convertLink("TimeCard") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/timecard.min.js") ; ?>
</script>