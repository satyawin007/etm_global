<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class EmployeeBattaController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addEmployeeBatta()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("sourcecity"=>"sourceCity", "destinationcity"=>"destinationCity", "vehicletype"=>"vehicleTypeId", "driverbattaperday"=>"driverBatta", "driversalaryperday"=>"driverSalary", "helperbattaperday"=>"helperBatta", "helpersalaryperday"=>"helperSalary", "noofdrivers"=>"noOfDrivers");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "EmployeeBatta";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("employeebattas");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("employeebattas");
			}	
		}		
		$form_info = array();
		$form_info["name"] = "addemployeebatta";
		$form_info["action"] = "addemployeebatta";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "employeebattas";
		$form_info["bredcum"] = "add employee batta";
		
		$form_fields = array();
		
		$cities =  \City::Where("status","=","ACTIVE")->get();
		$city_arr = array();
		foreach ($cities as $city){
			$city_arr[$city['id']] = $city->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","VEHICLE TYPE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$types =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['id']] = $type->name;
		}
		
		$form_field = array("name"=>"sourcecity", "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"destinationcity", "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"vehicletype", "content"=>"Vehicle Type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$type_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driverbattaperday", "content"=>"driver batta per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driversalaryperday", "content"=>"driver salary per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"helperbattaperday", "content"=>"helper batta per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"helpersalaryperday", "content"=>"helper salary per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofdrivers", "content"=>"no of drivers", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;		
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editEmployeeBatta()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("sourcecity"=>"sourceCity", "destinationcity"=>"destinationCity", "vehicletype"=>"vehicleTypeId", "driverbattaperday"=>"driverBatta", "driversalaryperday"=>"driverSalary", "helperbattaperday"=>"helperBatta", "helpersalaryperday"=>"helperSalary","status"=>"status", "noofdrivers"=>"noOfDrivers");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "EmployeeBatta";
			$values = array();
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editemployeebatta?id=".$data['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editemployeebatta?id=".$data['id']);
			}
		}
		$form_info = array();
		$form_info["name"] = "editemployeebatta";
		$form_info["action"] = "editemployeebatta";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "employeebattas";
		$form_info["bredcum"] = "edit employee batta";
	
		$entity = \EmployeeBatta::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$cities =  \City::Where("status","=","ACTIVE")->get();
			$city_arr = array();
			foreach ($cities as $city){
				$city_arr[$city['id']] = $city->name;
			}
			
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","VEHICLE TYPE")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$types =  \LookupTypeValues::where("parentId","=",$parentId)->get();
			$type_arr = array();
			foreach ($types as $type){
				$type_arr [$type['id']] = $type->name;
			}
			
			$form_field = array("name"=>"sourcecity", "value"=>$entity->sourceCity, "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"destinationcity", "value"=>$entity->destinationCity, "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"vehicletype", "value"=>$entity->vehicleTypeId,  "content"=>"Vehicle Type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$type_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"driverbattaperday", "value"=>$entity->driverBatta,  "content"=>"driver batta per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"driversalaryperday", "value"=>$entity->driverSalary,  "content"=>"driver salary per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"helperbattaperday", "value"=>$entity->helperBatta,  "content"=>"helper batta per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"helpersalaryperday", "value"=>$entity->helperSalary,  "content"=>"helper salary per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"noofdrivers", "content"=>"no of drivers", "value"=>$entity->noOfDrivers,  "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"status", "value"=>$entity->status,  "content"=>"Vehicle Type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "content"=>"", "value"=>$entity->id,  "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
				
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.editform",array("form_info"=>$form_info));
		}
	}
	
		
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageEmployeeBattas()
	{
		$values = Input::all();
		$values['bredcum'] = "EMPLOYEE BATTAS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addemployeebatta';
		$values['form_action'] = 'employeebattas';
		$values['action_val'] = '#';
		
		$theads = array('Source City','Destination City', "Vehicle Type", "Driver Batta", "Driver Salary", "Helper Batta", "Helper Salary",  "No of Drivers", "status", "Actions"); //"Driver Allowance", "Helper Allowance", 
		$values["theads"] = $theads;
		
		$values["provider"] = "employeebattas";		
		
		$form_info = array();
		$form_info["name"] = "addemployeebatta";
		$form_info["action"] = "addemployeebatta";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "employeebattas";
		$form_info["bredcum"] = "add employee batta";
		
		$form_fields = array();
		
		$cities =  \City::Where("status","=","ACTIVE")->get();
		$city_arr = array();
		foreach ($cities as $city){
			$city_arr[$city['id']] = $city->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","VEHICLE TYPE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['id']] = $type->name;
		}
		
		$form_field = array("name"=>"sourcecity", "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$city_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"destinationcity", "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$city_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicletype", "content"=>"Vehicle Type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$type_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driverbattaperday", "content"=>"driver batta per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driversalaryperday", "content"=>"driver salary per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"helperbattaperday", "content"=>"helper batta per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"helpersalaryperday", "content"=>"helper salary per day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofdrivers", "content"=>"no of drivers", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;

		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
