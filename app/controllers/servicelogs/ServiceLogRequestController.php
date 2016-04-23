<?php namespace servicelogs;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
class ServiceLogRequestController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addServiceLogRequest()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["test"];
			$success = false;
			$contract = \Contract::where("clientId","=",$values["clientid"])->where("depotId","=",$values["depot"])->get();
			if(count($contract)>0){
				$contract = $contract[0];
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ServiceLogRequest";
				//$values["test"];
				$field_names = array(
						"customdate"=>"customDate","vehicle"=>"vehicleId","pendingcomments"=>"comments",
						"pendingdates"=>"pendingDates"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key=="customdate" || $key=="todate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key=="pendingdates"){
							$dates = "";
							foreach ($values[$key] as $val1){
								$dates = $dates.date("Y-m-d",strtotime($val1)).",";
							}
							$fields[$val] = $dates;
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				$fields["contractId"] = $contract->id;
				if($db_functions_ctrl->insert($table, $fields)){
					return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
				}
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
	public function editServiceLogRequest()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$field_names = array(
					"status1"=>"status","deleted1"=>"deleted","comments1"=>"comments"
			);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="customdate" || $key=="todate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ServiceLogRequest";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("servicelogrequests");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("servicelogrequests");
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
			$dates_arr = array_reverse($dates_arr);
			foreach ($dates_arr as $dt=>$val){
				$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
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
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			$entity = \ServiceLog::where("status","=","ACTIVE")->
						where("contractId","=",$contractId)->
						where("contractVehicleId","=",$values["vehicleid"])->orderBy("serviceDate",'desc')->first();
			if($entity != null){
				$response[0] = array($entity->endReading);
			}
		}
		$today = new \DateTime($values["servicedate"]);
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
		
		$dates = "<option value=''> --select service date-- </option>";
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
		
		while($i<30){
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
		$today = new \DateTime(date("Y-m-d"));
		$prevdays = $today->modify('-30 day');
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
	public function manageServiceLogRequests()
	{
		$values = Input::all();
		$values['bredcum'] = "SERVICE LOG REQUESTS";
		$values['home_url'] = 'contractsmenu';
		$values['add_url'] = '';
		$values['form_action'] = 'servicelogs';
		$values['action_val'] = '';
		$values["showsearchrow"]="servlogrequests";
		$theads = array('client name', "client branch", "vehicle", "Pending Dates", "Custom Date", "comments", "Requested By", "status","Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
		
		$form_info = array();
		$form_info["name"] = "addservicelogrequest";
		$form_info["action"] = "addservicelogrequest";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicelogrequests";
		$form_info["bredcum"] = "add servicelog";
		
		$form_fields = array();		
		$form_info["form_fields"] = $form_fields;
		
		
		
		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editservicelogrequest";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicelogs";
		$form_info["bredcum"] = "edit servicelog";
		$form_field = array("name"=>"client1", "content"=>"client", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot1", "content"=>"depot", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle1", "content"=>"vehicle", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"pendingdates1", "content"=>"pending dates", "readonly"=>"readonly",  "required"=>"required", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments1", "content"=>"comments", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("Requested"=>"Requested","Open"=>"Open","Close"=>"Close"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"deleted1", "value"=>"", "content"=>"deleted", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("No"=>"No","Yes"=>"Yes"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "servicelogrequests&clientid=0&depotid=0";	
		return View::make('servicelogs.lookupdatatable', array("values"=>$values));
	}
	
}
