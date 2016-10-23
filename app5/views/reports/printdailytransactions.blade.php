<?php
use Illuminate\Support\Facades\Input;
use Monolog\Handler\IFTTTHandler;
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

	<title>EASY TRAVEL MANAGER</title>
	
	<style>
		table.dataTable,
		table.dataTable th,
		table.dataTable td {
			  -webkit-box-sizing: content-box;
			  -moz-box-sizing: content-box;
			  box-sizing: content-box;
		}
	</style>
</head>

<body>
	<?php 
		$values = Input::All();
		$brachname="";
		$branch = OfficeBranch::where("id","=",$values["branchId"])->get();
		
		if(count($branch) >0){
			$branch =$branch[0];
			$brachname =$branch->name;
		}
		
	?>
	<div style="width:210mm;  margin: auto;">
	<table width="100%" style='font-size: 75%;'>
	<tr>
		<td align='center' style='font-size: 120%;'><b>Morning Star Travels - BranchWise Account Transactions Details</b></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td align='center'><b>Completed Account Transactions Details for {{$brachname}} Branch for dates in between {{$values["fromDt"]}} and {{$values["toDt"]}}</b></td>
	</tr>
	<tr>
		<td align='center'>
		<table width='100%' border='1' cellpadding='2' cellspacing='0' >
			<tr id='row' bgcolor='#F5BCA9'>
            	<td colspan='8' align='center'> <b>Expenses</b></td>					
			</tr>
            <tr>									
				<th>Trans Date</th>
				<th>User</th>
				<th>Spent For</th>														
				<th>Pmt Type</th>														
				<th>Cheq/DD</th>														
				<th>Paid To</th>
				<th>Remarks</th>
				<th>Amount</th>
            </tr>
            <?php 
            	$fromdt = date("Y-m-d",strtotime($values["fromDt"]));
            	$todt = date("Y-m-d",strtotime($values["toDt"]));
            	$examount = 0.0;
            	$sql = 'SELECT expensetransactions.*, employee.fullName, lookuptypevalues.name from 
						expensetransactions JOIN employee on employee.id = expensetransactions.createdBy 
						LEFT JOIN lookuptypevalues on lookuptypevalues.id=expensetransactions.lookupValueId WHERE 
						expensetransactions.branchId='.$values["branchId"].' and expensetransactions.date BETWEEN "'.$fromdt.'" AND "'.$todt.'";';
            	
            	$recs = DB::select(DB::raw($sql));
            	foreach ($recs as $rec){
            ?>
            <tr id='row'>
				<td width='8%'>{{date("d-m-Y",strtotime($rec->date))}}</td>
				<td width='10%'>{{$rec->fullName}}</td>
				<?php 
            	if($rec->lookupValueId==8888){
						$rec->name = "CREDITED TO BRANCH - TRIP BALANCE";
					}
					if($rec->lookupValueId==9999){
						$rec->name = "DEBITED FROM BRANCH - TRIP BALANCE";
					}
					else if($rec->lookupValueId==999){
						if($rec->entityValue>0){
							$prepaidName = \LookupTypeValues::where("id","=",$rec->entityValue)->first();
							$prepaidName = $prepaidName->name;
							$rec->name = strtoupper($rec->entity);
							$rec->entityValue = $prepaidName;
						}
						else{
							$rec->name = strtoupper($rec->entity);
						}
					}
					else if($rec->lookupValueId==998){
						if($rec->entityValue>0){
							$creditsupplier = \CreditSupplier::where("id","=",$rec->entityValue)->first();
							$creditsupplier = $creditsupplier->supplierName;
							$rec->name = strtoupper($rec->entity);
							$rec->entityValue = $creditsupplier;
						}
						else{
							$rec->name = strtoupper($rec->entity);
						}
					}
					else if($rec->lookupValueId==997){
						if($rec->entityValue>0){
							$fuelstation = \FuelStation::where("id","=",$rec->entityValue)->first();
							$fuelstation = $fuelstation->name;
							$rec->name = strtoupper($rec->entity);
							$rec->entityValue = $fuelstation;
						}
						else{
							$rec->name = strtoupper($rec->entity);
						}
					}
					else if($rec->lookupValueId==991){
						if($rec->entityValue>0){
							$dfid = \DailyFinance::where("id","=",$rec->entityValue)->first();
							$dfid = $dfid->financeCompanyId;
							$finanacecompany = \FinanceCompany::where("id","=",$dfid)->first();
							$finanacecompany = $finanacecompany->name;
							$rec->name = strtoupper($rec->entity);
							$rec->entityValue = $finanacecompany;
						}
						else{
							$rec->name = strtoupper($rec->entity);
						}
					}
					else if($rec->lookupValueId==125){
						$bankdetails = \ExpenseTransaction::where("transactionId","=",$rec->transactionId)->leftjoin("bankdetails","bankdetails.id","=","expensetransactions.bankId")->first();
						$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
						$rec->entityValue = $bankdetails;
					}
					else if($rec->lookupValueId==123){
						$branch = OfficeBranch::where("id","=",$rec->branchId1)->get();
						if (count($branch)>0){
							$branch = $branch[0];
							$rec->entityValue = $branch->name;
						}
					}
				?>
				<td width='10%'>{{$rec->lookupValueId}},{{$rec->name}}</td>
				<td width='10%'>{{$rec->paymentType}}</td>
				<td width='10%'>{{"info"}}</td>
				<td width='5%'>{{$rec->entityValue}}</td>
				<td width='20%'>{{$rec->remarks}}</td>
				<td width='5%'>{{$rec->amount}}</td>
		     </tr>	
		     <?php 
		     		$examount = $examount+$rec->amount;
            	}
		     ?>		
		     <?php 
            	$fromdt = date("Y-m-d",strtotime($values["fromDt"]));
            	$todt = date("Y-m-d",strtotime($values["toDt"]));
            	$sql = "SELECT tripparticulars.*, employee.fullName, lookuptypevalues.name FROM `tripparticulars` 
						JOIN employee on employee.id = tripparticulars.createdBy LEFT JOIN lookuptypevalues ON lookuptypevalues.id = tripparticulars.lookupValueId 
						WHERE tripparticulars.branchId=".$values["branchId"]." and tripparticulars.lookupValueId IN(9999,63) AND tripparticulars.date BETWEEN \"$fromdt\" AND \"$todt\"";
            	
            	$recs = DB::select(DB::raw($sql));
            	foreach ($recs as $rec){
            ?>
            <tr id='row'>
				<td width='8%'>{{date("d-m-Y",strtotime($rec->date))}}</td>
				<td width='10%'>{{$rec->fullName}}</td>
				<?php 
				$tripinfo="";
				if($rec->lookupValueId==9999){
					$rec->name="TRIP BALANCE";
					if ($rec->tripType == "LOCAL"){
						$entities = \BusBookings::where("id","=",$rec->tripId)->get();
						foreach ($entities as $entity){
							$tripinfo = $entity->booking_number;
						}
					}
					else{
						$select_args = array();
						$select_args[] = "vehicle.veh_reg as vehicleId";
						$select_args[] = "tripdetails.tripStartDate as tripStartDate";
						$select_args[] = "tripdetails.id as routeInfo";
						$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
						$select_args[] = "tripdetails.routeCount as routes";
						$select_args[] = "tripdetails.id as id";
						$entities = \TripDetails::where("tripdetails.id","=",$rec->tripId)->leftjoin("vehicle", "vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
						foreach ($entities as $entity){
							$entity["tripStartDate"] = date("d-m-Y",strtotime($entity["tripStartDate"]));
							$entity["tripCloseDate"] = date("d-m-Y",strtotime($entity["tripCloseDate"]));
							if($entity["tripCloseDate"] == "01-01-1970"){
								$entity["tripCloseDate"] = "NOT CLOSED";
							}
							$routeInfo = "";
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
									$routeInfo = $routeInfo."<span style='font-size:13px; font-weight:bold; color:red;'>".$service->serviceNo."</span> - &nbsp; ".$service->sourceCity." TO ".$service->destinationCity."<br/>";
								}
							}
							$tripinfo = $routeInfo;
							$tripinfo = $tripinfo.$entity->vehicleId.", ";
							$tripinfo = $tripinfo.$entity->tripStartDate.", ";
						}
						
					}
				}
				if ($rec->tripType == "LOCAL"){
					$entities = \BusBookings::where("id","=",$rec->tripId)->get();
					foreach ($entities as $entity){
						$tripinfo = $entity->booking_number;
					}
				}
				?>
				<td width='10%'>{{$rec->name}}</td>
				<td width='10%'>{{"CASH"}}</td>
				<td width='10%'>{{"info"}}</td>
				<td width='5%'>{{$tripinfo}}</td>
				<td width='20%'>{{$rec->remarks}}</td>
				<td width='5%'>{{$rec->amount}}</td>
		     </tr>	
		     <?php 
		     		$examount = $examount+$rec->amount;
            	}
		     ?>			
			 <tr id='row'>
				<td colspan='8'> <b>Total Amount: {{$examount}}</b></td>
		     </tr>	
		</table>	
		</td>
	</tr>					
	<tr>
		<td align='center'>		
		<table width='100%' border='1' cellpadding='2' cellspacing='0'>
		<tr id='row' bgcolor='#9FF781'>
        	<td colspan='8' align='center'> <b>Incomes</b></td>					
		</tr>
        <tr>									
			<th>Trans Date</th>
			<th>User</th>
			<th>Income From</th>														
			<th>Pmt Type</th>														
			<th>Cheq/DD</th>														
			<th>Received From</th>
			<th>Remarks</th>
			<th>Amount</th>
         </tr>
         <?php 
            	$fromdt = date("Y-m-d",strtotime($values["fromDt"]));
            	$todt = date("Y-m-d",strtotime($values["toDt"]));
            	$inamount = 0.0;
            	$sql = 'SELECT incometransactions.*, employee.fullName, lookuptypevalues.name from 
						incometransactions JOIN employee on employee.id = incometransactions.createdBy 
						LEFT JOIN lookuptypevalues on lookuptypevalues.id=incometransactions.lookupValueId WHERE 
						incometransactions.branchId='.$values["branchId"].' and incometransactions.date BETWEEN "'.$fromdt.'" AND "'.$todt.'";';
            	
            	$recs = DB::select(DB::raw($sql));
            	foreach ($recs as $rec){
            ?>
            <tr id='row'>
				<td width='8%'>{{date("d-m-Y",strtotime($rec->date))}}</td>
				<td width='10%'>{{$rec->fullName}}</td>
				<?php 
					if($rec->lookupValueId==999){
						if($rec->entityValue>0){
							$prepaidName = \LookupTypeValues::where("id","=",$rec->entityValue)->first();
							$prepaidName = $prepaidName->name;
							$rec->name = strtoupper($rec->entity);
							$rec->entityValue = $prepaidName;
						}
						else{
							$rec->name = strtoupper($rec->entity);
						}
					}
					else if($rec->lookupValueId==73){
						$bankdetails = \IncomeTransaction::where("transactionId","=",$rec->transactionId)->leftjoin("bankdetails","bankdetails.id","=","incometransactions.bankId")->first();
						$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
						$rec->name = $bankdetails;
					}
					else if($rec->lookupValueId==84){
						$bankdetails = \ExpenseTransaction::where("transactionId","=",$rec->transactionId)->leftjoin("bankdetails","bankdetails.id","=","expensetransactions.bankId")->first();
						$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
						$rec->name = $bankdetails;
					}
					else if($rec->lookupValueId==243){
						$branch = OfficeBranch::where("id","=",$rec->branchId1)->get();
						if (count($branch)>0){
							$branch = $branch[0];
							$rec->entityValue = $branch->name;
						}
					}
				?>
				<td width='10%'>{{$rec->name}}</td>
				<td width='10%'>{{$rec->paymentType}}</td>
				<td width='10%'>{{"info"}}</td>
				<td width='5%'>{{$rec->entityValue}}</td>
				<td width='20%'>{{$rec->remarks}}</td>
				<td width='5%'>{{$rec->amount}}</td>
		     </tr>	
		     <?php 
		    		 $inamount = $inamount+$rec->amount;
            	}
		     ?>		
		     <?php 
            	$fromdt = date("Y-m-d",strtotime($values["fromDt"]));
            	$todt = date("Y-m-d",strtotime($values["toDt"]));
            	$sql = "SELECT tripparticulars.*, employee.fullName, lookuptypevalues.name FROM `tripparticulars` 
						JOIN employee on employee.id = tripparticulars.createdBy LEFT JOIN lookuptypevalues ON lookuptypevalues.id = tripparticulars.lookupValueId 
						WHERE tripparticulars.branchId=".$values["branchId"]." and tripparticulars.lookupValueId IN(8888,58,64) AND tripparticulars.date BETWEEN \"$fromdt\" AND \"$todt\"";
            	
            	$recs = DB::select(DB::raw($sql));
            	foreach ($recs as $rec){
            ?>
            <tr id='row'>
				<td width='8%'>{{date("d-m-Y",strtotime($rec->date))}}</td>
				<td width='10%'>{{$rec->fullName}}</td>
				<?php 
				$tripinfo="";
				if($rec->lookupValueId==8888){
					$rec->name="TRIP BALANCE";
					if ($rec->tripType == "LOCAL"){
						$entities = \BusBookings::where("id","=",$rec->tripId)->get();
						foreach ($entities as $entity){
							$tripinfo = $entity->booking_number;
						}
					}
					else{
						$select_args = array();
						$select_args[] = "vehicle.veh_reg as vehicleId";
						$select_args[] = "tripdetails.tripStartDate as tripStartDate";
						$select_args[] = "tripdetails.id as routeInfo";
						$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
						$select_args[] = "tripdetails.routeCount as routes";
						$select_args[] = "tripdetails.id as id";
						$entities = \TripDetails::where("tripdetails.id","=",$rec->tripId)->leftjoin("vehicle", "vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
						foreach ($entities as $entity){
							$entity["tripStartDate"] = date("d-m-Y",strtotime($entity["tripStartDate"]));
							$entity["tripCloseDate"] = date("d-m-Y",strtotime($entity["tripCloseDate"]));
							if($entity["tripCloseDate"] == "01-01-1970"){
								$entity["tripCloseDate"] = "NOT CLOSED";
							}
							$routeInfo = "";
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
									$routeInfo = $routeInfo."<span style='font-size:13px; font-weight:bold; color:red;'>".$service->serviceNo."</span> - &nbsp; ".$service->sourceCity." TO ".$service->destinationCity."<br/>";
								}
							}
							$tripinfo = $routeInfo;
							$tripinfo = $tripinfo.$entity->vehicleId.", ";
							$tripinfo = $tripinfo.$entity->tripStartDate.", ";
						}
						
					}
				}
				if ($rec->tripType == "LOCAL"){
					$entities = \BusBookings::where("id","=",$rec->tripId)->get();
					foreach ($entities as $entity){
						$tripinfo = $entity->booking_number;
					}
				}
				?>
				<td width='10%'>{{$rec->name}}</td>
				<td width='10%'>{{"CASH"}}</td>
				<td width='10%'>{{"info"}}</td>
				<td width='5%'>{{$tripinfo}}</td>
				<td width='20%'>{{$rec->remarks}}</td>
				<td width='5%'>{{$rec->amount}}</td>
		     </tr>	
		     <?php 
		     		$inamount = $inamount+$rec->amount;
            	}
		     ?>				
		<tr id='row'>
			<td colspan='8'> <b>Total Amount: {{$inamount}}</b></td>
		</tr>	
		</table>	
		 </td>
	</tr>
	<tr>
		<td align='center'><b>Completed Account Transactions Details for {{$brachname}} Branch for dates in between {{$values["fromDt"]}} and {{$values["toDt"]}}</b></td>
	</tr>
	<tr>
		<td align='center'>
				Printed on <b>{{date("d/m/Y h:i:sa")}}</b> By <b>{{Auth::user()->fullName;}}</b>
		</td>
	</tr>
	<tr>
	<td align='center'>
		<table width='30%' border='1' cellpadding='2' cellspacing='0' style='font-weight: bold;'>
			<tr id='row'>
                 <td colspan='2' align='center'> <b>Summary</b></td>					
			</tr>
	        <tr id='row'> <td width='50%'> Bookings : </td> <td width='50%' >0</td> </tr>
			<tr id='row'> <td> Cancellations : </td> <td >0</td> </tr>
			<tr id='row'> <td> Cargo Bookings: </td> <td>0</td> </tr>
			<tr id='row'> <td> Cargo Cancellations:  </td><td>0 </td> </tr>
			<tr id='row'> <td> Expenses:  </td><td>{{$examount}}</td> </tr>
			<tr id='row'> <td> Incomes:  </td><td>{{$inamount}} </td> </tr>
	     </table>
	</td>
	</tr>
	<tr>
		<td align='center'>
			<b> Total Net Amount : {{$examount+$inamount}}</b>
		</td>
	</tr>
</table> 
</body>

</html>
