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
		else if(isset($values["name"]) && $values["name"]=="estimatepurchaseorder") {
			$ret_arr = $this->getEstimatePurchaseOrder($values, $length, $start);
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
		
		if(in_array(323, $this->jobs)){
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
	
		if(in_array(325, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditItemCategories(", "jsdata"=>array("id","name","description","status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \ItemCategories::where("name", "like", "%$search%")->select($select_args)->limit($length)->offset($start)->get();
			$total = \ItemCategories::where("name", "like", "%$search%")->count();
		}
		else{
			$entities = \ItemCategories::where("id",">",0)->select($select_args)->limit($length)->offset($start)->get();
			$total = \ItemCategories::count();
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
		//$select_args[] = "item_categories.name as itemCategoryId";
		$select_args[] = "item_types.description as description";
		$select_args[] = "item_types.status as status";
		$select_args[] = "item_types.id as id";
		$select_args[] = "item_types.name as itemCategoryId";
		$actions = array();
	
		if(in_array(327, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditItemType(", "jsdata"=>array("id","name","itemCategoryId", "description","status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			//->join("item_categories","item_categories.id","=","item_types.itemCategoryId")
			$entities = \ItemTypes::where("item_types.name", "like", "%$search%")->select($select_args)->limit($length)->offset($start)->get();
			$total = \ItemTypes::where("item_types.name", "like", "%$search%")->count();
		}
		else{
			//join("item_categories","item_categories.id","=","item_types.itemCategoryId")->
			$entities = \ItemTypes::select($select_args)->limit($length)->offset($start)->get();
			$total = \ItemTypes::count();
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
		$select_args[] = "items.itemTypeId as itemTypeId";
		$select_args[] = "items.manufactures as manufactures";
		$select_args[] = "items.stockable as stockable";
		$select_args[] = "items.expirable as expirable";
		$select_args[] = "items.status as status";
		$select_args[] = "items.id as id";
	
		$actions = array();
	
		if(in_array(329, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditItem(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \Items::where("items.name", "like", "%$search%")
							->leftjoin("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")
							->select($select_args)->limit($length)->offset($start)->get();
			$total = \Items::where("items.name", "like", "%$search%")->count();
		}
		else{
			$entities = \Items::leftjoin("inventorylookupvalues","inventorylookupvalues.id","=","items.unitsOfMeasure")
						->select($select_args)->limit($length)->offset($start)->get();
			$total = \Items::count();
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
			
			$itemtypes = "";
			$itemtypes_arr = explode(",",$entity["itemTypeId"]);
			foreach ($itemtypes_arr as $itemtype){
				if($itemtype != "") {
					$itemtp = \ItemTypes::where("id","=",$itemtype)->get();
					$itemtp = $itemtp[0];
					$itemtp = $itemtp->name;
					$itemtypes = $itemtypes.$itemtp.", ";
				}
			}			
			$entity["itemTypeId"] = $itemtypes;
			
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
		if(in_array(321, $this->jobs)){
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
		$select_args[] = "purchase_orders.type as stocktype";
		$select_args[] = "purchase_orders.type as items";
		$select_args[] = "employee4.fullName as incharge";
		$select_args[] = "employee.fullName as receivedBy";
		$select_args[] = "purchase_orders.orderDate as orderDate";
		$select_args[] = "purchase_orders.paymentDate as paymentDate";
		$select_args[] = "purchase_orders.billNumber as billNumber";
		$select_args[] = "purchase_orders.amountPaid as amountPaid";
		$select_args[] = "purchase_orders.paymentType as paymentType";
		$select_args[] = "purchase_orders.totalAmount as totalAmount";
		$select_args[] = "purchase_orders.comments as comments";
		$select_args[] = "purchase_orders.status as status";
		$select_args[] = "employee2.fullName as createdBy";
		$select_args[] = "purchase_orders.workFlowStatus as workFlowStatus";
		$select_args[] = "employee3.fullName as updatedBy";
		$select_args[] = "purchase_orders.workFlowRemarks as workFlowRemarks";
		$select_args[] = "purchase_orders.id as id";
		$select_args[] = "purchase_orders.filePath as filePath";
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
			$entities = \PurchasedOrders::where("creditsuppliers.supplierName", "like", "%$search%")
						->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->leftjoin("employee","employee.id","=","purchase_orders.receivedBy")
						->leftjoin("employee as employee2", "employee2.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee3", "employee3.id","=","purchase_orders.updatedBy")
						->leftjoin("employee as employee4", "employee4.id","=","purchase_orders.inchargeId")
						->select($select_args)->limit($length)->offset($start)->get();
			$total = \PurchasedOrders::where("creditsuppliers.supplierName", "like", "%$search%")
						->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->where("purchase_orders.status","ACTIVE")->count();
		}
		else{
			$entities = \PurchasedOrders::where("purchase_orders.status","ACTIVE")
						->whereIn("purchase_orders.type",array("PURCHASE ORDER","OFFICE PURCHASE ORDER"))
						->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->leftjoin("employee","employee.id","=","purchase_orders.receivedBy")
						->leftjoin("employee as employee2", "employee2.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee3", "employee3.id","=","purchase_orders.updatedBy")
						->leftjoin("employee as employee4", "employee4.id","=","purchase_orders.inchargeId")
						->select($select_args)->limit($length)->offset($start)->get();
			$total = \PurchasedOrders::where("purchase_orders.status","ACTIVE")
						->whereIn("purchase_orders.type",array("PURCHASE ORDER","OFFICE PURCHASE ORDER"))->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$items = \PurchasedItems::leftjoin("items","items.id","=","purchased_items.itemId")
						->where("purchasedOrderId","=",$entity["id"])->where("purchased_items.status","=","ACTIVE")
						->select("items.name as name","purchased_items.purchasedQty as qty")->Get();
			$item_str = "";
			foreach ($items as $item){
				$item_str = $item_str.$item->name." (".$item->qty."),<br/>";
			}
			$entity["items"] = $item_str;
			if($entity["billNumber"] != ""){
				if($entity["filePath"]==""){
					$entity["billNumber"] = "<span style='color:red; font-weight:bold;'>".$entity["billNumber"]."</span>";
				}
				else{
					$entity["billNumber"] = "<a href='../app/storage/uploads/".$entity["filePath"]."' target='_blank'>".$entity["billNumber"]."</a>";
				}
				
			}
			
			if($entity["stocktype"] == "OFFICE PURCHASE ORDER"){
				$entity["stocktype"] = "OFFICE STOCK";
			}
			else{
				$entity["stocktype"] = "NON OFFICE STOCK";
			}
			$entity["orderDate"] = date("d-m-Y",strtotime($entity["orderDate"]));
			$pmtdate = date("d-m-Y",strtotime($entity["paymentDate"]));
			if($pmtdate=="00-00-0000" || $pmtdate=="01-01-1970"){
				$pmtdate = "";
			}
			$entity["paymentDate"] = $pmtdate;
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
					$action['url'] = "editpurchaseorder?stocktype=nonoffice";
					if($entity["stocktype"] == "OFFICE STOCK"){
						$action['url'] = "editpurchaseorder?stocktype=office";
					}
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			if(isset($entity["workFlowStatus"]) && $entity["workFlowStatus"]=="Approved"){
				$action_data = "";
			}
			$data_values[15] = $action_data;
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
		$select_args[] = "inventory_transaction.itemNumbers as itemNumbers";
		$select_args[] = "inventory_transaction.remarks as remarks";
		$select_args[] = "inventory_transaction.status as status";
		$select_args[] = "inventory_transaction.action as action";
		$select_args[] = "inventory_transaction.id as id";
		$select_args[] = "purchased_items.itemId as itemId";
		$select_args[] = "inventory_transaction.meeterReading as meeterReading";
		$select_args[] = "purchase_orders.billNumber as billNumber";
	
		$actions = array();
		//$action = array("url"=>"editusedstock?", "type"=>"", "css"=>"primary", "js"=>"modalEditPurchaseOrderItem(", "jsdata"=>array("id","itemId","manufacturerId", "qty", "unitPrice", "itemStatus", "status"), "text"=>"EDIT");
		//$actions[] = $action;
		if(in_array(331, $this->jobs)){
			$action = array("url"=>"#","css"=>"danger", "id"=>"deleteUsedStockItem", "type"=>"", "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
		$entities = array();
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if(isset($values["repairs"])){
			$total = 0;
			$data = array();
			$select_args = array();
			$select_args[] = "creditsuppliers.supplierName as creditSupplierId";
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
			$select_args[] = "purchase_orders.type as type";
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
				$entities = \PurchasedOrders::where("creditsuppliers.supplierName", "like", "%$search%")
								->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
								->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
								->join("employee","employee.id","=","purchase_orders.receivedBy")
								->select($select_args)->limit($length)->offset($start)->get();
				$total = count($entities);
			}
			else{
				$values["fromdate"] = date("Y-m-d",strtotime($values["fromdate"]));
				$values["todate"] = date("Y-m-d",strtotime($values["todate"]));
				$entities = \PurchasedOrders::where("purchase_orders.status","ACTIVE")
							->whereNotIn("purchase_orders.type",array("PURCHASE ORDER","OFFICE PURCHASE ORDER"))
							->where("purchase_orders.officeBranchId", "=", $values["warehouse"])
							->whereBetween("purchase_orders.orderDate",array($values["fromdate"],$values["todate"]))
							->leftjoin("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
							->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
							->leftjoin("employee","employee.id","=","purchase_orders.receivedBy")
							->select($select_args)->limit($length)->offset($start)->get();
				$total = \PurchasedOrders::where("purchase_orders.status","ACTIVE")
							->where("purchase_orders.type", "!=", "PURCHASE ORDER")
							->whereBetween("purchase_orders.orderDate",array($values["fromdate"],$values["todate"]))
							->count();
			}
		
			$entities = $entities->toArray();
			foreach($entities as $entity){
				$entity["orderDate"] = date("d-m-Y",strtotime($entity["orderDate"]));
				
				$select_args[] = "employee.fullName as receivedBy";
				$select_args[] = "purchase_orders.orderDate as orderDate";
				$select_args[] = "purchase_orders.billNumber as billNumber";
				$select_args[] = "purchase_orders.amountPaid as amountPaid";
				$select_args[] = "purchase_orders.paymentType as paymentType";
				$select_args[] = "purchase_orders.totalAmount as totalAmount";
				
				if($entity["type"]=="TO WAREHOUSE" || $entity["type"]=="TO WAREHOUSE REPAIR"){
					$vals = array("receivedBy","amountPaid","paymentType","totalAmount");
					$entity["creditSupplierId"]= $entity["type"];
					foreach ($vals as $val){
						$entity[$val] = "";
					}
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
						$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&type=repairs&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
				$data_values[10] = $action_data;
				$data[] = $data_values;
			}
			return array("total"=>$total, "data"=>$data);
		}
		else{
			if($search != ""){
				$entities = \InventoryTransactions::where("items.name","like","%$search%")->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->limit($length)->offset($start)->get();
				$total = \InventoryTransactions::where("items.name","like","%$search%")->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->count();
				if($total<= 0){
					$vehids = \Vehicle::where("veh_reg","like","%$search%")->get();
					$vehids_arr = array();
					foreach ($vehids as $vehid){
						$vehids_arr[] = $vehid->id;
					}
					$entities = \InventoryTransactions::whereIn("fromVehicleId",$vehids_arr)
									->orWhereIn("toVehicleId",$vehids_arr)
									->where("inventory_transaction.status","=","ACTIVE")
									->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")
									->leftjoin("purchased_orders","purchased_orders.id","=","purchased_items.purchasedOrderId")
									->leftjoin("items","items.id","=","purchased_items.itemId")
									->select($select_args)->limit($length)->offset($start)->get();
					$total = \InventoryTransactions::whereIn("fromVehicleId",$vehids_arr)->orWhereIn("toVehicleId",$vehids_arr)->where("inventory_transaction.status","=","ACTIVE")->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")->leftjoin("items","items.id","=","purchased_items.itemId")->select($select_args)->count();
				}
			}
			else{
				if(isset($values["fromdate"]) && isset($values["todate"]) && isset($values["warehouse"])){
					$values["fromdate"] = date("Y-m-d",strtotime($values["fromdate"]));
					$values["todate"] = date("Y-m-d",strtotime($values["todate"]));
					$sql = \InventoryTransactions::where("fromWareHouseId","=",$values["warehouse"])
									->where("inventory_transaction.status","=","ACTIVE");
									if(isset($values["stocktype"]) && $values["stocktype"]=="office"){
										$sql = $sql->where("purchase_orders.type","=","OFFICE PURCHASE ORDER");
									}
									else{
										$sql = $sql->where("purchase_orders.type","!=","OFFICE PURCHASE ORDER");
									}
									$sql->where("inventory_transaction.status","=","ACTIVE")
									->wherebetween("date",array($values["fromdate"],$values["todate"]))
									->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")
									->leftjoin("purchase_orders","purchased_items.purchasedOrderId","=","purchase_orders.id")
									->leftjoin("items","items.id","=","purchased_items.itemId");
					 $entities = 	$sql->select($select_args)->limit($length)->offset($start)->get();
				
					$total =\InventoryTransactions::where("fromWareHouseId","=",$values["warehouse"])
									->where("inventory_transaction.status","=","ACTIVE")
									->wherebetween("date",array($values["fromdate"],$values["todate"]))->count();
				}
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
		$warehouses =  \Depot::All();
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
			if($entity["billNumber"] != ""){
				$entity["qty"] = $entity["qty"]." (".$entity["billNumber"].")";
			}
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			if($entity["fromVehicleId"] != 0){
				$entity["fromVehicleId"] = $vehicles_arr[$entity["fromVehicleId"]];
			}
			else{
				$entity["fromVehicleId"] = "";
			}
			if($entity["toVehicleId"] != 0){
				$entity["toVehicleId"] = $vehicles_arr[$entity["toVehicleId"]];
				if($entity["meeterReading"] != 0){
					$entity["toVehicleId"] = $entity["toVehicleId"]."(".$entity["meeterReading"].")";
				}
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
			$data_values[12] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getEstimatePurchaseOrder($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "items.name as itemsname";
		$select_args[] = "manufactures.name as manufacturesname";
		$select_args[] = "creditsuppliers.supplierName as supplierName";
		$select_args[] = "estimate_purchase_order_details.quantity as quantity";
		$select_args[] = "estimate_purchase_order_details.unitprice as unitprice";
		$select_args[] = "estimate_purchase_order_details.remarks as remarks";
		$select_args[] = "estimatepurchaseorder.id as id";
			
		$actions = array();
		if(in_array(402, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditEstimatePurchaseOrder(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities =\EstimatePurchaseOrder::join("estimate_purchase_order_details","estimatepurchaseorder.id", "=", "estimate_purchase_order_details.estimate_purchase_order_id")
												->join("items","items.id", "=", "estimate_purchase_order_details.itemId")
												->join("manufactures","manufactures.id", "=", "estimate_purchase_order_details.manufactureId")
												->join("creditsuppliers","creditsuppliers.id", "=", "estimate_purchase_order_details.creditsupplierId")
												->where("creditsuppliers.supplierName","like","%$search%")
												->select($select_args)->limit($length)->offset($start)->get();
			$total = \EstimatePurchaseOrder::where("estimatepurchaseorder.id",">",0)->count();
		}
		else{
			if(isset($values["branchid"]) && $values["branchid"] == 0){
				$entities =\EstimatePurchaseOrder::join("estimate_purchase_order_details","estimatepurchaseorder.id", "=", "estimate_purchase_order_details.estimate_purchase_order_id")
												->join("items","items.id", "=", "estimate_purchase_order_details.itemId")
												->join("manufactures","manufactures.id", "=", "estimate_purchase_order_details.manufactureId")
												->join("creditsuppliers","creditsuppliers.id", "=", "estimate_purchase_order_details.creditsupplierId")
												->where("estimatepurchaseorder.id",">",0)
												->select($select_args)->limit($length)->offset($start)->get();
				$total = \EstimatePurchaseOrder::where("estimatepurchaseorder.id",">",0)->count();
			}
			else{
				$entities =\EstimatePurchaseOrder::join("estimate_purchase_order_details","estimatepurchaseorder.id", "=", "estimate_purchase_order_details.estimate_purchase_order_id")
												->join("items","items.id", "=", "estimate_purchase_order_details.itemId")
												->join("manufactures","manufactures.id", "=", "estimate_purchase_order_details.manufactureId")
												->join("creditsuppliers","creditsuppliers.id", "=", "estimate_purchase_order_details.creditsupplierId")
												->where("estimatepurchaseorder.branchId","=",$values["branchid"])
												->select($select_args)->limit($length)->offset($start)->get();
				$total = \EstimatePurchaseOrder::where("estimatepurchaseorder.id",">",0)->count();
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
			$data_values[7] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
}


