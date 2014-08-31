<div id="sbg-employee-shift-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="employee-shift-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#employee-shift-general">General</a></li>
			<span class="sbg-tab-title">Employee Shift Master</span>
		</ul>
		<div id="employee-shift-general" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>Employee ID : </span></td>
					<td style="width:auto">
						<input type="text" maxlength="10" size="10" id="txtEmployeeShiftId" value="Auto" disabled="disabled" />&nbsp;&nbsp;&nbsp;
						<span id="employee_shift_name"></span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Shift Type : </span></td>
					<td>
						<input type="radio" maxlength="10" size="10" name="rdoEmployeeShiftType" id="rdoEmployeeShiftType" value="0"/>Daily &nbsp;
						<input type="radio" maxlength="10" size="10" name="rdoEmployeeShiftType" id="rdoEmployeeShiftType" value="1"/>Weekly
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Shift Group : </span></td>
					<td>
						<select id="cobShiftGroup"><?php echo $this->getShiftGroup() ; ?></select>
						<span id="sg_err_group" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Rate Group : </span></td>
					<td>
						<select id="cobRateGroup"><?php echo $this->getRateGroup() ; ?></select>
						<span id="sg_err_rate" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Def. Time Card : </span></td>
					<td>
						<select id="cobTimeCard"><?php echo $this->getTimeCard() ; ?></select>
						<span id="sg_err_time" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="employee_shift_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnEmployeeShiftClear" type="button" value="Clear"></input>
			<input id="btnEmployeeShiftUpdate" type="button" value="Update"></input>
			<input id="btnEmployeeShiftPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-employee-shift-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 200px;">Name</td>
                <td style="width: 100px;">Shift Type</td>
                <td style="width: 100px;">Shift Group</td>
                <td style="width: 150px;">Rate Group</td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-employee-shift-data" style="overflow: auto;">
            <table id="sbg-employee-shift-table" cellspacing="0" cellpadding="5" class="data">
			<tr>
				<td style="width:50px;height:1px"></td>
				<td style="width:200px"></td>
				<td style="width:100px"></td>
				<td style="width:100px"></td>
				<td style="width:150px"></td>
				<td style="width:25px"></td>
				<td style="width:25px"></td>
			</tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var employee_shift_url = "<?php echo Util::convertLink("EmployeeShift") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/employeeshift.min.js") ; 
?>
</script>