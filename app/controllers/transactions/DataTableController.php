<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use masters\BlockDataEntryController;
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
		
		if(isset($values["name"]) && $values["name"]=="income") {
			$ret_arr = $this->getIncomeTransactions($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="fuel") {
			$ret_arr = $this->getFuelTransactions($values, $length, $start);
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
		
		$json_data = array(
				"draw"            => intval( $_REQUEST['draw'] ),
				"recordsTotal"    => intval( $total ),
				"recordsFiltered" => intval( $total ),
				"data"            => $data
			);
		echo json_encode($json_data);
	}
	
	private function getLookupValues($values, $length, $start, $typeId){
		$total = 0;
		$data = array();
		$select_args = array('name', "parentId", "remarks", "modules", "fields", "enabled", "status", "id");
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditLookupValue(", "jsdata"=>array("id","name","remarks","modules","fields","enabled","status"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){			
			$entities = \LookupTypeValues::where("name", "like", "%$search%")->select($select_args)->limit($length)->offset($start)->get();
			$parentName = \LookupTypeValues::where("id","=",$values["type"])->get();
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
			$entities = \LookupTypeValues::where("parentId", "=",$typeId)->select($select_args)->limit($length)->offset($start)->get();
			$parentName = \LookupTypeValues::where("id","=",$values["type"])->get();
			if(count($parentName)>0){
				$parentName = $parentName[0];
				$parentName = $parentName->name;
				foreach ($entities as $entity){
					$entity->parentId = $parentName;
				}
			}
			$total = \LookupTypeValues::where("parentId", "=",$typeId)->count();
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
	
	private function getIncomeTransactions($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "incometransactions.transactionId as id";
		$select_args[] = "officebranch.name as branchId";
		$select_args[] = "lookuptypevalues.name as name";
		$select_args[] = "incometransactions.date as date";
		$select_args[] = "incometransactions.amount as amount";
		$select_args[] = "incometransactions.paymentType as paymentType";
		$select_args[] = "incometransactions.remarks as remarks";
		$select_args[] = "incometransactions.transactionId as id";
		$select_args[] = "incometransactions.lookupValueId as lookupValueId";
		$select_args[] = "incometransactions.branchId as branch";
		
		$actions = array();
		if(in_array(302, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTransaction(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#delete", "type"=>"modal", "css"=>"danger", "js"=>"deleteTransaction(", "jsdata"=>array("id"), "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
			$entities = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("branchId","=",$values["branch1"])->whereBetween("date",array($startdt,$enddt))->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("branchId","=",$values["branch1"])->whereBetween("date",array($startdt,$enddt))->count();
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
				$expenses_arr["999"] = "PREPAID RECHARGE";
				$entity["name"] = $expenses_arr[$entity["lookupValueId"]];
			}
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			$bde = new BlockDataEntryController();
			$values1 = array("branch"=>$entity["branch"],"date"=>$entity["date"]);
			$valid = $bde->verifyTransactionDateandBranchLocally($values1);
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					
					if($valid=="YES"){
						$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
				else {
					if($valid=="YES"){
						$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
			}
			$data_values[7] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getVehicleRepairs($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$select_args[] = "clients.name as clientname";
		}
		else{
			$select_args[] = "officebranch.name as branchId";
		}
		$select_args[] = "creditsuppliers.supplierName as creditSupplierId";
		$select_args[] = "creditsuppliertransactions.date as date";
		$select_args[] = "creditsuppliertransactions.billNumber as billNumber";
		$select_args[] = "creditsuppliertransactions.paymentPaid as paymentPaid";
		$select_args[] = "creditsuppliertransactions.paymentType as paymentType";
		$select_args[] = "creditsuppliertransactions.amount as amount";
		$select_args[] = "creditsuppliertransactions.comments as comments";
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "creditsuppliertransactions.status as status";
		$select_args[] = "creditsuppliertransactions.labourCharges as labourCharges";
		$select_args[] = "creditsuppliertransactions.electricianCharges as electricianCharges";
		$select_args[] = "creditsuppliertransactions.batta as batta";
		$select_args[] = "creditsuppliertransactions.id as id";
		$select_args[] = "creditsuppliertransactions.branchId as branch";
		if(isset($values["type"]) && $values["type"]=="contracts"){
			$select_args[] = "depots.name as depotname";
		}
		$actions = array();
		if(in_array(308, $this->jobs)){
			$action = array("url"=>"editrepairtransaction?", "type"=>"", "css"=>"primary", "js"=>"modalEditRepairTransaction(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#","css"=>"danger", "id"=>"deleteRepairTransaction", "type"=>"", "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$supids_arr = array();
			$suppliers = \CreditSupplier::where("supplierName","like","%$search%")->get();
			foreach ($suppliers as $supplier){
				$supids_arr[] = $supplier->id;
			}
			$branchids_arr = array();
			$branches = \OfficeBranch::where("name","like","%$search%")->get();
			foreach ($branches as $branch){
				$branchids_arr[] = $branch->id;
			}
			$entities = \CreditSupplierTransactions::whereIn("creditsuppliertransactions.branchId",$branchids_arr)->orWhereIn("creditsuppliertransactions.creditSupplierId",$supids_arr)->where("creditsuppliertransactions.deleted","=","No")->leftjoin("vehicle", "vehicle.id","=","creditsuppliertransactions.vehicleId")->leftjoin("officebranch", "officebranch.id","=","creditsuppliertransactions.branchId")->leftjoin("creditsuppliers", "creditsuppliers.id","=","creditsuppliertransactions.creditSupplierId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \CreditSupplierTransactions::whereIn("creditsuppliertransactions.branchId",$branchids_arr)->orWhereIn("creditsuppliertransactions.creditSupplierId",$supids_arr)->where("creditsuppliertransactions.deleted","=","No")->count();
		}
		else if(isset($values["type"]) && $values["type"]=="contracts"){
			$entities = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","No")
							->where("contractId",">",0)
							->leftjoin("vehicle", "vehicle.id","=","creditsuppliertransactions.vehicleId")
							->leftjoin("creditsuppliers", "creditsuppliers.id","=","creditsuppliertransactions.creditSupplierId")
							->leftjoin("contracts", "contracts.id","=","creditsuppliertransactions.contractId")
							->leftjoin("clients", "clients.id","=","contracts.clientId")
							->leftjoin("depots", "depots.id","=","contracts.depotId")
							->select($select_args)->limit($length)->offset($start)->get();
			$total = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","No")
							->where("contractId",">",0)->count();
			foreach ($entities as $entity){
				$entity["clientname"] = $entity["depotname"]." (".$entity["clientname"].")";
			}
		}
		else{
			$entities = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","No")
							->leftjoin("vehicle", "vehicle.id","=","creditsuppliertransactions.vehicleId")
							->leftjoin("officebranch", "officebranch.id","=","creditsuppliertransactions.branchId")
							->leftjoin("creditsuppliers", "creditsuppliers.id","=","creditsuppliertransactions.creditSupplierId")
							->select($select_args)->limit($length)->offset($start)->get();
			$total = \CreditSupplierTransactions::count();
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			$entity["vehicleId"] = "<span style='color:red;' >VEHICLE : ".$entity["vehicleId"]."</span><br/>";
			$entity["vehicleId"] = $entity["vehicleId"]."Labour Charges : ".$entity["labourCharges"]."<br/>";
			$entity["vehicleId"] = $entity["vehicleId"]."Electricial Charges : ".$entity["electricianCharges"]."<br/>";
			$entity["vehicleId"] = $entity["vehicleId"]."Batta : ".$entity["batta"]."<br/>";
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			$bde = new BlockDataEntryController();
			$values1 = array("branch"=>$entity["branch"],"date"=>$entity["date"]);
			$valid = $bde->verifyTransactionDateandBranchLocally($values1);
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					
					if($valid=="YES"){
						$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
				else {
					if($valid=="YES"){
						$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
			}
			$data_values[10] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	
	private function getFuelTransactions($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		
		if(isset($values["contracts"]) && $values["contracts"]=="true"){
			$select_args[] = "clients.name as clientname";
		}
		else{
			$select_args[] = "officebranch.name as branchId";
		}
		$select_args[] = "fuelstationdetails.name as fuelStationName";
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "fueltransactions.filledDate as date";
		$select_args[] = "fueltransactions.amount as amount";
		$select_args[] = "fueltransactions.billNo as billNo";
		$select_args[] = "fueltransactions.paymentType as paymentType";
		$select_args[] = "fueltransactions.remarks as remarks";
		$select_args[] = "fueltransactions.id as id";
		$select_args[] = "fueltransactions.branchId as branch";
		if(isset($values["contracts"]) && $values["contracts"]=="true"){
			$select_args[] = "depots.name as depotname";
		}
		
		$actions = array();
		if(in_array(306, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTransaction(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#delete", "type"=>"modal", "css"=>"danger", "js"=>"deleteTransaction(", "jsdata"=>array("id"), "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		$entities = \Vehicle::where("id","=",0)->get();
		if($search != ""){
			$entities = \Vehicle::where("veh_reg", "like", "%$search%")->where("vehicle.status","=","ACTIVE")->orwhere("vehicle.status","=","INACTIVE")->leftjoin("lookuptypevalues","lookuptypevalues.id", "=", "vehicle.vehicle_type")->select($select_args)->limit($length)->offset($start)->get();
			$total = \Vehicle::where("veh_reg", "like", "%$search%")->where("vehicle.status","=","ACTIVE")->orwhere("vehicle.status","=","INACTIVE")->count();
			foreach ($entities as $entity){
				$entity->yearof_pur = date("d-m-Y",strtotime($entity->yearof_pur));
			}
		}
		else if(isset($values["tripid"])){
			$entities = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")->where("tripId","=",$values["tripid"])->leftjoin("officebranch", "officebranch.id","=","fueltransactions.branchId")
			->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
			->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")->where("tripId","=",$values["tripid"])->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else if(isset($values["contracts"]) && $values["contracts"]=="true" && isset($values["client"])){
			$entities = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
						->where("contractId",">",0)
						->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
						->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")
						->leftjoin("contracts", "contracts.id","=","fueltransactions.contractId")
						->leftjoin("clients", "clients.id","=","contracts.clientId")
						->leftjoin("depots", "depots.id","=","contracts.depotId")
						->select($select_args)->limit($length)->offset($start)->get();
			
			$total = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
								->where("contractId",">",0)->count();
			foreach ($entities as $entity){
				$entity["clientname"] = $entity["depotname"]." (".$entity["clientname"].")";
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else if(isset($values["branch1"])){
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
				
			$entities = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")->where("branchId","=",$values["branch1"])->whereBetween("filledDate",array($startdt,$enddt))->leftjoin("officebranch", "officebranch.id","=","fueltransactions.branchId")
				->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
				->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")->where("branchId","=",$values["branch1"])->whereBetween("filledDate",array($startdt,$enddt))->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			$bde = new BlockDataEntryController();
			$values1 = array("branch"=>$entity["branch"],"date"=>$entity["date"]);
			$valid = $bde->verifyTransactionDateandBranchLocally($values1);
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					
					if($valid=="YES"){
						$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
				else {
					if($valid=="YES"){
						$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
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
		$select_args[] = "expensetransactions.branchId as branch";
	
			
		$actions = array();
		if(in_array(304, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditTransaction(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
			$action = array("url"=>"#delete", "type"=>"modal", "css"=>"danger", "js"=>"deleteTransaction(", "jsdata"=>array("id"), "text"=>"DELETE");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","expensetransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","expensetransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->count();
			foreach ($entities as $entity){
				$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			}
		}
		else{
			$dtrange = $values["daterange"];
			$dtrange = explode(" - ", $dtrange);
			$startdt = date("Y-m-d",strtotime($dtrange[0]));
			$enddt = date("Y-m-d",strtotime($dtrange[1]));
			$entities = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")->where("branchId","=",$values["branch1"])->whereBetween("date",array($startdt,$enddt))->leftjoin("officebranch", "officebranch.id","=","expensetransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","expensetransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
			$total = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")->where("branchId","=",$values["branch1"])->whereBetween("date",array($startdt,$enddt))->count();
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
				$expenses_arr["991"] = "DAILY FINANCE PAYMENT";
				$entity["name"] = $expenses_arr[$entity["lookupValueId"]];
			}
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			$bde = new BlockDataEntryController();
			$values1 = array("branch"=>$entity["branch"],"date"=>$entity["date"]);
			$valid = $bde->verifyTransactionDateandBranchLocally($values1);
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					
					if($valid=="YES"){
						$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
				}
				else {
					if($valid=="YES"){
						$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
					}
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
}


