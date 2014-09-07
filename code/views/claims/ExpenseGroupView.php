<div id="sbg-expense-group-entry" style="width:500px;margin: 5px 5px 5px 5px;">
	<div id="expense-group-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#expense-group-header">General</a></li>
			<span class="sbg-tab-title">Expense Group</span>
		</ul>
		
		<div id="expense-group-header" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;"><span>ID : </span></td>
					<td>
						<input type="text" id="txtExpenseGroupId" size="10" value="Auto" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Group Name : </span></td>
					<td>
						<input type="text" maxlength="50" size="50" id="txtExpenseGroupDesc" />
						<span id="expense_group_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref : </span></td>
					<td>
						<input type="text" maxlength="10" size="10" id="txtExpenseGroupRef" />
					</td>
				</tr>
			</table>
		</div>
		
		<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
			<div class="sbg-entry-error" style="width:300px;">
				<span id="expense_group_err_mesg" class="sbg-error"></span>
			</div>
			<div class="sbg-entry-command">
				<input id="btnExpenseGroupAdd" type="button" value="Add"></input>
				<input id="btnExpenseGroupUpdate" type="button" value="Update"></input>
				<input id="btnExpenseGroupClear" type="button" value="Clear"></input>
				<input id="btnExpenseGroupPrint" type="button" value="Print"></input>
			</div>
		</div>
	</div>
	
</div>

<div id="expense-group-header-content" class="sbg-table">
	<div class="ui-widget-header">
		<table id="sbg-expense-group-header" class="header" cellspacing="0" cellpadding="5">
			<tr class="ui-widget-header">
				<td style="width: 50px;">ID</td>
				<td style="width: 250px;">Description</td>
				<td style="width: 200px;">Ref</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
			</tr>
		</table>
	</div>
	<div id="sbg-expense-group-data" style="overflow: auto;">
		<table id="sbg-expense-group-table" cellspacing="0" cellpadding="5" class="data">
		<tr><td style="width:50px;height:1px"></td>
			<td style="width:250px"></td>
			<td style="width:200px"></td>
			<td style="width:25px"></td>
			<td style="width:25px"></td>
		</tr>
		<?php echo $this->getList() ; ?>
		</table>
	</div>
</div>

<script type="text/javascript">
var expense_group_url = "<?php echo Util::convertLink("ExpenseGroup") ; ?>" ;

<?php include (PATH_CODE . "js/claims/expensegroup.min.js") ;?>
</script>