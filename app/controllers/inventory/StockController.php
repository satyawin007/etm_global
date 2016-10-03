<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use settings\AppSettingsController;

class StockController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addInventoryTransaction()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$url = "useitems";
			//$values['asdf'];
			if(isset($values["action"]) && ($values["action"]=="itemtovehicles" || $values["action"]=="itemtowarehouse")){
				$field_names = array("action"=>"action","date"=>"date","warehouse"=>"fromWareHouseId");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "date"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				\DB::beginTransaction();
				try{
					$db_functions_ctrl = new DBFunctionsController();
					$i = 0;
					$json_data = json_decode($values["jsondata"]);
					//print_r($json_data);die();
					foreach($json_data as $json_obj){
						$table = "InventoryTransactions";
						$fields["toVehicleId"] = $json_obj->vehicle;
						$fields["stockItemId"] = $json_obj->item;
						$fields["meeterReading"] = $json_obj->meeterreading;
						$fields["qty"] = $json_obj->qty;
						$fields["remarks"] = $json_obj->remarks;
						if(isset($json_obj->alertdate) && $json_obj->alertdate != ""){
							$fields["alertDate"] = date("Y-m-d",strtotime($json_obj->alertdate));
						}
						if(isset($json_obj->itemactions) && $json_obj->itemactions != ""){
							$fields["toActionId"] = $json_obj->itemactions;
						}
						if(isset($json_obj->itemnumbers) && $json_obj->itemnumbers != ""){
							$fields["itemNumbers"] = $json_obj->itemnumbers;
							$table = "\PurchasedItems";
							$fields1 = array("id"=>$json_obj->item);
							$itemnumbers = $db_functions_ctrl->get($table, $fields1);
							$itemnumbers = $itemnumbers[0];
							$itemnumbers = $itemnumbers->itemNumbers;
							$item_num_arr = explode(",",$json_obj->itemnumbers);
							foreach($item_num_arr as  $item_num){
								if($item_num==""){
									continue;
								}
								$item_num = $item_num;
								$itemnumbers = str_replace($item_num,"",$itemnumbers);
							}
							$itemnumbers = str_replace(",,",",",$itemnumbers);
							$data = array('id'=>$json_obj->item);
							$table = "\PurchasedItems";
							$fields1 = array("itemNumbers"=>$itemnumbers);
							$db_functions_ctrl->update($table, $fields1, $data);
						}
						$table = "InventoryTransactions";
						$db_functions_ctrl->insert($table, $fields);
						
						$table = "\PurchasedItems";
						$fields1 = array("id"=>$json_obj->item);
						$qty = $db_functions_ctrl->get($table, $fields1);
						$qty = $qty[0];
						$qty = $qty->qty;
						$qty = $qty - $json_obj->qty;
						
						$data = array('id'=>$json_obj->item);
						$table = "\PurchasedItems";
						$fields1 = array("qty"=>$qty);
						$db_functions_ctrl->update($table, $fields1, $data);
					}
				}
				catch(\Exception $ex){
					\DB::rollback();
					$json_resp = array();
					$json_resp["status"] = "fail";
					$json_resp["message"] = "Add inventory transaction (Item to Vehicle) : Operation Could not be completed, Try Again!";
					echo json_encode($json_resp);
					return;
				}
				DB::commit();
				$json_resp = array();
				$json_resp["status"] = "success";
				$json_resp["message"] = "Operation completed successfully";
				echo json_encode($json_resp);
				return;
			}
			
			if(isset($values["action"]) && $values["action"]=="warehousetowarehouse"){
				//$values["Sd"];
				$field_names = array("action"=>"action","date"=>"date","warehouse"=>"fromWareHouseId","towarehouse"=>"toWareHouseId","usedqty"=>"qty","remarks"=>"remarks");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "date"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				\DB::beginTransaction();
				try{
					$db_functions_ctrl = new DBFunctionsController();
					$json_data = json_decode($values["jsondata"]);
					//print_r($json_data);die();
					foreach($json_data as $json_obj){
						$table = "InventoryTransactions";
						$fields["stockItemId"] = $json_obj->item;
						$fields["toWareHouseId"] = $json_obj->towarehouse;
						$fields["qty"] = $json_obj->qty;
						$fields["remarks"] = $json_obj->remarks;
						if(isset($json_obj->itemnumbers) && $json_obj->itemnumbers != ""){
							$fields["itemNumbers"] = $json_obj->itemnumbers;
						}
						$db_functions_ctrl->insert($table, $fields);
							
						$table = "\PurchasedItems";
						$fields1 = array("id"=>$json_obj->item);
						$qty = $db_functions_ctrl->get($table, $fields1);
						$qty = $qty[0];
						$item_qty = $qty->qty;
						
						$table = "\PurchasedOrders";
						$fields1 = array("id"=>$qty->purchasedOrderId);
						$purchasedOrder = $db_functions_ctrl->get($table, $fields1);
						$purchasedOrder = $purchasedOrder[0];
						$purchasedOrdernew = new \PurchasedOrders();
						$purchasedOrdernew->type = 'TO WAREHOUSE';
						$purchasedOrdernew->creditSupplierId = $purchasedOrder->creditSupplierId;
						$purchasedOrdernew->officeBranchId = $json_obj->towarehouse;
						$purchasedOrdernew->receivedBy = $purchasedOrder->receivedBy;
						$purchasedOrdernew->orderDate = date("Y-m-d");
						$purchasedOrdernew->billNumber = $purchasedOrder->billNumber;
						$purchasedOrdernew->filePath = $purchasedOrder->filePath;
						$purchasedOrdernew->inchargeId = $purchasedOrder->inchargeId;
						$purchasedOrdernew->amountPaid = $purchasedOrder->amountPaid;
						$purchasedOrdernew->totalAmount = $purchasedOrder->totalAmount;
						$purchasedOrdernew->comments = $purchasedOrder->comments;
						$purchasedOrdernew->createdBy = \Auth::user()->fullName;
						$purchasedOrdernew->workFlowStatus = "Approved";
						$insertId = $purchasedOrdernew->save();
						$insertId = $purchasedOrdernew->id;
						
						$purchasedItem = new \PurchasedItems;
						$purchasedItem->purchasedOrderId = $insertId;
						$purchasedItem->parentOrderItemId = $json_obj->item;
						$purchasedItem->itemId = $qty->itemId;
						$purchasedItem->manufacturerId = $qty->manufacturerId;
						$purchasedItem->unitPrice = $qty->unitPrice;
						$purchasedItem->itemStatus = $qty->itemStatus;
						$purchasedItem->itemNumbers = $json_obj->itemnumbers;						
						$purchasedItem->itemNumbersAll = $json_obj->itemnumbers;
						
						if(isset($json_obj->itemnumbers) && $json_obj->itemnumbers != ""){
							$table = "\PurchasedItems";
							$fields1 = array("id"=>$json_obj->item);
							$itemnumbers = $db_functions_ctrl->get($table, $fields1);
							$itemnumbers = $itemnumbers[0];
							$itemnumbers = $itemnumbers->itemNumbers;
							$itemnumbers = str_replace($json_obj->itemnumbers,"",$itemnumbers);
								
							$data = array('id'=>$json_obj->item);
							$table = "\PurchasedItems";
							$fields1 = array("itemNumbers"=>$itemnumbers);
							$db_functions_ctrl->update($table, $fields1, $data);
						}
						
						$purchasedItem->qty = $json_obj->qty;
						$purchasedItem->purchasedQty = $json_obj->qty;
						$purchasedItem->createdBy = \Auth::user()->fullName;
						$purchasedItem->save();
						
						$qty = $item_qty - $json_obj->qty;
						$data = array('id'=>$json_obj->item);
						$table = "\PurchasedItems";
						$fields1 = array("qty"=>$qty);
						$db_functions_ctrl->update($table, $fields1, $data);
					}
				}
				catch(\Exception $ex){
					\DB::rollback();
					$json_resp = array();
					$json_resp["status"] = "fail";
					$json_resp["message"] = "Add inventory transaction (Warehouse to Warehouse) : Operation Could not be completed, Try Again!";
					echo json_encode($json_resp);
					return;
				}
				DB::commit();
				$json_resp = array();
				$json_resp["status"] = "success";
				$json_resp["message"] = "Operation completed successfully";
				echo json_encode($json_resp);
				return;
			}
			
			if(isset($values["action"]) && $values["action"]=="vehicletovehicle"){
				$field_names = array("action"=>"action","date"=>"date","warehouse"=>"fromWareHouseId","item"=>"stockItemId","fromvehicleno"=>"fromVehicleId","tovehicleno"=>"toVehicleId","fromaction"=>"fromActionId","toaction"=>"toActionId","usedqty"=>"qty","usedqty"=>"qty","remarks"=>"remarks","alertdate"=>"alertDate");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "date" || $key=="alertdate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				\DB::beginTransaction();
				try{
					$table = "InventoryTransactions";
					$db_functions_ctrl = new DBFunctionsController();
					$db_functions_ctrl->insert($table, $fields);
				}
				catch(\Exception $ex){
					\DB::rollback();
					$json_resp = array();
					$json_resp["status"] = "fail";
					$json_resp["message"] = "Add Inventory Transaction (vehicle to vehicle) : Operation Could not be completed, Try Again!";
					echo json_encode($json_resp);
					return;
				}
				DB::commit();
				$json_resp = array();
				$json_resp["status"] = "success";
				$json_resp["message"] = "Operation completed successfully";
				echo json_encode($json_resp);
				return;
			}
			
			if(isset($values["action"]) && $values["action"]=="vehicletowarehouse" && isset($values["repairtype"]) && $values["repairtype"]=="TO WAREHOUSE"){
				
				$field_names = array("repairvehicle"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
						"date"=>"orderDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
						"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "orderdate" || $key == "date" || $key == "issuedate" || $key == "transactiondate"){
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
				$fields["type"] = "TO WAREHOUSE REPAIR";
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
					$json_data = json_decode($values["jsondata"]);
					foreach($json_data as $json_obj){
						$fields = array();
						$fields["purchasedOrderId"] = $recid;
						$fields["itemId"] = $json_obj->item;
						$fields["manufacturerId"] = $json_obj->manufacturer;
						$fields["qty"] = $json_obj->qty;
						$fields["purchasedQty"] = $json_obj->qty;
						$fields["itemNumbers"] = $json_obj->itemnumbers;
						$fields["unitPrice"] = 0;
						$fields["itemStatus"] = $json_obj->itemstatus;
						$fields["vehicleId"] = $json_obj->vehicle;
						$fields["remarks"] = $json_obj->remarks;
						$db_functions_ctrl->insert($table, $fields);
					}
				}
				catch(\Exception $ex){
					\DB::rollback();
					$json_resp = array();
					$json_resp["status"] = "fail";
					$json_resp["message"] = "Add Purchase Item (vehicle to Warehouse) : Operation Could not be completed, Try Again!";
					echo json_encode($json_resp);
					return;
				}
				DB::commit();
				$json_resp = array();
				$json_resp["status"] = "success";
				$json_resp["message"] = "Operation completed successfully";
				echo json_encode($json_resp);
				return;
			}
			
			if(isset($values["action"]) && $values["action"]=="vehicletowarehouse" && isset($values["repairtype"]) && $values["repairtype"]=="TO CREDIT SUPPLIER"){
				//$values["sdf"];
				$field_names = array("creditsupplier"=>"creditSupplierId","warehouse"=>"officeBranchId","receivedby"=>"receivedBy", "paymenttype"=>"paymentType",
							"date"=>"orderDate","billnumber"=>"billNumber","amountpaid"=>"amountPaid","comments"=>"comments","totalamount"=>"totalAmount",
							"bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate","incharge"=>"inchargeId",
							"transactiondate"=>"transactionDate", "suspense"=>"suspense","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
						);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "orderdate" || $key == "date1" || $key == "date" || $key == "issuedate" || $key == "transactiondate"){
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
				$fields["type"] = "TO CREDIT SUPPLIER REPAIR";
				$db_functions_ctrl = new DBFunctionsController();
				$table = "PurchasedOrders";
				\DB::beginTransaction();
				$recid = "";
				try{
					$recid = $db_functions_ctrl->insertRetId($table, $fields);
				}
				catch(\Exception $ex){
					\Session::put("message","Add Repair Transaction : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}
				try{
					$db_functions_ctrl = new DBFunctionsController();
					$table = "PurchasedItems";
			
					$json_data = json_decode($values["jsondata"]);
					//print_r($json_data);die();
					foreach($json_data as $json_obj){
						$fields = array();
						$fields["purchasedOrderId"] = $recid;
						$fields["itemId"] = $json_obj->item;
						if (isset($json_obj->manufacturer) && $json_obj->manufacturer != "" ){
							$fields["manufacturerId"] = $json_obj->manufacturer;
						}
						$fields["vehicleId"] = $json_obj->vehicle;
						$fields["qty"] = $json_obj->qty;
						$fields["purchasedQty"] = $json_obj->qty;
						$fields["itemNumbers"] = $json_obj->itemnumbers;
						$fields["unitPrice"] = 0;
						if (isset($json_obj->unitprice) && $json_obj->unitprice != "" ){
							$fields["unitPrice"] = $json_obj->unitprice;
						}
						$fields["itemStatus"] = $json_obj->itemstatus;
						$fields["remarks"] = $json_obj->remarks;
						$db_functions_ctrl->insert($table, $fields);
					}
				}
				catch(\Exception $ex){
					\DB::rollback();
					$json_resp = array();
					$json_resp["status"] = "fail";
					$json_resp["message"] = "Add Purchase Item (vehicle to Credit Supplier) : Operation Could not be completed, Try Again!";
					echo json_encode($json_resp);
					return;
				}
				DB::commit();
				$json_resp = array();
				$json_resp["status"] = "success";
				$json_resp["message"] = "Operation completed successfully";
				echo json_encode($json_resp);
				return;
			}
		}
		
		if(isset($values["action"]) && $values["action"]=="creditsuppliertowarehouse" && isset($values["repairtype"]) && $values["repairtype"]=="TO VEHICLE1" ){
			$field_names = array("action"=>"action","date"=>"date","warehouse"=>"fromWareHouseId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "date"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			\DB::beginTransaction();
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$i = 0;
				$json_data = json_decode($values["jsondata"]);
				//print_r($json_data);die();
				foreach($json_data as $json_obj){
					$table = "InventoryTransactions";
					$fields["toVehicleId"] = $json_obj->vehicle;
					$fields["stockItemId"] = $json_obj->item;
					$fields["qty"] = $json_obj->qty;
					$fields["remarks"] = $json_obj->remarks;
					if(isset($json_obj->alertdate) && $json_obj->alertdate != ""){
						$fields["alertDate"] = date("Y-m-d",strtotime($json_obj->alertdate));
					}
					if(isset($json_obj->itemactions) && $json_obj->itemactions != ""){
						$fields["toActionId"] = $json_obj->itemactions;
					}
					if(isset($json_obj->itemnumbers) && $json_obj->itemnumbers != ""){
						$fields["itemNumbers"] = $json_obj->itemnumbers;
						$table = "\PurchasedItems";
						$fields1 = array("id"=>$json_obj->item);
						$itemnumbers = $db_functions_ctrl->get($table, $fields1);
						$itemnumbers = $itemnumbers[0];
						$itemnumbers = $itemnumbers->itemNumbers;
						$itemnumbers = str_replace($json_obj->itemnumbers,"",$itemnumbers);
							
						$data = array('id'=>$json_obj->item);
						$table = "\PurchasedItems";
						$fields1 = array("itemNumbers"=>$itemnumbers);
						$db_functions_ctrl->update($table, $fields1, $data);
					}
					$table = "InventoryTransactions";
					$db_functions_ctrl->insert($table, $fields);
		
					$table = "\PurchasedItems";
					$fields1 = array("id"=>$json_obj->item);
					$qty = $db_functions_ctrl->get($table, $fields1);
					$qty = $qty[0];
					$qty = $qty->qty;
					$qty = $qty - $json_obj->qty;
		
					$data = array('id'=>$json_obj->item);
					$table = "\PurchasedItems";
					$fields1 = array("qty"=>$qty);
					$db_functions_ctrl->update($table, $fields1, $data);
				}
			}
			catch(\Exception $ex){
				\DB::rollback();
				$json_resp = array();
				$json_resp["status"] = "fail";
				$json_resp["message"] = "Add inventory transaction (Credit Supplier to Vehicle) : Operation Could not be completed, Try Again!";
				echo json_encode($json_resp);
				return;
			}
			DB::commit();
			$json_resp = array();
			$json_resp["status"] = "success";
			$json_resp["message"] = "Operation completed successfully";
			echo json_encode($json_resp);
			return;
		}
		
		if(isset($values["action"]) && $values["action"]=="creditsuppliertowarehouse" && isset($values["repairreturntype"]) && $values["repairreturntype"]=="TO WAREHOUSE1" ){
			//$values["Sd"];
			$field_names = array("action"=>"action","date"=>"date","warehouse"=>"fromWareHouseId","towarehouse"=>"toWareHouseId","usedqty"=>"qty","remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "date"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			\DB::beginTransaction();
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$json_data = json_decode($values["jsondata"]);
				//print_r($json_data);die();
				foreach($json_data as $json_obj){
					$table = "InventoryTransactions";
					$fields["stockItemId"] = $json_obj->item;
					$fields["toWareHouseId"] = $json_obj->towarehouse2;
					$fields["qty"] = $json_obj->qty;
					$fields["remarks"] = $json_obj->remarks;
					if(isset($json_obj->itemnumbers) && $json_obj->itemnumbers != ""){
						$fields["itemNumbers"] = $json_obj->itemnumbers;
					}
					$db_functions_ctrl->insert($table, $fields);
						
					$table = "\PurchasedItems";
					$fields1 = array("id"=>$json_obj->item);
					$qty = $db_functions_ctrl->get($table, $fields1);
					$qty = $qty[0];
					$item_qty = $qty->qty;
					
					$table = "\PurchasedOrders";
					$fields1 = array("id"=>$qty->purchasedOrderId);
					$purchasedOrder = $db_functions_ctrl->get($table, $fields1);
					$purchasedOrder = $purchasedOrder[0];
					$purchasedOrdernew = new \PurchasedOrders();
					$purchasedOrdernew->type = 'TO WAREHOUSE';
					$purchasedOrdernew->creditSupplierId = $purchasedOrder->creditSupplierId;
					$purchasedOrdernew->officeBranchId = $json_obj->towarehouse2;
					$purchasedOrdernew->receivedBy = $purchasedOrder->receivedBy;
					$purchasedOrdernew->orderDate = date("Y-m-d");
					$purchasedOrdernew->billNumber = $purchasedOrder->billNumber;
					$purchasedOrdernew->filePath = $purchasedOrder->filePath;
					$purchasedOrdernew->inchargeId = $purchasedOrder->inchargeId;
					$purchasedOrdernew->amountPaid = $purchasedOrder->amountPaid;
					$purchasedOrdernew->totalAmount = $purchasedOrder->totalAmount;
					$purchasedOrdernew->comments = $purchasedOrder->comments;
					$purchasedOrdernew->createdBy = \Auth::user()->fullName;
					$insertId = $purchasedOrdernew->save();
					$insertId = $purchasedOrdernew->id;
					
					$purchasedItem = new \PurchasedItems;
					$purchasedItem->purchasedOrderId = $insertId;
					$purchasedItem->parentOrderItemId = $json_obj->item;
					$purchasedItem->itemId = $qty->itemId;
					$purchasedItem->manufacturerId = $qty->manufacturerId;
					$purchasedItem->unitPrice = $qty->unitPrice;
					$purchasedItem->itemStatus = $qty->itemStatus;
					$purchasedItem->itemNumbers = $json_obj->itemnumbers;						
					$purchasedItem->itemNumbersAll = $json_obj->itemnumbers;
					
					if(isset($json_obj->itemnumbers) && $json_obj->itemnumbers != ""){
						$table = "\PurchasedItems";
						$fields1 = array("id"=>$json_obj->item);
						$itemnumbers = $db_functions_ctrl->get($table, $fields1);
						$itemnumbers = $itemnumbers[0];
						$itemnumbers = $itemnumbers->itemNumbers;
						$itemnumbers = str_replace($json_obj->itemnumbers,"",$itemnumbers);
							
						$data = array('id'=>$json_obj->item);
						$table = "\PurchasedItems";
						$fields1 = array("itemNumbers"=>$itemnumbers);
						$db_functions_ctrl->update($table, $fields1, $data);
					}
					
					$purchasedItem->qty = $json_obj->qty;
					$purchasedItem->purchasedQty = $json_obj->qty;
					$purchasedItem->createdBy = \Auth::user()->fullName;
					$purchasedItem->save();
					
					$qty = $item_qty - $json_obj->qty;
					$data = array('id'=>$json_obj->item);
					$table = "\PurchasedItems";
					$fields1 = array("qty"=>$qty);
					$db_functions_ctrl->update($table, $fields1, $data);
				}
			}
			catch(\Exception $ex){
				\DB::rollback();
				$json_resp = array();
				$json_resp["status"] = "fail";
				$json_resp["message"] = "Add inventory transaction (Credit Supplier to Warehouse) : Operation Could not be completed, Try Again!";
				echo json_encode($json_resp);
				return;
			}
			DB::commit();
			$json_resp = array();
			$json_resp["status"] = "success";
			$json_resp["message"] = "Operation completed successfully";
			echo json_encode($json_resp);
			return;
		}
		
		\Session::put("message","Operation completed successfully!");
		//return \Redirect::to($url);	
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editItem()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("name"=>"name","number"=>"number","shortname"=>"shortName","description"=>"description","units"=>"unitsOfMeasure","tags"=>"tags","model"=>"itemModel","itemtype"=>"itemTypeId","manufacturer"=>"manufactures","stockable"=>"stockable","expirable"=>"expirable");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && $key == "manufacturer"){
					$mans = "";
					foreach ($values[$key] as $i){
						$mans = $mans.$i.",";
					}
					$fields[$val] = $mans;
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Items";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("edititem?id=".$values['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("edititem?id=".$values['id']);
			}
		}
		$form_info = array();
		$form_info["name"] = "edititem";
		$form_info["action"] = "edititem";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "EDIT ITEM";
		
		$entity = \Items::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$manufacturers_arr = array();
			$manufacturers = \Manufacturers::all();
			foreach ($manufacturers as $manufacturer){
				$manufacturers_arr[$manufacturer->id] = $manufacturer->name;
			}
			
			$itemtypes_arr = array();
			$item_types = \ItemTypes::all();
			foreach ($item_types as $item_type){
				$itemtypes_arr[$item_type->id] = $item_type->name;
			}
			
			$parentId = -1;
			$parent = \InventoryLookupValues::where("name","=","UNITS OF MEASUREMENT")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$units =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
			$units_arr = array();
			foreach ($units  as $unit){
				$units_arr[$unit['id']] = $unit->name;
			}
			
			$form_fields = array();		
			$form_field = array("name"=>"name", "value"=>$entity->name, "content"=>"item name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number", "value"=>$entity->number,  "content"=>"item number", "readonly"=>"","required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"shortname", "value"=>$entity->shortName,  "content"=>"short name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"description", "value"=>$entity->description,  "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"units", "id"=>"units", "value"=>$entity->unitsOfMeasure,  "content"=>"units of measure", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$units_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tags", "value"=>$entity->tags,  "content"=>"tags", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"model", "value"=>$entity->itemModel,  "content"=>"item model", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"itemtype", "id"=>"itemtype", "value"=>$entity->itemTypeId,  "content"=>"item type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$itemtypes_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"manufacturer[]", "id"=>"manufacturer", "value"=>explode(",", $entity->manufactures),  "content"=>"manufacturer", "readonly"=>"",  "required"=>"required", "multiple"=>"multiple", "type"=>"select", "options"=>$manufacturers_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"stockable", "value"=>$entity->stockable,  "content"=>"stockable", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"expirable", "value"=>$entity->expirable,  "content"=>"expirable", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		
			$form_info["form_fields"] = $form_fields;
			return View::make("inventory.edit2colform",array("form_info"=>$form_info));
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function useItems()
	{
		$values = Input::all();
		$values['bredcum'] = "ITEMS";
		$values['home_url'] = '#';
		$values['add_url'] = 'additem';
		$values['form_action'] = 'useitems';
		$values['action_val'] = '#';
		$theads = array('Item', 'date', "qty (bill no)", "from warehouse", "to warehouse", "from vehicle", "to vehicle", "from action", "to action", "itemnumbers", "remarks", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"edititem?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "addusedstock";
		$form_info["action"] = "addusedstock";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "USE STOCK ";
		
		$warehouse_arr_total = array();
		$warehouse_arr = array();
		
		$ass_branches = AppSettingsController::getEmpBranches();
		$ass_branches_arr = array();
		foreach ($ass_branches as $ass_branch){
			$ass_branches_arr[] = $ass_branch["id"];
		}
		
		$warehouses = \OfficeBranch::whereIn("id",$ass_branches_arr)->where("isWareHouse","=","Yes")->get();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
		$warehouse_arr_total["main warehouses"] = $warehouse_arr;
		
		$emp_contracts = \Auth::user()->contractIds;
		$depots = null;
		if($emp_contracts=="" || $emp_contracts==0){
			$depots = \Depot::where("status","=","ACTIVE")->get();
		}
		else{
			$emp_contracts = explode(",", $emp_contracts);
			$depots = \Depot::whereIn("id",$emp_contracts)
							->where("depots.status","=","ACTIVE")
							->select(array("depots.id as id","depots.name as name"))->groupBy("depots.id")->get();
		}
		$depot_arr = array();
		foreach ($depots as $depot){
			$depot_arr[] = $depot->id;
		}
		
		if(!isset($values["stocktype"])){
			foreach ($warehouses as $warehouse){
				$warehouse_arr = array();
				$sub_warehouses = \Depot::where("status","=","ACTIVE")
									->whereIn("id",$depot_arr)
									->where("ParentWarehouse","=",$warehouse->id)->get();
				foreach ($sub_warehouses as $sub_warehouse){
					$warehouse_arr[$sub_warehouse->id] = $sub_warehouse->name."(".$sub_warehouse->code.")";
				}
				$warehouse_arr_total[$warehouse->name] = $warehouse_arr;
			}
		}
				
		
		$form_fields = array();	
		if(isset($values["stocktype"]) && $values["stocktype"] == "office"){
			$form_field = array("name"=>"action", "id"=>"action",  "content"=>"select action", "readonly"=>"", "required"=>"required", "type"=>"select", "action"=>array("type"=>"onchange","script"=>"getItems(this.value)"), "options"=>array("itemtowarehouse"=>"item","warehousetowarehouse"=>"warehouse"), "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
		}
		else{
			$form_field = array("name"=>"action", "id"=>"action",  "content"=>"select action", "readonly"=>"", "required"=>"required", "type"=>"select", "action"=>array("type"=>"onchange","script"=>"getItems(this.value)"), "options"=>array("itemtovehicles"=>"item","warehousetowarehouse"=>"warehouse","vehicletovehicle"=>"vehicle to vehicle","vehicletowarehouse"=>"repairs","creditsuppliertowarehouse"=>"repairs return"), "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
		}
		$form_field = array("name"=>"date", "id"=>"date",  "content"=>"date", "readonly"=>"", "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"warehouse", "id"=>"warehouse",  "content"=>"warehouse", "readonly"=>"", "action"=>array("type"=>"onchange","script"=>"getItems(this.value)"), "required"=>"required", "type"=>"selectgroup", "options"=>$warehouse_arr_total, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"jsondata", "id"=>"jsondata", "value"=>"",  "content"=>"", "readonly"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		if(isset($values["stocktype"])&& $values["stocktype"] == "office"){
			$form_field = array("name"=>"stocktype", "id"=>"stocktype", "value"=>$values["stocktype"],  "content"=>"", "readonly"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
		else{
			$form_field = array("name"=>"stocktype", "id"=>"stocktype", "value"=>"nonoffice",  "content"=>"", "readonly"=>"", "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
// 		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
// 		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editmanufacturer";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "manufactures";
		$form_info["bredcum"] = "add manufacturer";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"name1", "content"=>"manufacturer name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description1", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		$values['provider'] = "";
		if(isset($values["fromdate"]) && isset($values["todate"]) && isset($values["warehouse1"])){
			$values['provider'] = "usedstock&fromdate=".$values["fromdate"]."&todate=".$values["todate"]."&warehouse=".$values["warehouse1"];
		}
		return View::make('inventory.usestockdatatable', array("values"=>$values));
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function getFields()
	{
		$values = Input::All();
		$form_fields = array();
		$form_info = array();
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
		$veh_actions_arr = array();
		$veh_actions =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$veh_actions_arr = array();
		foreach ($veh_actions  as $veh_action){
			$veh_actions_arr[$veh_action['id']] = $veh_action->name;
		}
	
		$select_fields = array();
		$select_fields[] = "items.name as name";
		$select_fields[] = "purchased_items.qty as qty";
		$select_fields[] = "purchased_items.unitPrice as unitPrice";
		$select_fields[] = "purchased_items.id as id";
	
		$stockitems =  \PurchasedOrders::where("officeBranchId","=",$values["warehouseid"])->where("purchased_items.status","=","ACTIVE")->join("purchased_items","purchased_items.purchasedOrderId","=","purchase_orders.id")->join("items","purchased_items.itemId","=","items.id")->select($select_fields)->get();
		$stockitems_arr = array();
		foreach ($stockitems as $stockitem){
			$stockitems_arr[$stockitem['id']] = $stockitem->name." - qty(".$stockitem->qty.") - Price for unit : ".$stockitem->unitPrice."";
		}
		//"itemtovehicles"=>"same item to multiple vehicles","itemstovehicle"=>"different items to same vehicle","vehicletowarehouse"=>"items moved to warehouse from vehicle","warehousetowarehouse"=>"items moved from warehouse to warehouse"
		if($values["action"] == "itemtowarehouse"){
			return view::make("inventory.usestockfields",array("values"=>$values));
		}
		if($values["action"] == "itemtovehicles"){
			return view::make("inventory.usestockfields",array("values"=>$values));
		}
		else if($values["action"] == "itemstovehicle"){
			return view::make("inventory.usestockfields",array("values"=>$values));
		}
		else if($values["action"] == "warehousetowarehouse"){
			/*$form_field = array("name"=>"towarehouse", "id"=>"paymenttype",  "content"=>"to warehouse", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"item[]", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "multiple"=>"multiple",  "action"=>array("type"=>"onchange","script"=>"getItemInfo(this.value)"), "options"=>$stockitems_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"units", "content"=>"units of measurement", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"usedqty", "content"=>"moved quantity", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;*/
			return view::make("inventory.usestockfields",array("values"=>$values));
		}
		else if($values["action"] == "TO WAREHOUSE" || $values["action"] == "TO CREDIT SUPPLIER"){
			return view::make("inventory.usestockfields",array("values"=>$values));
		}
		else if($values["action"] == "TO WAREHOUSE1" || $values["action"] == "TO VEHICLE1"){
			return view::make("inventory.usestockfields",array("values"=>$values));
		}
		else if($values["action"] == "vehicletovehicle"){
			$form_field = array("name"=>"alertdate", "id"=>"alertdate",  "content"=>"alert date", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$items_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fromvehicleno", "id"=>"fromvehicleno",  "content"=>"from vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fromaction", "id"=>"fromaction",  "content"=>"from action", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$veh_actions_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tovehicleno", "id"=>"tovehicleno",  "content"=>"to vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"toaction", "id"=>"toaction",  "content"=>"to action", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$veh_actions_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"usedqty", "content"=>"moved quantity", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
		}
	
// 		$form_field = array("name"=>"vehicleno", "id"=>"paymenttype",  "content"=>"vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"startreading", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"litres", "content"=>"litres", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"priceperlitre", "content"=>"price per litre", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calcTotal()"), "class"=>"form-control number");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"required", "type"=>"textarea", "class"=>"form-control");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"paymentpaid", "id"=>"paymenttype", "value"=>"No", "content"=>"payment paid", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>array("Yes"=>"YES","No"=>"NO"));
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD"));
// 		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$form_info["action"] = $values["action"];
		return view::make("inventory.paymentform",array("form_info"=>$form_info));
	}
		
	public function getItemInfo(){
		$values = Input::all();
		$itemid = $values["id"];
		$jsondata = array();
		
		$select_args = array();
		$select_args[] = "items.needAlert as needAlert";
		$select_args[] = "items.itemNumber as itemNumber";
		$select_args[] = "items.itemActions as itemActions";
		$select_args[] = "purchased_items.itemNumbers as itemNumbers";
		$select_args[] = "purchase_orders.filePath as filePath";
		$item = \PurchasedItems::where("purchased_items.id","=",$itemid)
						->where("purchased_items.status","=","ACTIVE")
						->join("items","items.id","=","purchased_items.itemId")
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->select($select_args)->first();
		
		$jsondata["itemactions"] = "<option value=''>-- select action --</option>";
		if($item->itemActions != ""){
			$jsondata["itemactionsstatus"] = "Yes";
			$itemactions = explode(",", $item->itemActions);
			$options_data = "<option value=''>-- select action --</option>";
			$actions = \InventoryLookupValues::wherein("id",$itemactions)->get();
			foreach ($actions as $action){
				$options_data = $options_data."<option value='".$action->id."' >".$action->name."</option>";
			}
			$jsondata["itemactions"] = $options_data;
		}
		$jsondata["filePath"] = $item->filePath;
		
		$jsondata["alertstatus"] = "No";
		if($item->needAlert == "Yes"){
			$jsondata["alertstatus"] = "Yes";
		}
		
		$jsondata["itemnumberstatus"] = "No";
		if($item->itemNumber == "Yes"){
			$jsondata["itemnumberstatus"] = "Yes";
			$itemnumbers_arr = explode(",", $item->itemNumbers);
			$options_data = "<option value=''>-- select item number --</option>";
			foreach ($itemnumbers_arr as $itemnumber){
				if($itemnumber != ""){
					$options_data = $options_data."<option value='".$itemnumber."' >".$itemnumber."</option>";
				}
			}
			$jsondata["itemnumbers"] = $options_data;
		}
		
		$select_args = array("inventorylookupvalues.name as unitsOfMeasure");
		$item = \PurchasedItems::where("purchased_items.id","=",$itemid)
					->leftjoin("items","items.id","=","purchased_items.itemId")
					->leftjoin("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")
					->select($select_args)->first();
		$jsondata["units"] = $item->unitsOfMeasure;
		echo json_encode($jsondata);
	}
	
	public function getRepairItemsBySupplier(){
		$values = Input::all();
		$stockitems =  \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.creditSupplierId","=",$values["supplierid"])
						->where("purchase_orders.type","=","TO CREDIT SUPPLIER REPAIR")
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->select(array("items.name as name", "items.shortName as shortName", "purchased_items.id as id", "purchased_items.qty as qty"))->get();
		$stockitems_arr = array();
		foreach ($stockitems as $stockitem){
			$stockitems_arr[$stockitem['id']] = $stockitem->name." (".$stockitem->qty.")";
		}
		foreach($stockitems_arr as  $key=>$val){
			echo "<option value='".$key."' >".$val."</option>";
		}
	}
	
	public function getAlertInfo(){
		$values = Input::all();
		$itemid = $values["id"];
		$alert = "No";
		$item = \PurchasedItems::where("purchased_items.id","=",$itemid)->join("items","items.id","=","purchased_items.itemId")->select(array("needAlert"))->first();
		$alert = $item->needAlert;
		echo $alert;
	}
	
	public function deleteUsedStockItem(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		\DB::beginTransaction();
		$ret_val = "success";
		try{
			$table = "\InventoryTransactions";
			$data = array('id'=>$values['id']);
			$entity = $db_functions_ctrl->get($table, $data);
			$entity = $entity[0];
			$usedqty = $entity->qty;
			$used_item_numbers = $entity->itemNumbers;
			
			if($entity->action=="warehousetowarehouse"){
				$table = "\PurchasedItems";
				$data = array('id'=>$entity->stockItemId);
				$entity = $db_functions_ctrl->get($table, $data);
				if(count($entity)>0){
					$entity = $entity[0];
					
					$updateqty = $entity->qty+$usedqty;
					$update_item_numbers = $used_item_numbers.$entity->itemNumbers;
					$table = "\PurchasedItems";
					$fields  = array("qty"=>$updateqty,"itemNumbers"=>$update_item_numbers);
					$data = array('id'=>$entity->id);
					$db_functions_ctrl->update($table, $fields, $data);
					\PurchasedItems::where("parentOrderItemId","=",$entity->id)->update(array("status"=>"DELETED"));
				}
				//$fields = array("qty"=>$updateqty,"status"=>"DELETED");
			}
			else{
				$table = "\PurchasedItems"; 
				$data = array('id'=>$entity->stockItemId);
				$entity = $db_functions_ctrl->get($table, $data);
				if(count($entity)>0){
					$entity = $entity[0];
					$updateqty = $entity->qty+$usedqty;
					$update_item_numbers = $used_item_numbers.$entity->itemNumbers;
					$table = "\PurchasedItems";
					$fields  = array("qty"=>$updateqty,"itemNumbers"=>$update_item_numbers);
					$data = array('id'=>$entity->id);
					$db_functions_ctrl->update($table, $fields, $data);
				}
			}
			
			$table = "\InventoryTransactions";
			$fields = array("status"=>"DELETED");
			$data = array('id'=>$values['id']);
			$db_functions_ctrl->update($table, $fields, $data);
		
		}
		catch(\Exception $ex){
			\DB::rollback();
			echo "fail";
			return;
		}
		\DB::commit();
		echo $ret_val;
	}
	
}
