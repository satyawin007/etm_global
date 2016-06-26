<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class LookupValueController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addLookupValue()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			if(isset($values["parentvalue"])){
				$field_names = array("parentvalue"=>"name","parentremarks"=>"remarks");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						$fields[$val] = $values[$key];
					}				
				}
				$entities = \LookupTypeValues::where("name", "=", $values['parentvalue'])->get();						
				if(count($entities)>0){
					\Session::put("message","data already existed!, try with different data.");
					return \Redirect::to("lookupvalues");
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "LookupTypeValues";
				if($db_functions_ctrl->insert($table, $fields)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("lookupvalues");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("lookupvalues");
				}	
			}
			$field_names = array("type"=>"parentId","value"=>"name", "modules"=>"modules", "showfields"=>"fields", "enabled"=>"enabled","remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key==="modules" || $key==="showfields"){
						$data = "";
						foreach($values[$key] as $val1){
							$data = $data.",".$val1;
						}
						$data = substr($data, 1);
						$fields[$val] = $data;
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$entities = \LookupTypeValues::where("parentId", "=", $values['type'])->where("name","=",$values['value'])->get();
			if(count($entities)>0){
				\Session::put("message","data already existed!, try with different data.");
				return \Redirect::to("lookupvalues?type=".$values["type"]);
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "LookupTypeValues";
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("lookupvalues?type=".$values["type"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("lookupvalues?type=".$values["type"]);
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
	public function editLookupValue()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["SDf"];
			$field_names = array("type1"=>"parentId","value1"=>"name", "modules1"=>"modules", "showfields1"=>"fields", "is_Enabled"=>"enabled","remarks1"=>"remarks","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key==="modules1" || $key==="showfields1"){
						$data = "";
						foreach($values[$key] as $val1){
							$data = $data.",".$val1;
						}
						$data = substr($data, 1);
						$fields[$val] = $data;
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			if(!isset($values["modules1"])){
				$fields["modules"] = "";
			}
			if(!isset($values["showfields1"])){
				$fields["fields"]="";
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\LookupTypeValues";
			
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				$entity = \LookupTypeValues::where("id", "=", $values['id1'])->get();
				$entity  = $entity[0];
				return \Redirect::to("lookupvalues?type=".$entity->parentId);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				$entity = \LookupTypeValues::where("id", "=", $values['id1'])->get();
				$entity  = $entity[0];
				return \Redirect::to("lookupvalues?type=".$entity->parentId);
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
	public function manageLookupValues()
	{
		$values = Input::all();
		$values['bredcum'] = "LOOKUP VALUES";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$theads = array('name', "type", "remarks", "Show in modules", "Fields", "enabled", "status", "Actions");
		$values["theads"] = $theads;
				
		$form_info = array();
		$form_info["name"] = "addlookupvalue";
		$form_info["action"] = "addlookupvalue";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add lookup value";
		$form_info["addlink"] = "addparent";
		
		$form_fields = array();
		
		$types =  \LookupTypeValues::where("parentId", "=", 0)->get();
		$types_arr = array();
		foreach ($types as $type){
			$types_arr[$type->id] = $type->name;
		}
		$val = "";
		if(!isset($values["type"])){
			$values["type"] = "-1";
		}
		
		$form_field = array("name"=>"type", "value"=>$values["type"], "content"=>"type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$types_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"value", "content"=>"value", "readonly"=>"", "action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'LookupTypeValues')"), "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"modules", "content"=>"show in modules", "readonly"=>"",  "required"=>"","type"=>"checkbox", "options"=>array("incharge"=>"YES", "vehicle"=>"YES", "branch"=>"YES", "employee"=>"YES"), "class"=>"form-control");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"modules[]", "content"=>"show in modules", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "options"=>array("DAILY TRIPS"=>"DAILY TRIPS","LOCAL TRIPS"=>"LOCAL TRIPS"));
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"enabled",  "value"=>"NO", "content"=>"is Enabled", "readonly"=>"",  "required"=>"","type"=>"radio", "options"=>array("NO"=>" NO","YES"=>" YES"), "class"=>"form-control");
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"showvalues", "content"=>"show Fields", "readonly"=>"",  "required"=>"","type"=>"checkbox", "options"=>array("incharge"=>"YES", "vehicle"=>"YES", "branch"=>"YES", "employee"=>"YES"), "class"=>"form-control");
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"showfields[]", "content"=>"show fields", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "options"=>array("INCHARGE"=>"INCHARGE","VEHICLE"=>"VEHICLE","BANK"=>"BANK","EMPLOYEE"=>"EMPLOYEE","BRANCH"=>"BRANCH","NEXT ALERT DATE"=>"NEXT ALERT DATE"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"additional information", "readonly"=>"",  "required"=>"required","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		
		$values["form_info"] = $form_info;
		
		$form_info = array();
		$form_info["name"] = "edit";
			
		$form_info["action"] = "editlookupvalue";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$form_fields = array();
		$form_field = array("name"=>"id1", "content"=>"Lookup ID", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"value1", "content"=>"value", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"modules", "content"=>"show in modules", "readonly"=>"",  "required"=>"","type"=>"checkbox", "options"=>array("incharge"=>"YES", "vehicle"=>"YES", "branch"=>"YES", "employee"=>"YES"), "class"=>"form-control");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"modules1[]", "id"=>"modules1", "content"=>"show in modules", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "options"=>array("DAILY TRIPS"=>"DAILY TRIPS","LOCAL TRIPS"=>"LOCAL TRIPS"));
// 		$form_fields[] = $form_field;		
// 		$form_field = array("name"=>"showvalues", "content"=>"show Fields", "readonly"=>"",  "required"=>"","type"=>"checkbox", "options"=>array("incharge"=>"YES", "vehicle"=>"YES", "branch"=>"YES", "employee"=>"YES"), "class"=>"form-control");
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"showfields1[]", "id"=>"showfields1", "content"=>"show fields", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "options"=>array("INCHARGE"=>"INCHARGE","VEHICLE"=>"VEHICLE","BANK"=>"BANK","EMPLOYEE"=>"EMPLOYEE","BRANCH"=>"BRANCH","NEXT ALERT DATE"=>"NEXT ALERT DATE"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "content"=>"additional information", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enabled1",  "value"=>"NO", "content"=>"is Enabled", "readonly"=>"",  "required"=>"","type"=>"radio", "options"=>array("NO"=>"NO","YES"=>"YES"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"radio", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;		
		$modals[] = $form_info;
		
		$form_info = array();
		$form_info["name"] = "addparent";
			
		$form_info["action"] = "addlookupvalue";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$form_fields = array();
		$form_field = array("name"=>"parentvalue", "content"=>"parent Lookup value", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"parent", "value"=>"yes", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hiddent", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"parentremarks", "content"=>"Additional Info", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		
		$values["provider"] = "lookupvalues&type=".$values["type"];
		
		$values["modals"] = $modals;
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
