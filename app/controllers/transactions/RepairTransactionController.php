<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
class RepairTransactionController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addRepairTransaction()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["Dsf"];
			$url = "repairtransactions";
			if(isset($values["clientname"])){
				$url = "repairtransactions?type=contracts";
			}
			$field_names = array("creditsupplier"=>"creditSupplierId","branch"=>"branchId","battapaidto"=>"battaEmployee", "paymenttype"=>"paymentType",
						"date"=>"date","billnumber"=>"billNumber","amountpaid"=>"paymentPaid","comments"=>"comments","totalamount"=>"amount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","vehicle"=>"vehicleId",
						"labourcharges"=>"labourCharges","electriciancharges"=>"electricianCharges","batta"=>"batta",
						"transactiondate"=>"transactionDate", "incharge"=>"inchargeId", "suspense"=>"suspense",
						"accountnumber"=>"accountNumber","bankname"=>"bankName","paymentdate"=>"paymentDate"
					);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "date" || $key == "date1" || $key == "issuedate" || $key == "transactiondate" || $key == "paymentdate" ){
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
			/* if(isset($values["clientname"]) && isset($values["depot"])){
				$contract = \Contract::where("clientId","=",$values["clientname"])->where("depotId","=",$values["depot"])->get();
				if(count($contract)>0){
					$contract = $contract[0];
					$fields["contractId"] = $contract->id;
				}
			} */
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "CreditSupplierTransactions"; 
			\DB::beginTransaction();
			$recid = "";
			try{
				$recid = $db_functions_ctrl->insertRetId($table, $fields);
				if(isset($values["incharge"]) && $values["incharge"]>0){
					$incharge_acct = \InchargeAccounts::where("empid","=",$values["incharge"])->first();
					$balance_amount = $incharge_acct->balance;
					$balance_amount = $balance_amount-$values["totalamount"];
					\InchargeAccounts::where("empid","=",$values["incharge"])->update(array("balance"=>$balance_amount));
				}
			}
			catch(\Exception $ex){
				\Session::put("message","Add Repaired Transaction : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$table = "CreditSupplierTransDetails";
					$fields = array();
					$fields["creditSupplierTransId"] = $recid;
					$fields["repairedItem"] = $jsonitem->i7;
					$fields["meeterReading"] = $jsonitem->i2;
					$fields["quantity"] = $jsonitem->i3;
					$fields["amount"] = $jsonitem->i4;
					$fields["comments"] = $jsonitem->i5;
					$fields["vehicleIds"] = $jsonitem->i8;
					$veh_arr = explode(",",$jsonitem->i8);
					$con_ids = "";
					foreach ($veh_arr as $veh){
						$contract_veh = \ContractVehicle::where("vehicleId","=",$veh)
										->where("status","=","ACTIVE")->get();
						if(count($contract_veh)>0){
							$contract_veh = $contract_veh[0];
							$con_ids = $con_ids.$contract_veh->contractId.",";
							$url = "repairtransactions?type=contracts";
						}
					}
					$fields["contractIds"] = $con_ids;
					$db_functions_ctrl->insert($table, $fields);
					$table = "CreditSupplierTransactions";
					$data = array("id"=>$recid);
					$fields = array();
					$fields["contractId"] = $con_ids;
					$db_functions_ctrl->update($table, $fields, $data);
				}
				
			}
			catch(\Exception $ex){
				\Session::put("message","Add Repaired Item : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			\DB::commit();
		}
		\Session::put("message","Operation completed successfully!");
		return \Redirect::to($url);
	}
	
	
	public function editRepairTransaction()
	{
		$values = Input::all();
	
		if (\Request::isMethod('post'))
		{
			//$values["sdf"];
			$url = "editrepairtransaction?id=".$values["id1"];
			$field_names = array("creditsupplier"=>"creditSupplierId","branch"=>"branchId","battapaidto"=>"battaEmployee", "paymenttype"=>"paymentType",
						"date"=>"date","billnumber"=>"billNumber","paymentpaid"=>"paymentPaid","comments"=>"comments","totalamount"=>"amount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","vehicle"=>"vehicleId",
						"labourcharges"=>"labourCharges","electriciancharges"=>"electricianCharges","batta"=>"batta",
						"transactiondate"=>"transactionDate", "incharge"=>"inchargeId", "suspense"=>"suspense",
						"accountnumber"=>"accountNumber","bankname"=>"bankName","paymentdate"=>"paymentDate"
					);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "date" || $key == "date1" || $key == "issuedate" || $key == "transactiondate" || $key == "paymentdate" ){
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
			/* if(isset($values["clientname"]) && isset($values["depot"])){
				$contract = \Contract::where("clientId","=",$values["clientname"])->where("depotId","=",$values["depot"])->get();
				if(count($contract)>0){
					$contract = $contract[0];
					$fields["contractId"] = $contract->id;
				}
			} */
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "CreditSupplierTransactions"; 
			\DB::beginTransaction();
			$recid = "";
			try{
				$recid = $db_functions_ctrl->update($table, $fields, array("id"=>$values["id1"]));
			}
			catch(\Exception $ex){
				\Session::put("message","Add Repaired Transaction : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$table = "CreditSupplierTransDetails"; 
				$table::where('creditSupplierTransId',"=", $values['id1'])->update(array("status"=>"DELETED"));
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
						$fields = array();
						$fields["creditSupplierTransId"] = $values['id1'];
						$fields["repairedItem"] = $jsonitem->i8;
						$fields["meeterReading"] = $jsonitem->i2;
						$fields["quantity"] = $jsonitem->i3;
						$fields["amount"] = $jsonitem->i4;
						$fields["comments"] = $jsonitem->i5;
						$fields["vehicleIds"] = $jsonitem->i9;
						$veh_arr = explode(",",$jsonitem->i9);
						$con_ids = "";
						foreach ($veh_arr as $veh){
							$contract_veh = \ContractVehicle::where("vehicleId","=",$veh)
											->where("status","=","ACTIVE")->get();
							if(count($contract_veh)>0){
								$contract_veh = $contract_veh[0];
								$con_ids = $con_ids.$contract_veh->contractId.",";
								$url = "repairtransactions?type=contracts";
							}
						}
						$fields["contractIds"] = $con_ids;
						$table = "CreditSupplierTransDetails";
						$db_functions_ctrl->insert($table, $fields);
						$table = "CreditSupplierTransactions";
						$data = array("id"=>$values['id1']);
						$fields = array();
						$fields["contractId"] = $con_ids;
						$db_functions_ctrl->update($table, $fields, $data);
				}
				
			}
			catch(\Exception $ex){
				\Session::put("message","Add Repaired Item : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			\DB::commit();
			\Session::put("message","Operation completed successfully!");
			return \Redirect::to($url);
		}
		
		$values['bredcum'] = "EDIT REPAIR TRANSACTION";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$theads = array('name', "type", "remarks", "status", "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "editrepairtransaction";
		$form_info["action"] = "editrepairtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "repairtransactions";
		$form_info["bredcum"] = "edit repairtransaction";
	
		$form_fields = array();
	
		$types =  \InventoryLookupValues::where("parentId", "=", 0)->get();
		$types_arr = array();
		foreach ($types as $type){
			$types_arr[$type->id] = $type->name;
		}
		$val = "";
		if(!isset($values["type"])){
			$values["type"] = "-1";
		}
	
		$entity = \CreditSupplierTransactions::where("id","=",$values['id'])->get();
		if(count($entity)>0){
			$entity = $entity[0];
			$types =  \InventoryLookupValues::where("parentId", "=", 0)->get();
			$types_arr = array();
			foreach ($types as $type){
				$types_arr[$type->id] = $type->name;
			}
			$val = "";
			if(!isset($values["type"])){
				$values["type"] = "-1";
			}
			
			$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
			->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
			$incharges_arr = array();
			foreach ($incharges as $incharge){
				$incharges_arr[$incharge->id] = $incharge->name;
			}
			
			$select_args =  array();
			$select_args[] = "cities.name as name";
			$select_args[] = "creditsuppliers.supplierName as supplierName";
			$select_args[] = "creditsuppliers.id as id";
		
			$credit_sup_arr = array();
			$credit_sups = \CreditSupplier::leftjoin("cities","cities.id","=","creditsuppliers.cityId")->select($select_args)->get();
			foreach ($credit_sups as $credit_sup){
				$credit_sup_arr[$credit_sup->id] = $credit_sup->supplierName."-".$credit_sup->name;
			}
			$emp_arr = array();
			$emps = \Employee::where("roleId","!=","19")->orWhere("roleId","!=","20")->get();
			foreach ($emps as $emp){
				$emp_arr[$emp->id] = $emp->fullName;
			}
			
			$veh_arr = array();
			$vehs = AppSettingsController::getNonContractVehicles();
			foreach ($vehs as $veh){
				$veh_arr[$veh['id']] = $veh['veh_reg'];
			}
		
			$warehouse_arr = array();
			$warehouses = AppSettingsController::getEmpBranches();
			foreach ($warehouses as $warehouse){
				$warehouse_arr[$warehouse["id"]] = $warehouse["name"];
			}
			$form_payment_fields= array();
			$form_field = array("name"=>"creditsupplier", "id"=>"creditsupplier", "value"=>$entity->creditSupplierId, "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billnumber", "id"=>"billnumber", "value"=>$entity->billNumber, "content"=>"bill number", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "value"=>$entity->suspense, "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billfile", "content"=>"upload bill", "value"=>$entity->filePath,  "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "id"=>"branch", "value"=>$entity->branchId, "content"=>"branch", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"date", "id"=>"date", "value"=>date("d-m-Y", strtotime($entity->date)), "content"=>"Transaction date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$veh_arr = array();
			/* if($entity->contractId>0){
				$veh_arr = array();
				$ass_clientbranches = \Auth::user()->contractIds;
				$ass_clientbranches = explode(",", $ass_clientbranches);
				$contracts_vehs = \ContractVehicle::where("contract_vehicles.contractId", "=", $entity->contractId)
							->where("contract_vehicles.status","=","ACTIVE")
							->join("contracts","contract_vehicles.contractId","=","contracts.id")
							->join("vehicle","contract_vehicles.vehicleId","=","vehicle.id")
							->select(array("vehicle.id as id", "vehicle.veh_reg as veh_reg"))->get();
				foreach ($contracts_vehs as $contracts_veh){
					$veh_arr[$contracts_veh->id] = $contracts_veh->veh_reg;
				}
				$clients =  AppSettingsController::getEmpClients();
				$clients_arr = array();
				foreach ($clients as $client){
					$clients_arr[$client['id']] = $client['name'];
				}
				$contract = \Contract::where("id","=",$entity->contractId)->first();
				$form_field = array("name"=>"clientname", "id"=>"clientname", "value"=>$contract->clientId, "content"=>"client name", "readonly"=>"",  "required"=>"", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
				$form_fields[] = $form_field;
				$depots =  \Depot::where("id","=",$contract->depotId)->get();
				$depots_arr = array();
				foreach ($depots as $depot){
					$depots_arr[$depot['id']] = $depot['name'];
				}
				$form_field = array("name"=>"depot", "id"=>"depot", "content"=>"depot/branch name", "readonly"=>"", "value"=>$contract->depotId, "required"=>"", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>$depots_arr);
				$form_fields[] = $form_field;
			} */
			
			/* $form_field = array("name"=>"vehicle", "id"=>"vehicle", "value"=>$entity->vehicleId, "content"=>"Vehicle", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$veh_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field; */
			$form_field = array("name"=>"labourcharges", "id"=>"labourcharges", "value"=>$entity->labourCharges, "content"=>"labour charges", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"electriciancharges", "id"=>"electriciancharges", "value"=>$entity->electricianCharges, "content"=>"electrician charges", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"batta", "id"=>"batta", "value"=>$entity->batta, "content"=>"batta", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"battapaidto", "id"=>"", "value"=>$entity->battaEmployee, "content"=>"batta paid to", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"enableincharge", "id"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"incharge", "id"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"),  "options"=>$incharges_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"inchargebalance", "id"=>"inchargebalance", "value"=>"", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymentpaid", "id"=>"paymentPaid", "value"=>$entity->paymentPaid, "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			if($entity->paymentPaid == "No"){
				$entity->paymentType = "";
			}
			$pmtdate = date("d-m-Y",strtotime($entity->paymentDate));
			if($pmtdate=="00-00-0000" || $pmtdate=="01-01-1970"){
				$pmtdate = "";
			}
			$form_field = array("name"=>"paymentdate","id"=>"paymentdate", "content"=>"Payment date", "value"=>$pmtdate,"readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
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
				$form_field = array("name"=>"issuedate","value"=>$entity->issueDate, "content"=>"issue date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"transactiondate", "value"=>$entity->transactionDate, "content"=>"transaction date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
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
				$form_field = array("name"=>"issuedate","value"=>$entity->issueDate, "content"=>"issue date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_payment_fields[] = $form_field;
				$form_field = array("name"=>"transactiondate", "value"=>$entity->transactionDate, "content"=>"transaction date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
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
			$form_field = array("name"=>"comments", "id"=>"", "value"=>$entity->comments, "content"=>"comments", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"totalamount", "id"=>"", "value"=>$entity->amount, "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "id"=>"", "value"=>$entity->id, "content"=>"", "readonly"=>"", "required"=>"required","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
	
			$form_info["form_fields"] = $form_fields;
			$form_info["form_payment_fields"] = $form_payment_fields;
			$values["form_info"] = $form_info;
			
			
			$form_info = array();
			$form_info["name"] = "edit";
			$form_info["action"] = "#";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			
			$parentId = -1;
			$types =  \LookupTypeValues::where("name", "=", "VEHICLE REPAIRS")->get();
			if(count($types)>0){
				$parentId = $types[0];
				$parentId = $parentId->id;
			}
			$items_arr = array();
			$items = \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
			foreach ($items as $item){
				$items_arr[$item->id] = $item->name;
			}
			
			if($entity->contractId != "" && $entity->contractId != 0){
				$veh_arr = array();
				$ass_clientbranches = \Auth::user()->contractIds;
				$ass_clientbranches = explode(",", $ass_clientbranches);
				
				$ass_clientbranches = \Auth::user()->contractIds;
				if(\Auth::user()->contractIds == "0" || \Auth::user()->contractIds == ""){
					$contracts_vehs = \ContractVehicle::where("contract_vehicles.status","=","ACTIVE")
											->join("vehicle","contract_vehicles.vehicleId","=","vehicle.id")
											->select(array("vehicle.id as id", "vehicle.veh_reg as veh_reg"))->get();
				}
				else{
					$ass_clientbranches = explode(",", $ass_clientbranches);
					$contracts_vehs = \ContractVehicle::whereIn("contracts.depotId",$ass_clientbranches)
											->where("contract_vehicles.status","=","ACTIVE")
											->join("contracts","contract_vehicles.contractId","=","contracts.id")
											->join("vehicle","contract_vehicles.vehicleId","=","vehicle.id")
											->select(array("vehicle.id as id", "vehicle.veh_reg as veh_reg"))->get();
				}
				foreach ($contracts_vehs as $contracts_veh){
					$veh_arr[$contracts_veh->id] = $contracts_veh->veh_reg;
				}
				$clients =  AppSettingsController::getEmpClients();
				$clients_arr = array();
				foreach ($clients as $client){
					$clients_arr[$client['id']] = $client['name'];
				}
				/* $form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
				 $form_fields[] = $form_field;
				 $form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
				 $form_fields[] = $form_field; */
			}
			else{
				$contracts_vehs = \Vehicle::where("status","=","ACTIVE")->get();
				foreach ($contracts_vehs as $contracts_veh){
					$veh_arr[$contracts_veh->id] = $contracts_veh->veh_reg;
				}
			}
			
			
			$item_info_arr = array("1"=>"info1","2"=>"info2");
			$form_fields = array();
			$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr,  "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehicles", "content"=>"Vehicle", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$veh_arr,"multiple"=>"multiple", "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"meeterreading", "content"=>"meeter reading", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			$modals[] = $form_info;
	
			$values["provider"] = "purchasedorder";
	
			$values["modals"] = $modals;
			return View::make('transactions.editrepairtransaction', array("values"=>$values));
		}
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editRepairTransaction1()
	{
		$values = Input::all();
		
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
// 			/$values["SDf"];
			$url = "repairtransactions";
			$field_names = array("creditsupplier"=>"creditSupplierId","branch"=>"branchId","battapaidto"=>"battaEmployee", "paymenttype"=>"paymentType",
						"date"=>"date","billnumber"=>"billNumber","amountpaid"=>"paymentPaid","comments"=>"comments","totalamount"=>"amount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","vehicle"=>"vehicleId",
						"labourcharges"=>"labourCharges","electriciancharges"=>"electricianCharges","batta"=>"batta",
						"transactiondate"=>"transactionDate", "incharge"=>"inchargeId", "suspense"=>"suspense", "accountnumber"=>"accountNumber","bankname"=>"bankName"
					);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "date" || $key == "date1" || $key == "issuedate" || $key == "transactiondate"){
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
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\CreditSupplierTransactions";
			
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editrepairtransaction?id=".$values['id1']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editrepairtransaction?id=".$values['id1']);
			}
		}
		$form_info = array();
		$form_info["name"] = "editrepairtransaction";
		$form_info["action"] = "editrepairtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "repairtransactions";
		$form_info["bredcum"] = "edit repairtransaction";
	
		$entity = \CreditSupplierTransactions::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$types =  \InventoryLookupValues::where("parentId", "=", 0)->get();
			$types_arr = array();
			foreach ($types as $type){
				$types_arr[$type->id] = $type->name;
			}
			$val = "";
			if(!isset($values["type"])){
				$values["type"] = "-1";
			}
			$select_args =  array();
			$select_args[] = "cities.name as name";
			$select_args[] = "creditsuppliers.supplierName as supplierName";
			$select_args[] = "creditsuppliers.id as id";
		
			$credit_sup_arr = array();
			$credit_sups = \CreditSupplier::leftjoin("cities","cities.id","=","creditsuppliers.cityId")->select($select_args)->get();
			foreach ($credit_sups as $credit_sup){
				$credit_sup_arr[$credit_sup->id] = $credit_sup->supplierName."-".$credit_sup->name;
			}
			$emp_arr = array();
			$emps = \Employee::where("roleId","!=","19")->orWhere("roleId","!=","20")->get();
			foreach ($emps as $emp){
				$emp_arr[$emp->id] = $emp->fullName;
			}
			
			$veh_arr = array();
			$vehs = AppSettingsController::getNonContractVehicles();
			foreach ($vehs as $veh){
				$veh_arr[$veh['id']] = $veh['veh_reg'];
			}
		
			$warehouse_arr = array();
			$warehouses = \OfficeBranch::all();
			foreach ($warehouses as $warehouse){
				$warehouse_arr[$warehouse->id] = $warehouse->name;
			}
			
			$form_field = array("name"=>"creditsupplier", "id"=>"creditsupplier", "value"=>$entity->creditSupplierId, "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billnumber", "id"=>"billnumber", "value"=>$entity->billNumber, "content"=>"bill number", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "value"=>$entity->suspense, "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billfile", "content"=>"upload bill", "value"=>$entity->filePath,  "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch", "id"=>"branch", "value"=>$entity->branchId, "content"=>"branch", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"date", "id"=>"date", "value"=>date("d-m-Y", strtotime($entity->date)), "content"=>"Transaction date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehicle", "id"=>"vehicle", "value"=>$entity->vehicleId, "content"=>"Vehicle", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$veh_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"labourcharges", "id"=>"labourcharges", "value"=>$entity->labourCharges, "content"=>"labour charges", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"electriciancharges", "id"=>"electriciancharges", "value"=>$entity->electricianCharges, "content"=>"electrician charges", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"batta", "id"=>"batta", "value"=>$entity->batta, "content"=>"batta", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"battapaidto", "id"=>"", "value"=>$entity->battaEmployee, "content"=>"batta paid to", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amountpaid", "id"=>"", "value"=>$entity->paymentPaid, "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"comments", "id"=>"", "value"=>$entity->comments, "content"=>"comments", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"totalamount", "id"=>"", "value"=>$entity->amount, "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "id"=>"", "value"=>$entity->id, "content"=>"", "readonly"=>"", "required"=>"required","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
		
			$form_info["form_fields"] = $form_fields;
			return View::make("transactions.edit2colmodalform",array("form_info"=>$form_info));
		}
	}
	
		
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageAddedPurchaseOrders()
	{
		$values = Input::all();
		$values['bredcum'] = "PURCHASE ORDER";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$theads = array('name', "type", "remarks", "status", "Actions");
		$values["theads"] = $theads;
				
		$form_info = array();
		$form_info["name"] = "addpurchaseorder";
		$form_info["action"] = "addpurchaseorder";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "#";
		$form_info["bredcum"] = "add inventory lookup value";
		$form_info["addlink"] = "addparent";
		
		$form_fields = array();
		
		$types =  \InventoryLookupValues::where("parentId", "=", 0)->get();
		$types_arr = array();
		foreach ($types as $type){
			$types_arr[$type->id] = $type->name;
		}
		$val = "";
		if(!isset($values["type"])){
			$values["type"] = "-1";
		}
		
		$credit_sup_arr = array();
		$credit_sups = \CreditSupplier::All();
		foreach ($credit_sups as $credit_sup){
			$credit_sup_arr[$credit_sup->id] = $credit_sup->supplierName;
		}
		$emp_arr = array();
		$emps = \Employee::where("roleId","!=","19")->orWhere("roleId","!=","20")->get();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName;
		}
		
		$warehouse_arr = array();
		$warehouses = \OfficeBranch::where("isWareHouse","=","Yes")->get();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
		
		$form_field = array("name"=>"creditsupplier", "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"warehouse", "content"=>"warehouse", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"receivedby", "content"=>"received by", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"orderdate", "content"=>"order date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments", "content"=>"comments", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		
		$values["form_info"] = $form_info;
		
		$form_info = array();
		
		$form_info["name"] = "edit";
			
		$form_info["action"] = "#";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$items_arr = array();
		$items = \Items::where("status","=","ACTIVE")->get();
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$item_info_arr = array("1"=>"info1","2"=>"info2");
		$form_fields = array();
		$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr, "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"iteminfo", "content"=>"manufacturer", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array(),  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"unitprice", "content"=>"price of unit", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "content"=>"status", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array("New"=>"New","Old"=>"Old"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;		
		$modals[] = $form_info;
		
		$values["provider"] = "purchasedorder";
		
		$values["modals"] = $modals;
		return View::make('inventory.purchaseorder', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function createRepairTransaction()
	{
		//$values["test"];
		$values = Input::all();
		$values['bredcum'] = "REPAIR TRANSACTION";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$theads = array('name', "type", "remarks", "status", "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "addrepairtransaction";
		$form_info["action"] = "addrepairtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "#";
		$form_info["bredcum"] = "add inventory lookup value";
		$form_info["addlink"] = "addparent";
	
		$form_fields = array();
	
		$types =  \InventoryLookupValues::where("parentId", "=", 0)->get();
		$types_arr = array();
		foreach ($types as $type){
			$types_arr[$type->id] = $type->name;
		}
		$val = "";
		if(!isset($values["type"])){
			$values["type"] = "-1";
		}
		$select_args =  array();
		$select_args[] = "cities.name as name";
		$select_args[] = "creditsuppliers.supplierName as supplierName";
		$select_args[] = "creditsuppliers.id as id";
	
		$credit_sup_arr = array();
		$credit_sups = \CreditSupplier::leftjoin("cities","cities.id","=","creditsuppliers.cityId")->select($select_args)->get();
		foreach ($credit_sups as $credit_sup){
			$credit_sup_arr[$credit_sup->id] = $credit_sup->supplierName."-".$credit_sup->name;
		}
		$emp_arr = array();
		$emps = \Employee::where("roleId","!=","19")->orWhere("roleId","!=","20")->get();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName;
		}
		
		$veh_arr = array();
		$vehs = AppSettingsController::getNonContractVehicles();
		foreach ($vehs as $veh){
			$veh_arr[$veh['id']] = $veh['veh_reg'];
		}
	
		$warehouse_arr = array();
		$warehouses = \OfficeBranch::all();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
						->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		
		$form_field = array("name"=>"creditsupplier", "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branchname", "content"=>"branch", "readonly"=>"readonly", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"Transaction date", "readonly"=>"readonly", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$veh_arr = array();
			$ass_clientbranches = \Auth::user()->contractIds;
			if(\Auth::user()->contractIds == "0" || \Auth::user()->contractIds == ""){
				$contracts_vehs = \ContractVehicle::where("contract_vehicles.status","=","ACTIVE")
							->join("vehicle","contract_vehicles.vehicleId","=","vehicle.id")
							->select(array("vehicle.id as id", "vehicle.veh_reg as veh_reg"))->get();
			}
			else{
				$ass_clientbranches = explode(",", $ass_clientbranches);
				$contracts_vehs = \ContractVehicle::whereIn("contracts.depotId",$ass_clientbranches)
								->where("contract_vehicles.status","=","ACTIVE")
								->join("contracts","contract_vehicles.contractId","=","contracts.id")	
								->join("vehicle","contract_vehicles.vehicleId","=","vehicle.id")
								->select(array("vehicle.id as id", "vehicle.veh_reg as veh_reg"))->get();
			}
			foreach ($contracts_vehs as $contracts_veh){
				$veh_arr[$contracts_veh->id] = $contracts_veh->veh_reg;
			}
			$clients =  AppSettingsController::getEmpClients();
			$clients_arr = array();
			foreach ($clients as $client){
				$clients_arr[$client['id']] = $client['name'];
			}
			/* $form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
			$form_fields[] = $form_field; */
		}
		/* $form_field = array("name"=>"vehicle", "content"=>"Vehicle", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$veh_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field; */
		$form_field = array("name"=>"labourcharges", "content"=>"labour charges", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"electriciancharges", "content"=>"electrician charges", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"batta", "content"=>"batta", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"battapaidto", "content"=>"batta paid to", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "content"=>"payment type", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"),  "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentdate", "content"=>"Payment date", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"inchargebalance", "value"=>"", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments", "content"=>"comments", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branch", "content"=>"", "value"=>"0", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
	
		$values["form_info"] = $form_info;
	
		$form_info = array();
	
		$form_info["name"] = "edit";
			
		$form_info["action"] = "#";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$parentId = -1;
		$types =  \LookupTypeValues::where("name", "=", "VEHICLE REPAIRS")->get();
		if(count($types)>0){
			$parentId = $types[0];
			$parentId = $parentId->id;
		}
		$items_arr = array();
		$items = \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$item_info_arr = array("1"=>"info1","2"=>"info2");
		$form_fields = array();
		$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr,  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicles", "content"=>"Vehicle", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$veh_arr, "multiple"=>"multiple", "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"meeterreading", "content"=>"meeter reading", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
	
		$values["provider"] = "purchasedorder";
	
		$values["modals"] = $modals;
		return View::make('transactions.addrepairtransaction', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function createPurchaseOrder()
	{
		$values = Input::all();
		$values['bredcum'] = "PURCHASE ORDER";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$theads = array('name', "type", "remarks", "status", "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "addpurchaseorder";
		$form_info["action"] = "addpurchaseorder";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "#";
		$form_info["bredcum"] = "add inventory lookup value";
		$form_info["addlink"] = "addparent";
	
		$form_fields = array();
	
		$types =  \InventoryLookupValues::where("parentId", "=", 0)->get();
		$types_arr = array();
		foreach ($types as $type){
			$types_arr[$type->id] = $type->name;
		}
		$val = "";
		if(!isset($values["type"])){
			$values["type"] = "-1";
		}
	
		$credit_sup_arr = array();
		$credit_sups = \CreditSupplier::All();
		foreach ($credit_sups as $credit_sup){
			$credit_sup_arr[$credit_sup->id] = $credit_sup->supplierName;
		}
		$emp_arr = array();
		$emps = \Employee::where("roleId","!=","19")->orWhere("roleId","!=","20")->get();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName;
		}
	
		$warehouse_arr = array();
		$warehouses = \OfficeBranch::where("isWareHouse","=","Yes")->get();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
	
		$form_field = array("name"=>"creditsupplier", "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"warehouse", "content"=>"warehouse", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"receivedby", "content"=>"received by", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"orderdate", "content"=>"order date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments", "content"=>"comments", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
	
		$values["form_info"] = $form_info;
	
		$form_info = array();
	
		$form_info["name"] = "edit";
			
		$form_info["action"] = "#";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$items_arr = array();
		$items = \Items::where("status","=","ACTIVE")->get();
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$item_info_arr = array("1"=>"info1","2"=>"info2");
		$form_fields = array();
		$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr, "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"iteminfo", "content"=>"manufacturer", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array(),  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"unitprice", "content"=>"price of unit", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "content"=>"status", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array("New"=>"New","Old"=>"Old"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
	
		$values["provider"] = "purchasedorder";
	
		$values["modals"] = $modals;
		return View::make('inventory.purchaseorder', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageRepairTransactions()
	{
		$values = Input::all();
		$values['bredcum'] = "REPAIR TRANSACTION";
		$values['home_url'] = '#';
		$values['add_url'] = 'addvehicle';
		$values['form_action'] = 'vehicles';
		$values['action_val'] = '';
	
		$action_val = "";
		$links = array();
		$values['action_val'] = $action_val;
		$values['links'] = $links;
		
		$values['create_link'] = array("href"=>"createrepairtransaction","text"=>"CREATE REPAIR TRANSACTION");
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$theads = array('Branch', 'Credit supplier', "date", "bill number", "payment paid", "payment Type", "total amount", "comments", "summary", "status", 'created by', 'wf status', 'wf updated By', 'wf_remarks', "Actions");
		}
		else {
			$theads = array('Branch', 'Credit supplier', "date", "bill number", "payment paid", "payment Type", "total amount", "comments", "summary", "status", 'created by', 'wf status', 'wf updated By', 'wf_remarks', "Actions");
		}
		$values["theads"] = $theads;
	
		//Code to add modal forms
		$modals =  array();
			
		$form_info = array();
		$form_info["name"] = "block";
		$form_info["action"] = "blockvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
	
		$form_fields = array();
		$form_field = array("name"=>"vehreg", "content"=>"Veh Reg No", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"blockeddate", "content"=>"blocked date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "readonly"=>"", "content"=>"remarks", "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
			
		$form_info = array();
		$form_info["name"] = "sell";
		$form_info["action"] = "sellvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
			
		$form_fields = array();
		$form_field = array("name"=>"vehreg1", "content"=>"Veh Reg No", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"soldto", "content"=>"sold to", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"address", "readonly"=>"", "content"=>"address", "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalcost", "content"=>"total cost", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paidamount", "content"=>"paid amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"solddate", "content"=>"sold date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id2", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "readonly"=>"", "content"=>"remarks", "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
	
		$values["provider"] = "vehicle_repairs";
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$values["provider"]  = $values["provider"]."&type=contracts";
		}
		return View::make('transactions.repairsdatatable', array("values"=>$values));
	}
	
	public function getManufacturers(){
		$values = Input::all();
		$itemid = $values["itemid"];
		$man = \Items::where("id","=",$itemid)->first();
		$mans = "";
		$mans_arr = explode(",",$man->manufactures);
		foreach ($mans_arr as $man){
			if($man != "") {
				$manId = $man;
				$man = \Manufacturers::where("id","=",$man)->get();
				$man = $man[0];
				$man = $man->name;
				$mans = $mans."<option value='".$manId."' >".$man."</option>";
			}
		}
		echo $mans;
	}
	
	public function deleteRepairTransaction(){
		$values = Input::all();
		$itemid = $values["id"];
		$fields = array("deleted"=>"Yes");
		$data = array('id'=>$values['id']);
		$db_functions_ctrl = new DBFunctionsController();
		$table = "\CreditSupplierTransactions";
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
		}
		else{
			echo "fail";
		}
	}
}
