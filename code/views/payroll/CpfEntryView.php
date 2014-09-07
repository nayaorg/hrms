<div id="sbg-cpfentry-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="cpfentry-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#cpfentry-general">General</a></li>
			<span class="sbg-tab-title">CPF Entry</span>
		</ul>
		<div id="cpfentry-general" style="height:160px;">
			<table>
				<tr>
					<td style="text-align:right;"><span>Employee : </span></td>
					<td colspan="4"><span id="lblCpfEntryIdName"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Company : </span></td>
					<td colspan="4"><span id="lblCpfEntryCoy"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Department : </span></td>
					<td colspan="2"><span id="lblCpfEntryDept"></span></td>
					<td style="text-align:right"><span>MBMF : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfEntryMbmf" /></td>
				</tr>
				<tr>
					<td style="text-align:right;width:130px"><span>Ordinary Wages : </span></td>
					<td style="text-align:right;width:120px;font-weight:bold"><span id="lblCpfEntryOw"></span></td>
					<td style="width:220px"></td>
					<td style="text-align:right;width:50px"><span>SINDA : </span></td>
					<td style="width:80px"><input type="text" maxlength="10" size="15" id="txtCpfEntrySinda" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Additional Wages : </span></td>
					<td style="text-align:right;font-weight:bold"><span id="lblCpfEntryAw"></span></td>
					<td></td>
					<td style="text-align:right"><span>CDAC : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfEntryCdac" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>CPF - Employee : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfEntryEmp" /></td>
					<td></td>
					<td style="text-align:right"><span>ECF : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfEntryEcf" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>CPF - Company : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfEntryCoy" /></td>
					<td></td>
					<td style="text-align:right"><span>SDL : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfEntrySdl" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="cpfentry_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnCpfEntryClear" type="button" value="Clear"></input>
			<input id="btnCpfEntrySave" type="button" value="Save"></input>
		</div>
	</div>
	
</div>

<div>
	<div id="sbg-cpfentry-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Month/Year : &nbsp;&nbsp;</span>
		<select id="cobCpfEntryMonth"><?php echo Util::getMonthOption() ; ?></select>&nbsp;-&nbsp;
		<select id="cobCpfEntryYear">><?php echo Util::getYearOption() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Company :&nbsp;&nbsp;</span><select id="cobCpfEntryCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Department :&nbsp;&nbsp;</span><select id="cobCpfEntryDept"><?php echo $this->getDepartment() ; ?></select>
		<input id="btnCpfEntryList" type="button" value="Get List" style="margin-left:20px"></input>
		<input id="btnCpfEntryReset" type="button" value="Clear List"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-cpfentry-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 80px;">OW</td>
				<td style="width: 80px;">AW</td>
				<td style="width: 80px;">CPF - Emp</td>
				<td style="width: 80px;">CPF - Coy</td>
				<td style="width: 20px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-cpfentry-data" style="overflow: auto;">
            <table id="sbg-cpfentry-table" cellspacing="0" cellpadding="5" class="data">
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
var cpfentry_url = "<?php echo Util::convertLink("CpfEntry") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/cpfentry.min.js") ; ?>
</script>