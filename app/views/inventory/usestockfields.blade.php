<?php 
	use Illuminate\Support\Facades\View;
	$branches =  \OfficeBranch::where("isWareHouse","=","Yes")->get();
	$branches_arr = array();
	foreach ($branches as $branch){
		if($values["warehouseid"] != $branch->id){
			$branches_arr[$branch->id] = $branch->name;
		}
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
	$form_fields = array();
	$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getItemInfo(this.value)"),   "options"=>$stockitems_arr);
	$form_fields[] = $form_field;
	$form_field = array("name"=>"itemnumbers", "id"=>"itemnumbers",  "content"=>"item numbers", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "multiple"=>"multiple", "action"=>array("type"=>"onchange","script"=>"calItemCount(this.id)"),  "options"=>array());
	$form_fields[] = $form_field;
	$form_field = array("name"=>"units", "id"=>"units",  "content"=>"units", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"qty", "id"=>"qty",  "content"=>"Quantity", "readonly"=>"",  "required"=>"", "type"=>"text", "action"=>array("type"=>"onchange","script"=>"validateQuantity(this.value)"), "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"towarehouse", "id"=>"towarehouse",  "content"=>"to warehouse", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",   "options"=>$branches_arr);
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
<?php } else if($values["action"] == "TO VEHICLE1"){?>
	<div class="form-group col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
		<div class="form-group" id="repairbuttons">
			<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> VEHICLE REG<span style="color:red;">*</span> </label>
			<div class="col-xs-7">
				<select class="form-control chosen-select vehicle" id="vehicle" required="required" name="repairvehicle" >
					<option value="">-- vehicle --</option>
					<?php 
						foreach($vehicles_arr as  $key=>$val){
							echo "<option value='".$key."' >".$val."</option>";
						}
					?>
				</select>
			</div>			
		</div>
	</div>
	<div class="col-xs-12" style="border: 1px solid #D5D5D5; margin-left: 10px; margin-top: 10px; max-width:98%">
		<div class="row" style="background-color: #307ECC;">
			<div style="margin-top: 5px; color:white; float:left;">
					&nbsp;ADD / REMOVE ROW 
				</a>
			</div>
			<div style="margin-top: 5px; margin-right:10px; float:right; color:white" ><i id="children_add" class="ace-icon fa fa-plus-circle bigger-160"></i> &nbsp;&nbsp; <i id="children_remove" class="ace-icon fa fa-minus-circle bigger-160"></i> &nbsp;&nbsp; <i id="children_refresh" class="ace-icon fa fa-refresh bigger-160"></i></div>
		</div>
		<div class="row col-xs-12" id="children_fields_all">
			<div id="children_fields" style="padding-top: 7px; padding-bottom: 2px;" class="children_fields">
				<div id="row0" class="">								
					<div class="form-group inline" style="float:left;width:28%;">
						<label class="col-xs-2 control-label no-padding-right" for="form-field-1">Supplier&nbsp; </label>
						<div class="col-xs-10">
							<select class="form-control creditsupplier chosen-select" id="creditsupplier0" name="creditsupplier[]" onchange="getCreditSupplierItems(this.value, this.id)">
								<option value="">-- creditsupplier --</option>
								<?php
									$creditsuppliers =  CreditSupplier::where("purchase_orders.type","=","TO CREDIT SUPPLIER")
														->join("purchase_orders","purchase_orders.creditSupplierId", "=", "creditsuppliers.id")
														->select(array("creditsuppliers.id as id", "creditsuppliers.supplierName as supplierName"))
														->groupBy("creditsuppliers.id")->get();
									$creditsuppliers_arr = array();
									foreach($creditsuppliers as  $creditsupplier){
										echo "<option value='".$creditsupplier->id."' >".$creditsupplier->supplierName."</option>";
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group inline" style="float:left; width:22%; margin-left:5px;">
						<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> Item</label>
						<div class="col-xs-9">
							<select class="form-control item chosen-select" id="item0" name="item[]" >
								<option value="">-- item --</option>
							</select>
						</div>
					</div>
					<div class="form-group inline" style="float:right; width: 50%; margin-right: 0%; margin-left: 1%;">
						<label style="width:5%; float:left; margin-right:5px;" class="control-label no-padding-right" for="form-field-1"> QTY </label>
						<div style="width:15%; float:left; margin-right:15px;">
							<input type="text" id="qty0" name="qty[]" class="form-control qty" onchange="qtyChange(this.id)">
						</div>
						<label style="width:8%;float:left; margin-right:5px;" class=" control-label no-padding-right" for="form-field-1"> STATUS </label>
						<div style="width:13%; float:left; margin-right:15px;">
							<select class="form-control  chosen-select warehouse" id="warehouse0" name="status[]" >
								<option value="Old">USED</option>
							</select>
						</div>
						<div style="width:35%; float:right; ">
							<input type="text" id="remarks0" placeholder="remarks"  name="remarks[]" class="form-control remarks" >
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } else if($values["action"] == "TO CREDIT SUPPLIER"){?>
	<?php 
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
<?php } ?>