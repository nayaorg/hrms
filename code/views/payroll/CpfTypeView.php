<div id="sbg-cpftype-entry" style="width:710px;margin: 5px 5px 5px 5px;">
	<div id="cpftype-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#cpftype-general">General</a></li>
			<li><a href="#cpftype-age35">35 and Below</a></li>
			<li><a href="#cpftype-age50">36 to 50</a></li>
			<li><a href="#cpftype-age55">51 to 55</a></li>
			<li><a href="#cpftype-age60">56 to 60</a></li>
			<li><a href="#cpftype-age65">61 to 65</a></li>
			<li><a href="#cpftype-age99">66 and Above</a></li>
			<span class="sbg-tab-title">CPF Type</span>
		</ul>
		<div id="cpftype-general" style="height:210px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtCpfTypeId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtCpfTypeDesc" /><span id="cpftype_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>OW Ceiling : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfTypeOw" /><span>&nbsp;per month</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>AW Ceiling : </span></td>
					<td><input type="text" maxlength="10" size="15" id="txtCpfTypeAw" /><span>&nbsp;per year</td>
				</tr>
			</table>
		</div>
		<div id="cpftype-age35" style="height:210px;">
			<table border="1" cellspacing="0" cellpadding="2">
				<tr>
					<td></td>
					<td colspan="3" style="text-align:center"><span>Employee</span></td>
					<td colspan="3" style="text-align:center"><span>Company</span></td>
				</tr>
				<tr>
					<td><span>Income Group</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
				</tr>
				<tr>
					<td><span>$0 to $50</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_1_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_1_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_1_1" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_1_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_1_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_1_1" /></td>
				</tr>
				<tr>
					<td><span>$50.01 to $500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_1_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_1_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_1_2" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_1_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_1_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_1_2" /></td>
				</tr>
				<tr>
					<td><span>$500.01 to $750</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_1_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_1_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_1_3" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_1_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_1_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_1_3" /></td>
				</tr>
				<tr>
					<td><span>$750.01 to $1200</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_1_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_1_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_1_4" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_1_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_1_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_1_4" /></td>
				</tr>
				<tr>
					<td><span>$1200.01 to $1500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_1_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_1_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_1_5" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_1_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_1_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_1_5" /></td>
				</tr>
				<tr>
					<td><span>$1500 and Above</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_1_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_1_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_1_6" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_1_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_1_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_1_6" /></td>
				</tr>
			</table>
		</div>
		<div id="cpftype-age50" style="height:210px;">
			<table border="1" cellspacing="0" cellpadding="2">
				<tr>
					<td></td>
					<td colspan="3" style="text-align:center"><span>Employee</span></td>
					<td colspan="3" style="text-align:center"><span>Company</span></td>
				</tr>
				<tr>
					<td><span>Income Group</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
				</tr>
				<tr>
					<td><span>$0 to $50</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_2_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_2_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_2_1" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_2_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_2_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_2_1" /></td>
				</tr>
				<tr>
					<td><span>$50.01 to $500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_2_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_2_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_2_2" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_2_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_2_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_2_2" /></td>
				</tr>
				<tr>
					<td><span>$500.01 to $750</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_2_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_2_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_2_3" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_2_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_2_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_2_3" /></td>
				</tr>
				<tr>
					<td><span>$750.01 to $1200</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_2_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_2_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_2_4" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_2_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_2_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_2_4" /></td>
				</tr>
				<tr>
					<td><span>$1200.01 to $1500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_2_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_2_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_2_5" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_2_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_2_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_2_5" /></td>
				</tr>
				<tr>
					<td><span>$1500 and Above</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_2_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_2_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_2_6" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_2_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_2_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_2_6" /></td>
				</tr>
			</table>
		</div>
		<div id="cpftype-age55" style="height:210px;">
			<table border="1" cellspacing="0" cellpadding="2">
				<tr>
					<td></td>
					<td colspan="3" style="text-align:center"><span>Employee</span></td>
					<td colspan="3" style="text-align:center"><span>Company</span></td>
				</tr>
				<tr>
					<td><span>Income Group</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
				</tr>
				<tr>
					<td><span>$0 to $50</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_3_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_3_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_3_1" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_3_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_3_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_3_1" /></td>
				</tr>
				<tr>
					<td><span>$50.01 to $500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_3_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_3_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_3_2" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_3_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_3_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_3_2" /></td>
				</tr>
				<tr>
					<td><span>$500.01 to $750</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_3_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_3_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_3_3" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_3_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_3_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_3_3" /></td>
				</tr>
				<tr>
					<td><span>$750.01 to $1200</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_3_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_3_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_3_4" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_3_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_3_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_3_4" /></td>
				</tr>
				<tr>
					<td><span>$1200.01 to $1500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_3_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_3_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_3_5" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_3_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_3_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_3_5" /></td>
				</tr>
				<tr>
					<td><span>$1500 and Above</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_3_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_3_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_3_6" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_3_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_3_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_3_6" /></td>
				</tr>
			</table>
		</div>
		<div id="cpftype-age60" style="height:210px;">
			<table border="1" cellspacing="0" cellpadding="2">
				<tr>
					<td></td>
					<td colspan="3" style="text-align:center"><span>Employee</span></td>
					<td colspan="3" style="text-align:center"><span>Company</span></td>
				</tr>
				<tr>
					<td><span>Income Group</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
				</tr>
				<tr>
					<td><span>$0 to $50</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_4_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_4_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_4_1" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_4_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_4_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_4_1" /></td>
				</tr>
				<tr>
					<td><span>$50.01 to $500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_4_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_4_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_4_2" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_4_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_4_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_4_2" /></td>
				</tr>
				<tr>
					<td><span>$500.01 to $750</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_4_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_4_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_4_3" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_4_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_4_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_4_3" /></td>
				</tr>
				<tr>
					<td><span>$750.01 to $1200</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_4_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_4_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_4_4" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_4_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_4_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_4_4" /></td>
				</tr>
				<tr>
					<td><span>$1200.01 to $1500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_4_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_4_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_4_5" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_4_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_4_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_4_5" /></td>
				</tr>
				<tr>
					<td><span>$1500 and Above</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_4_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_4_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_4_6" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_4_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_4_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_4_6" /></td>
				</tr>
			</table>
		</div>
		<div id="cpftype-age65" style="height:210px;">
			<table border="1" cellspacing="0" cellpadding="2">
				<tr>
					<td></td>
					<td colspan="3" style="text-align:center"><span>Employee</span></td>
					<td colspan="3" style="text-align:center"><span>Company</span></td>
				</tr>
				<tr>
					<td><span>Income Group</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
				</tr>
				<tr>
					<td><span>$0 to $50</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_5_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_5_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_5_1" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_5_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_5_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_5_1" /></td>
				</tr>
				<tr>
					<td><span>$50.01 to $500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_5_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_5_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_5_2" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_5_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_5_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_5_2" /></td>
				</tr>
				<tr>
					<td><span>$500.01 to $750</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_5_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_5_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_5_3" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_5_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_5_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_5_3" /></td>
				</tr>
				<tr>
					<td><span>$750.01 to $1200</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_5_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_5_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_5_4" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_5_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_5_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_5_4" /></td>
				</tr>
				<tr>
					<td><span>$1200.01 to $1500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_5_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_5_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_5_5" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_5_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_5_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_5_5" /></td>
				</tr>
				<tr>
					<td><span>$1500 and Above</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_5_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_5_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_5_6" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_5_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_5_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_5_6" /></td>
				</tr>
			</table>
		</div>
		<div id="cpftype-age99" style="height:210px;">
			<table border="1" cellspacing="0" cellpadding="2">
				<tr>
					<td></td>
					<td colspan="3" style="text-align:center"><span>Employee</span></td>
					<td colspan="3" style="text-align:center"><span>Company</span></td>
				</tr>
				<tr>
					<td><span>Income Group</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
					<td><span>Fix Amount</span></td>
					<td><span>CPF Rate</span></td>
					<td><span>Offset</span></td>
				</tr>
				<tr>
					<td><span>$0 to $50</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_6_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_6_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_6_1" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_6_1" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_6_1" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_6_1" /></td>
				</tr>
				<tr>
					<td><span>$50.01 to $500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_6_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_6_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_6_2" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_6_2" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_6_2" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_6_2" /></td>
				</tr>
				<tr>
					<td><span>$500.01 to $750</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_6_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_6_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_6_3" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_6_3" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_6_3" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_6_3" /></td>
				</tr>
				<tr>
					<td><span>$750.01 to $1200</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_6_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_6_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_6_4" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_6_4" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_6_4" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_6_4" /></td>
				</tr>
				<tr>
					<td><span>$1200.01 to $1500</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_6_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_6_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_6_5" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_6_5" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_6_5" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_6_5" /></td>
				</tr>
				<tr>
					<td><span>$1500 and Above</span></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpFix_6_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeEmpRate_6_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeEmpOff_6_6" /></td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyFix_6_6" /></td>
					<td><input type="text" maxlength="8" size="6" id="txtCpfTypeCoyRate_6_6" />%</td>
					<td><input type="text" maxlength="10" size="8" id="txtCpfTypeCoyOff_6_6" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="cpftype_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnCpfTypeAdd" type="button" value="Add"></input>
			<input id="btnCpfTypeClear" type="button" value="Clear"></input>
			<input id="btnCpfTypeUpdate" type="button" value="Update"></input>
			<input id="btnCpfTypePrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-cpftype-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-cpftype-data" style="overflow: auto;">
            <table id="sbg-cpftype-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var cpftype_url = "<?php echo Util::convertLink("CpfType") ; ?>" ;
<?php include (PATH_CODE . "js/payroll/cpftype.min.js") ; ?>
</script>