<div id="sbg-emppay-entry" style="margin: 5px 5px 5px 5px;width:610px;">
	<div id="emppay-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#emppay-general">General</a></li>
			<li><a href="#emppay-income">Income/Fund/Levy</a></li>
			<span class="sbg-tab-title">Employee Pay Setup</span>
		</ul>
		<div id="emppay-general" style="height:180px">
			<table>
				<tr>
					<td style="text-align:right;width:120px;"><span>Employee : </span></td>
					<td colspan="3" style="width:480px"><span id="lblEmpPayIdName"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Company/Dept. : </span></td>
					<td colspan="3"><span id="lblEmpPayCoy"></span></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Pay Start : </span></td>
					<td colspan="3"><input type="text" value="" maxlength="10" size="12" id="txtEmpPayStart" />
						<span id="emppay_err_start" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Pay End : </span></td>
					<td colspan="3"><input type="text" value="" maxlength="10" size="12" id="txtEmpPayEnd" />
						<span id="emppay_err_end" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;width:120px"><span>Basic Pay/Rate : </span></td>
					<td style="width:200px"><input type="text" maxlength="10" size="15" id="txtEmpPayValue" /></td>
					<td style="width:100px"></td>
					<td style="width:180px"></td>
				</tr>
				
				<tr>
					<td style="text-align:right;"><span>CPF Type : </span></td>
					<td><select id="cobEmpPayCpf"><?php echo $this->getCpfType() ; ?></select></td>
					<td style="text-align:right;"><span>CPF No : </span></td>
					<td><input type="text" maxlength="9" size="12" id="txtEmpPayCpfNo" /><span id="emppay_err_cpfno" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Bank Acct. No : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtEmpPayAcct" /></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div id="emppay-income" style="height:180px">
			<table>
				<tr>
					<td style="width:400px"><select id="cobEmpPayIncome1"><?php echo $this->getIncomeType(0) ; ?></select>
					<input id="txtEmpPayIncome1" type="text" maxlength="10" size="15"></input></td>
					<td style="width:100px"><span>Levies :</span></td>
					<td style="width:100px"><span>Ethnic Funds : </span></td>
				</tr>
				<tr>
					<td><select id="cobEmpPayIncome2"><?php echo $this->getIncomeType(0) ; ?></select>
					<input id="txtEmpPayIncome2" type="text" maxlength="10" size="15"></input></td>
					<td style="padding-left:10px"><input id="chkEmpPaySdl" type="checkbox" /><label for="chkEmpPaySdl">SDL</label></td>
					<td style="padding-left:10px"><input id="chkEmpPayMbmf" type="checkbox" /><label for="chkEmpPayMbmf">MBMF</label></td>
				</tr>
				<tr>
					<td><select id="cobEmpPayIncome3"><?php echo $this->getIncomeType(0) ; ?></select>
					<input id="txtEmpPayIncome3" type="text" maxlength="10" size="15"></input></td>
					<td></td>
					<td style="padding-left:10px"><input id="chkEmpPaySinda" type="checkbox" /><label for="chkEmpPaySinda">SINDA</label></td>
				</tr>
				<tr>
					<td><select id="cobEmpPayIncome4"><?php echo $this->getIncomeType(0) ; ?></select>
					<input id="txtEmpPayIncome4" type="text" maxlength="10" size="15"></input></td>
					<td></td>
					<td style="padding-left:10px;"><input id="chkEmpPayCdac" type="checkbox" /><label for="chkEmpPayCdac">CDAC</label></td>
				</tr>
				<tr>
					<td><select id="cobEmpPayIncome5"><?php echo $this->getIncomeType(0) ; ?></select>
					<input id="txtEmpPayIncome5" type="text" maxlength="10" size="15"></input></td>
					<td></td>
					<td style="padding-left:10px;"><input id="chkEmpPayEcf" type="checkbox" /><label for="chkEmpPayEcf">ECF</label></td>
				</tr>
				<tr>
					<td><select id="cobEmpPayIncome6"><?php echo $this->getIncomeType(0) ; ?></select>
					<input id="txtEmpPayIncome6" type="text" maxlength="10" size="15"></input></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="emppay_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnEmpPayClear" type="button" value="Clear"></input>
			<input id="btnEmpPaySave" type="button" value="Save"></input>
		</div>
	</div>
</div>

<div>
	<div id="sbg-payentry-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Company :&nbsp;&nbsp;</span><select id="cobEmpPayCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Department :&nbsp;&nbsp;</span><select id="cobEmpPayDept"><?php echo $this->getDepartment() ; ?></select>
		<input id="btnEmpPayList" type="button" value="Get Employee List" style="margin-left:20px"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-emppay-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 200px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 20px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-emppay-data" style="overflow: auto;">
            <table id="sbg-emppay-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:200px"></td><td style="width:200px"></td><td style="width:150px"></td><td style="width:20px"></td></tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var emppay_url = "<?php echo Util::convertLink("EmployeePay") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/employeepay.min.js") ; ?>
</script>