
<head>
<link href="css/expense.css" rel="stylesheet">
<script type="text/javascript" src="js/claim.js"></script>
<style type="text/css">
.table {
	width: 100%;
	max-width: 100%;
	margin-bottom: 20px;
}
</style>
</head>

<div class="col-md-11" style="margin-top: 20px">
	<div class="filter-unit">
		<input  style="height: 30px;"  type="text" placeholder="From Date"
			id="fromDate">
	</div>

	<div class="filter-unit">
		<input style="height: 30px;" type="text" placeholder="To Date"
			id="toDate">
	</div>
	
	<div class="filter-unit">
		<a href="#" onclick="return filter();" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Filter</a>
	</div>
	
	<div class="add-claim-btn">
		<a href="<?php echo $this->createMenuFunc("PortalClaim","Add Claim/Expense", PORTAL_CLAIM_ADD_VIEW)?>" onclick="return changeMainHeaderAddClaim();"
		   class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Add</a>
	</div>
</div>

<div class="col-md-11" id="errorShow"></div>

<div class="col-md-11" style="margin-top: 20px">
	<div class="box" id="testData">
		<table class="table table-striped" id="expense-table">
			<thead>
				<tr>
					<th width="20%">Description</th>
					<th width="20%">Type</th>
					<th width="20%">Amount</th>
					<th width="20%">Status</th>
					<th width="20%">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$list = $this->getClaimList ("","");
				foreach ( $list as $expenseItem ) {
					?>
					<tr>
					<td><?php echo $expenseItem['desc'];?></td>
					<td><?php echo $expenseItem['type'];?></td>
					<td><?php echo $expenseItem['amount'];?></td>
					<td><?php echo $expenseItem['status'];?></td>
					<td><?php echo $expenseItem['date'];?></td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php $arrNav = $this->getAddClaimNavigation();?>
<script>
	function changeMainHeaderAddClaim() {
		var navNameArr = <?php echo json_encode($arrNav[0])?>;
		var navURLArr  = <?php echo json_encode($arrNav[1])?>;
		var navMenuActiveLArr  = <?php echo json_encode($arrNav[2])?>;
		changeMainHeader(navNameArr, navURLArr, navMenuActiveLArr);
	}
</script>


