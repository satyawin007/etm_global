<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ServiceDetailsController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addServiceDetails()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$values['active'] = "Yes"; 
			$field_names = array("sourcecity"=>"sourceCity", "destinationcity"=>"destinationCity", "description"=>"description", "serviceno"=>"serviceNo", "active"=>"active");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$fields["active"] = "Yes";
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ServiceDetails";
			
			$entities = $db_functions_ctrl->get($table, array("serviceNo"=>$values["serviceno"], "serviceStatus"=>"ACTIVE"));
			if(count($entities)>0){
				\Session::put("message","Due to existing active service, Operation Could not be completed!");
				return \Redirect::to("servicedetails");
			}
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("servicedetails");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("servicedetails");
			}	
		}		
		$form_info = array();
		$form_info["name"] = "addservicedetails";
		$form_info["action"] = "addservicedetails";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicedetails";
		$form_info["bredcum"] = "add service details";
		
		$form_fields = array();
		
		$cities =  \City::Where("status","=","ACTIVE")->get();
		$city_arr = array();
		foreach ($cities as $city){
			$city_arr[$city['id']] = $city->name;
		}
		
		$types =  \LookupTypeValues::where("type","=","VEH_TYPE")->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['id']] = $type->value;
		}
		
		$form_field = array("name"=>"sourcecity", "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"destinationcity", "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"serviceno", "content"=>"Service no", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editServiceDetails()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("sourcecity1"=>"sourceCity", "servicestatu1"=>"serviceStatus", "destinationcity1"=>"destinationCity", "description1"=>"description", "serviceno1"=>"serviceNo", "active1"=>"active", "servicestatus1"=>"serviceStatus");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ServiceDetails";
			
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("servicedetails");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("servicedetails");
			}
		}
		$form_info = array();
		$form_info["name"] = "editservicedetails";
		$form_info["action"] = "editservicedetails";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicedetails";
		$form_info["bredcum"] = "edit service details";
		
		$entity = \ServiceDetails::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$cities =  \City::Where("status","=","ACTIVE")->get();
			$city_arr = array();
			foreach ($cities as $city){
				$city_arr[$city['id']] = $city->name;
			}

			$form_field = array("name"=>"sourcecity", "value"=>$entity->sourceCity, "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"destinationcity", "value"=>$entity->destinationCity, "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"serviceno", "value"=>$entity->serviceNo,  "content"=>"Service no", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"active", "value"=>$entity->active,  "content"=>"Active", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"servicestatus", "value"=>$entity->serviceStatus, "content"=>"service status", "readonly"=>"",  "required"=>"required",  "type"=>"select", "class"=>"form-control", "options"=>array("Active"=>"Active", "Inactive"=>"Inactive"));
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
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
	public function manageServiceDetails()
	{
		$values = Input::all();
		$values['bredcum'] = "SERVICE DETAILS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addservicedetails';
		$values['form_action'] = 'servicedetails';
		$values['action_val'] = '#';
		
		$theads = array('Source City','Destination City', "Service no", "description", "status", "service status", "Actions"); 
		$values["theads"] = $theads;
		
		$form_info = array();
		$form_info["name"] = "addservicedetails";
		$form_info["action"] = "addservicedetails";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicedetails";
		$form_info["bredcum"] = "add service details";
		
		$form_fields = array();
		
		$cities =  \City::Where("status","=","ACTIVE")->get();
		$city_arr = array();
		foreach ($cities as $city){
			$city_arr[$city['id']] = $city->name;
		}		
				
		$form_field = array("name"=>"sourcecity", "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$city_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"destinationcity", "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$city_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"serviceno", "content"=>"Service no", "readonly"=>"", "action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'ServiceDetails')"), "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description", "content"=>"description", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editservicedetails";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "servicedetails";
		$form_info["bredcum"] = "edit service details";
		$modals = array(); 
		$form_fields = array();
		$form_field = array("name"=>"sourcecity1", "value"=>"", "content"=>"Source city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"destinationcity1", "value"=>"", "content"=>"destination city", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>""),  "type"=>"select", "class"=>"form-control", "options"=>$city_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"serviceno1", "value"=>"",  "content"=>"Service no", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description1", "content"=>"description", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"active1", "value"=>"",  "content"=>"Active", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"servicestatus1", "value"=>"", "content"=>"service status", "readonly"=>"",  "required"=>"required",  "type"=>"select", "class"=>"form-control", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"));
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"id1", "value"=>"",  "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values["provider"] = "services";

		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
