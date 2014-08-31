<div id="sbg-race-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="race-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#race-general">General</a></li>
			<span class="sbg-tab-title">Race Master</span>
		</ul>
		<div id="race-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtRaceId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtRaceDesc" /><span id="race_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="race_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnRaceAdd" type="button" value="Add"></input>
			<input id="btnRaceClear" type="button" value="Clear"></input>
			<input id="btnRaceUpdate" type="button" value="Update"></input>
			<input id="btnRacePrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-race-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-race-data" style="overflow: auto;">
            <table id="sbg-race-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var race_url = "<?php echo Util::convertLink("Race") ; ?>" ;
<?php include (PATH_CODE . "js/hr/race.min.js") ; ?>
</script>