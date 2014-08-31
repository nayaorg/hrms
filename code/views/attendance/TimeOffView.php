<div id="sbg-timeoff-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="timeoff-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#timeoff-general">Entry</a></li>
			<span class="sbg-tab-title">Time Off Entry</span>
		</ul>
		<div id="timeoff-general" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;"><span>Dept : </span></td>
					<td>
						<select id="cobDept"><?php echo $this->getDepartment() ; ?></select>
					</td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Emp. ID : </span></td>
					<td><select id="cobEmpId"></select></td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Date : </span></td>
					<td><input type="text" id="txtDateOff"/></td>
				</tr>
                <tr>
					<td style="text-align:right;"><span>Time : </span></td>
					<td>
						<select id="cboTimeStartHour">
						</select>:	
						<select id="cboTimeStartMinute">
						</select> - 
						<select id="cboTimeEndHour">
						</select>:	
						<select id="cboTimeEndMinute">
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtTimeOffDesc" /><span id="timeoff_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="timeoff_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnTimeOffAdd" type="button" value="Add"></input>
			<input id="btnTimeOffClear" type="button" value="Clear"></input>
			<input id="btnTimeOffUpdate" type="button" value="Update"></input>
			<input id="btnTimeOffHide" type="button" value="Hide"></input>
		</div>
	</div>
</div>

<div>
	<div class="ui-widget-content ui-corner-all" style="height:50px;margin: 5px 5px 5px 5px;">
		<table width="100%">
			<tr>
				<td >
					<span>Period : </span>
					<input type="text" style="width:80px" id="txtPeriodStart" value="<?=date('d/m/Y')?>"/> - 
					<input type="text" style="width:80px" id="txtPeriodEnd" value="<?=date('d/m/Y')?>"/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input id="btnTimeOffApply" type="button" value="Get Timeoff Record"></input>
					<input id="btnTimeOffPrint" type="button" value="Print"></input>
				</td>
				<td style="text-align:right;" >
					<div class="sbg-entry-command">
						<input id="btnTimeOffInputControl" type="button" value="New Entry"></input>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-timeoff-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">Emp ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 75px;">Date</td>
                <td style="width: 50px;">Start Time</td>
                <td style="width: 50px;">End Time</td>
                <td style="width: 200px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-timeoff-data" style="overflow: auto;">
            <table id="sbg-timeoff-table" cellspacing="0" cellpadding="5" class="data">
			<tr>
                <td style="width:50px;height:1px"></td>
                <td style="width:150px"></td>
                <td style="width:75px"></td>
                <td style="width:50px"></td>
                <td style="width:50px"></td>
                <td style="width:200px"></td>
                <td style="width:25px"></td>
                <td style="width:25px"></td>
            </tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var timeoff_url = "<?php echo Util::convertLink("TimeOff") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/timeoff.min.js") ; ?>
</script>