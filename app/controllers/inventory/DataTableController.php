<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class DataTableController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	private $jobs;
	
	public function getDataTableData()
	{
		$this->jobs = \Session::get("jobs");
		$values = Input::All();
		$start = $values['start'];
		$length = $values['length'];
		$total = 0;
		$data = array();
		
		if(isset($values["name"]) && $values["name"]=="states") {
			$ret_arr = $this->getStates($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="inventorylookupvalues") {
			$ret_arr = $this->getInventoryLookupValues($values, $length, $start, $values["type"]);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="manufacturers") {
			$ret_arr = $this->getManufacturers($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="itemcategories") {
			$ret_arr = $this->getItemCategories($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="itemtypes") {
			$ret_arr = $this->getItemTypes($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="items") {
			$ret_arr = $this->getItems($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="purchaseorders") {
			$ret_arr = $this->getPurchaseOrders($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="getpurchaseorderitems") {
			$ret_arr = $this->getPurchaseOrderItems($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="usedstock") {
			$ret_arr = $this->getUsedStock($values, $length, $start);
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
	
	private function getManufacturers($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "manufactures.id as id";
		$select_args[] = "manufactures.name as name";
		$select_args[] = "manufactures.description as description";	
		$select_args[] = "manufactures.status as status";
		$select_args[] = "manufactures.id as id";
	
		$actions = array();
		
		if(in_array(321, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditManufacture(", "jsdata"=>array("id","name","description","status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
		
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \Manufacturers::where("name", "like", "%$search%")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \Manufacturers::where("id",">",0)->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
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
			$data_values[4] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getItemCategories($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "item_categories.id as id";
		$select_args[] = "item_categories.name as name";
		$select_args[] = "item_categories.description as description";
		$select_args[] = "item_categories.status as status";
		$select_args[] = "item_categories.id as id";
	
		$actions = array();
	
		if(in_array(323, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditManufacture(", "jsdata"=>array("id","name","description","status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \ItemCategories::where("name", "like", "%$search%")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \ItemCategories::where("id",">",0)->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
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
			$data_values[4] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getItemTypes($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "item_types.id as id";
		$select_args[] = "item_types.name as name";
		$select_args[] = "item_categories.name as itemCategoryId";
		$select_args[] = "item_types.description as description";
		$select_args[] = "item_types.status as status";
		$select_args[] = "item_types.id as id";
	
		$actions = array();
	
		if(in_array(325, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditItemType(", "jsdata"=>array("id","name","itemCategoryId", "description","status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \ItemTypes::where("item_types.name", "like", "%$search%")->join("item_categories","item_categories.id","=","item_types.itemCategoryId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \ItemTypes::join("item_categories","item_categories.id","=","item_types.itemCategoryId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
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
	
	private function getItems($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "items.name as name";
		$select_args[] = "items.description as description";
		$select_args[] = "items.shortName as shortName";
		$select_args[] = "inventorylookupvalues.name as unitsOfMeasure";
		$select_args[] = "items.tags as tags";
		$select_args[] = "items.itemModel as itemModel";
		$select_args[] = "item_types.name as itemTypeId";
		$select_args[] = "items.manufactures as manufactures";
		$select_args[] = "items.stockable as stockable";
		$select_args[] = "items.expirable as expirable";
		$select_args[] = "items.status as status";
		$select_args[] = "items.id as id";
	
		$actions = array();
	
		if(in_array(327, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditItem(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \Items::where("items.name", "like", "%$search%")->join("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")->join("item_types","item_types.id","=","items.itemTypeId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \Items::join("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")->join("item_types","item_types.id","=","items.itemTypeId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$mans = "";
			$mans_arr = explode(",",$entity["manufactures"]);
			foreach ($mans_arr as $man){
				if($man != "") {
					$man = \Manufacturers::where("id","=",$man)->get();
					$man = $man[0];
					$man = $man->name;
					$mans = $mans.$man.", ";
				}
			}
			$entity["manufactures"] = $mans;
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
			$data_values[11] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}

	private function getInventoryLookupValues($values, $length, $start, $typeId){
		$total = 0;
		$data = array();
		$select_args = array('name', "parentId", "remarks", "status", "id");
	
		$actions = array();
		if(in_array(319, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditLookupValue(", "jsdata"=>array("id","name","remarks","status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \InventoryLookupValues::where("name", "like", "%$search%")->select($select_args)->limit($length)->offset($start)->get();
			$parentName = \InventoryLookupValues::where("id","=",$values["type"])->get();
			if(count($parentName)>0){
				$parentName = $parentName[0];
				$parentName = $parentName->name;
				foreach ($entities as $entity){
					$entity->parentId = $parentName;
				}
			}
			$total = \LookupTypeValues::where("name", "like", "%$search%")->count();
		}
		else{
			$entities = \InventoryLookupValues::where("parentId", "=",$typeId)->select($select_args)->limit($length)->offset($start)->get();
			$parentName = \InventoryLookupValues::where("id","=",$values["type"])->get();
			if(count($parentName)>0){
				$parentName = $parentName[0];
				$parentName = $parentName->name;
				foreach ($entities as $entity){
					$entity->parentId = $parentName;
				}
			}
			$total = \InventoryLookupValues::where("parentId", "=",$typeId)->count();
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
			$data_values[4] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getPurchaseOrders($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "creditsuppliers.suppliername as creditSupplierId";
		$select_args[] = "officebranch.name as officeBranchId";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchase_orders.orderDate as orderDate";
		$select_args[] = "purchase_orders.billNumber as billNumber";
		$select_args[] = "purchase_orders.amountPaid as amountPaid";
		$select_args[] = "purchase_orders.paymentType as paymentType";
		$select_args[] = "purchase_orders.totalAmount as totalAmount";
		$select_args[] = "purchase_orders.comments as comments";
		$select_args[] = "purchase_orders.status as status";
		$select_args[] = "purchase_orders.id as id";
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
			$entities = \PurchasedOrders::where("creditsuppliers.supplierName", "like", "%$search%")->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")->join("employee","employee.id","=","purchase_orders.receivedBy")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \PurchasedOrders::where("purchase_orders.status","ACTIVE")->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")->join("employee","employee.id","=","purchase_orders.receivedBy")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
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
			$data_values[10] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getPurchaseOrderItems($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "items.name as itemId";
		$select_args[] = "manufactures.name as manufacturerId";
		$select_args[] = "purchased_items.qty as qty";
		$select_args[] = "purchased_items.unitPrice as unitPrice";
		$select_args[] = "purchased_items.itemStatus as itemStatus";
		$select_args[] = "purchased_items.status as status";
		$select_args[] = "purchased_items.id as id";
	
		$actions = array();
// 		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditPurchaseOrderItem(", "jsdata"=>array("id","itemId","manufacturerId", "qty", "unitPrice", "itemStatus", "status"), "text"=>"EDIT");
// 		$actions[] = $action;
		$action = array("url"=>"#","css"=>"danger", "id"=>"deletePurchaseOrderItem", "type"=>"", "text"=>"DELETE");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \PurchasedOrders::where("name", "like", "%$search%")->join("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")->join("item_types","item_types.id","=","items.itemTypeId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \PurchasedItems::where("purchasedOrderId","=",$values["id"])->join("items","items.id","=","purchased_items.itemId")->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")->select($select_args)->limit($length)->offset($start)->get();
			$total =\PurchasedItems::where("purchasedOrderId","=",$values["id"])->count();
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
				else if($action['url'] == "#"){
					$action_data = $action_data."<button class='btn btn-minier btn-".$action["css"]."' onclick='".$action["id"]."(".$entity["id"].")' >".strtoupper($action["text"])."</button>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[6] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getUsedStock($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "items.name as stockItemId";
		$select_args[] = "inventory_transaction.date as date";
		$select_args[] = "inventory_transaction.qty as qty";
		$select_args[] = "inventory_transaction.fromWareHouseId as fromWareHouseId";
		$select_args[] = "inventory_transaction.toWareHouseId as toWareHouseId";
		$select_args[] = "inventory_transaction.fromVehicleId as fromVehicleId";
		$select_args[] = "inventory_transaction.fromVehicleId as fromVehicleId";
		$select_args[] = "inventory_transaction.toVehicleId as toVehicleId";
		$select_args[] = "inventory_transaction.fromActionId as fromActionId";
		$select_args[] = "inventory_transaction.toActionId as toActionId";
		$select_args[] = "inventory_transaction.remarks as remarks";
		$select_args[] = "inventory_transaction.status as status";
		$select_args[] = "inventory_transaction.action as action";
		$select_args[] = "inventory_transaction.id as id";
		$select_args[] = "purchased_items.itemId as itemId";
	
		$actions = array();
		//$action = array("url"=>"editusedstock?", "type"=>"", "css"=>"primary", "js"=>"modalEditPurchaseOrderItem(", "jsdata"=>array("id","itemId","manufacturerId", "qty", "unitPrice", "itemStatus", "status"), "text"=>"EDIT");
		//$actions[] = $action;
		if(in_array(330, $this->jobs)){
			$action = array("url"=>"#","css"=>"danger", "id"=>"deleteUsedStockItem", "type"=>"", "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
		$entities = array();
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \InventoryTransactions::where("items.name","like","%$search%")->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \InventoryTransactions::where("items.name","like","%$search%")->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->count();
			if($total<= 0){
				$vehids = \Vehicle::where("veh_reg","like","%$search%")->get();
				$vehids_arr = array();
				foreach ($vehids as $vehid){
					$vehids_arr[] = $vehid->id;
				}
				$entities = \InventoryTransactions::whereIn("fromVehicleId",$vehids_arr)->orWhereIn("toVehicleId",$vehids_arr)->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->limit($length)->offset($start)->get();
				$total = \InventoryTransactions::whereIn("fromVehicleId",$vehids_arr)->orWhereIn("toVehicleId",$vehids_arr)->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->count();
			}
		}
		else{
			if(isset($values["fromdate"]) && isset($values["todate"]) && isset($values["warehouse"])){
				$values["fromdate"] = date("Y-m-d",strtotime($values["fromdate"]));
				$values["todate"] = date("Y-m-d",strtotime($values["todate"]));
				$entities = \InventoryTransactions::where("fromWareHouseId","=",$values["warehouse"])->where("inventory_transaction.status","=","ACTIVE")->wherebetween("date",array($values["fromdate"],$values["todate"]))->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->limit($length)->offset($start)->get();
				$total =\InventoryTransactions::where("fromWareHouseId","=",$values["warehouse"])->where("inventory_transaction.status","=","ACTIVE")->wherebetween("date",array($values["fromdate"],$values["todate"]))->count();
			}
		}
		
		$vehicles_arr = array();
		$vehicles =  \Vehicle::All();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
		}
		
		$warehouse_arr = array();
		$warehouses =  \OfficeBranch::All();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
		
		$vehactions_arr = array();
		$vehactions =  \InventoryLookupValues::All();
		foreach ($vehactions as $vehaction){
			$vehactions_arr[$vehaction->id] = $vehaction->name;
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			if($entity["fromVehicleId"] != 0){
				$entity["fromVehicleId"] = $vehicles_arr[$entity["fromVehicleId"]];
			}
			else{
				$entity["fromVehicleId"] = "";
			}
			if($entity["toVehicleId"] != 0){
				$entity["toVehicleId"] = $vehicles_arr[$entity["toVehicleId"]];
			}
			else{
				$entity["toVehicleId"] = "";
			}
			if($entity["fromWareHouseId"] != 0){
				$entity["fromWareHouseId"] = $warehouse_arr[$entity["fromWareHouseId"]];
			}
			else{
				$entity["fromWareHouseId"] = "";
			}
			if($entity["toWareHouseId"] != 0){
				$entity["toWareHouseId"] = $warehouse_arr[$entity["toWareHouseId"]];
			}
			else{
				$entity["toWareHouseId"] = "";
			}
			if($entity["fromActionId"] != 0){
				$entity["fromActionId"] = $vehactions_arr[$entity["fromActionId"]];
			}
			else{
				$entity["fromActionId"] = "";
			}
			if($entity["toActionId"] != 0 && isset($vehactions_arr[$entity["toActionId"]])){
				$entity["toActionId"] = $vehactions_arr[$entity["toActionId"]];
			}
			else{
				$entity["toActionId"] = "";
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
			$data_values[11] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
}


