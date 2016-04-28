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
			    white-space: nowrap;
			}
			td {
			    white-space: nowrap;
			}
			panel-group .panel {
			    margin-bottom: 20px;
			    border-radius: 4px;
			}
			label{
				text-align: right;
				margin-top: 2px;
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
			    margin-top: 0px;
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
			TRIPS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop
	
	<?php 
		$total_expenses = 0;
		$total_advance = 0;
		$total_fuel_amount = 0;
		$total_incomes = 0;
		$total = 0.0;
		
		$parentId = -1;
		$tripparticulars_arr = array();
		$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->where("modules","like","%LOCAL TRIPS%")->get();
		foreach ($tripparticulars as $tripparticular){
			$tripparticulars_arr[] = $tripparticular->id;
		}
		$select_args = array();
		$select_args[] = "officebranch.name as branchId";
		$select_args[] = "tripparticulars.vehicleId as vehicleId";
		$select_args[] = "tripparticulars.amount as amount";
		$select_args[] = "tripparticulars.date as date";
		$select_args[] = "tripparticulars.remarks as remarks";
		$tripadvances = \TripParticulars::where("tripId","=",$values["id"])->where("tripType","=","LOCAL")->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->leftjoin("officebranch","officebranch.id","=","tripparticulars.branchId")->select($select_args)->get();
		foreach($tripadvances as $tripadvance){
			$total = $total+$tripadvance->amount;
		} 
		$total_advance = $total;
	?>

	@section('page_content')
		<div class="col-xs-offset-4 col-xs-8 ccordion-style1 panel-group">
			<div style="float:left;">
				<a class="btn btn-sm btn-primary" href="addlocaltrip">CREATE/ADD BOOKING</a> &nbsp;&nbsp;
				<a class="btn btn-sm  btn-inverse" href="managetrips?triptype=LOCAL">MANAGE TRIPS</a> &nbsp;&nbsp;
			</div>
			<div style="float:right;">
				<a href="printlocaltrip?id={{$values['id']}}" class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-print bigger-160"></i>
					PRINT REPORT
				</a>
			</div>
		</div>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST1">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;BOOKING INFORMATION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST1">
					<div class="panel-body" style="padding: 0px">
					<div class="col-xs-offset-0 col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>SOURCE JOURNEY INFO</th>
									<th>RETURN JOURNEY INFO</th>
									<th>CUSTOMER INFO</th>
									<th>BOOKING AMOUNT INFO</th>
								</tr>
							</thead>
							<tbody>
								<tr>
							<?php 
								if(isset($values["id"])){
									$entities = \BusBookings::where("id","=",$values["id"])->get();
									foreach ($entities as $entity){
										$entity["source_date"] = date("d-m-Y",strtotime($entity["source_date"]));
										$entity["dest_date"] = date("d-m-Y",strtotime($entity["dest_date"]));
										if($entity["dest_date"] == "01-01-1970"){
											$entity["dest_date"] = "";
										}
										$entity["sourcetrip"] = $entity["source_start_place"]."<br/> ".$entity["source_end_place"];
										$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>Date & Time &nbsp;: ".$entity["source_date"]." ".$entity["source_time"];
										$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>Bus Type  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$entity["source_bustype"];
										$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>No of buses &nbsp;&nbsp;: ".$entity["source_busno"];
										$entity["returntrip"] = $entity["dest_start_place"]."<br/> ".$entity["dest_end_place"];
										$entity["returntrip"] = $entity["returntrip"]."<br/>Date & Time &nbsp;: ".$entity["dest_date"]." ".$entity["dest_time"];
										$entity["returntrip"] = $entity["returntrip"]."<br/>Bus Type  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$entity["dest_bustype"];
										$entity["returntrip"] = $entity["returntrip"]."<br/>No of buses &nbsp;&nbsp;: ".$entity["dest_busno"];
										
										$entity["custinfo"] = "Name : ".$entity["cust_name"]."<br/>Phone : ".$entity["cust_phone"];
										$entity["custinfo"] = $entity["custinfo"]."<br/><br/><br/><span>Fuel Charge By : ".$entity["fuel_charge_type"]."</span>";
										$entity["amount"] = "TOTAL &nbsp;&nbsp;&nbsp;   &nbsp;  : ".$entity["total_cost"]."<br/>ADVANCE : ".$total_advance."<br/>BALANCE : ".($entity["total_cost"]-$total_advance);
										$entity["amount"] = $entity["amount"]."<br/><br/><span style='font-size: 15px;font-weight: bold;color: red;'>BOOKING NO : ".$entity["booking_number"]."</span>";
										echo "<td>".$entity->sourcetrip."</td>";
										echo "<td>".$entity->returntrip."</td>";
										echo "<td>".$entity->custinfo."</td>";
										echo "<td>".$entity->amount."</td>";
									}
								}
							?>
								</tr>
							</tbody>
						</table>
					</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-offset-2 col-xs-10 ccordion-style1 panel-group">
			<?php if($values['type'] != "advances"){ ?>
				<a class="btn btn-sm  btn-purple" href="addlocaltripparticular?id={{$values['id']}}&type=advances">ADD BOOKING ADVANCE</a>&nbsp;&nbsp;
			<?php } if($values['type'] != "vehicleadvances"){ ?>
				<a class="btn btn-sm  btn-purple" href="addlocaltripparticular?bookingid={{$values['id']}}&id={{$values['id']}}&type=vehicleadvances">ADD VEHICLE ADVANCE</a>&nbsp;&nbsp;
			<?php } if($values['type'] != "expenses_and_incomes"){ ?>
				<a class="btn btn-sm btn-purple" href="addlocaltripparticular?id={{$values['id']}}&type=expenses_and_incomes">ADD TRIP EXPESENSES</a> &nbsp;&nbsp;
			<?php }?>
				<a class="btn btn-sm btn-purple" href="addlocaltripfuel?id={{$values['id']}}&triptype=LOCAL&transtype=fuel">ADD TRIP FUEL EXPENSES</a> &nbsp;&nbsp;
				<a class="btn btn-sm btn-purple" href="assigndrivervehicle?id={{$values['id']}}&triptype=LOCAL">ASSIGN DRIVER & VEHICLE</a> &nbsp;&nbsp;
				<a class="btn btn-sm btn-purple" href="bookingrefund?id={{$values['id']}}&triptype=LOCAL&transtype=bookingrefund">BOOKING REFUND</a> &nbsp;&nbsp;
		</div>
		<?php $url = "addlocaltripparticular?triptype=LOCAL&tripid=".$values["id"]."&type=".$values['type']; ?>
		<?php  if(isset($values["type"]) && $values["type"] == "advances"){?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tripadvances">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP BOOKING ADVANCES INFORMATION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="tripadvances">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						<div class="col-xs-offset-0 col-xs-12" style="margin-top: 0%; margin-bottom: 0%">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="padding:2%;">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>DATE</th>
										<th>AMOUNT</th>
										<th>TO BRANCH/BANK</th>
										<th>REMARKS</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php 
											$total = 0.0;
											$parentId = -1;
											$tripparticulars_arr = array();
											$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
											if(count($parent)>0){
												$parent = $parent[0];
												$parentId = $parent->id;
											}
											$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->where("modules","like","%LOCAL TRIPS%")->get();
											foreach ($tripparticulars as $tripparticular){
												$tripparticulars_arr[] = $tripparticular->id;
											}
											$select_args = array();
											$select_args[] = "tripparticulars.amount as amount";
											$select_args[] = "tripparticulars.date as date";
											$select_args[] = "tripparticulars.remarks as remarks";
											$tripadvances = \TripParticulars::where("tripId","=",$values["id"])->where("tripType","=","LOCAL")->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->get();
											foreach($tripadvances as $tripadvance){
												if($tripadvance->branchId>0){
													$branch = OfficeBranch::where("id","=",$tripadvance->branchId)->first();
													$tripadvance->branchId = $branch->name;
												}
												if($tripadvance->bankId>0){
													$branch = BankDetails::where("id","=",$tripadvance->bankId)->first();
													$tripadvance->branchId = $branch->bankName."-".$branch->accountNo;
												}
												$total = $total+$tripadvance->amount;
										?>
										<td>{{date("d-m-Y",strtotime($tripadvance->date))}}</td>
										<td>{{$tripadvance->amount}}</td>
										
										<td>{{$tripadvance->branchId}}</td>
										<td style="width:45%";>{{$tripadvance->remarks}}</td>
									</tr>
									<?php } $total_advance = $total;?>
									<tr>
										<td colspan="4" style="text-align: right;padding-right: 15%;font-size: 14px;font-weight: bold;">TOTAL ADVANCE AMOUNT : {{$total}}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php }  if(isset($values["type"]) && $values["type"] == "vehicleadvances"){?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tripvehicleadvances">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP VEHICLE ADVANCES INFORMATION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="tripvehicleadvances">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						<div class="col-xs-offset-0 col-xs-12" style="margin-top: 0%; margin-bottom: 0%">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="padding:2%;">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>DATE</th>
										<th>AMOUNT</th>
										<th>VEHICLE</th>
										<th>FROM BRANCH</th>
										<th>REMARKS</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php 
											$total = 0.0;
											$parentId = -1;
											$tripparticulars_arr = array();
											$parent = \LookupTypeValues::where("name","=","TRIP VEHICLE ADVANCES")->get();
											if(count($parent)>0){
												$parent = $parent[0];
												$parentId = $parent->id;
											}
											$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->where("modules","like","%LOCAL TRIPS%")->get();
											foreach ($tripparticulars as $tripparticular){
												$tripparticulars_arr[] = $tripparticular->id;
											}
											$select_args = array();
											$select_args[] = "officebranch.name as branchId";
											$select_args[] = "tripparticulars.vehicleId as vehicleId";
											$select_args[] = "tripparticulars.amount as amount";
											$select_args[] = "tripparticulars.date as date";
											$select_args[] = "tripparticulars.remarks as remarks";
											$tripadvances = \TripParticulars::where("tripId","=",$values["id"])->where("tripType","=","LOCAL")->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->leftjoin("officebranch","officebranch.id","=","tripparticulars.branchId")->select($select_args)->get();
											foreach($tripadvances as $tripadvance){
												$total = $total+$tripadvance->amount;
												$vehicle = Vehicle::where("id","=",$tripadvance->vehicleId)->first();
												$tripadvance->vehicleId = $vehicle->veh_reg;
										?>
										<td>{{date("d-m-Y",strtotime($tripadvance->date))}}</td>
										<td>{{$tripadvance->amount}}</td>
										<td>{{$tripadvance->vehicleId}}</td>
										<td>{{$tripadvance->branchId}}</td>
										<td style="width:45%";>{{$tripadvance->remarks}}</td>
									</tr>
									<?php } $total_advance = $total;?>
									<tr>
										<td colspan="5" style="text-align: right;padding-right: 15%;font-size: 14px;font-weight: bold;">TOTAL ADVANCE AMOUNT : {{$total}}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php } if(isset($values["expenses"])){?>
			<div class="col-xs-12" style="border: 1px solid #D5D5D5; margin-left: 14px; margin-top: 10px; margin-bottom: 15px; max-width:98%">
			<div class="row" style="background-color: #307ECC; padding:5px;font-weight: bold;font-size: 13px;line-height: 1;">
				<div style="margin-top: 5px; color:white; float:left;">
						&nbsp;ADD TRIP EXPENSES &nbsp; - &nbsp;  (ADD / REMOVE ROW)
					</a>
				</div>
				<div style="margin-top: 5px; margin-right:10px; float:right; color:white" ><i id="children_add" class="ace-icon fa fa-plus-circle bigger-160"></i> &nbsp;&nbsp; <i id="children_remove" class="ace-icon fa fa-minus-circle bigger-160"></i> &nbsp;&nbsp; <i id="children_refresh" class="ace-icon fa fa-refresh bigger-160"></i></div>
			</div>
			<?php 
				$vehicles = OfficeBranch::All();
				$parentId = -1;
				$parent = \LookupTypeValues::where("name","=","EXPENSE")->get();
				if(count($parent)>0){
					$parent = $parent[0];
					$parentId = $parent->id;
				}
				$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->where("modules","like","%LOCAL TRIPS%")->get();
				$tripparticulars_arr = array();
				$expense_fields = array();
				foreach ($tripparticulars as $tripparticular){
					$tripparticulars_arr[$tripparticular->id] = $tripparticular->name;
					$expense_fields[] = $tripparticular->id;
				}
				$tripexpenses =\TripParticulars::where("tripId","=",$values["id"])->where("tripType","=","LOCAL")->get();
				foreach ($tripexpenses as $tripexpense){
					if(in_array($tripexpense->lookupValueId, $expense_fields)){
						$total_expenses = $total_expenses+$tripexpense->amount;
					}
				}
				$tripfuelexpenses =\FuelTransaction::where("tripId","=",$values["id"])->where("paymentType","=","advance")->get();
				foreach ($tripfuelexpenses as $tripfuelexpense){
					$total_fuel_amount = $total_fuel_amount+$tripfuelexpense->amount;
				}
			?>
			<form name="tripsform" action="{{$url}}" method="post" onsubmit="return validateData();">
			<div class="row col-xs-12" id="children_fields_all" style="margin-top:10px;">
				<div id="children_fields" style="padding-top: 7px; padding-bottom: 2px;" class="children_fields">
					<div id="row0" class="">								
						<div class="form-group inline" style="float:left;width:30%;">
							<label class="col-xs-5 control-label no-padding-right" for="form-field-1">PARTICULAR NAME</label>
							<div class="col-xs-7">
								<select class="form-control item chosen-select" id="item0" name="lookupvalue[]" onchange="getItemInfo(this.value, this.id)">
									<option value="">-- item --</option>
									<?php 
										foreach ($tripparticulars_arr as $key=>$val){
											echo "<option value='".$key."'>".$val."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group inline" style="float:left; width:18%;">
							<div class="col-xs-11 col-xs-offset-0">
								<select name="vehicle[]" class="form-control chosen-select">
									<?php 
										echo "<option value=''>select vehicle</option>";
										$bookingId = \BusBookings::where("id","=",$values["id"])->get();
										if(count($bookingId)>0){
											$bookingId = $bookingId[0];
											$bookingId = $bookingId->booking_number;
										}
										$veh_ids = \BookingVehicles::where("booking_number","=", $bookingId)->get();
										$veh_ids_arr = array();
										foreach ($veh_ids as $veh_id){
											$veh_ids_arr[] = $veh_id->vehicleId;
										}
										$vehicles =  \Vehicle::whereIn("id",$veh_ids_arr)->get();
										foreach($vehicles as $vehicle){
											echo "<option value='".$vehicle->id."'>".$vehicle->veh_reg."</option>";
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group inline" style="float:right; width: 51%; margin-right: 0%; margin-left: 1%;">
							<label style="width:10%; float:left; margin-right:5px;" class="control-label no-padding-right" for="form-field-1"> AMOUNT </label>
							<div style="width:15%; float:left; margin-right:15px;">
								<input type="text" id="qty0" name="amount[]" class="form-control qty" onchange="qtyChange(this.id)">
							</div>
							<div style="width:71%; float:right; ">
								<input type="text" id="remarks0" placeholder="remarks"  name="remarks[]" class="form-control remarks" >
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12" style="min-width:104%; margin-left:-2%">
				<div style="background-color: #F5F5F5; border: 1px solid #E5E5E5; padding: 19px 20px 20px">
					<div style="margin-left:34%;">
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
			</div>
			</form>
		</div>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tripadvances">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP INCOME & EXPENSES SUMMARY
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="tripadvances">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						<div class="col-xs-offset-0 col-xs-12" style="margin-top: 0%; margin-bottom: 0%">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="padding:2%;">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>TOTAL INCOMES</th>
										<th>TOTAL EXPENSES</th>
										<th>BALANCE</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="" style="font-weight: bold; vertical-align: middle">
											<div style="float: left;">TOTAL ADVANCES </div> <div style="color:green;font-size: 16px;text-align: right;">{{$total_advance}}</div> 
											<div style="float: left;">TOTAL INCOMES </div> <div style="color:green;font-size: 16px;text-align: right;">{{$total_incomes}}</div>
										</td>
										<td class="" style="font-weight: bold; vertical-align: middle">
											<div style="float: left;">TOTAL EXPENSES  </div> <div style="color:red;font-size: 16px;text-align: right;">{{$total_expenses}}</div> 
											<div style="float: left;">FUEL EXPENSES(from Advance) </div> <div style="color:red;font-size: 16px;text-align: right;">{{$total_fuel_amount}}</div>
										</td>
										<td>
											<div style="float: left;">REMAINING AMOUNT(+/-) </div> <div style="font-size: 16px;text-align: right;">{{($total_advance+$total_incomes)-($total_expenses+$total_fuel_amount)}}</div>
										</td>
									</tr>
								</tbody>
							</table>
							<form name="tripsform" class="form-inline" action="{{$url}}" method="post" onsubmit="return validateData();">
								<table id="simple-table" style="margin-top:2%;" class="table table-striped table-bordered table-hover">
									<tbody>
										<tr>
											<td>
												<span style="font-weight: bold; font-size:12px;">DEBIT/CREDIT TO BRANCH &nbsp;&nbsp;</span>
												<select name="branch[]" id="branch" class="form-control" required="required">
													echo "<option value=''>select branch</option>";
												<?php 
													$vehicles = OfficeBranch::All();
													foreach($vehicles as $vehicle){
														echo "<option value='".$vehicle->id."'>".$vehicle->name."</option>";
													}
												?>
												</select>
											</td>
											<td>
												<span style="font-weight: bold; font-size:12px;">DATE &nbsp;&nbsp;</span>
												<input type="text" class="form-control date-picker" name="date[]" id="date}}"   />	
											</td>
											<td>
												<span style="font-weight: bold; font-size:12px;">AMOUNT &nbsp;&nbsp;</span>
												<input type="text" class="form-control" readonly="readonly" name="amount[]" id="amount" value="{{($total_advance+$total_incomes)-($total_expenses+$total_fuel_amount)}}"/>	
											</td>
											<td style="width:43%; font-weight: bold; vertical-align: middle">
												<span style=" font-weight: bold; font-size:12px;">REMARKS</span>
												<input type="text" style="min-width: 300px;" class="form-control" name="remarks[]" id="remarks" />
											</td>
											<input type="hidden" name="tripid" value="{{$values['id']}}"/>
											<input type="hidden" name="ids[]" value="0"/>
											<?php 
												$rem = ($total_advance+$total_incomes)-($total_expenses+$total_fuel_amount);
												if($rem<0){
													echo '<input type="hidden" name="lookupvalue[]" value="9999" />';
													echo '<input type="hidden" name="names[]" value="DEBITED AMOUNT FROM BRANCH" />';
												}
												else{
													echo '<input type="hidden" name="lookupvalue[]" value="8888" />';
													echo '<input type="hidden" name="names[]" value="CREDITED AMOUNT TO BRANCH" />';
												}
											?>
											
										</tr>
									</tbody>
								</table>
								<div class="clearfix form-actions" style="margin-bottom: 0px;" >
									<div class="col-md-offset-4 col-md-8"  >
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
					</div>
				</div>
			</div>
		</div>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">	
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;METER READING & CLOSING INFO
						</a>
					</h4>
				</div>
				<form class="form-horizontal" action="closetrip" method="post" role="form" name="closetrip">
				<input type="hidden" name="tripid" value="{{$values['id']}}"/>
				<div class="panel-collapse collapse in" id="collapseOne">
					<div class="panel-body">
						<?php 
							$bookingId = \BusBookings::where("id","=",$values["id"])->get();
							if(count($bookingId)>0){
								$bookingId = $bookingId[0];
								$bookingId = $bookingId->booking_number;
							}
							$veh_ids = \BookingVehicles::where("booking_number","=", $bookingId)->get();
							$veh_ids_arr = array();
							foreach ($veh_ids as $veh_id){
								$veh_ids_arr[] = $veh_id->vehicleId;
							}
							$vehicles =  \Vehicle::whereIn("id",$veh_ids_arr)->get();
							foreach($vehicles as $vehicle){
						?>
						<div class="row">
							<div class="col-xs-2">
								<input type="hidden" id="vehid" name="vehicleid[]" value="{{$vehicle->id}}" class="form-control">
								<div class="form-group">
									<div class="col-xs-12">
										<input type="text" id="vehiclename" name="vehiclename[]" readonly="readonly" value="{{$vehicle->veh_reg}}" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="col-xs-6 control-label no-padding-right" for="form-field-1">Closing Reading  </label>
									<div class="col-xs-6">
										<input type="text" id="closingreading" name="closingreading[]"  class="form-control number">
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label class="col-xs-5 control-label no-padding-right" for="form-field-1"> Closing Date  </label>
									<div class="col-xs-7">
										<input type="text" id="closingdate" name="closingdate[]"  required="required" class="form-control date-picker">
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> Remarks  </label>
									<div class="col-xs-9">
										<input type="text" id="remarks" name="remarks[]"  class="form-control">
									</div>
								</div>
							</div>
						</div>	
						<?php 
							}
						?>							
					</div>
					<input type="hidden" name="triptype" value="local"/>
				</div>
				<div class="clearfix form-actions" style="margin-bottom: 0px;" >
					<div class="col-md-offset-4 col-md-8"  >
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
		</div>
		
		<?php }?>
		<?php if(isset($values["type"]) && ($values["type"]=="advances" || $values["type"]=="vehicleadvances")){ ?>
		<?php if($values["type"]=="advances"){ ?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tripadvances">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP BOOKING ADVANCES
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="tripadvances">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						<div class="col-xs-offset-0 col-xs-12" style="margin-top: 0%; margin-bottom: 0%">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="center">
											<label class="pos-rel">
												<span class="lbl"></span>
											</label>
										</th>
										<th>PARTICULAR NAME</th>
										<th>AMOUNT</th>
										<th>TO BRANCH/BANK</th>
										<th>DATE</th>
										<th>REMARKS</th>
									</tr>
								</thead>
								<form name="tripsform" action="{{$url}}" method="post" onsubmit="return validateData();">
									<tbody>
										<?php 
											$vehicles = OfficeBranch::All();
											$banks = BankDetails::All();
											$parentId = -1;
											$tripparticulars_arr = array();
											$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
											if(count($parent)>0){
												$parent = $parent[0];
												$parentId = $parent->id;
											}
											$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->where("modules","like","%LOCAL TRIPS%")->get();
											foreach ($tripparticulars as $tripparticular){
												$fields = array();
												$fields ['id'] = $tripparticular->id;
												$fields ['name'] = $tripparticular->name;
												$fields ['branch'] = 0;
												$fields ['bank'] = 0;
												$showfields = explode(",", $tripparticular->fields);
												if(in_array("BRANCH",$showfields)){
													$fields['branch'] = 1;												
												}
												if(in_array("BANK",$showfields)){
													$fields['bank'] = 1;
												}
												$tripparticulars_arr[] = $fields;
											}
											$i = 0;
											foreach ($tripparticulars_arr as $tripparticular){
										?>
										<tr>
											<td class="center" style="font-weight: bold; vertical-align: middle">
												<label class="pos-rel">
													<input type="checkbox" class="ace" name="ids[]" id="ids_{{$i}}" value="{{$i}}"/>
													<span class="lbl"></span>
												</label>
												<input type="hidden" name="lookupvalue[]" id="id_{{$i}}" value="{{$tripparticular['id']}}" />
												<input type="hidden" name="names[]" id="names_{{$i}}" value="{{$tripparticular['name']}}" />
											</td>
											<td style="font-weight: bold; vertical-align: middle">
												<span style="color: red; font-weight: bold; font-size:14px;">{{$tripparticular['name']}}</span>
											</td>
											<td>
												<input type="text" class="form-control" name="amount[]" id="amount_{{$i}}" />	
											</td>
											<td>
												<?php if($tripparticular["branch"] == 1){ ?>
													<select name="branch[]" id="branch_{{$i}}" class="form-control chosen-select">
														echo "<option value=''>select branch</option>";
													<?php 
														foreach($vehicles as $vehicle){
															echo "<option value='".$vehicle->id."'>".$vehicle->name."</option>";
														}
													?>
													</select>
												<?php } else {?>
													<input name="branch[]" id="branch_{{$i}}" type="hidden" class="form-control">
												<?php } ?>
												<?php if($tripparticular["bank"] == 1){ ?>
													<select name="bank[]" id="bank_{{$i}}" class="form-control chosen-select">
														echo "<option value=''>select bank</option>";
													<?php 
														foreach($banks as $bank){
															echo "<option value='".$bank->id."'>".$bank->bankName."-".$bank->accountNo."</option>";
														}
													?>
													</select>
												<?php } else {?>
													<input name="bank[]" id="bank_{{$i}}" type="hidden" class="form-control">
												<?php } ?>
											</td>
											<td>
												<input type="text" class="form-control date-picker" name="date[]" id="date_{{$i}}" />	
											</td>
											<td style="width:35%"; class="center" style="font-weight: bold; vertical-align: middle">
												<input type="text" class="form-control" name="remarks[]" id="remarks_{{$i}}" />
											</td>
										</tr>
										<?php $i++; }?>
									</tbody>
									
							</table>
							<div class="clearfix form-actions" style="margin-bottom: 0px;" >
								<div class="col-md-offset-4 col-md-8"  >
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
						</div><!-- /.span -->
					</div>
				</div>
			</div>
		</div>	
		<?php } if(isset($values["type"]) && $values["type"] == "vehicleadvances"){?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tripadvances">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP VEHICLE ADVANCES
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="tripadvances">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						<div class="col-xs-offset-0 col-xs-12" style="margin-top: 0%; margin-bottom: 0%">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="center">
											<label class="pos-rel">
												<span class="lbl"></span>
											</label>
										</th>
										<th>PARTICULAR NAME</th>
										<th>AMOUNT</th>
										<th>FROM BRANCH</th>
										<th>VEHICLE</th>
										<th>DATE</th>
										<th>REMARKS</th>
									</tr>
								</thead>
								<form name="tripsform" action="{{$url}}" method="post" onsubmit="return validateData();">
									<tbody>
										<?php 
											$branches = OfficeBranch::All();
											
											$select_args = array();
											$select_args[] = "employee.fullName as name";
											$select_args[] = "inchargeaccounts.id as id";
											$incharges = InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empid")->select($select_args)->get();
											
											$bookingId = \BusBookings::where("id","=",$values["bookingid"])->get();
											if(count($bookingId)>0){
												$bookingId = $bookingId[0];
												$bookingId = $bookingId->booking_number;
											}
											$veh_ids = \BookingVehicles::where("booking_number","=", $bookingId)->get();
											$veh_ids_arr = array();
											foreach ($veh_ids as $veh_id){
												$veh_ids_arr[] = $veh_id->vehicleId;
											}
											$vehicles =  \Vehicle::whereIn("id",$veh_ids_arr)->get();
											
											$parentId = -1;
											$tripparticulars_arr = array();
											$parent = \LookupTypeValues::where("name","=","TRIP VEHICLE ADVANCES")->get();
											if(count($parent)>0){
												$parent = $parent[0];
												$parentId = $parent->id;
											}
											$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->where("modules","like","%LOCAL TRIPS%")->get();
											foreach ($tripparticulars as $tripparticular){
												$fields = array();
												$fields ['id'] = $tripparticular->id;
												$fields ['name'] = $tripparticular->name;
												$fields ['branch'] = 0;
												$fields ['vehicle'] = 0;
												$showfields = explode(",", $tripparticular->fields);
												if(in_array("BRANCH",$showfields)){
													$fields['branch'] = 1;												
												}
												if(in_array("VEHICLE",$showfields)){
													$fields['vehicle'] = 1;
												}
												if(in_array("INCHARGE",$showfields)){
													$fields['incharge'] = 1;
												}
												$tripparticulars_arr[] = $fields;
											}
											$i = 0;
											foreach ($tripparticulars_arr as $tripparticular){
										?>
										<tr>
											<td class="center" style="font-weight: bold; vertical-align: middle">
												<label class="pos-rel">
													<input type="checkbox" class="ace" name="ids[]" id="ids_{{$i}}" value="{{$i}}"/>
													<span class="lbl"></span>
												</label>
												<input type="hidden" name="lookupvalue[]" id="id_{{$i}}" value="{{$tripparticular['id']}}" />
												<input type="hidden" name="names[]" id="names_{{$i}}" value="{{$tripparticular['name']}}" />
											</td>
											<td style="font-weight: bold; vertical-align: middle">
												<span style="color: red; font-weight: bold; font-size:14px;">{{$tripparticular['name']}}</span>
											</td>
											<td>
												<input type="text" class="form-control" name="amount[]" id="amount_{{$i}}" />	
											</td>
											<td>
												<?php if(isset($tripparticular["branch"]) && $tripparticular["branch"] == 1){ ?>
													<select name="branch[]" id="branch_{{$i}}" class="form-control chosen-select">
														echo "<option value=''>select branch</option>";
													<?php 
														foreach($branches as $branch){
															echo "<option value='".$branch->id."'>".$branch->name."</option>";
														}
													?>
													</select>
													<input name="incharge[]" id="incharge_{{$i}}" type="hidden" class="form-control">
												<?php } else if(isset($tripparticular["incharge"]) && $tripparticular["incharge"] == 1){ ?>
													<select name="incharge[]" id="incharge_{{$i}}" class="form-control chosen-select">
														echo "<option value=''>select incharge</option>";
													<?php 
														foreach($incharges as $incharge){
															echo "<option value='".$incharge->id."'>".$incharge->name."</option>";
														}
													?>
													</select>
													<input name="branch[]" id="branch_{{$i}}" type="hidden" class="form-control">
												<?php } else {?>
													<input name="branch[]" id="branch_{{$i}}" type="hidden" class="form-control">
													<input name="incharge[]" id="incharge_{{$i}}" type="hidden" class="form-control">
												<?php } ?>
											</td>
											<td>
												<?php if($tripparticular["vehicle"] == 1){ ?>
													<select name="vehicle[]" id="vehicle_{{$i}}" class="form-control chosen-select">
														echo "<option value=''>select vehicle</option>";
													<?php 
														foreach($vehicles as $vehicle){
															echo "<option value='".$vehicle->id."'>".$vehicle->veh_reg."</option>";
														}
													?>
													</select>
												<?php } else {?>
													<input name="vehicle[]" id="vehicle_{{$i}}" type="hidden" class="form-control">
												<?php } ?>
											</td>
											<td>
												<input type="text" class="form-control date-picker" name="date[]" id="date_{{$i}}" />	
											</td>
											<td style="width:25%"; class="center" style="font-weight: bold; vertical-align: middle">
												<input type="text" class="form-control" name="remarks[]" id="remarks_{{$i}}" />
											</td>
										</tr>
										<?php $i++; }?>
									</tbody>
									
							</table>
							<div class="clearfix form-actions" style="margin-bottom: 0px;" >
								<div class="col-md-offset-4 col-md-8"  >
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
						</div><!-- /.span -->
					</div>
				</div>
			</div>
		</div>
		<?php } ?>	
		<?php } ?>
		</div>	
		<!-- div.dataTables_borderWrap -->
			<div>
				<table id="dynamic-table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<?php 
								$theads = $values['theads'];
								foreach($theads as $thead){
									echo "<th>".strtoupper($thead)."</th>";
								}
							?>
						</tr>
					</thead>
				</table>								
			</div>	
		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal)
		<?php }} ?>
		
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

			function modalEditTripParticulars(id,lookupvalue, date, amount, branch, remarks, status){
				$("#branch2").prop("disabled",false);
				$("#incharge1").prop("disabled",false);
				$("#lookupvalue1 option").each(function() {this.selected = (this.text == lookupvalue); });		
				$("#lookupvalue1").prop("disabled",true);	
				$("#date1").val(date);
				$("#amount1").val(amount);
				$("#remarks1").text(remarks);
				if(branch==""){
					$("#branch2").prop("disabled",true);
					$("#incharge1").prop("disabled",true);
				}
				else{
					$("#branch2 option").each(function() {this.selected = (this.text == branch); });
					$("#incharge1 option").each(function() {this.selected = (this.text == branch); });
				}
				$("#amou option").each(function() { this.selected = (this.value == driver2id); });
				$("#helper option").each(function() { this.selected = (this.value == helperid); });
				$("#status option").each(function() { this.selected = (this.value == status); });
				$("#id1").val(id);
				$('.chosen-select').trigger('chosen:updated');		
			}
			

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			function validateData(){
				var ids = document.forms['tripsform'].elements[ 'ids[]' ];
				for(i=0; i<ids.length;i++){
					if(ids[i].checked){
						if($("#amount_"+i).val()==""){
							alert("select complete information for trip particular : "+$("#names_"+i).val());
							return false;
						}
						if($("#remarks_"+i).val()==""){
							alert("select complete information for trip particular : "+$("#names_"+i).val());
							return false;
						}
					}
				    
				}
			};

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
			
			$("#children_add").on("click",function(){
    		    ele = $('#children_fields:last-child').clone();
    		    ele.appendTo('#children_fields_all');
    		    $('#children_fields:last-child select').removeClass("chosen-select").removeAttr("id").css("display", "block").next().remove();
                $('#children_fields:last-child select').addClass("chosen-select");
                $('#children_fields:last-child input').val("");
                $('#children_fields:last-child select').chosen();
			  });
	
			  $("#children_remove").on("click",function(){
					if(($(".children_fields").length)>1)
						$('#children_fields:last-child').remove();
			  });
	
			  $("#children_refresh").on("click",function(){
				  	ele = $('#children_fields:last-child').clone();
				  	$('#children_fields:last-child').remove();
	    		    ele.appendTo('#children_fields_all');
	    		    $('#children_fields:last-child select').removeClass("chosen-select").removeAttr("id").css("display", "block").next().remove();
	                $('#children_fields:last-child select').addClass("chosen-select");
	                $('#children_fields:last-child input').val("");
	                $('#children_fields:last-child select').chosen();
			  });
			

			
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
				//initiate dataTables plugin
				var myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)

				//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
		
				.DataTable( {
					bJQueryUI: true,
					"bPaginate": true, "bDestroy": true,
					bInfo: true,
					"aoColumns": [
					  <?php $cnt=count($values["theads"]); for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
					],
					"aaSorting": [],
					oLanguage: {
				        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
				    },
					"bProcessing": true,
			        "bServerSide": true,
					"ajax":{
		                url :"gettripsdatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
		                type: "post",  // method  , by default get
		                error: function(){  // error handling
		                    $(".employee-grid-error").html("");
		                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
		                    $("#employee-grid_processing").css("display","none");
		 
		                }
		            },
			
					//"sScrollY": "500px",
					//"bPaginate": false,
					"sScrollX" : "true",
					//"sScrollX": "300px",
					//"sScrollXInner": "120%",
					"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
			
					//"iDisplayLength": 50
			
			
					select: {
						style: 'multi'
					}
			    } );
			
				
				
				$.fn.dataTable.Buttons.swfPath = "../assets/js/dataTables/extensions/buttons/swf/flashExport.swf"; //in Ace demo ../assets will be replaced by correct assets path
				$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
				
				/*new $.fn.dataTable.Buttons( myTable, {
					buttons: [
					  {
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					  },
					  {
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: false,
						message: 'This print was produced using the Print button for DataTables'
					  }		  
					]
				} );
				myTable.buttons().container().appendTo( $('.tableTools-container') );
				*/
				
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
				
				
				
				
				
				myTable.on( 'select', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
					}
				} );
				myTable.on( 'deselect', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
					}
				} );
			
			
			
			
				/////////////////////////////////
				//table checkboxes
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
				
				//select/deselect all rows according to table header checkbox
				$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$('#dynamic-table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) myTable.row(row).select();
						else  myTable.row(row).deselect();
					});
				});
				
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
				$('<button style="margin-top:-5px;" class="btn btn-minier btn-primary" id="refresh"><i style="margin-top:-2px; padding:6px; padding-right:5px;" class="ace-icon fa fa-refresh bigger-110"></i></button>').appendTo('div.dataTables_filter');
				$("#refresh").on("click",function(){ myTable.search( '', true ).draw(); });
			});
		</script>
	@stop