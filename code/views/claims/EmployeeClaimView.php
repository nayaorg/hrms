<div id="employee-claim-title" style="padding-top:10px;padding-bottom:10px">
	<font size="3" ><center>Employee Claim Report</center></font>
</div>
<div style="margin-top:5px">
	<div id="sbg-employee-claim-option" class="ui-widget-content ui-corner-all" style="height:auto;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Month : &nbsp;</span>
			<select id="cobMonth">
				<?php echo $this->getMonth() ; ?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Year : &nbsp;</span>
			<select id="cobYear">
				<?php echo $this->getYear() ; ?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Dept. : &nbsp;</span>
			<select id="cobDept">
				<?php echo $this->getDepartment() ; ?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input id="btnEmployeeClaimView" type="button" value="View"></input>
		<input id="btnEmployeeClaimPrint" type="button" value="Print"></input>
		<input id="btnEmployeeClaimExport" type="button" value="Export"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-employee-claim-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
					<td style="width: 50px;">ID</td>
					<td style="width: 150px;">Name</td>
					<td style="width: 200px;">Amount</td>
                </tr>
			</table>
		</div>
        <div id="sbg-employee-claim-data" style="overflow: auto;">
            <table id="sbg-employee-claim-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:150px"></td>
				<td style="width:200px"></td>
			</tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var employee_claim_url = "<?php echo Util::convertLink("EmployeeClaim") ; ?>" ;
<?php 
	include (PATH_CODE . "js/claims/employeeclaim.js") ; 
?>
</script>