<?php namespace servicelogs;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use settings\AppSettingsController;
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
		else if(isset($values["name"]) && $values["name"]=="servicelogs") {
			$ret_arr = $this->getServiceLogs($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="servicelogrequests") {
			$ret_arr = $this->getServiceLogRequests($values, $length, $start);
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
			$entities = \Client::join("states","states.id", "=", "clients.stateId")->join("cities","cities.id", "=", "clients.cityId")->select($select_args)->limit($length)->offset($start)->get();
			$total = count(\Client::where("stateId","!=",0)->get());
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
	
	private function getServiceLogs($values, $length, $start){
		$total = 0;
		$data = array();
		
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			//$values['clientid']="";
			//$values['depotid']="";
		}
		if(!isset($values['clientid']) || !isset($values['depotid'])){
			return array("total"=>$total, "data"=>$data);
		}
		
		$select_args = array();
		$select_args[] = "vehicle.veh_reg as vehicleId"; 
		$select_args[] = "vehicle1.veh_reg as vehicleId1";
		$select_args[] = "service_logs.serviceDate as serviceDate";
		$select_args[] = "service_logs.startTime as startTime";
		$select_args[] = "service_logs.startReading as startReading";
		$select_args[] = "service_logs.endReading as endReading";
		$select_args[] = "service_logs.distance as distance";
		$select_args[] = "employee1.fullName as driver1Id";
		$select_args[] = "employee2.fullName as helperId";
		$select_args[] = "service_logs.status as tripno";
		$select_args[] = "service_logs.status as status";
		$select_args[] = "service_logs.id as id";
		$select_args[] = "service_logs.contractVehicleId as contractVehicleId";
		$select_args[] = "service_logs.remarks as remarks";
		$select_args[] = "service_logs.repairkms as repairkms";
		$select_args[] = "service_logs.startTime as startTime";
			
		$actions = array();
		if(in_array(408, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditServiceLog(", "jsdata"=>array("vehicleId", "serviceDate", "startTime", "startReading", "endReading", "distance", "repairkms", "remarks", "status", "id"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$vehs = \Vehicle::where("veh_reg","like","%$search%")->get();
			$vehs_arr = array();
			foreach ($vehs as  $veh){
				$vehs_arr[] = $veh->id;
			}
			$entities = \ServiceLog::where("service_logs.status", "=", "ACTIVE")
						->where("contracts.clientId","=",$values["clientid"])
						->where("contracts.depotId","=",$values["depotid"])
						->whereIn("contract_vehicles.vehicleId",$vehs_arr)
						->join("contracts","contracts.id", "=", "service_logs.contractId")
						->join("contract_vehicles","contract_vehicles.id", "=", "service_logs.contractVehicleId")
						->join("vehicle","vehicle.id", "=", "contract_vehicles.vehicleId")
						->leftjoin("vehicle as vehicle1","vehicle1.id", "=", "service_logs.substituteVehicleId")
						->leftjoin("employee as employee1","employee1.id", "=", "service_logs.driver1Id")
						->leftjoin("employee as employee2","employee2.id", "=", "service_logs.helperId")
						->select($select_args)->orderby("serviceDate","asc")->limit($length)->offset($start)->get();
			$total = \ServiceLog::where("service_logs.status", "=", "ACTIVE")
						->where("contracts.clientId","=",$values["clientid"])
						->where("contracts.depotId","=",$values["depotid"])
						->whereIn("contract_vehicles.vehicleId",$vehs_arr)
						->join("contracts","contracts.id", "=", "service_logs.contractId")
						->join("contract_vehicles","contract_vehicles.id", "=", "service_logs.contractVehicleId")
						->join("vehicle","vehicle.id", "=", "contract_vehicles.vehicleId")->count();
		}
		else{
			$entities = \ServiceLog::where("service_logs.status", "=", "ACTIVE")
						->where("contracts.clientId","=",$values["clientid"])
						->where("contracts.depotId","=",$values["depotid"])
						//->where("contract_vehicles.status","=","ACTIVE")
						->join("contracts","contracts.id", "=", "service_logs.contractId")
						->join("contract_vehicles","contract_vehicles.id", "=", "service_logs.contractVehicleId")
						->join("vehicle","vehicle.id", "=", "contract_vehicles.vehicleId")
						->leftjoin("vehicle as vehicle1","vehicle1.id", "=", "service_logs.substituteVehicleId")
						->leftjoin("employee as employee1","employee1.id", "=", "service_logs.driver1Id")
						->leftjoin("employee as employee2","employee2.id", "=", "service_logs.helperId")
						->select($select_args)->orderby("serviceDate","asc")->limit($length)->offset($start)->get();
			$total = \ServiceLog::where("service_logs.status", "=","ACTIVE")
						->where("clientId","=",$values["clientid"])
						->where("depotId","=",$values["depotid"])
						//->where("contract_vehicles.status","=","ACTIVE")
						->join("contract_vehicles","contract_vehicles.id", "=", "service_logs.contractVehicleId")
						->join("contracts","contracts.id", "=", "service_logs.contractId")->count();
		}
		$tripno = 1;
		$start = 0;
		$service_dt = "";
		$vehicleId = "";
		if(count($entities)>0){
			$entity = $entities[0];
			$entities = $entities->toArray();
			$tripno = 1;
			$start = 0;
			$service_dt = $entity["serviceDate"];
			$vehicleId = $entity["vehicleId"];
		}
		foreach($entities as $entity){
			if($start>0 && $entity["serviceDate"]==$service_dt && $vehicleId == $entity["vehicleId"]){
				$tripno++;
				$entity["tripno"] = $tripno;
			}
			else{
				$service_dt = $entity["serviceDate"];
				$vehicleId = $entity["vehicleId"];
				$tripno= 1;
				$entity["tripno"] = $tripno;
			}
			$start++;
			$entity["serviceDate"] = date("d-m-Y",strtotime($entity["serviceDate"]));
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
	
	private function getServiceLogRequests($values, $length, $start){
		$total = 0;
		$data = array();
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$values['clientid']="";
			$values['depotid']="";
			$values['logstatus']="";
		}
		if(!isset($values['clientid']) || !isset($values['depotid'])){
			return array("total"=>$total, "data"=>$data);
		}
		$logstatus_arr = array("Send for Approval", "Requested","Open","Closed");
		if(isset($values['logstatus']) && $values['logstatus']!="All"){
			$logstatus_arr = array($values['logstatus']);
		}
		$select_args = array();
		$select_args[] = "clients.name as clientId";
		$select_args[] = "depots.name as depotId";
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "servicelogrequests.pendingDates as pendingDates";
		$select_args[] = "servicelogrequests.customDate as customDate";
		$select_args[] = "servicelogrequests.comments as comments";
		$select_args[] = "employee.fullName as fullName";
		$select_args[] = "servicelogrequests.status as status";
		$select_args[] = "employee1.fullName as fullName1";
		$select_args[] = "servicelogrequests.opened_at as opened_at";
		$select_args[] = "servicelogrequests.id as id";
		$select_args[] = "servicelogrequests.deleted as deleted";
		
		$actions = array();
		if(in_array(417, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditServiceLogRequest(", "jsdata"=>array("id", "clientId", "depotId", "vehicleId", "pendingDates", "customDate", "comments", "status", "deleted"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$contract_arr =  array();
			$con_vehs = \DB::select(\DB::raw("select id from depots where name like '%$search%' and status='ACTIVE'"));
			foreach ($con_vehs as  $con_veh){
				$contract_arr[] = $con_veh->id;
			}
			$entities = \ServiceLogRequest::wherein("contracts.depotId",$contract_arr)
					->where("servicelogrequests.deleted", "=", "No")
					->join("contract_vehicles","contract_vehicles.id", "=", "servicelogrequests.vehicleId")
					->join("contracts","contracts.id", "=", "servicelogrequests.contractId")
					->join("clients","clients.id", "=", "contracts.clientId")
					->join("depots","depots.id", "=", "contracts.depotId")
					->join("vehicle","vehicle.id", "=", "servicelogrequests.vehicleId")
					->join("employee","employee.id", "=", "servicelogrequests.createdBy")
					->leftjoin("employee as employee1","employee1.id", "=", "servicelogrequests.openedBy")
					->select($select_args)->limit($length)->offset($start)->get();
			$total = \ServiceLogRequest::wherein("contracts.depotId",$contract_arr)
					->join("contracts","contracts.id", "=", "servicelogrequests.contractId")
					->where("servicelogrequests.deleted", "=", "No")->count();
		}
		else{
			$depotids_str = \Auth::user()->contractIds;
			if($depotids_str == ""){
				$depots = \Depot::where("status","=","ACTIVE")->get();
				foreach($depots as $depot){
					$depotids_str = $depotids_str.$depot->id.",";
				}
				$depotids_str = substr($depotids_str, 0, strlen($depotids_str)-1);
			}
			if(isset($values["clientid"]) && $values["clientid"] != 0){
				$contract_arr =  array();
				$con_vehs = \DB::select(\DB::raw("select id from contracts where clientId=".$values["clientid"]." and depotId in(".$depotids_str.")"));
				foreach ($con_vehs as  $con_veh){
					$contract_arr[] = $con_veh->id;
				}
				$entities = \ServiceLogRequest::wherein("servicelogrequests.contractId",$contract_arr)
						->where("servicelogrequests.deleted", "=", "No")
						->whereIn("servicelogrequests.status",$logstatus_arr)
						->leftjoin("contracts","contracts.id", "=", "servicelogrequests.contractId")
						->leftjoin("clients","clients.id", "=", "contracts.clientId")
						->leftjoin("depots","depots.id", "=", "contracts.depotId")
						->leftjoin("contract_vehicles","contract_vehicles.id", "=", "servicelogrequests.vehicleId")
						->leftjoin("vehicle","vehicle.id", "=", "contract_vehicles.vehicleId")
						->leftjoin("employee","employee.id", "=", "servicelogrequests.createdBy")
						->leftjoin("employee as employee1","employee1.id", "=", "servicelogrequests.openedBy")
						->select($select_args)->limit($length)->offset($start)->get();
				$total = \ServiceLogRequest::wherein("servicelogrequests.contractId",$contract_arr)
						->where("servicelogrequests.deleted", "=", "No")
						->whereIn("servicelogrequests.status",$logstatus_arr)->count();
			}
			else{
				$entities = \ServiceLogRequest::where("servicelogrequests.deleted", "=", "no records")->get();
			}
			/* else{
				$entities = \ServiceLogRequest::where("servicelogrequests.deleted", "=", "No")
						->whereIn("servicelogrequests.status",$logstatus_arr)
						->join("contracts","contracts.id", "=", "servicelogrequests.contractId")
						->join("clients","clients.id", "=", "contracts.clientId")
						->join("depots","depots.id", "=", "contracts.depotId")
						->join("vehicle","vehicle.id", "=", "servicelogrequests.vehicleId")
						->join("employee","employee.id", "=", "servicelogrequests.createdBy")
						->leftjoin("employee as employee1","employee1.id", "=", "servicelogrequests.openedBy")
						->select($select_args)->limit($length)->offset($start)->get();
				$total = \ServiceLogRequest::where("servicelogrequests.deleted", "=", "No")->count();
			} */
			
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			if($entity["customDate"] != "" && $entity["customDate"] != "1970-01-01"){
				$entity["customDate"] = date("d-m-Y",strtotime($entity["customDate"]));
			}
			else{
				$entity["customDate"] = "";
			}
			if($entity["fullName1"] != ""){
				$entity["opened_at"] = date("d-m-Y H:i",strtotime($entity["opened_at"]));
			}
			else{
				$entity["opened_at"] = "";
			}
			
			$dts = $entity["pendingDates"];
			$dts = explode(",", $dts);
			$dts_str = "";
			foreach ($dts as $dt){
				if($dt != ""){
					$dts_str = $dts_str.date("d-m-Y",strtotime($dt)).", ";
				}
			}
			$entity["pendingDates"] = $dts_str;
			
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
			
			if(in_array(417, $this->jobs)){
				$data_values[11] = $action_data = '<input type="hidden" name="recid[]" value='.$entity["id"].' /> <label> <input name="action[]" type="checkbox" class="ace" value="'.$entity['id'].'"> <span class="lbl">&nbsp;</span></label>';
			}
			else{
				$data_values[11] = " ";
			}
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
}


