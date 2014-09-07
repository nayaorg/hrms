
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv='Pragma' content='no-cache'>
	<meta http-equiv='Expires' content='Sat, 01 Jan 2000 06:00:00 GMT'>
	<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate, max-age=0'>
    <title>SBG HRMS Staff Portal Signin</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/sbg-util.min.js"></script>
</head>
<body style="padding-top:20px;">
<form accept-charset="UTF-8" id="sbg-main" name="sbg-main" method="post" action="">
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="panel-title">Please sign in</div>
					</div>
					<div class="panel-body">
						<div style="margin-bottom:15px" class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
			    		    <input class="form-control" placeholder="Staff ID/Code" name="txtName" id="txtName" type="text"></input>
			    		</div>
			    		<div style="margin-bottom:15px;" class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
			    			<input class="form-control" placeholder="Password" name="txtPwd" id="txtPwd" type="password" value=""></input>
			    		</div>
						<span id="err_mesg" class="has-error"></span>
			    		<input id="btnLogin" name="btnLogin" class="btn btn-lg btn-primary btn-block" type="button" value="Login"></input>
						
						<div class="checkbox pull-left">
							<label>
								<input type="checkbox" value="remember-me">Remember Me</input>
							</label>
						</div>
						<a class="pull-right" style="margin-top:10px;" href="#">Forgot password</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
var login_url =  "<?php echo Util::convertLink("Signin") ; ?>" ;
<?php include (PATH_CODE . "js/portal/signin.js") ; ?>
</script>
</body>
</html>
