<?php
use Illuminate\Support\Facades\Input;
?>
<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<!-- Apple devices fullscreen -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<!-- Apple devices fullscreen -->
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<title></title>
	<style type="text/css" media="print">
        @page 
        {
            size: auto;   /* auto is the current printer page size */
            margin: 8mm;  /* this affects the margin in the printer settings */
        }

        body 
        {
            margin: 0px;  /* the margin on the content before printing */
       }
    </style>
	</head>
<body>
	<div style="width:200mm;  margin: auto;">
  	<table width="100%">
		<tr>
			<td align='center' style="color:#8A0808;"><b>NEW MORNING STAR TRAVELS<br/>(TRIP CLOSING REPORT - Genereated by Ashok J)</b></td>
		</tr>
		<tr>
			<td align='center'><hr style="color: #8A0808; background-color: #8A0808;height: 3px;"/></td>
		</tr>
		<tr>
			<?php 
				$values = Input::All();
				$total_advances = 0;
				$total_expenses = 0;
				$total_advance = 0;
				$total_fuel_amount = 0;
				$total_incomes = 0;
				$select_args[] = "vehicle.veh_reg as vehicleId";
				$select_args[] = "tripdetails.tripStartDate as tripStartDate";
				$select_args[] = "tripdetails.id as routeInfo";
				$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
				$select_args[] = "tripdetails.routeCount as routes";
				$select_args[] = "tripdetails.id as id";
				
				if(isset($values["tripid"])){
					$entities = \TripDetails::where("tripdetails.id","=",$values["tripid"])->leftjoin("vehicle", "vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
					foreach ($entities as $entity){
						$entity["tripStartDate"] = date("d-m-Y",strtotime($entity["tripStartDate"]));
						$entity["tripCloseDate"] = date("d-m-Y",strtotime($entity["tripCloseDate"]));
						if($entity["tripCloseDate"] == "01-01-1970"){
							$entity["tripCloseDate"] = "NOT CLOSED";
						}
						$entity["fuelamount"] = 0;
						$entity["routeInfo"] = "";
						$entity["totalAdvance"] = \TripAdvances::where("tripID","=",$entity->id)->where("deleted","=","No")->sum("amount");
						$routeInfo = "";
						$driver1 = "NOT ASSIGNED";
						$driver2 = "NOT ASSIGNED";
						$helper = "NOT ASSIGNED";
						$employees = \Employee::where("roleId","=","19")->orWhere("roleId","=","20")->get();
						$tripservices = \TripServiceDetails::where("tripId","=",$entity->id)->where("status","=","Running")->get();
						foreach($tripservices as $tripservice){
							$select_args = array();
							$select_args[] = "cities.name as sourceCity";
							$select_args[] = "cities1.name as destinationCity";
							$select_args[] = "servicedetails.serviceNo as serviceNo";
							$select_args[] = "servicedetails.active as active";
							$select_args[] = "servicedetails.serviceStatus as serviceStatus";
							$select_args[] = "servicedetails.id as id";
							$service = \ServiceDetails::where("servicedetails.id","=",$tripservice->serviceId)->join("cities","cities.id","=","servicedetails.sourceCity")->join("cities as cities1","cities1.id","=","servicedetails.destinationCity")->select($select_args)->get();
							if(count($service)>0){
								$service = $service[0];
								$routeInfo = $routeInfo.$service->serviceNo." (".$service->sourceCity." - ".$service->destinationCity.") &nbsp; ";
							}
							foreach ($employees as $employee){
								if($employee->id == $tripservice->driver1){
									$driver1 = $employee->fullName;
								}
								else if($employee->id == $tripservice->driver2){
									$driver2 = $employee->fullName;
								}
								else if($employee->id == $tripservice->helper){
									$helper = $employee->fullName;
								}
							}
						}
						$entity["routeInfo"] = $routeInfo;
			?>
			<td align='center'>
				<table width="100%">
					<tr>	
						<td width="48%">Vehicle Reg #: {{$entity->vehicleId}}</td>
						<td width="4%">&nbsp;</td>
						<td width="48%" align="right">Driver1: {{$driver1}}</td>
					</tr>
					<tr>	
						<td width="48%">Trip Start Date: {{$entity->tripStartDate}}</td>
						<td width="4%">&nbsp;</td>
						<td width="48%"  align="right">Driver2: {{$driver2}}</td>
					</tr>
					<tr>	
						<td width="48%">Trip Close Date: {{$entity->tripCloseDate}}</td>
						<td width="4%">&nbsp;</td>
						<td width="48%"  align="right">Helper: {{$helper}}</td>
					</tr>
				</table>		
			</td>
		</tr>
		<tr>
			<td align='left'>Trip Service Routes: {{$routeInfo}}</td>
		</tr>
		<?php 
			} }
		?>
	</table>
	<table width="100%">
		<tr>
			<td style="width:60%;vertical-align: top;">
				<table width="100%" border='1' cellpadding='2' cellspacing='0' style="border: 1px solid green;border-collapse: collapse;" >
					<tr>
						<td colspan='3' align='center' style='color:green;'><b>TRIP INCOME DETAILS</b></td>
					</tr>
					<?php 
						$parentId = -1;
						$tripparticulars_arr = array();
						$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
						if(count($parent)>0){
							$parent = $parent[0];
							$parentId = $parent->id;
						}
						$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
						foreach ($tripparticulars as $tripparticular){
							$tripparticulars_arr[] = $tripparticular->id;
						}
						$select_args = array();
						$select_args[] = "officebranch.name as branchId";
						$select_args[] = "tripparticulars.amount as amount";
						$select_args[] = "tripparticulars.date as date";
						$select_args[] = "tripparticulars.remarks as remarks";
						$tripadvances = \TripParticulars::where("tripId","=",$values["tripid"])->where("tripType","=","DAILY")->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->leftjoin("officebranch","officebranch.id","=","tripparticulars.branchId")->select($select_args)->get();
						foreach($tripadvances as $tripadvance){
							$total_advance = $total_advance+$tripadvance->amount;
					?>
				</tr>
					<tr>
						<td width="120px">Advance Amount</td>
						<td align="right" width="90px">{{$tripadvance->amount}}</td>
						<td>{{$tripadvance->branchId}}</td>
					</tr>
					<?php } ?>
					
					<tr>
					<?php 
						$vehicles = OfficeBranch::All();
						$parentId = -1;
						$parent = \LookupTypeValues::where("name","=","INCOME")->get();
						if(count($parent)>0){
							$parent = $parent[0];
							$parentId = $parent->id;
						}
						$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
						$tripparticulars_arr = array();
						$tripparticulars_name_arr = array();
						$income_fields = array();
						foreach ($tripparticulars as $tripparticular){
							$fields = array();
							$fields ['id'] = $tripparticular->id;
							$income_fields[] = $tripparticular->id;
							$fields ['branch'] = 0;
							$showfields = explode(",", $tripparticular->fields);
							if(in_array("BRANCH",$showfields)){
								$fields['branch'] = 1;												
							}
							$fields ['name'] = $tripparticular->name;
							$tripparticulars_name_arr[$tripparticular->id] = $tripparticular->name;
							$tripparticulars_arr[] = $fields;
						}
						$i = 0;
						$tripincomes =\TripParticulars::where("tripId","=",$values["tripid"])->where("tripType","=","DAILY")->get();
						foreach ($tripincomes as $tripincome){
							if(in_array($tripincome->lookupValueId, $income_fields)){
								$total_incomes = $total_incomes+$tripincome->amount;
								echo '<tr>';
								echo '<td width="120px">'.$tripparticulars_name_arr[$tripincome->lookupValueId].'</td>';
								echo '<td align="right" width="90px">'.$tripincome->amount.'</td>';
								echo '</tr>';
								
							}
						}
					?>
					<tr>
						<td><font color='green'><b>Total Income</b></font></td>
						<td align="right"><font color='green'><b>{{$total_advance+$total_incomes}}</b></font></td>
					</tr>
				</table>
				<br/>
				<table width="100%" >
					<tr>
						<td colspan="3">
							<table width="100%" border='1' cellpadding='2' cellspacing='0' style="border: 1px solid #2E64FE;border-collapse: collapse;">
								<tr>
									<td colspan='3' align='center' style='color:#2E64FE;'><b>TRIP FUEL DETAILS</b>(Closing Reading: 0)</td>
								</tr>
								<tr>
									<?php 
										$tripfuelexpenses =\FuelTransaction::where("tripId","=",$values["tripid"])->where("fueltransactions.paymentType","=","advance")->join("fuelstationdetails","fuelstationdetails.id","=","fueltransactions.fuelStationId")->get();
										foreach ($tripfuelexpenses as $tripfuelexpense){
											$total_fuel_amount = $total_fuel_amount+$tripfuelexpense->amount;
											echo "<td><font color='#2E64FE'><b>".$tripfuelexpense->name."</b></font></td>";
											echo "<td align='right'><font color='#2E64FE'><b>".$tripfuelexpense->amount."</b></font></td>";
									?>
									<?php }?>
								</tr>	
								<tr>
									<td><font color='#2E64FE'><b>Total Fuel</b></font></td>
									<td align="right"><font color='#2E64FE'><b>{{$total_fuel_amount}}</b></font></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td style="vertical-align: top;">
				<table width="100%" border='1' cellpadding='2' cellspacing='0' style="border: 1px solid red;border-collapse: collapse;">
					<tr>
						<td colspan='3' align='center' style='color:red;'><b>TRIP EXPENSE DETAILS</b></td>
					</tr>
					<?php 
						$vehicles = OfficeBranch::All();
						$parentId = -1;
						$parent = \LookupTypeValues::where("name","=","EXPENSE")->get();
						if(count($parent)>0){
							$parent = $parent[0];
							$parentId = $parent->id;
						}
						$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
						$tripparticulars_arr = array();
						$tripparticulars_name_arr = array();
						$expense_fields = array();
						foreach ($tripparticulars as $tripparticular){
							$fields = array();
							$fields ['id'] = $tripparticular->id;
							$expense_fields[] = $tripparticular->id;
							$fields ['branch'] = 0;
							$showfields = explode(",", $tripparticular->fields);
							if(in_array("BRANCH",$showfields)){
								$fields['branch'] = 1;												
							}
							$fields ['name'] = $tripparticular->name;
							$tripparticulars_name_arr[$tripparticular->id] = $tripparticular->name;
							$tripparticulars_arr[] = $fields;
						}
						$tripexpenses =\TripParticulars::where("tripId","=",$values["tripid"])->where("tripType","=","DAILY")->get();
						foreach ($tripexpenses as $tripexpense){
							if(in_array($tripexpense->lookupValueId, $expense_fields)){
								$total_expenses = $total_expenses+$tripexpense->amount;
								echo "<tr>";
									echo'<td width="120px">'.$tripparticulars_name_arr[$tripexpense->lookupValueId].'</td>';
									echo '<td align="right" width="90px">'.$tripexpense->amount.'</td>';
								echo '</tr>';
							}
						}
					?>
					
					<tr>
						<td><font color='red'><b>Total Expenses</b></font></td>
						<td align="right"><font color='red'><b>{{$total_expenses}}</b></font></td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<table width="100%" border='1' cellpadding='2' cellspacing='0' style="border: 1px solid #8A0808;border-collapse: collapse;">
					<tr>
						<td colspan='3' align='center' style='color:#8A0808;'><b>TOTAL TRIP SUMMARY</b></td>
					</tr>
					<tr>
						<td align='right'>Total Income</td>
						<td align="right" width="90px">{{$total_advance + $total_incomes}}</td>
					</tr>
					<tr>
						<td align='right'>Total Expenses</td>
						<td align="right" width="90px">{{$total_fuel_amount + $total_expenses}}</td>
					</tr>
					<tr>
						<td align='right'>Fuel From Trip Advance</td>
						<td align="right" width="90px">{{$total_fuel_amount}}</td>
					</tr>
					<tr>
						<td align='right'><font color='#8A0808'><b>Total Trip Balance</b></font></td>
						<td align="right"><font color='#8A0808'><b>{{($total_advance + $total_incomes)-($total_fuel_amount + $total_expenses)}}</b></font></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr >
			<td colspan="2">
				<table width="100%"style="margin-top:40px;">
					<tbody>
						<tr>
							<td class='name' width="50%" align="left"><br/>Date : {{date("d-m-Y")}}<br/>Place:</td>
							<td class='price' width="40%" align="right"><br/>Driver's Signature:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<br/><br/>Driver's Fullname:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td class='price' width="10%" align="right"><br/><br/></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
  	</table>
</body>
</html>
