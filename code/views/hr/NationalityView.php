<div id="sbg-nat-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="nat-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#nat-general">General</a></li>
			<span class="sbg-tab-title">Nationality Master</span>
		</ul>
		<div id="nat-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtNatId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtNatDesc" /><span id="nat_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>IRAS Ref : </span></td>
					<td><input type="text" maxlength="3" size="5" id="txtNatRef" /><span id="nat_err_ref" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="nat_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnNatAdd" type="button" value="Add"></input>
			<input id="btnNatClear" type="button" value="Clear"></input>
			<input id="btnNatUpdate" type="button" value="Update"></input>
			<input id="btnNatPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-nat-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-nat-data" style="overflow: auto;">
            <table id="sbg-nat-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var nat_url = "<?php echo Util::convertLink("Nationality") ; ?>" ;
<?php include (PATH_CODE . "js/hr/nationality.min.js") ; ?>
</script>