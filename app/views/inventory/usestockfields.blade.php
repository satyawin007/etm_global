<?php 
	use Illuminate\Support\Facades\View;
// 	$branches =  \OfficeBranch::where("isWareHouse","=","Yes")->get();
// 	$branches_arr = array();
// 	foreach ($branches as $branch){
// 		if($values["warehouseid"] != $branch->id){
// 			$branches_arr[$branch->id] = $branch->name;
// 		}
// 	}
	
	$warehouse_arr_total = array();
	$warehouse_arr = array();
	$warehouses = \OfficeBranch::where("isWareHouse","=","Yes")->get();
	foreach ($warehouses as $warehouse){
		$warehouse_arr[$warehouse->id] = $warehouse->name;
	}
	$warehouse_arr_total["main warehouses"] = $warehouse_arr;
	foreach ($warehouses as $warehouse){
		$warehouse_arr = array();
		$sub_warehouses = \Depot::where("status","=","ACTIVE")
								->where("ParentWarehouse","=",$warehouse->id)->get();
		foreach ($sub_warehouses as $sub_warehouse){
			$warehouse_arr[$sub_warehouse->id] = $sub_warehouse->name."(".$sub_warehouse->code.")";
		}
		$warehouse_arr_total[$warehouse->name] = $warehouse_arr;
	}
	
	$vehicles =  \Vehicle::all();
	$vehicles_arr = array();
	foreach ($vehicles as $vehicle){
		$vehicles_arr[$vehicle['id']] = $vehicle->veh_reg;
	}
	
	$items_arr = array();
	$items =  \Items::where("status","=","ACTIVE")->get();
	foreach ($items as $item){
		$items_arr[$item['id']] = $item->name;
	}
	
	$parentId = -1;
	$parent = \InventoryLookupValues::where("name","=","ITEM ACTIONS")->get();
	if(count($parent)>0){
		$parent = $parent[0];
		$parentId = $parent->id;
	}
	
	$select_fields = array();
	$select_fields[] = "items.name as name";
	$select_fields[] = "purchased_items.qty as qty";
	$select_fields[] = "purchased_items.unitPrice as unitPrice";
	$select_fields[] = "creditsuppliers.supplierName as creditSupplier";
	$select_fields[] = "purchase_orders.billNumber as billNo";
	$select_fields[] = "purchased_items.id as id";
	
	$stockitems =  \PurchasedItems::where("purchase_orders.officeBranchId","=",$values["warehouseid"])
					->where("purchased_items.status","=","ACTIVE")
					->where("purchased_items.qty",">",0)
					->where("purchase_orders.status","=","ACTIVE")
					->join("purchase_orders","purchased_items.purchasedOrderId","=","purchase_orders.id")
					->join("items","purchased_items.itemId","=","items.id")
					->join("creditsuppliers","purchase_orders.creditSupplierId","=","creditsuppliers.id")
					->select($select_fields)->get();
	$stockitems_arr = array();
	foreach ($stockitems as $stockitem){
		$stockitems_arr[$stockitem['id']] = $stockitem->name." - qty(".$stockitem->qty.") - ".$stockitem->creditSupplier."(".$stockitem->billNo.")";
	}
	//print_r($values); die();
?>

<?php if($values["action"] == "itemtovehicles" || $values["action"] == "itemstovehicle" || $values["action"] == "vehicletowarehouse"){
	$form_fields = array();
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getItemInfo(this.value)"),   "options"=>$stockitems_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemactions", "id"=>"itemactions",  "content"=>"item actions", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>array());
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "action"=>array("type"=>"onchange","script"=>"calItemCount(this.id)"),  "options"=>array());
	$form_fields[] = $form_field;
	$form_field = array("name"=>"alertdate", "id"=>"alertdate",  "content"=>"alert date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"units", "id"=>"units",  "content"=>"units", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"vehicle", "id"=>"vehicle",  "content"=>"vehicle", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"remarks", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"bill", "id"=>"bill",  "content"=>"bill", "readonly"=>"",  "required"=>"", "type"=>"link", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info = array();
	$form_info["form_fields"] = $form_fields;
	$form_info["theads"] = array("ITEM", "ITEM NUMBERS", "ITEM ACTION", "VEHICLE", "QTY", "ALERT DATE", "REMARKS", "ACTION");
?>
	@include("inventory.tablerowform",$form_fields);
<?php } else if($values["action"] == "warehousetowarehouse"){
	foreach($warehouse_arr_total as $val => $subarr) {
		if(isset($warehouse_arr_total[$val][$values["warehouseid"]])){
			unset($warehouse_arr_total[$val][$values["warehouseid"]]);
		}
	}
	
	$form_fields = array();
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getItemInfo(this.value)"),   "options"=>$stockitems_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "action"=>array("type"=>"onchange","script"=>"calItemCount(this.id)"),  "options"=>array());
	$form_fields[] = $form_field;
	$form_field = array("name"=>"units", "id"=>"units",  "content"=>"units", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"towarehouse", "id"=>"towarehouse",  "content"=>"to warehouse", "readonly"=>"",  "required"=>"required", "type"=>"selectgroup", "class"=>"form-control chosen-select",   "options"=>$warehouse_arr_total);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"remarks", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"bill", "id"=>"bill",  "content"=>"bill", "readonly"=>"",  "required"=>"", "type"=>"link", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info = array();
	$form_info["form_fields"] = $form_fields;
	$form_info["theads"] = array("ITEM", "ITEM NUMBERS", "WAREHOUSE", "QTY", "REMARKS", "ACTION");
?>
	@include("inventory.tablerowform",$form_fields);
<?php } else if($values["action"] == "TO WAREHOUSE"){
	$form_fields = array();
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>$items_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"manufacturer", "id"=>"manufacturer",  "content"=>"manufacturer", "readonly"=>"readonly",  "required"=>"", "type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"calItemCountText(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"vehicle", "id"=>"vehicle",  "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemstatus", "id"=>"itemstatus",  "content"=>"item status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("USED"=>"USED","NEW"=>"NEW"));
	$form_fields[] = $form_field;
	$form_field = array("name"=>"remarks", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info = array();
	$form_info["form_fields"] = $form_fields;
	$form_info["theads"] = array("ITEM", 'MANUFACTURER', "VEHICLE", "STATUS", "ITEM NUMBERS", "QTY", "REMARKS", "ACTION");
?>
	@include("inventory.tablerowform",$form_fields);
<?php } else if($values["action"] == "TO VEHICLE1"){
	$branchId = $values["warehouseid"];
	$stateId = 0;
	if($branchId>1000){
		$branch = \Depot::where("id","=",$branchId)->first();
	}
	else {
		$branch = \OfficeBranch::where("id","=",$branchId)->first();
	}
	$stateId = $branch->stateId;
	$creditsuppliers =  CreditSupplier::where("purchase_orders.type","=","TO CREDIT SUPPLIER REPAIR")
							->where("stateId","=",$stateId)
							->join("purchase_orders","purchase_orders.creditSupplierId", "=", "creditsuppliers.id")
							->select(array("creditsuppliers.id as id", "creditsuppliers.supplierName as supplierName"))
							->groupBy("creditsuppliers.id")->get();
	$creditsuppliers_arr = array();
	foreach($creditsuppliers as  $creditsupplier){
		$creditsuppliers_arr[$creditsupplier->id] = $creditsupplier->supplierName;
	}
	$form_fields = array();
	$form_field = array("name"=>"creditsupplier1", "id"=>"creditsupplier1",  "content"=>"credit supplier", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getCreditSupplierItems(this.value)"), "options"=>$creditsuppliers_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>array());
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"calItemCountText(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"vehicle", "id"=>"vehicle",  "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemstatus", "id"=>"itemstatus",  "content"=>"item status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("USED"=>"USED","NEW"=>"NEW"));
	$form_fields[] = $form_field;
	$form_field = array("name"=>"remarks", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info = array();
	$form_info["form_fields"] = $form_fields;
	$form_info["theads"] = array('CREDIT SUPPLIER', "ITEM", "VEHICLE", "STATUS", "ITEM NUMBERS", "QTY", "REMARKS", "ACTION");
?>
	@include("inventory.tablerowform",$form_fields);

<?php } else if($values["action"] == "TO WAREHOUSE1"){
	
	$branchId = $values["warehouseid"];
	$stateId = 0;
	if($branchId>1000){
		$branch = \Depot::where("id","=",$branchId)->first();
	}
	else {
		$branch = \OfficeBranch::where("id","=",$branchId)->first();
	}
	$stateId = $branch->stateId;
	$creditsuppliers =  CreditSupplier::where("purchase_orders.type","=","TO CREDIT SUPPLIER REPAIR")
							->where("stateId","=",$stateId)
							->join("purchase_orders","purchase_orders.creditSupplierId", "=", "creditsuppliers.id")
							->select(array("creditsuppliers.id as id", "creditsuppliers.supplierName as supplierName"))
							->groupBy("creditsuppliers.id")->get();
	$creditsuppliers_arr = array();
	foreach($creditsuppliers as  $creditsupplier){
		$creditsuppliers_arr[$creditsupplier->id] = $creditsupplier->supplierName;
	}
	$form_fields = array();
	$form_field = array("name"=>"creditsupplier1", "id"=>"creditsupplier1",  "content"=>"credit supplier", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getCreditSupplierItems(this.value)"), "options"=>$creditsuppliers_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>array());
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"calItemCountText(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"towarehouse2", "id"=>"towarehouse2",  "content"=>"to warehouse", "readonly"=>"",  "required"=>"required", "type"=>"selectgroup", "class"=>"form-control chosen-select",   "options"=>$warehouse_arr_total);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemstatus", "id"=>"itemstatus",  "content"=>"item status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("USED"=>"USED","NEW"=>"NEW"));
	$form_fields[] = $form_field;
	$form_field = array("name"=>"remarks", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info = array();
	$form_info["form_fields"] = $form_fields;
	$form_info["theads"] = array('CREDIT SUPPLIER', "ITEM", "WAREHOUSE", "STATUS", "ITEM NUMBERS", "QTY", "REMARKS", "ACTION");
?>
	@include("inventory.tablerowform",$form_fields);	
	
<?php } else if($values["action"] == "TO CREDIT SUPPLIER"){?>
	<?php
		$branchId = $values["warehouseid"];
		$stateId = 0;
		if($branchId>1000){
			$branch = \Depot::where("id","=",$branchId)->first();
		}
		else {
			$branch = \OfficeBranch::where("id","=",$branchId)->first();
		}
		$stateId = $branch->stateId;
		$credit_sups = \CreditSupplier::where("stateId","=",$stateId)->where("status","=","ACTIVE")->get();
		$credit_sup_arr = array();
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
		
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
							->join("cities", "cities.id","=","employee.cityId")
							->where("cities.stateId","=",$stateId)
							->groupBy("employee.id")
							->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		$form_info = array();
		$form_fields = array();
	
		$form_field = array("name"=>"creditsupplier", "content"=>"credit supplier", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$credit_sup_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billnumber", "content"=>"bill number", "readonly"=>"", "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "content"=>"payment type", "readonly"=>"", "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"), "class"=>"form-control");
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
		$form_info["form_fields"] = $form_fields;		
	?>
		<?php $form_fields = $form_info['form_fields'];?>	
		<?php foreach ($form_fields as $form_field) {?>
			<div class="form-group col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
			<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
			<div class="form-group" >
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<input {{$form_field['readonly']}} type="{{$form_field['type']}}" id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
				</div>			
			</div>
			<?php } ?>
			<?php if($form_field['type'] === "empty" ){ ?>
			<div class="form-group" >
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<label class="control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				</div>			
			</div>
			<?php } ?>
			
			<?php if($form_field['type'] === "hidden"){ ?>
					<input type="{{$form_field['type']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" >
			<?php } ?>
			
			<?php if($form_field['type'] === "file"){ ?>				
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<input type="file" id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="form-control file"/>
				</div>			
			</div>
			<?php } ?>
			
			<?php if($form_field['type'] === "textarea"){ ?>				
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}"></textarea>
				</div>			
			</div>
			<?php } ?>
			
			<?php if($form_field['type'] === "radio"){ ?>				
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<div class="radio">
					<?php 
						foreach($form_field["options"] as $key => $value){
							echo "<label><input type='radio' name=\"".$form_field['name']."\"class='ace' value='$key'> <span class='lbl'>".$value."</span></label>&nbsp;&nbsp;";
						}
					?>
					</div>
				</div>			
			</div>
			<?php } ?>
			<?php if($form_field['type'] === "select"){ ?>
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<select class="{{$form_field['class']}}" {{$form_field['required']}} name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>  <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
						<option value="">-- {{$form_field['name']}} --</option>
						<?php 
							foreach($form_field["options"] as $key => $value){
								if(isset($form_field['value']) && $form_field['value']==$key) { 
									echo "<option selected='selected' value='$key'>$value</option>";
								}
								else{
									echo "<option value='$key'>$value</option>";
								}
							}
						?>
					</select>
				</div>			
			</div>				
			<?php } ?>
			<?php if($form_field['type'] === "checkboxslide"){ ?>
				<div class="form-group">
					<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-7" style="margin-top: 3px;">
						<input name="switch-field-1" class="ace ace-switch ace-switch-5" type="checkbox" />
						<span class="lbl"></span>
					</div>
				</div>
			<?php } ?>	
			
			<?php if($form_field['type'] === "checkbox"){ ?>
				<div class="form-group">
					<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-7">
						<?php 
						$options = $form_field["options"];
						foreach ($options as $key=>$value) {
						?>
						<div class="checkbox inline">
							<label>
								<input name="{{$key}}" value="YES" type="checkbox" class="ace">
								<span class="lbl">&nbsp;{{$key}} &nbsp;&nbsp;</span>
							</label>
						</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>	
		</div>							
		<?php } ?>
		
		<div id="paymentfields" style="margin-top: 15px; margin-bottom: -10px"></div>
<?php 
	$form_fields = array();
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>$items_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"manufacturer", "id"=>"manufacturer",  "content"=>"manufacturer", "readonly"=>"readonly",  "required"=>"", "type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"calItemCountText(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"unitprice", "id"=>"unitprice",  "content"=>"unitprice", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"vehicle", "id"=>"vehicle",  "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemstatus", "id"=>"itemstatus",  "content"=>"item status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("USED"=>"USED","NEW"=>"NEW"));
	$form_fields[] = $form_field;
	$form_field = array("name"=>"remarks", "id"=>"remarks",  "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info = array();
	$form_info["form_fields"] = $form_fields;
	$form_info["theads"] = array("ITEM", 'MANUFACTURER', "VEHICLE", "STATUS", "ITEM NUMBERS", "QTY","Unit Price", "REMARKS", "ACTION");

?>
@include("inventory.tablerowform",$form_fields);
<?php } ?>