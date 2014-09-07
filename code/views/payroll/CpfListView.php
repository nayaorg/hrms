<div style="margin-top:5px">
	<div id="sbg-cpflist-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Month : &nbsp;&nbsp;</span><select id="cobCpfListMonth"><?php echo Util::getMonthOption() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Year : &nbsp;&nbsp;</span><select id="cobCpfListYear">><?php echo Util::getYearOption() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Company :&nbsp;&nbsp;</span><select id="cobCpfListCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<input id="btnCpfListView" type="button" value="View" style="margin-left:20px"></input>
		<input id="btnCpfListPrint" type="button" value="Print"></input>
		<input id="btnCpfListExport" type="button" value="Export"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header" style="height:40px;">
			<table id="sbg-cpflist-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 150px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 80px;">Total Wages</td>
				<td style="width: 80px;">Fund/Levy</td>
				<td style="width: 80px;">CPF Employee</td>
				<td style="width: 80PX;">CPF Employer</td>
				<td style="width: 80px;">Total CPF</td>
                </tr>
			</table>
		</div>
        <div id="sbg-cpflist-data" style="overflow: auto;">
            <table id="sbg-cpflist-table" cellspacing="0" cellpadding="5" class="data">
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
var cpflist_url = "<?php echo Util::convertLink("CpfList") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/cpflist.min.js") ; ?>
</script>