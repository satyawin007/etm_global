<?php namespace attendence;

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
		
		if(isset($values["name"]) && $values["name"]=="getattendence") {
			$ret_arr = $this->getEmployees($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="getattendencetoupdate") {
			$ret_arr = $this->getEmployeesToUpdate($values, $length, $start);
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
		else if(isset($values["name"]) && $values["name"]=="inchargetransactions") {
			$ret_arr = $this->getInchargeTransactions($values, $length, $start);
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
	
	private function getEmployees($values, $length, $start){
		//$values["DSF"];
		$total = 0;
		$data = array();
		$select_args = array("employee.id", "employee.fullName", "employee.empCode", "employee.joiningDate", "employee.terminationDate", "employee.status");
	
		$actions = array();
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
			if($values["employeetype"] == "CLIENT BRANCH"){
				//$length = 25; //$values["length"];
				//$start = $values["start"];
				if($values["depot"]=="0"){
					\DB::statement(\DB::raw("CALL contract_driver_helper_all('".$values["client"]."');"));
				}
				else {
					\DB::statement(\DB::raw("CALL contract_driver_helper('".$values["depot"]."', '".$values["client"]."');"));
				}
				$entities = $users = \DB::table('temp_contract_drivers_helpers')->skip($start)->take($length)->get(); //\DB::select( \DB::raw("select * from temp_contract_drivers_helpers group by id limit $length,0"));
				$total = $users = \DB::table('temp_contract_drivers_helpers')->count();
			}
			else{
				$entities = \Employee::whereRaw(" (roleId!=20 and roleId!=19) and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")
										->select($select_args)->limit($length)->offset($start)->get();
				$total = \Employee::whereRaw(" (roleId!=20 and roleId!=19) and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")->count();
			}
		}
	
		//$entities = $entities->toArray();
		foreach($entities as $entity){
			$date1 = strtotime(date("Y-m-d",strtotime($entity->terminationDate)));
			$month = date("m",strtotime($values["date"]));
			$year = date("Y",strtotime($values["date"]));
			$dt = (date("d-m-Y",strtotime("01"."-".$month."-".$year)));
			$date2 = strtotime(date("Y-m-d",strtotime($dt)));
			if($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" && $date1<$date2){
				continue;
			}
			$data_values = array();
			$branch = "";
			if($values["employeetype"] == "CLIENT BRANCH"){
				$emp = \ContractVehicle::whereRaw(" contract_vehicles.status='ACTIVE' and (driver1Id=".$entity->id." or driver2Id=".$entity->id." or driver3Id=".$entity->id." or driver4Id=".$entity->id." or driver5Id=".$entity->id." or helperId=".$entity->id.")")
										->leftjoin("contracts","contracts.id","=","contract_vehicles.contractId")
										->leftjoin("depots","depots.id","=","contracts.depotId")
										->select(array("depots.name as dname"))->get();
				if(count($emp)>0){
					$emp = $emp[0];
					$branch = "-".$emp->dname;
				}
			}
			else{
				/*$emp = \Employee::whereRaw(" status='ACTIVE' and (roleId!=20 and roleId!=19) and  empCode=".$entity->empCode." and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")->get();
				if(count($emp)>0){
					$emp = $emp[0];					
				}
				*/
			}
			$data_values[] = $entity->fullName."(".$entity->empCode.")".$branch;
			$month = date("m",strtotime($values["date"]));
			$year = date("Y",strtotime($values["date"]));
			$date = date_create(date("d-m-Y",strtotime("01"."-".$month."-".$year)));
			if($month === date("m")){
				$today = date_create(date("d-m-Y"));
			}
			else{
				$today = date_create(date("t-m-Y", strtotime($values["date"])));
			}
			$diff = date_diff($date,$today);
			$diff =  $diff->format("%a");
			
			$emptype ="office";
			if($values["employeetype"] == "CLIENT BRANCH"){
				$emptype = "driver";
			}
			
			for($i=0; $i<=$diff; $i++){
				$date1 = strtotime(date("Y-m-d",strtotime($entity->joiningDate)));
				$date2 = strtotime(date("Y-m-d",strtotime(date_format($date, 'Y-m-d'))));
				$date3 = strtotime(date('Y-m-01'));
				
				if($date1>$date2){
					$date = date_add($date, date_interval_create_from_date_string('1 days'));
					$data_values[] = "";
					continue;
				}
				$date1 = strtotime(date("Y-m-d",strtotime($entity->terminationDate)));
				$date2 = strtotime(date("Y-m-d",strtotime(date_format($date, 'Y-m-d'))));

				if($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" && $date1<$date2){
					$date = date_add($date, date_interval_create_from_date_string('1 days'));
					$data_values[] = "";
					continue;
				}
				$isHoliday = false;
				$qry = \AttendenceLog::where("date","=",date_format($date, 'Y-m-d'));
						if($values["employeetype"] == "CLIENT BRANCH"){
							$qry->where("depotId","=",$values["depot"])->where("clientId","=",$values["client"]);
						}
						else{
							$qry->where("officeBranchId","=",$values["officebranch"]);
						}
				$at_log = 	$qry->where("day","=","HOLIDAY")->where("session","=",$values["session"])->get();
				if(count($at_log)>0){
					$isHoliday = true;
				}
				if(date_format($date, 'd-m-Y') == $values["date"]){
					$emp = \Attendence::where("empId","=",$entity->id)->where("session","=",$values["session"])->where("date","=",date("Y-m-d", strtotime($values["date"])))->get();
					if(count($emp)>0){
						$emp = $emp[0];
						if($emp->substituteId>0){
							$substitute = \Employee::where("id","=",$emp->substituteId)->first();
							$emp->substituteId = $substitute->fullName."(".$substitute->empCode.")";
						}
						if(($emp->day=="HOLIDAY" && $emp->session==$values["session"]) || $isHoliday || $values["day"]=="HOLIDAY"){
							$data_values[] =  "<span style='font-weight:bold; font-size:16px; color:black'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-13px; margin-bottom:-17px;' src='../assets/img/corner.png'/></span>";
						}
						else{
							$data_values[] =  "<span style='font-weight:bold; font-size:16px; color:red'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-13px; margin-bottom:-17px;' src='../assets/img/corner.png'/></span>";
						}
					}
					else{
						if($isHoliday || $values["day"]=="HOLIDAY"){
							$data_values[] =  "<span style='font-weight:bold; font-size:16px; color:red' id='".$entity->id."_".$i."' onclick='changeValue(this.id, \"".$entity->id."\", \"".$emptype."\")'>H</span>";
						}
						else{
							$data_values[] =  "<span style='font-weight:bold; font-size:16px; color:blue' id='".$entity->id."_".$i."' onclick='changeValue(this.id, \"".$entity->id."\", \"".$emptype."\")'>P</span>";
						}
					}
				}
				else{
					$emp = \Attendence::where("empId","=",$entity->id)->where("session","=",$values["session"])->where("date","=",date("Y-m-d", strtotime(date_format($date, 'd-m-Y'))))->get();
					if(count($emp)>0){
						$emp = $emp[0];
						if($emp->substituteId>0){
							$substitute = \Employee::where("id","=",$emp->substituteId)->first();
							$emp->substituteId = $substitute->fullName."(".$substitute->empCode.")";
						}
						if(($emp->day=="HOLIDAY" && $emp->session==$values["session"]) || $isHoliday){
							$data_values[] =  "<span style='font-weight:bold; color:red'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-20px; margin-bottom:-24px;' src='../assets/img/corner.png'/></span>";
						}
						else{
							$data_values[] =  "<span style='font-weight:bold; color:red'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-20px; margin-bottom:-24px;' src='../assets/img/corner.png'/></span>";
						}
					}
					else{
						$qry = \AttendenceLog::where("date","=",date_format($date, 'Y-m-d'));
						if($values["employeetype"] == "CLIENT BRANCH"){
							$qry->where("depotId","=",$values["depot"])->where("clientId","=",$values["client"]);
						}
						else{
							$qry->where("officeBranchId","=",$values["officebranch"]);
						}
						$at_log = 	$qry->where("session","=",$values["session"])->get();
						if(count($at_log)>0){
							if($isHoliday){
								$data_values[] =  "<span style='font-weight:bold; color:red'>H</span>";
							}
							else{
								$data_values[] =  "<span style='font-weight:bold; color:green'>P</span>";
							}
						}
						else{
							$data_values[] = "";
						}
					}
				}
				$date = date_add($date, date_interval_create_from_date_string('1 days'));
			}
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}
	
	private function getEmployeesToUpdate($values, $length, $start){
		//$values["DSF"];
		$total = 0;
		$data = array();
		$select_args = array("employee.id", "employee.fullName", "employee.empCode", "employee.joiningDate", "employee.terminationDate");
	
		$actions = array();
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
			if($values["employeetype"] == "CLIENT BRANCH"){
				if(isset($values["depot"]) && $values["depot"]==0){
					\DB::statement(\DB::raw("CALL contract_driver_helper_all('".$values["client"]."');"));
				}
				else{
					\DB::statement(\DB::raw("CALL contract_driver_helper('".$values["depot"]."', '".$values["client"]."');"));
				}
				$entities = \DB::select( \DB::raw("select * from temp_contract_drivers_helpers group by id limit $start,$length"));
				$total = \DB::select( \DB::raw("select count(*) as cnt from temp_contract_drivers_helpers group by id"));
				$total = count($total);
				
			}
			else{
				$entities = \Employee::whereRaw(" (roleId!=20 and roleId!=19) and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")
							->select($select_args)->limit($length)->offset($start)->get();
				$total = \Employee::whereRaw(" (roleId!=20 and roleId!=19) and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")->count();
			}
		}
	
		//$entities = $entities->toArray();
		foreach($entities as $entity){
			$date1 = strtotime(date("Y-m-d",strtotime($entity->terminationDate)));
			$month = date("m",strtotime($values["date"]));
			$year = date("Y",strtotime($values["date"]));
			$dt = (date("d-m-Y",strtotime("01"."-".$month."-".$year)));
			$date2 = strtotime(date("Y-m-d",strtotime($dt)));
			if($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" && $date1<$date2){
				continue;
			}
			$data_values = array();
			$branch = "";
			if($values["employeetype"] == "CLIENT BRANCH"){
				$emp = \ContractVehicle::whereRaw(" contract_vehicles.status='ACTIVE' and (driver1Id=".$entity->id." or driver2Id=".$entity->id." or driver3Id=".$entity->id." or driver4Id=".$entity->id." or driver5Id=".$entity->id." or helperId=".$entity->id.")")
				->leftjoin("contracts","contracts.id","=","contract_vehicles.contractId")
				->leftjoin("depots","depots.id","=","contracts.depotId")
				->select(array("depots.name as dname"))->get();
				if(count($emp)>0){
					$emp = $emp[0];
					$branch = "-".$emp->dname;
				}
			}
			else{
				/*$emp = \Employee::whereRaw(" status='ACTIVE' and (roleId!=20 and roleId!=19) and  empCode=".$entity->empCode." and FIND_IN_SET('".$values["officebranch"]."',employee.officeBranchIds)")->get();
				 if(count($emp)>0){
				 $emp = $emp[0];
				 }
				 */
			}
			$data_values[] = $entity->fullName."(".$entity->empCode.")".$branch;
			$month = date("m",strtotime($values["date"]));
			$year = date("Y",strtotime($values["date"]));
			$date = date_create(date("d-m-Y",strtotime("01"."-".$month."-".$year)));
			if($month === date("m")){
				$today = date_create(date("d-m-Y"));
			}
			else{
				$today = date_create(date("t-m-Y", strtotime($values["date"])));
			}
			$diff = date_diff($date,$today);
			$diff =  $diff->format("%a");
				
			$emptype ="office";
			if($values["employeetype"] == "CLIENT BRANCH"){
				$emptype = "driver";
			}
				
			for($i=0; $i<=$diff; $i++){
				$date1 = strtotime(date("Y-m-d",strtotime($entity->joiningDate)));
				$date2 = strtotime(date("Y-m-d",strtotime(date_format($date, 'Y-m-d'))));
				$date3 = strtotime(date('Y-m-01'));
				
				if($date1>$date2){
					$date = date_add($date, date_interval_create_from_date_string('1 days'));
					$data_values[] = "";
					continue;
				}
				$date1 = strtotime(date("Y-m-d",strtotime($entity->terminationDate)));
				$date2 = strtotime(date("Y-m-d",strtotime(date_format($date, 'Y-m-d'))));
				
				if($entity->terminationDate!="" && $entity->terminationDate!="0000-00-00" && $date1 != "1970-01-01" && $date1<$date2){
					$date = date_add($date, date_interval_create_from_date_string('1 days'));
					$data_values[] = "";
					continue;
				}
				$isHoliday = false;
				$qry = \AttendenceLog::where("date","=",date_format($date, 'Y-m-d'));
						if($values["employeetype"] == "CLIENT BRANCH"){
							$qry->where("depotId","=",$values["depot"])->where("clientId","=",$values["client"]);
						}
						else{
							$qry->where("officeBranchId","=",$values["officebranch"]);
						}
				$at_log = 	$qry->where("day","=","HOLIDAY")->where("session","=",$values["session"])->get();
				if(count($at_log)>0){
					$isHoliday = true;
				}
				if(date_format($date, 'd-m-Y') == $values["date"]){
					$emp = \Attendence::where("empId","=",$entity->id)->where("session","=",$values["session"])->where("date","=",date("Y-m-d", strtotime($values["date"])))->get();
					if(count($emp)>0){
						$emp = $emp[0];
						if($emp->substituteId>0){
							$substitute = \Employee::where("id","=",$emp->substituteId)->first();
							$emp->substituteId = $substitute->fullName."(".$substitute->empCode.")";
						}
						if(($emp->day=="HOLIDAY" && $emp->session==$values["session"]) || $isHoliday || $values["day"]=="HOLIDAY"){
							$data_values[] =  "<span style='font-weight:bold; color:red' id='_".$entity->id."_".$i."' onclick='updateAttendenceValues(this.id, \"".$emptype."\",\"".$emp["empId"]."\",\"".$emp["substituteId"]."\", \"".$emp["attendenceStatus"]."\", \"".$emp["comments"]."\", \"".$emp["attendenceStatusComments"]."\", ".$emp["id"].")'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-20px; margin-bottom:-15px;' src='../assets/img/corner.png'/></span>";
						}
						else{
							$data_values[] =  "<span style='font-weight:bold; color:red' id='_".$entity->id."_".$i."' onclick='updateAttendenceValues(this.id, \"".$emptype."\",\"".$emp["empId"]."\",\"".$emp["substituteId"]."\", \"".$emp["attendenceStatus"]."\", \"".$emp["comments"]."\", \"".$emp["attendenceStatusComments"]."\", ".$emp["id"].")'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-20px; margin-bottom:-15px;' src='../assets/img/corner.png'/></span>";
						}
					}
					else{
						if($isHoliday || $values["day"]=="HOLIDAY"){																							//(id, type, substitute, comments, status, empid)
							$data_values[] =  "<span style='font-weight:bold; font-size:16px; color:red' id='".$entity->id."_".$i."' onclick='updateAttendenceValues(this.id, \"".$entity->id."\", \"".$emptype."\", \"\", \"\", \"\", \"\", \"\")'>H</span>";
						}
						else{
							$data_values[] =  "<span style='font-weight:bold; font-size:16px; color:blue' id='".$entity->id."_".$i."' onclick='updateAttendenceValues(this.id, \"".$entity->id."\", \"".$emptype."\", \"\", \"\", \"\", \"\", \"\")'>P</span>";
						}
					}
				}
				else{
					$emp = \Attendence::where("empId","=",$entity->id)->where("session","=",$values["session"])->where("date","=",date("Y-m-d", strtotime(date_format($date, 'd-m-Y'))))->get();
					if(count($emp)>0){
						$emp = $emp[0];
						if($emp->substituteId>0){
							$substitute = \Employee::where("id","=",$emp->substituteId)->first();
							$emp->substituteId = "";
							if(count($substitute)>0){
								$emp->substituteId = $substitute->fullName."(".$substitute->empCode.")";
							}
						}
						if(($emp->day=="HOLIDAY" && $emp->session==$values["session"]) || $isHoliday){
							$data_values[] =  "<span style='font-weight:bold; color:red'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-20px; margin-bottom:-15px;' src='../assets/img/corner.png'/></span>";
						}
						else{
							$data_values[] =  "<span style='font-weight:bold; color:red'>".$emp["attendenceStatus"]."</span>&nbsp;&nbsp;<span style='font-weight:bold; color:red' id='".$entity->id."_".$i."' onclick='showData(\"".$emp["substituteId"]."\", \"".$emp["comments"]."\")'><img style='posistion:absolute; margin-right:-20px; margin-bottom:-15px;' src='../assets/img/corner.png'/></span>";
						}
					}
					else{
						$qry = \AttendenceLog::where("date","=",date_format($date, 'Y-m-d'));
									if($values["employeetype"] == "CLIENT BRANCH"){
										$qry->where("depotId","=",$values["depot"])->where("clientId","=",$values["client"]);
									}
									else{
										$qry->where("officeBranchId","=",$values["officebranch"]);
									}
						$at_log = 	$qry->where("session","=",$values["session"])->get();
						
						if(count($at_log)>0){
							if($isHoliday){
								$data_values[] =  "<span style='font-weight:bold; color:red'>H</span>";
							}
							else{
								$data_values[] =  "<span style='font-weight:bold; color:green'>P</span>";
							}
						}
						else{
							$data_values[] = "";
						}
					}
				}
				$date = date_add($date, date_interval_create_from_date_string('1 days'));
			}
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
		$select_args[] = "creditsuppliertransdetails.vehicleIds as vehicleIds";
		$select_args[] = "creditsuppliertransactions.workFlowStatus as workFlowStatus";
		$select_args[] = "creditsuppliertransactions.workFlowRemarks as workFlowRemarks";
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
		else {
			$qry = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","No");
							if($values["logstatus"] != "All"){
								$qry->where("creditsuppliertransactions.workFlowStatus","=",$values["logstatus"]);
							}
							$qry->where("creditsuppliertransdetails.status","=","ACTIVE")
							->leftjoin("creditsuppliertransdetails", "creditsuppliertransdetails.creditSupplierTransId","=","creditsuppliertransactions.id")
							->leftjoin("creditsuppliers", "creditsuppliers.id","=","creditsuppliertransactions.creditSupplierId")
							->leftjoin("officebranch", "officebranch.id","=","creditsuppliertransactions.branchId")
							->leftjoin("contracts", "contracts.id","=","creditsuppliertransactions.contractId")
							->leftjoin("clients", "clients.id","=","contracts.clientId")
							->leftjoin("depots", "depots.id","=","contracts.depotId");							
			$entities =    $qry->select($select_args)->limit($length)->groupBy("id")->offset($start)->get();
			
			$qry =  \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","No");
							if($values["logstatus"] != "All"){
								$qry->where("creditsuppliertransactions.workFlowStatus","=",$values["logstatus"]);
							}
							$qry->where("creditsuppliertransdetails.status","=","ACTIVE");
							$qry->leftjoin("creditsuppliertransdetails", "creditsuppliertransdetails.creditSupplierTransId","=","creditsuppliertransactions.id");
			$total = $qry->groupBy("creditsuppliertransactions.id")->count();
			foreach ($entities as $entity){
				$entity["clientname"] = $entity["depotname"]." (".$entity["clientname"].")";
			}
		}
		$entities = $entities->toArray();
		$vehs_arr = array();
		$vehicles = \Vehicle::All();
		foreach ($vehicles  as $vehicle){
			$vehs_arr[$vehicle->id] = $vehicle->veh_reg;
		}
		//print_r($entities);die();
		foreach($entities as $entity){
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			$trans_items = \CreditSupplierTransDetails::where("creditSupplierTransId","=",$entity["id"])
								->where("creditsuppliertransdetails.status","=","ACTIVE")
								->leftjoin("lookuptypevalues","lookuptypevalues.id","=","creditsuppliertransdetails.repairedItem")				
								->select(array("creditsuppliertransdetails.vehicleIds as vehicleIds", "lookuptypevalues.name as itemname"))->get();
			
			$entity["vehicleIds"] = "";
			foreach($trans_items as $trans_item){
				$vehs_arr_str = "";
				$veh_ids_arr = explode(",", $trans_item->vehicleIds);
				foreach ($veh_ids_arr  as $veh_id){
					if($veh_id != ""){
						$vehs_arr_str = $vehs_arr_str.$vehs_arr[$veh_id].",";
					}
				}
				$entity["vehicleIds"] = $entity["vehicleIds"]."<span style='color:red;' >VEHICLES : ".$vehs_arr_str."</span><br/>";
				$entity["vehicleIds"] = $entity["vehicleIds"]."<span style='color:green;' >REPAIRED ITEM : ".$trans_item->itemname."</span><br/>";
			}
			$entity["vehicleIds"] = $entity["vehicleIds"]."Labour Charges : ".$entity["labourCharges"]."<br/>";
			$entity["vehicleIds"] = $entity["vehicleIds"]."Electricial Charges : ".$entity["electricianCharges"]."<br/>";
			$entity["vehicleIds"] = $entity["vehicleIds"]."Batta : ".$entity["batta"]."<br/>";
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
			$action_data = '<label> <input name="action[]" type="checkbox" class="ace" value="'.$entity["id"].'"> <span class="lbl">&nbsp;</span></label>';
			$data_values[11] = $action_data;
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
		$select_args[] = "fueltransactions.workFlowStatus as workFlowStatus";
		$select_args[] = "fueltransactions.workFlowRemarks as workFlowRemarks";
		$select_args[] = "fueltransactions.id as id";
		$select_args[] = "fueltransactions.branchId as branch";
		$select_args[] = "fueltransactions.contractId as contractId";
		$select_args[] = "clients.name as clientname";
		$select_args[] = "depots.name as depotname";

		$actions = array();
		$values["actions"] = $actions;
	
		$search = $_REQUEST["search"];
		$search = $search['value'];
		$entities = \Vehicle::where("id","=",0)->get();
		if($search != ""){
			$entities = \Vehicle::where("veh_reg", "like", "%$search%")
							->where("vehicle.status","=","ACTIVE")->get();
			$veh_arr = array();
			foreach ($entities as $entity){
				$veh_arr[] = $entity->id;
			}
			$qry = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
							->whereIn("vehicleId",$veh_arr)
							->leftjoin("officebranch", "officebranch.id","=","fueltransactions.branchId")
							->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
							->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")
							->leftjoin("contracts", "contracts.id","=","fueltransactions.contractId")
							->leftjoin("clients", "clients.id","=","contracts.clientId")
							->leftjoin("depots", "depots.id","=","contracts.depotId");
			$entities = $qry->select($select_args)->limit($length)->offset($start)->get();
			
			$total = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
							->whereIn("vehicleId",$veh_arr)->count();
		}
		else {
			$qry = \FuelTransaction::where("fueltransactions.status","=","ACTIVE");
						if($values["logstatus"] != "All"){
							$qry->where("fueltransactions.workFlowStatus","=",$values["logstatus"]);
						}
						$qry->leftjoin("officebranch", "officebranch.id","=","fueltransactions.branchId")
						->leftjoin("vehicle", "vehicle.id","=","fueltransactions.vehicleId")
						->leftjoin("fuelstationdetails", "fuelstationdetails.id","=","fueltransactions.fuelStationId")
						->leftjoin("contracts", "contracts.id","=","fueltransactions.contractId")
						->leftjoin("clients", "clients.id","=","contracts.clientId")
						->leftjoin("depots", "depots.id","=","contracts.depotId");
			$entities = $qry->select($select_args)->limit($length)->offset($start)->get();
			
			$qry = \FuelTransaction::where("fueltransactions.status","=","ACTIVE");
						if($values["logstatus"] != "All"){
							$qry->where("fueltransactions.workFlowStatus","=",$values["logstatus"]);
						}
			$total = $qry->where("fueltransactions.workFlowStatus","=","Requested")->count();
		}
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
			if($entity["contractId"]>0){
				$entity["branchId"] = $entity["depotname"]."(".$entity["clientname"].")";
			}
			
			$data_values = array_values($entity);
			$values1 = array("branch"=>$entity["branch"],"date"=>$entity["date"]);
			$action_data = '<label> <input name="action[]" type="checkbox" class="ace" value="'.$entity["id"].'"> <span class="lbl">&nbsp;</span></label>';
			$data_values[10] = $action_data;
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
	
	private function getInchargeTransactions($values, $length, $start){
		$total = 0;
		$data = array();
	
		$actions = array();
		$values["actions"] = $actions;
		$search = $_REQUEST["search"];
		$search = $search['value'];
		
		if(isset($values["inchargereporttype"]) && $values["inchargereporttype"] == "Income"){
			
			$select_args = array();
			$select_args[] = "officebranch.name as branchId";
			$select_args[] = "employee.fullName as inchargeId";
			$select_args[] = "incometransactions.amount as amount";
			$select_args[] = "incometransactions.date as date";
			$select_args[] = "lookuptypevalues.name as name";
			$select_args[] = "incometransactions.remarks as remarks";
			$select_args[] = "incometransactions.workFlowStatus as workFlowStatus";
			$select_args[] = "incometransactions.workFlowRemarks as workFlowRemarks";
			$select_args[] = "incometransactions.transactionId as id";
			$select_args[] = "incometransactions.lookupValueId as lookupValueId";
			$select_args[] = "incometransactions.branchId as branch";
			$select_args[] = "clients.name as clientname";
			$select_args[] = "depots.name as depotname";
			$select_args[] = "employee.empCode as empCode";
			
			if($search != ""){
				$entities = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
				$total = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->count();
				foreach ($entities as $entity){
					$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
				}
			}
			else{
				$entities = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
								->where("incometransactions.inchargeId",">",0)
								->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")
								->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")
								->leftjoin("contracts", "contracts.id","=","incometransactions.contractId")
								->leftjoin("employee", "employee.id","=","incometransactions.inchargeId")
								->leftjoin("clients", "clients.id","=","contracts.clientId")
								->leftjoin("depots", "depots.id","=","contracts.depotId")
								->select($select_args)->limit($length)->offset($start)->get();
				$total = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->count();
				foreach ($entities as $entity){
					$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
				}
			}
		
			$entities = $entities->toArray();
			foreach($entities as $entity){
				$entity["inchargeId"] = $entity["inchargeId"]." (".$entity["empCode"].")";
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
				$action_data = '<label> <input name="action[]" type="checkbox" class="ace" value="'.$entity["id"].'"> <span class="lbl">&nbsp;</span></label>';
				$data_values[8] = $action_data;
				$data[] = $data_values;
			}
			return array("total"=>$total, "data"=>$data);
		}
		else if(isset($values["inchargereporttype"]) && $values["inchargereporttype"] == "Expense"){
			$select_args = array();
			$select_args[] = "officebranch.name as branchId";
			$select_args[] = "employee.fullName as inchargeId";
			$select_args[] = "expensetransactions.amount as amount";
			$select_args[] = "expensetransactions.date as date";
			$select_args[] = "lookuptypevalues.name as name";
			$select_args[] = "expensetransactions.remarks as remarks";
			$select_args[] = "expensetransactions.workFlowStatus as workFlowStatus";
			$select_args[] = "expensetransactions.workFlowRemarks as workFlowRemarks";
			$select_args[] = "expensetransactions.transactionId as id";
			$select_args[] = "expensetransactions.lookupValueId as lookupValueId";
			$select_args[] = "expensetransactions.branchId as branch";
			$select_args[] = "clients.name as clientname";
			$select_args[] = "depots.name as depotname";
			$select_args[] = "employee.empCode as empCode";
			
			if($search != ""){
				$entities = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->where("branchId","=",$values["branch1"])->leftjoin("officebranch", "officebranch.id","=","incometransactions.branchId")->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","incometransactions.lookupValueId")->select($select_args)->limit($length)->offset($start)->get();
				$total = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")->where("transactionId", "like", "%$search%")->count();
				foreach ($entities as $entity){
					$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
				}
			}
			else{
				$entities = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
								->where("expensetransactions.inchargeId",">",0)
								->leftjoin("officebranch", "officebranch.id","=","expensetransactions.branchId")
								->leftjoin("lookuptypevalues", "lookuptypevalues.id","=","expensetransactions.lookupValueId")
								->leftjoin("contracts", "contracts.id","=","expensetransactions.contractId")
								->leftjoin("employee", "employee.id","=","expensetransactions.inchargeId")
								->leftjoin("clients", "clients.id","=","contracts.clientId")
								->leftjoin("depots", "depots.id","=","contracts.depotId")
								->select($select_args)->limit($length)->offset($start)->get();
				$total = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")->count();
				foreach ($entities as $entity){
					$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
				}
			}
		
			$entities = $entities->toArray();
			foreach($entities as $entity){
				$entity["inchargeId"] = $entity["inchargeId"]." (".$entity["empCode"].")";
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
				$action_data = '<label> <input name="action[]" type="checkbox" class="ace" value="'.$entity["id"].'"> <span class="lbl">&nbsp;</span></label>';
				$data_values[8] = $action_data;
				$data[] = $data_values;
			}
			return array("total"=>$total, "data"=>$data);
		}
	}
}


