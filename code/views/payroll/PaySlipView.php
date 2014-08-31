<div class="ui-widget-content ui-corner_all" style="width:500px;margin: 5px 5px 5px 5px;">
	<div class="ui-widget-header" style="font-size:1.5em;height:30px;padding-left:10px">Print Pay Slip</div>
	<table>
		<tr style="height:30px;">
			<td style="width:100px;padding-left:10px"><span style="font-size:1.2em">Pay Date :</span></td>
			<td>
				<select id="cobPaySlipMonth"><?php echo Util::getMonthOption() ; ?></select>&nbsp;-&nbsp;
				<select id="cobPaySlipYear">><?php echo Util::getYearOption() ; ?></select>
			</td>
		</tr>
		<tr style="height:30px;">
			<td style="padding-left:10px;"><span style="font-size:1.2em">Company :</span></td>
			<td><select id="cobPaySlipCoy"><?php echo $this->getCompany() ; ?></select></td>
		</tr>
		<tr style="height:30px;">
			<td style="padding-left:10px;"><span style="font-size:1.2em">Department :</span></td>
			<td><select id="cobPaySlipDept"><?php echo $this->getDepartment() ; ?></select></td>
		</tr>
		<tr style="height:40px">
			<td></td>
			<td><input id="btnPaySlipPrint" type="button" value="Print"></input></td>
		</tr>
	</table>
	<div class="sbg-entry-error">
		<span id="payslip_err_mesg" class="sbg-error"></span>
	</div>
</div> 
<script type="text/javascript">
var payslip_url = "<?php echo Util::convertLink("PaySlip") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/payslip.min.js") ; ?>
</script>