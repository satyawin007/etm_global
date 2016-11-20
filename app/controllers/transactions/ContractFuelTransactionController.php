<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ContractFuelTransactionController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addTransaction()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();	
			
			//$values["Sdf"];
			if(isset($values["transtype"]) && $values["transtype"] == "income" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds", "bankId"=>"bankId",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense", "date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
					);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "vehicle"){
							$vehids = "";
							foreach ($values[$key] as $vehid){
								$vehids = $vehids.",".$vehid;
							}
							$vehids = substr($vehids, 1);
							$fields[$val] = $vehids;
						}
						else if($key == "suspense"){
							$sus_vals = array("on"=>"Yes","off"=>"No");
							$fields[$val] = $sus_vals[$values[$key]];
						}
						else{
							$fields[$val] = $values[$key];
						}
					}				
				}
				$expenses_arr = array();
				$expenses_arr["999"] = "PREPAID RECHARGE";
				$field_names = array("prepaidagent");
				foreach ($field_names as $field_name){
					if(isset($values[$field_name])){
						$fields["entity"] = $expenses_arr[$values["type"]];
						$fields["entityValue"] = $values[$field_name];
					}
				}
				if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
					$destinationPath = storage_path().'/uploads/'; // upload path
					$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
					$fileName = uniqid().'.'.$extension; // renameing image
					Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
					$fields["filePath"] = $fileName;
				}
				$transid =  strtoupper(uniqid().mt_rand(100,999));
				$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
				foreach($chars as $k=>$v){
					$transid = str_replace($k, $v, $transid);
				}
				$fields["transactionId"] = $transid;
				$fields["source"] = "income transaction";
				$db_functions_ctrl = new DBFunctionsController();
				$table = "IncomeTransaction";
				if($db_functions_ctrl->insert($table, $fields)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("incometransactions");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("incometransactions");
				}
			}
			if(isset($values["transtype"]) && $values["transtype"] == "expense" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds", "bankId"=>"bankId",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate","suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				$expenses_arr = array();
				$expenses_arr["998"] = "CREDIT SUPPLIER PAYMENT";
				$expenses_arr["997"] = "FUEL STATION PAYMENT";
				$expenses_arr["996"] = "LOAN PAYMENT";
				$expenses_arr["995"] = "RENT";
				$expenses_arr["994"] = "INCHARGE ACCOUNT CREDIT";
				$expenses_arr["993"] = "PREPAID RECHARGE";
				$expenses_arr["992"] = "ONLINE OPERATORS";
				$expenses_arr["991"] = "DAILY FINANCE PAYMENT";
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "vehicle"){
							$vehids = "";
							foreach ($values[$key] as $vehid){
								$vehids = $vehids.",".$vehid;
							}
							$vehids = substr($vehids, 1);
							$fields[$val] = $vehids;
						}
						else if($key == "suspense"){
							$sus_vals = array("on"=>"Yes","off"=>"No");
							$fields[$val] = $sus_vals[$values[$key]];
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				$field_names = array("prepaidagent", "creditsupplier", "fuelstation", "loanpayment", "officebranch", "prepaidagent", "onlineoperators", "dailyfinance");
				foreach ($field_names as $field_name){
					if(isset($values[$field_name])){
						unset($fields["type"]);
						$fields["entity"] = $expenses_arr[$values["type"]];
						$fields["entityValue"] = $values[$field_name];
					}
				}
				if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
					$destinationPath = storage_path().'/uploads/'; // upload path
					$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
					$fileName = uniqid().'.'.$extension; // renameing image
					Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
					$fields["filePath"] = $fileName;
				}
				$transid =  strtoupper(uniqid().mt_rand(100,999));
				$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
				foreach($chars as $k=>$v){
					$transid = str_replace($k, $v, $transid);
				}
				$fields["transactionId"] = $transid;
				$fields["source"] = "expense transaction";
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ExpenseTransaction";
				if($db_functions_ctrl->insert($table, $fields)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("expensetransactions");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("expensetransactions");
				}
			}
			if(isset($values["transtype"]) && $values["transtype"] == "fuel" ){
				
				if(isset($values["date"]) && $values["date"] == ""){
					$values["date"] = date("d-m-Y");
				}
				$field_names = array("branch"=>"branchId","totalamount"=>"amount","paymenttype"=>"paymentType", "vehicleno"=>"vehicleId","incharge"=>"inchargeId", "type"=>"name",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","tripid"=>"tripId",
						"fuelstationname"=>"fuelStationId","startreading"=>"startReading","litres"=>"litres","billno"=>"billNo",
						"paymentpaid"=>"paymentPaid","bankaccount"=>"bankAccountId","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense", "date"=>"filledDate","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "suspense"){
							$sus_vals = array("on"=>"Yes","off"=>"No");
							$fields[$val] = $sus_vals[$values[$key]];
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				
				//code to get contractid based on vehicleid
				$fields["contractId"] = 0;
				$contract_veh = \ContractVehicle::where("vehicleId","=",$values["vehicleno"])
									->where("status","=","ACTIVE")->get();
				if(count($contract_veh)>0){
					$contract_veh = $contract_veh[0];
					$fields["contractId"] = $contract_veh->contractId;
				}
				
				if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
					$destinationPath = storage_path().'/uploads/'; // upload path
					$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
					$fileName = uniqid().'.'.$extension; // renameing image
					Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
					$fields["filePath"] = $fileName;
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "FuelTransaction";
				if($db_functions_ctrl->insert($table, $fields)){
					\Session::put("message","Operation completed Successfully");
					if(isset($values["tripid"]) && $values["triptype"]=="local"){
						return \Redirect::to("addlocaltripfuel?triptype=LOCAL&transtype=fuel&id=".$values["tripid"]);
					}
					else if(isset($values["tripid"])){
						return \Redirect::to("addtripfuel?transtype=fuel&id=".$values["tripid"]);
					}
					return \Redirect::to("fueltransactions");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					if(isset($values["tripid"]) && $values["triptype"]=="local"){
						return \Redirect::to("addlocaltripfuel?triptype=LOCAL&transtype=fuel&id=".$values["tripid"]);
					}
					else if(isset($values["tripid"])){
						return \Redirect::to("addtripfuel?transtype=fuel&id=".$values["tripid"]);
					}
					return \Redirect::to("fueltransactions");
				}
			}
// 			if(isset($values["transtype"]) && $values["transtype"] == "fuel" ){
// 				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType","type"=>"name",
// 						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
// 						"transactiondate"=>"transactionDate","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
// 				);
// 				$fields = array();
// 				foreach ($field_names as $key=>$val){
// 					if(isset($values[$key])){
// 						if($key == "transactiondate" || $key=="date1" || $key=="issuedate"){
// 							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
// 						}
// 						else{
// 							$fields[$val] = $values[$key];
// 						}
// 					}
// 				}
// 				$db_functions_ctrl = new DBFunctionsController();
// 				$table = "IncomeTransaction";
// 				if($db_functions_ctrl->insert($table, $fields)){
// 					\Session::put("message","Operation completed Successfully");
// 					return \Redirect::to("transactions");
// 				}
// 				else{
// 					\Session::put("message","Operation Could not be completed, Try Again!");
// 					return \Redirect::to("transactions");
// 				}
// 			}
		}
	}
	
	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function editTransaction()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			if(isset($values["type1"]) && $values["type1"] == "income" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"date", "suspense"=>"suspense", "date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "vehicle"){
							$vehids = "";
							foreach ($values[$key] as $vehid){
								$vehids = $vehids.",".$vehid;
							}
							$vehids = substr($vehids, 1);
							$fields[$val] = $vehids;
						}
						else if($key == "suspense"){
							$sus_vals = array("on"=>"Yes","off"=>"No");
							$fields[$val] = $sus_vals[$values[$key]];
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				if(!isset($values["suspense"])){
					$fields["suspense"] = "No";
				}
				if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
					$destinationPath = storage_path().'/uploads/'; // upload path
					$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
					$fileName = uniqid().'.'.$extension; // renameing image
					Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
					$fields["filePath"] = $fileName;
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "IncomeTransaction";
				$data = array("id"=>$values["id"]);
				if($db_functions_ctrl->updatetrans($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("edittransaction?type=income&id=".$values["id"]);
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("edittransaction?type=income&id=".$values["id"]);
				}
			}
			if(isset($values["type1"]) && $values["type1"] == "expense" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate","suspense"=>"suspense", "date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "vehicle"){
							$vehids = "";
							foreach ($values[$key] as $vehid){
								$vehids = $vehids.",".$vehid;
							}
							$vehids = substr($vehids, 1);
							$fields[$val] = $vehids;
						}
						else if($key == "suspense"){
							$sus_vals = array("on"=>"Yes","off"=>"No");
							$fields[$val] = $sus_vals[$values[$key]];
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				if(!isset($values["suspense"])){
					$fields["suspense"] = "No";
				}
				if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
					$destinationPath = storage_path().'/uploads/'; // upload path
					$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
					$fileName = uniqid().'.'.$extension; // renameing image
					Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
					$fields["filePath"] = $fileName;
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ExpenseTransaction";
				$data = array("id"=>$values["id"]);
				if($db_functions_ctrl->updatetrans($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("edittransaction?type=expense&id=".$values["id"]);
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("edittransaction?type=expense&id=".$values["id"]);
				}
			}
			if(isset($values["type1"]) && $values["type1"] == "fuel" ){
				$field_names = array("branch"=>"branchId","totalamount"=>"amount","paymenttype"=>"paymentType", "vehicleno"=>"vehicleId","incharge"=>"inchargeId", "type"=>"name",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","tripid"=>"tripId",
						"fuelstationname"=>"fuelStationId","startreading"=>"startReading","litres"=>"litres","billno"=>"billNo",
						"paymentpaid"=>"paymentPaid","bankaccount"=>"bankAccountId","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate","suspense"=>"suspense","date"=>"filledDate","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "suspense"){
							$sus_vals = array("on"=>"Yes","off"=>"No");
							$fields[$val] = $sus_vals[$values[$key]];
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				if(!isset($values["suspense"])){
					$fields["suspense"] = "No";
				}
				if(!isset($values["suspense"])){
					$fields["suspense"] = "No";
				}
				if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
					$destinationPath = storage_path().'/uploads/'; // upload path
					$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
					$fileName = uniqid().'.'.$extension; // renameing image
					Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
					$fields["filePath"] = $fileName;
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "FuelTransaction";
				$data = array("id"=>$values["id"]);
				if(isset($values["tripid"])){
					if($db_functions_ctrl->update($table, $fields, $data)){
						\Session::put("message","Operation completed Successfully");
						return \Redirect::to("edittransaction?type=fuel&id=".$values["id"]);
					}
					else{
						\Session::put("message","Operation Could not be completed, Try Again!");
						return \Redirect::to("edittransaction?type=fuel&id=".$values["id"]);
					}
				}
				if($db_functions_ctrl->update($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("edittransaction?type=fuel&id=".$values["id"]);
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("edittransaction?type=fuel&id=".$values["id"]);
				}
			}
		}
		if(isset($values["type"]) && $values["type"]=="income"){
			$form_info = array();
			$form_info["name"] = "transactionform";
			$form_info["action"] = "edittransaction";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "incometransactions";
			$form_info["bredcum"] = "edit transaction";
			$entity = \IncomeTransaction::where("transactionId","=",$values['id'])->get();
			if(count($entity)>0){
				$entity = $entity[0];
				$entity->date = date("d-m-Y",strtotime($entity->date));
				
				$braches = \OfficeBranch::All();
				$brach_arr = array();
				foreach ($braches as $brach){
					$brach_arr[$brach->id] = $brach->name;
				}
				$parentId = \LookupTypeValues::where("name", "=", "INCOME")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
						
				}
				$incomes_arr = array();
				foreach ($incomes as $income){
					$incomes_arr[$income->id] = $income->name;
				}
				$incomes_arr["999"] = "PREPAID RECHARGE";
				
				$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
				$incharges_arr = array();
				foreach ($incharges as $incharge){
					$incharges_arr[$incharge->id] = $incharge->name;
				}
				
				$form_fields = array();	
				$form_payment_fields = array();
				$form_field = array("name"=>"branch", "id"=>"branchId","value"=>$entity->branchId, "content"=>"branch", "readonly"=>"",   "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$brach_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"type", "id"=>"transtype",  "value"=>$entity->lookupValueId, "content"=>"transaction type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incomes_arr);
				$form_fields[] = $form_field;
				
				if($entity->inchargeId != 0){
					$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
					$incharges_arr = array();
					foreach ($incharges as $incharge){
						$incharges_arr[$incharge->id] = $incharge->name;
					}
					$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				if($entity->vehicleIds != ""){
					$vehicles =  \Vehicle::All();
					$vehicles_arr = array();
					foreach ($vehicles as $vehicle){
						$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
					}
					$sel_vehs = explode(",", $entity->vehicleIds);
					$form_field = array("name"=>"vehicle[]", "id"=>"vehicle", "value"=>$sel_vehs, "content"=>"vehicle reg no", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
					$form_fields[] = $form_field;
				}
				if($entity->branchId1 != 0){
					$branches =  \OfficeBranch::All();
					$branches_arr = array();
					foreach ($branches as $branch){
						$branches_arr[$branch->id] = $branch->name;
					}
					$form_field = array("name"=>"branch1", "id"=>"branch1", "value"=>$entity->branchId1, "content"=>"Branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$branches_arr);
					$form_fields[] = $form_field;
				}
				if($entity->employeId != 0){
					$employees =  \Employee::All();
					$employees_arr = array();
					foreach ($employees as $employee){
						$employees_arr[$employee->id] = $employee->empCode." - ".$employee->fullName;
					}
					$form_field = array("name"=>"employee", "id"=>"employee", "value"=>$entity->employeId, "content"=>"Employee", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$employees_arr);
					$form_fields[] = $form_field;
				}
				if($entity->bankId != 0){
					$bankacts_arr = array();
					$bankacts =  \BankDetails::All();
					foreach ($bankacts as $bankact){
						$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
					}
					$form_field = array("name"=>"bankId", "id"=>"bankId", "value"=>$entity->bankId, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"transactiondate", "id"=>"transactiondate",  "value"=>date("d-m-Y",strtotime($entity->date)), "content"=>"transaction date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "value"=>$entity->suspense, "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"amount", "id"=>"amount",  "value"=>$entity->amount, "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billfile", "content"=>"upload bill", "value"=>$entity->filePath, "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
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
				}
				if($entity->paymentType === "dd"){
					$form_field = array("name"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"ddnumber","value"=>$entity->ddNumber, "content"=>"dd number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"issuedate", "value"=>date("d-m-Y",strtotime($entity->issueDate)),"content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
					$form_payment_fields[] = $form_field;
				}
				if($entity->paymentType === "ecs" || $entity->paymentType === "neft" || $entity->paymentType === "rtgs"){
					$form_field = array("name"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"accountnumber","value"=>$entity->accountNumber, "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
				}
				if($entity->paymentType === "credit_card"){
					$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","CREDIT CARD")->get();
					$cards_arr = array();
					foreach ($cards as $card){
						$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"credit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				if($entity->paymentType === "debit_card"){
					$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","DEBIT CARD")->get();
					$cards_arr = array();
					foreach ($cards as $card){
						$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"debit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"remarks", "id"=>"remarks", "value"=>$entity->remarks, "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"type1", "id"=>"type", "value"=>$values["type"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"id", "id"=>"transid", "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_info["form_fields"] = $form_fields;
				$form_info["form_payment_fields"] = $form_payment_fields;
				return View::make("transactions.edit2colmodalform",array("form_info"=>$form_info));
			}
		}
		else if(isset($values["type"]) && $values["type"]=="expense"){
			$form_info = array();
			$form_info["name"] = "transactionform";
			$form_info["action"] = "edittransaction";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "expensetransactions";
			$form_info["bredcum"] = "edit transaction";
				
			$entity = \ExpenseTransaction::where("transactionId","=",$values['id'])->get();
			if(count($entity)>0){
				$entity = $entity[0];
				$entity->date = date("d-m-Y",strtotime($entity->date));
				$braches = \OfficeBranch::All();
				$brach_arr = array();
				foreach ($braches as $brach){
					$brach_arr[$brach->id] = $brach->name;
				}
		
				$parentId = \LookupTypeValues::where("name", "=", "EXPENSE")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
				}
				$incomes_arr = array();
				foreach ($incomes as $income){
					$incomes_arr[$income->id] = $income->name;
				}
				$incomes_arr["998"] = "CREDIT SUPPLIER PAYMENT";
				$incomes_arr["997"] = "FUEL STATION PAYMENT";
				$incomes_arr["996"] = "LOAN PAYMENT";
				$incomes_arr["995"] = "RENT";
				$incomes_arr["994"] = "INCHARGE ACCOUNT CREDIT";
				$incomes_arr["993"] = "PREPAID RECHARGE";
				$incomes_arr["992"] = "ONLINE OPERATORS";
				$incomes_arr["991"] = "DAILY FINANCE PAYMENT";
		
				$form_fields = array();	
				$form_payment_fields = array();
				$form_field = array("name"=>"branch", "id"=>"branchId","value"=>$entity->branchId, "content"=>"branch", "readonly"=>"",   "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$brach_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"type", "id"=>"transtype",  "value"=>$entity->lookupValueId, "content"=>"transaction type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incomes_arr);
				$form_fields[] = $form_field;
				if($entity->inchargeId != 0){
					$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
					$incharges_arr = array();
					foreach ($incharges as $incharge){
						$incharges_arr[$incharge->id] = $incharge->name;
					}
					$form_field = array("name"=>"enableincharge", "id"=>"enableincharge","content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
					$form_fields[] = $form_field;
					$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				if($entity->vehicleIds != ""){
					$vehicles =  \Vehicle::All();
					$vehicles_arr = array();
					foreach ($vehicles as $vehicle){
						$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
					}
					$sel_vehs = explode(",", $entity->vehicleIds);
					$form_field = array("name"=>"vehicle[]", "id"=>"vehicle", "value"=>$sel_vehs, "content"=>"vehicle reg no", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
					$form_fields[] = $form_field;
				}
				if($entity->branchId1 != 0){
					$branches =  \OfficeBranch::All();
					$branches_arr = array();
					foreach ($branches as $branch){
						$branches_arr[$branch->id] = $branch->name;
					}
					$form_field = array("name"=>"branch1", "id"=>"branch1", "value"=>$entity->branchId1, "content"=>"Branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$branches_arr);
					$form_fields[] = $form_field;
				}
				if($entity->employeId != 0){
					$employees =  \Employee::All();
					$employees_arr = array();
					foreach ($employees as $employee){
						$employees_arr[$employee->id] = $employee->empCode." - ".$employee->fullName;
					}
					$form_field = array("name"=>"employee", "id"=>"employee", "value"=>$entity->employeId, "content"=>"Employee", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$employees_arr);
					$form_fields[] = $form_field;
				}
				if($entity->bankId != 0){
					$bankacts_arr = array();
					$bankacts =  \BankDetails::All();
					foreach ($bankacts as $bankact){
						$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
					}
					$form_field = array("name"=>"bankId", "id"=>"bankId", "value"=>$entity->bankId, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"transactiondate", "id"=>"transactiondate",  "value"=>$entity->date, "content"=>"transaction date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "value"=>$entity->suspense, "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"amount", "id"=>"amount",  "value"=>$entity->amount, "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billfile", "content"=>"upload bill", "value"=>$entity->filePath, "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
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
					$form_field = array("name"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"accountnumber","value"=>$entity->accountNumber, "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
				}
				if($entity->paymentType === "credit_card"){
					$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","CREDIT CARD")->get();
					$cards_arr = array();
					foreach ($cards as $card){
						$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"credit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				if($entity->paymentType === "debit_card"){
					$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","DEBIT CARD")->get();
					$cards_arr = array();
					foreach ($cards as $card){
						$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"debit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"remarks", "id"=>"remarks", "value"=>$entity->remarks, "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"type1", "id"=>"type", "value"=>$values["type"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"id", "id"=>"transid", "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_info["form_fields"] = $form_fields;
				$form_info["form_payment_fields"] = $form_payment_fields;
				return View::make("transactions.edit2colmodalform",array("form_info"=>$form_info));
			}
		}
		else if(isset($values["type"]) && $values["type"]=="fuel"){
			$form_info = array();
			$form_info["name"] = "transactionform";
			$form_info["action"] = "edittransaction";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "fueltransactions";
			$form_info["bredcum"] = "edit transaction";
		
			$entity = \FuelTransaction::where("id","=",$values['id'])->get();
			if(count($entity)>0){
				$entity = $entity[0];
				$entity->date = date("d-m-Y",strtotime($entity->date));
		
				$branches =  \OfficeBranch::All();
				$branches_arr = array();
				foreach ($branches as $branch){
					$branches_arr[$branch->id] = $branch->name;
				}
				
				$states =  \State::Where("status","=","ACTIVE")->get();
				$state_arr = array();
				foreach ($states as $state){
					$state_arr[$state['id']] = $state->name;
				}
				
				$vehicles =  \Vehicle::all();
				$vehicles_arr = array();
				foreach ($vehicles as $vehicle){
					$vehicles_arr[$vehicle['id']] = $vehicle->veh_reg;
				}
				
				$select_fields = array();
				$form_payment_fields= array();
				$select_fields[] = "fuelstationdetails.name as name";
				$select_fields[] = "cities.name as cityname";
				$select_fields[] = "fuelstationdetails.id as id";
				
				$fuelstations =  \FuelStation::join("cities","cities.id","=","fuelstationdetails.cityId")->select($select_fields)->get();
				$fuelstations_arr = array();
				foreach ($fuelstations as $fuelstation){
					$fuelstations_arr[$fuelstation['id']] = $fuelstation->name." - ".$fuelstation->cityname;
				}
				
				$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")
				->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
				$incharges_arr = array();
				foreach ($incharges as $incharge){
					$incharges_arr[$incharge->id] = $incharge->name;
				}
		
				/*
				$form_field = array("name"=>"transactionbranch", "content"=>"transaction branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control",  "options"=>$branches_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"filldate", "content"=>"fill date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				*/
				if($entity->tripId==0){
					$form_field = array("name"=>"vehicleno",  "value"=>$entity->vehicleId, "id"=>"vehicleno",  "content"=>"vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
					$form_fields[] = $form_field;
				}
				/*
				$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange","script"=>"changeState(this.value)"), "options"=>$state_arr, "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array(), "class"=>"form-control");
				$form_fields[] = $form_field;
				*/
				$form_field = array("name"=>"fuelstationname", "value"=>$entity->fuelStationId, "id"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$fuelstations_arr, "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"startreading", "value"=>$entity->startReading, "id"=>"startreading", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"litres", "value"=>$entity->litres, "id"=>"litres", "content"=>"litres", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"priceperlitre", "value"=>($entity->amount/$entity->litres), "id"=>"priceperlitre", "content"=>"price per litre", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calcTotal()"), "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"totalamount", "value"=>$entity->amount, "id"=>"totalamount", "content"=>"total amount", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"date", "value"=>date("d-m-Y",strtotime($entity->filledDate)), "id"=>"date", "content"=>"filled date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billno", "value"=>$entity->billNo, "id"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"enableincharge", "id"=>"enableincharge","content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "value"=>$entity->suspense,  "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billfile", "content"=>"upload bill", "value"=>$entity->filePath, "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"remarks", "value"=>$entity->remarks, "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"required", "type"=>"textarea", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"paymentpaid", "value"=>$entity->paymentPaid, "id"=>"paymentpaid", "content"=>"payment paid", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"YES","No"=>"NO"));
				$form_fields[] = $form_field;
				if($entity->paymentPaid == "No"){
					$entity->paymentType = "";
				}
				$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
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
					$form_field = array("name"=>"issuedate", "value"=>date("d-m-Y",strtotime($entity->issueDate)),"content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
					$form_payment_fields[] = $form_field;
				}
				if($entity->paymentType === "ecs" || $entity->paymentType === "neft" || $entity->paymentType === "rtgs"){
					$form_field = array("name"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"accountnumber","value"=>$entity->accountNumber, "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_payment_fields[] = $form_field;
				}
				if($entity->paymentType === "credit_card"){
					$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","CREDIT CARD")->get();
					$cards_arr = array();
					foreach ($cards as $card){
						$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"credit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				if($entity->paymentType === "debit_card"){
					$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","DEBIT CARD")->get();
					$cards_arr = array();
					foreach ($cards as $card){
						$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"debit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"type1", "id"=>"type", "value"=>$values["type"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"id", "id"=>"type", "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
				$form_fields[] = $form_field;
				
				if($entity->tripId>0){
					$form_field = array("name"=>"tripid", "id"=>"tripid", "value"=>$entity->tripId, "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				$form_info["form_fields"] = $form_fields;
				$form_info["form_payment_fields"] = $form_payment_fields;
				return View::make("transactions.edit2colmodalform",array("form_info"=>$form_info));
			}
		}
		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editServiceProvider()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("provider1"=>"provider","branch1"=>"branchId","name1"=>"name",
					"number1"=>"number","companyname1"=>"companyName","address1"=>"address","referencename1"=>"refName",
					"referencenumber1"=>"refNumber","internetconfigurationdetails1"=>"configDetails"
				);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ServiceProvider";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("serviceproviders?provider=".$values["provider1"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("serviceproviders?provider=".$values["provider1"]);
			}
		}
	}
	
		

	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function getPaymentFields()
	{
		$values = Input::all();
		$form_fields = array();
		$form_info = array();
		
		if(isset($values["paymenttype"]) && $values["paymenttype"] === "advance"){
			echo "";
			return "";
			/*
			 $incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
			$incharges_arr = array();
			foreach ($incharges as $incharge){
				$incharges_arr[$incharge->id] = $incharge->name;
			}
			$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			*/
		}
		if(isset($values["paymenttype"]) && $values["paymenttype"] === ""){
			echo "";
			return "";
		}
		if(isset($values["paymenttype"]) && $values["paymenttype"] === "cash"){
			echo "";
			return "";
		}
		if(isset($values["paymenttype"]) && $values["paymenttype"] === "cheque_debit"){
			$bankacts =  \BankDetails::All();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
			}
			$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			if(!isset($values["income"])){
				$form_field = array("name"=>"issuedate", "content"=>"issue date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"transactiondate", "content"=>"transaction date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
			}
			$form_info["form_fields"] = $form_fields;
		}
		if(isset($values["paymenttype"]) && $values["paymenttype"] === "cheque_credit"){
			$bankacts =  \BankDetails::All();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
			}
			$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"cheque number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			if(!isset($values["income"])){
				$form_field = array("name"=>"issuedate", "content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"transactiondate", "content"=>"transaction date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
			}
			$form_info["form_fields"] = $form_fields;
		}
		if(isset($values["paymenttype"]) && $values["paymenttype"] === "dd"){
			$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"ddnumber", "content"=>"dd number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"issuedate", "content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
		}
		if(isset($values["paymenttype"]) && ($values["paymenttype"] === "ecs" || $values["paymenttype"] === "neft" || $values["paymenttype"] === "rtgs")){
			$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"accountnumber", "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
		}		
		return view::make("transactions.paymentform",array("form_info"=>$form_info));
	}
	
	public function deleteTransaction()
	{
		$values = Input::all();
		if(isset($values["type"]) && $values["type"] == "income" ){
			$db_functions_ctrl = new DBFunctionsController();
			$table = "IncomeTransaction";
			$fields = array("status"=>"DELETED");
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->updatetrans($table, $fields, $data)){
				echo "success";
			}
			else{
				echo "fail";
			}
		}
		else if(isset($values["type"]) && $values["type"] == "expense" ){
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ExpenseTransaction";
			$fields = array("status"=>"DELETED");
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->updatetrans($table, $fields, $data)){
				echo "success";
			}
			else{
				echo "fail";
			}
		}
		else if(isset($values["type"]) && $values["type"] == "fuel" ){
			$db_functions_ctrl = new DBFunctionsController();
			$table = "FuelTransaction";
			$fields = array("status"=>"DELETED");
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				echo "success";
			}
			else{
				echo "fail";
			}
		}
	}
	
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function getFuelTransactionFields()
	{
		$values = Input::All();
		$form_fields = array();
		$form_info = array();
		
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$vehicles =  \Vehicle::all();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle['id']] = $vehicle->veh_reg;
		}
		
		$select_fields = array();
		$select_fields[] = "fuelstationdetails.name as name";
		$select_fields[] = "cities.name as cityname";
		$select_fields[] = "fuelstationdetails.id as id";
		$form_field = array("name"=>"", "value"=>"", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"hidden", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"", "value"=>"", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"hidden", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$fuelstations =  \FuelStation::leftjoin("cities","cities.id","=","fuelstationdetails.cityId")->select($select_fields)->get();
		$fuelstations_arr = array();
		foreach ($fuelstations as $fuelstation){
			$fuelstations_arr[$fuelstation['id']] = $fuelstation->name." - ".$fuelstation->cityname;
		}

		/*
		$form_field = array("name"=>"transactionbranch", "content"=>"transaction branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control",  "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"filldate", "content"=>"fill date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		*/
		if(isset($values["type"]) && $values["type"]=="trips"){}
		else if(isset($values["type"]) && $values["type"]=="localtrips"){
			$bookingId = \BusBookings::where("id","=",$values["bookingid"])->get();
			if(count($bookingId)>0){
				$bookingId = $bookingId[0];
				$bookingId = $bookingId->booking_number;
			}
			$veh_ids = \BookingVehicles::where("booking_number","=", $bookingId)->get();
			$veh_ids_arr = array();
			foreach ($veh_ids as $veh_id){
				$veh_ids_arr[] = $veh_id->vehicleId;
			}
			$vehicles =  \Vehicle::whereIn("id",$veh_ids_arr)->get();
			$vehicles_arr = array();
			foreach ($vehicles as $vehicle){
				$vehicles_arr[$vehicle['id']] = $vehicle->veh_reg;
			}
			$form_field = array("name"=>"vehicleno", "content"=>"vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
		}
		else{
			$form_field = array("name"=>"vehicleno", "content"=>"vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
		}
		/*
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange","script"=>"changeState(this.value)"), "options"=>$state_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		*/
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")
		->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		$form_field = array("name"=>"date", "content"=>"filled date", "readonly"=>"",  "required"=>"", "type"=>"text",  "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$fuelstations_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"litres", "content"=>"litres", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"priceperlitre", "content"=>"price per litre", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calcTotal()"), "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentpaid", "value"=>"No", "content"=>"payment paid", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"YES","No"=>"NO"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
			
		$form_info["form_fields"] = $form_fields;
		return view::make("transactions.paymentform",array("form_info"=>$form_info));
	}
	

	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function getTransactionFields()
	{
		//$values["test"];
		$form_fields = array();
		$form_field = array("name"=>"", "value"=>"", "content"=>"amount", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"", "value"=>"", "content"=>"amount", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_info = array();
		$values = Input::All();
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")
		->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		if(isset($values["typeId"]) && ($values["typeId"]>900 || $values["typeId"]=="88" || $values["typeId"]=="89" || $values["typeId"]=="108" || $values["typeId"]=="119" || $values["typeId"]=="120" || $values["typeId"]=="124"  || $values["typeId"]=="129"  || $values["typeId"]=="134" || $values["typeId"]=="121" || $values["typeId"]=="145" || $values["typeId"]=="146" || $values["typeId"]=="147") ) {
			$entity_name = "";
			$entity_text = "";
			$entity_arr = array();
			if($values["typeId"] == "999" || $values["typeId"] == "88" || $values["typeId"] == "89" ||  $values["typeId"]=="119" || $values["typeId"]=="120"  || $values["typeId"]=="129"){
				$parentId = \LookupTypeValues::where("name", "=", "PREPAID AGENTS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
						
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "prepaidagent";
				$entity_text = "prepaid agent";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "999";
			}
			if($values["typeId"] == "998"  || $values["typeId"]=="124"){
				$entities =  \CreditSupplier::All();
				$entity_arr = array();
				foreach ($entities as $entity){
					$entity_arr[$entity->id] = $entity->supplierName;
				}
				$entity_name = "creditsupplier";
				$entity_text = "creditsupplier name";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "998";
			}
			if($values["typeId"] == "997"  || $values["typeId"]=="134"){
				$entities =  \FuelStation::All();
				$entity_arr = array();
				foreach ($entities as $entity){
					$entity_arr[$entity->id] = $entity->name;
				}
				$entity_name = "fuelstation";
				$entity_text = "fuel station name";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "997";
			}
			if($values["typeId"] == "996" || $values["typeId"]=="147"){
				$entities =  \Loan::All();
				$entity_arr = array();
				foreach ($entities as $entity){
					$veh_arr = explode(",", $entity->vehicleId);
					$vehs = \Vehicle::whereIn("id",$veh_arr)->get();
					$veh_arr = "";
					foreach ($vehs as $veh){
						$veh_arr = $veh_arr.$veh->veh_reg.", ";
					}
					$entity_arr[$entity->id] = $entity->loanNo." - ".$veh_arr;
				}
				$entity_name = "loanpayment";
				$entity_text = "loan no";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "996";
			}
			if($values["typeId"] == "995"){
				$entities =  \OfficeBranch::All();
				$entity_arr = array();
				foreach ($entities as $entity){
					$entity_arr[$entity->id] = $entity->name;
				}
				$entity_name = "officebranch";
				$entity_text = "for office branch";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "995";
			}
			if($values["typeId"] == "994"){
				$entities =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
				$entity_arr = array();
				foreach ($entities as $entity){
					$entity_arr[$entity->id] = $entity->name;
				}
				$entity_name = "incharge";
				$entity_text = "incharge name";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "994";
			}
			if($values["typeId"] == "993"){
				$parentId = \LookupTypeValues::where("name", "=", "PREPAID AGENTS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
						
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "prepaidagent";
				$entity_text = "prepaid agent";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "993";
			}
			if($values["typeId"] == "992"){
				$parentId = \LookupTypeValues::where("name", "=", "ONLINE OPERATORS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "onlineoperators";
				$entity_text = "online operators";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "992";
			}
			if($values["typeId"] == "991" || $values["typeId"]=="121"){
				$qry = "select df.id as id, name, amountFinanced, installmentAmount, agmtDate, paidInstallments, totalInstallments from dailyfinances df, financecompanies f where df.financeCompanyId=f.id and df.deleted='No' order by name, agmtDate asc";
				$dailyfinances = \DB::select(\DB::raw($qry));
				$entity_arr = array();
				$dfName = '';
				$i  = 0;
				$loanNo= 0;
				foreach ($dailyfinances as $dailyfinance){
					$id = $dailyfinance->id;
					$name = $dailyfinance->name;
					$amountFinanced = $dailyfinance->amountFinanced;
					$paidInstallments = $dailyfinance->paidInstallments;
					$installmentAmount = $dailyfinance->installmentAmount;
					$eqry = "select sum(amount) as paidAmount from expensetransactions where entity='DAILY FINANCE PAYMENT' and entityValue=$id and status='ACTIVE'";
					$eresults = \DB::select(\DB::raw($eqry));
					$paidAmount = 0;
					if(count($eresults)>0){
						$erow = $eresults[0];
						$paidAmount = $erow->paidAmount;
					}
					if($paidAmount+($paidInstallments*$installmentAmount) >= $amountFinanced)
						continue;
					
					if($i == 0)
					{
						$dfName = $name;
						$loanNo = 1;
					}
					else if($dfName === $name)
					{
						$loanNo++;
					}
					else
					{
						$dfName = $name;
						$loanNo = 1;
					}
					$amountFinanced=$dailyfinance->amountFinanced;
					$installmentAmount=$dailyfinance->installmentAmount;
					$finName = $name.'-'.$amountFinanced.'-'.$installmentAmount.'- Loan No'.$loanNo;
					$i++;
					$entity_arr[$id] = $finName;
				}
				$entity_name = "dailyfinance";
				$entity_text = "daily finance ";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "991";
			}
			if($values["typeId"] == "108"){
				$parentId = \LookupTypeValues::where("name", "=", "PHONE NUMBERS (Company)")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
						
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "phonenumbers";
				$entity_text = "phone numbers";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "990";
			}
			if($values["typeId"] == "145"){
				$parentId = \LookupTypeValues::where("name", "=", "PF COMPANIES")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
			
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "pfcompany";
				$entity_text = "pf company";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "980";
			}
			if($values["typeId"] == "146"){
				$parentId = \LookupTypeValues::where("name", "=", "ESI COMPANIES")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
			
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "esicompany";
				$entity_text = "esi company";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "990";
			}
			$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
				$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
				$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"type", "value"=>$values["typeId"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			return view::make("transactions.paymentform",array("form_info"=>$form_info));
		}
		
		else if(isset($values["typeId"])) {
			$showfields = \LookupTypeValues::where("id", "=", $values["typeId"])->get();
			if(count($showfields)>0){
				$showfields = $showfields[0];
				$fields = explode(",", $showfields->fields);
				if(in_array("INCHARGE",$fields)){
					$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
					$incharges_arr = array();
					foreach ($incharges as $incharge){
						$incharges_arr[$incharge->id] = $incharge->name;
					}
					$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				if(in_array("VEHICLE",$fields)){
					$vehicles =  \Vehicle::All();
					$vehicles_arr = array();
					foreach ($vehicles as $vehicle){
						$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
					}
					$form_field = array("name"=>"vehicle[]", "content"=>"vehicle reg no", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
					$form_fields[] = $form_field;
				}
				if(in_array("BRANCH",$fields)){
					$branches =  \OfficeBranch::All();
					$branches_arr = array();
					foreach ($branches as $branch){
						$branches_arr[$branch->id] = $branch->name;
					}
					$form_field = array("name"=>"branch1", "content"=>"Branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$branches_arr);
					$form_fields[] = $form_field;
				}
				if(in_array("EMPLOYEE",$fields)){
					$employees =  \Employee::All();
					$employees_arr = array();
					foreach ($employees as $employee){
						$employees_arr[$employee->id] = $employee->empCode." - ".$employee->fullName;
					}
					$form_field = array("name"=>"employee", "content"=>"Employee", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$employees_arr);
					$form_fields[] = $form_field;
				}
				if(in_array("BANK",$fields)){
					$bankacts_arr = array();
					$bankacts =  \BankDetails::All();
					foreach ($bankacts as $bankact){
						$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
					}
					$form_field = array("name"=>"bankId", "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
					$form_fields[] = $form_field;
				}
			}
			$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
				$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
				$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"type", "value"=>$values["typeId"], "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			return view::make("transactions.paymentform",array("form_info"=>$form_info));
		}
		else{
			$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
				$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
				$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			return view::make("transactions.paymentform",array("form_info"=>$form_info));				
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageTransactions()
	{
		$values = Input::all();
		$values['bredcum'] = "INCOME/EXPENSE/FUEL TRANSACTIONS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;

		if(isset($values["transtype"]) && $values["transtype"]=="income"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "income&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
			
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="expense"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "expense&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
				
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('Branch', 'fuel station name', 'veh reg No', 'start reading', 'ltrs', 'full tank', 'incharge', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks',  "Actions");
			$values["theads"] = $theads;				
			$url = "fuel&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();;
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		
		$form_fields = array();
		
		$types_arr = array("Current"=>"Current","Mobile/Dongle"=>"Mobile/Dongle","Internet"=>"Internet","Water Cans/Tankers"=>"Water Cans/Tankers","Computer/Printer Purchases/Repairs"=>"Computer/Printer Purchases/Repairs");
		
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}		
		
		$val = "";	
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$form_info["form_fields"] = $form_fields;		
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('transactions.datatable', array("values"=>$values));
	}
	
	public function manageIncomeTransactions()
	{
		$values = Input::all();
		$values['bredcum'] = "INCOME TRANSACTIONS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		if(isset($values["transtype"]) && $values["transtype"]=="income"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "income&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
				
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="expense"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "expense&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
	
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();;
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
	
		$form_fields = array();
	
		$types_arr = array("Current"=>"Current","Mobile/Dongle"=>"Mobile/Dongle","Internet"=>"Internet","Water Cans/Tankers"=>"Water Cans/Tankers","Computer/Printer Purchases/Repairs"=>"Computer/Printer Purchases/Repairs");
	
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
	
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		$values["transtype"] = "income";
	
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('transactions.datatable', array("values"=>$values));
	}
	
	public function manageExpenseTransactions()
	{
		$values = Input::all();
		$values['bredcum'] = "EXPENSES TRANSACTIONS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		if(isset($values["transtype"]) && $values["transtype"]=="income"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "income&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
	
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="expense"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "expense&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
	
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
	
		$form_fields = array();
	
		$types_arr = array("Current"=>"Current","Mobile/Dongle"=>"Mobile/Dongle","Internet"=>"Internet","Water Cans/Tankers"=>"Water Cans/Tankers","Computer/Printer Purchases/Repairs"=>"Computer/Printer Purchases/Repairs");
	
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
	
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		$values["transtype"] = "expense";
	
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('transactions.datatable', array("values"=>$values));
	}
	
	public function manageFuelTransactions()
	{
		$values = Input::all();
		$values['bredcum'] = "FUEL TRANSACTIONS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		if(isset($values["transtype"]) && $values["transtype"]=="income"){
			$theads = array('Branch', 'fuel station name', 'veh reg No', 'start reading', 'ltrs', 'full tank', 'incharge', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks',  "Actions");
			$values["theads"] = $theads;
			$url = "income&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
	
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="expense"){
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "expense&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
	
		}
		else if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&";
			if(isset($values["branch1"])){
				$url = $url."branch1=".$values["branch1"];
			}
			if(isset($values["daterange"])){
				$url = $url."&daterange=".$values["daterange"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();;
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
	
		$form_fields = array();
	
		$types_arr = array("Current"=>"Current","Mobile/Dongle"=>"Mobile/Dongle","Internet"=>"Internet","Water Cans/Tankers"=>"Water Cans/Tankers","Computer/Printer Purchases/Repairs"=>"Computer/Printer Purchases/Repairs");
	
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
	
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->where("employee.status","=","ACTIVE")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		$values["transtype"] = "fuel";
	
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('transactions.datatable', array("values"=>$values));
	}
}
