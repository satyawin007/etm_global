<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
use masters\OfficeBranchController;
class TransactionController extends \Controller {
	private static function getEntityNames(){
		$entity_names_arr = array();
		$entity_names_arr["998"] = "CREDIT SUPPLIER PAYMENT";
		$entity_names_arr["124"] = "CREDIT SUPPLIER PAYMENT";
		$entity_names_arr["997"] = "FUEL STATION PAYMENT";
		$entity_names_arr["134"] = "FUEL STATION PAYMENT";
		$entity_names_arr["996"] = "LOAN PAYMENT";
		$entity_names_arr["996"] = "LOAN PAYMENT";
		$entity_names_arr["147"] = "LOAN PAYMENT";
		$entity_names_arr["147"] = "LOAN PAYMENT";
		$entity_names_arr["336"] = "LOAN INTEREST PAYMENT";
		$entity_names_arr["995"] = "RENT";
		$entity_names_arr["994"] = "INCHARGE ACCOUNT CREDIT";
		$entity_names_arr["993"] = "PREPAID RECHARGE";
		$entity_names_arr["992"] = "ONLINE OPERATORS";
		$entity_names_arr["991"] = "DAILY FINANCE PAYMENT";
		$entity_names_arr["121"] = "DAILY FINANCE PAYMENT";
		$entity_names_arr["283"] = "CREDIT CARD PAYMENT";
		$entity_names_arr["108"] = "PHONE NUMBERS (Company)";
		$entity_names_arr["119"] = "PREPAID AGENTS";
		$entity_names_arr["145"] = "PF PAYMENT";
		$entity_names_arr["146"] = "ESI PAYMENT";
		$entity_names_arr["339"] = "VENDOR PAYMENT EXPENSE";
		$entity_names_arr["340"] = "VENDOR PAYMENT INCOME";
		$entity_names_arr["342"] = "GLOBAL LOAN ISSUE";
		$entity_names_arr["343"] = "GLOBAL LOAN RETURN";
		$entity_names_arr["350"] = "OTHER CLIENT";
		$entity_names_arr["355"] = "LATE FEE/OTHER CHARGES";
		return $entity_names_arr;
	}
	
	private static function getEntityFields(){
		$entity_fields_arr = array();
		$entity_fields_arr[] = "late_fee_charges";
		$entity_fields_arr[] = "other_client";
		$entity_fields_arr[] = "loaninterestpayment";
		$entity_fields_arr[] = "global_loan_issue";
		$entity_fields_arr[] = "global_loan_return";
		$entity_fields_arr[] = "vendor_payment_expense";
		$entity_fields_arr[] = "vendor_payment_income";
		$entity_fields_arr[] = "credit_card_payment";
		$entity_fields_arr[] = "pfcompany";
		$entity_fields_arr[] = "esicompany";
		$entity_fields_arr[] = "prepaidagent";
		$entity_fields_arr[] = "creditsupplier"; 
		$entity_fields_arr[] = "fuelstation";
		$entity_fields_arr[] = "loanpayment";
		$entity_fields_arr[] = "officebranch";
		$entity_fields_arr[] = "prepaidagent";
		$entity_fields_arr[] = "onlineoperators";
		$entity_fields_arr[] = "dailyfinance";
		$entity_fields_arr[] = "credit_card_payment";
		$entity_fields_arr[] = "phonenumbers";
		return $entity_fields_arr;
	}
	
	public function postFile(){
		$values = Input::All();
		if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
			$fields = array();
			$destinationPath = storage_path().'/uploads/'; // upload path
			$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
			$fileName = uniqid().'.'.$extension; // renameing image
			Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
			$fields["filePath"] = $fileName;
			$table = $values["table"];
			if($table == "ExpenseTransaction" || $table == "IncomeTransaction"){
				$table::where("transactionId","=",$values['id'])->update($fields);
			}
			else{
				$table::where("id","=",$values['id'])->update($fields);
			}
		}
	}
	
	
	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addTransaction()
	{
		if (\Request::isMethod('post'))
		{
			//$val["dsaf"];
			$values = Input::all();	
			if(isset($values["transtype"]) && $values["transtype"] == "income" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds", "bankId"=>"bankId",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","billno"=>"billNo",
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
				$expenses_arr = TransactionController::getEntityNames();
				$field_names = TransactionController::getEntityFields();
				foreach ($field_names as $field_name){
					if(isset($values[$field_name])){
						$fields["entity"] = $expenses_arr[$values["type"]];
						$fields["entityValue"] = $values[$field_name];
					}
				}
				
				$fields["contractId"] = 0;
				if(isset($values["vehicleno"])){
					$contract_veh = \ContractVehicle::where("id","=",$values["vehicleno"])->get();
					if(count($contract_veh)>0){
						$contract_veh = $contract_veh[0];
						$fields["contractId"] = $contract_veh->contractId;
						$fields["vehicleId"] = $contract_veh->vehicleId;;
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
				$ret_id = 0;
				$ret_id = $db_functions_ctrl->insertRetId($table, $fields);
				if($ret_id != ""){
					$json_resp = array("status"=>"success","id"=>$ret_id, "table"=>$table, "message"=>"Operation completed Successfully");
					echo json_encode($json_resp);
					return;
				}
				else{
					$json_resp = array("status"=>"fail","message"=>"Operation Could not be completed, Try Again!");
					echo json_encode($json_resp);
					return;
				}
			}
			if(isset($values["transtype"]) && $values["transtype"] == "expense" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId", "meeterreading"=>"meeterReading",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds", "bankId"=>"bankId", "next_alert_date"=>"nextAlertDate",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","billno"=>"billNo",
						"transactiondate"=>"transactionDate","suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName","entity_date"=>"entityDate"
				);
				$fields = array();
				$expenses_arr = TransactionController::getEntityNames() ;
				
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate" || $key=="next_alert_date" || $key=="entity_date"){
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
				if(isset($values["meeterreading"])){
					if($fields["remarks"] != "" && strlen($fields["remarks"])>0){
						$fields["remarks"] = $fields["remarks"]."<br/>Meeter Reading : ".$values["meeterreading"];
					}
					else{
						$fields["remarks"] = $fields["remarks"]."Meeter Reading : ".$values["meeterreading"];
					}
				}
				$field_names = TransactionController::getEntityFields();
				foreach ($field_names as $field_name){
					if(isset($values[$field_name])){
						unset($fields["type"]);
						$fields["entity"] = $expenses_arr[$values["type"]];
						$fields["entityValue"] = $values[$field_name];
					}
				}				
				$fields["contractId"] = 0;
				if(isset($values["vehicleno"])){
					$contract_veh = \ContractVehicle::where("id","=",$values["vehicleno"])->get();
					if(count($contract_veh)>0){
						$contract_veh = $contract_veh[0];
						$fields["contractId"] = $contract_veh->contractId;
						$fields["vehicleId"] = $contract_veh->vehicleId;;
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
				
				if($values["type"] == 265){
					$from_incharge = "";
					$to_incharge = "";
					if(isset($fields["inchargeId"]) && $fields["inchargeId"]>0){
						$from_incharge = \Employee::where("id","=",$fields["inchargeId"])->first();
						$from_incharge = $from_incharge->fullName;
					}
					if(isset($fields["employeeId"]) && $fields["employeeId"]>0){
						$to_incharge = \Employee::where("id","=",$fields["employeeId"])->first();
						$to_incharge = $to_incharge->fullName;
					}
					$br = "<br/>";
					if($fields["remarks"] == ""){
						$br="";
					}
					$fields["remarks"] = $fields["remarks"].$br."AMOUNT PAID BY INCHARGE ".$from_incharge." TO INCHARGE ".$to_incharge;
				}
				$ret_id = 0;
				$ret_id = $db_functions_ctrl->insertRetId($table, $fields);
				if($ret_id != ""){
					if(isset($values["incharge"]) && $values["incharge"]>0){
						if($values["type"] == 251){
							$incharge_acct = \InchargeAccounts::where("empid","=",$values["incharge"])->first();
							$balance_amount = $incharge_acct->balance;
							$balance_amount = $balance_amount+$values["amount"];
							\InchargeAccounts::where("empid","=",$values["incharge"])->update(array("balance"=>$balance_amount));
						}
						else if($values["type"] == 265){
							$incharge_acct = \InchargeAccounts::where("empid","=",$values["incharge"])->first();
							$balance_amount = $incharge_acct->balance;
							$balance_amount = $balance_amount-$values["amount"];
							\InchargeAccounts::where("empid","=",$values["incharge"])->update(array("balance"=>$balance_amount));
							if(count($incharge_acct)>0){
								$transid =  strtoupper(uniqid().mt_rand(100,999));
								$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
								foreach($chars as $k=>$v){
									$transid = str_replace($k, $v, $transid);
								}
								$fields["transactionId"] = $transid;
								$fields["source"] = "income transaction";
								$from_incharge = "";
								$to_incharge = "";
								if(isset($fields["inchargeId"]) && $fields["inchargeId"]>0){
									$from_incharge = \Employee::where("id","=",$fields["inchargeId"])->first();
									$from_incharge = $from_incharge->fullName;
								}
								if(isset($fields["employeeId"]) && $fields["employeeId"]>0){
									$to_incharge = \Employee::where("id","=",$fields["employeeId"])->first();
									$to_incharge = $to_incharge->fullName;
								}
								$fields["remarks"] = "AMOUNT PAID BY INCHARGE ".$from_incharge." TO INCHARGE ".$to_incharge;
								$ret_id = $db_functions_ctrl->insertRetId("\IncomeTransaction", $fields);
									
								$temp_incharge = $fields["inchargeId"];
								$fields["inchargeId"] = $fields["employeeId"];
								$fields["employeeId"] = $temp_incharge;
								$transid =  strtoupper(uniqid().mt_rand(100,999));
								$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
								foreach($chars as $k=>$v){
									$transid = str_replace($k, $v, $transid);
								}
								$fields["transactionId"] = $transid;
								$fields["source"] = "expense transaction";
								
								$from_incharge = \Employee::where("id","=",$fields["employeeId"])->first();
								$from_incharge = $from_incharge->fullName;
								$to_incharge = \Employee::where("id","=",$fields["inchargeId"])->first();
								$to_incharge = $to_incharge->fullName;								
								$fields["remarks"] = "AMOUNT PAID BY INCHARGE ".$from_incharge." TO INCHARGE ".$to_incharge;
								
								$ret_id = $db_functions_ctrl->insertRetId($table, $fields);
								$incharge_acct = \InchargeAccounts::where("empid","=",$values["employee"])->get();
								
								$incharge_acct = $incharge_acct[0];
								$balance_amount = $incharge_acct->balance;
								$balance_amount = $balance_amount+$values["amount"];
								\InchargeAccounts::where("empid","=",$values["employee"])->update(array("balance"=>$balance_amount));
							}
						}
						else{
							$incharge_acct = \InchargeAccounts::where("empid","=",$values["incharge"])->first();
							$balance_amount = $incharge_acct->balance;
							$balance_amount = $balance_amount-$values["amount"];
							\InchargeAccounts::where("empid","=",$values["incharge"])->update(array("balance"=>$balance_amount));
						}
					}
					$json_resp = array("status"=>"success","id"=>$ret_id, "table"=>$table, "message"=>"Operation completed Successfully");
					echo json_encode($json_resp);
					return;
				}
				else{
					$json_resp = array("status"=>"fail","message"=>"Operation Could not be completed, Try Again!");
					echo json_encode($json_resp);
					return;
				}
			}
			if(isset($values["transtype"]) && $values["transtype"] == "fuel" ){
				if(isset($values["date"]) && $values["date"] == ""){
					$values["date"] = date("d-m-Y");
				}
				$field_names = array("branch"=>"branchId","totalamount"=>"amount","indentno"=>"indentNo","paymenttype"=>"paymentType", "vehicleno"=>"vehicleId","incharge"=>"inchargeId", "type"=>"name",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","tripid"=>"tripId",
						"fuelstationname"=>"fuelStationId","startreading"=>"startReading","litres"=>"litres","billno"=>"billNo",
						"paymentpaid"=>"paymentPaid","bankaccount"=>"bankAccountId","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense", "date"=>"date", "filleddate"=>"filledDate", "accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date" || $key=="filleddate" || $key=="issuedate"){
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
				if(isset($values["vehicleno"])){
					$contract_veh = \ContractVehicle::where("id","=",$values["vehicleno"])->get();
					if(count($contract_veh)>0){
						$contract_veh = $contract_veh[0];
						$fields["contractId"] = $contract_veh->contractId;
						$fields["vehicleId"] = $contract_veh->vehicleId;;
					}
				}
				if(isset($values["fulltank"]) && $values["fulltank"] == "YES"){
					$fields["fullTank"] = "YES";
				}
				else{
					$fields["fullTank"] = "NO";
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
				
				$recs = $table::where("filledDate","=",$fields["filledDate"])
								->where("vehicleId","=",$fields["vehicleId"])
								->where("startReading","=",$fields["startReading"])
								->where("status","=","ACTIVE")
								->get();
				if(count($recs)>0){
					$json_resp = array("status"=>"fail","message"=>"Duplicate Entry, Try with different values Again!");
					echo json_encode($json_resp);
					return;
				}
				$ret_id = 0;
				if(($ret_id=$db_functions_ctrl->insertRetId($table, $fields))>0){
					if(isset($values["incharge"]) && $values["incharge"]>0){
						$incharge_acct = \InchargeAccounts::where("empid","=",$values["incharge"])->first();
						$balance_amount = $incharge_acct->balance;
						$balance_amount = $balance_amount-$values["totalamount"];
						\InchargeAccounts::where("empid","=",$values["incharge"])->update(array("balance"=>$balance_amount));
					}
					$json_resp = array("status"=>"success","id"=>$ret_id, "table"=>$table, "message"=>"Operation completed Successfully");
					echo json_encode($json_resp);
					return;
				}
				else{
					$json_resp = array("status"=>"fail","message"=>"Operation Could not be completed, Try Again!");
					echo json_encode($json_resp);
					return;
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
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds","billno"=>"billNo",
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
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds","billno"=>"billNo",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","next_alert_date"=>"nextAlertDate",
						"transactiondate"=>"transactionDate","suspense"=>"suspense", "date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName", "entity_date"=>"entityDate"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate" || $key=="next_alert_date" || $key=="entity_date"){
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
				$field_names = array("branch"=>"branchId","totalamount"=>"amount","indentno"=>"indentNo","paymenttype"=>"paymentType", "vehicleno"=>"vehicleId","incharge"=>"inchargeId", "type"=>"name",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","tripid"=>"tripId",
						"fuelstationname"=>"fuelStationId","startreading"=>"startReading","litres"=>"litres","billno"=>"billNo","fulltank"=>"fullTank",
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
				
				$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
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
					$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
					$incharges_arr = array();
					foreach ($incharges as $incharge){
						$incharges_arr[$incharge->id] = $incharge->name;
					}
					$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				if($entity->vehicleIds != ""){
					$vehicles_arr = array();
					$vehs = AppSettingsController::getNonContractVehicles();
					foreach ($vehs as $veh){
						$vehicles_arr[$veh['id']] = $veh['veh_reg'];
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
				$form_field = array("name"=>"date1", "id"=>"transactiondate",  "value"=>date("d-m-Y",strtotime($entity->date)), "content"=>"transaction date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "value"=>$entity->suspense, "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"amount", "id"=>"amount",  "value"=>$entity->amount, "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billno", "value"=>$entity->billNo, "id"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
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
					$bankacts =  \BankDetails::where("Status","=","ACTIVE")->get();
					$bankacts_arr = array();
					foreach ($bankacts as $bankact){
						$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
					$form_info["form_fields"] = $form_fields;
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
				if(true){
					$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
					$incharges_arr = array();
					foreach ($incharges as $incharge){
						$incharges_arr[$incharge->id] = $incharge->name;
					}
					$form_field = array("name"=>"enableincharge", "id"=>"enableincharge","content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
					$form_fields[] = $form_field;
					$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"inchargebalance", "value"=>"",  "content"=>"Incharge balance", "value"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				if($entity->vehicleIds != ""){
					$vehicles_arr = array();
					$vehs = AppSettingsController::getNonContractVehicles();
					foreach ($vehs as $veh){
						$vehicles_arr[$veh['id']] = $veh['veh_reg'];
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
				if($entity->nextAlertDate != "0000-00-00" && $entity->nextAlertDate != "01-01-1970"){
					$form_field = array("name"=>"next_alert_date", "id"=>"next_alert_date", "value"=>date("d-m-Y",strtotime($entity->nextAlertDate)), "content"=>"next alert date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
					$form_fields[] = $form_field;
				}
				else{
					$form_field = array("name"=>"next_alert_date", "id"=>"next_alert_date", "value"=>"", "content"=>"next alert date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"date1", "id"=>"transactiondate",  "value"=>$entity->date, "content"=>"transaction date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "value"=>$entity->suspense, "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"amount", "id"=>"amount",  "value"=>$entity->amount, "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billno", "value"=>$entity->billNo, "id"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
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
					$bankacts =  \BankDetails::where("Status","=","ACTIVE")->get();
					$bankacts_arr = array();
					foreach ($bankacts as $bankact){
						$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
					$form_info["form_fields"] = $form_fields;
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
				
				$vehicles_arr = array();
				$vehs = AppSettingsController::getNonContractVehicles();
				foreach ($vehs as $veh){
					$vehicles_arr[$veh['id']] = $veh['veh_reg'];
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
				
				$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
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
				$form_field = array("name"=>"date", "value"=>date("d-m-Y",strtotime($entity->filledDate)), "id"=>"date", "content"=>"filled date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"previousreading","value"=>"", "content"=>"previous reading", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"startreading", "value"=>$entity->startReading, "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calculateMilage()"), "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"litres", "value"=>$entity->litres, "content"=>"litres", "readonly"=>"",  "required"=>"required", "type"=>"text",  "action"=>array("type"=>"onChange","script"=>"calculateMilage()"), "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"priceperlitre", "value"=>($entity->amount/$entity->litres), "content"=>"price per litre", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calcTotal()"), "class"=>"form-control number");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"fulltank", "value"=>$entity->fullTank, "content"=>"full tank", "readonly"=>"",  "required"=>"","type"=>"radio", "class"=>"form-control","options"=>array("YES"=>"YES", "NO"=>"NO"));
				$form_fields[] = $form_field;
				$form_field = array("name"=>"totalamount", "value"=>$entity->amount, "id"=>"totalamount", "content"=>"total amount", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;				
				$form_field = array("name"=>"mileage","value"=>"", "content"=>"mileage", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billno", "value"=>$entity->billNo, "id"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"indentno", "value"=>$entity->indentNo, "id"=>"indentno", "content"=>"indent no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"enableincharge", "id"=>"enableincharge","content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "value"=>$entity->suspense,  "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"inchargebalance", "value"=>"",  "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
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
					$bankacts =  \BankDetails::where("Status","=","ACTIVE")->get();
					$bankacts_arr = array();
					foreach ($bankacts as $bankact){
						$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
					}
					$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
					$form_fields[] = $form_field;
					$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
					$form_info["form_fields"] = $form_fields;
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
				$form_field = array("name"=>"vehicleno", "id"=>"vehicleno", "value"=>$entity->vehicleId, "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
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
	
	public function getInchargeBalance()
	{
		$values = Input::all();
		$incharge_balance = 0;
		$incharge = \InchargeAccounts::where("empid","=",$values["id"])->get();
		if(count($incharge)>0){
			$incharge = $incharge[0];
			$incharge_balance = $incharge->balance;
		}
		echo $incharge_balance;
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
// 			$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
// 			$form_fields[] = $form_field;
// 			$form_field = array("name"=>"accountnumber", "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
// 			$form_fields[] = $form_field;
			$bankacts =  \BankDetails::where("Status","=","ACTIVE")->get();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
			}
			$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
		}
		if(isset($values["paymenttype"]) && ($values["paymenttype"] === "credit_card")){
			$bankacts =  \Cards::where("cardType","=","CREDIT CARD")->where("Status","=","ACTIVE")->get();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->cardNumber." (".$bankact->cardHolderName.")";
			}
			$form_field = array("name"=>"bankaccount", "content"=>"card number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
		}
		if(isset($values["paymenttype"]) && ($values["paymenttype"] === "debit_card")){
			$bankacts =  \Cards::where("cardType","=","DEBIT CARD")->where("Status","=","ACTIVE")->get();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->cardNumber." (".$bankact->cardHolderName.")";
			}
			$form_field = array("name"=>"bankaccount", "content"=>"card number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;  
		}
		return view::make("transactions.paymentform",array("form_info"=>$form_info));
	}
	
	public function getMastersPaymentFields()
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
		if(isset($values["paymenttype"]) && $values["paymenttype"] === "dd"){
			$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"ddnumber", "content"=>"dd number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"issuedate", "content"=>"issue date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
		}
		if(isset($values["paymenttype"]) && ($values["paymenttype"] === "ecs" || $values["paymenttype"] === "neft" || $values["paymenttype"] === "rtgs")){
			$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"accountnumber", "content"=>"account number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;  
		}
		if(isset($values["paymenttype"]) && ($values["paymenttype"] === "credit_card")){
			$bankacts =  \Cards::where("cardType","=","CREDIT CARD")->where("Status","=","ACTIVE")->get();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->cardNumber." (".$bankact->cardHolderName.")";
			}
			$form_field = array("name"=>"bankaccount", "content"=>"card number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;  
		}
		if(isset($values["paymenttype"]) && ($values["paymenttype"] === "debit_card")){
		$bankacts =  \Cards::where("cardType","=","DEBIT CARD")->where("Status","=","ACTIVE")->get();
			$bankacts_arr = array();
			foreach ($bankacts as $bankact){
				$bankacts_arr[$bankact->id] = $bankact->cardNumber." (".$bankact->cardHolderName.")";
			}
			$form_field = array("name"=>"bankaccount", "content"=>"card number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"chequenumber", "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields; 
		}
		return view::make("masters.layouts.paymentform",array("form_info"=>$form_info));
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
		$select_fields = array();
		$select_fields[] = "fuelstationdetails.name as name";
		$select_fields[] = "cities.name as cityname";
		$select_fields[] = "fuelstationdetails.id as id";
		$fuelstations =  null;
		$status = "ACTIVE";
		if(isset($values["vehiclestatus"]) && $values["vehiclestatus"]=="INACTIVE"){
			$status = "INACTIVE";
		}
		if(isset($values["client"]) && isset($values["clientbranch"])){
			$vehicles =  \ContractVehicle::where("contract_vehicles.status","=",$status)
							->where("contracts.clientId","=",$values["client"])
							->where("contracts.depotId","=",$values["clientbranch"])
							->join("contracts","contracts.id","=","contract_vehicles.contractId")
							->join("vehicle","vehicle.id","=","contract_vehicles.vehicleId")
							->select("contract_vehicles.id as id","vehicle.veh_reg as veh_reg")
							->groupBy("vehicle.id")->get();
			$vehicles_arr = array();
			foreach ($vehicles as $vehicle){
				$vehicles_arr[$vehicle['id']] = $vehicle->veh_reg;
			}
			$client_stateid = \Depot::where("id","=",$values["clientbranch"])->first();
			$client_stateid = $client_stateid->stateId;
			$fuelstations = \FuelStation::where("fuelstationdetails.stateId","=",$client_stateid)
								->where("fuelstationdetails.status","=","ACTIVE")
								->leftjoin("cities","cities.id","=","fuelstationdetails.cityId")
								->select($select_fields)->get();
		}
		else{
			$vehicles_arr = array();
			$vehs = AppSettingsController::getNonContractVehicles();
			foreach ($vehs as $veh){
				$vehicles_arr[$veh['id']] = $veh['veh_reg'];
			}
			$branch_stateid = \OfficeBranch::where("id","=",$values["branch"])->first();
			$branch_stateid = $branch_stateid->stateId;
			$fuelstations = \FuelStation::where("fuelstationdetails.stateId","=",$branch_stateid)
								->where("fuelstationdetails.status","=","ACTIVE")
								->leftjoin("cities","cities.id","=","fuelstationdetails.cityId")
								->select($select_fields)->get();
		}
		
		$form_field = array("name"=>"", "value"=>"", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"hidden", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"", "value"=>"", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"hidden", "class"=>"form-control number");
		$form_fields[] = $form_field;
		
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
			$form_field = array("name"=>"vehicleno", "content"=>"vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getendreading()"), "required"=>"required", "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
		}
		else{
			$form_field = array("name"=>"vehicleno", "content"=>"vehicle number", "readonly"=>"", "action"=>array("type"=>"onchange","script"=>"getendreading()"), "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
		}
		/*
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange","script"=>"changeState(this.value)"), "options"=>$state_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		*/
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
							->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		$form_field = array("name"=>"filleddate", "content"=>"filled date", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"getpreviouslogs(this.value)"), "required"=>"", "type"=>"text",  "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$fuelstations_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"previousreading", "content"=>"previous reading", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calculateMilage()"), "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"litres", "content"=>"litres", "readonly"=>"",  "required"=>"required", "type"=>"text",  "action"=>array("type"=>"onChange","script"=>"calculateMilage()"), "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"priceperlitre", "content"=>"price per litre", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calcTotal()"), "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fulltank", "content"=>"full tank", "readonly"=>"",  "required"=>"","type"=>"radio", "class"=>"form-control","options"=>array("YES"=>"YES", "NO"=>"NO"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"mileage", "content"=>"mileage", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"indentno", "content"=>"indent no", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"),  "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"inchargebalance", "value"=>"", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentpaid", "value"=>"No", "content"=>"payment paid", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"YES","No"=>"NO"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
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
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
							->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		if(isset($values["typeId"]) && ($values["typeId"]>900 || $values["typeId"]=="88" || $values["typeId"]=="89" || 
				 $values["typeId"]=="108" || $values["typeId"]=="119" || $values["typeId"]=="120" || 
				 $values["typeId"]=="124" || $values["typeId"]=="129" || $values["typeId"]=="134" || 
				 $values["typeId"]=="121" || $values["typeId"]=="145" || $values["typeId"]=="146" || 
				 $values["typeId"]=="147" || $values["typeId"]=="283" || $values["typeId"]=="336" || 
				 $values["typeId"]=="339" || $values["typeId"]=="340" || $values["typeId"]=="342" || 
				 $values["typeId"]=="343" || $values["typeId"]=="350" ||  $values["typeId"]=="355") ) {
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
				//$values["typeId"] = "999";
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
				//$values["typeId"] = "998";
			}
			if($values["typeId"]=="283"){
				$entities =  \Cards::where("cardType","=","CREDIT CARD")->where("status","=","ACTIVE")->get();
				$entity_arr = array();
				foreach ($entities as $entity){
					$entity_arr[$entity->id] = $entity->cardNumber." (".$entity->cardHolderName.")";
				}
				$entity_name = "credit_card_payment";
				$entity_text = "credit card payment";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
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
				//$values["typeId"] = "997";
			}
			if($values["typeId"] == "996" || $values["typeId"]=="147" || $values["typeId"]=="336" || $values["typeId"]=="355"){
				$entities =  \Loan::leftJoin("financecompanies","financecompanies.id","=","loans.financeCompanyId")
								->where("loans.status","=","ACTIVE")->select(array("loans.id","loans.loanNo","loans.purpose","loans.vehicleId","financecompanies.name as finName"))->get();
				$entity_arr = array();
				
				foreach ($entities as $entity){
					$veh_arr = explode(",", $entity->vehicleId);
					$vehs = \Vehicle::whereIn("id",$veh_arr)->get();
					$veh_arr = "";
					foreach ($vehs as $veh){
						$veh_arr = $veh_arr.$veh->veh_reg.", ";
					}
					$entity_arr[$entity->id] = $entity->loanNo."-".$entity->purpose." (".$entity->finName.")"." - ".$veh_arr;
				}
				if($values["typeId"]=="336"){
					$entity_name = "loaninterestpayment";
				}
				if($values["typeId"]=="355"){
					$entity_name = "late_fee_charges";
				}
				else{
					$entity_name = "loanpayment";
				}
				
				$entity_text = "loan no";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"entity_date", "content"=>"for the month of", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				//$values["typeId"] = $values["typeId"];
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
				//$values["typeId"] = "995";
			}
			if($values["typeId"] == "994"){
				$entities =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
				$entity_arr = array();
				foreach ($entities as $entity){
					$entity_arr[$entity->id] = $entity->name;
				}
				$entity_name = "incharge";
				$entity_text = "incharge name";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				//$values["typeId"] = "994";
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
				//$values["typeId"] = "993";
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
				//$values["typeId"] = "992";
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
				$entity_text = "daily finance";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				//$values["typeId"] = "991";
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
				//$values["typeId"] = "108";
			}
			if($values["typeId"] == "989"){
				$parentId = \LookupTypeValues::where("name", "=", "VEHICLE RENEWALS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "vehiclerenewals";
				$entity_text = "Vehicle Renewals";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"next_alert_date", "content"=>"next alert date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				
				if(isset($values["type"]) && $values["type"]=="contracts"){
				}
				else{
					$vehicles_arr = array();
					$vehs = AppSettingsController::getNonContractVehicles();
					foreach ($vehs as $veh){
						$vehicles_arr[$veh['id']] = $veh['veh_reg'];
					}
					$form_field = array("name"=>"vehicle", "content"=>"vehicle reg no", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
					$form_fields[] = $form_field;
				}
				//$values["typeId"] = "989";
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
				//$values["typeId"] = "145";
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
				//$values["typeId"] = "146";
			}
			if($values["typeId"] == "339" || $values["typeId"] == "340" ){
				$parentId = \LookupTypeValues::where("name", "=", "VENDORS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
						
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				if($values["typeId"] == "339"){
					$entity_name = "vendor_payment_expense";
					$values["typeId"] = "339";
				}
				else{
					$entity_name = "vendor_payment_income";
					$values["typeId"] = "340";
				}
				$entity_text = "vendor";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				//$values["typeId"] = "339";
			}
			if($values["typeId"] == "342" || $values["typeId"] == "343"){
				$parentId = \LookupTypeValues::where("name", "=", "GLOBAL LOANS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
			
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				
				if($values["typeId"] == "342"){
					$entity_name = "global_loan_issue";
					$values["typeId"] = "342";
				}
				else{
					$entity_name = "global_loan_return";
					$values["typeId"] = "343";
				}
				$entity_text = "global loan";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
			}
			if($values["typeId"] == "350"){
				$parentId = \LookupTypeValues::where("name", "=", "OTHER CLIENTS")->get();
				$incomes = array();
				if(count($parentId)>0){
					$parentId = $parentId[0];
					$parentId = $parentId->id;
					$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
						
				}
				foreach ($incomes as $income){
					$entity_arr[$income->id] = $income->name;
				}
				$entity_name = "other_client";
				$entity_text = "other client";
				$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
				$form_fields[] = $form_field;
				$values["typeId"] = "350";
			}
			$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
// 				$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
// 				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			if($values["transtype"] == "expense"){
// 				$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
// 				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
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
					$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
					$incharges_arr = array();
					foreach ($incharges as $incharge){
						$incharges_arr[$incharge->id] = $incharge->name;
					}
					$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				if(in_array("VEHICLE",$fields)){
					if(isset($values["type"]) && $values["type"]=="contracts"){
					}
					else{
						$vehicles_arr = array();
						$vehs = AppSettingsController::getNonContractVehicles();
						foreach ($vehs as $veh){
							$vehicles_arr[$veh['id']] = $veh['veh_reg'];
						}
						$form_field = array("name"=>"vehicle[]", "content"=>"vehicle reg no", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
						$form_fields[] = $form_field;						
					}
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
						$incharge_act = \InchargeAccounts::where("empid","=",$employee->id)->where("status","=","Active")->get();
						if(count($incharge_act)>0){
							$employees_arr[$employee->id] = $employee->empCode." - ".$employee->fullName." (INCHARGE)";
						}
						else{
							$employees_arr[$employee->id] = $employee->empCode." - ".$employee->fullName;
						}
					}
					if(isset($values["typeId"]) && $values["typeId"]==265){
						$form_field = array("name"=>"employee", "content"=>"To Employee/Incharge", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$employees_arr);
						$form_fields[] = $form_field;
					}
					else{
						$form_field = array("name"=>"employee", "content"=>"employee", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$employees_arr);
						$form_fields[] = $form_field;
					}
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
				if(in_array("NEXT ALERT DATE",$fields)){
					$form_field = array("name"=>"next_alert_date", "content"=>"NEXT ALERT DATE", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
					$form_fields[] = $form_field;
				}
				if(isset($values["typeId"]) && $values["typeId"]==305){
					$form_field = array("name"=>"emiamount", "content"=>"emi amount", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
					$form_fields[] = $form_field;
					$form_field = array("name"=>"emi_paid_date", "content"=>"emi paid DATE", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
					$form_fields[] = $form_field;
				}
			}
			$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;

			$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
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
				if(isset($values["typeId"]) && $values["typeId"]==265){
					$form_field = array("name"=>"incharge", "content"=>"From Incharge", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				else{
					$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
					$form_fields[] = $form_field;
				}
				$form_field = array("name"=>"inchargebalance", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
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
			$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
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
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
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
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$values['bredcum'] = "CONTRACT INCOME TRANSACTIONS";
		}
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		
		$theads = array('trans Id', 'branch/Contract', 'transaction name', 'date', 'amount', 'payment type', 'bill no', 'remarks', 'created by', 'wf status', 'wf updated By', 'wf_remarks',  "Actions");
		$values["theads"] = $theads;
		$url = "income";
		$values["provider"]= $url;
		
		$form_fields = array();
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$clients =  AppSettingsController::getEmpClients();
			$clients_arr = array();
			foreach ($clients as $client){
				$clients_arr[$client['id']] = $client['name'];
			}
			$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehiclestatus", "content"=>"vehicles contract status", "readonly"=>"",  "required"=>"", "type"=>"radio", "class"=>"form-control chosen-select", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getContractVehicles(this.id);"), "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehicleno", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field;
				
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type',  'bill no',  'remarks', 'created by', 'wf status', 'wf updated By', 'wf_remarks',  "Actions");
			$values["theads"] = $theads;
			$url = "income&type=contracts&contracts=true";
			$values["provider"]= $url;
		}
		$values["transtype"] = "income";
		$val = "";
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
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$values['bredcum'] = "CONTRACT EXPENSE TRANSACTIONS";
		}
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		
		$theads = array('trans Id', 'branch/Contract', 'transaction name', 'date', 'amount', 'payment type', 'bill No', 'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks', "Actions");
		$values["theads"] = $theads;
		$url = "expense";
		$values["provider"]= $url;
		
		$form_fields = array();
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$clients =  AppSettingsController::getEmpClients();
			$clients_arr = array();
			foreach ($clients as $client){
				$clients_arr[$client['id']] = $client['name'];
			}
			$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehiclestatus", "content"=>"vehicles contract status", "readonly"=>"",  "required"=>"", "type"=>"radio", "class"=>"form-control chosen-select", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getContractVehicles(this.id);"), "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehicleno", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field;
			$form_field = array("name"=>"meeterreading", "content"=>"meeter reading (if any)", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
				
			$theads = array('trans Id', 'branch', 'transaction name', 'date', 'amount', 'payment type', 'bill no',  'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks', "Actions");
			$values["theads"] = $theads;
			$url = "expense&type=contracts&contracts=true";
			$values["provider"]= $url;
		}
		$values["transtype"] = "expense";
		$val = "";
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
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$values['bredcum'] = "CONTRACT FUEL TRANSACTIONS";
		}
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$form_info['iscontractfuel'] = "YES";
		}
		
		$theads = array('Branch', 'fuel station name', 'veh reg No', 'start reading', 'ltrs', 'full tank', 'mileage', 'incharge', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks',  "Actions");
		$values["theads"] = $theads;
		$url = "fuel";
		$values["provider"]= $url;
	
		$form_fields = array();
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$clients =  AppSettingsController::getEmpClients();
			$clients_arr = array();
			foreach ($clients as $client){
				$clients_arr[$client['id']] = $client['name'];
			}
			$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehiclestatus", "content"=>"vehicles contract status", "readonly"=>"",  "required"=>"", "type"=>"radio", "class"=>"form-control chosen-select", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getContractFuelFields('fuel');"), "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field;
			$theads = array('Contract', 'fuel station name', 'veh reg No', 'start reading', 'ltrs', 'full tank', 'mileage', 'incharge', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks',  "Actions");
			//$theads = array('Contract', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', 'created by', 'wf status', 'wf updated By',  'wf_remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&type=contracts&contracts=true";
			$values["provider"]= $url;
		}
	
		$types_arr = array("Current"=>"Current","Mobile/Dongle"=>"Mobile/Dongle","Internet"=>"Internet","Water Cans/Tankers"=>"Water Cans/Tankers","Computer/Printer Purchases/Repairs"=>"Computer/Printer Purchases/Repairs");
	
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
	
	public function getEndReading()
	{
		$values = Input::all();
		$json_resp = array();
		$entity = \ServiceLog::join("contract_vehicles","contract_vehicles.id","=","service_logs.contractVehicleId")
					->where("contract_vehicles.id","=",$values['id'])
					->where("substituteVehicleId","=",0)
					->where("serviceDate","<",date("Y-m-d",strtotime($values['date'])))
					->orderBy("serviceDate","asc")->get();
		$json_resp["endReading"] = 0;
		if(count($entity)>0){
			$len = count($entity);
			$entity = $entity[$len-1];
			$json_resp["endReading"] = $entity->endReading;
		}
		else{
			$con_veh_id = \ContractVehicle::where("contract_vehicles.id","=",$values['id'])->first();
			$con_veh_id = $con_veh_id->vehicleId;
			$meeters = \VehicleMeeter::where("vehicleId","=",$con_veh_id)->where("status","=","ACTIVE")->first();
			if(count($meeters)>0){
				$json_resp["endReading"] = $meeters->startReading;
			}
		}
		echo json_encode($json_resp);
	}
	
	public function getPreviousLogs()
	{	
		$values = Input::all();
		$json_resp = array();
		$table = "";
		$con_veh_id = \ContractVehicle::where("contract_vehicles.id","=",$values['vehicleid'])->first();
		$con_veh_id = $con_veh_id->vehicleId;
		$entities = \FuelTransaction::where("filledDate","<",date("Y-m-d",strtotime($values['date'])))
								->where("fueltransactions.vehicleId","=",$con_veh_id)
								->where("fueltransactions.status","=","ACTIVE")
								->where("fullTank","=","Yes")
								->limit(6)->orderBy("filledDate","desc")
								->select(array("filledDate","startReading","litres","amount","fullTank"))->get();
		$i=0;
		$cnt = count($entities);
		if((count($entities)>5)){
			$cnt = 5;
		}
		for($i=0; $i<$cnt; $i++){
			$table = $table."<tr>";
			$table = $table."<td>".date("d-m-Y",strtotime($entities[$i]->filledDate))."</td>";
			$table = $table."<td>".$entities[$i]->startReading."</td>";
			$table = $table."<td>".$entities[$i]->litres."</td>";
			$table = $table."<td>".$entities[$i]->amount."</td>";
			$table = $table."<td>".$entities[$i]->fullTank."</td>";
			if ($i+1 < $cnt){
				$table = $table."<td>".round((($entities[$i]->startReading-$entities[$i+1]->startReading)/$entities[$i]->litres), 2)."</td>";
			}
			else{
				"<td>0</td>";
			}
			$table = $table."</tr>";
		}
		echo $table;
	}
	
	public function getVehicleLastReading()
	{
		$values = Input::all();
		$json_resp = array();
		$reading = 0;
		$con_veh_id = \ContractVehicle::where("contract_vehicles.id","=",$values['vehicleId'])->first();
		$con_veh_id = $con_veh_id->vehicleId;
		$entities = \FuelTransaction::where("filledDate","<",date("Y-m-d",strtotime($values['date'])))
						->where("vehicleId","=",$con_veh_id)
						->where("status","=","ACTIVE")
						->where("fullTank","=","YES")
						->limit(6)->orderBy("filledDate","desc")->get();
		if(count($entities)>0){
			$entities = $entities[0];
			$reading = $entities->startReading;
		}
		echo $reading;
	}
}
