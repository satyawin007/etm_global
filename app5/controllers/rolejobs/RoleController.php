<?php namespace rolejobs;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class RoleController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addRole()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("rolename"=>"roleName","description"=>"description");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Role";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("roles");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("roles");
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
	public function editRole()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("rolename1"=>"roleName","description1"=>"description", "statecode1"=>"code","status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Role";
			$values = array();
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("roles");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("roles");
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
	public function manageRoles()
	{
		$values = Input::all();
		$values['bredcum'] = "ROLES";
		$values['home_url'] = 'roles';
		$values['add_url'] = 'roles';
		$values['form_action'] = 'roles';
		$values['action_val'] = '#';
		$theads = array('Role ID','role Name', "description", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editrole?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "addrole";
		$form_info["action"] = "addrole";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "roles";
		$form_info["bredcum"] = "add role";
		
		$form_fields = array();		
		$form_field = array("name"=>"rolename", "content"=>"role name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editrole";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "roles";
		$form_info["bredcum"] = "add role";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"rolename1", "value"=>"", "content"=>"role name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description1", "value"=>"", "content"=>"description", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "roles";

		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
