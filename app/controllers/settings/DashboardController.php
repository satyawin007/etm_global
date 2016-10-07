<?php namespace settings;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
class DashboardController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	
	public function getDashboardDataTableData()
	{
		$this->jobs = \Session::get("jobs");
		$values = Input::All();
		$start = $values['start'];
		$length = $values['length'];
		$total = 0;
		$data = array();
	
		if(isset($values["name"]) && $values["name"]=="vehiclerenewals") {
			$ret_arr = $this->getVehicleRenewals($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="feultransactionsstatus") {
			$ret_arr = $this->getFeulTransactionsStatus($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="repairtransactionsstatus") {
			$ret_arr = $this->getRepairTransactionsStatus($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="purchaseordersstatus") {
			$ret_arr = $this->getPurchaseOrdersStatus($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="inchargetransactionsstatus") {
			$ret_arr = $this->getInchargeTransactionsStatus($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="employeeleaves") {
			$ret_arr = $this->getEmployeeLeaves($values, $length, $start);
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
	
	private function getVehicleRenewals($values, $length, $start)
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::All();
			$start = $values['start'];
			$length = $values['length'];
			$total = 0;
			$today = date("Y-m-d");
			
			$resp = array();
			$vehcileids = array();
			$select_args = array();
			$select_args[] = "vehicle.veh_reg as veh_reg";
			$select_args[] = "lookuptypevalues.name as name";
			$select_args[] = "expensetransactions.nextAlertDate as nextAlertDate";
// 			$select_args[] = "vehicle.insurance_last_paid as insurance_last_paid";
// 			$select_args[] = "vehicle.pol_last_paid as pol_last_paid";
// 			$select_args[] = "vehicle.permit_last_paid as permit_last_paid";
// 			$select_args[] = "vehicle.fit_last_paid as fit_last_paid";
			
			$search = $_REQUEST["search"];
			$search = $search['value'];
			if($search != ""){
				$entities = \Vehicle::where("vehicle.status","=","ACTIVE")
									->where("vehicle.veh_reg", "like", "%$search%")
									->where("expensetransactions.nextAlertDate","!=","0000-00-00")
									->where("expensetransactions.nextAlertDate","!=","1970-01-01")
									->leftjoin("expensetransactions","expensetransactions.vehicleIds","=","vehicle.id")
									->leftjoin("lookuptypevalues","expensetransactions.lookupValueId","=","lookuptypevalues.id")
									->select($select_args)->orderBy("vehicle.id")->get();
			}
			else{
				$entities = \Vehicle::where("vehicle.status","=","ACTIVE")
									->where("expensetransactions.nextAlertDate","!=","0000-00-00")
									->where("expensetransactions.nextAlertDate","!=","1970-01-01")
									->leftjoin("expensetransactions","expensetransactions.vehicleIds","=","vehicle.id")
									->leftjoin("lookuptypevalues","expensetransactions.lookupValueId","=","lookuptypevalues.id")
									->select($select_args)->orderBy("vehicle.id")->get();
			}
			$cnt = 0;
			foreach ($entities as $entity){
				$date1=date_create($today);
				$date2=date_create($entity->nextAlertDate);
				$diff=date_diff($date1,$date2);
// 				echo $diff->format("%R%a").", "; continue;
				$row = array();
				if($diff->format("%R%a") > 0 && $diff->format("%R%a") < 30){
					$row["0"] = $entity->veh_reg;
					$row["1"] = $entity->name;
					$row["2"] = "";
					if($diff->format("%R%a") > 0 && $diff->format("%R%a") < 10){
						$row["3"] = '<span class="badge badge-warning">'.($diff->format("%a")).'</span>';
					}
					else{
						$row["3"] = "";
					}
					if($diff->format("%R%a") >= 10){
						$row["4"] = '<span class="badge badge-danger">'.($diff->format("%a")).'</span>';
					}
					else{
						$row["4"] = "";
					}
					$resp[] = $row;
					$cnt++;
				}
				else if($diff->format("%R%a") < 0){
					$row["0"] = $entity->veh_reg;
					$row["1"] = $entity->name;
					$row["2"] = '<span class="badge badge-inverse">'.$diff->format("%a").'</span>';
					$row["3"] = "";
					$row["4"] = "";
					$resp[] = $row;
					$cnt++;
				}
				/* if(!in_array($entity->veh_reg, $vehcileids)){
					$date2=date_create($entity->insurance_last_paid);
					$diff=date_diff($date1,$date2);
					if($diff->format("%R%a") < 0 && $diff->format("%R%a") < -365){
						$row["0"] = $entity->veh_reg; 
						$row["1"] = "INSURANCE PAYMENT";
						$row["2"] = '<span class="badge badge-inverse">'.($diff->format("%a")-365).'</span>';
						$row["3"] = "";
						$row["4"] = "";
						$resp[] = $row;
						$cnt++;
					}
					$date2=date_create($entity->pol_last_paid);
					$diff=date_diff($date1,$date2);
					if($diff->format("%R%a") < 0 && $diff->format("%R%a") < -365){
						$row["0"] = $entity->veh_reg;
						$row["1"] = "POLLUTION PAYMENT";
						$row["2"] = '<span class="badge badge-inverse">'.($diff->format("%a")-365).'</span>';
						$row["3"] = "";
						$row["4"] = "";
						$resp[] = $row;
						$cnt++;
					}
					$date2=date_create($entity->permit_last_paid);
					$diff=date_diff($date1,$date2);
					if($diff->format("%R%a") < 0 && $diff->format("%R%a") < -365){
						$row["0"] = $entity->veh_reg;
						$row["1"] = "PERMIT PAYMENT";
						$row["2"] = '<span class="badge badge-inverse">'.($diff->format("%a")-365).'</span>';
						$row["3"] = "";
						$row["4"] = "";
						$resp[] = $row;
						$cnt++;
					}
					$date2=date_create($entity->fit_last_paid);
					$diff=date_diff($date1,$date2);
					if($diff->format("%R%a") < 0 && $diff->format("%R%a") < -365){
						$row["0"] = $entity->veh_reg;
						$row["1"] = "FITNESS PAYMENT";
						$row["2"] = '<span class="badge badge-inverse">'.($diff->format("%a")-365).'</span>';
						$row["3"] = "";
						$row["4"] = "";
						$resp[] = $row;
						$cnt++;
					}
				}
				$vehcileids[] = $entity->veh_reg; */
			}
			return array("total"=>$cnt, "data"=>$resp);
		}
	}
	
	private function getFeulTransactionsStatus($values, $length, $start)
	{
		$empids_arr = array();
		if(\Auth::user()->rolePrevilegeId==2){
			$emps = \Employee::All();
			foreach ($emps as $emp){
				$empids_arr[] = $emp->id;
			}
		}
		else{
			$empids_arr[] = \Auth::user()->id;
		}
		
		if (\Request::isMethod('post'))
		{
			$select_args = array();
			$resp = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "employee.id as empid";
			
			$search = $_REQUEST["search"];
			$search = $search['value'];
			if($search != ""){
				$entities = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->join("employee","employee.id","=","fueltransactions.createdBy")
											->where("employee.fullName", "like", "%$search%")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->join("employee","employee.id","=","fueltransactions.createdBy")
											->where("employee.fullName", "like", "%$search%")
											->groupBy("employee.id")
											->count();
// 				$entities = \Vehicle::where("vehicle.status","=","ACTIVE")
// 				->where("vehicle.veh_reg", "like", "%$search%")
// 				//->where("expensetransactions.nextAlertDate","!=","0000-00-00")
// 						->leftjoin("expensetransactions","expensetransactions.vehicleIds","=","vehicle.id")
// 						->leftjoin("lookuptypevalues","expensetransactions.lookupValueId","=","lookuptypevalues.id")
// 				->select($select_args)->orderBy("vehicle.id")->limit($length)->offset($start)->get();
			}
			else{
				$entities = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->whereIn("fueltransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","fueltransactions.createdBy")
											->groupBy("fueltransactions.createdBy")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->whereIn("fueltransactions.createdBy",$empids_arr)
											->groupBy("fueltransactions.createdBy")->get();
				$cnt = count($cnt);

			}
			foreach ($entities as $entity){
				$row = array();
				$row["0"] = $entity->name;
				
				$sent_for_app_cnt = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->where("fueltransactions.createdBy","=",$entity->empid)
											->where("fueltransactions.workFlowStatus","=","Sent for Approval")
											->groupBy("fueltransactions.createdBy")
											->count();
				$row["1"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$requested_for_app_cnt = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->where("fueltransactions.createdBy","=",$entity->empid)
											->where("fueltransactions.workFlowStatus","=","Requested")
											->groupBy("fueltransactions.createdBy")
											->count();
				$row["2"] = '<span class="badge badge-warning">'.$requested_for_app_cnt.'</span>';
				$sent_for_app_cnt = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->where("fueltransactions.createdBy","=",$entity->empid)
											->where("fueltransactions.workFlowStatus","=","Approved")
											->groupBy("fueltransactions.createdBy")
											->count();
				$row["3"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$rejected_for_app_cnt = \FuelTransaction::where("fueltransactions.status","=","ACTIVE")
											->where("fueltransactions.createdBy","=",$entity->empid)
											->where("fueltransactions.workFlowStatus","=","Rejected")
											->groupBy("fueltransactions.createdBy")
											->count();
				$row["4"] = '<span class="badge badge-danger">'.$rejected_for_app_cnt.'</span>';;
				$resp[] = $row;
			}
			return array("total"=>$cnt, "data"=>$resp);
		}
	}
	
	private function getRepairTransactionsStatus($values, $length, $start)
	{
		if (\Request::isMethod('post'))
		{
			$empids_arr = array();
			if(\Auth::user()->rolePrevilegeId==2){
				$emps = \Employee::All();
				foreach ($emps as $emp){
					$empids_arr[] = $emp->id;
				}
			}
			else{
				$empids_arr[] = \Auth::user()->id;
			}
			$select_args = array();
			$resp = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "employee.id as empid";
				
			$search = $_REQUEST["search"];
			$search = $search['value'];
			if($search != ""){
				$entities = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","creditsuppliertransactions.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","creditsuppliertransactions.createdBy")
											->groupBy("employee.id")->get();
				$cnt = count($cnt);
			}
			else{
				$entities = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
											->whereIn("creditsuppliertransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","creditsuppliertransactions.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
											->whereIn("creditsuppliertransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","creditsuppliertransactions.createdBy")
											->groupBy("employee.id")->get();
				$cnt = count($cnt);
			}
			foreach ($entities as $entity){
				$row = array();
				$row["0"] = $entity->name;
				
				
				$sent_for_app_cnt = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
													->where("creditsuppliertransactions.createdBy","=",$entity->empid)
													->where("creditsuppliertransactions.workFlowStatus","=","Sent for Approval")
													->groupBy("creditsuppliertransactions.createdBy")
													->count();
				$row["1"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$requested_for_app_cnt = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
													->where("creditsuppliertransactions.createdBy","=",$entity->empid)
													->where("creditsuppliertransactions.workFlowStatus","=","Requested")
													->groupBy("creditsuppliertransactions.createdBy")
													->count();
				$row["2"] = '<span class="badge badge-warning">'.$requested_for_app_cnt.'</span>';
				$sent_for_app_cnt = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
													->where("creditsuppliertransactions.createdBy","=",$entity->empid)
													->where("creditsuppliertransactions.workFlowStatus","=","Approved")
													->groupBy("creditsuppliertransactions.createdBy")
													->count();
				$row["3"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$rejected_for_app_cnt = \CreditSupplierTransactions::where("creditsuppliertransactions.deleted","=","NO")
													->where("creditsuppliertransactions.createdBy","=",$entity->empid)
													->where("creditsuppliertransactions.workFlowStatus","=","Rejected")
													->groupBy("creditsuppliertransactions.createdBy")
													->count();
				$row["4"] = '<span class="badge badge-danger">'.$rejected_for_app_cnt.'</span>';
				$resp[] = $row;
			}
			return array("total"=>$cnt, "data"=>$resp);
		}
	}
	
	private function getPurchaseOrdersStatus($values, $length, $start)
	{
		$empids_arr = array();
		if(\Auth::user()->rolePrevilegeId==2){
			$emps = \Employee::All();
			foreach ($emps as $emp){
				$empids_arr[] = $emp->id;
			}
		}
		else{
			$empids_arr[] = \Auth::user()->id;
		}
		
		if (\Request::isMethod('post'))
		{
			$select_args = array();
			$resp = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "employee.id as empid";
	
			$search = $_REQUEST["search"];
			$search = $search['value'];
			if($search != ""){
				$entities =\PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","purchase_orders.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
										->where("employee.fullName", "like", "%$search%")
										->join("employee","employee.id","=","purchase_orders.createdBy")
										->groupBy("employee.id")->get();
				$cnt = count($cnt);
			}
			else{
				$entities =\PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
											->whereIn("purchase_orders.createdBy",$empids_arr)
											->join("employee","employee.id","=","purchase_orders.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
											->whereIn("purchase_orders.createdBy",$empids_arr)
											->join("employee","employee.id","=","purchase_orders.createdBy")
											->groupBy("employee.id")->get();
				$cnt = count($cnt);
			}
			foreach ($entities as $entity){
				$row = array();
				$row["0"] = $entity->name;
				$sent_for_app_cnt = \PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
													->where("purchase_orders.createdBy","=",$entity->empid)
													->where("purchase_orders.workFlowStatus","=","Approved")
													->groupBy("purchase_orders.createdBy")
													->count();
				$row["1"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$sent_for_app_cnt = \PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
													->where("purchase_orders.createdBy","=",$entity->empid)
													->where("purchase_orders.workFlowStatus","=","Sent for Approval")
													->groupBy("purchase_orders.createdBy")
													->count();
				$row["2"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$requested_for_app_cnt = \PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
													->where("purchase_orders.createdBy","=",$entity->empid)
													->where("purchase_orders.workFlowStatus","=","Requested")
													->groupBy("purchase_orders.createdBy")
													->count();
				$row["3"] = '<span class="badge badge-warning">'.$requested_for_app_cnt.'</span>';
				$rejected_for_app_cnt = \PurchasedOrders::where("purchase_orders.status","=","ACTIVE")
													->where("purchase_orders.createdBy","=",$entity->empid)
													->where("purchase_orders.workFlowStatus","=","Rejected")
													->groupBy("purchase_orders.createdBy")
													->count();
				$row["4"] = '<span class="badge badge-danger">'.$rejected_for_app_cnt.'</span>';;
				$resp[] = $row;
			}
			return array("total"=>$cnt, "data"=>$resp);
		}
	}
	
	private function getEmployeeLeaves($values, $length, $start)
	{
		$empids_arr = array();
		if(\Auth::user()->rolePrevilegeId==2){
			$emps = \Employee::All();
			foreach ($emps as $emp){
				$empids_arr[] = $emp->id;
			}
		}
		else{
			$empids_arr[] = \Auth::user()->id;
		}
		
		if (\Request::isMethod('post'))
		{
			$select_args = array();
			$resp = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "employee.id as empid";
	
			$search = $_REQUEST["search"];
			$search = $search['value'];
			if($search != ""){
				$entities =\Leaves::where("leaves.deleted","=","No")
								->where("employee.fullName", "like", "%$search%")
								->join("employee","employee.id","=","leaves.createdBy")
								->groupBy("employee.id")
								->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \Leaves::where("leaves.deleted","=","No")
								->where("employee.fullName", "like", "%$search%")
								->join("employee","employee.id","=","leaves.createdBy")
								->groupBy("employee.id")->get();
				$cnt = count($cnt);
			}
			else{
				$entities =\Leaves::where("leaves.deleted","=","No")
								->whereIn("leaves.createdBy",$empids_arr)
								->join("employee","employee.id","=","leaves.createdBy")
								->groupBy("employee.id")
								->select($select_args)->limit($length)->offset($start)->get();
				$cnt = \Leaves::where("leaves.deleted","=","No")
								->whereIn("leaves.createdBy",$empids_arr)
								->join("employee","employee.id","=","leaves.createdBy")
								->groupBy("employee.id")->get();
				$cnt = count($cnt);
			}
			foreach ($entities as $entity){
				$row = array();
				$row["0"] = $entity->name;
				$sent_for_app_cnt = \Leaves::where("leaves.deleted","=","No")
										->where("leaves.createdBy","=",$entity->empid)
										->where("leaves.workFlowStatus","=","Sent for Approval")
										->groupBy("leaves.createdBy")
										->count();
				$row["1"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$requested_for_app_cnt = \Leaves::where("leaves.deleted","=","No")
										->where("leaves.createdBy","=",$entity->empid)
										->where("leaves.workFlowStatus","=","Requested")
										->groupBy("leaves.createdBy")
										->count();
				$row["2"] = '<span class="badge badge-warning">'.$requested_for_app_cnt.'</span>';
				$sent_for_app_cnt = \Leaves::where("leaves.deleted","=","No")
										->where("leaves.createdBy","=",$entity->empid)
										->where("leaves.workFlowStatus","=","Approved")
										->groupBy("leaves.createdBy")
										->count();
				$row["3"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$rejected_for_app_cnt = \Leaves::where("leaves.deleted","=","No")
										->where("leaves.createdBy","=",$entity->empid)
										->where("leaves.workFlowStatus","=","Rejected")
										->groupBy("leaves.createdBy")
										->count();
				$row["4"] = '<span class="badge badge-danger">'.$rejected_for_app_cnt.'</span>';;
				$resp[] = $row;
			}
			return array("total"=>$cnt, "data"=>$resp);
		}
	}
	
	private function getInchargeTransactionsStatus($values, $length, $start)
	{
		$empids_arr = array();
		if(\Auth::user()->rolePrevilegeId==2){
			$emps = \Employee::All();
			foreach ($emps as $emp){
				$empids_arr[] = $emp->id;
			}
		}
		else{
			$empids_arr[] = \Auth::user()->id;
		}
		
		if (\Request::isMethod('post'))
		{
			$select_args = array();
			$resp = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "employee.id as empid";
	
			$search = $_REQUEST["search"];
			$search = $search['value'];
			if($search != ""){
				$entities = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","incometransactions.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt1 = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","incometransactions.createdBy")
											->groupBy("employee.id")->get();
				$cnt1 = count($cnt1);
			}
			else{
				$entities =\IncomeTransaction::where("incometransactions.status","=","ACTIVE")
											->whereIn("incometransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","incometransactions.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt1 = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
											->whereIn("incometransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","incometransactions.createdBy")
											->groupBy("employee.id")->get();
				$cnt1 = count($cnt1);
			}
			foreach ($entities as $entity){
				$row = array();
				$row["0"] = $entity->name;
				$row["1"] = "INCOME TRANSACTION";
				$sent_for_app_cnt = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
													->where("incometransactions.createdBy","=",$entity->empid)
													->where("incometransactions.workFlowStatus","=","Sent for Approval")
													->groupBy("incometransactions.createdBy")
													->count();
				$row["2"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$requested_for_app_cnt = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
														->where("incometransactions.createdBy","=",$entity->empid)
														->where("incometransactions.workFlowStatus","=","Requested")
														->groupBy("incometransactions.createdBy")
														->count();
				$row["3"] = '<span class="badge badge-warning">'.$requested_for_app_cnt.'</span>';
				$sent_for_app_cnt = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
														->where("incometransactions.createdBy","=",$entity->empid)
														->where("incometransactions.workFlowStatus","=","Apporved")
														->groupBy("incometransactions.createdBy")
														->count();
				$row["4"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$rejected_for_app_cnt = \IncomeTransaction::where("incometransactions.status","=","ACTIVE")
														->where("incometransactions.createdBy","=",$entity->empid)
														->where("incometransactions.workFlowStatus","=","Rejected")
														->groupBy("incometransactions.createdBy")
														->count();
				$row["5"] = '<span class="badge badge-danger">'.$rejected_for_app_cnt.'</span>';;
				$resp[] = $row;
			}
			
			if($search != ""){
				$entities =\ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","expensetransactions.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt2 = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
											->where("employee.fullName", "like", "%$search%")
											->join("employee","employee.id","=","expensetransactions.createdBy")
											->groupBy("employee.id")->get();
				$cnt2 = count($cnt2);
			}
			else{
				$entities =\ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
											->whereIn("expensetransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","expensetransactions.createdBy")
											->groupBy("employee.id")
											->select($select_args)->limit($length)->offset($start)->get();
				$cnt2 = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
											->whereIn("expensetransactions.createdBy",$empids_arr)
											->join("employee","employee.id","=","expensetransactions.createdBy")
											->groupBy("employee.id")->get();
				$cnt2 = count($cnt2);
			}
			foreach ($entities as $entity){
				$row = array();
				$row["0"] = $entity->name;
				$row["1"] = "EXPENSE TRANSACTION";
				$sent_for_app_cnt = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
													->where("expensetransactions.createdBy","=",$entity->empid)
													->where("expensetransactions.workFlowStatus","=","Sent for Approval")
													->groupBy("expensetransactions.createdBy")
													->count();
				$row["2"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$requested_for_app_cnt = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
													->where("expensetransactions.createdBy","=",$entity->empid)
													->where("expensetransactions.workFlowStatus","=","Requested")
													->groupBy("expensetransactions.createdBy")
													->count();
				$row["3"] = '<span class="badge badge-warning">'.$requested_for_app_cnt.'</span>';
				$sent_for_app_cnt = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
													->where("expensetransactions.createdBy","=",$entity->empid)
													->where("expensetransactions.workFlowStatus","=","Apprvoed")
													->groupBy("expensetransactions.createdBy")
													->count();
				$row["4"] = '<span class="badge badge-success">'.$sent_for_app_cnt.'</span>';
				$rejected_for_app_cnt = \ExpenseTransaction::where("expensetransactions.status","=","ACTIVE")
													->where("expensetransactions.createdBy","=",$entity->empid)
													->where("expensetransactions.workFlowStatus","=","Rejected")
													->groupBy("expensetransactions.createdBy")
													->count();
				$row["5"] = '<span class="badge badge-danger">'.$rejected_for_app_cnt.'</span>';;
				$resp[] = $row;
			}
			return array("total"=>($cnt1+$cnt2), "data"=>$resp);
		}
	}

}
