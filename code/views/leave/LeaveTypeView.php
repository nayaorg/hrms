<div id="sbg-leavetype-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="leavetype-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#leavetype-general">General</a></li>
			<span class="sbg-tab-title">Leave Type Master</span>
		</ul>
		<div id="leavetype-general">
			<table>
				<tr>
					<td style="text-align:right;width:130px;"><span>ID : </span></td>
					<td style="width:470px"><input type="text" maxlength="10" size="10" id="txtLeaveTypeId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtLeaveTypeDesc" /><span id="leavetype_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Period : </span></td>
					<td><input type="text" maxlength="1" size="10" id="txtLeaveTypePeriod" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="leavetype_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnLeaveTypeAdd" type="button" value="Add"></input>
			<input id="btnLeaveTypeClear" type="button" value="Clear"></input>
			<input id="btnLeaveTypeUpdate" type="button" value="Update"></input>
			<input id="btnLeaveTypePrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-leavetype-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 200px;">Description</td>
                <td style="width: 100px;">Period</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-leavetype-data" style="overflow: auto;">
            <table id="sbg-leavetype-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:200px"></td>
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
var leavetype_url = "<?php echo Util::convertLink("LeaveType") ; ?>" ;
<?php include (PATH_CODE . "js/leave/leavetype.min.js") ; ?>
</script>