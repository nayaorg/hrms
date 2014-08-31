<div id="sbg-payentry-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="payentry-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#payentry-basic">Basic Pay</a></li>
			<li><a href="#payentry-income">Incomes</a></li>
			<li><a href="#payentry-deduct">Deductions</a></li>
			<span class="sbg-tab-title">Pay Entry</span>
		</ul>
		<div id="payentry-basic" style="height:150px;">
			<table>
				<tr>
					<td style="text-align:right;width:130px;"><span>Employee : </span></td>
					<td colspan="3"><span id="lblPayEntryIdName"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Company : </span></td>
					<td colspan="3"><span id="lblPayEntryCoy"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Department : </span></td>
					<td colspan="3"><span id="lblPayEntryDept"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Basic Amount/Rate : </span></td>
					<td style="width:250px"><input type="text" maxlength="10" size="15" id="txtPayEntryValue" /></td>
					<td style="width:80px;text-align:right"><span>Basic Pay : </span></td>
					<td style="text-align:right;width:120px;font-weight:bold"><span id="lblPayEntryBasic"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Month/Days : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtPayEntryQty"></td>
					<td style="text-align:right"><span>Income : </span></td>
					<td style="text-align:right;font-weight:bold"><span id="lblPayEntryIncome"></span></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td style="text-align:right"><span>Deduction : </span></td>
					<td style="text-align:right;font-weight:bold"><span id="lblPayEntryDeduct"></span></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td style="text-align:right"><span>Total Pay : </span></td>
					<td style="text-align:right;font-weight:bold"><span id="lblPayEntryNet"></span></td>
				</tr>
			</table>
		</div>
		<div id="payentry-income" style="height:150px;">
			<table>
				<tr>
					<td style="text-align:right"><select id="cobPayEntryIncome1"><?php echo $this->getPayType(0) ; ?></select></td>
					<td><input id="txtPayEntryIncome1" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td style="text-align:right"><select id="cobPayEntryIncome2"><?php echo $this->getPayType(0) ; ?></select></td>
					<td><input id="txtPayEntryIncome2" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td style="text-align:right"><select id="cobPayEntryIncome3"><?php echo $this->getPayType(0) ; ?></select></td>
					<td><input id="txtPayEntryIncome3" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td style="text-align:right"><select id="cobPayEntryIncome4"><?php echo $this->getPayType(0) ; ?></select></td>
					<td><input id="txtPayEntryIncome4" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td style="text-align:right"><select id="cobPayEntryIncome5"><?php echo $this->getPayType(0) ; ?></select></td>
					<td><input id="txtPayEntryIncome5" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td style="text-align:right"><select id="cobPayEntryIncome6"><?php echo $this->getPayType(0) ; ?></select></td>
					<td><input id="txtPayEntryIncome6" type="text" maxlength="10" size="15"></input></td>
				</tr>
			</table>
		</div>
		<div id="payentry-deduct" style="height:150px;">
			<table>
				<tr>
					<td><select id="cobPayEntryDeduct1"><?php echo $this->getPayType(1) ; ?></select></td>
					<td><input id="txtPayEntryDeduct1" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><select id="cobPayEntryDeduct2"><?php echo $this->getPayType(1) ; ?></select></td>
					<td><input id="txtPayEntryDeduct2" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><select id="cobPayEntryDeduct3"><?php echo $this->getPayType(1) ; ?></select></td>
					<td><input id="txtPayEntryDeduct3" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><select id="cobPayEntryDeduct4"><?php echo $this->getPayType(1) ; ?></select></td>
					<td><input id="txtPayEntryDeduct4" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><select id="cobPayEntryDeduct5"><?php echo $this->getPayType(1) ; ?></select></td>
					<td><input id="txtPayEntryDeduct5" type="text" maxlength="10" size="15"></input></td>
				</tr>
				<tr>
					<td><select id="cobPayEntryDeduct6"><?php echo $this->getPayType(1) ; ?></select></td>
					<td><input id="txtPayEntryDeduct6" type="text" maxlength="10" size="15"></input></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="payentry_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnPayEntryTotal" type="button" value="Refresh Total"></input>
			<input id="btnPayEntryClear" type="button" value="Clear"></input>
			<input id="btnPayEntrySave" type="button" value="Save"></input>
		</div>
	</div>
	
</div>

<div>
	<div id="sbg-payentry-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Pay Date : &nbsp;&nbsp;</span>
		<select id="cobPayEntryMonth"><?php echo Util::getMonthOption() ; ?></select>&nbsp;-&nbsp;
		<select id="cobPayEntryYear">><?php echo Util::getYearOption() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Company :&nbsp;&nbsp;</span><select id="cobPayEntryCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Department :&nbsp;&nbsp;</span><select id="cobPayEntryDept"><?php echo $this->getDepartment() ; ?></select>
		<input id="btnPayEntryList" type="button" value="Get List" style="margin-left:20px"></input>
		<input id="btnPayEntryReset" type="button" value="Clear List"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-payentry-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 80px;">Basic</td>
				<td style="width: 80px;">Incomes</td>
				<td style="width: 80px;">Deductions</td>
				<td style="width: 80px;">Total Pay</td>
				<td style="width: 20px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-payentry-data" style="overflow: auto;">
            <table id="sbg-payentry-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:150px"></td>
				<td style="width:200px"></td>
				<td style="width:150px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
				<td style="width:20px"></td></tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var payentry_url = "<?php echo Util::convertLink("PayEntry") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/payentry.min.js") ; ?>
</script>