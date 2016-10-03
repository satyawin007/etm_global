<?php namespace contracts;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
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
		
		if(isset($values["name"]) && $values["name"]=="clients") {
			$ret_arr = $this->getClients($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="depots") {
			$ret_arr = $this->getDepots($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="contracts") {
			$ret_arr = $this->getContracts($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="vehiclemeeters") {
			$ret_arr = $this->getVehicleMeeters($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="clientholidays") {
			$ret_arr = $this->getClientHolidays($values, $length, $start);
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
	

	private function getClients($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "clients.id as clientId";
		$select_args[] = "clients.name as clientName";
		$select_args[] = "clients.code as clientCode";
// 		$select_args[] = "states.name as stateName";
// 		$select_args[] = "cities.name as cityName";
		$select_args[] = "clients.status as status";
		$select_args[] = "clients.id as id";
			
		$actions = array();
		if(in_array(404, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditClient(", "jsdata"=>array("id","clientName","clientCode",  "status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \Client::where("clients.name", "like", "%$search%")->join("states","states.id", "=", "clients.stateId")->join("cities","cities.id", "=", "clients.cityId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \Client::select($select_args)->limit($length)->offset($start)->get();
			$total = \Client::count();
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
	
	private function getDepots($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "depots.id as depotId";
		$select_args[] = "depots.name as depotName";
		$select_args[] = "depots.code as depotCode";
		$select_args[] = "officebranch.name as parentwarehouse";
		$select_args[] = "cities.name as cityName";
		$select_args[] = "districts.name as districtName";
		$select_args[] = "states.name as stateName";
		$select_args[] = "depots.status as status";
		$select_args[] = "depots.id as id";
			
		$actions = array();
		if(in_array(406, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditDepot(", "jsdata"=>array("id","depotName","depotCode","parentwarehouse", "cityName", "districtName", "stateName", "status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \Depot::where("depots.name", "like", "%$search%")
					->join("states","states.id", "=", "depots.stateId")
					->join("officebranch","officebranch.id", "=", "depots.parentWarehouse")
					->join("cities","cities.id", "=", "depots.cityId")
					->join("districts","districts.id", "=", "depots.districtId")
					->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \Depot::join("states","states.id", "=", "depots.stateId")
					->join("cities","cities.id", "=", "depots.cityId")
					->join("officebranch","officebranch.id", "=", "depots.parentWarehouse")
					->join("districts","districts.id", "=", "depots.districtId")
					->select($select_args)->limit($length)->offset($start)->get();
			$total = count(\Depot::where("stateId","!=",0)->get());
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
	
	private function getVehicleMeeters($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "vehiclemeterdetails.meterNo as meterNo";
		$select_args[] = "vehiclemeterdetails.startDate as startDate";
		$select_args[] = "vehiclemeterdetails.endDate as endDate";
		$select_args[] = "vehiclemeterdetails.startReading as startReading";
		$select_args[] = "vehiclemeterdetails.endReading as endReading";
		$select_args[] = "vehiclemeterdetails.status as status";
		$select_args[] = "vehiclemeterdetails.id as id";
		$actions = array();
		if(in_array(414, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditVehicleMeeter(", "jsdata"=>array("id","vehicleId","meterNo", "startDate", "endDate", "startReading", "endReading", "status"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \VehicleMeeter::where("vehicle.veh_reg", "like", "%$search%")
						->leftjoin("vehicle","vehicle.id", "=", "vehiclemeterdetails.vehicleId")
						->select($select_args)->limit($length)->offset($start)->get();
			$total = count($entities);
		}
		else{
			$entities = \VehicleMeeter::leftjoin("vehicle","vehicle.id", "=", "vehiclemeterdetails.vehicleId")
			->select($select_args)->limit($length)->offset($start)->get();
			$total = \VehicleMeeter::All()->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["startDate"] = date("d-m-Y",strtotime($entity["startDate"]));
			if($entity["endDate"] != ""){
				$entity["endDate"] = date("d-m-Y",strtotime($entity["endDate"]));
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
	
	private function getContracts($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "clients.name as clientId";
		$select_args[] = "depots.name as depotId";
		$select_args[] = "contracts.routeId as routeId";
		$select_args[] = "contracts.routeId as vehicles";
		$select_args[] = "lookuptypevalues.name as vehicleTypeId";
		$select_args[] = "contracts.distance as distance";
		$select_args[] = "contracts.contractType as contractType";
		$select_args[] = "contracts.startDate as startDate";
		$select_args[] = "contracts.floorRate as floorRate";
		$select_args[] = "contracts.status as status";
		$select_args[] = "contracts.id as id";
		$select_args[] = "contracts.endDate as endDate";
			
		$actions = array();
		if(in_array(402, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditContract(", "jsdata"=>array("id"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$contract_arr =  array();
			//$con_vehs = \DB::select(\DB::raw("select contractId from contract_vehicles where vehicleId in(select id from vehicle where veh_reg like '%$search%')"));
			$con_vehs = \DB::select(\DB::raw("select id as contractId from contracts where depotId in(select id from depots where name like '%$search%')"));
			foreach ($con_vehs as  $con_veh){
				$contract_arr[] = $con_veh->contractId;
			}
			$entities = \Contract::where("contracts.status", "!=", "DELETED")->whereIn("contracts.id", $contract_arr)
							->leftjoin("clients","clients.id", "=", "contracts.clientId")
							->leftjoin("depots","depots.id", "=", "contracts.depotId")
							->leftjoin("lookuptypevalues","lookuptypevalues.id", "=", "contracts.vehicleTypeId")
							->select($select_args)->limit($length)->offset($start)->get();
			$total = \Contract::where("contracts.status", "!=", "DELETED")->count();
		}
		else{
			if(isset($values["clientid"]) && $values["clientid"] != 0){
				$entities = \Contract::where("contracts.status", "!=", "DELETED")->where("contracts.clientId", "=", $values["clientid"])
						->leftjoin("clients","clients.id", "=", "contracts.clientId")
						->leftjoin("depots","depots.id", "=", "contracts.depotId")
						->leftjoin("lookuptypevalues","lookuptypevalues.id", "=", "contracts.vehicleTypeId")
						->select($select_args)->limit($length)->offset($start)->get();
				$total = \Contract::where("contracts.status", "!=", "DELETED")->where("contracts.clientId", "=", $values["clientid"])->count();
			}
			else{
				$entities = \Contract::where("contracts.status", "!=", "DELETED")
						->leftjoin("clients","clients.id", "=", "contracts.clientId")
						->leftjoin("depots","depots.id", "=", "contracts.depotId")
						->leftjoin("lookuptypevalues","lookuptypevalues.id", "=", "contracts.vehicleTypeId")
						->select($select_args)->limit($length)->offset($start)->get();
				$total = \Contract::where("contracts.status", "!=", "DELETED")->count();
			}
			
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["startDate"] = date("d-m-Y",strtotime($entity["startDate"]))." TO ".date("d-m-Y",strtotime($entity["endDate"]));
			$services =  \DB::select(\DB::raw("select servicedetails.id as id, city1.name as name1, city2.name as name2, servicedetails.description from servicedetails join cities as city1 on city1.id=servicedetails.sourceCity join cities as city2 on servicedetails.destinationCity=city2.id where servicedetails.id=".$entity["routeId"].";"));
			$services_data = "";
			foreach ($services as $service){
				$desc = "";
				if($service->description != ""){
					$desc = " ".$service->description;
				}
				$services_data = $service->name1."-".$service->name2.$desc."<br/>";
			}
			$entity["routeId"] = $services_data;
			
			$floorrates =  \ContractVehicle::where("contractId","=",$entity["id"])->where("contract_vehicles.status","=","ACTIVE")
							->select("floorRate as floorRate")->get();
			$floorrates_str = "";
			foreach ($floorrates as $floorrate){
				if($floorrate->floorRate != "0.00"){
					$floorrates_str = $floorrate->floorRate."<br/>".$floorrates_str;
				}
			}
			if($entity["floorRate"] != 0){
				$entity["floorRate"] = $entity["floorRate"]."<br/>".$floorrates_str;
			}
			else{
				$entity["floorRate"] = $floorrates_str;
			}
			
			
			$routes =  \ContractVehicle::where("contractId","=",$entity["id"])->where("contract_vehicles.status","=","ACTIVE")
							->select("routes as routes")->get();
			if(count($routes)>0){
				$routes_str = "";
				foreach ($routes as $route){
					if($route->routes!=""){
						$routes_str = $routes_str.",".$route->routes;
					}
				}
				$routes_str = substr($routes_str, 1);
				if($routes_str != ""){
					$routes = explode(",",$routes_str);
					foreach ($routes as $route){
						$services =  \DB::select(\DB::raw("select servicedetails.id as id, city1.name as name1, city2.name as name2, servicedetails.description from servicedetails join cities as city1 on city1.id=servicedetails.sourceCity join cities as city2 on servicedetails.destinationCity=city2.id where servicedetails.id=".$route.";"));
						$services_data = "";
						foreach ($services as $service){
							$desc = "";
							if($service->description != ""){
								$desc = " ".$service->description;
							}
							$services_data = $service->name1."-".$service->name2.$desc.",<br/>";
						}
						$entity["routeId"] = $entity["routeId"].$services_data;
					}
				}
			}
			$convehicles =  \ContractVehicle::where("contractId","=",$entity["id"])->where("contract_vehicles.status","=","ACTIVE")
										->join("vehicle","vehicle.id","=","contract_vehicles.vehicleId")->select("vehicle.veh_reg as vehicle","vehicleStartDate as startDate")->get();
			$convehicles_data = "";
			foreach ($convehicles as $convehicle){
				$convehicles_data = $convehicles_data.$convehicle->vehicle."(".date("d-m-Y",strtotime($convehicle->startDate))."),<br/> ";
			}
			$entity["vehicles"] = $convehicles_data;
			
			$convehicles =  \ContractVehicle::where("contractId","=",$entity["id"])->where("contract_vehicles.status","=","ACTIVE")
										->join("lookuptypevalues","lookuptypevalues.id","=","contract_vehicles.vehicleTypeId")->select("lookuptypevalues.name as vehtype")->get();
			$convehicletypes_data = "";
			foreach ($convehicles as $convehicle){
				$convehicletypes_data = $convehicletypes_data.$convehicle->vehtype.", <br/>";
			}
			if($entity["vehicleTypeId"] != ""){
				$entity["vehicleTypeId"] = $entity["vehicleTypeId"].", ".$convehicletypes_data;
			}
			else{
				$entity["vehicleTypeId"] = $convehicletypes_data;
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
			$data_values[10] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getClientHolidays($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "clients.name as clientname";
		$select_args[] = "depots.name as depotname";
		$select_args[] = "clientholidays.fromDate as fromDate";
		$select_args[] = "clientholidays.toDate as toDate";
		$select_args[] = "clientholidays.comments as comments";
		$select_args[] = "clientholidays.status as status";
		$select_args[] = "clientholidays.deleted as deleted";
		$select_args[] = "employee.fullName as openedBy";
		$select_args[] = "clientholidays.opened_at as opened_at";
		$select_args[] = "clientholidays.id as id";
		
		$logstatus_arr = array("Send for Approval", "Pending", "Requested","Open","Closed");
		if(isset($values['logstatus']) && $values['logstatus']!="All"){
			$logstatus_arr = array($values['logstatus']);
		}
			
		$actions = array();
		if(in_array(416, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditClientHolidays(", "jsdata"=>array("id","clientname","depotname", "fromDate", "toDate", "comments", "status", "deleted"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
		
		if(!isset($values["clientid"])){
			$values["clientid"]=0;
		}
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		$depot_arr = explode(",", \Auth::user()->contractIds);
		if(\Auth::user()->contractIds==""){
			$depot_arr = array();
			$depots = \Depot::where("status","=","ACTIVE")->get();
			foreach ($depots as $depot){
				$depot_arr[] = $depot->id;
			}
		}
		if($search != ""){
			$entities = \ClientHolidays::where("depots.name","like","%$search%")
					->whereIn("depotId", $depot_arr)
					->where("clients.id", $values["clientid"])
					->leftjoin("contracts","contracts.id", "=", "clientholidays.contractId")
					->leftjoin("clients","clients.id", "=", "contracts.clientId")
					->leftjoin("depots","depots.id", "=", "contracts.depotId")
					->leftjoin("employee","employee.id", "=", "clientholidays.openedBy")
					->select($select_args)->orderBy("clientholidays.id","desc")->limit($length)->offset($start)->get();
			$total = \ClientHolidays::join("contracts","contracts.id", "=", "clientholidays.contractId")
							->leftjoin("depots","depots.id", "=", "contracts.depotId")
							->where("depots.name","like","%$search%")
							->where("contracts.clientId", $values["clientid"])
							->where("clientholidays.deleted","=","No")->whereIn("depotId", $depot_arr)->count();
		}
		else{
			$entities = \ClientHolidays::leftjoin("contracts","contracts.id", "=", "clientholidays.contractId")
							->leftjoin("clients","clients.id", "=", "contracts.clientId")
							->leftjoin("depots","depots.id", "=", "contracts.depotId")
							->where("clients.id","=", $values["clientid"])
							->whereIn("depotId", $depot_arr)
							->where("clientholidays.deleted","=","No")
							->whereIn("clientholidays.status",$logstatus_arr)
							->leftjoin("employee","employee.id", "=", "clientholidays.openedBy")
							->select($select_args)->orderBy("clientholidays.id","desc")->limit($length)->offset($start)->get();
			$total = \ClientHolidays::leftjoin("contracts","contracts.id", "=", "clientholidays.contractId")
							->leftjoin("clients","clients.id", "=", "contracts.clientId")
							->where("clients.id","=", $values["clientid"])
							->whereIn("depotId", $depot_arr)
							->where("clientholidays.deleted","=","No")
			                ->whereIn("clientholidays.status",$logstatus_arr)->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["fromDate"] = date("d-m-Y",strtotime($entity["fromDate"]));
			$entity["toDate"] = date("d-m-Y",strtotime($entity["toDate"]));
			$entity["opened_at"] = date("d-m-Y h:i:s ",strtotime($entity["opened_at"]));
			
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
			$data_values[9] = $action_data;
			if(in_array(416, $this->jobs)){
				$data_values[10] = $action_data = '<input type="hidden" name="recid[]" value='.$entity["id"].' /> <label> <input name="action[]" type="checkbox" class="ace" value="'.$entity['id'].'"> <span class="lbl">&nbsp;</span></label>';
			}
			else{
				$data_values[10] = " ";
			}
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
}


