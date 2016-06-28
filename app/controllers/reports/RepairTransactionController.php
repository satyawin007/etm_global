<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
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
			}
			catch(\Exception $ex){
				\Session::put("message","Add Purchase order : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$table = "CreditSupplierTransDetails"; 
				
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["creditSupplierTransId"] = $recid;
					$fields["repairedItem"] = $jsonitem->i5;
					$fields["quantity"] = $jsonitem->i1;
					$fields["amount"] = $jsonitem->i2;
					$fields["comments"] = $jsonitem->i3;
					$db_functions_ctrl->insert($table, $fields);
				}
				
			}
			catch(\Exception $ex){
				\Session::put("message","Add Purchased Item : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			\DB::commit();
		}
		\Session::put("message","Operation completed successfully!");
		return \Redirect::to($url);
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editRepairTransaction()
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
			$emps = \Vehicle::All();
			foreach ($emps as $emp){
				$veh_arr[$emp->id] = $emp->veh_reg;
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
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD"), "class"=>"form-control");
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
		$emps = \Vehicle::All();
		foreach ($emps as $emp){
			$veh_arr[$emp->id] = $emp->veh_reg;
		}
	
		$warehouse_arr = array();
		$warehouses = \OfficeBranch::all();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		$form_field = array("name"=>"creditsupplier", "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"Transaction date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"Vehicle", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$veh_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
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
		$form_field = array("name"=>"paymenttype", "content"=>"payment type", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments", "content"=>"comments", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"suspense", "content"=>"suspense", "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
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
		return View::make('transactions.purchaseorder', array("values"=>$values));
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
	
		$theads = array('Branch', 'Credit supplier', "date", "bill number", "payment paid", "payment Type", "total amount", "comments", "summary", "status", "Actions");
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
