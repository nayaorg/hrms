<div id="sbg-holiday-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="holiday-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#holiday-general">General</a></li>
			<span class="sbg-tab-title">Holiday Master</span>
		</ul>
		<div id="holiday-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtHolidayId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Date : </span></td>
					<td><input type="text" value="" maxlength="10" size="12" id="txtHolidayDate" />
						<span id="holiday_err_date" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="50" size="40" id="txtHolidayDesc" />
						<span id="holiday_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="holiday_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnHolidayAdd" type="button" value="Add"></input>
			<input id="btnHolidayClear" type="button" value="Clear"></input>
			<input id="btnHolidayUpdate" type="button" value="Update"></input>
			<input id="btnHolidayPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-holiday-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Date</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-holiday-data" style="overflow: auto;">
            <table id="sbg-holiday-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:100px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var holiday_url = "<?php echo Util::convertLink("Holiday") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/Holiday.min.js") ; ?>
</script>