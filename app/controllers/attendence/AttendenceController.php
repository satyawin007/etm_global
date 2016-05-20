<?php namespace attendence;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
class AttendenceController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function transactionsWorkFlow(){
		$values = Input::all();
		if(isset($values["type"]) && $values["type"]=="fueltransactions"){
			return $this->fuelTransactionsWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="repairtransactions"){
			return $this->repairTransactionsWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="inchargetransactions"){
			return $this->inchargeTransactionsWorkFlow($values);
		}
	}
	

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageAttendence()
	{
		$values = Input::all();
		$values['bredcum'] = "ATTENDENCE";
		$values['home_url'] = '#';
		$values['add_url'] = 'additem';
		$values['form_action'] = 'attendence';
		$values['action_val'] = '#';
			
		$actions = array();
		$action = array("url"=>"edititem?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "attendence";
		$form_info["action"] = "attendence";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "add item";
		
		$branch_arr = array();
		$branches = \OfficeBranch::where("status","=","ACTIVE")->get();
		foreach ($branches as $branch){
			$branch_arr[$branch->id] = $branch->name;
		}
		
		
		$form_fields = array();		
		$form_field = array("name"=>"employeetype", "content"=>"employee type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("OFFICE"=>"OFFICE", "NON-OFFICE"=>"NON-OFFICE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"officebranch", "content"=>"office branch", "readonly"=>"","required"=>"", "type"=>"select", "options"=>$branch_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"session", "content"=>"session", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("MORNING"=>"MORNING","AFTERNOON"=>"AFTERNOON"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"day", "content"=>"day", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("WORKING DAY"=>"WORKING DAY","HOLIDAY"=>"HOLIDAY"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofpresents", "content"=>"no of presents", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofabsents", "content"=>"no of absents", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$modals = array();
		$values["modals"] = $modals;
		
		$values['provider'] = "items";
		return View::make('attendence.lookupdatatable', array("values"=>$values));
	}
	
	public function workFlowUpdate(){
		$values = Input::all();
		$json_data = array();
		$json_data["status"] = "fail";
		$json_data["message"] = "operation could not be completed";
		if(isset($values["transactiontype"]) && isset($values["table"])){
			if(isset($values["action"])){
				$update_dt = array("workFlowStatus"=>$values["workflowstatus"], "workFlowRemarks"=>$values["remarks"]);
				$table = $values["table"];
				foreach($values["action"] as $rec){
					$table::where("id","=",$rec)->update($update_dt);
				}
				$json_data["status"] = "success";
				$json_data["message"] = "operation completed successfully";
			}
		}
		echo json_encode($json_data);
	}
}
	
