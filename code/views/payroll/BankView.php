<div id="sbg-bank-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="bank-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#bank-general">General</a></li>
			<span class="sbg-tab-title">Bank Master</span>
		</ul>
		<div id="bank-general">
			<table>
				<tr>
					<td style="text-align:right;width:130px;"><span>ID : </span></td>
					<td style="width:470px"><input type="text" maxlength="10" size="10" id="txtBankId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Bank Name : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtBankDesc" /><span id="bank_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref No : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtBankRef" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Export File Name : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtBankFile" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="bank_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnBankAdd" type="button" value="Add"></input>
			<input id="btnBankClear" type="button" value="Clear"></input>
			<input id="btnBankUpdate" type="button" value="Update"></input>
			<input id="btnBankPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-bank-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 200px;">Description</td>
                <td style="width: 100px;">Ref No</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-bank-data" style="overflow: auto;">
            <table id="sbg-bank-table" cellspacing="0" cellpadding="5" class="data">
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
var bank_url = "<?php echo Util::convertLink("Bank") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/bank.min.js") ; ?>
</script>