<?php namespace servicelogs;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use settings\AppSettingsController;
class ServiceLogController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addServiceLog()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["test"];
			$success = false;
			$contract = \Contract::where("clientId","=",$values["clientname"])->where("depotId","=",$values["depot"])->get();
			if(count($contract)>0){
				$contract = $contract[0];
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ServiceLog";
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["contractId"] = $contract->id;
					$fields["contractVehicleId"] = $jsonitem->vehicle;
					$fields["serviceDate"] = $jsonitem->servicedate;
					if($jsonitem->starttime != ""){
						$fields["startTime"] = $jsonitem->starttime;
					}
					if($jsonitem->substitutevehicle != ""){
						$fields["substituteVehicleId"] = $jsonitem->substitutevehicle;
					}
					if($jsonitem->driver1 != ""){
						$fields["driver1Id"] = $jsonitem->driver1;
					}
					if($jsonitem->driver2 != ""){
						$fields["driver2Id"] = $jsonitem->driver2;
					}
					if($jsonitem->helper != ""){
						$fields["helperId"] = $jsonitem->helper;
					}
					if($jsonitem->penalitiestype != ""){
						$fields["penalityTypeId"] = $jsonitem->penalitiestype;
					}
					if(isset($jsonitem->penalityamount) &&  $jsonitem->penalityamount != ""){
						$fields["penalityAmount"] = $jsonitem->penalityamount;
					}
					if(isset($jsonitem->distance) &&  $jsonitem->distance != ""){
						$fields["distance"] = $jsonitem->distance;
					}
					if(isset($jsonitem->repairkms) &&  $jsonitem->repairkms != ""){
						$fields["repairkms"] = $jsonitem->repairkms;
					}
					$fields["startReading"] = $jsonitem->startreading;
					$fields["endReading"] = $jsonitem->endreading;
					if(isset($jsonitem->remarks) &&  $jsonitem->remarks != ""){
						$fields["remarks"] = $jsonitem->remarks;
					}
					$db_functions_ctrl->insert($table, $fields);
					
					$veh_meeter = \VehicleMeeter::where("status","=","ACTIVE")
										->where("vehicleId","=",$jsonitem->vehicle)
										->update(array("endReading"=>$jsonitem->endreading,"endDate"=>date("Y-m-d")));
					$success = true;
				}
			}
			if($success){
				return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
			}
			else{
				return json_encode(['status' => 'fail', 'message' => 'Operation Could not be completed, Try Again!']);
			}
		}
		
		$form_info = array();
		$form_info["name"] = "addcity";
		$form_info["action"] = "addcity";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cities";
		$form_info["bredcum"] = "add city";
		
		$form_fields = array();
		
		$states =  \State::all();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name; 	
		}
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"citycode", "content"=>"city code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editServiceLog()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$field_names = array(
							"starttime1"=>"startTime","startreading1"=>"startReading","endreading1"=>"endReading",
							"distance1"=>"distance","repairkms1"=>"repairkms","status1"=>"status",
							"remarks1"=>"remarks"
						   );
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="fromdate" || $key=="todate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ServiceLog";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("servicelogs");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("servicelogs");
			}
		}
	}
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getVehicleContractInfo()
	{
		$values = Input::all();
		$response = "<option value=''> --select vehicle-- </option>";
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			$entities = \ContractVehicle::join("vehicle","vehicle.id","=","contract_vehicles.vehicleId")->
					where("contractId","=",$contractId)->where("contract_vehicles.status","=","ACTIVE")
					->select(array("vehicle.id as id", "vehicle.veh_reg as name"))->get();
			foreach ($entities as $entity){
				$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";
			}
		}
		echo $response;
	}
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getDriverHelper()
	{
		$values = Input::all();
		$response = array();
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			
			$drivers =  \Employee::All();
			$drivers_arr = array();
			foreach ($drivers as $driver){
				$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
			}
			
			$entities = \ContractVehicle::where("contract_vehicles.status","=","ACTIVE")->
						where("contractId","=",$contractId)->
						where("vehicleId","=",$values["vehicleid"])->get();
			foreach ($entities as $entity){
				$response[] = array("<option value=''> --select driver1-- </option>"."<option  selected value='".$entity->driver1Id."'>".$drivers_arr[$entity->driver1Id]."</option>");
				if($entity->driver2Id != 0){
					$response[] = array("<option value=''> --select driver2-- </option>"."<option selected  value='".$entity->driver2Id."'>".$drivers_arr[$entity->driver2Id]."</option>");
				}
				else{
					$response[] = array("<option value=''> --select driver2-- </option>");
				}
				if($entity->helperId != 0){
					$response[] = array("<option value=''> --select helper-- </option>"."<option selected  value='".$entity->helperId."'>".$drivers_arr[$entity->helperId]."</option>");
				}
				else{
					$response[] = array("<option value=''> --select helper-- </option>");
				}
				break;
			}
			$today = date("Y-m-d");
			$prevdays = date('Y-m-d',strtotime("-5 days"));
			$dates_arr = array();
			$i = 0;
			while($i<=5){
				$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` WHERE fromDate<='".date('Y-m-d',strtotime("-".$i." days"))."' and toDate>='".date('Y-m-d',strtotime("-".$i." days"))."'"));
				if(count($holidays)>0) {
					$holidays = $holidays[0];
					if($holidays->count==0)
						$dates_arr[date('Y-m-d',strtotime("-".$i." days"))] = date('Y-m-d',strtotime("-".$i." days"));
				}
				$i++;
			}
			$dates = "<option value=''> --select service date-- </option>";
			foreach ($dates_arr as $dt=>$val){
				$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
			}
			//$dates_arr = array_reverse($dates_arr);
			$opendts = \ServiceLogRequest::where("contractId","=",$contractId)
			->where("vehicleId","=",$values["vehicleid"])
			->where("status","=","Open")->get();
			foreach ($opendts as $opendt){
				$opendt_arr = explode(",", $opendt->pendingDates);
				foreach ($opendt_arr as $opendt_arr_item){
					if($opendt_arr_item != " "){
						$dates = $dates."<option value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
					}
				}
			}
			$response[] = array($dates);
		}
		echo json_encode($response);
	}
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getStartReading()
	{
		$values = Input::all();
		$startreading = "";
		$response = array();
		$response[0] = array(0);
		$servlogs = array();
		$contractId = 0;
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			$servlogs = \ServiceLog::where("status","=","ACTIVE")->
						where("contractId","=",$contractId)->
						where("contractVehicleId","=",$values["vehicleid"])->orderBy("serviceDate",'desc')->limit(5)->offset(0)->get();
			if(count($servlogs)>0){
				$servlog = $servlogs[0];
				$response[0] = array($servlog->endReading);
			}
			else{
				$veh_meeter = \VehicleMeeter::where("status","=","ACTIVE")->
						where("vehicleId","=",$values["vehicleid"])->get();
				if(count($veh_meeter)>0){
					$veh_meeter = $veh_meeter[0];
					$response[0] = array($veh_meeter->startReading);
				}
			}
		}
		$today = new \DateTime(date("Y-m-d"));
		$dates_arr = array();
		$i = 0;
		$cmp_date = $today->modify("0 day");
		while($i<5){
			$cmp_date = $cmp_date->format('Y-m-d');
			$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` WHERE fromDate<='".$cmp_date."' and toDate>='".$cmp_date."'"));
			if(count($holidays)>0) {
				$holidays = $holidays[0];
				if($holidays->count==0)
					$dates_arr[$cmp_date] = $cmp_date;
			}
			$i++;
			$cmp_date = $today->modify("-1 day");
			$today = $cmp_date;
		}
		
		$dates = "";
		foreach ($dates_arr as $dt=>$val){
			if($values["servicedate"]==$dt){
				$dates = $dates."<option selected value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
			}
			else{
				$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
			}
		}
		
		//$dates_arr = array_reverse($dates_arr);
		$opendts = \ServiceLogRequest::where("contractId","=",$contractId)
				->where("vehicleId","=",$values["vehicleid"])
				->where("status","=","Open")->get();
		foreach ($opendts as $opendt){
			$opendt_arr = explode(",", $opendt->pendingDates);
			foreach ($opendt_arr as $opendt_arr_item){
				if($opendt_arr_item != " "){
					$dates = $dates."<option value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
				}
			}
		}
		
		$response[] = array($dates);
		
		$vehicles =  \Vehicle::all();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
		}
		$drivers =  \Employee::where("roleId","=",19)->get();
		$drivers_arr = array();
		foreach ($drivers as $driver){
			$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
		}
		$helpers =  \Employee::where("roleId","=",20)->get();
		$helpers_arr = array();
		foreach ($helpers as $helper){
			$helpers_arr[$helper['id']] = $helper['fullName']." (".$helper->empCode.")";
		}
		
		$con_vehs_text_arr = array();
		foreach ($servlogs as $servlog){
			$con_vehs_text = array();
			$con_vehs_text['vehicle'] = $vehicles_arr[$servlog->contractVehicleId];
			$con_vehs_text['servicedate'] = date("d-m-Y",strtotime($servlog->serviceDate));
			$con_vehs_text['reading'] = $servlog->startReading." - ".$servlog->endReading." = ".($servlog->endReading-$servlog->startReading);
			$drivers = "";
			if($servlog->driver1Id != 0){
				$drivers = $drivers.$drivers_arr[$servlog->driver1Id].", ";
			}
			if($servlog->driver2Id != 0){
				$drivers = $drivers.$drivers_arr[$servlog->driver2Id].", ";
			}
			$con_vehs_text['drivers'] = $drivers;
			if($servlog->helperId != 0){
				$con_vehs_text['helper'] = $helpers_arr[$servlog->helperId];
			}
			$con_vehs_text['remarks'] = $servlog->remarks;
			$con_vehs_text['status'] = $servlog->status;
			$con_vehs_text_arr[] = $con_vehs_text;
		}
		$response[] = $con_vehs_text_arr;
		echo json_encode($response);;
	}
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getPendingServiceLogs()
	{
		$values = Input::all();
		$startreading = "";
		$response = array();
		$today = new \DateTime(date("Y-m-d"));
		$dates_arr = array();
		$i = 0;
		$cmp_date = $today->modify("0 day");
		$contractId = 0;
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
		}
		
		while($i<10){
			$cmp_date = $cmp_date->format('Y-m-d');
			$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` WHERE fromDate<='".$cmp_date."' and toDate>='".$cmp_date."'"));
			if(count($holidays)>0) {
				$holidays = $holidays[0];
				if($holidays->count==0)
					$dates_arr[$cmp_date] = $cmp_date;
			}
			$i++;
			$cmp_date = $today->modify("-1 day");
			$today = $cmp_date;
		}
		$today = date("Y-m-d");
		$prevdays = date('Y-m-d',strtotime("-10 days"));
		$dates = "<option value=''> --select service date-- </option>";
		$servicelogs = \ServiceLog::where("contractId","=",$contractId)->
						where("contractVehicleId","=",$values["vehicleid"])->
						whereBetween("serviceDate",array($prevdays, $today))->select("serviceDate")->get();
			
		foreach ($servicelogs as $servicelog){
			if (($key = array_search($servicelog->serviceDate, $dates_arr)) !== false) {
				unset($dates_arr[$key]);
			}
		}
		$dates_arr = array_reverse($dates_arr);
		foreach ($dates_arr as $dt=>$val){
			$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
		}
		$response[] = array($dates);
	
		echo json_encode($response);;
	}
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getBranchbyCityId()
	{
		$values = Input::all();
		$entities = \OfficeBranch::where("Id","=",$values['id'])->get();
		$response = "";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";
		}
		echo $response;
	}

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageServiceLogs()
	{
		$values = Input::all();
		$values['bredcum'] = "SERVICE LOGS";
		$values['home_url'] = 'contractsmenu';
		$values['add_url'] = '';
		$values['form_action'] = 'servicelogs';
		$values['action_val'] = '';
		$theads = array('vehicle no', "service date", "start time", "start reading", "end reading", "distance", "driver",  "helper", "status","Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
	
		
		$form_info = array();
		$form_info["name"] = "addservicelog";
		$form_info["action"] = "addservicelog";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicelogs";
		$form_info["bredcum"] = "add servicelog";
		
		$form_fields = array();		
		$states =  \State::all();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state['name'];
		}
		
		$cities =  \City::all();
		$citie_arr = array();
		foreach ($cities as $city){
			//$citie_arr[$city['id']] = $city['name'];
		}
		
		$districts =  \District::all();
		$districts_arr = array();
		foreach ($districts as $district){
			$districts_arr[$district['id']] = $district['name'];
		}
		
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		
		$services =  \DB::select(\DB::raw("select servicedetails.id as id, city1.name as name1, city2.name as name2, servicedetails.description from servicedetails join cities as city1 on city1.id=servicedetails.sourceCity join cities as city2 on servicedetails.destinationCity=city2.id"));
		$services_arr = array();
		foreach ($services as $service){
			$desc = "";
			if($service->description != ""){
				$desc = " ".$service->description;
			}
			$services_arr[$service->id] = $service->name1."-".$service->name2.$desc;
		}
		
		$parentId = \LookupTypeValues::where("name", "=", "VEHICLE TYPE")->get();
		$vehtypes = array();
		if(count($parentId)>0){
			$parentId = $parentId[0];
			$parentId = $parentId->id;
			$vehtypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		
		}
		$vehtypes_arr = array();
		foreach ($vehtypes as $vehtype){
			$vehtypes_arr[$vehtype->id] = $vehtype->name;
		}
		
		$parentId = \LookupTypeValues::where("name", "=", "PENALITY TYPES")->get();
		$pentypes = array();
		if(count($parentId)>0){
			$parentId = $parentId[0];
			$parentId = $parentId->id;
			$pentypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		
		}
		$pentypes_arr = array();
		foreach ($pentypes as $pentype){
			$pentypes_arr[$pentype->id] = $pentype->name;
		}
		
		$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		
		$ex_Vehicles = \ContractVehicle::where("status","=","ACTIVE")->get();
		$ex_Vehicles_arr = array();
		foreach ($ex_Vehicles as $ex_Vehicle){
			$ex_Vehicles_arr[] = $ex_Vehicle['vehicleId'];
		}
		$vehicles =  \Vehicle::all();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			if(!in_array($vehicle['id'],$ex_Vehicles_arr)){
				$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
			}
		}
		
		//$drivers =  \Employee::where("roleId","=",19)->get();
		$drivers_arr = array();
		/*foreach ($drivers as $driver){
			$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
		}
		*/
		
		//$helpers =  \Employee::where("roleId","=",20)->get();
		$helpers_arr = array();
		/*foreach ($helpers as $helper){
			$helpers_arr[$helper['id']] = $helper['fullName']." (".$helper->empCode.")";
		}
		*/
		
		$times_arr = array();
		$hr = 0; $min = 0; $min_val= "";$hr_val = "";
		while($hr<12){
			$min_val = $min;
			$hr_val = $hr;
			if($hr<10){ $hr_val = "0".$hr; }
			if($min<10){ $min_val = "0".$min; }
			$times_arr[$hr_val.":".$min_val." AM"] = "".$hr_val.":".$min_val." AM";
			$min = $min+5;
			if($min>=56){ $hr++; $min=0;}
		}
		$hr = 0; $min = 0; $min_val= "";$hr_val = "";
		while($hr<12){
			$min_val = $min;
			$hr_val = $hr;
			if($hr<10){ $hr_val = "0".$hr; }
			if($min<10){ $min_val = "0".$min; }
			$times_arr[$hr_val.":".$min_val." PM"] = "".$hr_val.":".$min_val." PM";
			$min = $min+5;
			if($min>=56){ $hr++; $min=0;}
		}
		
		
		$form_fields =  array();
		$form_field = array("name"=>"vehicle", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onChange", "script"=>"getDriverHelper(this.value);"), "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"servicedate", "content"=>"service date", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select","action"=>array("type"=>"onChange", "script"=>"getStartReading(this.value);"),  "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"substitutevehicle", "content"=>"sub. vehicle", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onChange", "script"=>"getDriverHelper(this.value);"), "options"=>$vehicles_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"starttime", "content"=>"time", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$times_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"endreading", "content"=>"end reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"distance", "content"=>"distance", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"repairkms", "content"=>"repair KMs", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driver1", "content"=>"driver1", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driver2", "content"=>"driver2", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"helper", "content"=>"helper", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$helpers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"penalitiestype", "content"=>"penalities type", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$pentypes_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"penalityamount", "content"=>"penality amt", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"action", "content"=>"show ", "readonly"=>"",  "required"=>"", "type"=>"checkbox", "options"=>array("substitutevehicleckbox"=>"substitute vehicle", "fine"=>"penalty", "drv_helper"=>"drvs,hlp","pendingservlogs"=>"pending servlogs"),  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"jsondata", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editservicelog";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicelogs";
		$form_info["bredcum"] = "edit servicelog";
		$form_field = array("name"=>"vehicle1", "content"=>"vehicle", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"servicedate1", "content"=>"service date", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"starttime1", "content"=>"time", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$times_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading1", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"endreading1", "content"=>"end reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"distance1", "content"=>"distance", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"repairkms1", "content"=>"repair KMs", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "servicelogs";	
		return View::make('servicelogs.formrowdatatable', array("values"=>$values));
	}
	
}
