<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ServiceProviderController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addServiceProvider()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			        
			$field_names = array("provider"=>"provider","branch"=>"branchId","branch"=>"branchId","name"=>"name",
					"number"=>"number","companyname"=>"companyName","address"=>"address","referencename"=>"refName",
					"refencenumber"=>"refNumber","internetconfigurationdetails"=>"configDetails"
				);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ServiceProvider";
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("serviceproviders?provider=".$values["provider"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("serviceproviders?provider=".$values["provider"]);
			}	
		}
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editServiceProvider()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("provider1"=>"provider","branch1"=>"branchId","name1"=>"name",
					"number1"=>"number","companyname1"=>"companyName","address1"=>"address","referencename1"=>"refName",
					"referencenumber1"=>"refNumber","internetconfigurationdetails1"=>"configDetails","status1"=>"status"
				);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ServiceProvider";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("serviceproviders?provider=".$values["provider1"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("serviceproviders?provider=".$values["provider1"]);
			}
		}
	}
	
		
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageServiceProviders()
	{
		$values = Input::all();
		$values['bredcum'] = "SERVICE PROVIDERS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$theads = array('type','branch', 'name', 'number', 'companyname', 'address', 'Ref. Name', 'ref. number', "status",  "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
		$entries = $values['entries'];
		
		$select_args = array();
		$select_args[] = "serviceproviders.id as id";
		$select_args[] = "serviceproviders.provider as provider";
		$select_args[] = "serviceproviders.name as name";
		$select_args[] = "serviceproviders.number as number";
		$select_args[] = "serviceproviders.companyName as companyName";
		$select_args[] = "serviceproviders.address as address";
		$select_args[] = "serviceproviders.refName as refName";
		$select_args[] = "serviceproviders.refNumber as refNumber";
		$select_args[] = "officebranch.name as branchId";
		
		$entities = "";
		$total = 0;
		if(isset($values["provider"])){
			$entities = \ServiceProvider::where("provider", "=",$values["provider"])->leftjoin("officebranch", "officebranch.id","=","serviceproviders.branchId")->select($select_args)->paginate($entries);
			$total = \ServiceProvider::where("provider", "=",$values["provider"])->get();
			$total = count($total);
		}
		else{
			$entities = \ServiceDetails::where("id","=",0)->paginate($entries);
		}
			
		$values['entities'] = $entities;
		$values['total'] = $total;
		
		$form_info = array();
		$form_info["name"] = "addserviceprovider";
		$form_info["action"] = "addserviceprovider";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add service provider";
		
		$form_fields = array();
		
		$types_arr = array("Current"=>"Current","Mobile/Dongle"=>"Mobile/Dongle","Internet"=>"Internet","Water Cans/Tankers"=>"Water Cans/Tankers","Computer/Printer Purchases/Repairs"=>"Computer/Printer Purchases/Repairs");
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = 0;
		}
		if(isset($values["provider"]) && $values["provider"] === "Current"){		
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"name", "content"=>"name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;	
			$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;				
			$form_field = array("name"=>"companyname", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;				
			$form_field = array("name"=>"address", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;				
			$form_field = array("name"=>"referencename", "content"=>"reference name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;				
			$form_field = array("name"=>"refencenumber", "content"=>"refence number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;				
		}
		else if(isset($values["provider"]) && $values["provider"] === "Mobile/Dongle"){
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;			
		}
		else if(isset($values["provider"]) && $values["provider"] === "Phone"){
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"name", "content"=>"name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename", "content"=>"reference name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"refencenumber", "content"=>"refence number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Internet"){
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename", "content"=>"reference name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"internetconfigurationdetails", "content"=>"internet configuration details", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"refencenumber", "content"=>"refence number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Water Cans/Tankers"){
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename", "content"=>"reference name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"refencenumber", "content"=>"refence number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Computer/Printer Purchases/Repairs"){
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename", "content"=>"reference name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"refencenumber", "content"=>"refence number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else {
			$form_field = array("name"=>"provider", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$types_arr);
			$form_fields[] = $form_field;
		}
		
		$form_info["form_fields"] = $form_fields;		
		$values["form_info"] = $form_info;
		
		$form_info = array();
		$form_info["name"] = "edit";			
		$form_info["action"] = "editserviceprovider";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$form_fields = [];
		if(isset($values["provider"]) && $values["provider"] === "Current"){
			$form_field = array("name"=>"provider1", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch1", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"name1", "content"=>"name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number1", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname1", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address1", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename1", "content"=>"reference name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencenumber1", "content"=>"refence number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Mobile/Dongle"){
			$form_field = array("name"=>"provider1", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch1", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number1", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname1", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address1", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Phone"){
			$form_field = array("name"=>"provider1", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch1", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"name1", "content"=>"name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number1", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname1", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address1", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename1", "content"=>"reference name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencenumber1", "content"=>"refence number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Internet"){
			$form_field = array("name"=>"provider1", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch1", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname1", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"internetconfigurationdetails1", "content"=>"internet configuration details", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename1", "content"=>"reference name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencenumber1", "content"=>"refence number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Water Cans/Tankers"){
			$form_field = array("name"=>"provider1", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch1", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number1", "content"=>"number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname1", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address1", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename1", "content"=>"reference name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencenumber1", "content"=>"refence number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else if(isset($values["provider"]) && $values["provider"] === "Computer/Printer Purchases/Repairs"){
			$form_field = array("name"=>"provider1", "value"=>$values["provider"], "content"=>"provider", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$types_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch1", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"companyname1", "content"=>"company name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"address1", "content"=>"address", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencename1", "content"=>"reference name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"referencenumber1", "content"=>"refence number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		$form_field = array("name"=>"status1", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$values["provider"] = $values["provider"];			
		$values["modals"] = $modals;
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
