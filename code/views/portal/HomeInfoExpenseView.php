

<table class="table table-striped">
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
		$list = $this->getClaimList ("", "",NUM_CLAIM_SHOW);
		
		foreach ( $list as $expenseItem ) {
			?>
			<tr>
			<td><?php echo $expenseItem['desc'];?></td>
			<td><?php echo $expenseItem['type']?></td>
			<td><?php echo $expenseItem['amount']?></td>
			<td><?php echo $expenseItem['status']?></td>
			<td><?php echo $expenseItem['date']?></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>