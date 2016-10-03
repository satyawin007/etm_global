<?php namespace salaries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use settings\AppSettingsController;

class SalaryAdvancesController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addSalaryAdvance()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["dsf"];
			$field_names = array("employeename"=>"empId", "officebranch"=>"branchId", "depot"=>"depotId", "clientname"=>"clientId",  "date"=>"paymentDate", "amount"=>"amount", 
								"incharge"=>"inchargeId", "remarks"=>"comments", "paymenttype"=>"paymentType", "bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber",
								"issuedate"=>"issueDate","transactiondate"=>"transactionDate","accountnumber"=>"accountNumber","bankname"=>"bankName",);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "transactiondate" || $key=="date" || $key=="issuedate" || $key=="next_alert_date" || $key=="entity_date"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else if(isset($values[$key])){
						$fields[$val] = $values[$key];
					}		
				}
			}
			$fields["sourceEntity"] = "salaryadvance";
			$fields["dueType"] = "Loan";
			$db_functions_ctrl = new DBFunctionsController();
			$table = "EmpDueAmount"; 
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("salaryadvances");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("salaryadvances");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editSalaryAdvance()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["sda"];
			$field_names = array("employeename"=>"empId", "officebranch"=>"branchId", "depot"=>"depotId", "clientname"=>"clientId",  "date"=>"paymentDate", "amount"=>"amount", 
								"incharge"=>"inchargeId", "remarks"=>"comments", "stauts"=>"status", "paymenttype"=>"paymentType", "bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber",
								"issuedate"=>"issueDate","transactiondate"=>"transactionDate","accountnumber"=>"accountNumber","bankname"=>"bankName",);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "transactiondate" || $key=="date" || $key=="issuedate" || $key=="next_alert_date" || $key=="entity_date"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else if(isset($values[$key])){
						$fields[$val] = $values[$key];
					}	
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$data = array('id'=>$values['id1']);			
			$table = "\EmpDueAmount";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editsalaryadvance?id=".$values['id1']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editsalaryadvance?id=".$values['id1']);
			}
		}
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editsalaryadvance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
	
		$entity = \EmpDueAmount::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$emps = \Employee::all();
			$emp_arr = array();
			foreach ($emps as $emp){
				$emp_arr[$emp->id] = $emp->fullName." - ".$emp->empCode;
			}
			$branches = AppSettingsController::getEmpBranches();
			$branches_arr = array();
			foreach ($branches as $branch){
				$branches_arr[$branch["id"]] = $branch["name"];
			}
			
			$clients =  AppSettingsController::getEmpClients();
			$clients_arr = array();
			foreach ($clients as $client){
				$clients_arr[$client['id']] = $client['name'];
			}
			
			$select_args = array();
			$select_args[] = "inchargeaccounts.id as id";
			$select_args[] = "employee.fullName as fullName";
			$incharges =  \InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empId")->select($select_args)->get();
			$incharges_arr = array();
			foreach ($incharges as $incharge){
				$incharges_arr[$incharge->id] = $incharge->fullName;
			}
			$form_fields = array();	
			$form_payment_fields = array();
			$form_field = array("name"=>"employeetype", "id"=>"employeetype", "value"=>0, "content"=>"employee type", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"enableClientDepot(this.value);"),  "options"=>array("OFFICE"=>"OFFICE", "CLIENT BRANCH"=>"CLIENT BRANCH"), "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"clientname", "id"=>"clientname", "value"=>$entity->clientId, "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"officebranch", "id"=>"officebranch", "value"=>$entity->branchId, "content"=>"office branch", "readonly"=>"","required"=>"", "type"=>"select", "options"=>$branches_arr, "action"=>array("type"=>"onChange", "script"=>"getEmployeesByOffice(this.value);"), "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"depot", "id"=>"depot", "value"=>$entity->depotId, "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getEmployeesByDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field;
			$form_field = array("name"=>"employeename", "id"=>"employeename", "value"=>$entity->empId, "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$incharges_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"date",  "value"=>$entity->paymentDate, "content"=>"advance Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amount", "value"=>$entity->amount, "content"=>"advance amount <span style='font-size:11px;'><br/>(Enter <span style='color:red;'>negitive - </span> value for returned advance amount)</span>", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "value"=>$entity->comments, "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "value"=>"", "id"=>"status", "value"=>$entity->status, "content"=>"status", "readonly"=>"", "required"=>"", "type"=>"select",  "class"=>"form-control chosen-select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			if($entity->paymentType === "cheque_credit"){
				$bankacts =  \BankDetails::All();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"issuedate","value"=>date("d-m-Y",strtotime($entity->issueDate)), "content"=>"issue date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"transactiondate", "value"=>date("d-m-Y",strtotime($entity->transactionDate)), "content"=>"transaction date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
			}
			if($entity->paymentType === "cheque_debit"){
				$bankacts =  \BankDetails::All();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount",  "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"issuedate","value"=>date("d-m-Y",strtotime($entity->issueDate)), "content"=>"issue date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"transactiondate", "value"=>date("d-m-Y",strtotime($entity->transactionDate)), "content"=>"transaction date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
			}
			if($entity->paymentType === "dd"){
				$form_field = array("name"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"ddnumber","value"=>$entity->ddNumber, "content"=>"dd number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"issuedate", "value"=>$entity->issueDate,"content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
			}
			if($entity->paymentType === "ecs" || $entity->paymentType === "neft" || $entity->paymentType === "rtgs"){
				$bankacts =  \BankDetails::where("Status","=","ACTIVE")->get();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
				$form_info["form_fields"] = $form_fields;
			}
			if($entity->paymentType === "credit_card"){
				$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","CREDIT CARD")->get();
				$cards_arr = array();
				foreach ($cards as $card){
					$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"credit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
			}
			if($entity->paymentType === "debit_card"){
				$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","DEBIT CARD")->get();
				$cards_arr = array();
				foreach ($cards as $card){
					$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"debit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_payment_fields[] = $form_field;
			}
			$form_field = array("name"=>"id1", "value"=>$entity->id, "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		
			$form_info["form_fields"] = $form_fields;
			$form_info["form_payment_fields"] = $form_payment_fields;
			return View::make("salaries.edit2colmodalform",array("form_info"=>$form_info));
		}
	}
	
		
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageSalaryAdvances()
	{
		$values = Input::all();
		$values['bredcum'] = "EMPLOYEE SALARY ADVANCES";
		$values['home_url'] = '#';
		$values['add_url'] = 'addsalaryadvance';
		$values['form_action'] = 'salaryadvance';
		$values['action_val'] = '#';
		$theads = array('Emp Id','Emp Name', "advance amount", "from branch", "paid date", "payment info", "comments", "status", "Actions");
		$values["theads"] = $theads;
			
		$form_info = array();
		$form_info["name"] = "addsalaryadvance";
		$form_info["action"] = "addsalaryadvance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "salaryadvances";
		$form_info["bredcum"] = "add salaryadvance";
		
		$emps = \Employee::all();
		$emp_arr = array();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName." - ".$emp->empCode;
		}
		
		$branches = AppSettingsController::getEmpBranches();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch["id"]] = $branch["name"];
		}
				
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		
		$select_args = array();
		$select_args[] = "inchargeaccounts.id as id";
		$select_args[] = "employee.fullName as fullName";
		$incharges =  \InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empId")->select($select_args)->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->fullName;
		}
		
		$form_fields = array();		
		$form_field = array("name"=>"employeetype", "content"=>"employee type", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"enableClientDepot(this.value);"),  "options"=>array("OFFICE"=>"OFFICE", "CLIENT BRANCH"=>"CLIENT BRANCH"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"officebranch","content"=>"office branch", "readonly"=>"","required"=>"", "type"=>"select", "options"=>$branches_arr, "action"=>array("type"=>"onChange", "script"=>"getEmployeesByOffice(this.value);"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getEmployeesByDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employeename", "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$incharges_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"advance Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amount", "content"=>"advance amount <span style='font-size:11px;'><br/>(Enter <span style='color:red;'>negitive - </span> value for returned advance amount)</span>", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		/*$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editsalaryadvance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"employeename1", "value"=>"", "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date1", "value"=>"", "content"=>"advance Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amount1", "value"=>"", "content"=>"advance amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "value"=>"", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;*/
		
		$values['provider'] = "salaryadvances";
		return View::make('salaries.lookupdatatable', array("values"=>$values));
	}	
	
	public function deleteSalaryAdvance(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$data = array('id'=>$values['id']);
		$table = "\EmpDueAmount";
		$fields = array("status"=>"DELETED", "deleted"=>"Yes");
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
			return;
		}
		echo "fail";
	}
	
	public function rejectLeave(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$data = array('id'=>$values['id']);
		$table = "\Leaves";
		$fields = array("status"=>"Rejected");
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
			return;
		}
		echo "fail";
	}
	
	public function leaveDetails(){
		$values = Input::all();
		$empid = $values["eid"];
		$salaryMonth = $values["dt"];
		$noOfDays = date("t", strtotime($salaryMonth)) -1;
		$startDate = $salaryMonth;
		$endDate =  date('Y-m-d', strtotime($salaryMonth.'+ '.$noOfDays.' days'));
		$jsondata = array();
	
		$recs = DB::select( DB::raw("SELECT * from leaves where (fromDate BETWEEN '".$startDate."' and '".$endDate."' or toDate BETWEEN '".$startDate."' and '".$endDate."') and empId=".$empid." and deleted='No'"));
		$data = "";
		foreach ($recs as $rec){
			$data = $data."<tr>";
			$data = $data."<td>".date("d-m-Y",strtotime($rec->fromDate))."</td>";
			$data = $data."<td>".$rec->fromMrngEve."</td>";
			$data = $data."<td>".date("d-m-Y",strtotime($rec->toDate))."</td>";
			$data = $data."<td>".$rec->toMrngEve."</td>";
			$data = $data."<td>".$rec->noOfLeaves."</td>";
			$data = $data."<td>".$rec->remarks."</td>";
			$data = $data."<td>".$rec->status."</td>";
		}
		$jsondata["tbody"] = $data;
		echo json_encode($jsondata);
	}
}
