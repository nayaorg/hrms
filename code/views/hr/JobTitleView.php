<div id="sbg-job-entry" style="width:610px;margin: 5px 5px 5px 5px;">
	<div id="job-tabs">
		<ul style="font-size: 0.8em;">
			<li><a href="#job-general">General</a></li>
			<span class="sbg-tab-title">Job Title Master</span>
		</ul>
		<div id="job-general" style="height:100px;">
			<table>
				<tr>
					<td style="text-align:right;width:100px;"><span>ID : </span></td>
					<td style="width:480px"><input type="text" maxlength="10" size="10" id="txtJobId" value="Auto" disabled="disabled" /></td>
				</tr>
				<tr>
					<td style="text-align:right;"><span>Description : </span></td>
					<td><input type="text" maxlength="30" size="40" id="txtJobDesc" /><span id="job_err_desc" style="color:red;display:none;padding-left:5px">*</span></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ui-widget-content ui-corner-all" style="height:40px;margin-top:2px;">
		<div class="sbg-entry-error" style="width:300px;">
			<span id="job_err_mesg" class="sbg-error"></span>
		</div>
		<div class="sbg-entry-command">
			<input id="btnJobAdd" type="button" value="Add"></input>
			<input id="btnJobClear" type="button" value="Clear"></input>
			<input id="btnJobUpdate" type="button" value="Update"></input>
			<input id="btnJobPrint" type="button" value="Print"></input>
		</div>
	</div>
</div>

<div>
	<div class="sbg-table">
		<div class="ui-widget-header">
			<table id="sbg-job-header" class="header" cellspacing="0" cellpadding="5">
                <tr class="ui-widget-header">
                <td style="width: 50px;">ID</td>
                <td style="width: 400px;">Description</td>
				<td style="width: 25px;"></td>
				<td style="width: 25px;"></td>
                </tr>
			</table>
		</div>
        <div id="sbg-job-data" style="overflow: auto;">
            <table id="sbg-job-table" cellspacing="0" cellpadding="5" class="data">
			<tr><td style="width:50px;height:1px"></td><td style="width:400px"></td><td style="width:25px"></td><td style="width:25px"></td></tr>
			<?php echo $this->getList() ; ?>
            </table>
        </div>
	</div>
</div> 
<script type="text/javascript">
var job_url = "<?php echo Util::convertLink("JobTitle") ; ?>" ;
<?php include (PATH_CODE . "js/hr/jobtitle.min.js") ; ?>
</script>