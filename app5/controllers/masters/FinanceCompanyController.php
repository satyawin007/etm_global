<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class FinanceCompanyController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addFinanceCompany()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("companyname"=>"name","contactperson"=>"contactPerson","phoneno1"=>"phone1","phoneno2"=>"phone2","fulladdress"=>"fullAddress","cityname"=>"cityId","statename"=>"stateId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "FinanceCompany";
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("financecompanies");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("financecompanies");
			}
		}
		
		$form_info = array();
		$form_info["name"] = "addfinancecompany";
		$form_info["action"] = "addfinancecompany";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "financecompanies";
		$form_info["bredcum"] = "add finance company";
		
		$form_fields = array();
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"companyname", "content"=>"company Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"contactperson", "content"=>"contact person", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"phoneno1", "content"=>"phone no1", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"phoneno2", "content"=>"phone no2", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fulladdress", "content"=>"full address", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editFinanceCompany()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("companyname"=>"name","contactperson"=>"contactPerson","phoneno1"=>"phone1","phoneno2"=>"phone2","fulladdress"=>"fullAddress","cityname"=>"cityId","statename"=>"stateId","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "FinanceCompany";
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editfinancecompany?id=".$data['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editfinancecompany?id=".$data['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editfinancecompany?id";
		$form_info["action"] = "editfinancecompany?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "financecompanies";
		$form_info["bredcum"] = "edit finance company";
	
		$form_fields = array();
	
		$entity = \FinanceCompany::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$states =  \State::Where("status","=","ACTIVE")->get();
			$state_arr = array();
			foreach ($states as $state){
				$state_arr[$state['id']] = $state->name;
			}
			
			$cities =  \City::where("stateId","=",$entity->stateId)->get();
			$cities_arr = array();
			foreach ($cities as $city){
				$cities_arr[$city['id']] = $city->name;
			}
			
			$form_field = array("name"=>"companyname", "value"=>$entity->name, "content"=>"company Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contactperson", "value"=>$entity->contactPerson, "content"=>"contact person", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"phoneno1", "value"=>$entity->phone1, "content"=>"phone no1", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"phoneno2", "value"=>$entity->phone2, "content"=>"phone no2", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fulladdress", "value"=>$entity->fullAddress, "content"=>"full address", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statename", "value"=>$entity->stateId, "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "value"=>$entity->cityId, "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$cities_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "value"=>$entity->status, "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
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
	public function manageFinanceCompanies()
	{
		$values = Input::all();
		$values['bredcum'] = "FINANCE COMPANIES";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addfinancecompany';
		$values['form_action'] = 'financecompanies';
		$values['action_val'] = '';
		
		$theads = array('Company Name','Contact Person', "Phone1", "Phone2", "Full Address", "City Name", "State Name", "status", "Actions");
		$values["theads"] = $theads;
			
		$form_info = array();
		$form_info["name"] = "addfinancecompany";
		$form_info["action"] = "addfinancecompany";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "financecompanies";
		$form_info["bredcum"] = "add finance company";
		
		$form_fields = array();
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"companyname", "content"=>"company Name", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'FinanceCompany')"),  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"contactperson", "content"=>"contact person", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"phoneno1", "content"=>"phone no1", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"phoneno2", "content"=>"phone no2", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fulladdress", "content"=>"full address", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values['provider'] = "financecompanies";
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
