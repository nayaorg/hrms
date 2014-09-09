<head>
<link href="css/expense.css" rel="stylesheet">
<script type="text/javascript" src="js/claim.js"></script>
<script type="text/javascript" src="js/addClaim.js"></script>
<style type="text/css">
.table {
	width: 100%;
	max-width: 100%;
	margin-bottom: 20px;
}
button {
 	margin-left: 2px;
 	margin-right: 2px;
}
label {
	font-weight: normal !important;
}
</style>
</head>

<!-- Add Claim Header Modal HTML -->
<div id="addClaimHeaderView" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Claim Header</h4>
            </div>
            <div class="modal-body">
	            <div id="modalAddViewContent">
					<?php include 'ClaimAddView.php'; ?>
	        	</div>
            </div>
            <div class="modal-footer">
            	<div id="modalAddViewButtonContent">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	                <button id="btnClaimAdd" class="btn btn-primary" onclick="return addClaim();">Save </button>
			   </div>
            </div>
        </div>
    </div>
</div>

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
		<a href="#addClaimHeaderView" role="button" class="btn btn-primary btn-sm" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add</a>
	</div>
</div>

<div class="col-md-11" id="errorShow"></div>

<div class="col-md-11" style="margin-top: 20px">
	<div class="box" id="testData">
		<table class="table table-striped" id="expense-table">
			<thead>
				<tr>
					<th width="30%">Description</th>
					<th width="10%">Type</th>
					<th width="10%">Amount</th>
					<th width="10%">Status</th>
					<th width="10%">Date</th>
					<th width="30">&nbsp</th>
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
						<td>
							<a role="button" onclick="return editHeader(<?php echo $expenseItem['id'];?>);" id="btnEdit" class="btn btn-primary">Edit Header</a>
							<a role="button" onclick="return uploadDoc(<?php echo $expenseItem['id'];?>);" id="btnUpload" class="btn btn-primary">Upload Doc</a>
							<a role="button" onclick="return editHeader(<?php echo $expenseItem['id'];?>);" id="btnEdit" class="btn btn-primary">Edit Header</a>
							
							
							<button onclick="return test(<?php echo $expenseItem['id'];?>, this);" id="btnEdit" class="btn btn-primary">Test add modal</button>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php //$arrNav = $this->getAddClaimNavigation();?>
<script>
				// delete later
	function changeMainHeaderAddClaim() {
//		var navNameArr = <?php// echo json_encode($arrNav[0])?>;
//		var navURLArr  = <?php// echo json_encode($arrNav[1])?>;
//		var navMenuActiveLArr  = <?php// echo json_encode($arrNav[2])?>;
// 		changeMainHeader(navNameArr, navURLArr, navMenuActiveLArr);
	}
</script>


