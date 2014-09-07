<div id="sbg-timecardlimit-entry" style="width:410px;margin: 5px 5px 5px 5px;">
	<div id="timecardlimit-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#timecardlimit-general">General</a></li>
			<span class="sbg-tab-title">Time Card Limit Master</span>
		</ul>
		<div id="timecardlimit-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;" id="labelTimeCardLimitId"><span>ID : </span></td>
					<td><input type="text" maxlength="10" size="10" id="txtTimeCardLimitId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;width:100px;"><span>Before : </span></td>
					<td><input type="number" id="inpTimeCardLimitBefore" min="0" max="12" value="0"></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>After : </span></td>
					<td><input type="number" id="inpTimeCardLimitAfter" min="0" max="12" value="0"></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="timecardlimit_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnTimeCardLimitUpdate" type="button" value="Update"></input>
		</div>
	</div>
</div>
<script type="text/javascript">
var timecardlimit_url = "<?php echo Util::convertLink("TimeCardLimit") ; ?>" ;
<?php include (PATH_CODE . "js/attendance/timecardlimit.min.js") ; ?>
</script>