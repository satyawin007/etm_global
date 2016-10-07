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
		//return json_encode(['status' => 'fail', 'message' => 'Please wait for sometime...!']);
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
					else{
						$fields["startTime"]="";
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
					if($jsonitem->driver3 != ""){
						$fields["driver3Id"] = $jsonitem->driver3;
					}
					if($jsonitem->driver4 != ""){
						$fields["driver4Id"] = $jsonitem->driver4;
					}
					if($jsonitem->driver5 != ""){
						$fields["driver5Id"] = $jsonitem->driver5;
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
					$success = false;
					$recs = \ServiceLog::where("contractVehicleId","=",$fields["contractVehicleId"])
											->where("serviceDate","=",$fields["serviceDate"])
											->where("startTime","=",$fields["startTime"])
											->where("status","=","ACTIVE")
											->get();
					if(!(count($recs)>0)){
						$db_functions_ctrl->insert($table, $fields);
						$success = true;
					}
					$veh_id = array();
					if(isset($jsonitem->substitutevehicle) &&  $jsonitem->substitutevehicle != ""){
						$veh_id = \Vehicle::where("vehicle.id","=",$jsonitem->substitutevehicle)
									->where("status","=","ACTIVE")
									->select(array("vehicle.id as vehicleId"))
									->get();
					}
					else{
						$veh_id = \ContractVehicle::where("contract_vehicles.id","=",$jsonitem->vehicle)->where("status","=","ACTIVE")->get();
					}
					if(count($veh_id)>0 && $success){
						$veh_id  = $veh_id[0];
						$veh_meeter = \VehicleMeeter::where("status","=","ACTIVE")
										->where("vehicleId","=",$veh_id->vehicleId)
										->update(array("endReading"=>$jsonitem->endreading,"endDate"=>$fields["serviceDate"]));
					}
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
		
		$states =  \State::Where("status","=","ACTIVE")->get();
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
		$status = "ACTIVE";
		if(isset($values["vehiclestatus"]) && $values["vehiclestatus"]=="INACTIVE"){
			$status = "INACTIVE";
		}
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			$entities = \ContractVehicle::join("vehicle","vehicle.id","=","contract_vehicles.vehicleId")
							->where("contractId","=",$contractId)
							->where("contract_vehicles.status","=",$status)
							->select(array("contract_vehicles.id as id", "vehicle.id as vehid", "vehicle.veh_reg as name"))
							->groupBy("vehicle.id")->get();
			foreach ($entities as $entity){
				if(isset($values["type"]) && $values["type"]=="vehicleids"){
					$response = $response."<option value='".$entity->vehid."'>".$entity->name."</option>";
				}
				else{
					$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";
				}
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
						where("id","=",$values["vehicleid"])->get();
			foreach ($entities as $entity){
				$response[] = array("<option value=''> --select driver1-- </option>"."<option  selected value='".$entity->driver1Id."'>".$drivers_arr[$entity->driver1Id]."</option>");
				if($entity->driver2Id != 0){
					$response[] = array("<option value=''> --select driver2-- </option>"."<option selected  value='".$entity->driver2Id."'>".$drivers_arr[$entity->driver2Id]."</option>");
				}
				else{
					$response[] = array("<option value=''> --select driver2-- </option>");
				}
				if($entity->driver3Id != 0){
					$response[] = array("<option value=''> --select driver3-- </option>"."<option selected  value='".$entity->driver3Id."'>".$drivers_arr[$entity->driver3Id]."</option>");
				}
				else{
					$response[] = array("<option value=''> --select driver3-- </option>");
				}
				if($entity->driver4Id != 0){
					$response[] = array("<option value=''> --select driver4-- </option>"."<option selected  value='".$entity->driver4Id."'>".$drivers_arr[$entity->driver4Id]."</option>");
				}
				else{
					$response[] = array("<option value=''> --select driver4-- </option>");
				}
				if($entity->driver5Id != 0){
					$response[] = array("<option value=''> --select driver5-- </option>"."<option selected  value='".$entity->driver5Id."'>".$drivers_arr[$entity->driver5Id]."</option>");
				}
				else{
					$response[] = array("<option value=''> --select driver5-- </option>");
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
				$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` left join contracts on contracts.id=clientholidays.contractId WHERE contractId=".$contractId." and clientholidays.status='Open' and  (fromDate<='".date('Y-m-d',strtotime("-".$i." days"))."' and toDate>='".date('Y-m-d',strtotime("-".$i." days"))."')"));
				if(count($holidays)>0) {
					$holidays = $holidays[0];
					if($holidays->count==0)
						$dates_arr[date('Y-m-d',strtotime("-".$i." days"))] = date('Y-m-d',strtotime("-".$i." days"));
				}
				$i++;
			}
			$ex_dates = array();
			$dates = "<option value=''> --select service date-- </option>";
			foreach ($dates_arr as $dt=>$val){
				if($dt != "" && $dt!= "1970-01-01"){
					if(!in_array($dt, $ex_dates)){
						$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
					}
				}
				$ex_dates[] = $dt;
			}
			//$dates_arr = array_reverse($dates_arr);
			$opendts = \ServiceLogRequest::where("contractId","=",$contractId)
					//->where("vehicleId","=",$values["vehicleid"])
					->where("deleted","=","No")
					->where("status","=","Open")->get();
			foreach ($opendts as $opendt){
				$opendt_arr = explode(",", $opendt->pendingDates);
				foreach ($opendt_arr as $opendt_arr_item){
					if($opendt_arr_item != ""){
						if(!in_array($opendt_arr_item, $ex_dates)){
							$dates = $dates."<option value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
						}
					}
					$ex_dates[] = $opendt_arr_item;
				}
				if($opendt->customDate != "" && $opendt->customDate!= "1970-01-01"){
					if(!in_array($opendt->customDate, $ex_dates)){
						$dates = $dates."<option value='$opendt->customDate'>".date('d-m-Y',strtotime($opendt->customDate))."</option>";
					}
					$ex_dates[] = $opendt->customDate;
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
	public function getStartReadingSubstitute()
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
				
			$selct_args = array();
			$selct_args[] = "contract_vehicles.vehicleId as contractVehicleId1";
			$selct_args[] = "service_logs.serviceDate as serviceDate";
			$selct_args[] = "service_logs.startReading as startReading";
			$selct_args[] = "service_logs.endReading as endReading";
			$selct_args[] = "service_logs.driver1Id as driver1Id";
			$selct_args[] = "service_logs.driver2Id as driver2Id";
			$selct_args[] = "service_logs.driver3Id as driver3Id";
			$selct_args[] = "service_logs.driver4Id as driver4Id";
			$selct_args[] = "service_logs.driver5Id as driver5Id";
			$selct_args[] = "service_logs.helperId as helperId";
			$selct_args[] = "service_logs.remarks as remarks";
			$selct_args[] = "service_logs.status as status";
			$selct_args[] = "service_logs.contractVehicleId as contractVehicleId";
			$servlogs = \ServiceLog::join("contract_vehicles","contract_vehicles.id","=","service_logs.contractVehicleId")
									->where("service_logs.contractVehicleId","=",$values["vehicleid"])
									->where("service_logs.status","=","ACTIVE")
									->where("service_logs.contractId","=",$contractId)
									->where("service_logs.serviceDate","<=",$values["date"])
									->orderBy("serviceDate",'desc')->select($selct_args)->get();
			$response[0] = 0;
			$veh_meeter = \VehicleMeeter::where("vehiclemeterdetails.status","=","ACTIVE")
							->where("vehiclemeterdetails.vehicleId","=",$values["subvehicleid"])->get();
			if(count($veh_meeter)>0){
				$veh_meeter = $veh_meeter[0];
				$response[0] = array($veh_meeter->endReading);
			}
		}
		$today = new \DateTime(date("Y-m-d"));
		$dates_arr = array();
		$i = 0;
		$cmp_date = $today->modify("0 day");
		while($i<=5){
			$cmp_date = $cmp_date->format('Y-m-d');
			$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` left join contracts on contracts.id=clientholidays.contractId WHERE contractId=".$contractId." and clientholidays.status='Open' and  (fromDate<='".date('Y-m-d',strtotime("-".$i." days"))."' and toDate>='".date('Y-m-d',strtotime("-".$i." days"))."')"));
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
		$ex_dates = array();
		foreach ($dates_arr as $dt=>$val){
			if($dt != "" && $dt != "1970-01-01"){
				if(!in_array($dt, $ex_dates)){
					if($values["servicedate"]==$dt){
						$dates = $dates."<option selected value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
					}
					else{
						$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
					}
				}
				$ex_dates[] = $dt;
			}
		}
	
		//$dates_arr = array_reverse($dates_arr);
		$opendts = \ServiceLogRequest::where("contractId","=",$contractId)
						//->where("vehicleId","=",$values["vehicleid"])
						->where("deleted","=","No")->where("status","=","Open")->get();
		foreach ($opendts as $opendt){
			$opendt_arr = explode(",", $opendt->pendingDates);
			foreach ($opendt_arr as $opendt_arr_item){
				if($opendt_arr_item != ""){
					if(!in_array($opendt_arr_item, $ex_dates)){
						if($values["servicedate"]==$opendt_arr_item){
							$dates = $dates."<option selected value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
						}
						else {
							$dates = $dates."<option value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
						}
						$ex_dates[] = $opendt_arr_item;
					}
				}
			}
			if($opendt->customDate != "" && $opendt->customDate!= "1970-01-01"){
				if(!in_array($opendt->customDate, $ex_dates)){
					if($values["servicedate"]==$opendt->customDate){
						$dates = $dates."<option selected value='$opendt->customDate'>".date('d-m-Y',strtotime($opendt->customDate))."</option>";
					}
					else{
						$dates = $dates."<option value='$opendt->customDate'>".date('d-m-Y',strtotime($opendt->customDate))."</option>";
					}
					$ex_dates[] = $opendt->customDate;
				}
			}
		}
	
		$response[] = array($dates);
	
		$vehicles =  \Vehicle::all();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
		}
		$drivers =  \Employee::All();
		$drivers_arr = array();
		foreach ($drivers as $driver){
			$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
		}
	
		$con_vehs_text_arr = array();
		$i=0;
	
		foreach ($servlogs as $servlog){
			if($i>=5){
				break;
			}
			$i++;
			$con_vehs_text = array();
			$con_vehs_text['vehicle'] = $vehicles_arr[$servlog->contractVehicleId1];
			$con_vehs_text['servicedate'] = date("d-m-Y",strtotime($servlog->serviceDate));
			$con_vehs_text['reading'] = $servlog->startReading." - ".$servlog->endReading." = ".($servlog->endReading-$servlog->startReading);
			$drivers = "";
	
			if($servlog->driver1Id != 0 && isset($drivers_arr[$servlog->driver1Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver1Id].", ";
			}
			if($servlog->driver2Id != 0 && isset($drivers_arr[$servlog->driver2Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver2Id].", ";
			}
			if($servlog->driver3Id != 0 && isset($drivers_arr[$servlog->driver3Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver3Id].", ";
			}
			if($servlog->driver4Id != 0 && isset($drivers_arr[$servlog->driver4Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver4Id].", ";
			}
			if($servlog->driver5Id != 0 && isset($drivers_arr[$servlog->driver5Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver5Id].", ";
			}
			$con_vehs_text['drivers'] = $drivers;
			if($servlog->helperId != 0 && !in_array($servlog->helperId, $drivers_arr)){
				$con_vehs_text['helper'] = $drivers_arr[$servlog->helperId];
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
			
			$selct_args = array();
			$selct_args[] = "contract_vehicles.vehicleId as contractVehicleId1";
			$selct_args[] = "service_logs.serviceDate as serviceDate";
			$selct_args[] = "service_logs.substituteVehicleId as substituteVehicleId";
			$selct_args[] = "service_logs.startReading as startReading";
			$selct_args[] = "service_logs.endReading as endReading";
			$selct_args[] = "service_logs.driver1Id as driver1Id";
			$selct_args[] = "service_logs.driver2Id as driver2Id";
			$selct_args[] = "service_logs.driver3Id as driver3Id";
			$selct_args[] = "service_logs.driver4Id as driver4Id";
			$selct_args[] = "service_logs.driver5Id as driver5Id";			
			$selct_args[] = "service_logs.helperId as helperId";
			$selct_args[] = "service_logs.remarks as remarks";
			$selct_args[] = "service_logs.status as status";
			$selct_args[] = "service_logs.contractVehicleId as contractVehicleId";
			$servlogs = \ServiceLog::join("contract_vehicles","contract_vehicles.id","=","service_logs.contractVehicleId")
						->where("service_logs.contractVehicleId","=",$values["vehicleid"])
						->where("service_logs.status","=","ACTIVE")
						->where("service_logs.contractId","=",$contractId)
						//->where("substituteVehicleId","=",0)
						->where("service_logs.serviceDate","<=",$values["date"])
						->orderBy("serviceDate",'desc')->select($selct_args)->get();
			if(count($servlogs)>0){
				$len = count($servlogs);
				$servlog = $servlogs[0];
				$veh_meeter = \VehicleMeeter::where("vehiclemeterdetails.status","=","ACTIVE")
								->join("contract_vehicles","contract_vehicles.vehicleId","=","vehiclemeterdetails.vehicleId")
								->where("contract_vehicles.id","=",$values["vehicleid"])->get();
				if(count($veh_meeter)>0){
					$veh_meeter = $veh_meeter[0];					
					$mtr_dt = new \DateTime($veh_meeter->startDate);
					$slog_dt = new \DateTime($servlog->serviceDate);					
					if ($mtr_dt > $slog_dt) {
						$response[0] = array($veh_meeter->startReading);
					}
					else{
						foreach ($servlogs as $servlog){
							if($servlog->substituteVehicleId>0){
								continue;
							}
							$response[0] = array($servlog->endReading);
							break;
						}						
					}
				}
				else{
					foreach ($servlogs as $servlog){
						if($servlog->substituteVehicleId>0){
							continue;
						}
						$response[0] = array($servlog->endReading);
						break;
					}
				}
			}
			else{
				$veh_meeter = \VehicleMeeter::where("vehiclemeterdetails.status","=","ACTIVE")
								->join("contract_vehicles","contract_vehicles.vehicleId","=","vehiclemeterdetails.vehicleId")
								->where("contract_vehicles.id","=",$values["vehicleid"])->get();
				if(count($veh_meeter)>0){
					$veh_meeter = $veh_meeter[0];
					$response[0] = array($veh_meeter->endReading);
				}
			}
		}
		$today = new \DateTime(date("Y-m-d"));
		$dates_arr = array();
		$i = 0;
		$cmp_date = $today->modify("0 day");
		while($i<=5){
			$cmp_date = $cmp_date->format('Y-m-d');
			$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` left join contracts on contracts.id=clientholidays.contractId WHERE contractId=".$contractId." and clientholidays.status='Open' and  (fromDate<='".date('Y-m-d',strtotime("-".$i." days"))."' and toDate>='".date('Y-m-d',strtotime("-".$i." days"))."')"));
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
		$ex_dates = array();
		foreach ($dates_arr as $dt=>$val){
			if($dt != "" && $dt != "1970-01-01"){
				if(!in_array($dt, $ex_dates)){
					if($values["servicedate"]==$dt){
						$dates = $dates."<option selected value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
					}
					else{
						$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
					}
				}
				$ex_dates[] = $dt;
			}
		}
		
		//$dates_arr = array_reverse($dates_arr);
		$opendts = \ServiceLogRequest::where("contractId","=",$contractId)
				//->where("vehicleId","=",$values["vehicleid"])
				->where("deleted","=","No")->where("status","=","Open")->get();
		foreach ($opendts as $opendt){
			$opendt_arr = explode(",", $opendt->pendingDates);
			foreach ($opendt_arr as $opendt_arr_item){
				if($opendt_arr_item != ""){
					if(!in_array($opendt_arr_item, $ex_dates)){
						if($values["servicedate"]==$opendt_arr_item){
							$dates = $dates."<option selected value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
						}
						else {
							$dates = $dates."<option value='$opendt_arr_item'>".date('d-m-Y',strtotime($opendt_arr_item))."</option>";
						}
						$ex_dates[] = $opendt_arr_item;
					}
				}
			}
			if($opendt->customDate != "" && $opendt->customDate!= "1970-01-01"){
				if(!in_array($opendt->customDate, $ex_dates)){
					if($values["servicedate"]==$opendt->customDate){
						$dates = $dates."<option selected value='$opendt->customDate'>".date('d-m-Y',strtotime($opendt->customDate))."</option>";
					}
					else{
						$dates = $dates."<option value='$opendt->customDate'>".date('d-m-Y',strtotime($opendt->customDate))."</option>";
					}
					$ex_dates[] = $opendt->customDate;
				}
			}
		}
		
		$response[] = array($dates);
		
		$vehicles =  \Vehicle::all();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
		}
		$drivers =  \Employee::All();
		$drivers_arr = array();
		foreach ($drivers as $driver){
			$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
		}
				
		$con_vehs_text_arr = array();
		$i=0;
		
		foreach ($servlogs as $servlog){
			if($i>=5){
				break;
			}
			$i++;
			$con_vehs_text = array();			 
			$con_vehs_text['vehicle'] = $vehicles_arr[$servlog->contractVehicleId1];
			if($servlog->substituteVehicleId>0){
				$con_vehs_text['vehicle'] = $con_vehs_text['vehicle']."            (".$vehicles_arr[$servlog->substituteVehicleId].")";
			}
			$con_vehs_text['servicedate'] = date("d-m-Y",strtotime($servlog->serviceDate));
			$con_vehs_text['reading'] = $servlog->startReading." - ".$servlog->endReading." = ".($servlog->endReading-$servlog->startReading);
			$drivers = "";

			if($servlog->driver1Id != 0 && isset($drivers_arr[$servlog->driver1Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver1Id].", ";
			}
			if($servlog->driver2Id != 0 && isset($drivers_arr[$servlog->driver2Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver2Id].", ";
			}
			if($servlog->driver3Id != 0 && isset($drivers_arr[$servlog->driver3Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver3Id].", ";
			}
			if($servlog->driver4Id != 0 && isset($drivers_arr[$servlog->driver4Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver4Id].", ";
			}
			if($servlog->driver5Id != 0 && isset($drivers_arr[$servlog->driver5Id])){
				$drivers = $drivers.$drivers_arr[$servlog->driver5Id].", ";
			}
			$con_vehs_text['drivers'] = $drivers;
			if($servlog->helperId != 0 && !in_array($servlog->helperId, $drivers_arr)){
				$con_vehs_text['helper'] = $drivers_arr[$servlog->helperId];
			}
			$con_vehs_text['remarks'] = $servlog->remarks;
			$con_vehs_text['status'] = $servlog->status;
			$con_vehs_text_arr[] = $con_vehs_text;
		}
		$response[] = $con_vehs_text_arr;
		echo json_encode($response);;
	}
	
	public function viewPendingServiceLogs()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$data = "";
			$contractid = \Contract::where("status","=","ACTIVE")->where("clientId","=",$values["clientid"])->where("depotId","=",$values["depot"])->get();
			if(count($contractid)>0){
				$contractid = $contractid[0];
				$contractid = $contractid->id;
				$con_vehicles = \ContractVehicle::where("contract_vehicles.status","=","ACTIVE")
										->leftjoin("vehicle","vehicle.id","=","contract_vehicles.vehicleId")
										->where("contractId","=",$contractid)
										->select(array("contract_vehicles.id as convehId", "vehicleId as vehicleId", "vehicle.veh_reg as vehiclereg"))->get();
				$clientname = \Client::where("id","=",$values["clientid"])->first();
				$clientname = $clientname->name;
				$depotname = \Depot::where("id","=",$values["depot"])->first();
				$depotname = $depotname->name;
				foreach($con_vehicles as $con_vehicle){
					$args = array("vehicleid"=>$con_vehicle->convehId,"clientid"=>$values["clientid"],"depotid"=>$values["depot"]);
					$dates = $this->getPendingServiceLogs($args);
					if(count($dates)>0){
						$data = $data."<tr>";
						$data = $data."<td>".$clientname."</td>";
						$data = $data."<td>".$depotname."</td>";
						$data = $data."<td>".$con_vehicle->vehiclereg."</td>";
						$dt_str = "";
						foreach ($dates as $date=>$val){
							$dt_str = $dt_str.date("d-m-Y",strtotime($date)).", ";
						}
						$data = $data."<td>".$dt_str."</td>";
						$data = $data."</tr>";
					}
				}
			}
			echo $data;
			return;
		}
		
		$values['bredcum'] = "VIEW PENDING SERVICE LOGS";
		$values['home_url'] = 'contractsmenu';
		$values['add_url'] = '';
		$values['form_action'] = 'servicelogs';
		$values['action_val'] = '';
		$values["showsearchrow"]="servlogrequests";
		$theads = array('client name', "client branch", "vehicle", "Pending Dates", "Custom Date", "comments", "Requested By", "status", "Opened/Closed By", "Opended On", "Actions", "change status");
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
		
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "servicelogrequests&clientid=0&depotid=0";
		return View::make('servicelogs.pendingservlogsdatatable', array("values"=>$values));
	}
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getPendingServiceLogs($args=null)
	{
		$values = Input::all();
		if($args != null){
			$values = $args;
		}
		$startreading = "";
		$response = array();
		$today = new \DateTime(date("Y-m-d"));
		$dates_str = "";
		$dates_arr = array();
		$i = 0;
		$cmp_date = $today->modify("0 day");
		$contractId = 0;
		$contract_start_dt = "";
		$end_days = 30;
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			$entities = \ContractVehicle::where("contractId","=",$contractId)
							->where("status","=","ACTIVE")
							->where("id","=",$values['vehicleid'])->get();
			if(count($entities)>0){
				$entities = $entities[0];
				$contract_start_dt = $entities->vehicleStartDate;
			}
		}
		if($contract_start_dt != "" && $contract_start_dt != "0000-00-00"){
			$from=date_create(date('Y-m-d'));
			$to=date_create(date('Y-m-d', strtotime($contract_start_dt)));
			$diff=date_diff($to,$from);
			$diff = $diff->format('%a');
			if($diff>5){
				$today = new \DateTime(date("Y-m-d"));
				$from=date_create(date('Y-m-d'));
				$from=$from->modify("-6 day");
				$to=date_create(date('Y-m-d', strtotime($contract_start_dt)));
				$diff=date_diff($to,$from);
				$end_days = $diff->format('%a');
				$cmp_date = $today->modify("-6 day");
			}
			else{
				$end_days = 0;
			}
		}
	
		while($i<$end_days){
			$cmp_date = $cmp_date->format('Y-m-d');
			$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` WHERE contractId=".$contractId." and clientholidays.status='Open' and  (fromDate<=STR_TO_DATE('".$cmp_date."','%Y-%m-%d') and toDate>=STR_TO_DATE('".$cmp_date."','%Y-%m-%d'))"));
			if(count($holidays)>0) {
				$holidays = $holidays[0];
				if($holidays->count==0) {
					$dates_arr[$cmp_date] = $cmp_date;
				}
			}
			$i++;
			$cmp_date = $today->modify("-1 day");
			$today = $cmp_date;
		}
		$open_dts = \ServiceLogRequest::where("contractId","=",$contractId)
						->where("deleted","=",'No')
						->where("status","=",'Open')->get();
						//->where("vehicleId","=",$values["vehicleid"])->get();
		foreach ($open_dts as $open_dt){
			$odts_arr = explode(",", $open_dt->pendingDates);
			foreach ($odts_arr as $odts_arr_item){
				if (array_key_exists($odts_arr_item, $dates_arr)) {
					unset($dates_arr[$odts_arr_item]);
				}
			}
		}
		$service_logs = \ServiceLog::join("contract_vehicles","contract_vehicles.contractId","=","service_logs.contractId")
							->where("service_logs.contractId","=",$contractId)
							->where("service_logs.status","=",'ACTIVE')
							->where("service_logs.contractVehicleId","=",$values["vehicleid"])->get();
		foreach ($service_logs as $service_log){
			if (array_key_exists($service_log->serviceDate, $dates_arr)) {
				unset($dates_arr[$service_log->serviceDate]);
			}
		}
		$response = array();
		$dates = "";
		$dates_arr = array_reverse($dates_arr);
		if($args != null){
			return $dates_arr;
		}
		foreach ($dates_arr as $dt=>$val){
			$dates = $dates."<option value='$dt'>".date('d-m-Y',strtotime($dt))."</option>";
		}
		$response[] = array($dates);
		echo json_encode($response);
	}
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function checkPendingDates()
	{
		$values = Input::all();
		$startreading = "";
		$response = array();
		$today = new \DateTime(date("Y-m-d"));
		$dates_str = "";
		$dates_arr = array();
		$i = 0;
		$cmp_date = $today->modify("0 day");
		$contractId = 0;
		$contract_start_dt = "";
		$end_days = 30;
		$entities = \Contract::where("clientId","=",$values['clientid'])->where("depotId","=",$values['depotid'])->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$contractId = $entities->id;
			$entities = \ContractVehicle::where("contractId","=",$contractId)
							->where("status","=","ACTIVE")
							->where("id","=",$values['vehicleid'])->get();
			if(count($entities)>0){
				$entities = $entities[0];
				$contract_start_dt = $entities->vehicleStartDate;
			}
		}
		if($contract_start_dt != "" && $contract_start_dt != "0000-00-00"){
			$from=date_create(date('Y-m-d'));
			$to=date_create(date('Y-m-d', strtotime($contract_start_dt)));
			$diff=date_diff($to,$from);
			$diff = $diff->format('%a');
			if($diff>5){
				$today = new \DateTime(date("Y-m-d"));
				$from=date_create(date('Y-m-d'));
				$from=$from->modify("-6 day");
				$to=date_create(date('Y-m-d', strtotime($contract_start_dt)));
				$diff=date_diff($to,$from);
				$end_days = $diff->format('%a');
				$cmp_date = $today->modify("-6 day");
			}
			else{
				$end_days = 0;
			}
		}
		while($i<$end_days){
			$cmp_date = $cmp_date->format('Y-m-d');
			$holidays = \DB::select(\DB::raw("SELECT count(*) as count FROM `clientholidays` WHERE contractId=".$contractId." and clientholidays.status='Open' and  (fromDate<=STR_TO_DATE('".$cmp_date."','%Y-%m-%d') and toDate>=STR_TO_DATE('".$cmp_date."','%Y-%m-%d'))"));
			if(count($holidays)>0) {
				$holidays = $holidays[0];
				if($holidays->count==0) {
					$dates_arr[$cmp_date] = $cmp_date;
				}
			}
			$i++;
			$cmp_date = $today->modify("-1 day");
			$today = $cmp_date;
		}
		$open_dts = \ServiceLogRequest::where("contractId","=",$contractId)
						->where("deleted","=","No")
						->where("status","=",'Open')->get();
						//->where("vehicleId","=",$values["vehicleid"])->get();
		foreach ($open_dts as $open_dt){
			$odts_arr = explode(",", $open_dt->pendingDates);
			foreach ($odts_arr as $odts_arr_item){
				if (array_key_exists($odts_arr_item, $dates_arr)) {
					unset($dates_arr[$odts_arr_item]);
				}
			}
		}
		$service_logs = \ServiceLog::join("contract_vehicles","contract_vehicles.id","=","service_logs.contractVehicleId")
							->where("service_logs.contractId","=",$contractId)
							->where("service_logs.status","=",'ACTIVE')
							->where("service_logs.contractVehicleId","=",$values["vehicleid"])->select(array("service_logs.serviceDate"))->get();
		foreach ($service_logs as $service_log){
			if (array_key_exists($service_log->serviceDate, $dates_arr)) {				
				unset($dates_arr[$service_log->serviceDate]);
			}
		}
		$response = array();
		foreach ($dates_arr as $dates_arr_item){
			$dates_str = $dates_str.date("d-m-Y",strtotime($dates_arr_item)).", ";
		}
		if(count($dates_arr)>0){
			$dates_str = "Pending Dates : <br/>".$dates_str;
			$response = array("status"=>"fail", "message"=>"There are some pending service logs <a onclick='showPendingLogs(\"".$dates_str."\")'>click here</a>");
		}
		else{
			$response = array("status"=>"success","message"=>"");
		}
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
		$theads = array('vehicle no', 'sub Vehicle', "service date", "start time", "start reading", "end reading", "distance", "driver",  "helper", "trip no", "status","Actions");
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
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state['name'];
		}
		
		$cities =  \City::Where("status","=","ACTIVE")->get();
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
			$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
			if(!in_array($vehicle['id'],$ex_Vehicles_arr)){
				//$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
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
		$form_field = array("name"=>"substitutevehicle", "content"=>"sub. vehicle", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onChange", "script"=>"getStartReadingSubstitute(this.value);"), "options"=>$vehicles_arr);
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
		$form_field = array("name"=>"driver3", "content"=>"driver3", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driver4", "content"=>"driver4", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driver5", "content"=>"driver5", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
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
