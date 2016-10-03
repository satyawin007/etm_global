<?php namespace contracts;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class VehicleMeeterController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addVehicleMeeter()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("vehicle"=>"vehicleId","meeterno"=>"meterNo","startdate"=>"startDate","startreading"=>"startReading");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "startdate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$fields["endReading"] = $fields["startReading"];
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\VehicleMeeter"; 
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("vehiclemeeters");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("vehiclemeeters");
			}
		}
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editVehicleMeeter()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("meeterno1"=>"meterNo","startdate1"=>"startDate","endtdate1"=>"endDate",
							"startreading1"=>"startReading","endreading1"=>"endReading","status1"=>"status"
							);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "startdate1" || $key == "endtdate1"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\VehicleMeeter";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("vehiclemeeters");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("vehiclemeeters");
			}
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
	public function getMeeterNo()
	{
		$values = Input::all();
		$entities = \VehicleMeeter::where("vehicleId","=",$values['vehicleid'])->get();
		if(count($entities)>0){
			echo "M".(count($entities)+1);
			return;
		}
		echo "M1";
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
	public function manageVehicleMeeters()
	{
		$values = Input::all();
		$values['bredcum'] = "VEHICLE MEETERS";
		$values['home_url'] = 'contractsmenu';
		$values['add_url'] = 'addvehiclemeeter';
		$values['form_action'] = 'vehiclemeeter';
		$values['action_val'] = '';
		$theads = array('Vehicle','meeter no', "start date", "end date", "start reading","end reading", "status","Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
	
		
		$form_info = array();
		$form_info["name"] = "addvehiclemeeter";
		$form_info["action"] = "addvehiclemeeter";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "vehiclemeeters";
		$form_info["bredcum"] = "add vehicle meeter";
		
		$ex_Vehicles = \VehicleMeeter::where("status","=","ACTIVE")->get();
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
		$form_field = array("name"=>"vehicle", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getMeeterNo(this.value);"), "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"meeterno", "content"=>"meeterno", "readonly"=>"readonly",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startdate", "content"=>"start date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading", "content"=>"startreading", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editvehiclemeeter";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "vehiclemeeters";
		$form_info["bredcum"] = "add client";
		
		$form_field = array("name"=>"vehicle1", "content"=>"vehicle", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"meeterno1", "content"=>"meeterno", "readonly"=>"readonly",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startdate1", "content"=>"start date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"endtdate1", "content"=>"end date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading1", "content"=>"start reading", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"endreading1", "content"=>"end reading", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "vehiclemeeters";	
		return View::make('contracts.lookupdatatable', array("values"=>$values));
	}
	
}
