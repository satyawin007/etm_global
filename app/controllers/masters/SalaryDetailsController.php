<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class SalaryDetailsController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addSalaryDetails()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("bankname"=>"bankName","branchname"=>"branchName","accountname"=>"accountName","accountno"=>"accountNo","accounttype"=>"accountType","balanceamount"=>"balanceAmount","cityname"=>"cityId","statename"=>"stateId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "BankDetails";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("addbankdetails");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("addbankdetails");
			}
		}
		
		
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editSalaryDetails()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$field_names = array("bankaccount"=>"bankAccount","paymenttype"=>"paymentType",
								 "effectivefrom"=>"fromDate","batta"=>"batta","accounttype"=>"accountType",
								 "salary"=>"salary","status"=>"status","bankname"=>"bankName","accountnumber"=>"accountNumber",
								 "chequenumber"=>"chequeNumber","issuedate"=>"issueDate","transactiondate"=>"transactionDate"
								);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "effectivefrom" || $key == "transactiondate" || $key=="issuedate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "SalaryDetails";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editsalarydetails?id=".$values['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editsalarydetails?id=".$values['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editsalarydetails?id";
		$form_info["action"] = "editsalarydetails?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "salarydetails";
		$form_info["bredcum"] = "edit salary details";
	
		$form_fields = array();
		
		$select_args = array();
		$select_args[] = "employee.empCode as empId";
		$select_args[] = "employee.fullName as empName";
		$select_args[] = "employee.typeId as typeId";
		$select_args[] = "employee.salaryCardNo as salaryCardNo";
		$select_args[] = "cities.name as cityName";
		$select_args[] = "officebranch.name as OfficeBranch";
		$select_args[] = "client.name as client";
		$select_args[] = "role.roleName as title";
		$select_args[] = "empsalarydetails.salary as salary";
		$select_args[] = "empsalarydetails.batta as batta";
		$select_args[] = "empsalarydetails.paymentType as paymentType";
		$select_args[] = "empsalarydetails.bankAccount as bankAccount";
		$select_args[] = "empsalarydetails.bankName as bankName";
		$select_args[] = "empsalarydetails.accountNumber as accountNumber";
		$select_args[] = "empsalarydetails.chequeNumber as chequeNumber";
		$select_args[] = "empsalarydetails.issueDate as issueDate";
		$select_args[] = "empsalarydetails.transactionDate as transactionDate";
		$select_args[] = "empsalarydetails.fromDate as fromDate";
		$select_args[] = "empsalarydetails.id as id";
		
		$entity = \SalaryDetails::where("empsalarydetails.empId","=",$values['id'])->join("employee","employee.id","=","empsalarydetails.empId")->join("role","employee.roleId","=","role.id")->leftjoin("officebranch", "employee.officeBranchId","=","officebranch.id")->leftjoin("client", "employee.clientId","=","client.id")->leftjoin("user_roles_master", "empsalarydetails.title","=","user_roles_master.id")->join("cities", "cities.id","=","employee.cityId")->select($select_args)->get();;
			
		if(count($entity)){
			$entity = $entity[0];
			
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","PAYMENT TYPE")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			$type_arr = array();
			foreach ($types as $type){
				$type_arr [$type['name']] = $type->name;
			}
			
			$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
			$banks_arr = array();
			foreach ($banks as $bank){
				$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
			}
			
			$roles = \UserRoleMaster::All();
			$roles_arr = array();
			foreach ($roles as $role){
				$roles_arr[$role->id] = $role->name;
			}
			if($entity->typeId==1){
				$emptype ="Office";
			}
			else{
				$emptype = "Non-Office";
			}
			if(date("d-m-Y",strtotime($entity->fromDate)) == "01-01-1970" || date("d-m-Y",strtotime($entity->fromDate)) == ""){
				$entity->fromDate = "";
			}
			else{
				$entity->fromDate = date("d-m-Y",strtotime($entity->fromDate));
			}
			$form_field = array("name"=>"employeetype", "id"=>"employeetype", "value"=>$emptype, "content"=>"employeetype", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"client", "value"=>$entity->OfficeBranch, "content"=>"Branch Name", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"employeename", "value"=>$entity->empName, "content"=>"employee name", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"designation", "value"=>$entity->title, "content"=>"designation", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			/* $form_field = array("name"=>"newdesignation[]", "id"=>"newdesignation", "value"=>array(), "content"=>"account type", "readonly"=>"",  "required"=>"required",  "type"=>"select", "multiple"=>"multiple", "class"=>"form-control chosen-select", "options"=>$roles_arr);
			$form_fields[] = $form_field; */
			$form_field = array("name"=>"salary", "value"=>$entity->salary, "content"=>"salary", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"batta", "value"=>$entity->batta, "content"=>"daily Batta", "readonly"=>"",  "required"=>"required",   "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"effectivefrom", "value"=>$entity->fromDate, "content"=>"effectivefrom", "readonly"=>"",  "required"=>"required",   "type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"));
			$form_fields[] = $form_field;
			if($entity->paymentType === "cheque_credit"){
				//die();
				$bankacts =  \BankDetails::All();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "cheque_debit"){
				$bankacts =  \BankDetails::All();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount",  "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "dd"){
				$form_field = array("name"=>"bankname", "id"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"ddnumber", "id"=>"ddnumber","value"=>$entity->ddNumber, "content"=>"dd number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"issuedate", "id"=>"issuedate", "value"=>date("d-m-Y",strtotime($entity->issueDate)),"content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "ecs" || $entity->paymentType === "neft" || $entity->paymentType === "rtgs"){
				$form_field = array("name"=>"bankname", "id"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"accountnumber", "id"=>"accountnumber","value"=>$entity->accountNumber, "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"salarycardno", "value"=>$entity->salaryCardNo, "content"=>"salary card no", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control input-mask-card");
			$form_fields[] = $form_field;
			/* $form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank Account", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>$banks_arr, "class"=>"form-control");
			$form_fields[] = $form_field;		
			$form_field = array("name"=>"status", "id"=>"status", "value"=>$entity->status, "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
			$form_fields[] = $form_field; */	
			$form_field = array("name"=>"id1", "id"=>"id1", "value"=>$entity->id, "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.edit2colform",array("form_info"=>$form_info));
		}
	}
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getCitiesbyStateId()
	{
		$values = Input::all();
		$entities = \City::where("stateId","=",$values['id'])->get();
		$response = "";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";			
		}
		echo $response;
	}	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getBranchbyCityId()
	{
		$values = Input::all();
		$entities = \OfficeBranch::where("Id","=",$values['id'])->get();
		$response = "";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";
		}
		echo $response;
	}

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageSalaryDetails()
	{
		$values = Input::all();
		$values['bredcum'] = "EMPLOYEE SALARY DETAILS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = 'salarydetails';
		$values['action_val'] = '';
		$theads = array('Employee Code','Employee Name', "City", "Office Branch", "Client", "Title", "Salary", "Batta", "Payment Type", "Status", "Actions");
		$values["theads"] = $theads;
			
		$tds = array('empId','empName', "cityName", "OfficeBranch", "client", "title", "salary", "batta", "paymentType", "status");
		$values["tds"] = $tds;
			
		$actions = array();
		$action = array("url"=>"editsalarydetails?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
	
		$select_args = array();
		$select_args[] = "empsalarydetails.id as id";
		$select_args[] = "employee.empCode as empId";
		$select_args[] = "employee.fullName as empName";
		$select_args[] = "officebranch.name as OfficeBranch";
		$select_args[] = "client.name as client";
		$select_args[] = "user_roles_master.name as title";
		$select_args[] = "empsalarydetails.salary as salary";
		$select_args[] = "empsalarydetails.batta as batta";
		$select_args[] = "empsalarydetails.paymentType as paymentType";
		$select_args[] = "empsalarydetails.status as status";
		$select_args[] = "cities.name as cityName";
		
		$entries = $values['entries'];
		$entities = \SalaryDetails::join("employee","employee.id","=","empsalarydetails.empId")->leftjoin("officebranch", "employee.officeBranchId","=","officebranch.id")->leftjoin("client", "employee.clientId","=","client.id")->leftjoin("user_roles_master", "empsalarydetails.title","=","user_roles_master.id")->join("cities", "cities.id","=","employee.cityId")->select($select_args)->paginate($entries);
		
		$total = \SalaryDetails::count();
			
		$values['entities'] = $entities;
		$values['total'] = $total;
		
		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		
		$form_fields = array();
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","BANK NAME")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$banks =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		$bank_arr = array();
		foreach ($banks as $bank){
			$bank_arr [$bank['name']] = $bank->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","ACCOUNT TYPES")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['name']] = $type->name;
		}
		
		$states =  \State::all();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values['provider'] = "salarydetails";
		return View::make('masters.layouts.datatable', array("values"=>$values));
	}
	
}
