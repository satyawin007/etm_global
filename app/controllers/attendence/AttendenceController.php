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
	public function addAttendence()
	{
		
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["test"];
			$time = date('H:i:s',strtotime("12 PM"));
			if( $values["session"]=="MORNING" && date('H:i:s') > $time){
				return json_encode(['status' => 'fail', 'message' => 'Attendence for MORNING SESSION is closed']);
			}
			$time = date('H:i:s',strtotime("6 PM"));
			if( $values["session"]=="AFTERNOON" && date('H:i:s') > $time){
				return json_encode(['status' => 'fail', 'message' => 'Attendence for AFTERNOON SESSION is closed']);
			}
			$success = true;
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Attendence"; 
			$jsonitems = json_decode($values["jsondata"]);
			foreach ($jsonitems as $jsonitem){
				$success = false;
				$fields = array();
				$fields["session"] = $values["session"];
				$fields["day"] = $values["day"];
				$fields["holidayReason"] = $values["holidayreason"];
				$fields["date"] = date("Y-m-d", strtotime($values["date"]));
				if($jsonitem->empid != ""){
					$fields["empId"] = $jsonitem->empid;
				}
				if($jsonitem->Substitute != ""){
					$fields["substituteId"] = $jsonitem->Substitute;
				}
				if($jsonitem->comments != ""){
					$fields["comments"] = $jsonitem->comments;
				}
				$cnt = \Attendence::where("empId","=",$jsonitem->empid)->where("session","=",$values["session"])->where("date","=",date("Y-m-d", strtotime($values["date"])))->count();
				if($cnt==0){
					$db_functions_ctrl->insert($table, $fields);
				}
				$success = true;
			}
			if($success){
				return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
			}
			else{
				return json_encode(['status' => 'fail', 'message' => 'Operation Could not be completed, Try Again!']);
			}
		}
	}
	
	public function addAttendenceLog()
	{
		$values = Input::all();
		//$values["test"];
		
		$time = date('H:i:s',strtotime("12 PM"));
		if( $values["session"]=="MORNING" && date('H:i:s') > $time){
			return json_encode(['status' => 'fail', 'message' => 'Attendence for MORNING SESSION is closed']);
		}
		$time = date('H:i:s',strtotime("6 PM"));
		if( $values["session"]=="AFTERNOON" && date('H:i:s') > $time){
			return json_encode(['status' => 'fail', 'message' => 'Attendence for AFTERNOON SESSION is closed']);
		}
		
		$success = true;
		$db_functions_ctrl = new DBFunctionsController();
		$table = "\AttendenceLog";
		$jsonitems = json_decode($values["jsondata"]);
		$success = false;
		$fields = array();
		$fields["session"] = $values["session"];
		$fields["day"] = $values["day"];
		$fields["date"] = date("Y-m-d", strtotime($values["date"]));
		$fields["time"] = date("H:i:s");
		$fields["holidayReason"] = $values["holidayreason"];
		if($values["employeetype"] == "CLIENT BRANCH"){
			$fields["clientId"] = $values["clientname"];
			$fields["depotId"] = $values["depot"];
		}
		else{
			$fields["officeBranchId"] = $values["officebranch"];
		}
		$db_functions_ctrl->insert($table, $fields);
		$success = true;
		if($success){
			return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
		}
		else{
			return json_encode(['status' => 'fail', 'message' => 'Operation Could not be completed, Try Again!']);
		}
	}
	
	public function getAttendenceLog()
	{
		$values = Input::all();
		
		$time = date('H:i:s',strtotime("12 PM"));
		if( $values["session"]=="MORNING" && date('H:i:s') > $time){
			return json_encode(['status' => 'fail', 'message' => 'Attendence for MORNING SESSION is closed']);
		}
		$time = date('H:i:s',strtotime("6 PM"));
		if( $values["session"]=="AFTERNOON" && date('H:i:s') > $time){
			return json_encode(['status' => 'fail', 'message' => 'Attendence for AFTERNOON SESSION is closed']);
		}
		
		$fields = array();
		$fields["session"] = $values["session"];
		$fields["day"] = $values["day"];
		$fields["date"] = date("Y-m-d", strtotime($values["date"]));
		$fields["time"] = date("H:i:s");
		if($values["employeetype"] == "CLIENT BRANCH"){
			$fields["clientId"] = $values["clientname"];
			$fields["depotId"] = $values["depot"];
		}
		else{
			$fields["officeBranchId"] = $values["officebranch"];
		}
		$qry = \AttendenceLog::where("day","=",$values["day"])
					->where("session","=",$values["session"])
					->where("date","=",date("Y-m-d", strtotime($values["date"])));
					if($values["employeetype"] == "CLIENT BRANCH"){
						$qry->where("clientId","=",$values["clientname"]);
						$qry->where("depotId","=",$values["depot"]);
					}
					else{
						$qry->where("officeBranchId","=",$values["officebranch"]);
					}
		$cnt = $qry->count();
		return json_encode(['status' => 'success', 'rec_count' => $cnt]);
	}
	
	public function updateAttendence()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["test"];
			$success = true;
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Attendence";
			$jsonitems = json_decode($values["jsondata"]);
			foreach ($jsonitems as $jsonitem){
				$success = false;
				$fields = array();
// 				$fields["session"] = $values["session"];
// 				$fields["day"] = $values["day"];
// 				$fields["date"] = date("Y-m-d", strtotime($values["date"]));
// 				if($jsonitem->empid != ""){
// 					$fields["empId"] = $jsonitem->empid;
// 				}
				if($jsonitem->Substitute != ""){
					$fields["substituteId"] = $jsonitem->Substitute;
				}
				if($jsonitem->comments != ""){
					$fields["comments"] = $jsonitem->comments;
				}
				if($jsonitem->attendence_status != ""){
					$fields["attendenceStatus"] = $jsonitem->attendence_status;
				}
				if($jsonitem->statuschangecomments != ""){
					$fields["attendenceStatusComments"] = $jsonitem->statuschangecomments;
				}
				$cnt = \Attendence::where("id","=",$jsonitem->recid)->update($fields);
				if($cnt==0){
					$db_functions_ctrl->insert($table, $fields);
				}
				$success = true;
			}
			if($success){
				return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
			}
			else{
				return json_encode(['status' => 'fail', 'message' => 'Operation Could not be completed, Try Again!']);
			}
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
		$form_info["action"] = "addattendence";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "add item";
		
		$branch_arr = array();
		$branches = \OfficeBranch::where("status","=","ACTIVE")->get();
		foreach ($branches as $branch){
			$branch_arr[$branch->id] = $branch->name;
		}
		
		$clients =  \Client::all();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		
		
		$form_fields = array();		
		$form_field = array("name"=>"employeetype", "content"=>"employee type", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"enableClientDepot(this.value);"),  "options"=>array("OFFICE"=>"OFFICE", "CLIENT BRANCH"=>"CLIENT BRANCH"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"officebranch", "content"=>"office branch", "readonly"=>"","required"=>"", "type"=>"select", "options"=>$branch_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"session", "content"=>"session", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("MORNING"=>"MORNING","AFTERNOON"=>"AFTERNOON"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"day", "content"=>"day", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("WORKING DAY"=>"WORKING DAY","HOLIDAY"=>"HOLIDAY"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"holidayreason", "content"=>"holiday reason", "readonly"=>"readonly",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofpresents", "content"=>"no of presents", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofabsents", "content"=>"no of absents", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"jsondata", "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden","value"=>"", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$modals = array();
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "edit";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "add item";
		$form_fields = array();
		$form_field = array("name"=>"officebranch", "content"=>"office branch", "readonly"=>"","required"=>"", "type"=>"select", "options"=>$branch_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals['form_info'] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "items";
		if(isset($values['employeetype']) && isset($values['officebranch']) && isset($values['client']) && isset($values['depot']) && isset($values['date'])){
			$url = "&name=getattendence";
			$url = $url."&employeetype=".$values["employeetype"];
			$url = $url."&officebranch=".$values["officebranch"];
			$url = $url."&client=".$values["client"];
			$url = $url."&depot=".$values["depot"];
			$url = $url."&date=".$values["date"];
			$url = $url."&session=".$values["session"];
			$url = $url."&day=".$values["day"];
			$values['provider'] = $url;
			$month = date("m",strtotime($values["date"]));
			$year = date("Y",strtotime($values["date"]));
			$values["startdate"] = date("d-m-Y",strtotime("01"."-".$month."-".$year));
		}
		return View::make('attendence.lookupdatatable', array("values"=>$values));
	}
	
	public function getDayTotalAttendence(){
		$values = Input::All();
		$select_args = array("employee.id", "employee.fullName", "employee.empCode");
		if($values["employeetype"] == "CLIENT BRANCH"){
			$entities = \ContractVehicle::where("contract_vehicles.status", "=","ACTIVE")
						->where("contracts.clientId","=",$values["clientname"])
						->where("contracts.depotId","=",$values["depot"])
						->join("contracts", "contract_vehicles.contractId", "=", "contracts.id")
						->join("employee", "contract_vehicles.driver1Id", "=", "employee.id")
						->select($select_args)->get();
		}
		else{
			$entities = \Employee::where("officeBranchId", "=",$values["officebranch"])
						->select($select_args)->get();
		}
		$emp_arr = array();
		foreach($entities as $entity){
			$emp_arr[] =  $entity->id;
		}
		$abs_emps_cnt  = \Attendence::where("date","=",date("Y-m-d", strtotime($values["date"])))
					->whereIn("empId",$emp_arr)
					->where("session","=",$values["session"])
					->where("day","=",$values["day"])
					->where("attendenceStatus","!=","P")
					->count();
		$tot_presents = count($emp_arr) - $abs_emps_cnt;
		
		echo json_encode(array("noofpresents"=>$tot_presents, "noofabsents"=>$abs_emps_cnt));
	}
}
	
