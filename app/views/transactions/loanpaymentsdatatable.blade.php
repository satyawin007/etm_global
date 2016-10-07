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
			LOAN PAYMENTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group" style="width: 98%; margin-left: 10px;">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;LOAN PAYMENTS
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php 
							$form_info = $values["form_info"]; 
							if(isset($values["show"]) && $values["show"]=="true"){
						?>
							@include("salaries.addlookupform",$form_info)	
						<?php }?>					
					</div>
				</div>
			</div>
		</div>	
		</div>	
		<div class="row">
			<div class="col-xs-12" style="max-width: 98%;margin-left: 12px;">
				<div class="table-header">
					Results for "LOAN PAYMENTS"
					<!--
					<span style="float:right; font-size: 16px; font-weight: bold;">Total Net Amt : <span id="totnetamt">0</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Total Client Paid Amt : <span id="totpaidamt">0</span> &nbsp;&nbsp;&nbsp;  Balance Amt : <span id="balamt">0</span> &nbsp;&nbsp;&nbsp;</span>				 
					-->
					</div>
				<?php
					$totnetamt = 0;
					$totpaidamt = 0;
					$values = Input::All();
					if(isset($values["branch"]) && isset($values["expensetype"]) && isset($values["financecompany"]) && isset($values["typeofloan"])){		
				?>	
	
				<!-- div.table-responsive -->
	
				<!-- div.dataTables_borderWrap -->
				<div>
					<?php 
						$url = "addloanpayments";
						if(isset($values["entity_date"])){
							$url = $url."?entity_date=".$values["entity_date"];
						}						
						if(isset($values["date"])){
							$url = $url."&date=".$values["date"];
						}
						if(isset($values["expensetype"])){
							$url = $url."&expensetype=".$values["expensetype"];
						}
						if(isset($values["paymenttype"])){
							$url = $url."&paymenttype=".$values["paymenttype"];
						}
						if(isset($values["financecompany"])){
							$url = $url."&financecompany=".$values["financecompany"];
						}
						if(isset($values["typeofloan"])){
							$url = $url."&typeofloan=".$values["typeofloan"];
						}
						if(isset($values["incharge"])){
							$url = $url."&incharge=".$values["incharge"];
						}
						if(isset($values["branch"])){
							$url = $url."&branch=".$values["branch"];
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
							<th class="center"></th>
							<th>S No</th>
							<th>Fin. Company</th>
							<th>Loan Date</th>
							<th>Loan No</th>
							<th>Amount</th>
							<th>Tot Paid Amt</th>
							<th>ROI</th>
							<th>Pmt Amt</th>
							<th>Month</th>
							<th>Paid Amt</th>
							<th>comments</th>
						</tr>
					</thead>
						<tbody>
						<?php
							$select_args = array();
							$select_args[] = "loans.id";
							$select_args[] = "loans.loanNo";
							$select_args[] = "loans.purpose";
							$select_args[] = "loans.vehicleId";
							$select_args[] = "financecompanies.name as finName";
							$select_args[] = "loans.agmtDate";
							$select_args[] = "loans.amountFinanced";
							$select_args[] = "loans.interestRate";
							$select_args[] = "loans.installmentAmount";
							
							$entities =  \Loan::leftJoin("financecompanies","financecompanies.id","=","loans.financeCompanyId")
												->where("loans.status","=","ACTIVE")
												->where("financecompanies.id","=",$values["financecompany"])
												->where("loans.purpose","=",$values["typeofloan"])
												->select($select_args)
												->get();
							$i = 0;
							foreach($entities as $entity){
								$dt_salary = 0;
								$dt_allowance = 0;
								$lt_salary = 0;
								$deductions = 0;
								$salaryMonth = $values["entity_date"];
							?>	
							<tr>
								<td class="center" style="font-weight: bold; vertical-align: middle">
									<label class="pos-rel">
										<input type="checkbox" class="ace"  name="ids[]" id="ids_{{$i}}" value="{{$i}}"/>
										<span class="lbl"></span>
									</label>
									<input type="hidden" name="id[]" id="id_{{$i}}" value="{{$i}}" />
									<input type="hidden" name="vehid[]" id="{{$i}}_loanid" value="{{$entity->id}}" />
									<input type="hidden" name="loanno[]" id="{{$i}}_loanno" value="{{$entity->loanNo}}" />
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$i+1}}</span>
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->finName}}</span>
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{date("d-m-Y",strtotime($entity->agmtDate))}}</span>
								</td>	
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->loanNo}}</span>
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->amountFinanced}}</span>
								</td>
								<?php 
									$tot_paid_amt = ExpenseTransaction::where("status","=","ACTIVE")->where("entity","=","LOAN PAYMENT")->where("entityValue","=",$entity->id)->sum("amount");
								?>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$tot_paid_amt}}</span>
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->interestRate}}</span>
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->installmentAmount}}</span>
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{date("M",strtotime($values["entity_date"]))}}</span>
								</td>
								<td style="vertical-align: middle;">
									<input type="text" style="min-width:70px;" name="amount[]" id="{{$i}}_amount"  value=""/>	
								</td>
								<td style="vertical-align: middle;">
									<input type="text" style="min-width:270px;" name="remarks[]"  id="{{$i}}_remarks" value=""/>	
								</td>
							</tr>
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
			$("#incharge").attr("disabled",true);
			$("#enableincharge").val("NO");
			$("#paymenttype").attr("disabled",false);
			<?php 
				if(isset($values["enableincharge"]) && $values["enableincharge"]=="YES") {
					echo '$("#incharge").attr("disabled",false);';
					echo '$("#enableincharge").val("YES");'; 
				}
				
			?>
			$('.chosen-select').trigger('chosen:updated');

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
				month = $("#entity_date").val();
				if(month == ""){
					alert("select  for month");
					return;
				}	

				expensetype = $("#expensetype").val();
				if(expensetype == ""){
					alert("select  expensetype");
					return;
				}	

				financecompany = $("#financecompany").val();
				if(financecompany == ""){
					alert("select  financecompany");
					return;
				}	

				typeofloan = $("#typeofloan").val();
				if(typeofloan == ""){
					alert("select type of loan");
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
				clientname = $("#clientname").val();
				if(clientname ==  ""){
					alert("please select clientname");
					return;
				}
				depot = $("#depot").val();
				if(depot ==  ""){
					alert("please select depot");
					return;
				}
				$('#verify').hide();
				//location.replace("payemployeesalary?branch="+branch+"&paymenttype="+pmttype+"&month="+month+"&paymentdate="+dt+"&clientname="+clientname+"&depot="+depot);
			}

			function calcNetAmount (id){
				id = id.split("_")[0];
				veh_gross = $("#"+id+"_veh_gross").val();
				veh_tds = (parseInt(veh_gross)*tdspercentage)/(100);
				veh_emi = $("#"+id+"_veh_emi").val();
				if(veh_emi==""){
					veh_emi=0;
				}
				veh_stopped = $("#"+id+"_veh_stopped").val();
				if(veh_stopped==""){
					veh_stopped=0;
				}
				otherincome = $("#"+id+"_veh_otherincome").val();
				if(otherincome==""){
					otherincome=0;
				}
				otherdeductions = $("#"+id+"_veh_otherdeductions").val();
				if(otherdeductions==""){
					otherdeductions=0;
				}
				net = parseInt(veh_gross)-parseInt(veh_tds);
				net = net-(parseInt(veh_emi));
				net = net-parseInt(veh_stopped);
				net = net+parseInt(otherincome);
				net = net-parseInt(otherdeductions);
				$("#"+id+"_veh_netamount").val(net);
				$("#"+id+"_veh_tds").val(veh_tds);			
			}

			function viewDetails(data){
				bootbox.alert(data);
				/*month = $("#month").val();
				$.ajax({
			      url: "getempsalary?eid="+eid+"&role="+type+"&dt="+month,
			      success: function(data) {
			    	  var obj = JSON.parse(data);
			    	  $("#duetbody").html(obj.due);
			    	  $("#dailytbody").html(obj.dailytrips);
			    	  $("#localtbody").html(obj.localtrips);
			      },
			      type: 'GET'
			   });*/
			}

			function editRecord(rowid, recid){
				$("#"+rowid+"_editbtn").html('<a class="btn btn-minier btn-success" onclick="return saveRecord('+rowid+','+recid+');">Save</a>');
				$("#"+rowid+"_veh_gross").attr("readonly",false);
		    	$("#"+rowid+"_veh_tds").attr("readonly",false);
		    	$("#"+rowid+"_veh_emi").attr("readonly",false);
		    	$("#"+rowid+"_veh_stopped").attr("readonly",false);
		    	$("#"+rowid+"_veh_otherincome").attr("readonly",false);
		    	$("#"+rowid+"_veh_otherdeductions").attr("readonly",false);
		    	$("#"+rowid+"_veh_clientamount").attr("readonly",false);
		    	$("#"+rowid+"_veh_remarks").attr("readonly",false);				
			}

			function saveRecord(rowid, recid){
				month = $("#month").val();
				paymentdate = $("#paymentdate").val();
				paymenttype = $("#paymenttype").val();
				tdspercentage = $("#tdspercentage").val();				
				billdate = $("#billdate").val();
				billno = $("#billno").val();
				
				veh_gross = $("#"+rowid+"_veh_gross").val();
				veh_tds = $("#"+rowid+"_veh_tds").val();
				veh_emi = $("#"+rowid+"_veh_emi").val();
				veh_stopped = $("#"+rowid+"_veh_stopped").val();
				veh_otherincome = $("#"+rowid+"_veh_otherincome").val();
				veh_otherdeductions = $("#"+rowid+"_veh_otherdeductions").val();
				veh_clientamount = $("#"+rowid+"_veh_clientamount").val();
				veh_remarks = $("#"+rowid+"_veh_remarks").val();
				veh_netamount = $("#"+rowid+"_veh_netamount").val();
				
				url = "editclientincome?";
				url = url+"rid="+recid;
				url = url+"&paymentdate="+paymentdate;
				url = url+"&paymenttype="+paymenttype;
				url = url+"&tdspercentage="+tdspercentage;
				url = url+"&billdate="+billdate;
				url = url+"&billno="+billno;
				
				url = url+"&veh_gross="+veh_gross;
				url = url+"&veh_tds="+veh_tds;
				url = url+"&veh_emi="+veh_emi;
				url = url+"&veh_stopped="+veh_stopped;
				url = url+"&veh_otherincome="+veh_otherincome;
				url = url+"&veh_otherdeductions="+veh_otherdeductions;
				url = url+"&veh_clientamount="+veh_clientamount;
				url = url+"&veh_remarks="+veh_remarks;
				url = url+"&veh_netamount="+veh_netamount;

				$.ajax({
			      url: url,
			      success: function(data) {
			    	  if(data=="success"){
			    		  bootbox.alert("operation completed successfully!", function(result) {});
				   	  }
			    	  if(data=="fail"){
			    		  bootbox.alert("operation could not be completed successfully!", function(result) {});
				   	  }
			      },
			      type: 'GET'
			    });

				$("#"+rowid+"_editbtn").html('<a class="btn btn-minier btn-success" onclick="return editRecord('+rowid+','+recid+');">Edit</a>');
				$("#"+rowid+"_veh_gross").attr("readonly",true);
		    	$("#"+rowid+"_veh_tds").attr("readonly",true);
		    	$("#"+rowid+"_veh_emi").attr("readonly",true);
		    	$("#"+rowid+"_veh_stopped").attr("readonly",true);
		    	$("#"+rowid+"_veh_otherincome").attr("readonly",true);
		    	$("#"+rowid+"_veh_otherdeductions").attr("readonly",true);
		    	$("#"+rowid+"_veh_clientamount").attr("readonly",true);
		    	$("#"+rowid+"_veh_remarks").attr("readonly",true);			}

			function cancelSave(rowid, eid){
				$("#"+rowid+"_editbtn").html('<a class="btn btn-minier btn-success" onclick="return editRecord('+rowid+','+eid+');">Edit</a>');
				$("#"+rowid+"_veh_gross").attr("readonly",true);
		    	$("#"+rowid+"_veh_tds").attr("readonly",true);
		    	$("#"+rowid+"_veh_emi").attr("readonly",true);
		    	$("#"+rowid+"_veh_stopped").attr("readonly",true);
		    	$("#"+rowid+"_veh_otherincome").attr("readonly",true);
		    	$("#"+rowid+"_veh_otherdeductions").attr("readonly",true);
		    	$("#"+rowid+"_veh_clientamount").attr("readonly",true);
		    	$("#"+rowid+"_veh_remarks").attr("readonly",true);	
			}

			$("#enableincharge").on("change",function(){
			  	val = $("#enableincharge").val();
			  	if(val == "YES"){
			  		$("#paymentpaid").val("Yes");
				  	$("#paymentpaid").attr("disabled",false);
					$("#incharge").attr("disabled",false);
					$('.chosen-select').trigger('chosen:updated');
				}
				else{
					$("#paymentpaid").val("No");
					$("#paymentpaid").attr("disabled",false);
					$("#incharge").attr("disabled",true);
					$('.chosen-select').trigger('chosen:updated');
				}
			  });

			function getInchargeBalance(val){
				$.ajax({
			      url: "getinchargebalance?id="+val,
			      success: function(data) {
			    	  $("#inchargebalance").val(data);
			      },
			      type: 'GET'
			   });
			}

			function changeDepot(val){
				$.ajax({
			      url: "getdepotsbyclientId?id="+val,
			      success: function(data) {
				      data = "<option value='0'>ALL</option>"+data;
			    	  $("#depot").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			    });

				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
			}

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			function validateData(){
				tot_salary_amt =  0;
				ret_val =  true;
				var ids = document.forms['tripsform'].elements[ 'ids[]' ];
				for(i=0; i<ids.length;i++){
					if(ids[i].checked){
						if($("#"+i+"_netsalary").text()!="0"){
							tot_salary_amt = tot_salary_amt+parseInt($("#"+i+"_netsalary").text());
						}
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
				if($("#paymenttype").val() == "neft" || $("#paymenttype").val() == "rtgs" || $("#paymenttype").val() == "ecs" ){
					if($("#chequenumber").val() == ""){
						alert("enter transaction number");
						return false;
					}
					val = $("#chequenumber").val();
					url = "gettransactionamount?transid="+val;
					$.ajax({
				      url: url,
				      async: false,
				      success: function(data) {
					      data = data.substring(9);
					      avail_amt = parseInt(data);
					      if(tot_salary_amt>avail_amt){						      
					      	alert("Transaction can not be done due to insufficient funds.\navailable amount is : "+avail_amt+" and trans amount : "+tot_salary_amt);
					      	ret_val = false;
					      }
				      },
				      type: 'GET'
				    });
				}				
			    return ret_val;
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
			
			//or change it into a date range picker
			$('.input-daterange').datepicker({autoclose:true,todayHighlight: true});

			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
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
				$("#getbtn").on("click", function(){
					val = $("#transfield").val();
					url = "gettransactionamount?transid="+val;
					$.ajax({
				      url: url,
				      success: function(data) {
				    	  $("#transvalue").html(data);
				      },
				      type: 'GET'
				    });
				});
				
				//initiate dataTables plugin
				var myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable( {
					bAutoWidth: false,
					"aoColumns": [
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false },
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false },
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false },
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false }
					],
					"aaSorting": [],
					
					
					//"bProcessing": true,
			        //"bServerSide": true,
			        //"sAjaxSource": "http://127.0.0.1/table.php"	,
			
					//,
					//"sScrollY": "200px",
					//"bPaginate": false,
			
					//"sScrollX": "100%",
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