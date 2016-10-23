<?php namespace contracts;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
class ClientHolidaysController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addClientHolidays()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("fromdate"=>"fromDate","todate"=>"toDate","comments"=>"comments","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="fromdate" ||  $key=="todate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$contract = \Contract::where("clientId","=",$values["clientname"])->where("depotId","=",$values["depot"])->first();
			if($contract != null){
				$fdt = date("Y-m-d",strtotime($fields["fromDate"]));
				$tdt = date("Y-m-d",strtotime($fields["toDate"]));
				$ex_recs = \ClientHolidays::whereRaw("contractId=$contract->id and ((fromDate between '$fdt' and '$tdt') or  (toDate between '$fdt' and '$tdt'))")
							->get();
				if(count($ex_recs)>0){
					\Session::put("message","From Date or To Date is already Existed");
					return \Redirect::to("clientholidays");
				}
				$fields["contractId"] = $contract->id;
				$fields["deleted"] = "No";
				$fields["opened_at"] = date("Y-m-d h:i");
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ClientHolidays";
				if($db_functions_ctrl->insert($table, $fields)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("clientholidays");
				}
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("clientholidays");
			}
		}
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editClientHolidays()
	{
		$values = Input::all();
	if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("fromdate1"=>"fromDate","todate1"=>"toDate","comments1"=>"comments","status1"=>"status","deleted1"=>"deleted");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="fromdate1" ||  $key=="todate1"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ClientHolidays";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("clientholidays");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("clientholidays");
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editcity?id";
		$form_info["action"] = "editcity?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cities";
		$form_info["bredcum"] = "edit city";
	
		$form_fields = array();
	
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		$entity = \City::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"", "value"=>$entity->name, "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"citycode", "content"=>"city code", "readonly"=>"",  "value"=>$entity->code, "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "value"=>$entity->stateId, "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
		
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.editform",array("form_info"=>$form_info));
		}
	}
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getCitiesbyStateId()
	{
		$values = Input::all();
		$entities = \City::where("stateId","=",$values['id'])->get();
		$response = "<option> --select city-- </option>";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";			
		}
		echo $response;
	}	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getfinanceCompanybyCityId()
	{
		$values = Input::all();
		$entities = \FinanceCompany::where("cityId","=",$values['id'])->get();
		$response = "<option> --select finance company-- </option>";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";
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
	public function manageClientHolidays()
	{
		$values = Input::all();
		$values['bredcum'] = "CLIENT HOLIDAYS";
		$values['home_url'] = 'contractsmenu';
		$values['add_url'] = 'addclientholidays';
		$values['form_action'] = 'clientholidays';
		$values['action_val'] = '';
		$theads = array('client','Branch/Depot', 'from date', "to date", "comments", "status", "Deleted", "open/Closed By", "open/Closed at", "Actions", "change status");
		$values["theads"] = $theads;
		$values["showsearchrow"]="servlogrequests";
			
		$actions = array();
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
	
		
		$form_info = array();
		$form_info["name"] = "addclientholidays";
		$form_info["action"] = "addclientholidays";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "clientholidays";
		$form_info["bredcum"] = "add clientholidays";
		
		$form_fields = array();		
		
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		
		/*
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"), "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$citie_arr);
		$form_fields[] = $form_field;
		*/
		$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments", "content"=>"comments", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("Requested"=>"Requested","Closed"=>"Closed"));
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editclientholidays";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "clients";
		$form_info["bredcum"] = "add client";
		$form_field = array("name"=>"clientname1", "content"=>"client name", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot1", "content"=>"depot/branch name", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fromdate1", "content"=>"from date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"todate1", "content"=>"to date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments1", "content"=>"comments", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("Requested"=>"Requested"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"deleted1", "content"=>"deleted", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("Yes"=>"Yes","No"=>"No"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "content"=>"", "value"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "clientholidays";	
		return View::make('contracts.lookupdatatable', array("values"=>$values));
	}
	
	function updateClientHolidaysRequestStatus(){
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$status = false;
			$fields = array();
			$fields["status"] = $values["updatelogstatus"];
			if($values["updatelogstatus"] == "Open" || $values["updatelogstatus"] == "Close" || $values["updatelogstatus"] == "Closed"){
				$fields["openedBy"] = \Auth::user()->id;
				$fields["opened_at"] = date("Y-m-d h:i:s");
			}
			//print_r($fields); die();
			foreach ($values["action"] as $action){
				$status = false;
				$db_functions_ctrl = new DBFunctionsController();
				$table = "\ClientHolidays"; 
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
