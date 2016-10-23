<?php
use Illuminate\Support\Facades\Input;
?>
@extends('masters.master')
	@section('inline_css')
		<style>
			.pagination {
			    display: inline-block;
			    padding-left: 0;
			    padding-bottom:10px;
			    margin: 0px 0;
			    border-radius: 4px;
			}
			.dataTables_wrapper .row:last-child {
			    border-bottom: 0px solid #e0e0e0;
			    padding-top: 5px;
			    padding-bottom: 0px;
			    background-color: #EFF3F8;
			}
			th {
			    white-space: wrap;
			}
			table {
				min-width: 100% !important;
			}
			td {
			    white-space: wrap;
			}
			panel-group .panel {
			    margin-bottom: 20px;
			    border-radius: 4px;
			}
			label{
				text-align: right;
				margin-top: 8px;
			}
			.table {
			    width: 100%;
			    max-width: 100%;
			    margin-bottom: 0px;
			}
			.form-actions {
			    display: block;
			    background-color: #F5F5F5;
			    border-top: 1px solid #E5E5E5;
			    /* margin-bottom: 20px; 
			    margin-top: 20px;
			    padding: 19px 20px 20px;*/
			}
		</style>
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
		<link rel="stylesheet" href="../assets/css/daterangepicker.css" />
	@stop
		
	@stop
	
	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			REPORTS
		</small>
	@stop

	@section('page_content')
		<div class="row col-xs-offset-0 col-xs-12">
			<!-- #section:pages/pricing.large -->
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-dark">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-inverse">BRANCH TRANSACTIONS REPORTS</a></h5>
					</div>
					<?php $jobs = Session::get("jobs");?>
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								
								<?php if(in_array(601, $jobs)) {?>				
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=dailytransactions"> DAILY TRANSACTION </a>
								</li>
								<?php } if(in_array(602, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=dailyfinance"> DAILY FINANCE REPORT </a>
								</li>
								<?php } if(in_array(603, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=dailyfinancedetailed"> DAILY FINANCE DETAILS REPORT </a>
								</li>
								<?php } ?>								
								<li>
									&nbsp;
								</li>
								<li>
									&nbsp;
								</li>	
								<li>
									&nbsp;
								</li>
							</ul>
						</div>
	
						<div>
							<a href="#" class="btn btn-block btn-inverse">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-primary">
								<span>VEHICLE REPORTS</span>
							</a></h5>
					</div>
	
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(604, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=fuel"> FUEL TRACKING REPORT </a>
								</li>
								<?php } if(in_array(605, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=creditsupplier"> CREDIT SUPPLIER REPORT </a>
								</li>
								<?php } if(in_array(606, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehiclemileage"> VEHICLE MILEAGE REPORT </a>
								</li>
								<?php } if(in_array(607, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehiclemileage_full"> VEHICLE MILEAGE REPORT (FULL) </a>
								</li>
								<?php } if(in_array(608, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehicleperformance"> VEHICLE PERFORMANCE REPORT  </a>
								</li>
								<?php }?>
								<li>
									&nbsp;
								</li>
							</ul>
						</div>
						<div>
							<a href="#" class="btn btn-block btn-primary">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-primary">
								<span>VEHICLE REPORTS</span>
							</a></h5>
					</div>
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(609, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=clientvehicletrips"> CLIENT VEHICLE TRIPS REPORT </a>
								</li>
								<?php } if(in_array(610, $jobs)) {?>								
								<!-- 
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="dailyfinancereport"> VEHICLE VERIFICATION REPORT </a>
								</li>	
								 -->							
								<?php } if(in_array(612, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=extrakmsreport"> EXTRA KMS REPORT </a>
								</li>
								<?php } if(in_array(612, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=contractvehiclesreport">CONTRACT VEHICLES REPORT </a>
								</li>
								<?php } if(in_array(612, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehicletrackingreport">VEHICLE TRACKING REPORT </a>
								</li>
								<?php } ?>
								<li>
									&nbsp;
								</li>
								<li>
									&nbsp;
								</li>
							</ul>
						</div>
						<div>
							<a href="#" class="btn btn-block btn-primary">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-orange">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-warning">SALARY REPORTS</a></h5>
					</div>
	
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(613, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=salaryadvances&type=office"> SALARY ADVANCES (OFFICE) </a>
								</li>
								<?php } if(in_array(613, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=salaryadvances&type=nonoffice"> SALARY ADVANCES (NON OFFICE) </a>
								</li>
								<?php } if(in_array(614, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=salary"> SALARY REPORT </a>
								</li>
								<?php } if(in_array(615, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=estimatedsalary"> ESTIMATED SALARY REPORT </a>
								</li>
								<?php } if(in_array(616, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=attendence"> ATTENDENCE </a>
								</li>
								<?php } if(in_array(617, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=attendencedetailed"> ATTENDENCE DETAILED </a>
								</li>
								<?php } ?>
							</ul>
						</div>
	
						<div>
							<a href="#" class="btn btn-block btn-warning">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			
			<!-- /section:pages/pricing.large -->
		</div>
		
		<div class="row col-xs-offset-0 col-xs-12">
			<!-- #section:pages/pricing.large -->
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-green">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-success">OTHER REPORTS</a></h5>
					</div>
	
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(618, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=inchargetransactions"> INCHARGE TRANSACTIONS </a>
								</li>
								<?php } if(in_array(619, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=loans"> LOANS REPORT </a>
								</li>
								<?php } if(in_array(620, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=bankposition"> BANK POSITION </a>
								</li>
								<?php } if(in_array(621, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=loginlog"> LOGIN DETAILS </a>
								</li>
								<?php } if(in_array(622, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=employeemainloginlog"> EMPLOYEE MAIN LOGIN DETAILS </a>
								</li>
								<?php } ?>
							</ul>
						</div>
	
						<div>
							<a href="#" class="btn btn-block btn-success">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-green">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-success">OTHER REPORTS</a></h5>
					</div>
	
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(618, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vendorpaymentsreport"> VENDOR PAYMENTS </a>
								</li>
								<?php } if(in_array(619, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=globalloansreport"> GLOBAL LOANS </a>
								</li>
								<?php } if(in_array(620, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehiclerenewalsreport"> VEHICLE RENEWALS </a>
								</li>
								<?php } if(in_array(620, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=cardspaymentreport"> CARDS PAYMENT INFO </a>
								</li>
								<?php } ?>
								<li>
									&nbsp;
								</li>
							</ul>
						</div>
	
						<div>
							<a href="#" class="btn btn-block btn-success">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-orange">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-warning">
								<span>STOCK REPORTS</span>
							</a></h5>
					</div>
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(623, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=stockpurchase"> STOCK PURCHASE REPORT </a>
								</li>
								<?php } if(in_array(624, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehiclestockhistory"> VEHICLE-STOCK HISTORY REPORT </a>
								</li>
								<?php } if(in_array(625, $jobs)) {?>	
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=inventory"> INVENTORY REPORT </a>
								</li>
								<?php } if(in_array(626, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=officeinventory"> OFFICE INVENTORY REPORT </a>
								</li>
								<?php } if(in_array(627, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=repairstock"> REPAIR STOCK REPORT </a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<div>
							<a href="#" class="btn btn-block btn-warning">
							</a>
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-primary">
								<span>VEHICLE SERVICE REPORTS</span>
							</a></h5>
					</div>
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(628, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=servicelog"> SERVICE LOG DETAILS </a>
								</li>
								<?php } if(in_array(611, $jobs)) {?>								
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=holidaysrunningreport"> HOLIDAYS RUNNING REPORT </a>
								</li>
								<?php } ?>
	
								<li>
									&nbsp;
								</li>
								
								<li>
									&nbsp;
								</li>
								
								<li>
									&nbsp;
								</li>
							</ul>
						</div>
						<div>
							<a href="#" class="btn btn-block btn-primary">
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 pricing-box">
				<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter"><a href="#" class="btn btn-block btn-primary">
								<span>CLIENT INCOME REPORTS</span>
							</a></h5>
					</div>
					<div class="widget-body">
						<div class="widget-main">
							<ul class="list-unstyled spaced2">
								<?php if(in_array(628, $jobs)) {?>
								<li>
									<i class="ace-icon fa fa-check green"></i>
									<a target="_blank" href="report?reporttype=vehicleincome"> VEHICLE INCOME </a>
								</li>
								<?php } ?>								
								<li>
									&nbsp;
								</li>
								<li>
									&nbsp;
								</li>
								
								<li>
									&nbsp;
								</li>
								
								<li>
									&nbsp;
								</li>
							</ul>
						</div>
						<div>
							<a href="#" class="btn btn-block btn-primary">
							</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /section:pages/pricing.large -->
		</div>
	</div>		
	
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/js/dataTables/jquery.dataTables.bootstrap.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/dataTables.buttons.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.flash.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.html5.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.print.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.colVis.js"></script>
		<script src="../assets/js/dataTables/extensions/select/dataTables.select.js"></script>
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/date-time/moment.js"></script>
		<script src="../assets/js/date-time/daterangepicker.js"></script>		
		<script src="../assets/js/bootbox.js"></script>
		<script src="../assets/js/chosen2.jquery.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">

			function changeDate(val){
				if(document.getElementById("trip_"+val).checked){
					var today = new Date();
				    var dd = today.getDate();
				    var mm = today.getMonth()+1; //January is 0!

				    var yyyy = today.getFullYear();
				    if(dd<10){
				        dd='0'+dd
				    } 
				    if(mm<10){
				        mm='0'+mm
				    } 
				    var today = dd+'-'+mm+'-'+yyyy;
					$("#date_"+val).val($("#date").val());
					$("#date_"+val).prop("readonly",true);
				}
				else{
					$("#date_"+val).val("");
					$("#date_"+val).prop("readonly",false);
				}
			}

			function verifyDate(){
				pmttype = $("#paymenttype").val();
				dt = $("#paymentdate").val();
				month = $("#month").val();
				branch = $("#branch").val();
				if(branch == ""){
					alert("select branch");
					return;
				}
				if(pmttype == ""){
					alert("select payment type");
					return;
				}
				if(month == ""){
					alert("select salary month");
					return;
				}
				if(dt == ""){
					alert("select payment date");
					return;
				}
				$('#verify').hide();
				location.replace("payemployeesalary?branch="+branch+"&paymenttype="+pmttype+"&month="+month+"&paymentdate="+dt);
			}

			function calcSalary(rowid, eid,type){
				month = $("#month").val();
				$.ajax({
			      url: "getcalempsalary?eid="+eid+"&role="+type+"&dt="+month,
			      success: function(data) {
			    	  var obj = JSON.parse(data);
			    	  $("#"+rowid+"_deductions").val(obj.due);
			    	  $("#"+rowid+"_daily_trips_salary").val(obj.dailytrips);
			    	  $("#"+rowid+"_daily_trips_allowance").val("0.00");
			    	  $("#"+rowid+"_local_trips_salary").val("0.00");
			      },
			      type: 'GET'
			   });
			}

			function viewDetails(rowid, eid,type){
				month = $("#month").val();
				$.ajax({
			      url: "getempsalary?eid="+eid+"&role="+type+"&dt="+month,
			      success: function(data) {
			    	  var obj = JSON.parse(data);
			    	  $("#duetbody").html(obj.due);
			    	  $("#dailytbody").html(obj.dailytrips);
			    	  $("#localtbody").html(obj.localtrips);
			      },
			      type: 'GET'
			   });
			}

			function editRecord(rowid, eid,type){
				$("#editbtn").html('<a class="btn btn-minier btn-success" onclick="return saveRecord('+rowid+','+eid+',\''+type+'\');">Save</a>');
				$("#detailsbtn").html('<a class="btn btn-minier btn-danger" onclick="return cancelSave('+rowid+','+eid+',\''+type+'\');">Cancel</a>');
				$("#"+rowid+"_deductions").attr("readonly",false);
		    	$("#"+rowid+"_daily_trips_salary").attr("readonly",false);
		    	$("#"+rowid+"_daily_trips_allowance").attr("readonly",false);
		    	$("#"+rowid+"_local_trips_salary").attr("readonly",false);
		    	$("#"+rowid+"_comments").attr("readonly",false);
				
			}

			function saveRecord(rowid, eid,type){
				salarymonth = $("#month").val();
				pfopted = $("#"+rowid+"_pfopted").val();
				deductions = $("#"+rowid+"_deductions").val();
				daily_trips = $("#"+rowid+"_daily_trips_salary").val();
				daily_trips_allowance = $("#"+rowid+"_daily_trips_allowance").val();
				local_trips_salary = $("#"+rowid+"_local_trips_salary").val();
				comments = $("#"+rowid+"_comments").val();
				url = "editsalarytransaction?";
				url = url+"eid="+eid;
				url = url+"&pfopted="+pfopted;
				url = url+"&deductions="+deductions;
				url = url+"&daily_trips_salary="+daily_trips;
				url = url+"&daily_trips_allowance="+daily_trips_allowance;
				url = url+"&local_trips_salary="+local_trips_salary;
				url = url+"&comments="+comments;
				url = url+"&month="+salarymonth;

				$.ajax({
			      url: url,
			      success: function(data) {
			    	  if(data=="success"){
			    		  bootbox.confirm("operation completed successfully!", function(result) {});
				   	  }
			    	  if(data=="fail"){
			    		  bootbox.confirm("operation could not be completed successfully!", function(result) {});
				   	  }
			      },
			      type: 'GET'
			    });

				$("#"+rowid+"_deductions").attr("readonly",true);
		    	$("#"+rowid+"_daily_trips_salary").attr("readonly",true);
		    	$("#"+rowid+"_daily_trips_allowance").attr("readonly",true);
		    	$("#"+rowid+"_local_trips_salary").attr("readonly",true);
		    	$("#"+rowid+"_comments").attr("readonly",true);
				$("#editbtn").html('<a class="btn btn-minier btn-success" onclick="return editRecord('+rowid+','+eid+',\''+type+'\');">Edit</a>');
				$("#detailsbtn").html('<a href="#modal-table" role="button" data-toggle="modal" class="btn btn-minier btn-info" onclick="return viewDetails('+rowid+','+eid+',\''+type+'\');">Details</a>');
			}

			function cancelSave(rowid, eid,type){
				$("#editbtn").html('<a class="btn btn-minier btn-success" onclick="return editRecord('+rowid+','+eid+',\''+type+'\');">Edit</a>');
				$("#detailsbtn").html('<a href="#modal-table" role="button" data-toggle="modal" class="btn btn-minier btn-info" onclick="return viewDetails('+rowid+','+eid+',\''+type+'\');">Details</a>');
				$("#"+rowid+"_deductions").attr("readonly",true);
		    	$("#"+rowid+"_daily_trips_salary").attr("readonly",true);
		    	$("#"+rowid+"_daily_trips_allowance").attr("readonly",true);
		    	$("#"+rowid+"_local_trips_salary").attr("readonly",true);
		    	$("#"+rowid+"_comments").attr("readonly",true);
			}


			function validateData(){
				var ids = document.forms['tripsform'].elements[ 'ids[]' ];
				for(i=0; i<ids.length;i++){
					if(ids[i].checked){
						if($("#"+i+"_daily_trips_salary").val()==""){
							alert("enter complete information for employee : "+$("#"+i+"_employeename").val());
							return false;
						}
						if($("#"+i+"_daily_trips_allowance").val()==""){
							alert("enter complete information for employee : "+$("#"+i+"_employeename").val());
							return false;
						}
						if($("#"+i+"_daily_trips_salary").val()==""){
							alert("enter complete information for employee : "+$("#"+i+"_employeename").val());
							return false;
						}
						if($("#"+i+"_local_trips_salary").val()==""){
							alert("enter complete information for employee : "+$("#"+i+"_employeename").val());
							return false;
						}
						if($("#"+i+"_deductions").val()==""){
							alert("enter complete information for employee : "+$("#"+i+"_employeename").val());
							return false;
						}
					}
				    
				}
			};

			$("#provider").on("change",function(){
				val = $("#provider option:selected").html();
				window.location.replace('serviceproviders?provider='+val);
			});

			$('.number').keydown(function(e) {
				this.value = this.value.replace(/[^0-9.]/g, ''); 
				this.value = this.value.replace(/(\..*)\./g, '$1');
			});
		
			//datepicker plugin
			//link
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			//show datepicker when clicking on the icon
			.next().on(ace.click_event, function(){
				$(this).prev().focus();
			});

			//$('.input-mask-phone').mask('(999) 999-9999');
			
			

			
			<?php 
				if(Session::has('message')){
					echo "bootbox.confirm('".Session::pull('message')."', function(result) {});";
				}
			?>

			//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
			$('.date-range-picker').daterangepicker({
				'applyClass' : 'btn-sm btn-success',
				'cancelClass' : 'btn-sm btn-default',	
				locale: {
					applyLabel: 'Apply',
					cancelLabel: 'Cancel',
				}
			})
			<?php 
				if(isset($values["daterange"])){
					echo "$('.date-range-picker').val('".$values["daterange"]."')";
				}
			?>
			

			if(!ace.vars['touch']) {
				$('.chosen-select').chosen({allow_single_deselect:true}); 
				//resize the chosen on window resize
		
				$(window)
				.off('resize.chosen')
				.on('resize.chosen', function() {
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				}).trigger('resize.chosen');
				//resize chosen on sidebar collapse/expand
				$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
					if(event_name != 'sidebar_collapsed') return;
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				});
		
		
				$('#chosen-multiple-style .btn').on('click', function(e){
					var target = $(this).find('input[type=radio]');
					var which = parseInt(target.val());
					if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
					 else $('#form-field-select-4').removeClass('tag-input-style');
				});
			}

		</script>
	@stop