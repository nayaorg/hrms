<div id="sbg-company-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="company-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#company-general">General</a></li>
			<li><a href="#company-iras">IRAS</a></li>
			<li><a href="#company-addr">Address</a></li>
			<span class="sbg-tab-title">Company Profile</span>
		</ul>
		<div id="company-general" style="height:180px;">
			<table>
				<tr>
					<td style="text-align:right;width:120px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="12" size="12" id="txtCoyId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Name : </span></td>
					<td><input type="text" maxlength="60" size="60" id="txtCoyName" />
					<span id="coy_err_name" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref No : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtCoyRef" /></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>CPF CSN No : </span></td>
					<td><input type="text" maxlength="15" size="20" id="txtCoyCpfNo" /><span>&nbsp;&nbsp;(e.g. 209904795BPTE01)</span>
					<span id="coy_err_cpfno" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>CPF Ref Code : </span></td>
					<td><input type="text" maxlength="2" size="5" id="txtCoyCpfRef" /><span>&nbsp;&nbsp;(two digit payment advice code)
					<span id="coy_err_cpfref" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Bank : </span></td>
					<td><select id="cobCoyBank"><?php echo $this->getBank() ; ?></select></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Bank Acct. No : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtCoyAcctNo" /></td>
				</tr>

			</table>
		</div>
		<div id="company-iras" style="height:180px;">
			<table>
				<tr>
					<td style="text-align:right;width:130px"><span>Tax Ref No : </span></td>
					<td style="width:470px;"><input type="text" maxlength="20" size="30" id="txtCoyRegNo" />
					<span id="coy_err_regno" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Tax Ref No Type : </span></td>
					<td><select id="cobCoyRegType"><?php echo $this->getRegType() ; ?></select>
					<span id="coy_err_regtype" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Authorised Person : </span></td>
					<td><input type="text" maxlength="15" size="20" id="txtCoyAuthName" />
					<span id="coy_err_authname" style="color:Red;display:none">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Designation : </span></td>
					<td><input type="text" maxlength="30" size="30" id="txtCoyAuthTitle" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Contact No : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtCoyAuthTel" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>EGM/AGM Date : </span></td>
					<td><input type="text" maxlength="4" size="6" id="txtCoyEgm" />
					<span>&nbsp;(MMDD)</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Bonus Date : </span></td>
					<td><input type="text" maxlength="4" size="6" id="txtCoyBonus" />
					<span>&nbsp;(MMDD)</span>
					</td>
				</tr>
			</table>
		</div>
		<div id="company-addr" style="height:180px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>Address : </span></td>
					<td style="width:500px"><input type="text" maxlength="40" size="50" id="txtCoyAddr1" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span></span></td>
					<td><input type="text" maxlength="40" size="50" id="txtCoyAddr2" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span></span></td>
					<td><input type="text" maxlength="40" size="50" id="txtCoyAddr3" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span></span></td>
					<td><input type="text" maxlength="40" size="50" id="txtCoyAddr4" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span></span></td>
					<td><input type="text" maxlength="40" size="50" id="txtCoyAddr5" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Tel No : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtCoyTel" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Fax No : </span></td>
					<td><input type="text" maxlength="20" size="30" id="txtCoyFax" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="coy_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnCoyAdd" type="button" value="Add"></input>
			<input id="btnCoyClear" type="button" value="Clear"></input>
			<input id="btnCoyUpdate" type="button" value="Update"></input>
			<input id="btnCoyPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-company-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Name</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-company-data" style="overflow: auto;">
            <table id="sbg-company-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:400px"></td>
				<td style="width:25px"></td>
				<td style="width:25px"></td>
			</tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var coy_url = "<?php echo Util::convertLink("Company") ; ?>" ;
<?php include (PATH_CODE . "js/admin/company.min.js") ; ?>
</script>