<div id="sbg-emp-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="emp-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#emp-general">General</a></li>
			<li><a href="#emp-profile">Profile</a></li>
			<li><a href="#emp-contact">Address</a></li>
			<li><a href="#emp-remarks">Remarks</a></li>
			<span class="sbg-tab-title">Employee Master</span>
		</ul>
		<div id="emp-general" style="height:220px">
			<table>
				<tr>
					<td style="text-align:right;width:180px"><span>ID : </span></td>
					<td style="width:300px"><input type="text" maxlength="12" size="12" id="txtEmpId" value="Auto" disabled="disabled" /></td>
					<td style="text-align:right;width:120px"><span>Code : </span></td>
					<td style="width:480px"><input type="text" maxlength="20" size="20" id="txtCode" /><span id="emp_err_code" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Name : </span></td>
					<td colspan="3"><input type="text" maxlength="50" size="50" id="txtEmpName" /><span id="emp_err_name" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Company : </span></td>
					<td colspan="3"><select id="cobEmpCoy"><?php echo $this->getCompany() ; ?></select>
						<span id="emp_err_coy" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Type : </span></td>
					<td colspan="3"><select id="cobEmpType"><?php echo $this->getEmployeeType() ; ?></select>
						<span id="emp_err_type" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Department : </span></td>
					<td colspan="3"><select id="cobEmpDept"><?php echo $this->getDepartment() ; ?></select>
						<span id="emp_err_dept" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Job Title : </span></td>
					<td colspan="3"><select id="cobEmpJob"><?php echo $this->getJobTitle() ; ?></select>
						<span id="emp_err_job" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Work Permit : </span></td>
					<td colspan="3"><select id="cobEmpPermit"><?php echo $this->getWorkPermit() ; ?></select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Date Joined : </span></td>
					<td colspan="3"><input type="text" value="" maxlength="10" size="12" id="txtEmpJoin" />
						<span id="emp_err_join" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Date Resign : </span></td>
					<td colspan="3"><input type="text" value="" maxlength="10" size="12" id="txtEmpResign" />
						<span id="emp_err_resign" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
		<div id="emp-profile" style="height:220px">
			<table>
				<tr>
					<td style="text-align:right;width:120px"><span>ID No : </span></td>
					<td style="width:480"><input type="text" maxlength="30" size="30" id="txtEmpNric" /><span id="emp_err_nric" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;width:120px"><span>ID Type : </span></td>
					<td><select id="cobEmpIdType"><?php echo $this->getIdType() ; ?></select>
						<span id="emp_err_idtype" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Gender : </span></td>
					<td><select id="cobEmpGender"><?php echo $this->getGender() ; ?></select></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Marital Status : </span></td>
					<td><select id="cobEmpMarital"><?php echo $this->getMarital() ; ?></select></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Race : </span></td>
					<td><select id="cobEmpRace"><?php echo $this->getRace() ; ?></select>
						<span id="emp_err_race" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Nationality : </span></td>
					<td><select id="cobEmpNat"><?php echo $this->getNationality() ; ?></select>
						<span id="emp_err_nat" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Date of Birth : </span></td>
					<td><input type="text" value="" maxlength="10" size="12" id="txtEmpDob" />
						<span id="emp_err_dob" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
			</table>
		</div>
		<div id="emp-contact" style="height:220px">
			<table>
				<tr>
					<td style="text-align:right;width:120px"><span>Block/House No : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="12" id="txtEmpHouseNo" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Street Name : </span></td>
					<td><input type="text" maxlength="32" size="35" id="txtEmpStreet" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Level/Unit No : </span></td>
					<td>&nbsp;#&nbsp;<input type="text" maxlength="2" size="4" id="txtEmpLevel" />
					&nbsp;-&nbsp;<input type="text" maxlength="5" size="8" id="txtEmpUnitNo" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Postal Code : </span></td>
					<td><input type="text" maxlength="6" size="8" id="txtEmpPostal" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Tel No : </span></td>
					<td><input type="text" maxlength="20" size="20" id="txtEmpTel" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Mobile No : </span></td>
					<td><input type="text" value="" maxlength="20" size="20" id="txtEmpMobile" /></td>
				</tr>
				<tr>
					<td style="text-align:right"><span>Email : </span></td>
					<td><input type="text" maxlength="80" size="60" id="txtEmpEmail" /></td>
				</tr>
			</table>
		</div>
		<div id="emp-remarks" style="height:220px">
			<table>
				<tr>
					<td><textarea id="txtEmpRmks" style="height:200px;width:550px"></textarea></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="emp_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnEmpAdd" type="button" value="Add"></input>
			<input id="btnEmpClear" type="button" value="Clear"></input>
			<input id="btnEmpUpdate" type="button" value="Update"></input>
			<input id="btnEmpPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>
<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-emp-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 100px;">Code</td>
                <td style="width: 200px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 200px;">Department</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-emp-data" style="overflow: auto;">
            <table id="sbg-emp-table" cellspacing="0" cellpadding="5" class="data">
				<tr>
				<td style="width:50px;height:1px"></td>
				<td style="width:100px"></td>
				<td style="width:200px"></td>
				<td style="width:200px"></td>
				<td style="width:200px"></td>
				<td style="width:25px"></td>
				<td style="width:25px"></td>
				</tr>
				<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var emp_url = "<?php echo Util::convertLink("Employee") ; ?>" ;
<?php include (PATH_CODE . "js/hr/employee.min.js") ; ?>
</script>