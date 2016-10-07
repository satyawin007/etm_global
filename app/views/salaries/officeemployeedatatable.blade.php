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
				margin-top: 0px;
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
			<div class="col-xs-6 ">
				<?php 
					$jobs = Session::get("jobs");
					if(in_array(338, $jobs)){
				?>
					<a class="btn btn-sm btn-primary" href="payemployeesalary?show=true">DRIVERS/HELPERS SALARY</a> &nbsp;&nbsp;
				<?php 
					}
					if(in_array(339, $jobs)){
				?>
					<a class="btn btn-sm  btn-inverse" href="payofficeemployeesalary?show=true"> OFFICE EMPLOYEES SALARY </a> &nbsp;&nbsp;
				<?php 
					} 
				?>
			</div>
			<div class="col-xs-5">
				<div class="row">
					<div class="col-xs-12 col-sm-12">						
						<div class="input-group">
							<span class="input-group-addon" id="transvalue">
								AMOUNT
							</span>
							<select class="form-control chosen-select" id="transfield" >
								<option value="">--select trans id--</option>
								<?php 
									$recs1 = \ExpenseTransaction::where("status","=","ACTIVE")->where("lookupValueId","=",346)->get();
									foreach($recs1 as $rec1){
										echo "<option value='".$rec1->chequeNumber."'>".$rec1->chequeNumber." (Rs. ".$rec1->amount.")</option>";
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" id="getbtn" class="btn btn-purple btn-sm">
									GET
								</button>
							</span>
						</div>
					</div>
				</div>
			</div>
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
					Results for "OFFICE EMPLOYEES"
				</div>
				<?php 
					$values = Input::All();
					if(isset($values["paymenttype"]) && isset($values["month"]) && isset($values["paymentdate"]) && isset($values["fromdate"])){		
				?>	
	
				<!-- div.table-responsive -->
	
				<!-- div.dataTables_borderWrap -->
				<div>
					<?php 
						//"bankaccount","chequenumber","bankname","accountnumber","issuedate","transactiondate"
						$url = "addemployeesalary";
						if(isset($values["month"]) && isset($values["paymentdate"])){
							$url = $url."?month=".$values["month"]."&paymentdate=".$values["paymentdate"];
						}
						if(isset($values["show"])){
							$url = $url."&show=".$values["show"];
						}
						if(isset($values["branch"])){
							$url = $url."&branch=".$values["branch"];
						}
						if(isset($values["incharge"])){
							$url = $url."&incharge=".$values["incharge"];
						}
						if(isset($values["paymenttype"])){
							$url = $url."&paymenttype=".$values["paymenttype"];
						}
						if(isset($values["clientname"])){
							$url = $url."&clientname=".$values["clientname"];
						}
						if(isset($values["depot"])){
							$url = $url."&depot=".$values["depot"];
						}
						if(isset($values["fromdate"])){
							$url = $url."&fromdate=".$values["fromdate"];
						}
						if(isset($values["todate"])){
							$url = $url."&todate=".$values["todate"];
						}
						if(isset($values["casualleaves"])){
							$url = $url."&casualleaves=".$values["casualleaves"];
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
							<th>Emp Name</th>
							<th>Role</th>
							<th>Salary</th>
							<th>Summary</th>
							<th style="min-width:140px;">Due/Leave Amount</th>
							<th>Leaves</th>
							<th>Casual Leaves</th>
							<th>Due Deductions</th>
							<th>Leave Deductions</th>
							<th>PF Details</th>
							<th>Travel/Other Amt</th>
							<th>Net Salary</th>
							<th>Gross Salary</th>
							<th>Card No</th>
							<th>comments</th>
						</tr>
					</thead>
						<tbody>
						<?php
							$entities = Employee::whereRaw(" status='ACTIVE' and (roleId!=20 and roleId!=19) and FIND_IN_SET('".$values["branch"]."',employee.officeBranchIds)")
												  ->get();
							$i = 0;
							
							$roles_arr = array();
							$roles = Role::All();
							foreach ($roles as $role){
								$roles_arr[$role->id] = $role->roleName;
							}
							
							$fromdt = date("Y-m-d",strtotime($values["fromdate"]));
							$todt = date("Y-m-d",strtotime($values["todate"]));
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
										<input type="checkbox" onclick="this.checked=!this.checked;" class="ace" name="ids[]" id="ids_{{$i}}" value="{{$i}}"/>
										<span class="lbl"></span>
									</label>
									<input type="hidden" name="id[]" id="id_{{$i}}" value="{{$entity->id}}" />
									<input type="hidden" name="employeename[]" id="{{$i}}_employeename" value="{{$entity->fullName}}" />
								</td>
								<td style="font-weight: bold; vertical-align: middle">
									<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->fullName}} - {{$entity->empCode}} </span>
								</td>
								<?php 
									$salary_amt = 0;
									$salary = SalaryDetails::where("empId","=",$entity->id)->get();
									if(count($salary)>0){
										$salary = $salary[0];
										$salary_amt = $salary->salary;
									}
								?> 
								<?php 
									$due_amt = "0.00";
									$recs1 = DB::select( DB::raw("SELECT SUM(`amount`) amt FROM `empdueamount` WHERE empId = ".$entity->id." and deleted='No'") );
									foreach ($recs1 as $rec1){
										$due_amt = $rec1->amt;
										if($due_amt == ""){
											$due_amt = "0.00";
										}
									}
								?>
								<?php 
									$leaves = 0;
									$leaves_amt = 0;
									$recs1 = DB::select( DB::raw($sql = "select count(*) as cnt from attendence where attendence.empId='".$entity->id."' and (attendenceStatus = 'A') and date between '$fromdt' and '$todt'") );
									foreach ($recs1 as $rec1){
										$leaves = $rec1->cnt;
										$leaves = $leaves/2;
										$after_leaves = $leaves;
										$date1 = strtotime(date("Y-m-d",strtotime($entity->joiningDate)));
										$date2 = strtotime(date("Y-m-d",strtotime($entity->terminationDate)));
										$date3 = strtotime($fromdt);
										$date4 = strtotime($todt);
										if($date1>$date3 || ($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" &&  $date2<$date4)){
											$after_leaves = $leaves;
										}
										else{
											$after_leaves = $leaves-$values["casualleaves"];
										}
										if($after_leaves<0){
											$after_leaves = 0;
										}
										
										$date1=date_create($fromdt);
										$date2=date_create($todt);
										$diff=date_diff($date1,$date2);
										$working_days =  $diff->format("%a");
										$working_days = $working_days+1;
										$leaves_amt = ($salary_amt/$working_days)*$after_leaves;
										$leaves_amt = intval($leaves_amt);
									}
								?>
								
								<?php
									$total_days = 0;
									$date1=date_create($fromdt);
									$date2=date_create($todt);
									$diff=date_diff($date1,$date2);
									$total_days =  $diff->format("%a");
									$total_days = $total_days+1;
									
									$casual_leaves = $values["casualleaves"];
									$late_joing_days = 0;
									$early_erminated_days = 0;
									$actual_working_days = $total_days;
									$employee_working_days = $total_days-$leaves;
									
									$previous_salary = 0;
									$increment = 0;
									$salary_details = SalaryDetails::where("empid","=",$entity->id)->Get();
									if(count($salary_details)>0){
										$salary_details = $salary_details[0];
										$previous_salary = $salary_details->previousSalary;
										$incrdate = date("d-m-Y",strtotime($salary_details->increamentDate));
										$increment = $salary_details->increament." (".$incrdate.")";
									}
									$joining_date = date("d-m-Y",strtotime($entity->joiningDate));
									$details_data = "<table class=\'table table-striped table-bordered table-hover\'><tr><th>ENTITY</th><th>VALUE</th></tr>";
									$details_data = $details_data."<tr><td>Total Days</td><td>".$total_days."</td></tr>";
									$details_data = $details_data."<tr><td>Casual Leaves</td><td>".$casual_leaves."</td></tr>";
									$details_data = $details_data."<tr><td>Late Joing Days</td><td>".$late_joing_days."</td></tr>";
									$details_data = $details_data."<tr><td>Early Terminated Days</td><td>".$early_erminated_days."</td></tr>";
									$details_data = $details_data."<tr><td>Actual Working Days</td><td>".$actual_working_days."</td></tr>";
									$details_data = $details_data."<tr><td>Employee Working Days</td><td>".$employee_working_days."</td></tr>";
									$details_data = $details_data."<tr><td>Previous Salary</td><td>".$previous_salary."</td></tr>";
									$details_data = $details_data."<tr><td>Increment</td><td>".$increment."</td></tr>";
									$details_data = $details_data."<tr><td>Joining Date</td><td>".$joining_date."</td></tr>";
								?>
								<?php 
									if(isset($roles_arr[$entity->roleId])){
										$entity->roleId = $roles_arr[$entity->roleId];
									}
								?>
								<td style="font-weight: bold; vertical-align: middle">{{$entity->roleId}}</td>
								<td style="font-weight: bold; vertical-align: middle">
									<input type="text" style="max-width:70px;"  name="emp_salary[]" id="{{$i}}_emp_salary" readonly="readonly" value="{{$salary_amt}}"/>
								</td>
								<td style="font-weight: bold; vertical-align: middle; min-width:120px;"><span id="{{$i}}_editbtn"><a class="btn btn-minier btn-success" onclick="return editRecord({{$i}},{{$entity->id}},'{{$entity->roleId}}');">Edit</a></span> &nbsp;&nbsp; <span id="{{$i}}_detailsbtn"><a role="button" data-toggle="modal" onclick="return viewDetails('{{$details_data}}')"  class="btn btn-minier btn-info">Details</a></span></td>
								<td style="font-weight: bold; vertical-align: middle">Due Amt : {{$due_amt}}</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;" name="leaves[]" id="{{$i}}_leaves" readonly="readonly" value="{{$leaves}}"/>	
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;"  name="casual_leaves[]" id="{{$i}}_casual_leaves" readonly="readonly" value="{{$values['casualleaves']}}"/>	
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;"  name="due_deductions[]" id="{{$i}}_due_deductions"readonly="readonly" onchange="calcSalary(this.id)" value="{{$rec->dueDeductions}}"/>	
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;" name="leave_deductions[]" id="{{$i}}_leave_deductions" readonly="readonly" onchange="calcSalary(this.id)" value="{{$rec->leaveDeductions}}"/>	
								</td>
								<td style="vertical-align: middle">
									<select name="pfopted[]" id="pfopted_{{$i}}" class="form-control" >
										<option value="Yes">Yes</option>
										<option selected value="No">No</option>
									</select>
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="min-width:70px;" name="other_amt[]" id="{{$i}}_other_amt" readonly="readonly" onchange="calcSalary(this.id)" value="{{$rec->otherAmount}}"/>	
								</td>
								<td style="font-weight: bold; vertical-align: middle" id="{{$i}}_netsalary">{{$rec->salaryPaid}}</td>
								<td style="font-weight: bold; vertical-align: middle">{{$rec->actualSalary}}</td>
								<td style="font-weight: bold; vertical-align: middle; min-width:150px;">{{$entity->cardNumber}}</td>
								<td style="vertical-align: middle">
									<input type="text" style="min-width:270px;" name="comments[]" readonly="readonly" id="{{$i}}_comments" value="{{$rec->comments}}"/>	
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
								
								<?php 
									$due_amt = "0.00";
									$recs = DB::select( DB::raw("SELECT SUM(`amount`) amt FROM `empdueamount` WHERE empId = ".$entity->id." and deleted='No'") );
									foreach ($recs as $rec){
										$due_amt = $rec->amt;
										if($due_amt == ""){
											$due_amt = "0.00";
										}
									}
								?>
								
								<?php 
									$salary_amt = 0;
									$salary = SalaryDetails::where("empId","=",$entity->id)->get();
									if(count($salary)>0){
										$salary = $salary[0];
										$salary_amt = $salary->salary;
									}
									
									$leaves = 0;
									$leaves_amt = 0;
									$recs = DB::select( DB::raw($sql = "select count(*) as cnt from attendence where attendence.empId='".$entity->id."' and attendenceStatus = 'A' and date between '$fromdt' and '$todt'") );
									foreach ($recs as $rec){
										$leaves = $rec->cnt;
										$leaves = $leaves/2;
										$after_leaves = $leaves;
										$date1 = strtotime(date("Y-m-d",strtotime($entity->joiningDate)));
										$date2 = strtotime(date("Y-m-d",strtotime($entity->terminationDate)));
										$date3 = strtotime($fromdt);
										$date4 = strtotime($todt);
										if(($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" && $date1>$date3) || ($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" &&  $date2<$date4)){
											$after_leaves = $leaves;
										}
										else{
											$after_leaves = $leaves-$values["casualleaves"];
										}
										if($after_leaves<0){
											$after_leaves = 0;
										}

										$date1=date_create($fromdt);
										$date2=date_create($todt);
										$diff=date_diff($date1,$date2);
										$working_days =  $diff->format("%a");
										$working_days = $working_days+1;
										$leaves_amt = ($salary_amt/$working_days)*$after_leaves;
										$leaves_amt = intval($leaves_amt);
									}
								?>
								
								<?php 
									$net_salary = $salary_amt-($leaves_amt+$due_amt);
								?>
								
								<?php
									$total_days = 0;
									$date1=date_create($fromdt);
									$date2=date_create($todt);
									$diff=date_diff($date1,$date2);
									$total_days =  $diff->format("%a");
									$total_days = $total_days+1;
									
									$casual_leaves = $values["casualleaves"];
									$late_joing_days = 0;
									$early_erminated_days = 0;
									$actual_working_days = $total_days;
									$employee_working_days = $total_days-$leaves;
									
									$previous_salary = 0;
									$increment = 0;
									$salary_details = SalaryDetails::where("empid","=",$entity->id)->Get();
									if(count($salary_details)>0){
										$salary_details = $salary_details[0];
										$previous_salary = $salary_details->previousSalary;
										$incrdate = date("d-m-Y",strtotime($salary_details->increamentDate));
										$increment = $salary_details->increament." (".$incrdate.")";
									}
									$joining_date = date("d-m-Y",strtotime($entity->joiningDate));
									$details_data = "<table class=\'table table-striped table-bordered table-hover\'><tr><th>ENTITY</th><th>VALUE</th></tr>";
									$details_data = $details_data."<tr><td>Total Days</td><td>".$total_days."</td></tr>";
									$details_data = $details_data."<tr><td>Casual Leaves</td><td>".$casual_leaves."</td></tr>";
									$details_data = $details_data."<tr><td>Late Joing Days</td><td>".$late_joing_days."</td></tr>";
									$details_data = $details_data."<tr><td>Early Terminated Days</td><td>".$early_erminated_days."</td></tr>";
									$details_data = $details_data."<tr><td>Actual Working Days</td><td>".$actual_working_days."</td></tr>";
									$details_data = $details_data."<tr><td>Employee Working Days</td><td>".$employee_working_days."</td></tr>";
									$details_data = $details_data."<tr><td>Previous Salary</td><td>".$previous_salary."</td></tr>";
									$details_data = $details_data."<tr><td>Increment</td><td>".$increment."</td></tr>";
									$details_data = $details_data."<tr><td>Joining Date</td><td>".$joining_date."</td></tr>";
								?>
								<?php 
									if(isset($roles_arr[$entity->roleId])){
										$entity->roleId = $roles_arr[$entity->roleId];
									}
								?>
								<td style="font-weight: bold; vertical-align: middle">{{$entity->roleId}}</td>
								<td style="font-weight: bold; vertical-align: middle">
									<input type="text" style="max-width:70px;"  name="emp_salary[]" id="{{$i}}_emp_salary" readonly="readonly" value="{{$salary_amt}}"/>
								</td>
								<td style="font-weight: bold; vertical-align: middle; min-width:70px;"><a role="button" data-toggle="modal" onclick="return viewDetails('{{$details_data}}')" class="btn btn-minier btn-info">Details</a></td>
								
								<td style="font-weight: bold; vertical-align: middle">Due Amt : {{$due_amt}}</td>
								
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;" name="leaves[]" id="{{$i}}_leaves" readonly="readonly" value="{{$leaves}}"/>	
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;"  name="casual_leaves[]" id="{{$i}}_casual_leaves"  value="{{$values['casualleaves']}}"/>	
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;"  name="due_deductions[]" id="{{$i}}_due_deductions" onchange="calcSalary(this.id)" value="{{$due_amt}}"/>	
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="max-width:70px;" name="leave_deductions[]" id="{{$i}}_leave_deductions" onchange="calcSalary(this.id)" value="{{$leaves_amt}}"/>	
								</td>
								<td style="vertical-align: middle">
									<select name="pfopted[]" id="pfopted_{{$i}}" class="form-control" >
										<option value="Yes">Yes</option>
										<option selected value="No">No</option>
									</select>
								</td>
								<td style="vertical-align: middle">
									<input type="text" style="min-width:70px;" name="other_amt[]" id="{{$i}}_other_amt" onchange="calcSalary(this.id)" value="0.00"/>	
								</td>
								<td style="font-weight: bold; vertical-align: middle" id="{{$i}}_netsalary">{{$net_salary}}</td>
								<td style="font-weight: bold; vertical-align: middle">{{$salary_amt}}</td>
								<td style="font-weight: bold; vertical-align: middle; min-width:150px;">{{$entity->cardNumber}}</td>
								<td style="vertical-align: middle">
									<input type="text" style="min-width:270px;" name="comments[]" id="{{$i}}_comments" value=""/>	
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
					<div class="modal-header no-padding" style="margin-top: 10px;">
						<div class="table-header">
							Leave Details
						</div>
					</div>
		
					<div class="modal-body no-padding">
						<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
							<thead>
								<tr>
									<th>From Date</th>
									<th>from Mor/Eve</th>
									<th>To Date</th>
									<th>to Mor/Eve</th>
									<th>Leaves</th>
									<th>Remarks</th>
									<th>Status</th>
								</tr>
							</thead>
		
							<tbody id="tbody">
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
				$("#incharge").attr("disabled",true);
				<?php if(isset($values["enableincharge"]) && $values["enableincharge"]=="YES"){?>
					$("#incharge").attr("disabled",false);
				<?php } ?>
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

			function enableIncharge(val){
				if(val == "YES"){
					$("#incharge").attr("disabled",false);
					$('.chosen-select').trigger('chosen:updated');
				}
				else{
					$("#incharge").attr("disabled",true);
					$('.chosen-select').trigger('chosen:updated');
				}
			}

			function getInchargeBalance(val){
				$.ajax({
			      url: "getinchargebalance?id="+val,
			      success: function(data) {
			    	  $("#inchargebalance").val(data);
			      },
			      type: 'GET'
			   });
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
				fromdate = $("#fromdate").val();
				if(fromdate == ""){
					alert("select fromdate");
					return;
				}
				todate = $("#todate").val();
				if(todate == ""){
					alert("select to date");
					return;
				}
				$('#verify').hide();
				location.replace("payofficeemployeesalary?branch="+branch+"&paymenttype="+pmttype+"&month="+month+"&paymentdate="+dt+"&fromdate="+fromdate+"&todate="+todate);
			}

			function calcSalary(id){
				id = id.split("_")[0];
				salary = $("#"+id+"_emp_salary").val();
				other_amount = $("#"+id+"_other_amt").val();
				due_deductions = $("#"+id+"_due_deductions").val();
				leave_deductions = $("#"+id+"_leave_deductions").val();
				//alert(id+" "+salary+" "+other_amount+" "+due_deductions+" "+leave_deductions);
				salary = (parseInt(salary)+parseInt(other_amount))-(parseInt(due_deductions)+parseInt(leave_deductions));
				$("#"+id+"_netsalary").html(salary);			
			}

			function viewDetails(data){
				bootbox.alert(data);
				/*month = $("#month").val();
				$.ajax({
			      url: "getleavedetails?eid="+eid+"&dt="+month,
			      success: function(data) {
			    	  var obj = JSON.parse(data);
			    	  $("#tbody").html(obj.tbody);
			      },
			      type: 'GET'
			   });*/
			}

			function editRecord(rowid, eid,type){
				$("#"+rowid+"_editbtn").html('<a class="btn btn-minier btn-success" onclick="return saveRecord('+rowid+','+eid+',\''+type+'\');">Save</a>');
				$("#"+rowid+"_detailsbtn").html('<a class="btn btn-minier btn-danger" onclick="return cancelSave('+rowid+','+eid+',\''+type+'\');">Cancel</a>');
				$("#"+rowid+"_emp_salary").attr("readonly",true);
		    	$("#"+rowid+"_other_amt").attr("readonly",false);
		    	$("#"+rowid+"_leave_amount").attr("readonly",false);
		    	$("#"+rowid+"_due_deductions").attr("readonly",false);
		    	$("#"+rowid+"_leave_deductions").attr("readonly",false);
		    	$("#"+rowid+"_comments").attr("readonly",false);
				
			}

			function saveRecord(rowid, eid,type){
				salarymonth = $("#month").val();
				pfopted = $("#"+rowid+"_pfopted").val();
				emp_salary = $("#"+rowid+"_emp_salary").val();
				leaves = $("#"+rowid+"_leaves").val();
				other_amount = $("#"+rowid+"_other_amt").val();
				deductions = $("#"+rowid+"_due_deductions").val();
				leave_deductions = $("#"+rowid+"_leave_deductions").val();
				comments = $("#"+rowid+"_comments").val();
				url = "editsalarytransaction?";
				url = url+"eid="+eid;
				url = url+"&pfopted="+pfopted;
				url = url+"&deductions="+deductions;
				url = url+"&other_amt="+other_amount;
				url = url+"&leave_deductions="+leave_deductions;
				url = url+"&emp_salary="+emp_salary;
				url = url+"&comments="+comments;
				url = url+"&month="+salarymonth;

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

				$("#"+rowid+"_emp_salary").attr("readonly",true);
		    	$("#"+rowid+"_other_amt").attr("readonly",true);
		    	$("#"+rowid+"_leave_amount").attr("readonly",true);
		    	$("#"+rowid+"_due_deductions").attr("readonly",true);
		    	$("#"+rowid+"_leave_deductions").attr("readonly",true);
		    	$("#"+rowid+"_comments").attr("readonly",true);
				$("#"+rowid+"_editbtn").html('<a class="btn btn-minier btn-success" onclick="return editRecord('+rowid+','+eid+',\''+type+'\');">Edit</a>');
				$("#"+rowid+"_detailsbtn").html('<a href="#modal-table" role="button" data-toggle="modal" class="btn btn-minier btn-info" onclick="return viewDetails('+rowid+','+eid+',\''+type+'\');">Details</a>');
			}

			function cancelSave(rowid, eid,type){
				$("#"+rowid+"_editbtn").html('<a class="btn btn-minier btn-success" onclick="return editRecord('+rowid+','+eid+',\''+type+'\');">Edit</a>');
				$("#"+rowid+"_detailsbtn").html('<a href="#modal-table" role="button" data-toggle="modal" class="btn btn-minier btn-info" onclick="return viewDetails('+rowid+','+eid+',\''+type+'\');">Details</a>');
				$("#"+rowid+"_emp_salary").attr("readonly",true);
		    	$("#"+rowid+"_other_amt").attr("readonly",true);
		    	$("#"+rowid+"_leave_amount").attr("readonly",true);
		    	$("#"+rowid+"_due_deductions").attr("readonly",true);
		    	$("#"+rowid+"_leave_deductions").attr("readonly",true);
		    	$("#"+rowid+"_comments").attr("readonly",true);
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

			$('.input-daterange').datepicker({autoclose:true,todayHighlight: true});
		
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
			});
			
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
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },
					  { "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false },{ "bSortable": false }
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