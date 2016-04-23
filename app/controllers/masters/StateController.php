<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class StateController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addState()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("statename"=>"name","statecode"=>"code");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "State";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("states");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("states");
			}	
		}		
		$form_info = array();
		$form_info["name"] = "addstate";
		$form_info["action"] = "addstate";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
		
		$form_fields = array();
		
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statecode", "content"=>"state code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editState()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("statename1"=>"name","statecode1"=>"code","status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\State";
			$values = array();
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("states");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("states");
			}
		}
		$form_info = array();
		$form_info["name"] = "editstate";
		$form_info["action"] = "editstate";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "edit state";
	
		$entity = \State::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$form_fields = array();	
			$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"", "value"=>$entity->name,  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statecode", "content"=>"state code", "readonly"=>"",  "value"=>$entity->code, "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "content"=>"", "readonly"=>"",  "value"=>$entity->id, "required"=>"","type"=>"hidden", "value"=>$entity->id, "class"=>"form-control");
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
	public function manageStates()
	{
		$values = Input::all();
		$values['bredcum'] = "STATES";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addstate';
		$values['form_action'] = 'states';
		$values['action_val'] = '#';
		$theads = array('State ID','State Name', "State Code", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editstate?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "addstate";
		$form_info["action"] = "addstate";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
		
		$form_fields = array();		
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statecode", "content"=>"state code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editstate";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"statename1", "value"=>"", "content"=>"state name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statecode1", "value"=>"", "content"=>"state code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "states";

		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
