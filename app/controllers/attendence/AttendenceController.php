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
			$date1=date_create($values["date"]);
			$date2=date_create(date("d-m-Y"));
			$diff=date_diff($date1,$date2);
			$diff =  $diff->format("%R%a");
			if($diff>0){
				return json_encode(['status' => 'fail', 'message' => 'Attendence for PREVIOUS DATES is not allowed']);
			}
			/*$time = date('H:i:s',strtotime("12 PM"));
			if( $values["session"]=="MORNING" && date('H:i:s') > $time){
				return json_encode(['status' => 'fail', 'message' => 'Attendence for MORNING SESSION is closed']);
			}
			$time = date('H:i:s',strtotime("6 PM"));
			if( $values["session"]=="AFTERNOON" && date('H:i:s') > $time){
				return json_encode(['status' => 'fail', 'message' => 'Attendence for AFTERNOON SESSION is closed']);
			}*/
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
		if(isset($values["action"]) && $values["action"]=="update"){
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
			$qry = \AttendenceLog::where("date","=",$fields["date"]);
						if($values["employeetype"] == "CLIENT BRANCH"){
							$qry->where("depotId","=",$values["depot"])->where("clientId","=",$values["clientname"]);
						}
						else{
							$qry->where("officeBranchId","=",$values["officebranch"]);
						}
			$at_log = 	$qry->where("session","=",$values["session"])->get();
			if(count($at_log)>0){
				return json_encode(['status' => 'success', 'message' => 'Operation completed/updated Successfully']);
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
		$at_log = $db_functions_ctrl->get($table, array("session" => $values["session"],"date" => date("Y-m-d", strtotime($values["date"]))));
		if(count($at_log)>0){
			return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
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
		
		$date1=date_create($values["date"]);
		$date2=date_create(date("d-m-Y"));
		$diff=date_diff($date1,$date2);
		$diff =  $diff->format("%R%a");
		if($diff>0){
			return json_encode(['status' => 'fail', 'message' => 'Attendence for PREVIOUS DATES is not allowed']);
		}
		$time = date('H:i:s',strtotime("6 PM"));
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
					$fields["session"] = $values["session"];
					$fields["day"] = $values["day"];
					$fields["date"] = date("Y-m-d", strtotime($values["date"]));
					if($jsonitem->comments == ""){
						$fields["comments"] = $jsonitem->statuschangecomments;
					}
					if($jsonitem->empid != ""){
						$fields["empId"] = $jsonitem->empid;
						$db_functions_ctrl->insert($table, $fields);
					}
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
		
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		$depots_arr = array();
		$depots_arr["0"] = "ALL";
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		$employee_type = "";
		$office_branch = 0;
		$clientid = 0;
		$depotid = 0;
		$session = "";
		$day = "";
		$date = "";
		$holiday_reason = "";
		if(isset($values["employeetype"])){
			$employee_type = $values["employeetype"];
		}
		if(isset($values["officebranch"])){
			$office_branch = $values["officebranch"];
		}
		if(isset($values["client"])){
			$clientid = $values["client"];
			$depots = \Contract::where("clientId","=",$values["client"])
								->leftjoin("depots","depots.id","=","contracts.depotId")
								->where("contracts.status","=","ACTIVE")->get();
			foreach ($depots as $depot){
				$depots_arr[$depot['id']] = $depot['name'];
			}
		}
		if(isset($values["depot"])){
			$depotid = $values["depot"];
		}
		if(isset($values["date"])){
			$date = $values["date"];
		}
		if(isset($values["session"])){
			$session = $values["session"];
		}
		if(isset($values["day"])){
			$day = $values["day"];
		}
		if(isset($values["holidayreason"])){
			$holiday_reason = $values["holidayreason"];
		}
		
		$form_fields = array();		
		$form_field = array("name"=>"employeetype", "value"=>$employee_type, "content"=>"employee type", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"enableClientDepot(this.value);"),  "options"=>array("OFFICE"=>"OFFICE", "CLIENT BRANCH"=>"CLIENT BRANCH"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"clientname", "value"=>$clientid, "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"officebranch", "value"=>$office_branch, "content"=>"office branch", "readonly"=>"","required"=>"", "type"=>"select", "options"=>$branch_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "value"=>$depotid, "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$depots_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"session", "value"=>$session, "content"=>"session", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("MORNING"=>"MORNING","AFTERNOON"=>"AFTERNOON"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "value"=>$date, "content"=>"date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"day", "value"=>$day, "content"=>"day", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("WORKING DAY"=>"WORKING DAY","HOLIDAY"=>"HOLIDAY"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"holidayreason", "value"=>$holiday_reason, "content"=>"holiday reason", "readonly"=>"readonly",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
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
			if(isset($values['name'])){
				$url = "&name=".$values['name'];
			}
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
			if($values["depot"]=="0"){
				\DB::statement(\DB::raw("CALL contract_driver_helper_all('".$values["clientname"]."');"));
			}
			else {
				\DB::statement(\DB::raw("CALL contract_driver_helper('".$values["depot"]."', '".$values["clientname"]."');"));
			}
			$entities = \DB::select( \DB::raw("select * from temp_contract_drivers_helpers group by id"));
		}
		else{
			$entities = \Employee::whereRaw(" status='ACTIVE' and (roleId!=20 and roleId!=19) and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")
							->select($select_args)->get();
		}
		
		/*
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
						->whereNotIn("roleId",array(19,20))
						->select($select_args)->get();
		}
		*/
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
	
