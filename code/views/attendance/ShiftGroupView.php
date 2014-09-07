<div id="sbg-shift-group-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="shift-group-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#shift-group-general">General</a></li>
			<span class="sbg-tab-title">Shift Group Master</span>
		</ul>
		<div id="shift-group-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtShiftGroupId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtShiftGroupDesc" /><span id="shift_group_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref No : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtShiftGroupRef" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="shift_group_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnShiftGroupAdd" type="button" value="Add"></input>
			<input id="btnShiftGroupClear" type="button" value="Clear"></input>
			<input id="btnShiftGroupUpdate" type="button" value="Update"></input>
			<input id="btnShiftGroupPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-shift-group-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
                <td style="width: 100px;">Ref No</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-shift-group-data" style="overflow: auto;">
            <table id="sbg-shift-group-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:100px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var shift_group_url = "<?php echo Util::convertLink("ShiftGroup") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/shiftgroup.min.js") ; ?>
</script>