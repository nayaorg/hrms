<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head><title>SBG HRMS Staff Portal</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<meta http-equiv='Pragma' content='no-cache'>
	<meta http-equiv='Expires' content='Sat, 01 Jan 2000 06:00:00 GMT'>
	<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate, max-age=0'>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="css/floating.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/datepicker.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/sbg-util.js"></script>
	<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
	
<!--	<script type="text/javascript" src="js/home.js"></script>   -->
</head>

<body class="skin-blue">
    <header class="header">
		<img src="image/logo.png" class="logo" alt="Company Logo" />
        <nav class="navbar  navbar-static-top" role="navigation">    
            <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
			
			<div class="navbar-left">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-envelope"></i>
							<span class="label label-success">4</span>
						</a>
					</li>
                    
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-comments"></i>
							<span class="label label-warning">10</span>
						</a>    
					</li>
                        
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-tasks"></i>
							<span class="label label-danger">9</span>
						</a>    
					</li>
				</ul>
			</div>        
			<div class="navbar-right">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag"></i>
                            <span>Language</span>
                        </a>    
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-power-off"></i>
                            <span>Sign Out</span>
                        </a>
                  
                    </li>
                </ul>
            </div>
        </nav>
    </header>
	<div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas"  role="navigation">                
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="image/staff_image.png" class="img-circle" alt="Staff Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Employee Name</p>
                            <a href="#">My Profile</a>
                        </div>
                    </div>
                    
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a onclick="return changeMainHeaderHome();" 
                            	href="<?php echo $this->createMenuFunc("Home","Home Master", PORTAL_HOME)?>">
                                <i class="fa fa-home"></i> <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a  onclick="return changeMainHeaderClaim();"  
                            	href="<?php echo $this->createMenuFunc("Home","Home Master", PORTAL_CLAIM)?>">
                                <i class="fa fa-usd"></i> <span>My Expenses/Claims</span> <small class="badge pull-right bg-green">new</small>
                            </a>
                        </li>
                        <li>
                            <a  onclick="return changeMainHeaderLeave();" 
                            	href="<?php echo $this->createMenuFunc("Home","Home Master", PORTAL_LEAVES)?>">
                                <i class="fa fa-calendar-o"></i> <span>My Leave</span> <small class="badge pull-right bg-green">new</small>
                            </a>
                        </li>
                        <li>
                            <a onclick="return changeMainHeaderCalendar()" 
                            	href="<?php echo $this->createMenuFunc("Home","Home Master", PORTAL_CALENDAR)?>">
                                <i class="fa fa-calendar"></i> <span>Calendar</span>
                                <small class="badge pull-right bg-red">3</small>
                            </a>
                        </li>
                   
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">                
                <!-- Content Header (Page header) -->
                <section class="content-header" id="main-header">
                    <h1>Dashboard<small>My Views</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $this->createMenuFunc("Home","Home Master", PORTAL_HOME)?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <!-- <div><h1>content division</h1></div> -->
					<?php $this->processClaimInfo(); //$this->processHomeInfo();?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php 
	$arrHomeNav  	= $this->getHomeNavigation();
	$arrClaimNav 	= $this->getClaimNavigation();
	$arrLeaveNav 	= $this->getLeaveNavigation();
	$arrCalendarNav = $this->getCalendarNavigation();
?>

<script type ="text/javascript">
var home_url  = "<?php echo Util::convertLink("Home") ; ?>" ;
var claim_url = "<?php echo Util::convertLink("PortalClaim") ; ?>" ;
<?php  include (PATH_CODE . "js/portal/home.js") ; ?>

function changeMainHeaderHome() {
	changeMainHeader(<?php echo json_encode($arrHomeNav[0])?>, 
					 <?php echo json_encode($arrHomeNav[1])?>, 
					 <?php echo json_encode($arrHomeNav[2])?>);
}

function changeMainHeaderClaim() {
	changeMainHeader(<?php echo json_encode($arrClaimNav[0])?>, 
					 <?php echo json_encode($arrClaimNav[1])?>, 
					 <?php echo json_encode($arrClaimNav[2])?>);
}

function changeMainHeaderLeave() {
	changeMainHeader(<?php echo json_encode($arrLeaveNav[0])?>, 
					 <?php echo json_encode($arrLeaveNav[1])?>, 
					 <?php echo json_encode($arrLeaveNav[2])?>);
}

function changeMainHeaderCalendar() {
	changeMainHeader(<?php echo json_encode($arrCalendarNav[0])?>, 
					 <?php echo json_encode($arrCalendarNav[1])?>, 
					 <?php echo json_encode($arrCalendarNav[2])?>);
}
</script>
 
</body>
</html>
