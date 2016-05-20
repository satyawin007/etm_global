<?php namespace servicelogs;

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
			$values['clientid']="";
			$values['depotid']="";
		}
		if(!isset($values['clientid']) || !isset($values['depotid'])){
			return array("total"=>$total, "data"=>$data);
		}
		
		$select_args = array();
		$select_args[] = "vehicle.veh_reg as vehicleId";
		$select_args[] = "service_logs.serviceDate as serviceDate";
		$select_args[] = "service_logs.startTime as startTime";
		$select_args[] = "service_logs.startReading as startReading";
		$select_args[] = "service_logs.endReading as endReading";
		$select_args[] = "service_logs.distance as distance";
		$select_args[] = "service_logs.driver1Id as driver1Id";
		$select_args[] = "service_logs.helperId as helperId";
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
			$entities = \ServiceLog::where("service_logs.status", "!=", "DELETED")->whereIn("contractVehicleId",$vehs_arr)
						->join("vehicle","vehicle.id", "=", "service_logs.contractVehicleId")
						->select($select_args)->orderby("serviceDate","desc")->limit($length)->offset($start)->get();
			$total = \ServiceLog::where("service_logs.status", "!=", "DELETED")->whereIn("contractVehicleId",$vehs_arr)->count();
		}
		else{
			$entities = \ServiceLog::where("service_logs.status", "!=", "DELETED")
						->join("vehicle","vehicle.id", "=", "service_logs.contractVehicleId")
						->select($select_args)->orderby("serviceDate","desc")->limit($length)->offset($start)->get();
			$total = \ServiceLog::where("service_logs.status", "!=", "DELETED")->count();
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
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
			$data_values[9] = $action_data;
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
		$logstatus_arr = array("Requested","Open","Closed");
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
			$con_vehs = \DB::select(\DB::raw("select id from vehicle where veh_reg like '%$search%'"));
			foreach ($con_vehs as  $con_veh){
				$contract_arr[] = $con_veh->id;
			}
			$entities = \ServiceLogRequest::wherein("servicelogrequests.vehicleId",$contract_arr)
					->where("servicelogrequests.deleted", "=", "No")
					->join("contracts","contracts.id", "=", "servicelogrequests.contractId")
					->join("clients","clients.id", "=", "contracts.clientId")
					->join("depots","depots.id", "=", "contracts.depotId")
					->join("vehicle","vehicle.id", "=", "servicelogrequests.vehicleId")
					->join("employee","employee.id", "=", "servicelogrequests.createdBy")
					->select($select_args)->limit($length)->offset($start)->get();
			$total = \ServiceLogRequest::wherein("servicelogrequests.vehicleId",$contract_arr)
					->where("servicelogrequests.deleted", "=", "No")->count();
		}
		else{	
			if(isset($values["clientid"]) && $values["clientid"] != 0){
				$contract_arr =  array();
				$con_vehs = \DB::select(\DB::raw("select id from contracts where clientId=".$values["clientid"]));
				foreach ($con_vehs as  $con_veh){
					$contract_arr[] = $con_veh->id;
				}
				$entities = \ServiceLogRequest::wherein("servicelogrequests.contractId",$contract_arr)
						->where("servicelogrequests.deleted", "=", "No")
						->whereIn("servicelogrequests.status",$logstatus_arr)
						->join("contracts","contracts.id", "=", "servicelogrequests.contractId")
						->join("clients","clients.id", "=", "contracts.clientId")
						->join("depots","depots.id", "=", "contracts.depotId")
						->join("vehicle","vehicle.id", "=", "servicelogrequests.vehicleId")
						->join("employee","employee.id", "=", "servicelogrequests.createdBy")
						->select($select_args)->limit($length)->offset($start)->get();
				$total = \ServiceLogRequest::wherein("servicelogrequests.vehicleId",$contract_arr)
						->where("servicelogrequests.deleted", "=", "No")->count();
			}
			else{
				$entities = \ServiceLogRequest::where("servicelogrequests.deleted", "=", "No")
						->whereIn("servicelogrequests.status",$logstatus_arr)
						->join("contracts","contracts.id", "=", "servicelogrequests.contractId")
						->join("clients","clients.id", "=", "contracts.clientId")
						->join("depots","depots.id", "=", "contracts.depotId")
						->join("vehicle","vehicle.id", "=", "servicelogrequests.vehicleId")
						->join("employee","employee.id", "=", "servicelogrequests.createdBy")
						->select($select_args)->limit($length)->offset($start)->get();
				$total = \ServiceLogRequest::where("servicelogrequests.deleted", "=", "No")->count();
			}
			
		}
	
		$entities = $entities->toArray();
		foreach($entities as $entity){
			if($entity["customDate"] != "" && $entity["customDate"] != "1970-01-01"){
				$entity["customDate"] = date("d-m-Y",strtotime($entity["customDate"]));
			}
			else{
				$entity["customDate"] = "";
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
			$data_values[8] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
}


