<div style="margin-top:5px">
	<div id="sbg-incomeyear-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Year : &nbsp;&nbsp;</span>
		<select id="cobIncomeYear">><?php echo Util::getYearOption() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Company :&nbsp;&nbsp;</span><select id="cobIncomeYearCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Department :&nbsp;&nbsp;</span><select id="cobIncomeYearDept"><?php echo $this->getDepartment() ; ?></select>
		<input id="btnIncomeYearView" type="button" value="View" style="margin-left:20px"></input>
		<input id="btnIncomeYearPrint" type="button" value="Print"></input>
		<input id="btnIncomeYearExport" type="button" value="IRAS Export"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-incomeyear-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 80px;">Salary</td>
				<td style="width: 80px;">Bonus</td>
				<td style="width: 80px;">Director's Fee</td>
				<td style="width: 80px;">Others</td>
				<td style="width: 80px;">Total</td>
                </tr>
			</table>
		</div>
        <div id="sbg-incomeyear-data" style="overflow: auto;">
            <table id="sbg-incomeyear-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:150px"></td>
				<td style="width:200px"></td>
				<td style="width:150px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
				<td style="width:80px"></td>
			</tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var incomeyear_url = "<?php echo Util::convertLink("IncomeYear") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/incomeyear.min.js") ; ?>
</script>