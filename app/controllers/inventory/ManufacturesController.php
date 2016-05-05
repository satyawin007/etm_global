<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ManufacturesController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addManufacturer()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("name"=>"name","description"=>"description");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Manufacturers";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("manufacturers");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("manufacturers");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editManufacturer()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("name1"=>"name","description1"=>"description","status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Manufacturers";
			$values = array();
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("manufacturers");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("manufacturers");
			}
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageManufacturers()
	{
		$values = Input::all();
		$values['bredcum'] = "MANUFACTURERS";
		$values['home_url'] = '#';
		$values['add_url'] = 'addmanufacturer';
		$values['form_action'] = 'manufactures';
		$values['action_val'] = '#';
		$theads = array(' ID','Name', "Description", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editmanufacturer?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "addmanufacturer";
		$form_info["action"] = "addmanufacturer";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "manufactures";
		$form_info["bredcum"] = "add manufacturer";
		
		$form_fields = array();		
		$form_field = array("name"=>"name", "content"=>"manufacturer name", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'Manufacturers')"),  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editmanufacturer";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "manufactures";
		$form_info["bredcum"] = "add manufacturer";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"name1", "content"=>"manufacturer name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description1", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "manufacturers";

		return View::make('inventory.lookupdatatable', array("values"=>$values));
	}	
}
