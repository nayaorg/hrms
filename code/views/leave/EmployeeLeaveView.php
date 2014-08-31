<div id="sbg-empleave-entry" style="margin: 5px 5px 5px 5px;width:610px;">
	<div id="empleave-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#empleave-general">General</a></li>
			<span class="sbg-tab-title">Employee Leave Setup</span>
		</ul>
		<div id="empleave-general" style="height:180px">
			<table>
				<tr>
					<td style="text-align:right;width:120px;"><span>Employee : </span></td>
					<td style="width:480px"><span id="lblEmpLeaveIdName"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Company/Dept. : </span></td>
					<td><span id="lblEmpLeaveCoy"></span></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Leave Group : </span></td>
					<td><select id="cobEmpLeaveGroup"><?php echo $this->getGroup() ; ?></select></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="empleave_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnEmpLeaveClear" type="button" value="Clear"></input>
			<input id="btnEmpLeaveSave" type="button" value="Save"></input>
		</div>
	</div>
</div>

<div>
	<div id="sbg-empleave-option" class="ui-widget-content ui-corner-all" style="height:35px;margin-left:5px;margin-right:5px;padding-left:10px;padding-top:5px">
		<span>Company :&nbsp;&nbsp;</span><select id="cobEmpLeaveCoy"><?php echo $this->getCompany() ; ?></select>&nbsp;&nbsp;&nbsp;
		<span>Department :&nbsp;&nbsp;</span><select id="cobEmpLeaveDept"><?php echo $this->getDepartment() ; ?></select>
		<input id="btnEmpLeaveList" type="button" value="Get Employee List" style="margin-left:20px"></input>
	</div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-empleave-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 200px;">Name</td>
                <td style="width: 200px;">Company</td>
				<td style="width: 150px;">Department</td>
				<td style="width: 20px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-empleave-data" style="overflow: auto;">
            <table id="sbg-empleave-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:200px"></td><td style="width:200px"></td><td style="width:150px"></td><td style="width:20px"></td></tr>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var empleave_url = "<?php echo Util::convertLink("EmployeeLeave") ; ?>" ;
<?php include (PATH_CODE . "js/leave/employeeleave.min.js") ; ?>
</script>