<?php namespace reports;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class DataTableController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function getDataTableData()
	{
		$values = Input::All();
		$start = $values['start'];
		$length = $values['length'];
		$total = 0;
		$data = array();
		
		if(isset($values["name"]) && $values["name"]=="stockpurchase") {
			$ret_arr = $this->getStockPurchase($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="vehiclestockhistory") {
			$ret_arr = $this->getVehicleStockHistory($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="repairstock") {
			$ret_arr = $this->getRepairStock($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="expense") {
			$ret_arr = $this->getExpenseTransactions($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="vehicle_repairs") {
			$ret_arr = $this->getVehicleRepairs($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="getrepairtransactionitems") {
			$ret_arr = $this->getRepairTransactionItems($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="loginlog") {
			$ret_arr = $this->getLoginLog($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		
		$json_data = array(
				"draw"            => intval( $_REQUEST['draw'] ),
				"recordsTotal"    => intval( $total ),
				"recordsFiltered" => intval( $total ),
				"data"            => $data
			);
		echo json_encode($json_data);
	}
	
	private function getStockPurchase($values, $length, $start){
		$total = 0;
		$data = array();
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$values['warehouse']="";
			$values['fromdate']="";
		}
		if(!isset($values['warehouse']) || !isset($values['fromdate'])){
			return array("total"=>$total, "data"=>$data);
		}
		$select_args = array();
		$select_args[] = "officebranch.name as officeBranchId";
		$select_args[] = "items.name as item";
		$select_args[] = "manufactures.name as manufacturer";
		$select_args[] = "purchased_items.qty as qty";
		$select_args[] = "purchased_items.qty as totalAmount";
		$select_args[] = "purchase_orders.orderDate as orderDate";
		$select_args[] = "creditsuppliers.suppliername as creditSupplierId";
		$select_args[] = "employee1.fullName as incharge";
		$select_args[] = "purchase_orders.billNumber as billNumber";
		$select_args[] = "purchase_orders.status as paymentInfo";
		$select_args[] = "purchase_orders.comments as comments";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchase_orders.id as id";
		$select_args[] = "purchase_orders.amountPaid as amountPaid";
		$select_args[] = "purchase_orders.paymentType as paymentType";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchased_items.unitPrice as unitPrice";
		$select_args[] = "purchase_orders.filePath as filePath";
		$select_args[] = "item_types.name as itemtype";
		$actions = array();
		$jobs = \Session::get("jobs");
		if(in_array(331, $jobs)){
			$action = array("url"=>"editpurchaseorder?", "type"=>"", "css"=>"primary", "js"=>"modalEditPurchaseOrder(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#","css"=>"danger", "id"=>"deletePurchaseOrder", "type"=>"", "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$item_arr = array();
			$items = \Items::where("items.name","like","%$search%")->get();
			foreach ($items as $item){
				$item_arr[] = $item->id;
			}
			$entities = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","PURCHASE ORDER")
						->whereIn("items.id",$item_arr)
						->leftjoin("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->leftjoin("items","items.id","=","purchased_items.itemId")
						->leftjoin("item_types","item_types.id","=","items.itemTypeId")
						->leftjoin("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->leftjoin("employee","employee.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee1","employee1.id","=","purchase_orders.inchargeId")
						->select($select_args)->orderBy("purchase_orders.orderDate","desc")->limit($length)->offset($start)->get();
			$total = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","PURCHASE ORDER")
						->leftjoin("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->leftjoin("items","items.id","=","purchased_items.itemId")
						->whereIn("items.id",$item_arr)->count();
		}
		else{
			$fromdt = date("Y-m-d",strtotime($values['fromdate']));
			$todt = date("Y-m-d",strtotime($values['todate']));
			$entities = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","PURCHASE ORDER")
						->where("purchase_orders.officeBranchId", "=", $values["warehouse"])
						->whereBetween("purchase_orders.orderDate",array($fromdt,$todt))
						->leftjoin("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->leftjoin("items","items.id","=","purchased_items.itemId")
						->leftjoin("item_types","item_types.id","=","items.itemTypeId")
						->leftjoin("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->leftjoin("employee","employee.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee1","employee1.id","=","purchase_orders.inchargeId")
						->select($select_args)->orderBy("purchase_orders.orderDate","desc")->limit($length)->offset($start)->get();
			$total = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","PURCHASE ORDER")
						->leftjoin("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->where("purchase_orders.officeBranchId", "=", $values["warehouse"])
						->whereBetween("purchase_orders.orderDate",array($fromdt,$todt))->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["item"] = $entity["item"]."(".$entity["itemtype"].")";
			$entity["paymentInfo"] = "Amount Paid : ".$entity["amountPaid"]."<br/>"."Payment Type : ".$entity["paymentType"];
			$entity["totalAmount"] = sprintf('%0.2f',$entity["qty"]*$entity["unitPrice"]);
			if($entity["filePath"] != ""){
				if($entity["filePath"]==""){
					$entity["billNumber"] = "<span style='color:red; font-weight:bold;'>".$entity["billNumber"]."</span>";
				}
				else{
					$entity["billNumber"] = "<a href='../app/storage/uploads/".$entity["filePath"]."' target='_blank'>".$entity["billNumber"]."</a>";
				}
			}
			$entity["orderDate"] = date("d-m-Y",strtotime($entity["orderDate"]));
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else if($action['url'] == "#"){
					$action_data = $action_data."<button class='btn btn-minier btn-".$action["css"]."' onclick='".$action["id"]."(".$entity["id"].")' >".strtoupper($action["text"])."</button>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[12] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getVehicleStockHistory($values, $length, $start){
		$total = 0;
		$data = array();
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$values['vehicle']="";
			$values['fromdate']="";
		}
		if(!isset($values['vehicle']) || !isset($values['fromdate'])){
			return array("total"=>$total, "data"=>$data);
		}
		$select_args = array();
		$select_args[] = "vehicle.veh_reg as veh_reg";
		$select_args[] = "officebranch.name as officebranch";
		$select_args[] = "items.name as item";
		$select_args[] = "manufactures.name as manufacturer";
		$select_args[] = "purchased_items.qty as qty";
		$select_args[] = "purchased_items.qty as totalAmount";
		$select_args[] = "inventory_transaction.date as transactiondate";
		$select_args[] = "purchase_orders.orderDate as orderDate";
		$select_args[] = "creditsuppliers.suppliername as creditSupplierId";
		$select_args[] = "purchase_orders.billNumber as billNumber";
		$select_args[] = "purchase_orders.status as paymentInfo";
		$select_args[] = "inventory_transaction.remarks as comments";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchase_orders.id as id";
		$select_args[] = "purchase_orders.amountPaid as amountPaid";
		$select_args[] = "purchase_orders.paymentType as paymentType";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchased_items.unitPrice as unitPrice";
		$select_args[] = "purchase_orders.filePath as filePath";
		$select_args[] = "depots.name as depotName";
		$select_args[] = "officebranch.id as branchId";
		$actions = array();
		$jobs = \Session::get("jobs");
		if(in_array(329, $jobs)){
			$action = array("url"=>"editpurchaseorder?", "type"=>"", "css"=>"primary", "js"=>"modalEditPurchaseOrder(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#","css"=>"danger", "id"=>"deletePurchaseOrder", "type"=>"", "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$item_arr = array();
			$items = \Items::where("items.name","like","%$search%")->get();
			foreach ($items as $item){
				$item_arr[] = $item->id;
			}
			$entities = \InventoryTransactions::where("inventory_transaction.status","=","ACTIVE")
						->whereIn("items.id",$item_arr)			
						->join("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->join("vehicle","vehicle.id","=","inventory_transaction.toVehicleId")
						->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->leftjoin("officebranch","officebranch.id","=","inventory_transaction.fromWareHouseId")
						->leftjoin("depots","depots.id","=","inventory_transaction.fromWareHouseId")
						->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->join("employee","employee.id","=","purchase_orders.createdBy")
						->select($select_args)->orderBy("inventory_transaction.date","desc")->limit($length)->offset($start)->get();
			$total = \InventoryTransactions::where("inventory_transaction.status","=","ACTIVE")
						->join("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")
						->join("items","items.id","=","purchased_items.itemId")
						->whereIn("items.id",$item_arr)->count();
		}
		else{
			$fromdt = date("Y-m-d",strtotime($values['fromdate']));
			$todt = date("Y-m-d",strtotime($values['todate']));
			$entities = \InventoryTransactions::where("toVehicleId","=",$values["vehicle"])
						->where("inventory_transaction.status","=","ACTIVE")
						->whereBetween("inventory_transaction.date",array($fromdt,$todt))
						->join("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->join("vehicle","vehicle.id","=","inventory_transaction.toVehicleId")
						->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->leftjoin("officebranch","officebranch.id","=","inventory_transaction.fromWareHouseId")
						->leftjoin("depots","depots.id","=","inventory_transaction.fromWareHouseId")
						->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->join("employee","employee.id","=","purchase_orders.createdBy")
						->select($select_args)->orderBy("inventory_transaction.date","desc")->limit($length)->offset($start)->get();
			$total = \InventoryTransactions::where("toVehicleId","=",$values["vehicle"])
						->whereBetween("inventory_transaction.date",array($fromdt,$todt))->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["orderDate"] = date("d-m-Y",strtotime($entity["orderDate"]));
			$entity["transactiondate"] = date("d-m-Y",strtotime($entity["transactiondate"]));
			$entity["paymentInfo"] = "Amount Paid : ".$entity["amountPaid"]."<br/>"."Payment Type : ".$entity["paymentType"];
			$entity["totalAmount"] = sprintf('%0.2f',$entity["qty"]*$entity["unitPrice"]);
			if($entity["filePath"] != ""){
				if($entity["filePath"]==""){
					$entity["billNumber"] = "<span style='color:red; font-weight:bold;'>".$entity["billNumber"]."</span>";
				}
				else{
					$entity["billNumber"] = "<a href='../app/storage/uploads/".$entity["filePath"]."' target='_blank'>".$entity["billNumber"]."</a>";
				}
			}
			if($entity["branchId"] == "" || $entity["branchId"] == 0){
				$entity["officebranch"] = $entity["depotName"];
			}
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else if($action['url'] == "#"){
					$action_data = $action_data."<button class='btn btn-minier btn-".$action["css"]."' onclick='".$action["id"]."(".$entity["id"].")' >".strtoupper($action["text"])."</button>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[14] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getRepairStock($values, $length, $start){
		$total = 0;
		$data = array();
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$values['creditsupplier']="";
			$values['fromdate']="";
		}
		if(!isset($values['creditsupplier']) || !isset($values['fromdate'])){
			return array("total"=>$total, "data"=>$data);
		}
		$select_args = array();
		$select_args[] = "officebranch.name as officeBranchId";
		$select_args[] = "items.name as item";
		$select_args[] = "manufactures.name as manufacturer";
		$select_args[] = "purchased_items.qty as qty";
		$select_args[] = "purchase_orders.totalAmount as totalAmount";
		$select_args[] = "purchase_orders.orderDate as orderDate";
		$select_args[] = "creditsuppliers.suppliername as creditSupplierId";
		$select_args[] = "employee1.fullName as incharge";
		$select_args[] = "purchase_orders.billNumber as billNumber";
		$select_args[] = "purchase_orders.status as paymentInfo";
		$select_args[] = "purchase_orders.comments as comments";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchase_orders.id as id";
		$select_args[] = "purchase_orders.amountPaid as amountPaid";
		$select_args[] = "purchase_orders.paymentType as paymentType";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchased_items.unitPrice as unitPrice";
		$select_args[] = "purchase_orders.filePath as filePath";
		$select_args[] = "depots.name as depotName";
		$select_args[] = "officebranch.id as branchId";
		$actions = array();
		$jobs = \Session::get("jobs");
		if(in_array(331, $jobs)){
			$action = array("url"=>"editpurchaseorder?", "type"=>"", "css"=>"primary", "js"=>"modalEditPurchaseOrder(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#","css"=>"danger", "id"=>"deletePurchaseOrder", "type"=>"", "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
	if($search != ""){
			$item_arr = array();
			$items = \Items::where("items.name","like","%$search%")->get();
			foreach ($items as $item){
				$item_arr[] = $item->id;
			}
			$entities = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","TO CREDIT SUPPLIER")
						->whereIn("items.id",$item_arr)
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->leftjoin("depots","depots.id","=","purchase_orders.officeBranchId")
						->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->join("employee","employee.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee1","employee1.id","=","purchase_orders.inchargeId")
						->select($select_args)->orderBy("purchase_orders.orderDate","desc")->limit($length)->offset($start)->get();
			$total = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","TO CREDIT SUPPLIER")
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->whereIn("items.id",$item_arr)->count();
		}
		else{
			if(isset($values["item"]) && $values["item"] != 0 ){
				$fromdt = date("Y-m-d",strtotime($values['fromdate']));
				$todt = date("Y-m-d",strtotime($values['todate']));
				$entities = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","TO CREDIT SUPPLIER")
						->where("purchase_orders.creditSupplierId", "=", $values["creditsupplier"])
						->where("items.id", "=", $values["item"])
						->whereBetween("purchase_orders.orderDate",array($fromdt,$todt))
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->leftjoin("depots","depots.id","=","purchase_orders.officeBranchId")
						->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->join("employee","employee.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee1","employee1.id","=","purchase_orders.inchargeId")
						->select($select_args)->orderBy("purchase_orders.orderDate","desc")->limit($length)->offset($start)->get();
				$total = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
						->where("purchase_orders.type","=","TO CREDIT SUPPLIER")
						->where("items.id", "=", $values["item"])
						->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->where("purchase_orders.creditSupplierId", "=", $values["creditsupplier"])
						->join("items","items.id","=","purchased_items.itemId")
						->whereBetween("purchase_orders.orderDate",array($fromdt,$todt))->count();
				
			}
			else{
				$fromdt = date("Y-m-d",strtotime($values['fromdate']));
				$todt = date("Y-m-d",strtotime($values['todate']));
				$entities = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
							->where("purchase_orders.type","=","TO CREDIT SUPPLIER")
							->where("purchase_orders.creditSupplierId", "=", $values["creditsupplier"])
							->whereBetween("purchase_orders.orderDate",array($fromdt,$todt))
							->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
							->join("items","items.id","=","purchased_items.itemId")
							->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")
							->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
							->leftjoin("depots","depots.id","=","purchase_orders.officeBranchId")
							->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
							->join("employee","employee.id","=","purchase_orders.createdBy")
							->leftjoin("employee as employee1","employee1.id","=","purchase_orders.inchargeId")
							->select($select_args)->orderBy("purchase_orders.orderDate","desc")->limit($length)->offset($start)->get();
				$total = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
							->where("purchase_orders.type","=","TO CREDIT SUPPLIER")
							->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
							->where("purchase_orders.creditSupplierId", "=", $values["creditsupplier"])
							->whereBetween("purchase_orders.orderDate",array($fromdt,$todt))->count();
			}
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["orderDate"] = date("d-m-Y",strtotime($entity["orderDate"]));
			$entity["paymentInfo"] = "Amount Paid : ".$entity["amountPaid"]."<br/>"."Payment Type : ".$entity["paymentType"];
			$entity["totalAmount"] = sprintf('%0.2f',$entity["totalAmount"]);
			if($entity["filePath"] != ""){
				if($entity["filePath"]==""){
					$entity["billNumber"] = "<span style='color:red; font-weight:bold;'>".$entity["billNumber"]."</span>";
				}
				else{
					$entity["billNumber"] = "<a href='../app/storage/uploads/".$entity["filePath"]."' target='_blank'>".$entity["billNumber"]."</a>";
				}
			}
			if($entity["branchId"] == "" || $entity["branchId"] == 0){
				$entity["officeBranchId"] = $entity["depotName"];
			}
			
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else if($action['url'] == "#"){
					$action_data = $action_data."<button class='btn btn-minier btn-".$action["css"]."' onclick='".$action["id"]."(".$entity["id"].")' >".strtoupper($action["text"])."</button>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[12] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	
	private function getFuelTransactions($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		
		$select_args[] = "officebranch.name as branchId";
		$select_args[] = "fuelstationdetails.name as fuelStationName";
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "fueltransactions.filledDate as date";
		$select_args[] = "fueltransactions.amount as amount";
		$select_args[] = "fueltransactions.billNo as billNo";
		$select_args[] = "fueltransactions.paymentType as paymentType";
		$select_args[] = "fueltransactions.remarks as remarks";
		$select_args[] = "fueltransactions.id as id";
		
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTransaction(", "jsdata"=>array("id"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \Vehicle::where("veh_reg", "like", "%$search%")->where("vehicle.status","=","ACTIVE")->orwhere("vehicle.status","=","INACTIVE")->leftjoin("lookuptypevalues","lookuptypevalues.id", "=", "vehicle.vehicle_type")->select($select_args)->limit($length)->offset($start)->get();
			$total = \Vehicle::where("veh_reg", "like", "%$search%")->where("vehicle.status","=","ACTIVE")->orwhere("vehicle.status","=","INACTIVE")->count();
			foreach ($entities as $entity){
				$entity->yearof_pur = date("d-m-Y",strtotime($entity->yearof_pur));
			}
		}
		else if(isset($values["tripid"])){
			$entities = \FuelTransaction::where("tripId","=",$values["tripid"])->leftjoin("officebranch", "officebranch.id","=","fueltransactions.branchId")
			->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
			->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \FuelTransaction::where("tripId","=",$values["tripid"])->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
				
			$entities = \FuelTransaction::where("branchId","=",$values["branch1"])->whereBetween("filledDate",array($startdt,$enddt))->leftjoin("officebranch", "officebranch.id","=","fueltransactions.branchId")
				->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
				->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \FuelTransaction::where("branchId","=",$values["branch1"])->whereBetween("filledDate",array($startdt,$enddt))->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[8] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getExpenseTransactions($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "expensetransactions.transactionId as id";
		$select_args[] = "officebranch.name as branchId";
		$select_args[] = "lookuptypevalues.name as name";
		$select_args[] = "expensetransactions.date as date";
		$select_args[] = "expensetransactions.amount as amount";
		$select_args[] = "expensetransactions.paymentType as paymentType";
		$select_args[] = "expensetransactions.remarks as remarks";
		$select_args[] = "expensetransactions.transactionId as id";
		$select_args[] = "expensetransactions.lookupValueId as lookupValueId";
	
			
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTransaction(", "jsdata"=>array("id"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \ExpenseTransaction::where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","expensetransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","expensetransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \ExpenseTransaction::where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
			$entities = \ExpenseTransaction::where("branchId","=",$values["branch1"])->whereBetween("date",array($startdt,$enddt))->leftjoin("officebranch", "officebranch.id","=","expensetransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","expensetransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \ExpenseTransaction::where("branchId","=",$values["branch1"])->whereBetween("date",array($startdt,$enddt))->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			if($entity["lookupValueId"]>900){
				$expenses_arr = array();
				$expenses_arr["998"] = "CREDIT SUPPLIER PAYMENT";
				$expenses_arr["997"] = "FUEL STATION PAYMENT";
				$expenses_arr["996"] = "LOAN PAYMENT";
				$expenses_arr["995"] = "RENT";
				$expenses_arr["994"] = "INCHARGE ACCOUNT CREDIT";
				$expenses_arr["993"] = "PREPAID RECHARGE";
				$expenses_arr["992"] = "ONLINE OPERATORS";
				$entity["name"] = $expenses_arr[$entity["lookupValueId"]];
			}
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[7] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getRepairTransactionItems($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "lookuptypevalues.name as repairedItem";
		$select_args[] = "creditsuppliertransdetails.quantity as quantity";
		$select_args[] = "creditsuppliertransdetails.amount as amount";
		$select_args[] = "creditsuppliertransdetails.comments as comments";
		$select_args[] = "creditsuppliertransdetails.status as status";
		$select_args[] = "creditsuppliertransdetails.id as id";
		
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditPurchaseOrderItem(", "jsdata"=>array("id","repairedItem","quantity", "amount", "comments", "status"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \PurchasedOrders::where("name", "like", "%$search%")->join("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")->join("item_types","item_types.id","=","items.itemTypeId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \CreditSupplierTransDetails::where("creditSupplierTransId","=",$values["id"])->join("lookuptypevalues","lookuptypevalues.id","=","creditsuppliertransdetails.repairedItem")->select($select_args)->limit($length)->offset($start)->get();
			$total = \CreditSupplierTransDetails::where("creditSupplierTransId","=",$values["id"])->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[5] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getLoginLog($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "login_log.user_full_name as name";
		$select_args[] = "login_log.emailId as emailId";
		$select_args[] = "login_log.ipaddress as ipaddress";
		$select_args[] = "login_log.logindate as logindate";
		$select_args[] = "login_log.logintime as logintime";
	
		$actions = array();
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		echo "test";
		die();
		if(!isset($values["fromdate"]) || !isset($values["todate"])){
			return array("total"=>0, "data"=>array());
			die();
		}
		$frmdt = date("Y-m-d",strtotime($values["fromdate"]));
		$todt = date("Y-m-d",strtotime($values["todate"]));
		if($search != ""){
			$stations = array();
			$pss = PoliceStation::where("name", "like", "%$search%")->get();
			foreach ($pss as $ps){
				$stations[] = $ps->id;
			}
			$entities = \CaseDetails::whereIn("policeStationId",$stations)->join("policestations", "policestations.id","=","case_details.policestationId")->join("courts", "courts.id","=","case_details.courtId")->select($select_args)->get();
			$total = count($entities);
		}
		else{
			if(isset($values["username"]) && $values["username"] == 0){
				$entities = LoginLog::wherebetween("logindate",array($frmdt,$todt))->select($select_args)->limit($length)->offset($start)->get();
				$total = LoginLog::wherebetween("logindate",array($frmdt,$todt))->count();
			}
			elseif (isset($values["username"]) && $values["username"] > 0){
				$entities = LoginLog::wherebetween("logindate",array($frmdt,$todt))->where("user_id","=",$values["username"])->select($select_args)->limit($length)->offset($start)->get();
				$total = LoginLog::wherebetween("logindate",array($frmdt,$todt))->where("user_id","=",$values["username"])->count();
			}
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["logindate"] = date("d-m-Y", strtotime($entity["logindate"]));
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[5] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
}


