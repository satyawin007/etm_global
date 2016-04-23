<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
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
			$values = Input::all();
			$url = "purchaseorder";
			$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
						"orderdate"=>"orderDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
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
			$db_functions_ctrl = new DBFunctionsController();
			$table = "PurchasedOrders";
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
				$table = "PurchasedItems";
				
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["purchasedOrderId"] = $recid;
					$fields["itemId"] = $jsonitem->i5;
					$fields["manufacturerId"] = $jsonitem->i6;
					$fields["qty"] = $jsonitem->i2;
					$fields["unitPrice"] = $jsonitem->i3;
					$fields["itemStatus"] = $jsonitem->i4;
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
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD"), "class"=>"form-control");
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
		$warehouses = \OfficeBranch::where("isWareHouse","=","Yes")->get();
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
		$form_field = array("name"=>"warehouse", "content"=>"warehouse", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"receivedby", "content"=>"received by", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"orderdate", "content"=>"order date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "content"=>"payment type", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"comments", "content"=>"comments", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control ");
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
	
	public function editPurchaseOrder()
	{
		$values = Input::all();
		
		if (\Request::isMethod('post'))
		{
			//$values["sdf"];
			$url = "editpurchaseorder?id=".$values["id"];
			$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
						"orderdate"=>"orderDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
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
					$fields["itemId"] = $jsonitem->i6;
					$fields["manufacturerId"] = $jsonitem->i7;
					$fields["qty"] = $jsonitem->i2;
					$fields["unitPrice"] = $jsonitem->i3;
					$fields["itemStatus"] = $jsonitem->i4;
					$fields["status"] = "ACTIVE";
					if($jsonitem->i5 == "undefined"){
						$fields["purchasedOrderId"] = $values["id"];
						$db_functions_ctrl->insert($table, $fields);
					}
					else{
						$data = array("id"=>$jsonitem->i5);
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
			$warehouses = \OfficeBranch::where("isWareHouse","=","Yes")->get();
			foreach ($warehouses as $warehouse){
				$warehouse_arr[$warehouse->id] = $warehouse->name;
			}
			$form_fields = array();
			$form_payment_fields= array();
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
			if($entity->amountPaid == "No"){
				$entity->paymentType = "";
			}
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"", "required"=>"","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD"), "class"=>"form-control");
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
			}
			$form_field = array("name"=>"comments", "id"=>"comments", "value"=>$entity->comments, "content"=>"comments", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"totalamount", "id"=>"totalamount", "value"=>$entity->totalAmount, "content"=>"total amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"jsondata", "value"=>"", "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id, "content"=>"", "readonly"=>"", "required"=>"required","type"=>"hidden", "class"=>"form-control ");
			$form_fields[] = $form_field;
	
			$form_info["form_fields"] = $form_fields;
			$form_info["form_payment_fields"] = $form_payment_fields;
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
			return View::make('inventory.editpurchaseorder', array("values"=>$values));
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
		
		$values['create_link'] = array("href"=>"createpurchaseorder","text"=>"CREATE PURCHASE ORDER");
	
		$theads = array('Credit supplier','Warehouse', "received By", "order date", "bill number", "amount paid", "payment type", "total amount", "comments", "status", "Actions");
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
