<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
class EmployeeController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}
	
	public function manageEmployees(){
		$values = Input::all();
		if(isset($values['edit']) && isset($values['empid'])){
			return View::make('masters.editemployee', array("values"=>$values));			
		}
		else if(isset($values['terminate']) && isset($values['empid'])){
			return View::make('masters.editemployee', array("values"=>$values));
		}
		else {
			if(!isset($values['action'])){
				$values['action'] = "all";
			}
			$jobs = \Session::get("jobs");
			$values['bredcum'] = "EMPLOYEES";
			$values['home_url'] = 'masters';
			$values['add_url'] = "#";
			if(in_array(201, $jobs)){
				$values['add_url'] = 'addemployee';
			}
			$values['form_action']= 'employees';
		
			$select = array();
			$select['name'] = "branch";
			
			$branches = \OfficeBranch::all();
			$branch_arr = array();
			$branch_arr[""] = "ALL";
			foreach ($branches as $branch){
				$branch_arr[$branch->id] = $branch->name; 
			}
			$select['options'] = $branch_arr;
			$selects = array();
			$selects[] = $select;
			$values["selects"] = $selects;
			
			if(!isset($values['entries'])){
				$values['entries'] = 10;
			}
			if(!isset($values['page'])){
				$values['page'] = 1;
			}
			$action_val = ""; 
			$links = array();
			if(isset($values['action']) && $values['action']=="driver_helpers") {
				$url = "employees?action=driver_helpers";
				$url = $url."&page=".$values['page'];
				$link = array("url"=>$url, "name"=>"Load Drivers/Helpers");
				$action_val = "driver_helpers";
				$links[] = $link;
			}
			else{
				$link = array("url"=>"employees?action=driver_helpers", "name"=>"Load Drivers/Helpers");
				$links[] = $link;
			}
			
			if(isset($values['action']) && $values['action']=="blocked") {
				$url = "employees?action=blocked";
				$url = $url."&page=".$values['page'];
				$link = array("url"=>$url, "name"=>"Load Blocked Employees");
				$action_val = "blocked";
				$links[] = $link;
			}
			else{
				$link = array("url"=>"employees?action=blocked", "name"=>"Load Blocked Employees");
				$links[] = $link;
			}
			
			if(isset($values['action']) && $values['action']=="terminated") {
				$url = "employees?action=terminated";
				$url = $url."&page=".$values['page'];
				$link = array("url"=>$url, "name"=>"Load Terminated Employees");
				$action_val = "terminated";
				$links[] = $link;
			}
			else{
				$link = array("url"=>"employees?action=terminated", "name"=>"Load Terminated Employees");
				$links[] = $link;
			}
			if(isset($values['action']) && $values['action']=="all") {
				$url = "employees?action=all";
				$url = $url."&page=".$values['page'];
				$link = array("url"=>$url, "name"=>"Office Employees");
				$action_val = "all";
				$links[] = $link;
			}
			else{
				$link = array("url"=>"employees?action=all", "name"=>"Office Employees");
				$links[] = $link;
			}
			$values['action_val'] = $action_val;
			$values['links'] = $links;	
			$values['entities'] = array();
			
			$theads = array('EmployeeID','Employee Name', "Branch", "MobileNuber", "Designation", "Email", "Attachments", "Family Members","Profile", "Actions");
			$values["theads"] = $theads;
			
			$tds = array('empCode','fullName', "officeBranchName", "mobileNo", "name", "emailid", "proofs", "fatherName","status");
			$values["tds"] = $tds;
			
			$actions = array();
			if(in_array(202, $jobs)){
				$action = array("url"=>"editsalarydetails?","css"=>"success", "type"=>"", "text"=>"salary Add/Edit");
				$actions[] = $action; 
			}
			if(in_array(203, $jobs)){
				$action = array("url"=>"employeeprofile?","css"=>"primary", "type"=>"", "text"=>"Edit");
				$actions[] = $action;
			}
			if(in_array(204, $jobs)){
				if(isset($values['action']) && $values['action']=="terminated") {
					$action = array("url"=>"#terminate", "type"=>"modal", "css"=>"inverse", "js"=>"modalTerminateEmployee(", "jsdata"=>array("id","fullName","empCode"), "text"=>"Unterminate");
				}
				else{
					$action = array("url"=>"#terminate", "type"=>"modal", "css"=>"inverse", "js"=>"modalTerminateEmployee(", "jsdata"=>array("id","fullName","empCode"), "text"=>"terminate");
				}
				$actions[] = $action;
			}
			if(in_array(205, $jobs)){
				if(isset($values['action']) && $values['action']=="blocked") {
					$action = array("url"=>"#block", "type"=>"modal", "css"=>"purple", "js"=>"modalBlockEmployee(", "jsdata"=>array("id","fullName","empCode"),  "text"=>"Unblock");
				}
				else{
					$action = array("url"=>"#block", "type"=>"modal", "css"=>"purple", "js"=>"modalBlockEmployee(", "jsdata"=>array("id","fullName","empCode"),  "text"=>"block");
				}
				$actions[] = $action;
			}
			$values["actions"] = $actions;
			
			$entries = $values['entries'];
			$total = 0;
			
			$select_args = array();
			$select_args[] = "officebranch.name as officeBranchName";
			$select_args[] = "employee.empCode as empCode";
			$select_args[] = "employee.id as id";
			$select_args[] = "employee.mobileNo as mobileNo";
			$select_args[] = "user_roles_master.name as name";
			$select_args[] = "employee.emailid as emailid";
			$select_args[] = "employee.proofs as proofs";
			$select_args[] = "employee.fatherName as fatherName";
			$select_args[] = "employee.status as status";
			$select_args[] = "employee.fullName as fullName";
			
			$values['provider'] = "employees&action=";
			if(isset($values['action']) && $values['action']=="driver_helpers"){
				$values["provider"] = $values["provider"]."driver_helpers";
				if(isset($values['branch']) && $values['branch'] != ""){
					$values["provider"] = $values["provider"]."&branch=".$values['branch'];
					/*$entities = \Employee::leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->where('officeBranchId',"=",$values['branch'])->where('roleId',"=",20)->orwhere("roleId", "=",19)->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->select($select_args)->paginate($entries);
					$total = \Employee::leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->where('officeBranchId',"=",$values['branch'])->where('roleId',"=",20)->orwhere("roleId", "=",19)->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->select($select_args)->get();
					$total = count($total);
					*/
				}
			}
			else if(isset($values['action']) && $values['action']=="blocked"){
				$values["provider"] = $values["provider"]."blocked";
				if(isset($values['branch']) && $values['branch'] != ""){
					$values["provider"] = $values["provider"]."&branch=".$values['branch'];
					$entities = \Employee::where('officeBranchId',"=",$values['branch'])->where('status',"=","BLOCKED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->paginate($entries);
					$total = \Employee::where('officeBranchId',"=",$values['branch'])->where('status',"=","BLOCKED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->get();
					$total = count($total);
				}
				else {
					$entities = \Employee::where('status',"=","BLOCKED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->paginate($entries);
					$total = \Employee::where('status',"=","BLOCKED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->get();
					$total = count($total);
				}
			}
			else if(isset($values['action']) && $values['action']=="terminated"){
				$values["provider"] = $values["provider"]."terminated";
				if(isset($values['branch']) && $values['branch'] != ""){
					$values["provider"] = $values["provider"]."&branch=".$values['branch'];
					$entities = \Employee::where('officeBranchId',"=",$values['branch'])->where('status',"=","TERMINATED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->paginate($entries);
					$total = \Employee::where('officeBranchId',"=",$values['branch'])->where('status',"=","TERMINATED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->get();
					$total = count($total);
				}
				else {
					$entities = \Employee::where('status',"=","TERMINATED")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->paginate($entries);
					$total = \Employee::where('status',"=","TERMINATED")->get();
					$total = count($total);
				}
			}
			else if(isset($values['action']) && $values['action']=="all"){
				$values["provider"] = $values["provider"]."all";
				if(isset($values['branch']) && $values['branch'] != ""){
					$values["provider"] = $values["provider"]."&branch=".$values['branch'];
					$entities = \Employee::where('officeBranchId',"=",$values['branch'])->where('roleId',"!=",20)->where("roleId", "!=",19)->where("status", "=","Active")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->paginate($entries);
					$total = \Employee::where('officeBranchId',"=",$values['branch'])->where('roleId',"!=",20)->where("roleId", "!=",19)->where("status", "=","Active")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->get();
					$total = count($total);
				}
				else {
					$entities = \Employee::where('roleId',"!=",20)->where("roleId", "!=",19)->where("status", "=","Active")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->select($select_args)->paginate($entries);
					$total = \Employee::where('roleId',"!=",20)->where("roleId", "!=",19)->where("status", "=","Active")->leftjoin('user_roles_master','employee.roleId','=','user_roles_master.id')->leftjoin('officebranch','employee.officeBranchId','=','officebranch.id')->get();
					$total = count($total);					
				}
			}
			
			
			//Code to add modal forms
			$modals =  array();
			
			$form_info = array();
			$form_info["name"] = "terminate";
			$form_info["action"] = "terminateemployee";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			
			$form_fields = array();
			$form_field = array("name"=>"empname", "content"=>"emp name", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
			$form_fields[] = $form_field;				
			$form_field = array("name"=>"empid", "content"=>"emp id", "readonly"=>"readonly", "required"=>"required","type"=>"text",  "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"termination_date", "content"=>"termination date", "readonly"=>"", "required"=>"required", 	"type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "readonly"=>"", "content"=>"remarks", "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			$modals[] = $form_info;
			
			$form_info = array();
			$form_info["name"] = "block";
			
			$form_info["action"] = "blockemployee";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
				
			$form_fields = array();
			$form_field = array("name"=>"empname1", "content"=>"emp name", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"empid1", "content"=>"emp id", "readonly"=>"readonly", "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
			$form_fields[] = $form_field;				
			$form_field = array("name"=>"remarks", "readonly"=>"", "content"=>"remarks", "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			$modals[] = $form_info;
			
			$values["modals"] = $modals;
				
			return View::make('masters.layouts.employeedatatable', array("values"=>$values));
		}
	}
	

	/**
	 * Terminate an employee.
	 *
	 * @return Response
	 */
	public function terminateEmployee()
	{
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$table = "Employee";
		$fields = array();
		$dt = date('Y-m-d', strtotime($values['termination_date']));
		$emp = \Employee::where("id", "=", $values["id"])->get();
		$data = "";
		$isTerminated = false;
		if(count($emp)>0){
			$emp = $emp[0];
			if($emp->status == "TERMINATED"){
				$data = array("id"=>$values["id"]);
				$fields = array( "status"=>"ACTIVE","terminationDate"=>$dt);
				$isTerminated = true;
			}
			else{
				$data = array("id"=>$values["id"]);
				$fields = array( "status"=>"TERMINATED","terminationDate"=>$dt);
			}
		}
		$values = array();
		if($db_functions_ctrl->update($table, $fields, $data)){
			if($isTerminated){
				\Session::put("message","Employee Unterminated Successfully");
				return Redirect::to("employees");
			}
			else{
				\Session::put("message","Employee Terminated Successfully");
				return Redirect::to("employees");
			}
		}
		else{
			\Session::put("message","Operation could not be completed, Try Again!");
			return Redirect::to("employees");
		}
	}


	/**
	 * Terminate an employee.
	 *
	 * @return Response
	 */
	public function blockEmployee()
	{
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$table = "Employee";
		$fields = array();
		$emp = \Employee::where("id", "=", $values["id1"])->get();
		$data = "";
		$isBlocked = false;
		if(count($emp)>0){
			$emp = $emp[0];
			if($emp->status == "BLOCKED"){
				$data = array("id"=>$values["id1"]);
				$fields = array( "status"=>"ACTIVE");
				$isBlocked = true;
			}
			else{
				$data = array("id"=>$values["id1"]);
				$fields = array( "status"=>"BLOCKED");
			}
		}
		$values = array();
		if($db_functions_ctrl->update($table, $fields, $data)){
			if($isBlocked){
				\Session::put("message","Employee Unblocked Successfully");
				return Redirect::to("employees");
			}
			else{
				\Session::put("message","Employee Blocked Successfully");
				return Redirect::to("employees");
			}
		}
		else{
			\Session::put("message","Operation could not be completed, Try Again!");
			return Redirect::to("employees");
		}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function addEmployee()
	{
		$values = Input::all();
		$field_names = array("fullname"=>"fullName","gender"=>"gender", "city"=>"cityId","employeeid"=>"empCode",
				"email"=>"emailId","password"=>"password", "roleprevilage"=>"rolePrevilegeId",
				"workgroup"=>"workGroup","age"=>"age", "fathername"=>"fatherName",
				"religion"=>"religion","residance"=>"residance", "nonlocaldetails"=>"detailsForNonLocal",
				"phonenumber"=>"mobileNo","homenumber"=>"homePhoneNo", "idproof"=>"idCardName",
				"idproofnumber"=>"idCardNumber","joiningdate"=>"joiningDate", "rtaoffice"=>"rtaBranch",
				"aadhdaarnumber"=>"aadharNumber","rationcardnumber"=>"rationCardNumber", "drivinglicence"=>"drivingLicence",
				"drivingliceneexpiredate"=>"drvLicenceExpDate","accountnumber"=>"accountNumber", "bankname"=>"bankName",
				"ifsccode"=>"ifscCode","branchname"=>"branchName", "presentaddress"=>"presentAddress","dateofbirth"=>"dob"
			);
		$fields = array();
		foreach ($field_names as $key=>$val){
			if(isset($values[$key])){
				if($val == "dob" || $val == "drvLicenceExpDate" || $val == "joiningDate"){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if($val == "password"){
					$fields[$val] = \Hash::make($values[$key]);
				}
				else {
					$fields[$val] = $values[$key];
				}
			}
		}
		$fields["roleId"] = $fields["rolePrevilegeId"];
		$entity = new \Employee();
		foreach($fields as $key=>$value){
			$entity[$key] = $value;
		}
		
		$db_functions_ctrl = new DBFunctionsController();
		$table = "Employee";
		$values = array();
		if($db_functions_ctrl->insert($table, $fields)){
			\Session::put("message","Operation completed Successfully");
			return \Redirect::to("addemployee");
		}
		else{
			\Session::put("message","Operation Could not be completed, Try Again!");
			return \Redirect::to("addemployee");
		}
		
		
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function editEmployee()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("cityname"=>"cityId","officebranchcode"=>"code", "officebranchname"=>"name","statename"=>"stateId","iswarehouse"=>"isWareHouse");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$field_names1 = array(
					"advanceamount"=>"advanceAmount",  "monthlyrent"=>"monthlyRent", "ownername"=>"ownerName", "contactno"=>"ownerContactNo", "occupationdate"=>"occupiedDate", "agreementexpdate"=>"expDate",
					"paymenttype"=>"paymentType", "bankaccount"=>"bankAccount", "paymentexpecteday"=>"paymentExpectedDay", "currentbillpaidbyowner"=>"currentBillPaidByOwner", "muncipaltaxpaidbyowner"=>"muncipalTaxPaidByOwner"
			);
			$fields1 = array();
			foreach ($field_names1 as $key=>$val){
				if(isset($values[$key])){
					if($val == "occupiedDate" || $val == "expDate"){
						$fields1[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields1[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table1 = "\RentDetails";
				
				
			$update = \OfficeBranch::where("id","=",$values['id'])->update($fields);
			$data = array("id"=>$values['id']);
			$id = $values['id'];
				
			$rentid  = \RentDetails::where("officeBranchId","=",$values['id'])->get();
			if(count($rentid)>0){
				$rentid = $rentid[0]->id;
				$data = array("id"=>$rentid);
				if($db_functions_ctrl->update($table1, $fields1, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("editofficebranch?id=".$id);
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("editofficebranch?id=".$id);
				}
			}
			\Session::put("message","Operation Could not be completed, Try Again!");
			return \Redirect::to("editofficebranch?id=".$id);
		}
	
		$form_info = array();
		$form_info["name"] = "editemployee?id";
		$form_info["action"] = "editemployee?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "employees";
		$form_info["bredcum"] = " edit employee";
	
		$form_fields = array();
	
		$states =  \State::all();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		$select_args = array();
		$select_args[] = "employee.name as name";
		$select_args[] = "officebranch.code as code";
		$select_args[] = "officebranch.stateId as stateId";
		$select_args[] = "officebranch.cityId as cityId";
		$select_args[] = "officebranch.id as id";
	
	
		$entity = \OfficeBranch::where("officebranch.id","=", $values['id'])->leftjoin("rentdetails", "rentdetails.officeBranchId", "=", "officebranch.id")->select($select_args)->get();
	
		if(count($entity)){
			$entity = $entity[0];
			$states =  \State::all();
			$state_arr = array();
			foreach ($states as $state){
				$state_arr[$state['id']] = $state->name;
			}
				
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","PAYMENT TYPE")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$paymenttypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
			$pmttype_arr = array();
			foreach ($paymenttypes  as $paymenttype){
				$pmttype_arr[$paymenttype['name']] = $paymenttype->name;
			}
				
			$banks =  \BankDetails::all();
			$bank_arr = array();
			foreach ($banks as $bank){
				$bank_arr[$bank['id']] = $bank->bankName."-".$bank->accountNo;
			}
				
			$cities = \City::where("stateId", "=", $entity->stateId)->get();
			$cities_arr = array();
			foreach ($cities as $city){
				$cities_arr[$city['id']] = $city->name;
			}
	
			$tabs = array();
			$form_fields = array();
			$form_field = array("name"=>"statename", "value"=>$entity->stateId,  "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"), "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", );
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "value"=>$entity->cityId, "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$cities_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"officebranchname", "value"=>$entity->name, "content"=>"office branch name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"officebranchcode", "value"=>$entity->code, "content"=>"office branch code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$tab = array();
			$tab['form_fields'] = $form_fields;
			$tab['href'] = "tabone";
			$tab['heading'] = strtoupper("Basic Information");
			$tabs[] = $tab;
				
				
				
			$form_fields = array();
			$form_field = array("name"=>"advanceamount", "value"=>$entity->advanceAmount, "content"=>"advance amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"monthlyrent", "value"=>$entity->monthlyRent, "content"=>"monthly rent", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"ownername", "value"=>$entity->ownerName, "content"=>"owner name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contactno", "value"=>$entity->ownerContactNo, "content"=>"contact no", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"occupationdate", "value"=>date("d-m-Y",strtotime($entity->occupiedDate)), "content"=>"occupation date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"agreementexpdate", "value"=>date("d-m-Y",strtotime($entity->expDate)), "content"=>"agreement exp date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$pmttype_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$bank_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymentexpecteday", "value"=>$entity->paymentExpectedDay, "content"=>"payment expected day [1-30]", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"currentbillpaidbyowner", "value"=>$entity->currentBillPaidByOwner, "content"=>"Current Bill paid by Owner", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"muncipaltaxpaidbyowner", "value"=>$entity->muncipalTaxPaidByOwner, "content"=>"muncipal tax paid by owner", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"iswarehouse", "value"=>$entity->isWareHouse, "content"=>"is warehouse", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$tab = array();
			$tab['form_fields'] = $form_fields;
			$tab['href'] = "tabtwo";
			$tab['heading'] = strtoupper("Rental information");
			$tabs[] = $tab;
			$form_info["tabs"] = $tabs;
			return View::make("masters.layouts.edittabbedform",array("form_info"=>$form_info));
		}
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEmpId()
	{
		$empCode = \Employee::orderBy('id', 'desc')->first();
		$empCode = $empCode->empCode;
		$empCode = "MST".(substr($empCode, 3)+1);
		echo $empCode;
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
