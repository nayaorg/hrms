<div id="sbg-rate-group-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="rate-group-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#rate-group-general">General</a></li>
			<span class="sbg-tab-title">Rate Group Master</span>
		</ul>
		<div id="rate-group-general" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:auto"><input type="text" maxlength="10" size="10" id="txtRateGroupId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;width:100px;"><span>Desc : </span></td>
					<td style="width:auto" colspan=3><input type="text" maxlength="30" size="30" id="txtRateGroupDesc"/></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Rate Type : </span></td>
					<td>
						<select id="cobRateType" style="width: 80px">
							<option value="0">Hourly</option>
							<option value="1">Daily</option>
						</select>
						<span id="sg_err_rate_type" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Rate Normal : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtRateNormalNormal"/></td>
					<td style="text-align:right;"><span>O/T : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtRateNormalOT"/></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Rate Weekend : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtRateWeekendNormal"/></td>
					<td style="text-align:right;"><span>O/T : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtRateWeekendOT"/></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Rate Holiday : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtRateHolidayNormal"/></td>
					<td style="text-align:right;"><span>O/T : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtRateHolidayOT"/></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="rate_group_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnRateGroupAdd" type="button" value="Add"></input>
			<input id="btnRateGroupClear" type="button" value="Clear"></input>
			<input id="btnRateGroupUpdate" type="button" value="Update"></input>
			<input id="btnRateGroupPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-rate-group-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Desc</td>
                <td style="width: 60px;">Rate Type</td>
                <td style="width: 60px;">Normal</td>
                <td style="width: 60px;">O/T</td>
                <td style="width: 60px;">Weekend</td>
                <td style="width: 60px;">O/T</td>
                <td style="width: 60px;">Holiday</td>
                <td style="width: 60px;">O/T</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-rate-group-data" style="overflow: auto;">
            <table id="sbg-rate-group-table" cellspacing="0" cellpadding="5" class="data">
			<tr>
				<td style="width:50px;height:1px"></td>
                <td style="width: 100px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 60px;"></td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
			</tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var rate_group_url = "<?php echo Util::convertLink("RateGroup") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/rategroup.min.js") ; 
?>
</script>