<div style="margin-top:5px">
	<div id="sbg-paylist-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Pay Date : &nbsp;&nbsp;</span>
		<select id="cobPayListMonth"><?php echo Util::getMonthOption() ; ?></select>&nbsp;-&nbsp;
		<select id="cobPayListYear">><?php echo Util::getYearOption() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Company :&nbsp;&nbsp;</span><select id="cobPayListCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Department :&nbsp;&nbsp;</span><select id="cobPayListDept"><?php echo $this->getDepartment() ; ?></select>
		<input id="btnPayListView" type="button" value="View" style="margin-left:20px"></input>
		<input id="btnPayListPrint" type="button" value="Print"></input>
		<input id="btnPayListExport" type="button" value="Bank Export"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-paylist-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 80px;">Income</td>
				<td style="width: 80px;">Deductions</td>
				<td style="width: 80px;">Fund/Levy</td>
				<td style="width: 80px;">CPF Emp</td>
				<td style="width: 80px;">Net Pay</td>
                </tr>
			</table>
		</div>
        <div id="sbg-paylist-data" style="overflow: auto;">
            <table id="sbg-paylist-table" cellspacing="0" cellpadding="5" class="data">
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
var paylist_url = "<?php echo Util::convertLink("PayList") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/paylist.min.js") ; ?>
</script>