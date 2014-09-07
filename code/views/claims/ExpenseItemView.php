<div id="sbg-expense-item-entry" style="width:550px;margin: 5px 5px 5px 5px;">
	<div id="expense-item-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#expense-item-header">General</a></li>
			<li><a href="#expense-item_limit">Claim Limit</a></li>
			<span class="sbg-tab-title">Expense Item</span>
		</ul>
		
		<div id="expense-item-header" style="height:auto;">
			<table>
				<tr>
					<td style="text-align:right;"><span>ID : </span></td>
					<td>
						<input type="text" id="txtExpenseItemId" size="10" value="Auto" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td>
						<input type="text" maxlength="50" size="50" id="txtExpenseItemDesc" />
						<span id="expense_item_err_desc" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Group : </span></td>
					<td>
						<select id="cobExpenseItemGroup"><?php echo $this->getExpenseGroup() ; ?></select>
						<span id="expense_item_err_group" style="color:red;display:none;padding-left:5px">*</span>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Ref : </span></td>
					<td>
						<input type="text" maxlength="10" size="10" id="txtExpenseItemRef" />
					</td>
				</tr>
			</table>
		</div>
		<div id="expense-item_limit" style="height:auto;">
			<table>
				<tr>
					<td style="width:30px"></td>
					<td style="width:200px">Claim Group<select id="cobExpenseClaimGroup" style="display:none"><?php echo $this->getClaimGroup() ; ?></select></td>
					<td style="width:120px">Limit Type<select id="cobExpenseClaimType" style="display:none"><?php echo $this->getLimitType() ; ?></select></td>
					<td style="width:80px">Amount</td>
					<td style="width:40px;text-align:center"><a href="javascript:" onclick="addClaimLimit()"><img src="image/add.png" title="Add Limit" style="width:24px;height:24px"></img></a></td>
				</tr>
			</table>
			<div style="overflow: auto;height:180px;">
				<table id="tblLimit" class="sbg-table-list" cellspacing="0">
					
				</table>
			</div>
		</div>
		
		<div class="ui-widget-content ui-corner-all" style="height:50px;margin-top:2px;">
			<div class="sbg-entry-error" style="width:300px;">
				<span id="expense_item_err_mesg" class="sbg-error"></span>
			</div>
			<div class="sbg-entry-command">
				<input id="btnExpenseItemAdd" type="button" value="Add"></input>
				<input id="btnExpenseItemUpdate" type="button" value="Update"></input>
				<input id="btnExpenseItemClear" type="button" value="Clear"></input>
				<input id="btnExpenseItemPrint" type="button" value="Print"></input>
			</div>
		</div>
	</div>
	
</div>

<div id="expense-item-header-content" class="sbg-table">
	<div class="ui-widget-header">
		<table id="sbg-expense-item-header" class="header" cellspacing="0" cellpadding="5">
			<tr class="ui-widget-header">
				<td style="width: 50px;">ID</td>
                <td style="width: 200px;">Description</td>
				<td style="width: 100px;">Group</td>
                <td style="width: 80px;">Ref</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
			</tr>
		</table>
	</div>
	<div id="sbg-expense-item-data" style="overflow: auto;">
		<table id="sbg-expense-item-table" cellspacing="0" cellpadding="5" class="data">
		<tr><td style="width:50px;height:1px"></td>
			<td style="width:200px"></td>
			<td style="width:100px"></td>
			<td style="width:80px"></td>
			<td style="width:25px"></td>
			<td style="width:25px"></td>
		</tr>
		<?php echo $this->getList() ; ?>
		</table>
	</div>
</div>

<script type="text/javascript">
var expense_item_url = "<?php echo Util::convertLink("ExpenseItem") ; ?>" ;
<?php include (PATH_CODE . "js/claims/expenseitem.js") ;?>
</script>