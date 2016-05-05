<?php namespace rolejobs;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class JobsController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function rolePrivileges()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$roleId = $values["roleid"];
			if(isset($values["ids"])){
				$ids = $values["ids"];
				$affectedRows = \RolePrivileges::where('roleId', '=', $values["roleid"])->delete();
				foreach($ids as $id){
					$fields = array();
					$fields["roleId"] = $roleId;
					$fields["jobId"] = $id;
					$db_functions_ctrl = new DBFunctionsController();
					$table = "RolePrivileges";
					$values = array();
					$db_functions_ctrl->insert($table, $fields);
				}
				$roleid = \Auth::user()->rolePrevilegeId;
				$privileges = \RolePrivileges::where("roleId","=",$roleid)->get();
				$privileges_arr = array();
				foreach ($privileges as $privilege){
					$privileges_arr[] = $privilege->jobId;
				}
				\Session::put("jobs",$privileges_arr);
			}
			\Session::put("message","Operation completed Successfully");
			return \Redirect::to("jobs?&id=".$roleId);
		}		
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
	public function manageJobs()
	{
		$values = array();
		$values["bredcum"] = "PRIVILAGES";
		return View::make('rolejobs.jobs', array("values"=>$values));
	}	
}
