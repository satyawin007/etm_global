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
			.chosen-container{
			  width: 100% !important;
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
			EMPLOYEE SALARIES
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="col-xs-offset-4 col-xs-8 ccordion-style1 panel-group">
			<a class="btn btn-sm btn-primary" href="payemployeesalary">DRIVERS/HELPERS SALARY</a> &nbsp;&nbsp;
			<a class="btn btn-sm  btn-inverse" href="payofficeemployeesalary"> OFFICE EMPLOYEES SALARY </a> &nbsp;&nbsp;
		</div>
<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group" style="width: 98%; margin-left: 10px;">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;PAY EMPLOYEE SALARY
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						@include("salaries.addlookupform",$form_info)						
					</div>
				</div>
			</div>
		</div>	
		</div>	
		<div class="row">
			<div class="col-xs-12" style="max-width: 98%;margin-left: 12px;">
				<div class="table-header">
					Results for "DRIVERS AND HELPERS"
				</div>
				<?php 
					$values = Input::All();
					if(isset($values["paymenttype"]) && isset($values["month"]) && isset($values["paymentdate"])){		
				?>	
	
				<!-- div.table-responsive -->
	
				<!-- div.dataTables_borderWrap -->
				<div>
					<?php 
						$url = "addemployeesalary";
						if(isset($values["month"]) && isset($values["paymentdate"])){
							$url = $url."?month=".$values["month"]."&paymentdate=".$values["paymentdate"];
						}
						if(isset($values["branch"])){
							$url = $url."&branch=".$values["branch"];
						}
						if(isset($values["paymenttype"])){
							$url = $url."&paymenttype=".$values["paymenttype"];
						}
						if(isset($values["bankaccount"])){ $url = $url."&bankaccount=".$values["bankaccount"];}
						if(isset($values["chequenumber"])){ $url = $url."&chequenumber=".$values["chequenumber"];}
						if(isset($values["bankname"])){ $url = $url."&bankname=".$values["bankname"];}
						if(isset($values["accountnumber"])){ $url = $url."&accountnumber=".$values["accountnumber"];}
						if(isset($values["issuedate"])){ $url = $url."&issuedate=".$values["issuedate"];}
						if(isset($values["transactiondate"])){ $url = $url."&transactiondate=".$values["transactiondate"];}
					?>
					<form name="tripsform" action="{{$url}}" method="post" onsubmit="return validateData();">
					<table id="dynamic-table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="center">
							</th>
							<th>Emp Name</th>
							<th>Role</th>
							<th>Trip Details</th>
							<th>PF opted</th>
							<th>DT salary</th>
							<th>DT allowance</th>
							<th>LT salary</th>
							<th>deductions</th>
							<th>comments</th>
						</tr>
					</thead>
						<tbody>
						<?php 
							$entities = \Employee::where("roleId","=",19)->orWhere("roleId","=",20)->get();
							$i = 0;
							foreach($entities as $entity){
								if($entity->roleId == 19){
									$entity->roleId = "DRIVER";
								}
								else if($entity->roleId == 20){
									$entity->roleId = "HELPER";
								}
								$dt_salary = 0;
								$dt_allowance = 0;
								$lt_salary = 0;
								$deductions = 0;
								$salaryMonth = $values["month"];
								$noOfDays = date("t", strtotime($salaryMonth)) -1;
								$startDate = $salaryMonth;
								$endDate =  date('Y-m-d', strtotime($salaryMonth.'+ '.$noOfDays.' days'));
								$recs = SalaryTransactions::where("salaryMonth","=",$values["month"])->where("empId","=",$entity->id)->where("deleted","=","No")->get();
								if(count($recs)>0){
									$rec = $recs[0];
							?>
							<tr>
								<td class="center" style="font-weight: bold; vertical-align: middle">
									<label class="pos-rel">
										<input type="hidden" name="ids[]" id="ids_{{$i}}" value="-1">
										<span class="lbl"></span>
									</label>
									<input type="hidden" name="id[]" id="id_{{$i}}" value="{{$entity->id}}" />
									<input type="hidden" name="employeename[]" id="{{$i}}_employeename" value="{{$entity->fullName}}" />
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->fullName}} - {{$entity->empCode}} </span>
								</td> 
								<td style="font-weight: bold; vertical-align: middle">{{$entity->roleId}}</td>
								<td style="font-weight: bold; vertical-align: middle; min-width:120px;"><span id="editbtn"><a class="btn btn-minier btn-success" onclick="return editRecord({{$i}},{{$entity->id}},'{{$entity->roleId}}');">Edit</a></span> &nbsp;&nbsp; <span id="detailsbtn"><a href="#modal-table" role="button" data-toggle="modal" onclick="return viewDetails({{$entity->id}},'{{$entity->roleId}}')"  class="btn btn-minier btn-info">Details</a></span></td>
								<td>
									<select name="pfopted[]" id="pfopted_{{$i}}" class="form-control" >
										<option <?php if($rec->pfopted== "Yes"){ echo " selected "; } ?> value="Yes">Yes</option>
										<option <?php if($rec->pfopted== "No"){ echo " selected "; } ?> value="No">No</option>
									</select>
								</td>
								<td >
									<input type="text" style="max-width:100px;" name="daily_trips_salary[]" id="{{$i}}_daily_trips_salary" readonly="readonly" value="{{$rec->dailyTripsSalary}}"/>	
								</td>
								<td>
									<input type="text" style="max-width:100px;"  name="daily_trips_allowance[]" id="{{$i}}_daily_trips_allowance" readonly="readonly" value="{{$rec->dailyTripsAllowance}}"/>	
								</td>
								<td>
									<input type="text" style="max-width:100px;"  name="local_trips_salary[]" id="{{$i}}_local_trips_salary" readonly="readonly" value="{{$rec->localTripsSalary}}"/>	
								</td>
								<td>
									<input type="text" style="max-width:100px;" name="deductions[]" id="{{$i}}_deductions" readonly="readonly" value="{{$rec->dueDeductions}}"/>	
								</td>
								<td>
									<input type="text" style="min-width:300px;" name="comments[]" id="{{$i}}_comments" readonly="readonly" value="{{$rec->comments}}"/>	
								</td>
							</tr>
							<?php } else  { ?>
							<tr>
								<td class="center" style="font-weight: bold; vertical-align: middle">
									<label class="pos-rel">
										<input type="checkbox" class="ace" name="ids[]" id="ids_{{$i}}" value="{{$i}}"/>
										<span class="lbl"></span>
									</label>
									<input type="hidden" name="id[]" id="id_{{$i}}" value="{{$entity->id}}" />
									<input type="hidden" name="employeename[]" id="{{$i}}_employeename" value="{{$entity->fullName}}" />
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->fullName}} - {{$entity->empCode}} </span>
								</td> 
								<td style="font-weight: bold; vertical-align: middle">{{$entity->roleId}}</td>
								<td style="font-weight: bold; vertical-align: middle; min-width:120px;"><a class="btn btn-minier btn-purple"  onclick="calcSalary({{$i}},{{$entity->id}},'{{$entity->roleId}}');">Calc</a> &nbsp;&nbsp; <a href="#modal-table" role="button" data-toggle="modal" onclick="return viewDetails({{$i}},{{$entity->id}},'{{$entity->roleId}}')" class="btn btn-minier btn-info">Details</a></td>
								<td>
									<select name="pfopted[]" id="pfopted_{{$i}}" class="form-control" >
										<option value="Yes">Yes</option>
										<option selected value="No">No</option>
									</select>
								</td>
								<td >
									<input type="text" style="max-width:100px;" name="daily_trips_salary[]" id="{{$i}}_daily_trips_salary" value=""/>	
								</td>
								<td>
									<input type="text" style="max-width:100px;"  name="daily_trips_allowance[]" id="{{$i}}_daily_trips_allowance"  value=""/>	
								</td>
								<td>
									<input type="text" style="max-width:100px;"  name="local_trips_salary[]" id="{{$i}}_local_trips_salary" value=""/>	
								</td>
								<td>
									<input type="text" style="max-width:100px;" name="deductions[]" id="{{$i}}_deductions" value=""/>	
								</td>
								<td>
									<input type="text" style="min-width:300px;" name="comments[]" id="{{$i}}_deductions" value=""/>	
								</td>
							</tr>
							<?php }?>
							<?php $i++; }?>
						</tbody>
					</table>
					<div class="clearfix form-actions" style="margin-bottom: 0px;" >
						<div class="col-md-offset-4 col-md-8" style="margin-top: 2%; margin-bottom: 1%">
							<button id="submit" class="btn primary" type="submit" id="submit">
								<i class="ace-icon fa fa-check bigger-110"></i>
								SUBMIT
							</button>
							<!--  <input type="submit" class="btn btn-info" type="button" value="SUBMIT"> -->
							&nbsp; &nbsp; &nbsp;
							<button id="reset" class="btn" type="reset">
								<i class="ace-icon fa fa-undo bigger-110"></i>
								RESET
							</button>
						</div>
					</div>
				</form>
				</div>
				<?php }?>
			</div>
		</div>
	</div>		
	
	<div id="modal-table" class="modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content" style="min-width: 120%;">
				<div class="modal-header no-padding">
					<div class="table-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							<span class="white">&times;</span>
						</button>
						SALARY INFORMATION
					</div>
				</div>
				<div style="padding: 20px;">
					<div class="modal-header no-padding">
						<div class="table-header">
							Due Amount
						</div>
					</div>
		
					<div class="modal-body no-padding">
						<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
							<thead>
								<tr>
									<th>Total Due Amount</th>
								</tr>
							</thead>
		
							<tbody id="duetbody">
								<td>0.00</td>
							</tbody>
						</table>
					</div>
					
					<div class="modal-header no-padding" style="margin-top: 10px;">
						<div class="table-header">
							Daily Trip Details
						</div>
					</div>
		
					<div class="modal-body no-padding">
						<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
							<thead>
								<tr>
									<th>Start Date</th>
									<th>Service Date</th>
									<th>Service No</th>
									<th>Vehicle No</th>
									<th>Vehicle Type</th>
									<th>Trip Salary</th>
									<th>Trip Allowance</th>
								</tr>
							</thead>
		
							<tbody id="dailytbody">
								<td colspan="6">Total Services</td>
								<td >0</td>
							</tbody>
						</table>
					</div>
					
					<div class="modal-header no-padding" style="margin-top: 10px;">
						<div class="table-header">
							Local Trip Details
						</div>
					</div>
		
					<div class="modal-body no-padding">
						<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
							<thead>
								<tr>
									<th>Booking Details</th>
									<th>Vehicle No</th>
									<th>Vehicle Type</th>
								</tr>
							</thead>
		
							<tbody id="localtbody">
							</tbody>
						</table>
					</div>
				</div>
	
				<div class="modal-footer no-margin-top">
					<button class="btn btn-sm btn-danger pull-left" data-dismiss="modal">
						<i class="ace-icon fa fa-times"></i>
						Close
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
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
		<script src="../assets/js/chosen.jquery.js"></script>
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

			function showPaymentFields(val){
				$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				url = 'getpaymentfields?paymenttype=';
				url = url+val;
				$.ajax({
				      url: url,
				      success: function(data) {
				    	  $("#addfields").html(data);
				    	  $('.date-picker').datepicker({
							autoclose: true,
							todayHighlight: true
						  });
				    	  $("#addfields").show();
				      },
				      type: 'GET'
				   });
			}

			function validateForm(){
				branch = $("#branch").val();
				if(branch ==  ""){
					alert("please select branch");
					return;
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

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

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

			jQuery(function($) {
				//initiate dataTables plugin
				var myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable( {
					bAutoWidth: false,
					"aoColumns": [
					  { "bSortable": false },
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },
					  { "bSortable": false }
					],
					"aaSorting": [],
					
					
					//"bProcessing": true,
			        //"bServerSide": true,
			        //"sAjaxSource": "http://127.0.0.1/table.php"	,
			
					//,
					//"sScrollY": "200px",
					//"bPaginate": false,
			
					"sScrollX": "100%",
					//"sScrollXInner": "120%",
					//"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
			
					//"iDisplayLength": 50
			
			
					select: {
						style: 'multi'
					}
			    } );
			
				
				//style the message box
				var defaultCopyAction = myTable.button(1).action();
				myTable.button(1).action(function (e, dt, button, config) {
					defaultCopyAction(e, dt, button, config);
					$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
				});
				
				
				var defaultColvisAction = myTable.button(0).action();
				myTable.button(0).action(function (e, dt, button, config) {
					
					defaultColvisAction(e, dt, button, config);
					
					
					if($('.dt-button-collection > .dropdown-menu').length == 0) {
						$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
					}
					$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
				});
			
				////
			
				setTimeout(function() {
					$($('.tableTools-container')).find('a.dt-button').each(function() {
						var div = $(this).find(' > div').first();
						if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
						else $(this).tooltip({container: 'body', title: $(this).text()});
					});
				}, 500);
			
				/////////////////////////////////
				//table checkboxes
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(!this.checked) myTable.row(row).deselect();
					else myTable.row(row).select();
				});
			
				$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});
				
				//And for the first simple table, which doesn't have TableTools or dataTables
				//select/deselect all rows according to table header checkbox
				var active_class = 'active';
				$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
						else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#simple-table').on('click', 'td input[type=checkbox]' , function(){
					var $row = $(this).closest('tr');
					if(this.checked) $row.addClass(active_class);
					else $row.removeClass(active_class);
				});
			
				
			
				/********************************/
				//add tooltip for small view action buttons in dropdown menu
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				
				//tooltip placement on right or left
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
				
			
			})
		</script>
	@stop