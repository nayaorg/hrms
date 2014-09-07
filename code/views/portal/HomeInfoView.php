<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css">
<style type="text/css">
h2 {
	margin: 0;
	color: #666;
	padding-top: 90px;
	font-size: 25px;
	font-family: "trebuchet ms", sans-serif;
}

.item {
	background: #333;
	text-align: center;
	height: 150px !important;
}

.carousel {
	margin-top: 0px;
}

.bs-example {
	margin: 0px;
}
</style>
</head>
<body>



	<div class="row">
		<div class="col-md-10">
			<!-- Default box -->
			<div class="row">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">Expense</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-default btn-sm" data-widget="collapse"
								data-toggle="tooltip" title="Collapse">
								<i class="fa fa-minus"></i>
							</button>
							<button class="btn btn-default btn-sm" data-widget="remove"
								data-toggle="tooltip" title="Remove">
								<i class="fa fa-times"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<?php include 'HomeInfoExpenseView.php';?>
					</div>
				</div>
				<!-- /.box -->
			</div>


			<div class="row">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">Leave</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-default btn-sm" data-widget="collapse"
								data-toggle="tooltip" title="Collapse">
								<i class="fa fa-minus"></i>
							</button>
							<button class="btn btn-default btn-sm" data-widget="remove"
								data-toggle="tooltip" title="Remove">
								<i class="fa fa-times"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<?php include 'HomeInfoLeaveView.php';?>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<code>.box-footer</code>
					</div>
					<!-- /.box-footer-->
				</div>
			</div>
		</div>
		<!-- /.col -->

		<div class="col-xs-6">
			
				<div class="small-box bg-green">
					<div class="inner">
						<h3>
							53<sup style="font-size: 20px">%</sup>
						</h3>
						<p>Bounce Rate</p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
					<a href="#" class="small-box-footer"> More info <i
						class="fa fa-arrow-circle-right"></i>
					</a>
				</div>
			
				<div class="bs-example">
					<div id="myCarousel" class="carousel slide" data-interval="3000"
						data-ride="carousel">

						<ol class="carousel-indicators">
							<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
							<li data-target="#myCarousel" data-slide-to="1"></li>
							<li data-target="#myCarousel" data-slide-to="2"></li>
							<li data-target="#myCarousel" data-slide-to="3"></li>
							<li data-target="#myCarousel" data-slide-to="4"></li>
						</ol>

						<div class="carousel-inner">
							<div class="active item">
								<h2>Slide 1</h2>
								<div class="carousel-caption">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
								</div>
							</div>

							<div class="item">
								<h2>Slide 2</h2>
								<div class="carousel-caption">
									<p>Aliquam sit amet gravida nibh, facilisis gravida odio.</p>
								</div>
							</div>

							<div class="item">
								<h2>Slide 3</h2>
								<div class="carousel-caption">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
								</div>
							</div>

							<div class="item">
								<h2>Slide 4</h2>
								<div class="carousel-caption">
									<p>Aliquam sit amet gravida nibh, facilisis gravida odio.</p>
								</div>
							</div>

							<div class="item">
								<h2>Slide 5</h2>
								<div class="carousel-caption">
									<p>Praesent commodo cursus magna, vel scelerisque nisl
										consectetur.</p>
								</div>
							</div>

						</div>

						<a class="carousel-control left" href="#myCarousel"
							data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span>
						</a> <a class="carousel-control right" href="#myCarousel"
							data-slide="next"> <span
							class="glyphicon glyphicon-chevron-right"></span>
						</a>
					</div>
				</div>
			</div>
		
		<!-- ./col -->
	</div>
	<!-- /.row -->


</body>
</html>

