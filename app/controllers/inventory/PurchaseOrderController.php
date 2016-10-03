<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
class PurchaseOrderController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addPurchaseOrder()
	{
		if (\Request::isMethod('post'))
		{
			//$values["DSF"];
			$values = Input::all();
			$url = "purchaseorder";
			$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
						"orderdate"=>"orderDate","paymentdate"=>"paymentDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","incharge"=>"inchargeId",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
					);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "orderdate" || $key == "paymentdate" || $key == "date1" || $key == "issuedate" || $key == "transactiondate"){
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
// 			if(isset($values["incharge"]) && $values["incharge"]>0){
// 				DB::statement(DB::raw("CALL update_incharge_amount(".$values["incharge"].", -".$values["totalamount"].");"));
// 			}
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			if(isset($values["stocktype"]) && $values["stocktype"]=="OFFICE"){
				$fields["type"] = "OFFICE PURCHASE ORDER";
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "PurchasedOrders";
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
				\Session::put("message","Add Purchase order : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$table = "PurchasedItems";
				
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["purchasedOrderId"] = $recid;
					$fields["itemId"] = $jsonitem->i7;
					$fields["itemTypeId"] = $jsonitem->i8;
					$fields["manufacturerId"] = $jsonitem->i9;
					$fields["itemNumbers"] = $jsonitem->i3;
					$fields["itemNumbersAll"] = $jsonitem->i3;
					$fields["qty"] = $jsonitem->i4;
					$fields["purchasedQty"] = $jsonitem->i4;
					$fields["unitPrice"] = $jsonitem->i5;
					$fields["itemStatus"] = $jsonitem->i6;
					$db_functions_ctrl->insert($table, $fields);
				}				
			}
			catch(\Exception $ex){
				\Session::put("message","Add Purchase Item : Operation Could not be completed, Try Again!");
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
	public function editPurchaseOrder1()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
						"orderdate"=>"orderDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate","date1"=>"date","suspense"=>"suspense","accountnumber"=>"accountNumber","bankname"=>"bankName"
					);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "orderdate" || $key == "date1" || $key == "issuedate" || $key == "transactiondate"){
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
			if(!isset($values["suspense"])){
				$fields["suspense"] = "No";
			}
			$data = array('id'=>$values['id']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\PurchasedOrders"; 
			
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editpurchaseorder?id=".$values['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editpurchaseorder?id=".$values['id']);
			}
		}
		$form_info = array();
		$form_info["name"] = "editpurchaseorder";
		$form_info["action"] = "editpurchaseorder";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "purchaseorder";
		$form_info["bredcum"] = "edit purchaseorder";
	
		$entity = \PurchasedOrders::where("id","=",$values['id'])->get();
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
			$form_fields = array();
			$form_field = array("name"=>"creditsupplier", "id"=>"creditsupplier", "value"=>$entity->creditSupplierId, "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"warehouse", "id"=>"warehouse", "value"=>$entity->officeBranchId, "content"=>"warehouse", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"receivedby", "id"=>"receivedby", "value"=>$entity->receivedBy, "content"=>"received by", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"orderdate", "id"=>"orderdate", "value"=> date("d-m-Y",strtotime($entity->orderDate)), "content"=>"order date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billnumber", "id"=>"billnumber", "value"=>$entity->billNumber, "content"=>"bill number", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"suspense", "content"=>"suspense", "value"=>$entity->suspense, "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"billfile", "content"=>"upload bill",  "value"=>$entity->filePath, "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amountpaid", "id"=>"amountpaid", "value"=>$entity->amountPaid, "content"=>"amount paid", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"comments", "id"=>"comments", "value"=>$entity->comments, "content"=>"comments", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"totalamount", "id"=>"totalamount", "value"=>$entity->totalAmount, "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id, "content"=>"", "readonly"=>"", "required"=>"required","type"=>"hidden", "class"=>"form-control ");
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
		$warehouses = AppSettingsController::getEmpBranches();
		foreach ($warehouses as $warehouse){
			$branch = \OfficeBranch::where("id","=",$warehouse["id"])->first();
			if($branch->isWareHouse == "Yes"){
				$warehouse_arr[$warehouse["id"]] = $warehouse["name"];
			}
		}
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
									->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		
		$form_field = array("name"=>"warehouse", "content"=>"warehouse", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "action"=>array("type"=>"onChange","script"=>"getCreditSupplierByState(this.value)"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"creditsupplier", "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"receivedby", "content"=>"received by", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$incharges_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"orderdate", "content"=>"order date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "action"=>array("type"=>"onChange","script"=>"verifyBillNo(this.value)"), "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "content"=>"payment type", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"),  "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentdate", "content"=>"payment paid date", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
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
		$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$stocktype = "NON OFFICE";
		if(isset($values["stocktype"]) && $values["stocktype"]=="office"){
			$stocktype = "OFFICE";
		}
		$form_field = array("name"=>"stocktype", "content"=>"", "value"=>$stocktype, "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
	
		$values["form_info"] = $form_info;
	
		$form_info = array();
	
		$form_info["name"] = "edit";
			
		$form_info["action"] = "#";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$items_arr = array();
		if(isset($values["stocktype"]) && $values["stocktype"]=="office"){
			$items = \Items::where("status","=","ACTIVE")->where("stockType","=","OFFICE")->get();
		}
		else{
			$items = \Items::where("status","=","ACTIVE")->where("stockType","=","NON OFFICE")->get();
		}
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$item_info_arr = array("1"=>"info1","2"=>"info2");
		$form_fields = array();
		$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr, "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"itemtype", "content"=>"item type", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array(),  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"iteminfo", "content"=>"manufacturer", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array(),  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"itemnumbers", "content"=>"item numbers", "readonly"=>"readonly", "required"=>"","type"=>"textarea", "placeholder"=>"Enter item numbers as comma(,) separated value without any spaces like 24A4,1B35", "action"=>array("type"=>"onchange","script"=>"validateInput(this.value)"), "class"=>"form-control");
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
	
	
	public function editPurchaseOrder()
	{
		$values = Input::all();
		
		if (\Request::isMethod('post'))
		{
			//$values["sdf"];
			if($values["type"] == "repairs"){
				$url = "editpurchaseorder?&type=repairs&id=".$values["id"];
				if(isset($values["stocktype"])&& $values["stocktype"]=="office"){
					$url = $url."&stocktype=office";
				}
				else{
					$url = $url."&stocktype=nonoffice";
				}
				$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
						"orderdate"=>"orderDate","paymentdate"=>"paymentDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","incharge"=>"inchargeId",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "orderdate" || $key == "paymentdate" ||$key == "date1" || $key == "issuedate" || $key == "transactiondate"){
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
				$table = "PurchasedOrders";
				\DB::beginTransaction();
				$recid = "";
				try{
					$db_functions_ctrl->update($table, $fields, array("id"=>$values["id"]));
				}
				catch(\Exception $ex){
					\Session::put("message","UpdatePurchase1 order : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}
				try{
					$db_functions_ctrl = new DBFunctionsController();
					$table = "PurchasedItems";
					$table::where('purchasedOrderId',"=", $values['id'])->update(array("status"=>"DELETED"));
						
					$jsonitems = json_decode($values["jsondata"]);
						
					foreach($jsonitems as $jsonitem){
						$fields = array();
						$fields["itemId"] = $jsonitem->i9;
						$fields["manufacturerId"] = $jsonitem->i10;
						$fields["vehicleId"] = $jsonitem->i11;
						$fields["qty"] = $jsonitem->i5;
						$fields["purchasedQty"] = $jsonitem->i5;
						$fields["itemNumbers"] = $jsonitem->i4;
						$fields["unitPrice"] = 0;
						if (isset($jsonitem->i6) && $jsonitem->i6 != "" ){
							$fields["unitPrice"] = $jsonitem->i6;
						}
						$fields["itemStatus"] = $jsonitem->i3;
						$fields["remarks"] = $jsonitem->i7;
						
						if($jsonitem->i12 == "undefined"){
							$fields["purchasedOrderId"] = $values["id"];
							$db_functions_ctrl->insert($table, $fields);
						}
						else{
							$data = array("id"=>$jsonitem->i12);
							$fields["status"] = "ACTIVE";
							$db_functions_ctrl->update($table, $fields, $data);
						}
					}
						
				}
				catch(\Exception $ex){
					\Session::put("message","Update Purchase2 Item : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}
				\DB::commit();
				\Redirect::to($url);
				
			}
			else {
				$url = "editpurchaseorder?id=".$values["id"];
				if(isset($values["stocktype"])&& $values["stocktype"]=="office"){
					$url = $url."&stocktype=office";
				}
				else{
					$url = $url."&stocktype=nonoffice";
				}
				$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
							"orderdate"=>"orderDate","paymentdate"=>"paymentDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
							"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","incharge"=>"inchargeId",
							"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
						);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "orderdate" || $key == "paymentdate" || $key == "date1" || $key == "issuedate" || $key == "transactiondate"){
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
				$table = "PurchasedOrders";
				\DB::beginTransaction();
				$recid = "";
				try{
					$db_functions_ctrl->update($table, $fields, array("id"=>$values["id"]));
				}
				catch(\Exception $ex){
					\Session::put("message","UpdatePurchase order : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}
				try{
					$db_functions_ctrl = new DBFunctionsController();
					$table = "PurchasedItems";
					$table::where('purchasedOrderId',"=", $values['id'])->update(array("status"=>"DELETED"));
					
					$jsonitems = json_decode($values["jsondata"]);
					
					foreach ($jsonitems as $jsonitem){
						$fields = array();
						$fields["itemId"] = $jsonitem->i8;
						$fields["itemTypeId"] = $jsonitem->i9;
						$fields["manufacturerId"] = $jsonitem->i10;
						$fields["itemNumbers"] = $jsonitem->i3;
						$fields["itemNumbersAll"] = $jsonitem->i3;
						$fields["qty"] = $jsonitem->i4;
						$fields["purchasedQty"] = $jsonitem->i4;
						$fields["unitPrice"] = $jsonitem->i5;
						$fields["itemStatus"] = $jsonitem->i6;
						$fields["status"] = "ACTIVE";
						if($jsonitem->i7 == "undefined"){
							$fields["purchasedOrderId"] = $values["id"];
							$db_functions_ctrl->insert($table, $fields);
						}
						else{
							$data = array("id"=>$jsonitem->i7);
							$db_functions_ctrl->update($table, $fields, $data);
						}
					}
					
				}
				catch(\Exception $ex){
					\Session::put("message","Update Purchase Item : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}
				\DB::commit();
				\Redirect::to($url);
			}
		}
		$values['bredcum'] = "EDIT PURCHASE ORDER";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$theads = array('name', "type", "remarks", "status", "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "editpurchaseorder";
		$form_info["action"] = "editpurchaseorder?id=".$values["id"];
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
	
		$entity = \PurchasedOrders::where("id","=",$values['id'])->get();
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
			$warehouses = AppSettingsController::getEmpBranches();
			foreach ($warehouses as $warehouse){
				$branch = \OfficeBranch::where("id","=",$warehouse["id"])->first();
				if($branch->isWareHouse == "Yes"){
					$warehouse_arr[$warehouse["id"]] = $warehouse["name"];
				}
			}
			
			$incharges =  \InchargeAccounts::join("employee", "employee.id","=","inchargeaccounts.empid")
								->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
			$incharges_arr = array();
			foreach ($incharges as $incharge){
				$incharges_arr[$incharge->id] = $incharge->name;
			}
			
			$form_fields = array();
			$form_payment_fields= array();
			$form_field = array("name"=>"warehouse", "id"=>"warehouse", "value"=>$entity->officeBranchId, "content"=>"warehouse", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "action"=>array("type"=>"onChange", "script"=>"getCreditSupplierByState(this.value)"), "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			if($entity->type == "PURCHASE ORDER" || $entity->type == "TO CREDIT SUPPLIER REPAIR"){
				$form_field = array("name"=>"creditsupplier", "id"=>"creditsupplier", "value"=>$entity->creditSupplierId, "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
			}
			if($entity->type == "PURCHASE ORDER" || $entity->type == "TO CREDIT SUPPLIER REPAIR"){
				$form_field = array("name"=>"receivedby", "id"=>"receivedby", "value"=>$entity->receivedBy, "content"=>"received by", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$incharges_arr, "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"orderdate", "id"=>"orderdate", "value"=> date("d-m-Y",strtotime($entity->orderDate)), "content"=>"order date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			if($entity->type == "PURCHASE ORDER" || $entity->type == "OFFICE PURCHASE ORDER" || $entity->type == "TO CREDIT SUPPLIER REPAIR"){
				if($entity->type == "TO CREDIT SUPPLIER REPAIR"){
					$values['bredcum'] = "REPAIRS TO CREDIT SUPPLIER";
				}
				$form_field = array("name"=>"billnumber", "id"=>"billnumber", "value"=>$entity->billNumber, "content"=>"bill number", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"suspense", "content"=>"suspense", "value"=>$entity->suspense, "readonly"=>"", "required"=>"","type"=>"checkboxslide", "options"=>array("YES"=>" YES","NO"=>" NO"),  "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"billfile", "content"=>"upload bill",  "value"=>$entity->filePath, "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"enableincharge", "id"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$entity->inchargeId, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"inchargebalance", "value"=>"", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"amountpaid", "id"=>"amountpaid", "value"=>$entity->amountPaid, "content"=>"amount paid", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
				$form_fields[] = $form_field;
				$pmtdate = date("d-m-Y",strtotime($entity->paymentDate));
				if($pmtdate=="00-00-0000" || $pmtdate=="01-01-1970"){
					$pmtdate = "";
				}
				$form_field = array("name"=>"paymentdate", "id"=>"paymentdate", "value"=>$pmtdate, "content"=>"payment paid date", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
				if($entity->amountPaid == "No"){
					$entity->paymentType = "";
				}
				$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
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
				$form_field = array("name"=>"comments", "id"=>"comments", "value"=>$entity->comments, "content"=>"comments", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control ");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"totalamount", "id"=>"totalamount", "value"=>$entity->totalAmount, "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
				$form_fields[] = $form_field;
				if($values["type"] == "repairs"){
					$form_field = array("name"=>"type", "id"=>"type",  "content"=>"type", "value"=>"repairs", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
				else{
					$form_field = array("name"=>"type", "id"=>"type",  "content"=>"type", "value"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
					$form_fields[] = $form_field;
				}
			}
			if($entity->type == "TO WAREHOUSE REPAIR"){
				$values['bredcum'] = "REPAIRS TO WAREHOUSE";
			}
			if($entity->type == "TO WAREHOUSE"){
				$values['bredcum'] = "STOCK MOVED TO WAREHOUSE";
			}
			
			$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id, "content"=>"", "readonly"=>"", "required"=>"required","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
			if(isset($values["stocktype"])){
				$form_field = array("name"=>"stocktype", "value"=>$values["stocktype"], "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
				$form_fields[] = $form_field;
			}
	
			$form_info["form_fields"] = $form_fields;
			$form_info["form_payment_fields"] = $form_payment_fields;
			$values["form_info"] = $form_info;
			$form_info = array();
			$form_info["name"] = "edit";
			$form_info["action"] = "#";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$items_arr = array();
			if(isset($values["stocktype"]) && $values["stocktype"]=="office"){
				$items = \Items::where("status","=","ACTIVE")->where("stockType","=","OFFICE")->get();
			}
			else{
				$items = \Items::where("status","=","ACTIVE")->where("stockType","=","NON OFFICE")->get();
			}			
			foreach ($items as $item){
				$items_arr[$item->id] = $item->name;
			}
			$item_info_arr = array("1"=>"info1","2"=>"info2");
			
			if ($values["type"] == "repairs"){
				$vehicles =  \Vehicle::all();
				$vehicles_arr = array();
				foreach ($vehicles as $vehicle){
					$vehicles_arr[$vehicle['id']] = $vehicle->veh_reg;
				}
				
				$form_fields = array();
				$form_field = array("name"=>"item1", "id"=>"item1",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>$items_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"iteminfo", "id"=>"iteminfo",  "content"=>"manufacturer", "readonly"=>"readonly",  "required"=>"", "type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"itemnumbers1", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"calItemCountText(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"qty1", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"unitprice1", "id"=>"unitprice",  "content"=>"unitprice", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"vehicle1", "id"=>"vehicle",  "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"itemstatus1", "id"=>"itemstatus",  "content"=>"item status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("USED"=>"USED","NEW"=>"NEW"));
				$form_fields[] = $form_field;
				$form_field = array("name"=>"remarks1", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_info["form_fields"] = $form_fields;
				$modals[] = $form_info;
			}
			else{
				$form_fields = array();
				$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr, "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"itemtype", "content"=>"item type", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array(),  "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"iteminfo", "content"=>"manufacturer", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array(),  "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"itemnumbers", "content"=>"item numbers", "readonly"=>"readonly", "required"=>"","type"=>"textarea", "placeholder"=>"Enter item numbers as comma(,) separated value without any spaces like 24A4,1B35", "action"=>array("type"=>"onchange","script"=>"validateInput(this.value)"), "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"unitprice", "content"=>"price of unit", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"status", "content"=>"status", "readonly"=>"", "required"=>"required", "type"=>"select", "options"=>array("New"=>"New","Old"=>"Old"), "class"=>"form-control chosen-select");
				$form_fields[] = $form_field;
				$form_info["form_fields"] = $form_fields;
				$modals[] = $form_info;
			}
			$values["provider"] = "purchasedorder";
		
			$values["modals"] = $modals;
			if ($values["type"] == "repairs"){
				return View::make('inventory.editstockrepairs', array("values"=>$values));
			}
			else{
				return View::make('inventory.editpurchaseorder', array("values"=>$values));
			}
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function managePurchaseOrders()
	{
		$values = Input::all();
		$values['bredcum'] = "PURCHASE ORDERS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addvehicle';
		$values['form_action'] = 'vehicles';
		$values['action_val'] = '';
	
		$action_val = "";
		$links = array();
		$values['action_val'] = $action_val;
		$values['links'] = $links;
		
		$values['create_link'] = array("href"=>"createpurchaseorder?stocktype=nonoffice","text"=>"CREATE PURCHASE ORDER");
		$values['create_link1'] = array("href"=>"createpurchaseorder?stocktype=office","text"=>"CREATE OFFICE PURCHASE ORDER");
		
		$theads = array('Credit supplier','Warehouse', 'stock type', "Items (qty)", "incharge", "received By", "order date", "pmt date", "bill number", "amount paid", "payment type", "total amount", "comments", "status",  'created by', 'wf status', 'wf updated By', 'wf_remarks', "Actions");
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
	
		$values["provider"] = "purchaseorders";
		return View::make('inventory.datatable', array("values"=>$values));
	}
	
	public function getManufacturers(){
		$values = Input::all();
		$jsondata = array();
		$itemid = $values["itemid"];
		$item = \Items::where("id","=",$itemid)->first();
		
		$jsondata["itemnumberstatus"] = $item->itemNumber;
		
		$mans = "";
		$mans_arr = explode(",",$item->manufactures);
		foreach ($mans_arr as $man){
			if($man != "") {
				$manId = $man;
				$man = \Manufacturers::where("id","=",$man)->get();
				$man = $man[0];
				$man = $man->name;
				$mans = $mans."<option value='".$manId."' >".$man."</option>";
			}
		}
		if($mans == ""){
			$mans = $mans."<option value='' ></option>";
		}
		$jsondata["manufactures"] = $mans;
		

		$types = "";
		$mans_arr = explode(",",$item->itemTypeId);
		foreach ($mans_arr as $man){
			if($man != "") {
				$manId = $man;
				$man = \ItemTypes::where("id","=",$man)->get();
				$man = $man[0];
				$man = $man->name;
				$types = $types."<option value='".$manId."' >".$man."</option>";
			}
		}
		if($types == ""){
			$types = $types."<option value='' ></option>";
		}
		$jsondata["itemtypes"] = $types;
		echo json_encode($jsondata);
	}
	
	public function verifyBillNo(){
		$values = Input::all();
		$recs = \PurchasedOrders::where("creditSupplierId","=", $values["creditsupplier"])
								->where("billNumber","=", $values["billno"])->first();
		if(count($recs)>0){
			echo "yes";
		}
		else{
			echo "no";
		}
	}
	
	public function getCreditSuppliersByState(){
		$values = Input::all();
		$jsondata = array();
		$branchId = $values["branchId"];
		$stateId = 0;
		$branch = \OfficeBranch::where("id","=",$branchId)->first();
		$stateId = $branch->stateId;
		$suppliers = \CreditSupplier::where("stateId","=",$stateId)->where("status","=","ACTIVE")->get();
		$suppliers_options = "";
		foreach ($suppliers as $supplier){
			$suppliers_options = $suppliers_options."<option value='".$supplier->id."' >".$supplier->supplierName."</option>";
		}
		$jsondata["suppliers"] = $suppliers_options;
		
		$incharges =  \InchargeAccounts::join("employee", "employee.id","=","inchargeaccounts.empid")
							->join("cities", "cities.id","=","employee.cityId")
							->where("cities.stateId","=",$stateId)
							->select(array("inchargeaccounts.empid as id","employee.fullName as name"))
							->groupBy("employee.id")->get();
		$incharges_options = "";
		foreach ($incharges as $incharge){
			$incharges_options = $incharges_options."<option value='".$incharge->id."' >".$incharge->name."</option>";
		}
		$jsondata["incharges"] = $incharges_options;
		echo json_encode($jsondata);
	}
	
	public function deletePurchaseOrder(){
		$values = Input::all();
		$itemid = $values["id"];
		$fields = array("status"=>"DELETED");
		$data = array('id'=>$values['id']);
		$db_functions_ctrl = new DBFunctionsController();
		$table = "\PurchasedOrders";
			
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
		}
		else{
			echo "fail";
		}
	}
}
