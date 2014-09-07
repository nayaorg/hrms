<div id="disciplinary-title" style="padding-top:10px;padding-bottom:10px">
	<font size="3" ><center>Disciplinary Report</center></font>
</div>
<div style="margin-top:5px">
	<div id="sbg-disciplinary-option" class="ui-widget-content ui-corner-all" style="height:auto;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<table>
			<tr>
				<td style="vertical-align:top">
					<table>
						<tr>
							<td style="text-align:right"><span>Date : </span></td>
							<td ><input type="text" id="txtDateReportBegin" size="10" width="10"/> - <input type="text" id="txtDateReportEnd" size="10"/></td>
						</tr>
						<tr>
							<td><span>Type : </span></td>
							<td>
								<select id="cboType">
									<?php echo $this->getDisciplinaryType() ; ?>
								</select>
							</td>
						</tr>
					</table>
				</td>
				<td style="vertical-align:top">
					<table>
						<tr>
							<td>
								<span>Dept. : </span>
								<select id="cobDepartment"><?php echo $this->getDepartment() ; ?></select> &nbsp;
							</td>
						</tr>
					</table>
				</td>
				<td style="vertical-align:top">
					<div>
						<input id="btnDisciplinaryView" type="button" value="View"></input>
						<input id="btnDisciplinaryPrint" type="button" value="Print"></input>
						<input id="btnDisciplinaryExport" type="button" value="Export"></input>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-disciplinary-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;" onclick='sort_table(0)'>ID</td>
                <td style="width: 150px;" onclick='sort_table(1)'>Name</td>
                <td style="width: 80px;" onclick='sort_table(2)'>In</td>
				<td style="width: 80px;" onclick='sort_table(3)'>Late In</td>
				<td style="width: 80px;" onclick='sort_table(4)'>Out</td>
				<td style="width: 80px;" onclick='sort_table(5)'>Late Out</td>
				<td style="width: 80px;" onclick='sort_table(6)'>Remarks</td>
                </tr>
			</table>
		</div>
        <div id="sbg-disciplinary-data" style="overflow: auto;">
            <table id="sbg-disciplinary-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td>
				<td style="width:150px"></td>
				<td style="width: 80px"></td>
				<td style="width: 80px"></td>
				<td style="width: 80px;"></td>
				<td style="width: 80px;"></td>
				<td style="width: 80px;"></td>
			</tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var disciplinary_url = "<?php echo Util::convertLink("Disciplinary") ; ?>" ;
<?php 
	include (PATH_CODE . "js/attendance/disciplinary.min.js") ; 
?>
</script>