<?php namespace trips;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class DataTableController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	private $jobs;
	public function getDataTableData()
	{
		$this->jobs = \Session::get("jobs");
		$values = Input::All();
		$start = $values['start'];
		$length = $values['length'];
		$total = 0;
		$data = array();
		if(isset($values["name"]) && isset($values["daterange"]) && $values["name"]=="dailytrips") {
			$ret_arr = $this->getDailyTrips($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && isset($values["daterange"]) && $values["name"]=="localtrips") {
			$ret_arr = $this->getLocalTrips($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="serviceroutes") {
			$ret_arr = $this->getServiceRoutes($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="tripparticulars") {
			$ret_arr = $this->getTripParticulars($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="localtripparticulars") {
			$ret_arr = $this->getLocalTripParticulars($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="assigndrivervehicle") {
			$ret_arr = $this->getAssignedDriverVehicle($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		$json_data = array(
				"draw"            => intval( $_REQUEST['draw'] ),
				"recordsTotal"    => intval( $total ),
				"recordsFiltered" => intval( $total ),
				"data"            => $data
			);
		echo json_encode($json_data);
	}
	
	private function getDailyTrips($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "tripdetails.tripStartDate as tripStartDate";
		$select_args[] = "tripdetails.id as routeInfo";
		$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
		$select_args[] = "tripdetails.routeCount as routes";
		$select_args[] = "tripdetails.id as totalAdvance";
		$select_args[] = "tripdetails.routeCount as fuelamount";
		$select_args[] = "tripdetails.id as expenses";
		$select_args[] = "tripdetails.id as incomes";
		$select_args[] = "tripdetails.id as id";
			
		$actions = array();
		if(in_array(310, $this->jobs)){
			$action = array("url"=>"editdailytrip?triptype=DAILY","css"=>"primary", "type"=>"", "text"=>"Edit");
			$actions[] = $action;
		}
		if(in_array(311, $this->jobs)){
			$action = array("url"=>"addtripparticular?type=expenses_and_incomes&","css"=>"inverse", "type"=>"", "text"=>"MANAGE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \IncomeTransaction::where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \IncomeTransaction::where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
			$entities = \TripDetails::where("tripdetails.status","=","Running")->whereBetween("tripStartDate",array($startdt,$enddt))->leftjoin("vehicle", "vehicle.id","=","tripdetails.vehicleId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \TripDetails::where("status","=","Running")->whereBetween("tripStartDate",array($startdt,$enddt))->count();
			foreach ($entities as $entity){
				$entity["tripStartDate"] = date("d-m-Y",strtotime($entity["tripStartDate"]));
				$entity["tripCloseDate"] = date("d-m-Y",strtotime($entity["tripCloseDate"]));
				if($entity["tripCloseDate"] == "01-01-1970"){
					$entity["tripCloseDate"] = "NOT CLOSED";
				}
				$entity["fuelamount"] = 0;
				$entity["routeInfo"] = "";
				$routeInfo = "";
				$tripservices = \TripServiceDetails::where("tripId","=",$entity->id)->get();
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
						$routeInfo = $routeInfo."<span style='font-size:13px; font-weight:bold; color:red;'>".$service->serviceNo."</span> - &nbsp;".$service->sourceCity." TO ".$service->destinationCity."<br/>";
					}
				}
				$entity["routeInfo"] = $routeInfo;
				
				$entity["expenses"] = 0;
				$parentId = -1;
				$tripparticulars_arr = array();
				$parent = \LookupTypeValues::where("name","=","TRIP INCOMES")->get();
				if(count($parent)>0){
					$parent = $parent[0];
					$parentId = $parent->id;
				}
				$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
				foreach ($tripparticulars as $tripparticular){
					$tripparticulars_arr[] = $tripparticular->id;
				}
				$entity["incomes"] = \TripParticulars::where("tripId","=",$entity->id)->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->sum('amount');
				
				$parentId = -1;
				$tripparticulars_arr = array();
				$parent = \LookupTypeValues::where("name","=","TRIP EXPENSES")->get();
				if(count($parent)>0){
					$parent = $parent[0];
					$parentId = $parent->id;
				}
				$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
				foreach ($tripparticulars as $tripparticular){
					$tripparticulars_arr[] = $tripparticular->id;
				}
				$entity["expenses"] = \TripParticulars::where("tripId","=",$entity->id)->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->sum('amount');
				
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
				$entity["totalAdvance"] = \TripParticulars::where("tripId","=",$entity->id)->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->sum('amount');
				$entity["fuelamount"] = \FuelTransaction::where("tripId","=",$entity->id)->where("paymentType","=","advance")->sum('amount');
			}
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[9] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getLocalTrips($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
	
		$select_args[] = "busbookings.booking_number as booking_number";
		$select_args[] = "busbookings.cust_name as sourcetrip";
		$select_args[] = "busbookings.cust_name as returntrip";
		$select_args[] = "busbookings.cust_name as custinfo";
		$select_args[] = "busbookings.booking_number as journeyinfo";
		$select_args[] = "busbookings.booking_number as amount";
		$select_args[] = "busbookings.source_start_place as source_start_place";
		$select_args[] = "busbookings.source_end_place as source_end_place";
		$select_args[] = "busbookings.dest_start_place as dest_start_place";
		$select_args[] = "busbookings.dest_end_place as dest_end_place";
		$select_args[] = "busbookings.cust_name as cust_name";
		$select_args[] = "busbookings.cust_phone as cust_phone";
		$select_args[] = "busbookings.source_date as source_date";
		$select_args[] = "busbookings.source_time as source_time";
		$select_args[] = "busbookings.dest_date as dest_date";
		$select_args[] = "busbookings.dest_time as dest_time";
		$select_args[] = "busbookings.total_cost as total_cost";
		$select_args[] = "busbookings.id as id";
		$select_args[] = "busbookings.status as status";
			
		$actions = array();
		if(in_array(313, $this->jobs)){
			$action = array("url"=>"editlocaltrip?","css"=>"primary","id"=>"editbooking", "type"=>"", "text"=>"Edit");
			$actions[] = $action;
		}
		if(in_array(314, $this->jobs)){
			$action = array("url"=>"#","css"=>"danger", "id"=>"deletebooking", "type"=>"", "text"=>"Delete");
			$actions[] = $action;
		}
		if(in_array(315, $this->jobs)){
			$action = array("url"=>"#","css"=>"purple", "id"=>"cancelbooking", "type"=>"", "text"=>"cancel");
			$actions[] = $action;
		}
		if(in_array(316, $this->jobs)){
			$action = array("url"=>"printlocaltrip?","css"=>"pink", "type"=>"", "id"=>"printbooking", "text"=>"print");
			$actions[] = $action;
		}
		if(in_array(317, $this->jobs)){
			$action = array("url"=>"addlocaltripparticular?type=expenses_and_incomes", "id"=>"addlocalbooking",  "css"=>"inverse", "type"=>"", "text"=>"MANAGE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \IncomeTransaction::where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \IncomeTransaction::where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
			if($values['bookingtype']=="activebookings"){
				$entities = \BusBookings::whereBetween("booking_date",array($startdt,$enddt))->whereNotIn('status', array("Cancelled","Deleted"))->select($select_args)->limit($length)->offset($start)->get();
				$total = \BusBookings::whereBetween("booking_date",array($startdt,$enddt))->whereNotIn('status', array("Cancelled","Deleted"))->count();
			}
			else{
				$entities = \BusBookings::whereBetween("booking_date",array($startdt,$enddt))->where('status',"=","Cancelled")->select($select_args)->limit($length)->offset($start)->get();
				$total = \BusBookings::whereBetween("booking_date",array($startdt,$enddt))->where('status',"=","Cancelled")->count();
			}
			foreach ($entities as $entity){
				$entity["source_date"] = date("d-m-Y",strtotime($entity["source_date"]));
				$entity["dest_date"] = date("d-m-Y",strtotime($entity["dest_date"]));
				if($entity["dest_date"] == "01-01-1970"){
					$entity["dest_date"] = "";
				}
				$entity["sourcetrip"] = $entity["source_start_place"]." <br/>TO<br/> ".$entity["source_end_place"];
				$entity["returntrip"] = $entity["dest_start_place"]." <br/>TO<br/> ".$entity["dest_end_place"];
				$entity["custinfo"] = $entity["cust_name"]."<br/>".$entity["cust_phone"];
				
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
				$tripadvances = \TripParticulars::where("tripId","=",$entity["id"])->where("tripType","=","LOCAL")->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->get();
				$total_adv = 0;
				foreach($tripadvances as $tripadvance){
					$total_adv = $total+$tripadvance->amount;
				}
				
				$entity["amount"] = "TOTAL &nbsp;&nbsp;&nbsp;   &nbsp;  : ".$entity["total_cost"]."<br/>ADVANCE : ".$total_adv."<br/>BALANCE : ".($entity["total_cost"]-$total_adv);
				$entity["journeyinfo"] = "START &nbsp;&nbsp;&nbsp;&nbsp;: ".$entity["source_date"]." ".$entity["source_time"]."<br/>RETURN : ".$entity["dest_date"]." ".$entity["dest_time"];
			}
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else if($action['url'] == "#"){
					if($action['id']=="cancelbooking" && $entity["status"]=="Cancelled"){
						$action["text"] = "un canel";
						$entity["id"] = "\"".$entity["id"]."&action=uncancel\"";
					}
					$action_data = $action_data."<button class='btn btn-minier btn-".$action["css"]."' onclick='".$action["id"]."(".$entity["id"].")' >".strtoupper($action["text"])."</button>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' id='".$action["id"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[6] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getAssignedDriverVehicle($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "bookingvehicles.booking_number as booking_number";
		$select_args[] = "bookingvehicles.vehicleId as vehicleId";
		$select_args[] = "bookingvehicles.driver1 as driver1";
		$select_args[] = "bookingvehicles.driver2 as driver2";
		$select_args[] = "bookingvehicles.helper as helper";
		$select_args[] = "bookingvehicles.id as id";
		$select_args[] = "bookingvehicles.driver1 as driver11";
		$select_args[] = "bookingvehicles.driver2 as driver21";
		$select_args[] = "bookingvehicles.helper as helper1";
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditAssignedValues(", "jsdata"=>array("id","driver11", "driver21", "helper1"), "text"=>"EDIT SERVICE");
		$actions[] = $action;
		$values["actions"] = $actions;
		$drivers = \Employee::where("roleId","=",19)->orWhere("roleId","=",20)->get();
		$drivers_arr = array();
		foreach($drivers as $driver){
			$drivers_arr[$driver->id] = $driver->fullName;
		}
		$vehicles = \Vehicle::All();
		$vehicles_arr = array();
		foreach($vehicles as $vehicle){
			$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
		}
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \IncomeTransaction::where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \IncomeTransaction::where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$bookingnumber = \BusBookings::where("id","=",$values["id"])->first();
			$bookingnumber = $bookingnumber->booking_number;
			$entities = \BookingVehicles::where("booking_number","=",$bookingnumber)->select($select_args)->limit($length)->offset($start)->get();
			$total = \BookingVehicles::where("booking_number","=",$bookingnumber)->count();
			$vehicle = \Vehicle::all();
			foreach ($entities as $entity){
				if(array_key_exists($entity->driver1,$drivers_arr)){
					$entity->driver1 = $drivers_arr[$entity->driver1];
				}
				else{
					$entity->driver1 ="";
				}
				if(array_key_exists($entity->driver2,$drivers_arr)){
						$entity->driver2 = $drivers_arr[$entity->driver2];
				}
				else{
					$entity->driver2 ="";
				}
				if(array_key_exists($entity->helper,$drivers_arr)){
						$entity->helper = $drivers_arr[$entity->helper];
				}
				else{
					$entity->helper  = "";
				}
				$entity->vehicleId = $vehicles_arr[$entity->vehicleId];
			}
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[5] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getServiceRoutes($values, $length, $start){
		$total = 0;
		$data = array();
			
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditDailyTrip(", "jsdata"=>array("id","service1id", "serviceDate", "driver1id", "driver2id", "helperid"), "text"=>"EDIT SERVICE");
		$actions[] = $action;
		//$action = array("url"=>"#cancel", "type"=>"modal", "css"=>"purple", "js"=>"modalEditState(", "jsdata"=>array("id","serviceId","serviceDate","driver1"), "text"=>"CANCEL SERVICE");
		//$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \IncomeTransaction::where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \IncomeTransaction::where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$select_args = array();
			$select_args[] = "tripservicedetails.tripRouteNo as routeno";
			$select_args[] = "tripservicedetails.serviceId as serviceId";
			$select_args[] = "tripservicedetails.serviceDate as serviceDate";
			$select_args[] = "tripservicedetails.driver1 as driver1";
			$select_args[] = "tripservicedetails.driver2 as driver2";
			$select_args[] = "tripservicedetails.helper as helper";
			$select_args[] = "tripservicedetails.id as id";
			$select_args[] = "tripservicedetails.serviceId as service1id"; 
			$select_args[] = "tripservicedetails.driver1 as driver1id";
			$select_args[] = "tripservicedetails.driver2 as driver2id";
			$select_args[] = "tripservicedetails.helper as helperid";
			$entities  = \TripServiceDetails::where("tripId","=",$values["tripid"])->where("status","=","Running")->select($select_args)->get();
			foreach($entities as $entity){
				$entity["serviceDate"] = date("d-m-Y",strtotime($entity["serviceDate"]));
				$select_args = array();
				$select_args[] = "cities.name as sourceCity";
				$select_args[] = "cities1.name as destinationCity";
				$select_args[] = "servicedetails.serviceNo as serviceNo";
				$select_args[] = "servicedetails.active as active";
				$select_args[] = "servicedetails.serviceStatus as serviceStatus";
				$select_args[] = "servicedetails.id as id";
				$service = \ServiceDetails::where("servicedetails.id","=",$entity->serviceId)->join("cities","cities.id","=","servicedetails.sourceCity")->join("cities as cities1","cities1.id","=","servicedetails.destinationCity")->select($select_args)->get();
				if(count($service)>0){
					$service = $service[0];
					$entity->serviceId = "<span style='font-size:13px; font-weight:bold; color:red;'>".$service->serviceNo."</span> (".$service->sourceCity." TO ".$service->destinationCity.")<br/>";
				}
				$driver = \Employee::where("id","=",$entity->driver1)->get();
				if(count($driver)>0){
					$driver = $driver[0];
					$entity->driver1 = $driver->fullName;
				}
				$driver = \Employee::where("id","=",$entity->driver2)->get();
				if(count($driver)>0){
					$driver = $driver[0];
					$entity->driver2 = $driver->fullName;
				}
				$driver = \Employee::where("id","=",$entity->helper)->get();
				if(count($driver)>0){
					$driver = $driver[0];
					$entity->helper = $driver->fullName;
				}
				
			}
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[6] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getLocalTripParticulars($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "tripparticulars.tripId as tripId";
		$select_args[] = "tripparticulars.lookupValueId as lookupValueId";
		$select_args[] = "tripparticulars.lookupValueId as type";
		$select_args[] = "tripparticulars.date as date";
		$select_args[] = "tripparticulars.amount as amount";
		$select_args[] = "tripparticulars.branchId as branchId";
		$select_args[] = "tripparticulars.bankId as bankId";
		$select_args[] = "tripparticulars.vehicleId as vehicleId";
		$select_args[] = "tripparticulars.remarks as remarks";
		$select_args[] = "tripparticulars.status as status";
		$select_args[] = "tripparticulars.id as id";
		$select_args[] = "tripparticulars.inchargeId as inchargeId";
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTripParticulars(", "jsdata"=>array("id","lookupValueId", "date", "amount", "branchId", "remarks", "status"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \TripParticulars::where("tripId","like","%$search%")->where("status","=","ACTIVE")->where("tripType","=","LOCAL")->select($select_args)->limit($length)->offset($start)->get();
			$total = \TripParticulars::where("tripId","like","%$search%")->where("status","=","ACTIVE")->where("tripType","=","LOCAL")->count();
		}
		else{
			$entities = \TripParticulars::where("tripId","=",$values["tripid"])->where("status","=","ACTIVE")->where("tripType","=","LOCAL")->select($select_args)->limit($length)->offset($start)->get();
			$total = \TripParticulars::where("tripId","=",$values["tripid"])->where("status","=","ACTIVE")->where("tripType","=","LOCAL")->count();
		}
		foreach ($entities as $entity){
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			$parentId = -1;
			$parent = \LookupTypeValues::where("id","=",$entity->lookupValueId)->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->parentId;
				$entity->lookupValueId = $parent->name;
			}
			$parent = \LookupTypeValues::where("id","=",$parentId)->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
				$entity->type = $parent->name;
			}
			if($entity->branchId>0){
				$branch = \OfficeBranch::where("id","=",$entity->branchId)->get();
				if(count($branch)>0){
					$branch = $branch[0];
					$entity->branchId = $branch->name;
				}
			}
			else{
				$entity->branchId = "";
			}
			if($entity->inchargeId>0){
				$select_args = array();
				$select_args[] = "employee.fullName as name";
				$select_args[] = "inchargeaccounts.id as id";
				$incharges = \InchargeAccounts::where("inchargeaccounts.id","=",$entity->inchargeId)->join("employee","employee.id","=","inchargeaccounts.empid")->select($select_args)->get();
				if(count($incharges)>0){
					$incharges = $incharges[0];
					$entity->branchId = $incharges->name;
				}
			}
			if($entity->bankId>0){
				$bank = \BankDetails::where("id","=",$entity->bankId)->get();
				if(count($bank)>0){
					$bank = $bank[0];
					$entity->bankId = $bank->bankName."-".$bank->accountNo;
				}
			}
			else{
				$entity->bankId = "";
			}
			if($entity->vehicleId>0){
				$bank = \Vehicle::where("id","=",$entity->vehicleId)->get();
				if(count($bank)>0){
					$bank = $bank[0];
					$entity->vehicleId = $bank->veh_reg;
				}
			}
			else{
				$entity->vehicleId = "";
			}
			$value_name_arr = array("9999"=>"DEBITED FROM BRANCH", "8888"=>"CREDITED TO BRANCH", "9001"=>"Last Closing Reading", "9002"=>"Initial Reading", "9003"=>"Closing Reading", "9004"=>"Wasted Meters", "9005"=>"Meter Reading Remarks");
			$name_arr = array("9999", "8888", "9001", "9002", "9003", "9004", "9005");
			if(in_array($entity->lookupValueId,$name_arr)){
				$entity->lookupValueId = $value_name_arr[$entity->lookupValueId];
			}
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[10] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getTripParticulars($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "tripparticulars.tripId as tripId";
		$select_args[] = "tripparticulars.lookupValueId as lookupValueId";
		$select_args[] = "tripparticulars.lookupValueId as type";
		$select_args[] = "tripparticulars.date as date";
		$select_args[] = "tripparticulars.amount as amount";
		$select_args[] = "tripparticulars.branchId as branchId";
		$select_args[] = "tripparticulars.remarks as remarks";
		$select_args[] = "tripparticulars.status as status";
		$select_args[] = "tripparticulars.id as id";
		$select_args[] = "tripparticulars.inchargeId as inchargeId";
		
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTripParticulars(", "jsdata"=>array("id","lookupValueId", "date", "amount", "branchId", "remarks", "status"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \TripParticulars::where("tripId","like","%$search%")->where("status","=","ACTIVE")->where("tripType","=","DAILY")->select($select_args)->limit($length)->offset($start)->get();
			$total = \TripParticulars::where("tripId","like","%$search%")->where("status","=","ACTIVE")->where("tripType","=","DAILY")->count();
		}
		else{
			$entities = \TripParticulars::where("tripId","=",$values["tripid"])->where("status","=","ACTIVE")->where("tripType","=","DAILY")->select($select_args)->limit($length)->offset($start)->get();
			$total = \TripParticulars::where("tripId","=",$values["tripid"])->where("status","=","ACTIVE")->where("tripType","=","DAILY")->count();
		}
		foreach ($entities as $entity){
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			$parentId = -1;
			$parent = \LookupTypeValues::where("id","=",$entity->lookupValueId)->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->parentId;
				$entity->lookupValueId = $parent->name;
			}
			$parent = \LookupTypeValues::where("id","=",$parentId)->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
				$entity->type = $parent->name;
			}
			if($entity->branchId>0){
				$branch = \OfficeBranch::where("id","=",$entity->branchId)->get();
				if(count($branch)>0){
					$branch = $branch[0];
					$entity->branchId = $branch->name;
				}
			}
			else{
				$entity->branchId = "";
			}
			if($entity->inchargeId>0){
				$select_args = array();
				$select_args[] = "employee.fullName as name";
				$select_args[] = "inchargeaccounts.id as id";
				$incharges = \InchargeAccounts::where("inchargeaccounts.id","=",$entity->inchargeId)->join("employee","employee.id","=","inchargeaccounts.empid")->select($select_args)->get();
				if(count($incharges)>0){
					$incharges = $incharges[0];
					$entity->branchId = $incharges->name;
				}
			}
			$value_name_arr = array("9999"=>"DEBITED FROM BRANCH", "8888"=>"CREDITED TO BRANCH", "9001"=>"Last Closing Reading", "9002"=>"Initial Reading", "9003"=>"Closing Reading", "9004"=>"Wasted Meters", "9005"=>"Meter Reading Remarks");
			$name_arr = array("9999", "8888", "9001", "9002", "9003", "9004", "9005");
			if(in_array($entity->lookupValueId,$name_arr)){
				$entity->lookupValueId = $value_name_arr[$entity->lookupValueId];
			}
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[8] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
}


