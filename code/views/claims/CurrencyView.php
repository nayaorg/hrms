<div id="sbg-currency-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="currency-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#currency-general">General</a></li>
			<span class="sbg-tab-title">Currency Master</span>
		</ul>
		<div id="currency-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtCurrencyId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtCurrencyDesc" /><span id="currency_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Code : </span></td>
					<td><input type="text" maxlength="3" size="5" id="txtCurrencyRef" /><span id="currency_err_ref" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="currency_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnCurrencyAdd" type="button" value="Add"></input>
			<input id="btnCurrencyClear" type="button" value="Clear"></input>
			<input id="btnCurrencyUpdate" type="button" value="Update"></input>
			<input id="btnCurrencyPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-currency-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-currency-data" style="overflow: auto;">
            <table id="sbg-currency-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var currency_url = "<?php echo Util::convertLink("Currency") ; ?>" ;
<?php include (PATH_CODE . "js/claims/currency.min.js") ; ?>
</script>