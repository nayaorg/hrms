<div id="sbg-wp-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="wp-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#wp-general">General</a></li>
			<span class="sbg-tab-title">Work Permit Type Master</span>
		</ul>
		<div id="wp-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtWPId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtWPDesc" /><span id="wp_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Levy Amount : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtWPLevy" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="wp_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnWPAdd" type="button" value="Add"></input>
			<input id="btnWPClear" type="button" value="Clear"></input>
			<input id="btnWPUpdate" type="button" value="Update"></input>
			<input id="btnWPPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-wp-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 300px;">Description</td>
				<td style="width: 100px;">Levy</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-wp-data" style="overflow: auto;">
            <table id="sbg-wp-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:300px"></td><td style="width:100px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var wp_url = "<?php echo Util::convertLink("WorkPermit") ; ?>" ;
<?php include (PATH_CODE . "js/hr/workpermit.min.js") ; ?>
</script>