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
				$recs = \ServiceLogRequest::where("contractId","=",$contract->id)
							->where("deleted","=","No")
							->where("vehicleId","=",$values["vehicle"])
							->get();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key=="customdate" || $key=="todate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key=="pendingdates"){
							$dates = "";
							foreach ($values[$key] as $val1){
								$dt = date("Y-m-d",strtotime($val1));
								$contains = false;
								foreach ($recs as $rec){
									if (substr_count($rec->pendingDates, $dt) > 0) {
										$contains = true;
									}
								}
								if(!$contains){
									$dates = $dates.date("Y-m-d",strtotime($val1)).",";
								}
							}
							$fields[$val] = $dates;
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				if(!isset($fields["pendingDates"])){
					$fields["pendingDates"]="";
				}
				$fields["contractId"] = $contract->id;
				if($fields["pendingDates"]=="" && $values["customdate"]==""){
					return json_encode(['status' => 'fail', 'message' => 'Service Date or Custom Date is already exists!']);
				}
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
	public function editServiceLogRequest()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$field_names = array(
					"deleted1"=>"deleted","comments1"=>"comments" //"status1"=>"status",
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
			if($values["status1"] == "Open" || $values["status1"] == "Close"){
				$fields["openedBy"] = \Auth::user()->id;
				$fields["opened_at"] = date("Y-m-d h:i:s");
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
		$form_field = array("name"=>"client1", "content"=>"client", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot1", "content"=>"depot", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle1", "content"=>"vehicle", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"pendingdates1", "content"=>"pending dates", "readonly"=>"readonly",  "required"=>"required", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"customdate1", "content"=>"custom date", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments1", "content"=>"comments", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("Requested"=>"Requested"), "class"=>"form-control");
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
	
	function updateServiceLogRequestStatus(){
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$status = false;
			$fields = array();
			$fields["status"] = $values["updatelogstatus"];
			if($values["updatelogstatus"] == "Open" || $values["updatelogstatus"] == "Close"){
				$fields["openedBy"] = \Auth::user()->id;
				$fields["opened_at"] = date("Y-m-d h:i:s");
			}
			foreach ($values["action"] as $action){
				$status = false;
				$db_functions_ctrl = new DBFunctionsController();
				$table = "\ServiceLogRequest";
				$data = array("id"=>$action);
				$db_functions_ctrl->update($table, $fields, $data);
				$status = true;
			}
			if($status){
				echo json_encode(array("status"=>"success", "message"=>"Operation completed Successfully"));
				return;
			}
			else{
				echo json_encode(array("status"=>"fail", "message"=>"Operation Could not be completed, Try Again!"));
				return;
			}
		}
	}
	
}
