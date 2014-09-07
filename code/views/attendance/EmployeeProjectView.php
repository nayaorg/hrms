<div id="employee-project-title" style="padding-top:10px;padding-bottom:10px">
	<font size="3" ><center>Employee Project Report</center></font>
</div>
<div style="margin-top:5px">
	<div id="sbg-employee-project-option" class="ui-widget-content ui-corner-all" style="height:auto;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<table>
			<tr>
				<td style="vertical-align:top">
					<table>
						<tr>
							<td style="text-align:right"><span>Date : </span></td>
							<td ><input type="text" id="txtDateReportBegin" size="8" width="10"/> - <input type="text" id="txtDateReportEnd" size="8" width="10"/></td>
						</tr>
						<tr>
							<td><span>Project : </span></td>
							<td>
								<select id="cboProject">
									<?php echo $this->getProjectList() ; ?>
								</select>
							</td>
						</tr>
					</table>
				</td>
				<td style="vertical-align:top">
					<table>
						<tr>
							<td><span>Dept. :</span></td>
							<td><select id="cobDepartment"><?php echo $this->getDepartment() ; ?></select> &nbsp;&nbsp;&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="vertical-align:top">
					<div>
						<input id="btnEmployeeProjectView" type="button" value="View"></input>
						<input id="btnEmployeeProjectPrint" type="button" value="Print"></input>
						<input id="btnEmployeeProjectExport" type="button" value="Export"></input>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-employee-project-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 100px;">Rate Type</td>
                <td style="width: 100px;">Work Time</td>
                <td style="width: 100px;">Rate</td>
				<td style="width: 150px;">Cost</td>
                </tr>
			</table>
		</div>
        <div id="sbg-employee-project-data" style="overflow: auto;">
            <table id="sbg-employee-project-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:100px;height:1px"></td>
				<td style="width:100px"></td>
				<td style="width:100px"></td>
				<td style="width:150px"></td>
			</tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var employee_project_url = "<?php echo Util::convertLink("EmployeeProject") ; ?>" ;
<?php 
	include (PATH_CODE . "js/attendance/employeeproject.min.js") ; 
?>
</script>