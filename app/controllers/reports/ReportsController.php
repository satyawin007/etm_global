<?php namespace reports;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use settings\AppSettingsController;
class ReportsController extends \Controller {

	
	
	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	
	public function carryForward()
	{
		$values = Input::All();
		$values["type"] = "194";
		$nextDay = strtotime(date("Y-m-d", strtotime($values["date1"])) . " +1 day");
		$nextDay = date ( 'Y-m-d' , $nextDay );
		$values["remarks"] = "C/F from ".$values["date1"];
		$values["date1"] = $nextDay;
		$values["paymenttype"] = "cash";
		
		$cf_details = \IncomeTransaction::where("branchId","=",$values["branch"])->where("date","=",$nextDay)->where("status","=","ACTIVE")->where("lookupValueId","=","194")->get();
		if(count($cf_details)>0){
			$cf_details = $cf_details[0];
			$values["amount"] = $cf_details->amount+$values["amount"];
			$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
					"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds", "bankId"=>"bankId",
					"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
					"transactiondate"=>"transactionDate", "suspense"=>"suspense", "date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
			);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$fields["name"] = "income";
			$db_functions_ctrl = new DBFunctionsController();
			$table = "IncomeTransaction";
			$data = array("id"=>$cf_details->transactionId);
			if($db_functions_ctrl->updatetrans($table, $fields, $data)){
				echo "success";
				return;
			}
			else{
				echo "fail";
				return;
			}
		}
		$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
				"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds", "bankId"=>"bankId",
				"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
				"transactiondate"=>"transactionDate", "suspense"=>"suspense", "date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
		);
		$fields = array();
		foreach ($field_names as $key=>$val){
			if(isset($values[$key])){
				$fields[$val] = $values[$key];
			}
		}
		$transid =  strtoupper(uniqid().mt_rand(100,999));
		$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
		foreach($chars as $k=>$v){
			$transid = str_replace($k, $v, $transid);
		}
		$fields["transactionId"] = $transid;
		$fields["source"] = "income transaction";
		$db_functions_ctrl = new DBFunctionsController();
		$table = "IncomeTransaction";
		if($db_functions_ctrl->insert($table, $fields)){
			echo "success";
			return;
		}
		else{
			echo "fail";
			return;
		}
		
	}
	
	public function processBranchSuspense(){
		$values = Input::all();
		$field_names = array("reportbranchid"=>"branchId","reportdate"=>"reportDate","itreportdate"=>"itReportDate", "acbookingincome"=>"bookings_income", "acbookingscancel"=>"bookings_cancel",
				"accargossimplyincome"=>"cargos_simply_income","accargossimplycancel"=>"cargos_simply_cancel","acotherincome"=>"other_income","actotalincome"=>"total_income", "actotalexpense"=>"total_expense",
				"acdepositamount"=>"bank_deposit","acbranchdeposit"=>"branch_deposit","actodaysuspense"=>"today_suspense","adjustedamount"=>"adjusted_amount",
				"verstatus"=>"verification_status", "vercomments"=>"comments"
		);
		$fields = array();
		foreach ($field_names as $key=>$val){
			if(isset($values[$key])){
				if($key == "reportdate" || $key == "itreportdate" ){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else {
					$fields[$val] = $values[$key];
				}
			}
		}
		
		$branch_suspense = \BranchSuspenseReport::where("branchId","=",$fields["branchId"])->where("reportDate","=",$fields["reportDate"])->get();
		if(count($branch_suspense)>0){
			$db_functions_ctrl = new DBFunctionsController();
			$table = "BranchSuspenseReport";
			$data = array("branchId"=>$fields["branchId"],"reportDate"=>$fields["reportDate"]);
			if($db_functions_ctrl->updatesuspense($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("report?reporttype=dailysettlement");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("report?reporttype=dailysettlement");
			}
		}
		
		$db_functions_ctrl = new DBFunctionsController();
		$table = "BranchSuspenseReport";
		if($db_functions_ctrl->insert($table, $fields)){
			\Session::put("message","Operation completed Successfully");
			return \Redirect::to("report?reporttype=dailysettlement");
		}
		else{
			\Session::put("message","Operation Could not be completed, Try Again!");
			return \Redirect::to("report?reporttype=dailysettlement");
		}
	}

	
	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	
	public function getReport()
	{
		$values = Input::all();
		if(isset($values["reporttype"]) && $values["reporttype"] == "dailytransactions"){
			return $this->getDailyTransactiosReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "dailytransactionsofemployee"){
			return $this->getDailyTransactiosEmployeeReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "dailysettlement"){
			return $this->getDailySettlementReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "dailysettlementreport"){
			return $this->getDailySettlementReportsReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "dailyfinancedetailed"){
			return $this->getDailyFinanceDetailedReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "fuel"){
			return $this->getFuelReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "creditsupplier"){
			return $this->getCreditSupplierReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "salaryadvances"){
			return $this->getSalaryAdvancesReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "salary"){
			return $this->getSalaryReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "inchargetransactions"){
			return $this->getInchargeTransactionsReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "loans"){
			return $this->getLoansReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "dailyfinance"){
			return $this->getDailyFinanceReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "bankposition"){
			return $this->getBankPositionReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "stockpurchase"){
			return $this->getStockPurchaseReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "vehiclestockhistory"){
			return $this->getVehicleStockHistoryReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "inventory"){
			return $this->getInventoryReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "repairstock"){
			return $this->getRepairStockReport($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "loginlog"){
			return $this->getLoginLogInfo($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "vehiclemileage"){
			return $this->getVehicleMileage($values);
		}
		if(isset($values["reporttype"]) && $values["reporttype"] == "vehicleperformance"){
			return $this->getVehiclePerformance($values);
		}
	}
	
	private function getDailyTransactiosReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$brachId = $values["branch"];
			$empId = "-1";
			if(isset($values["employee"])){
				$empId = $values["employee"];
			}
			$reportFor = "-1";
			if(isset($values["reportfor"])){
				$reportFor = $values["reportfor"];
			}
			$resp = array();
			if($values["btntype"] == "ticket_corgos_summery"){
				if($brachId == 0){
					$branches =  \OfficeBranch::OrderBy("name")->get();
					foreach ($branches as $branch){
						$recs = DB::select( DB::raw("select lookupValueId,sum(amount) as amt from incometransactions where paymentType='CASH' and  branchId=".$branch->id." and date between '".$frmDt."' and '".$toDt."' group by lookupValueId order By lookupValueId"));
						if(count($recs)>0) {
							$row = array();
							$row["branch"] = "<a href='#edit' data-toggle='modal' onclick=\"modalGetInfo(".$branch->id.", '".$frmDt."', '".$toDt."', ".$empId.", '".$reportFor."')\" title='get report details'>".$branch->name."</a>";
							$totalAmt = 0;
							foreach ($recs as $rec){
								if($rec->lookupValueId==85){
									$row["tickets"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==86){
									$row["ticketcancel"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==87){
									$row["cargosimply"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==-1){
									$row["cargosimplycancel"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
							}
							if(!isset($row["tickets"])){
								$row["tickets"] = 0;
							}
							if(!isset($row["ticketcancel"])){
								$row["ticketcancel"] = 0;
							}
							if(!isset($row["cargosimply"])){
								$row["cargosimply"] = 0;
							}
							if(!isset($row["cargosimplycancel"])){
								$row["cargosimplycancel"] = 0;
							}
							if(!isset($row["cargos"])){
								$row["cargos"] = 0;
							}
							$row["total"] = $totalAmt;
							$resp[] = $row;
						}
					}
				}
				else if($brachId > 0){
					if($empId>0){
						$recs = DB::select( DB::raw("select lookupValueId,sum(amount) as amt from incometransactions where paymentType='CASH' and createdBy=".$empId." and branchId=".$brachId." and date between '".$frmDt."' and '".$toDt."' group by lookupValueId order By lookupValueId"));
						if(count($recs)>0) {
							$row = array();
							$brachName = \OfficeBranch::where("id","=",$brachId)->first();
							$brachName = $brachName->name;
							$row["branch"] = "<a href='#edit' data-toggle='modal' onclick=\"modalGetInfo(".$brachId.", '".$frmDt."', '".$toDt."', ".$empId.", '".$reportFor."')\" title='get report details'>".$brachName."</a>";
							$totalAmt = 0;
							foreach ($recs as $rec){
								if($rec->lookupValueId==85){
									$row["tickets"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==86){
									$row["ticketcancel"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==87){
									$row["cargosimply"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==-1){
									$row["cargosimplycancel"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
							}
							if(!isset($row["tickets"])){
								$row["tickets"] = 0;
							}
							if(!isset($row["ticketcancel"])){
								$row["ticketcancel"] = 0;
							}
							if(!isset($row["cargosimply"])){
								$row["cargosimply"] = 0;
							}
							if(!isset($row["cargosimplycancel"])){
								$row["cargosimplycancel"] = 0;
							}
							if(!isset($row["cargos"])){
								$row["cargos"] = 0;
							}
							$row["total"] = $totalAmt;
							$resp[] = $row;
						}
					}
					else {
						$recs = DB::select( DB::raw("select lookupValueId,sum(amount) as amt from incometransactions where paymentType='CASH' and  branchId=".$brachId." and date between '".$frmDt."' and '".$toDt."' group by lookupValueId order By lookupValueId"));
						if(count($recs)>0) {
							$row = array();
							$brachName = \OfficeBranch::where("id","=",$brachId)->first();
							$brachName = $brachName->name;
							$row["branch"] = "<a href='#edit' data-toggle='modal' onclick=\"modalGetInfo(".$brachId.", '".$frmDt."', '".$toDt."', ".$empId.", '".$reportFor."')\" title='get report details'>".$brachName."</a>";
							$totalAmt = 0;
							foreach ($recs as $rec){
								if($rec->lookupValueId==85){
									$row["tickets"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==86){
									$row["ticketcancel"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==87){
									$row["cargosimply"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
								if($rec->lookupValueId==-1){
									$row["cargosimplycancel"] = $rec->amt;
									$totalAmt = $totalAmt+$rec->amt;
								}
							}
							if(!isset($row["tickets"])){
								$row["tickets"] = 0;
							}
							if(!isset($row["ticketcancel"])){
								$row["ticketcancel"] = 0;
							}
							if(!isset($row["cargosimply"])){
								$row["cargosimply"] = 0;
							}
							if(!isset($row["cargosimplycancel"])){
								$row["cargosimplycancel"] = 0;
							}
							if(!isset($row["cargos"])){
								$row["cargos"] = 0;
							}
							$row["total"] = $totalAmt;
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["btntype"] == "branch_summery"){
				DB::statement(DB::raw("CALL branch_summary_report('".$frmDt."', '".$toDt."');"));
				if(true){
					if ($brachId == 0){
						$branches =  \OfficeBranch::OrderBy("name")->get();
					}
					else{
						$branches =  \OfficeBranch::where("id","=",$brachId)->OrderBy("name")->get();
					}
					foreach ($branches as $branch){
						$row = array();
						$recs = DB::select( DB::raw("select sum(amount) as amt from temp_branch_summary where type!=243 and  transactiontype ='incometransactions' and branchId=".$branch->id." and date between '".$frmDt."' and '".$toDt."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["branch"] = $branch->name;
							$row["income"] = $rec->amt;
						}
						$recs = DB::select( DB::raw("select sum(amount) as amt from temp_branch_summary where type=243 and  transactiontype ='incometransactions' and  branchId=".$branch->id." and date between '".$frmDt."' and '".$toDt."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["amtreceived"] = $rec->amt;
						}
						$recs = DB::select( DB::raw("select sum(amount) as amt from temp_branch_summary where type!=123 and  transactiontype ='expensetransactions' and type!=125 and  branchId=".$branch->id." and date between '".$frmDt."' and '".$toDt."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["expense"] = $rec->amt;
						}
						$recs = DB::select( DB::raw("select sum(amount) as amt from temp_branch_summary where type=123 and  transactiontype ='expensetransactions' and  branchId=".$branch->id." and date between '".$frmDt."' and '".$toDt."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["amtdeposited"] = $rec->amt;
						}
						$recs = DB::select( DB::raw("select sum(amount) as amt from temp_branch_summary where type=125 and  transactiontype ='expensetransactions' and  branchId=".$branch->id." and date between '".$frmDt."' and '".$toDt."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["bank_deposits"] = $rec->amt;
						}
						if ($row["income"] != 0 || $row["amtreceived"] !=0 || $row["expense"] !=0 || $row["amtdeposited"] !=0 || $row["bank_deposits"] !=0){
							$income = $row["income"]+$row["amtreceived"];
							$expens = $row["expense"]+$row["amtdeposited"]+$row["bank_deposits"];
							$row["balance"] = $income - $expens ;
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["btntype"] == "txn_details"){
				DB::statement(DB::raw("CALL daily_transactions_report('".$frmDt."', '".$toDt."');"));
				if($brachId == 0 && $reportFor=="0"){
					$recs = DB::select( DB::raw("select * from temp_daily_transaction order by branchId"));
				}
				else if($brachId > 0 && $reportFor=="0"){
					$recs = DB::select( DB::raw("select * from temp_daily_transaction where branchId=".$brachId." order by branchId"));
				}
				else if($brachId > 0 && $reportFor != "0"){
					$recs = DB::select( DB::raw("select * from temp_daily_transaction where branchId=".$brachId." and name='".$reportFor."' order by branchId"));
				}
				else if($brachId == 0 && $reportFor != "0"){
					$recs = DB::select( DB::raw("select * from temp_daily_transaction where  name='".$reportFor."' order by branchId"));
				}
				if(count($recs)>0) {
					$totalAmt = 0;
					foreach ($recs as $rec){
						$row = array();
						$brachName = "";
						if($rec->branchId>0){
							$brachName = \OfficeBranch::where("id","=",$rec->branchId)->first();
							$brachName = $brachName->name;
						}
						$row = array();
						$row["branch"] = $brachName;
						$row["type"] = strtoupper($rec->type);
						$row["date"] = date("d-m-Y",strtotime($rec->date));
						$row["amount"] = $rec->amount;
						$row["purpose"] = strtoupper($rec->name);
						if($rec->lookupValueId==999){
							if($rec->entityValue>0){
								$prepaidName = \LookupTypeValues::where("id","=",$rec->entityValue)->first();
								$prepaidName = $prepaidName->name;
								$row["purpose"] = strtoupper($rec->entity);
								$row["employee"] = $prepaidName;
							}
							else{
								$row["purpose"] = strtoupper($rec->entity);
								$row["employee"] = "";
							}
						}
						else if($rec->lookupValueId==73){
							$bankdetails = \IncomeTransaction::where("transactionId","=",$rec->rowid)->leftjoin("bankdetails","bankdetails.id","=","incometransactions.bankId")->first();
							$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
							$row["employee"] = $bankdetails;
						}
						else if($rec->lookupValueId==84){
							$bankdetails = \ExpenseTransaction::where("transactionId","=",$rec->rowid)->leftjoin("bankdetails","bankdetails.id","=","expensetransactions.bankId")->first();
							$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
							$row["employee"] = $bankdetails;
						}
						else{
							if($rec->entityValue != "0"){
								$row["employee"] = $rec->entity." - ".$rec->entityValue;
							}
							else{
								$row["employee"] = $rec->entity;
							}
						}
						$row["comments"] = $rec->remarks;
						//$row["billno"] = $rec->billNo;
						$row["createdby"] = $rec->createdBy;
						$totalAmt = $totalAmt+$rec->amount;
						$row["total"] = $totalAmt;
						$resp[] = $row;
					}
				}
			}
			echo json_encode($resp);
			return;
		}
		
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
		
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
		
		$form_fields = array();
		
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		$branches_arr["0"] = "ALL BRANCHES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$emps =  \Employee::All();
		$emps_arr = array();
		$emps_arr["0"] = "ALL EMPLOYEES";
		foreach ($emps as $emp){
			$emps_arr[$emp->id] = $emp->fullName;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","INCOME")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$transtype_arr = array();
		$transtype_arr["0"] = "ALL";
		foreach ($incomes as $income){
			$transtype_arr [$income->name] = strtoupper($income->name);
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","EXPENSE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		foreach ($incomes as $income){
			$transtype_arr [$income->name] = strtoupper($income->name);
		}
		
		$form_field = array("name"=>"branch", "content"=>"branch name", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"disableEmployee(this.value)"), "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employee", "content"=>"employee", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$emps_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reportfor", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$transtype_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		
		$values["provider"] = "bankdetails";
		return View::make('reports.dailytransactionreport', array("values"=>$values));
	}
	
	private function getDailyTransactiosEmployeeReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$brachId = $values["branch"];
			$empId = "-1";
			if(isset($values["employee"])){
				$empId = $values["employee"];
			}
			$reportFor = "-1";
			if(isset($values["reportfor"])){
				$reportFor = $values["reportfor"];
			}
			$resp = array();
			$employees = \Employee::All();
			foreach ($employees as $employee){
				$recs = DB::select( DB::raw("select lookupValueId, sum(amount) as amt from incometransactions where paymentType='CASH' and createdBy=".$employee->id." and branchId=".$brachId." and date between '".$frmDt."' and '".$toDt."' group by lookupValueId order By lookupValueId"));
				if(count($recs)>0) {
					$row = array();
					$row["branch"] = $employee->fullName;
					$totalAmt = 0;
					foreach ($recs as $rec){
						if($rec->lookupValueId==85){
							$row["tickets"] = $rec->amt;
							$totalAmt = $totalAmt+$rec->amt;
						}
						if($rec->lookupValueId==86){
							$row["ticketcancel"] = $rec->amt;
							$totalAmt = $totalAmt+$rec->amt;
						}
						if($rec->lookupValueId==87){
							$row["cargosimply"] = $rec->amt;
							$totalAmt = $totalAmt+$rec->amt;
						}
						if($rec->lookupValueId==-1){
							$row["cargosimplycancel"] = $rec->amt;
							$totalAmt = $totalAmt+$rec->amt;
						}
					}
					if(!isset($row["tickets"])){
						$row["tickets"] = 0;
					}
					if(!isset($row["ticketcancel"])){
						$row["ticketcancel"] = 0;
					}
					if(!isset($row["cargosimply"])){
						$row["cargosimply"] = 0;
					}
					if(!isset($row["cargosimplycancel"])){
						$row["cargosimplycancel"] = 0;
					}
					if(!isset($row["cargos"])){
						$row["cargos"] = 0;
					}
					$row["total"] = $totalAmt;
					$resp[] = $row;
				}
			}
			echo json_encode($resp);
			return;
		}
		
		$values["branch"] = 1;
		$values["fromdate"] = "2015-10-10";
		$values["todate"] = "2016-10-10";
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
	
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		$branches_arr["0"] = "ALL BRANCHES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
	
		$emps =  \Employee::All();
		$emps_arr = array();
		$emps_arr["0"] = "ALL EMPLOYEES";
		foreach ($emps as $emp){
			$emps_arr[$emp->id] = $emp->fullName;
		}
	
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","INCOME")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$transtype_arr = array();
		$transtype_arr["0"] = "ALL";
		foreach ($incomes as $income){
			$transtype_arr [$income->name] = strtoupper($income->name);
		}
	
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","EXPENSE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		foreach ($incomes as $income){
			$transtype_arr [$income->name] = strtoupper($income->name);
		}
	
		$form_field = array("name"=>"branch", "content"=>"branch name", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onchange","script"=>"disableEmployee(this.value)"), "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employee", "content"=>"employee", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$emps_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reportfor", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$transtype_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
	
		$values["provider"] = "bankdetails";
		return View::make('reports.dailytransactionemployeemodal', array("values"=>$values));
	}
	
	private function getSalaryAdvancesReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "employee.fullName as empname";
			$select_args[] = "empdueamount.amount as amount";
			$select_args[] = "empdueamount.paymentDate as paymentDate";
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "empdueamount.comments as remarks";
			$select_args[] = "empdueamount.id as id";
			$totaladvances = 0;
			$totalreturns  = 0;
			if(true){
				if($values["employee"] == "0"){
					$salaryadvances =  \EmpDueAmount::where("empdueamount.status","=","ACTIVE")->orWhere("empdueamount.deleted","=","No")->join("employee","employee.id","=","empdueamount.empId")->leftjoin("officebranch","officebranch.id","=","empdueamount.branchId")->OrderBy("paymentDate")->select($select_args)->get();
					foreach ($salaryadvances as $salaryadvance){
						$row = array();
						$row["empname"] = $salaryadvance->empname;
						if($salaryadvance->amount>0){
							$totaladvances = $totaladvances+$salaryadvance->amount;
							$row["amount"] = "<span style='color:green'> ".$salaryadvance->amount."</span>";
						}
						else{
							$totalreturns = $totaladvances+$totalreturns;
							$row["amount"] = "<span style='color:red'> ".$salaryadvance->amount."</span>";
						}
						$row["paymentDate"] = date("d-m-Y",strtotime($salaryadvance->paymentDate));
						$row["branch"] = $salaryadvance->branch;
						$row["remarks"] = $salaryadvance->remarks;
						$row["id"] = $salaryadvance->id;
						$resp[] = $row;
					}
				}
				else if($values["employee"] > 0){
				$salaryadvances =  \EmpDueAmount::where("empdueamount.empId","=",$values["employee"])
							->where(function($query){$query->where("empdueamount.status","=","ACTIVE")->orWhere("empdueamount.deleted","=","No");})->join("employee","employee.id","=","empdueamount.empId")->leftjoin("officebranch","officebranch.id","=","empdueamount.branchId")->OrderBy("paymentDate")->select($select_args)->get();
					foreach ($salaryadvances as $salaryadvance){
						$row = array();
						$row["empname"] = $salaryadvance->empname;
						if($salaryadvance->amount>0){
							$totaladvances = $totaladvances+$salaryadvance->amount;
							$row["amount"] = "<span style='color:green'> ".$salaryadvance->amount."</span>";
						}
						else{
							$totalreturns = $totaladvances+$totalreturns;
							$row["amount"] = "<span style='color:red'> ".$salaryadvance->amount."</span>";
						}
						$row["paymentDate"] = date("d-m-Y",strtotime($salaryadvance->paymentDate));
						$row["branch"] = $salaryadvance->branch;
						$row["remarks"] = $salaryadvance->remarks;
						$row["id"] = $salaryadvance->id;
						$resp[] = $row;
					}
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \Employee::ALL();
		$branches_arr = array();
		$branches_arr["0"] = "ALL EMPLOYEES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->fullName." (".$branch->empCode.")";
		}
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employee", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		
		$add_form_fields = array();
		$emps =  \Employee::where("roleId","=",19)->get();
		$emps_arr = array();
		$emps_arr["0"] = "ALL DRIVERS";
		foreach ($emps as $emp){
			$emps_arr[$emp->id] = $emp->fullName;
		}
		
		$vehs =  \Vehicle::where("status","=","ACTIVE")->get();
		$vehs_arr = array();
		foreach ($vehs as $veh){
			$vehs_arr[$veh->id] = $veh->veh_reg;
		}
		$form_field = array("name"=>"fuelstation", "content"=>"by station", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"driver", "content"=>"by driver", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$emps_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"by vehicle", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$vehs_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;

		$form_info["form_fields"] = $form_fields;
		$form_info["add_form_fields"] = $add_form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.salaryadvancesreport', array("values"=>$values));
	}
	
	private function getBankPositionReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "employee.fullName as empname";
			$select_args[] = "empdueamount.amount as amount";
			$select_args[] = "empdueamount.paymentDate as paymentDate";
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "empdueamount.comments as remarks";
			$select_args[] = "empdueamount.id as id";
			$totaladvances = 0;
			$totalreturns  = 0;
			
			DB::statement(DB::raw("CALL bankposition_report('".$frmDt."', '".$toDt."');"));
			if($values["reportfor"] == "transaction_details"){
				if($values["bank"] == "0" && $values["branch"] == "0"){
					$recs = DB::select( DB::raw("SELECT * FROM temp_bankposition_transaction;"));
					foreach ($recs as $rec){
						$row = array();
						$row["type"] = "DEBIT";
						if($rec->type=="income" || $rec->type=="LOCAL"){
							$row["type"] = "CREDIT";
						}
						$row["purpose"] = strtoupper($rec->name);
						if($rec->lookupValueId==991|| $rec->lookupValueId==996){
							$row["purpose"] = strtoupper($rec->entity);
						}
						$row["date"] = date("d-m-Y", strtotime($rec->date));
						$row["chque"] = "";
						if($rec->paymentType=="cheque_credit" || $rec->paymentType=="cheque_debit"){
							$row["chque"] = $rec->chequeNumber;
						}
						$row["amount"] = $rec->amount;
						$row["obalance"] = "0.00";
						$row["cbalance"] = "0.00";
						$row["desc"] = $rec->remarks;
						$resp[] = $row;
					}
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \OfficeBranch::ALL();
		$branches_arr = array();
		$branches_arr["0"] = "ALL BRANCHES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$banks =  \BankDetails::ALL();
		$banks_arr = array();
		$banks_arr["0"] = "ALL BANKS";
		foreach ($banks as $bank){
			$banks_arr[$bank->id] = $bank->bankName;
		}
		$form_field = array("name"=>"reportfor", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>array("bank_summary"=>"Bank Summary Report","transaction_details"=>"Transaction Details Report"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"bank", "content"=>"bank ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$banks_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branch", "content"=>"branch ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.bankpositionreport', array("values"=>$values));
	}
	
	private function getLoansReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "employee.fullName as empname";
			$select_args[] = "empdueamount.amount as amount";
			$select_args[] = "empdueamount.paymentDate as paymentDate";
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "empdueamount.comments as remarks";
			$select_args[] = "empdueamount.id as id";
			$totaladvances = 0;
			$totalreturns  = 0;
			$mons = array(1 => "JANUARY", 2 => "FEBRUARY", 3 => "MARCH", 4 => "APRIL", 5 => "MAY", 6 => "JUNE", 7 => "JULY", 8 => "AUGUST", 9 => "SEPTEMBER", 10 => "OCTOBER", 11 => "NOVEMBER", 12 => "DECEMBER");
			if(true){
				if($values["loan"] == "0"){
					$sql = 'SELECT date, financecompanies.name, loans.vehicleId, loans.agmtDate, loans.installmentAmount, loans.totalInstallments, loans.paidInstallments, loans.paymentType, loans.bankAccountId, concat(bankdetails.bankName," - ",bankdetails.accountNo) as bankName,loans.loanNo, loans.id as loanId, expensetransactions.remarks as remarks FROM `expensetransactions` join loans on loans.id=expensetransactions.entityValue join financecompanies on financecompanies.id=loans.financeCompanyId join bankdetails on bankdetails.id=loans.bankAccountId where entity="LOAN PAYMENT" and date between "'.$frmDt.'" and "'.$toDt.'" order by date;';
				}
				else if($values["loan"] > 0){
					$sql = 'SELECT date, financecompanies.name, loans.vehicleId, loans.agmtDate, loans.installmentAmount, loans.totalInstallments, loans.paidInstallments, loans.paymentType, loans.bankAccountId, concat(bankdetails.bankName," - ",bankdetails.accountNo) as bankName,loans.loanNo, loans.id as loanId, expensetransactions.remarks as remarks FROM `expensetransactions` join loans on loans.id=expensetransactions.entityValue join financecompanies on financecompanies.id=loans.financeCompanyId join bankdetails on bankdetails.id=loans.bankAccountId where loans.id='.$values["loan"].' and entity="LOAN PAYMENT" and date between "'.$frmDt.'" and "'.$toDt.'" order by date;';
				}
				$recs = DB::select( DB::raw($sql));
				foreach ($recs as $rec){
					$row = array();
					$row["date"] = date("d-m-Y",strtotime($rec->date));
					$row["fincompany"] = $rec->name;
					
					$veh_arr = explode(",", $rec->vehicleId);
					$vehs = \Vehicle::whereIn("id",$veh_arr)->get();
					$veh_arr = "";
					foreach ($vehs as $veh){
						$veh_arr = $veh_arr.$veh->veh_reg.", ";
					}
					$row["vehicles"] = $veh_arr;
					
					$agmtDate = $rec->agmtDate;
					$month = date("m", strtotime($agmtDate));
					$month_name = $mons[intval($month)];
					$year = date("Y", strtotime($agmtDate));
					$endDate = date('Y-m-d', strtotime("+$rec->totalInstallments months", strtotime($agmtDate)));						
					$endmonth = date("m", strtotime($endDate));
					$endmonth_name = $mons[intval($endmonth)];
					$endyear = date("Y", strtotime($endDate));
					$row["emiperiod"] = $month_name.", ".$year." - ".$endmonth_name.", ".$endyear;
					$row["emiamt"] = sprintf('%0.2f', $rec->installmentAmount);
					
					$sql = 'select count(*) as cnt from expensetransactions where entity="LoAN PAYMENT" and entityValue='.$rec->loanId.' and date BETWEEN "'.$rec->agmtDate.'" and "'.$rec->date.'";';
					$rec1 = DB::select( DB::raw($sql));
					$rec1 = $rec1[0];
					$row["paidemis"] = ($rec->paidInstallments+$rec1->cnt)."/".$rec->totalInstallments;
					
					$row["paymenttype"] = $rec->paymentType;
					$row["bankdetails"] = $rec->bankName;
					$row["loanno"] = $rec->loanNo;
					$row["remarks"] = $rec->remarks;
					$resp[] = $row;
				}
			}
			else if($values["employee"] > 0){
				$salaryadvances =  \EmpDueAmount::where("empdueamount.empId","=",$values["employee"])
				->where(function($query){$query->where("empdueamount.status","=","ACTIVE")->orWhere("empdueamount.deleted","=","No");})->join("employee","employee.id","=","empdueamount.empId")->leftjoin("officebranch","officebranch.id","=","empdueamount.branchId")->OrderBy("paymentDate")->select($select_args)->get();
				foreach ($salaryadvances as $salaryadvance){
					$row = array();
					$row["empname"] = $salaryadvance->empname;
					if($salaryadvance->amount>0){
						$totaladvances = $totaladvances+$salaryadvance->amount;
						$row["amount"] = "<span style='color:green'> ".$salaryadvance->amount."</span>";
					}
					else{
						$totalreturns = $totaladvances+$totalreturns;
						$row["amount"] = "<span style='color:red'> ".$salaryadvance->amount."</span>";
					}
					$row["paymentDate"] = date("d-m-Y",strtotime($salaryadvance->paymentDate));
					$row["branch"] = $salaryadvance->branch;
					$row["remarks"] = $salaryadvance->remarks;
					$row["id"] = $salaryadvance->id;
					$resp[] = $row;
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$loans =  \Loan::ALL();
		$loans_arr = array();
		$loans_arr["0"] = "ALL LOANS";
		foreach ($loans as $loan){
			$vehs = "";
			if($loan->vehicleId != ""){
				$veh_arr = explode(",", $loan->vehicleId);
				$vehicles = \Vehicle::whereIn("id",$veh_arr)->get();
				$i = 0;
				for($i=0; $i<count($vehicles); $i++){
					if($i+1 == count($vehicles)){
						$vehs = $vehs.$vehicles[$i]->veh_reg;
					}
					else{
						$vehs = $vehs.$vehicles[$i]->veh_reg.", ";
					}
				}
			}
			$loans_arr[$loan->id] = $loan->loanNo." (".$vehs.")";
		}
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"loan", "content"=>"loan no", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$loans_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.loansreport', array("values"=>$values));
	}
	
	private function getDailyFinanceReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["date"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "employee.fullName as empname";
			$select_args[] = "empdueamount.amount as amount";
			$select_args[] = "empdueamount.paymentDate as paymentDate";
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "empdueamount.comments as remarks";
			$select_args[] = "empdueamount.id as id";
			$totaladvances = 0;
			$totalreturns  = 0;
			if(true){
				$sql = 'SELECT date, expensetransactions.amount, financecompanies.name, loans.amountFinanced, loans.vehicleId, loans.agmtDate, loans.installmentAmount, loans.totalInstallments, loans.paidInstallments, loans.paymentType, loans.bankAccountId, concat(bankdetails.bankName," - ",bankdetails.accountNo) as bankName,loans.loanNo, loans.id as loanId, expensetransactions.remarks as remarks FROM `expensetransactions` join loans on loans.id=expensetransactions.entityValue join financecompanies on financecompanies.id=loans.financeCompanyId join bankdetails on bankdetails.id=loans.bankAccountId where entity="LOAN PAYMENT" and date between "'.$frmDt.'" and "'.$frmDt.'" order by date;';
				$recs = DB::select( DB::raw($sql));
				foreach ($recs as $rec){
					$row = array();
					$Date = $rec->agmtDate;
					$startDate = strtotime(date("Y-m-d", strtotime($Date)) . " +".$rec->paidInstallments." day");
					$startDate = date ( 'Y-m-d' , $startDate );
					$endDate = strtotime(date("Y-m-d", strtotime($Date)) . " +".$rec->totalInstallments." day");
					$endDate = date ( 'Y-m-d' , $endDate );

					$sql = 'select sum(amount) as amt from expensetransactions where entity="LoAN PAYMENT" and status="ACTIVE" and entityValue='.$rec->loanId.' and date BETWEEN "'.$rec->agmtDate.'" and "'.$frmDt.'";';
					$rec1 = DB::select( DB::raw($sql));
					$rec1 = $rec1[0];
					$totalamnt = $rec1->amt;
					$amount = $rec->installmentAmount;
					$todayPaidAmnt = $rec->amount;
					$start = strtotime($startDate);
					$end = strtotime($frmDt);
					$datediff = $end - $start;
					$days = floor($datediff/(60*60*24));

					$paidStuff = (int)($totalamnt/$amount);
					$remstuff = $totalamnt%$amount;
						
					$currentDay = $days+$rec->paidInstallments;
					$totalTobePaid = $currentDay*$amount;
					$suspenseAmount = ($days*$amount) - $totalamnt;
					$total_installments = $rec->totalInstallments;
					
					if(true){ //$currentDay <= $total_installments
						$row["fincompany"] = $rec->name;
						$row["loanamt"] = sprintf('%0.2f', $rec->amountFinanced);
						$row["startdt"] = date("d-m-Y",strtotime($startDate));
						$row["enddt"] = date("d-m-Y",strtotime($endDate));
						$row["suspense"] = "<font color='red'><b>".sprintf('%0.2f', $suspenseAmount)."</b></font>";
						$row["paidemis"] = ($days+1+$rec->paidInstallments)." Day";
						$row["todaypayment"] = sprintf('%0.2f',$todayPaidAmnt);
						$row["todaysuspense"] = sprintf('%0.2f',$todayPaidAmnt-$amount);
					}
					
					$resp[] = $row;
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$loans =  \Loan::ALL();
		$loans_arr = array();
		$loans_arr["0"] = "ALL LOANS";
		foreach ($loans as $loan){
			$vehs = "";
			if($loan->vehicleId != ""){
				$veh_arr = explode(",", $loan->vehicleId);
				$vehicles = \Vehicle::whereIn("id",$veh_arr)->get();
				$i = 0;
				for($i=0; $i<count($vehicles); $i++){
					if($i+1 == count($vehicles)){
						$vehs = $vehs.$vehicles[$i]->veh_reg;
					}
					else{
						$vehs = $vehs.$vehicles[$i]->veh_reg.", ";
					}
				}
			}
			$loans_arr[$loan->id] = $loan->loanNo." (".$vehs.")";
		}
		$form_field = array("name"=>"date", "content"=>"date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.dailyfinancereport', array("values"=>$values));
	}
	
	private function getDailySettlementReport($values){
		if (\Request::isMethod('post'))
		{
			$dt = date("Y-m-d", strtotime($values["date"]));
			$brachId = $values["branch"];
			$resp = array();
			
			$booking_income = 0;
			$booking_cancel = 0;
			$corgos_simply_income = 0;
			$corgos_simply_cancel = 0;
			$other_income = 0;
			$total_expenses = 0;
			$bank_deposited = 0;
			$branch_deposited = 0;
			
			
			DB::statement(DB::raw("CALL daily_transactions_report('".$dt."', '".$dt."');"));
			$recs = DB::select( DB::raw("select * from temp_daily_transaction where branchId=".$brachId." order by branchId"));
			if(count($recs)>0) {
				$totalAmt = 0;
				foreach ($recs as $rec){
					$row = array();
					$brachName = "";
					if($rec->branchId>0){
						$brachName = \OfficeBranch::where("id","=",$rec->branchId)->first();
						$brachName = $brachName->name;
					}
					$row = array();
					$row["branch"] = $brachName;
					if($rec->type=="LOCAL"  || $rec->type == "DAILY"){
						$rec->type = $rec->type." TRIP";
					}
					$row["type"] = strtoupper($rec->type);
					$row["date"] = date("d-m-Y",strtotime($rec->date));
					$row["amount"] = $rec->amount;
					$row["purpose"] = strtoupper($rec->name);
					if($rec->lookupValueId==8888){
						$row["purpose"] = "CREDITED TO BRANCH - TRIP BALANCE";
					}
					if($rec->lookupValueId==9999){
						$row["purpose"] = "DEBITED FROM BRANCH - TRIP BALANCE";
					}
					else if($rec->lookupValueId==999){
						if($rec->entityValue>0){
							$prepaidName = \LookupTypeValues::where("id","=",$rec->entityValue)->first();
							$prepaidName = $prepaidName->name;
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = $prepaidName;
						}
						else{
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = "";
						}
					}
					else if($rec->lookupValueId==998){
						if($rec->entityValue>0){
							$creditsupplier = \CreditSupplier::where("id","=",$rec->entityValue)->first();
							$creditsupplier = $creditsupplier->supplierName;
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = $creditsupplier;
						}
						else{
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = "";
						}
					}
					else if($rec->lookupValueId==997){
						if($rec->entityValue>0){
							$fuelstation = \FuelStation::where("id","=",$rec->entityValue)->first();
							$fuelstation = $fuelstation->name;
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = $fuelstation;
						}
						else{
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = "";
						}
					}
					else if($rec->lookupValueId==991){
						if($rec->entityValue>0){
							$dfid = \DailyFinance::where("id","=",$rec->entityValue)->first();
							$dfid = $dfid->financeCompanyId;
							$finanacecompany = \FinanceCompany::where("id","=",$dfid)->first();
							$finanacecompany = $finanacecompany->name;
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = $finanacecompany;
						}
						else{
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = "";
						}
					}
					else if($rec->lookupValueId==73){
						$bankdetails = \IncomeTransaction::where("transactionId","=",$rec->rowid)->leftjoin("bankdetails","bankdetails.id","=","incometransactions.bankId")->first();
						$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
						$row["employee"] = $bankdetails;
					}
					else if($rec->lookupValueId==84){
						$bankdetails = \ExpenseTransaction::where("transactionId","=",$rec->rowid)->leftjoin("bankdetails","bankdetails.id","=","expensetransactions.bankId")->first();
						$bankdetails = $bankdetails->bankName." - ".$bankdetails->accountNo;
						$row["employee"] = $bankdetails;
					}
					else if($rec->lookupValueId==63){
						$lookupvalue = \LookupTypeValues::where("id","=",$rec->lookupValueId)->first();
						$lookupvalue = $lookupvalue->name;
						$row["employee"] = "";
					}
					else{
						if($rec->entityValue != "0"){
							$row["purpose"] = strtoupper($rec->entity);
							$row["employee"] = $rec->lookupValueId." - ".$rec->entityValue;
						}
						else{
							$row["employee"] = $rec->entity;
						}
							
					}
					
					if($row["type"] == "LOCAL TRIP" || $row["type"]=="DAILY TRIP"){
						if($row["purpose"] == "VEHICLE ADVANCES"){
							$row["purpose"] = $row["purpose"]." (".$row["type"].")";
							$row["type"] = "EXPENSE";
						}
						if($row["purpose"] == "ADVANCE AMOUNT"){
							$row["purpose"] = $row["purpose"]." (".$row["type"].")";
							$row["type"] = "INCOME";
						}
						if($row["purpose"] == "CREDITED TO BRANCH - TRIP BALANCE"){
							$row["purpose"] = $row["purpose"]." (".$row["type"].")";
							$row["type"] = "INCOME";
						}
						if($row["purpose"] == "DEBITED FROM BRANCH - TRIP BALANCE"){
							$row["purpose"] = $row["purpose"]." (".$row["type"].")";
							$row["type"] = "EXPENSE";
						}
					}
					
					if($row["purpose"] == "TICKETS AMOUNT" ){
						$booking_income = $booking_income+$row["amount"];
					}
					else if($row["purpose"] == "TICKETS CANCEL AMOUNT" ){
						$booking_cancel = $booking_cancel+$row["amount"];
					}
					else if($row["purpose"] == "CARGO SIMPLY AMOUNT" ){
						$corgos_simply_income = $corgos_simply_income+$row["amount"];
					}
					else if($row["purpose"] == "CARGO SIMPLY CANCEL" ){
						$corgos_simply_cancel = $corgos_simply_cancel+$row["amount"];
					}
					else if($row["purpose"] == "BANK DEPOSITS" ){
						$bank_deposited = $bank_deposited+$row["amount"];
					}
					else if($row["type"] == "INCOME" ){
						$other_income = $other_income+$row["amount"];
					}
					else if($row["type"] == "EXPENSE" && $row["purpose"] == "BRANCH DEPOSIT" ){
						$branch_deposited = $branch_deposited+$row["amount"];
					}
					else if($row["type"] == "EXPENSE" ){
						$total_expenses = $total_expenses+$row["amount"];
					}
					
					$row["comments"] = $rec->remarks;
					$row["createdby"] = $rec->createdBy;
					$resp[] = $row;
				}
			}
			$booking_income = sprintf('%0.2f', $booking_income);
			$booking_cancel = sprintf('%0.2f', $booking_cancel);
			$corgos_simply_income = sprintf('%0.2f', $corgos_simply_income);
			$corgos_simply_cancel = sprintf('%0.2f', $corgos_simply_cancel);
			$other_income = sprintf('%0.2f', $other_income);
			$total_expenses = sprintf('%0.2f', $total_expenses);
			$bank_deposited = sprintf('%0.2f', $bank_deposited);
			$branch_deposited = sprintf('%0.2f', $branch_deposited);
				
			$cf_amt = 0;
			$cf_prev_amt = 0;
			$nextDay = strtotime(date("Y-m-d", strtotime($dt)) . " +1 day");
			$nextDay = date ('Y-m-d', $nextDay);
			$cf_details = \IncomeTransaction::where("branchId","=",$brachId)->where("date","=",$nextDay)->where("status","=","ACTIVE")->where("lookupValueId","=","194")->get();
			if(count($cf_details)>0){
				$cf_details = $cf_details[0];
				$cf_amt = $cf_details->amount;
			}
			$cf_details = \IncomeTransaction::where("branchId","=",$brachId)->where("date","=",date("Y-m-d",strtotime($dt)))->where("status","=","ACTIVE")->where("lookupValueId","=","194")->get();
			if(count($cf_details)>0){
				$cf_details = $cf_details[0];
				$cf_prev_amt = $cf_details->amount;
			}
			
			$resp_arr = array("data"=>$resp,"booking_income"=>$booking_income,"booking_cancel"=>$booking_cancel,"cargos_simply_income"=>$corgos_simply_income,
					"cargos_simply_cancel"=>$corgos_simply_cancel,"other_income"=>$other_income,"total_expense"=>$total_expenses,
					"branch_deposites"=>$branch_deposited,"bank_deposits"=>$bank_deposited,"cf_amt"=>$cf_amt,"cf_prev_amt"=>$cf_prev_amt
					);
			echo json_encode($resp_arr);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \OfficeBranch::ALL();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$form_field = array("name"=>"branch", "content"=>"branch ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"date ", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "dailysettlement";
		return View::make('reports.dailysettlementreport', array("values"=>$values));
	}
	
	private function getDailySettlementReportsReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "sum(actualSalary) as actualSalary";
			$select_args[] = "sum(dueDeductions) as dueDeductions";
			$select_args[] = "sum(leaveDeductions) as leaveDeductions";
			$select_args[] = "sum(pf) as pf";
			$select_args[] = "sum(esi) as esi";
			$select_args[] = "sum(salaryPaid) as salaryPaid";
			if(isset($values["branch"])){
				if($values["branch"] == "0"){
					$branchsuspenses = \BranchSuspenseReport::whereBetween("reportDate",array($frmDt,$toDt))->OrderBy("reportDate")->get() ;
					foreach ($branchsuspenses as $branchsuspense){
						$row = array();
						$branchname = \OfficeBranch::where("id","=",$branchsuspense->branchId)->get();
						if(count($branchname)>0){
							$branchname = $branchname[0];
							$branchname = $branchname->name;
						}
						else {
							$branchname = "";
						}
						$row["branchname"] = $branchname;
						$row["reportdate"] = date("d-m-Y",strtotime($branchsuspense->reportDate));
						$row["income"] = sprintf('%0.2f', $branchsuspense->total_income);
						$row["expense"] = sprintf('%0.2f', $branchsuspense->total_expense);
						$row["bankdeposit"] = sprintf('%0.2f', $branchsuspense->bank_deposit);
						$row["branchdeposit"] = sprintf('%0.2f', $branchsuspense->branch_deposit);
						$balanceWithoutCF = $branchsuspense->total_income-($branchsuspense->total_expense+$branchsuspense->bank_deposit+$branchsuspense->branch_deposit);
						$row["balance"] = sprintf('%0.2f', $balanceWithoutCF);
						
						$cf_amt = 0;
						$checkString = "";
						$col ="";
						$nextDay = strtotime(date("Y-m-d", strtotime($branchsuspense->reportDate)) . " +1 day");
						$nextDay = date ( 'Y-m-d' , $nextDay );
						$cf_details = \IncomeTransaction::where("branchId","=",$branchsuspense->branchId)->where("date","=",$nextDay)->where("status","=","ACTIVE")->where("lookupValueId","=","194")->get();
						if(count($cf_details)>0){
							$cf_details = $cf_details[0];
							$cf_amt = $cf_details->amount;
						}
						if($cf_amt>($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)){
							$rem = round(($cf_amt-($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)),2);
							$checkString = $rem." (LESS)";
							$col = "red";
						}
						else if($cf_amt<($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)){
							$rem = round(($cf_amt-($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)),2);
							$checkString = -1*$rem." (MORE)";
							$col = "green";
						}
						else {
							$checkString="DONE";
							$col="lightgrey";
						}
						
						$row["carryforward"] = sprintf('%0.2f', ($balanceWithoutCF-$cf_amt));
						$row["settlement"] = sprintf('%0.2f', $cf_amt);
						$row["status"] = "<span style='color:".$col.";font-weight:bold;'>".$checkString."</span>"; 
						$row["comments"] = $branchsuspense->comments;
						$date = date("d-m-Y",strtotime($branchsuspense->reportDate));
						$row["action"] = '<a href="report?reporttype=dailysettlement&branch='.$branchsuspense->branchId.'&date='.$date.'" target="_blank"><button class="btn btn-minier btn-primary">&nbsp;&nbsp;EDIT&nbsp;&nbsp;</button></a>';
						$resp[] = $row;
					}
				}
				else if($values["branch"]>0){
					$branchsuspenses = \BranchSuspenseReport::where("branchId","=",$values["branch"])->whereBetween("reportDate",array($frmDt,$toDt))->OrderBy("reportDate")->get() ;
					foreach ($branchsuspenses as $branchsuspense){
						$row = array();
						$branchname = \OfficeBranch::where("id","=",$branchsuspense->branchId)->get();
						if(count($branchname)>0){
							$branchname = $branchname[0];
							$branchname = $branchname->name;
						}
						else {
							$branchname = "";
						}
						$row["branchname"] = $branchname;
						$row["reportdate"] = date("d-m-Y",strtotime($branchsuspense->reportDate));
						$row["income"] = sprintf('%0.2f', $branchsuspense->total_income);
						$row["expense"] = sprintf('%0.2f', $branchsuspense->total_expense);
						$row["bankdeposit"] = sprintf('%0.2f', $branchsuspense->bank_deposit);
						$row["branchdeposit"] = sprintf('%0.2f', $branchsuspense->branch_deposit);
						$balanceWithoutCF = $branchsuspense->total_income-($branchsuspense->total_expense+$branchsuspense->bank_deposit+$branchsuspense->branch_deposit);
						$row["balance"] = sprintf('%0.2f', $balanceWithoutCF);
						
						$cf_amt = 0;
						$checkString = "";
						$col ="";
						$nextDay = strtotime(date("Y-m-d", strtotime($branchsuspense->reportDate)) . " +1 day");
						$nextDay = date ( 'Y-m-d' , $nextDay );
						$cf_details = \IncomeTransaction::where("branchId","=",$branchsuspense->branchId)->where("date","=",$nextDay)->where("status","=","ACTIVE")->where("lookupValueId","=","194")->get();
						if(count($cf_details)>0){
							$cf_details = $cf_details[0];
							$cf_amt = $cf_details->amount;
						}
						if($cf_amt>($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)){
							$rem = round(($cf_amt-($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)),2);
							$checkString = $rem." (LESS)";
							$col = "red";
						}
						else if($cf_amt<($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)){
							$rem = round(($cf_amt-($branchsuspense->bank_deposit+$branchsuspense->branch_deposit)),2);
							$checkString = -1*$rem." (MORE)";
							$col = "green";
						}
						else {
							$checkString="DONE";
							$col="lightgrey";
						}
						
						$row["carryforward"] = sprintf('%0.2f', ($balanceWithoutCF-$cf_amt));
						$row["settlement"] = sprintf('%0.2f', $cf_amt);
						$row["status"] = "<span style='color:".$col.";font-weight:bold;'>".$checkString."</span>"; 
						$row["comments"] = $branchsuspense->comments;
						$date = date("d-m-Y",strtotime($branchsuspense->reportDate));
						$row["action"] = '<a href="report?reporttype=dailysettlement&branch='.$branchsuspense->branchId.'&date='.$date.'" target="_blank"><button class="btn btn-minier btn-primary">&nbsp;&nbsp;EDIT&nbsp;&nbsp;</button></a>';
						$resp[] = $row;
					}
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \OfficeBranch::ALL();
		$branches_arr = array();
		$branches_arr["0"] = "ALL BRANCHES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$form_field = array("name"=>"branch", "content"=>"branch ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "dailysettlement";
		return View::make('reports.dailysettlementreportsreport', array("values"=>$values));
	}
	
	private function getInchargeTransactionsReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$resp2 = array();
			$totexpenses = 0;
			$select_args = array();
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "incometransactions.amount as amount";
			$select_args[] = "incometransactions.date as date";
			$select_args[] = "incometransactions.remarks as remarks";
			$select_args[] = "employee.fullName as name";
			if(isset($values["incharge"])){
				$select_args = array();
				$select_args[] = "officebranch.name as branch";
				$select_args[] = "incometransactions.amount as amount";
				$select_args[] = "incometransactions.date as date";
				$select_args[] = "incometransactions.remarks as remarks";
				$select_args[] = "employee.fullName as name";
				$inchargetransactions = \IncomeTransaction::leftjoin("officebranch","officebranch.id","=","incometransactions.branchId")
							->leftjoin("employee","employee.id","=","incometransactions.createdBy")
							->where("inchargeId","=",$values["incharge"])
							->where("lookupValueId","=",161)
							->whereBetween("date",array($frmDt,$toDt))
							->OrderBy("date")->select($select_args)->get() ;
				foreach ($inchargetransactions as $inchargetransaction){
					$row = array();
					$row["branch"] = $inchargetransaction->branch;
					$row["type"] =  "<span style='color:green;'>Debited from Incharge Account</span>";
					$row["amount"] = $inchargetransaction->amount;
					$row["date"] = date("d-m-Y",strtotime($inchargetransaction->date));
					$row["remarks"] = $inchargetransaction->remarks;
					$row["name"] = $inchargetransaction->name;
					$resp[] = $row;
				}
				$select_args = array();
				$select_args[] = "officebranch.name as branch";
				$select_args[] = "expensetransactions.amount as amount";
				$select_args[] = "expensetransactions.date as date";
				$select_args[] = "expensetransactions.remarks as remarks";
				$select_args[] = "employee.fullName as name";
				$inchargetransactions = \ExpenseTransaction::leftjoin("officebranch","officebranch.id","=","expensetransactions.branchId")
										->leftjoin("employee","employee.id","=","expensetransactions.createdBy")
										->where("inchargeId","=",$values["incharge"])
										->where("lookupValueId","=",251)
										->whereBetween("date",array($frmDt,$toDt))
										->OrderBy("date")->select($select_args)->get() ;
				foreach ($inchargetransactions as $inchargetransaction){
					$row = array();
					$row["branch"] = $inchargetransaction->branch;
					$row["type"] =  "<span style='color:red;'>Credited into Incharge Account</span>";
					$row["amount"] = $inchargetransaction->amount;
					$row["date"] = date("d-m-Y",strtotime($inchargetransaction->date));
					$row["remarks"] = $inchargetransaction->remarks;
					$row["name"] = $inchargetransaction->name;
					$resp[] = $row;
				}
				
				DB::statement(DB::raw("CALL incharge_transaction_report('".$frmDt."', '".$toDt."');"));
				$recs = DB::select( DB::raw("SELECT *,temp_incharge_transaction.name as purpose, temp_incharge_transaction.createdBy as createdBy, officebranch.name as branchname FROM `temp_incharge_transaction` left join officebranch on officebranch.id=temp_incharge_transaction.branchId where inchargeId='".$values["incharge"]."'"));
				foreach ($recs as $rec){
					$row = array();
					$row["branch"] = $rec->branchname;
					$row["date"] = date("d-m-Y",strtotime($rec->date));
					$row["amount"] =  $rec->amount;
					$totexpenses = $totexpenses+$rec->amount;
					$row["purpose"] =  $rec->purpose;
					$row["paidto"] =  $rec->entityValue;//$rec->type." - ".$rec->tripId." - ".$rec->entityValue;
					$vehreg = "";
					if($rec->type == "LOCAL"){
						$row["purpose"] = "LOCAL TRIP ADVANCE : <br/>";
						$entities = \BusBookings::where("id","=",$rec->tripId)->get();
						foreach ($entities as $entity){
							$entity["sourcetrip"] = $entity["source_start_place"]."<br/> ".$entity["source_end_place"];
							$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>Date & Time &nbsp;: ".$entity["source_date"]." ".$entity["source_time"];
							$row["purpose"] = $row["purpose"].$entity->sourcetrip;
						}
						if($rec->entityValue>0){
							$vehicle = \Vehicle::where("id","=",$rec->entityValue)->get();
							if(count($vehicle)>0){
								$vehicle = $vehicle[0];
								$vehreg = $vehicle->veh_reg;
							}
						}
						$row["paidto"] = $vehreg;
					}
					if($rec->type == "DAILY"){
						$select_args = array();
						$select_args[] = "vehicle.veh_reg as vehicleId";
						$select_args[] = "tripdetails.tripStartDate as tripStartDate";
						$select_args[] = "tripdetails.id as routeInfo";
						$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
						$select_args[] = "tripdetails.routeCount as routes";
						$select_args[] = "tripdetails.id as id";
						$routeInfo = "";
						$entities = \TripDetails::where("tripdetails.id","=",$rec->tripId)->leftjoin("vehicle", "vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
						foreach ($entities as $entity){
							$entity["tripStartDate"] = date("d-m-Y",strtotime($entity["tripStartDate"]));
							$tripservices = \TripServiceDetails::where("tripId","=",$entity->id)->where("status","=","Running")->get();
							foreach($tripservices as $tripservice){
								$select_args = array();
								$select_args[] = "cities.name as sourceCity";
								$select_args[] = "cities1.name as destinationCity";
								$select_args[] = "servicedetails.serviceNo as serviceNo";
								$select_args[] = "servicedetails.active as active";
								$select_args[] = "servicedetails.serviceStatus as serviceStatus";
								$select_args[] = "servicedetails.id as id";
								$service = \ServiceDetails::where("servicedetails.id","=",$tripservice->serviceId)->join("cities","cities.id","=","servicedetails.sourceCity")->join("cities as cities1","cities1.id","=","servicedetails.destinationCity")->select($select_args)->get();
								if(count($service)>0){
									$service = $service[0];
									$routeInfo = $routeInfo."<span style='font-size:13px; font-weight:bold; color:red;'>".$service->serviceNo."</span> - &nbsp; ".$service->sourceCity." TO ".$service->destinationCity."<br/>";
								}
							}
							$row["purpose"] = "DAILY TRIP ADVANCE : <br/>";
							$row["purpose"] = $row["purpose"].$routeInfo;
							$row["paidto"] = $entity->vehicleId;
						}
					}
					if($row["paidto"] == "0"){
						$row["paidto"] = "";
					}
					$row["remarks"] =$rec->remarks;
					$row["name"] = $rec->createdBy;
					$resp2[] = $row;
				}
			}
			$resp_json = array("data1"=>$resp,"data2"=>$resp2,"total_expenses"=>$totexpenses);
			echo json_encode($resp_json);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		
		$select_args = array();
		$select_args[] = "inchargeaccounts.empid as id";
		$select_args[] = "employee.fullName as fullName";	
		$incharges =  \InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empId")->select($select_args)->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->fullName;
		}
		$form_field = array("name"=>"incharge", "content"=>"incharge ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$incharges_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "dailysettlement";
		return View::make('reports.inchargetransactionsreport', array("values"=>$values));
	}
	
	private function getSalaryReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "officebranch.name as branch";
			$select_args[] = "sum(actualSalary) as actualSalary";
			$select_args[] = "sum(dueDeductions) as dueDeductions";
			$select_args[] = "sum(leaveDeductions) as leaveDeductions";
			$select_args[] = "sum(pf) as pf";
			$select_args[] = "sum(esi) as esi";
			$select_args[] = "sum(salaryPaid) as salaryPaid";
			$totalactsalary = 0;
			$totalduedeductions  = 0;
			$totalleavedeductions  = 0;
			$totalpf  = 0;
			$totalesi = 0;
			$totalsalarypaid = 0;
			
			if(isset($values["typeofreport"]) && $values["typeofreport"]=="branch_wise_salary_report"){
				if($values["paidfrombranch"] == "0"){
					$sql = "SELECT officebranch.name as branch, sum(actualSalary) as actualSalary, sum(dueDeductions) as dueDeductions, sum(leaveDeductions) as leaveDeductions, sum(pf) as pf, sum(esi) as esi, sum(salaryPaid) as salaryPaid from empsalarytransactions left join officebranch on officebranch.id=empsalarytransactions.branchId where paymentDate BETWEEN '".$frmDt."' and '".$toDt."' group by branchId";
					$salarytransactions =  \DB::select(DB::raw($sql));
					foreach ($salarytransactions as $salarytransaction){
						$row = array();
						$row["branch"] = $salarytransaction->branch;
						$row["actualSalary"] = $salarytransaction->actualSalary;
						$row["dueDeductions"] = $salarytransaction->dueDeductions;
						$row["leaveDeductions"] = $salarytransaction->leaveDeductions;
						$row["pf"] = $salarytransaction->pf;
						$row["esi"] = $salarytransaction->esi;
						$row["salaryPaid"] = $salarytransaction->salaryPaid;
						$resp[] = $row;
						$totalactsalary = $totalactsalary+$salarytransaction->actualSalary;
						$totalduedeductions = $totalduedeductions+$salarytransaction->dueDeductions;
						$totalleavedeductions = $totalleavedeductions+$salarytransaction->leaveDeductions;
						$totalpf = $totalpf+$salarytransaction->pf;
						$totalesi = $totalesi+$salarytransaction->esi;
						$totalsalarypaid = $totalsalarypaid+$salarytransaction->salaryPaid;
					}
				}
				else if($values["paidfrombranch"]>0){
					$sql = "SELECT officebranch.name as branch, sum(actualSalary) as actualSalary, sum(dueDeductions) as dueDeductions, sum(leaveDeductions) as leaveDeductions, sum(pf) as pf, sum(esi) as esi, sum(salaryPaid) as salaryPaid from empsalarytransactions left join officebranch on officebranch.id=empsalarytransactions.branchId where branchId= ".$values["paidfrombranch"]." and paymentDate BETWEEN '".$frmDt."' and '".$toDt."' group by branchId";
					$salarytransactions =  \DB::select(DB::raw($sql));
					foreach ($salarytransactions as $salarytransaction){
						$row = array();
						$row["branch"] = $salarytransaction->branch;
						$row["actualSalary"] = $salarytransaction->actualSalary;
						$row["dueDeductions"] = $salarytransaction->dueDeductions;
						$row["leaveDeductions"] = $salarytransaction->leaveDeductions;
						$row["pf"] = $salarytransaction->pf;
						$row["esi"] = $salarytransaction->esi;
						$row["salaryPaid"] = $salarytransaction->salaryPaid;
						$resp[] = $row;
						$totalactsalary = $totalactsalary+$salarytransaction->actualSalary;
						$totalduedeductions = $totalduedeductions+$salarytransaction->dueDeductions;
						$totalleavedeductions = $totalleavedeductions+$salarytransaction->leaveDeductions;
						$totalpf = $totalpf+$salarytransaction->pf;
						$totalesi = $totalesi+$salarytransaction->esi;
						$totalsalarypaid = $totalsalarypaid+$salarytransaction->salaryPaid;
					}
				}
			}
			if(isset($values["typeofreport"]) && $values["typeofreport"]=="bank_payment_report"){
				if($values["paidfrombranch"] == "0"){
					$sql = "SELECT officebranch.name as branch, employee.empCode as empId, employee.fullName as name, salaryMonth, paymentDate, (actualSalary) as actualSalary, (dueDeductions) as dueDeductions, (leaveDeductions) as leaveDeductions, (pf) as pf, (esi) as esi, (salaryPaid) as salaryPaid, empsalarytransactions.paymentType, empsalarytransactions.bankAccount, empsalarytransactions.accountNumber, empsalarytransactions.chequeNumber, empsalarytransactions.bankName, empsalarytransactions.accountNumber, empsalarytransactions.issueDate, empsalarytransactions.transactionDate from empsalarytransactions join employee on employee.id=empsalarytransactions.empId left join officebranch on officebranch.id=empsalarytransactions.branchId where paymentType != 'cash' and paymentDate BETWEEN '".$frmDt."' and '".$toDt."' order by branchId";
				}
				else if($values["paidfrombranch"]>0){
					$sql = "SELECT officebranch.name as branch, employee.empCode as empId, employee.fullName as name, salaryMonth, paymentDate, (actualSalary) as actualSalary, (dueDeductions) as dueDeductions, (leaveDeductions) as leaveDeductions, (pf) as pf, (esi) as esi, (salaryPaid) as salaryPaid, empsalarytransactions.paymentType, empsalarytransactions.bankAccount, empsalarytransactions.accountNumber, empsalarytransactions.chequeNumber, empsalarytransactions.bankName, empsalarytransactions.accountNumber, empsalarytransactions.issueDate, empsalarytransactions.transactionDate from empsalarytransactions join employee on employee.id=empsalarytransactions.empId left join officebranch on officebranch.id=empsalarytransactions.branchId where branchId=".$values["paidfrombranch"]." and paymentType != 'cash' and paymentDate BETWEEN '".$frmDt."' and '".$toDt."' order by branchId";
				}
				$salarytransactions =  \DB::select(DB::raw($sql));
				foreach ($salarytransactions as $salarytransaction){
					$row = array();
					$row["branch"] = $salarytransaction->branch;
					$row["name"] = $salarytransaction->name."-".$salarytransaction->empId;
					$row["salaryMonth"] = date("M",strtotime($salarytransaction->salaryMonth)).", ".date("Y",strtotime($salarytransaction->salaryMonth));
					$row["paymentDate"] = date("d-m-Y",strtotime($salarytransaction->paymentDate));
					$row["paymentInfo"] = "";
					if($salarytransaction->paymentType == "neft"){
						$row["paymentInfo"] = "Payment type : NEFT<br/>";
						$row["paymentInfo"] = $row["paymentInfo"]."Bank Name : ".$salarytransaction->bankName."<br/>";
						$row["paymentInfo"] = $row["paymentInfo"]."Acct No. : ".$salarytransaction->accountNumber."<br/>";
					}
					else if($salarytransaction->paymentType == "ecs"){
						$row["paymentInfo"] = "Payment type : ESC<br/>";
						$row["paymentInfo"] = $row["paymentInfo"]."Bank Name : ".$salarytransaction->bankName."<br/>";
						$row["paymentInfo"] = $row["paymentInfo"]."Acct No. : ".$salarytransaction->accountNumber."<br/>";
					}
					else if($salarytransaction->paymentType == "rtgs"){
						$row["paymentInfo"] = "Payment type : RTGS<br/>";
						$row["paymentInfo"] = $row["paymentInfo"]."Bank Name : ".$salarytransaction->bankName."<br/>";
						$row["paymentInfo"] = $row["paymentInfo"]."Acct No. : ".$salarytransaction->accountNumber."<br/>";
					}
					else if($salarytransaction->paymentType == "cheque_debit"){
						$row["paymentInfo"] = "Payment type : CHEQUE DEBIT<br/>";
						$bankinfo = "";
						$bank = \BankDetails::where("id","=",$salarytransaction->bankAccount)->get();
						if(count($bank)>0){
							$bank = $bank[0];
							$bankinfo = $bankinfo."Bank Name : ".$bank->bankName." - ".$bank->accountNo."(".$bank->branchName.")<br/>";
						}
						$row["paymentInfo"] = $row["paymentInfo"].$bankinfo;
						$row["paymentInfo"] = $row["paymentInfo"]."Cheque No. : ".$salarytransaction->chequeNumber."<br/>";
					}
					$row["actualSalary"] = $salarytransaction->actualSalary;
					$row["dueDeductions"] = $salarytransaction->dueDeductions;
					$row["leaveDeductions"] = $salarytransaction->leaveDeductions;
					$row["pf"] = $salarytransaction->pf;
					$row["esi"] = $salarytransaction->esi;
					$row["salaryPaid"] = $salarytransaction->salaryPaid;
					$resp[] = $row;
				}
			}
			if(isset($values["typeofreport"]) && $values["typeofreport"]=="employee_wise_salary_report"){
				if($values["employee"] == "0"){
					$sql = "SELECT officebranch.name as branch, employee.empCode as empId, employee.fullName as name, salaryMonth, paymentDate, (actualSalary) as actualSalary, (dueDeductions) as dueDeductions, (leaveDeductions) as leaveDeductions, (pf) as pf, (esi) as esi, (salaryPaid) as salaryPaid from empsalarytransactions join employee on employee.id=empsalarytransactions.empId left join officebranch on officebranch.id=empsalarytransactions.branchId where paymentDate BETWEEN '".$frmDt."' and '".$toDt."' order by branchId";
					$salarytransactions =  \DB::select(DB::raw($sql));
					foreach ($salarytransactions as $salarytransaction){
						$row = array();
						$row["branch"] = $salarytransaction->branch;
						$row["empId"] = $salarytransaction->empId;
						$row["name"] = $salarytransaction->name;
						$row["salaryMonth"] = date("M",strtotime($salarytransaction->salaryMonth)).", ".date("Y",strtotime($salarytransaction->salaryMonth));
						$row["paymentDate"] = date("d-m-Y",strtotime($salarytransaction->paymentDate));
						$row["actualSalary"] = $salarytransaction->actualSalary;
						$row["dueDeductions"] = $salarytransaction->dueDeductions;
						$row["leaveDeductions"] = $salarytransaction->leaveDeductions;
						$row["pf"] = $salarytransaction->pf;
						$row["esi"] = $salarytransaction->esi;
						$row["salaryPaid"] = $salarytransaction->salaryPaid;
						$resp[] = $row;
					}
				}
				else if($values["employee"]>0){
					$sql = "SELECT officebranch.name as branch, sum(actualSalary) as actualSalary, sum(dueDeductions) as dueDeductions, sum(leaveDeductions) as leaveDeductions, sum(pf) as pf, sum(esi) as esi, sum(salaryPaid) as salaryPaid from empsalarytransactions left join officebranch on officebranch.id=empsalarytransactions.branchId where empId= ".$values["employee"]." and paymentDate BETWEEN '".$frmDt."' and '".$toDt."'";
					$salarytransactions =  \DB::select(DB::raw($sql));
					foreach ($salarytransactions as $salarytransaction){
						$row = array();
						$row["branch"] = $salarytransaction->branch;
						$row["actualSalary"] = $salarytransaction->actualSalary;
						$row["dueDeductions"] = $salarytransaction->dueDeductions;
						$row["leaveDeductions"] = $salarytransaction->leaveDeductions;
						$row["pf"] = $salarytransaction->pf;
						$row["esi"] = $salarytransaction->esi;
						$row["salaryPaid"] = $salarytransaction->salaryPaid;
						$resp[] = $row;
						$totalactsalary = $totalactsalary+$salarytransaction->actualSalary;
						$totalduedeductions = $totalduedeductions+$salarytransaction->dueDeductions;
						$totalleavedeductions = $totalleavedeductions+$salarytransaction->leaveDeductions;
						$totalpf = $totalpf+$salarytransaction->pf;
						$totalesi = $totalesi+$salarytransaction->esi;
						$totalsalarypaid = $totalsalarypaid+$salarytransaction->salaryPaid;
					}
				}
			}
			if(isset($values["typeofreport"]) && $values["typeofreport"]=="pf_report"){
				$sql = "SELECT officebranch.name as branch, employee.empCode as empId, employee.fullName as name, salaryMonth, paymentDate, (pf) as pf from empsalarytransactions join employee on employee.id=empsalarytransactions.empId left join officebranch on officebranch.id=empsalarytransactions.branchId where pfOpted='Yes' and  paymentDate BETWEEN '".$frmDt."' and '".$toDt."' order by branchId";
				$salarytransactions =  \DB::select(DB::raw($sql));
				foreach ($salarytransactions as $salarytransaction){
					$row = array();
					$row["branch"] = $salarytransaction->branch;
					$row["empId"] = $salarytransaction->empId;
					$row["name"] = $salarytransaction->name;
					$row["salaryMonth"] = date("M",strtotime($salarytransaction->salaryMonth)).", ".date("Y",strtotime($salarytransaction->salaryMonth));
					$row["paymentDate"] = date("d-m-Y",strtotime($salarytransaction->paymentDate));
					$row["pf"] = $salarytransaction->pf;
					$resp[] = $row;
				}
			}
			if(isset($values["typeofreport"]) && $values["typeofreport"]=="esi_report"){
				$sql = "SELECT officebranch.name as branch, employee.empCode as empId, employee.fullName as name, salaryMonth, paymentDate, (esi) as esi from empsalarytransactions join employee on employee.id=empsalarytransactions.empId left join officebranch on officebranch.id=empsalarytransactions.branchId where pfOpted='Yes' and  paymentDate BETWEEN '".$frmDt."' and '".$toDt."' order by branchId";
				$salarytransactions =  \DB::select(DB::raw($sql));
				foreach ($salarytransactions as $salarytransaction){
					$row = array();
					$row["branch"] = $salarytransaction->branch;
					$row["empId"] = $salarytransaction->empId;
					$row["name"] = $salarytransaction->name;
					$row["salaryMonth"] = date("M",strtotime($salarytransaction->salaryMonth)).", ".date("Y",strtotime($salarytransaction->salaryMonth));
					$row["paymentDate"] = date("d-m-Y",strtotime($salarytransaction->paymentDate));
					$row["pf"] = $salarytransaction->esi;
					$resp[] = $row;
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \OfficeBranch::ALL();
		$branches_arr = array();
		$branches_arr["0"] = "ALL BRANCHES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$employees =  \Employee::ALL();
		$employees_arr = array();
		$employees_arr["0"] = "ALL EMPLOYEES";
		foreach ($employees as $employee){
			$employees_arr[$employee->id] = $employee->fullName." (".$employee->empCode.")";
		}
		
		$report_type_arr = array();
		$report_type_arr["branch_wise_salary_report"] = "BRANCH WISE SALARY REPORT";
		$report_type_arr["employee_wise_salary_report"] = "EMPLOYEE WISE SALARY REPORT";
		$report_type_arr["pf_report"] = "PF REPORT";
		$report_type_arr["esi_report"] = "ESI REPORT";
		$report_type_arr["bank_payment_report"] = "BANK PAYMENT REPORT";
		$form_field = array("name"=>"typeofreport", "content"=>"type of report ", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"showSelectionType(this.value)"),  "options"=>$report_type_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paidfrombranch", "content"=>"paid from ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employee", "content"=>"employee ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$employees_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$add_form_fields = array();
		$emps =  \Employee::where("roleId","=",19)->get();
		$emps_arr = array();
		$emps_arr["0"] = "ALL DRIVERS";
		foreach ($emps as $emp){
			$emps_arr[$emp->id] = $emp->fullName;
		}
	
		$vehs =  \Vehicle::where("status","=","ACTIVE")->get();
		$vehs_arr = array();
		foreach ($vehs as $veh){
			$vehs_arr[$veh->id] = $veh->veh_reg;
		}
		$form_field = array("name"=>"fuelstation", "content"=>"by station", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"driver", "content"=>"by driver", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$emps_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"by vehicle", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$vehs_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$form_info["add_form_fields"] = $add_form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.salaryreport', array("values"=>$values));
	}
	
	private function getFuelReport($values){
		if (\Request::isMethod('post'))
		{
			if(!isset($values["fromdate"])){
				$values["fromdate"] = "10-10-2013";
			}
			if(!isset($values["todate"])){
				$values["todate"] = date("d-m-Y");
			}
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			DB::statement(DB::raw("CALL fuel_transactions_report('".$frmDt."', '".$toDt."');"));
			if($values["fuelreporttype"] == "balanceSheetNoDt" || $values["fuelreporttype"] == "balanceSheet"){
				if($values["fuelstation"] == "0"){
					$fuelstations =  \FuelStation::OrderBy("name")->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$row["totalamt"] = 0;
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_fuel_transaction` where entity='FUEL TRANSACTION' and fuelStation='".$fuelstation->name."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["totalamt"] = $rec->amt;
						}
						$row["paidamt"] = 0;
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_fuel_transaction` where fuelStation='".$fuelstation->name."' and (entity='EXPENSE TRANSACTION' or paymentPaid='Yes')"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["paidamt"] = $rec->amt;
						}
						$row["balance"] = $row["totalamt"]-$row["paidamt"];
						if($row["paidamt"] != 0  || $row["totalamt"] != 0){
							$resp[] = $row;
						}
					}
				}
				else if($values["fuelstation"] > 0){
					$fuelstations =  \FuelStation::where("id","=",$values["fuelstation"])->get();
					if(count($fuelstations)>0){
						$fuelstation = $fuelstations[0];
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$row["totalamt"] = 0;
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_fuel_transaction` where entity='FUEL TRANSACTION' and fuelStation='".$fuelstation->name."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["totalamt"] = $rec->amt;
						}
						$row["paidamt"] = 0;
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_fuel_transaction` where fuelStation='".$fuelstation->name."' and (entity='EXPENSE TRANSACTION' or paymentPaid='Yes')"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$row["paidamt"] = $rec->amt;
						}
						$row["balance"] = $row["totalamt"]-$row["paidamt"];
						if($row["paidamt"] != 0  || $row["totalamt"] != 0){
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["fuelreporttype"] == "payment"){
				if($values["fuelstation"] == "0"){
					$fuelstations =  \FuelStation::OrderBy("name")->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (paymentPaid='Yes' or entity='EXPENSE TRANSACTION') and fuelStation='".$fuelstation->name."'"));
						foreach($recs as  $rec) {
							$row["amount"] = $rec->amount;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$branchname = "";
							$b = \OfficeBranch::where("id","=",$rec->branchId)->get();
							if(count($b)>0){
								$b = $b[0];
								$branchname = $b->name;
							}
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType."<br/>"."Transaction Branch : ".$branchname;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
				else if($values["fuelstation"] > 0){
					$fuelstations =  \FuelStation::where("id","=",$values["fuelstation"])->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (paymentPaid='Yes' or entity='EXPENSE TRANSACTION') and fuelStation='".$fuelstation->name."'"));
						foreach($recs as  $rec) {
							$row["amount"] = $rec->amount;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["fuelreporttype"] == "tracking"){
				if($values["fuelstation"] == "0"){
					$fuelstations =  \FuelStation::OrderBy("name")->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (entity='FUEL TRANSACTION') and fuelStation='".$fuelstation->name."'"));
						foreach($recs as  $rec) {
							$row["vehicle"] = $rec->veh_reg;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["ltrs"] = $rec->ltrs;
							$row["amount"] = $rec->amount;
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
				else if($values["fuelstation"] > 0){
					$fuelstations =  \FuelStation::where("id","=",$values["fuelstation"])->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (entity='FUEL TRANSACTION') and fuelStation='".$fuelstation->name."'"));
						foreach($recs as  $rec) {
							$row["vehicle"] = $rec->veh_reg;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["ltrs"] = $rec->ltrs;
							$row["amount"] = $rec->amount;
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["fuelreporttype"] == "vehicleReport"){
				$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (entity='FUEL TRANSACTION') and vehicleId='".$values["vehicle"]."'"));
				foreach($recs as  $rec) {
					$row = array();
					$row["fuelstation"] = $rec->fuelStation;
					$row["vehicle"] = $rec->veh_reg;
					$row["date"] = date("d-m-Y",strtotime($rec->date));
					$row["ltrs"] = $rec->ltrs;
					$row["amount"] = $rec->amount;
					$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
					$row["remarks"] = $rec->remarks;
					$row["createdBy"] = $rec->createdBy;
					$resp[] = $row;
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \FuelStation::leftjoin("cities","cities.id","=","fuelstationdetails.cityId")->select($select_args)->get();
		$branches_arr = array();
		$branches_arr["0"] = "ALL FUEL STATIONS";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->fname." (".$branch->cname.")";
		}
	
		$fuel_rep_arr = array();
		$fuel_rep_arr['balanceSheetNoDt'] = "Fuel Station Balance Sheet";
		$fuel_rep_arr['balanceSheet'] = "Fuel Station Range Sheet";
		$fuel_rep_arr['payment'] = "Fuel Station Payments";
		$fuel_rep_arr['tracking'] = "Track By Station";
		$fuel_rep_arr['vehicleReport'] = "Track By Vehicle";
		$fuel_rep_arr['employeeReport'] = "Track By Driver";
	
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fuelreporttype", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"showSelectionType(this.value)"), "options"=>$fuel_rep_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$add_form_fields = array();
		$emps =  \Employee::where("roleId","=",19)->get();
		$emps_arr = array();
		$emps_arr["0"] = "ALL DRIVERS";
		foreach ($emps as $emp){
			$emps_arr[$emp->id] = $emp->fullName;
		}
	
		$vehs =  \Vehicle::where("status","=","ACTIVE")->get();
		$vehs_arr = array();
		foreach ($vehs as $veh){
			$vehs_arr[$veh->id] = $veh->veh_reg;
		}
		$form_field = array("name"=>"fuelstation", "content"=>"by station", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"driver", "content"=>"by driver", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$emps_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"by vehicle", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$vehs_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$form_info["add_form_fields"] = $add_form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.fuelreport', array("values"=>$values));
	}
	
	private function getDailyFinanceDetailedReport($values){
		if (\Request::isMethod('post'))
		{
			if(!isset($values["fromdate"])){
				$values["fromdate"] = "10-10-2013";
			}
			if(!isset($values["todate"])){
				$values["todate"] = date("d-m-Y");
			}
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();//'Finance Company',"Loan Amount",'Loan No', "Paid Amount", "Paid Date","Office Branch", "Created By"
			$select_args[] = "financecompanies.name as name";
			$select_args[] = "fuelstationdetails.name as fname";
			$select_args[] = "cities.name as cname";
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array("Finance Company","Loan No","Loan Amount", "Paid Amount", "Paid Date","Office Branch", "Created By");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$qry = "select df.id as id, name, amountFinanced, installmentAmount, agmtDate, paidInstallments, totalInstallments from dailyfinances df, financecompanies f where df.financeCompanyId=f.id and df.deleted='No' order by name, agmtDate asc";
				$dailyfinances = \DB::select(\DB::raw($qry));
				$entity_arr = array();
				$dfName = '';
				$i  = 0;
				$loanNo= 0;
				foreach ($dailyfinances as $dailyfinance){
					$id = $dailyfinance->id;
					$name = $dailyfinance->name;
					$amountFinanced = $dailyfinance->amountFinanced;
					$paidInstallments = $dailyfinance->paidInstallments;
					$installmentAmount = $dailyfinance->installmentAmount;
					$eqry = "select sum(amount) as paidAmount from expensetransactions where entity='DAILY FINANCE PAYMENT' and entityValue=$id and status='ACTIVE'";
					$eresults = \DB::select(\DB::raw($eqry));
					$paidAmount = 0;
					if(count($eresults)>0){
						$erow = $eresults[0];
						$paidAmount = $erow->paidAmount;
					}
					if($paidAmount+($paidInstallments*$installmentAmount) >= $amountFinanced)
						continue;
					
					if($i == 0)
					{
						$dfName = $name;
						$loanNo = 1;
					}
					else if($dfName === $name)
					{
						$loanNo++;
					}
					else
					{
						$dfName = $name;
						$loanNo = 1;
					}
					$amountFinanced=$dailyfinance->amountFinanced;
					$installmentAmount=$dailyfinance->installmentAmount;
					$finName = $name.'-'.$amountFinanced.'-'.$installmentAmount.'- Loan No'.$loanNo;
					$i++;
					$entity_arr[$id] = $finName;
				}
				$entity_name = "dailyfinance";
				$entity_text = "daily finance ";
		$form_field = array("name"=>$entity_name, "content"=>$entity_text, "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$entity_arr);
		$form_fields[] = $form_field;
	
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.dailyfinancedetailed', array("values"=>$values));
	}
	
	private function getCreditSupplierReport($values){
		if (\Request::isMethod('post'))
		{
			if(!isset($values["fromdate"])){
				$values["fromdate"] = "10-10-2013";
			}
			if(!isset($values["todate"])){
				$values["todate"] = date("d-m-Y");
			}
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			DB::statement(DB::raw("CALL credit_supplier_report('".$frmDt."', '".$toDt."');"));
			if($values["supplierreporttype"] == "balanceSheetNoDt" || $values["supplierreporttype"] == "balanceSheet"){
				if($values["creditsupplier"] == "0"){
					$creditSuppliers =  \CreditSupplier::OrderBy("supplierName")->get();
					foreach ($creditSuppliers as $creditSupplier){
						$row = array();
						$row["fuelstation"] = $creditSupplier->supplierName;
						$repair_paidamt = 0;
						$repair_unpaidamt = 0;
						$purchase_paidamt = 0;
						$purchase_unpaidamt = 0;
						$payments = 0;
						
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='repairs' and paymentPaid='Yes' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$repair_paidamt = $rec->amt;
						}
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='repairs' and paymentPaid='No' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$repair_unpaidamt = $rec->amt;
						}
						
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='purchase' and paymentPaid='Yes' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$purchase_paidamt = $rec->amt;
						}
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='purchase' and paymentPaid='No' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$purchase_unpaidamt = $rec->amt;
						}

						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='expensetransactions' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$payments = $rec->amt;
						}
						
						$supplier_balance = ($repair_paidamt-$repair_unpaidamt)+($purchase_paidamt-$purchase_unpaidamt)+($payments);
						
						if($supplier_balance != 0){
							$row["repairs"] = "<div><span style='color:red;float:right;'>UNPAID :".sprintf('%0.2f',$repair_unpaidamt)."<span><br/>";
							$row["repairs"] = $row["repairs"]."<span style='color:green;float:right;'>PAID : ".sprintf('%0.2f',$repair_paidamt)."<span></div>";
							
							$row["purchases"] = "<div><span style='color:red;float:right;'>UNPAID :".sprintf('%0.2f',$purchase_unpaidamt)."<span><br/>";
							$row["purchases"] = $row["purchases"]."<span style='color:green;float:right;'>PAID : ".sprintf('%0.2f',$purchase_paidamt)."<span></div>";
							
							$row["payments"] = "<div><span style='color:blue;float:right;'>".sprintf('%0.2f',$payments)."<span><br/>";
							$color = "color:red";
							if($supplier_balance>0){
								$color = "color:green";
							}
							$row["balance"] = "<div><span style='".$color.";float:right;'>".sprintf('%0.2f',$supplier_balance)."<span><br/>";
							
							$resp[] = $row;
						}
					}
				}
				else if($values["creditsupplier"] > 0){
					$creditSuppliers =  \CreditSupplier::where("id","=",$values["creditsupplier"])->get();
					foreach ($creditSuppliers as $creditSupplier){
						$row = array();
						$row["fuelstation"] = $creditSupplier->supplierName;
						$repair_paidamt = 0;
						$repair_unpaidamt = 0;
						$purchase_paidamt = 0;
						$purchase_unpaidamt = 0;
						$payments = 0;
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='repairs' and paymentPaid='Yes' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$repair_paidamt = $rec->amt;
						}
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='repairs' and paymentPaid='No' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$repair_unpaidamt = $rec->amt;
						}
						
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='purchase' and paymentPaid='Yes' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$purchase_paidamt = $rec->amt;
						}
						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='purchase' and paymentPaid='No' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$purchase_unpaidamt = $rec->amt;
						}

						$recs = DB::select( DB::raw("SELECT sum(amount) as amt FROM `temp_credit_supplier` where entity='expensetransactions' and creditsupplier='".$creditSupplier->supplierName."'"));
						if(count($recs)>0) {
							$rec = $recs[0];
							$payments = $rec->amt;
						}
						
						$supplier_balance = ($repair_paidamt-$repair_unpaidamt)+($purchase_paidamt-$purchase_unpaidamt)+($payments);
						
						if($supplier_balance != 0){
							$row["repairs"] = "<div><span style='color:red;float:right;'>UNPAID :".sprintf('%0.2f',$repair_unpaidamt)."<span><br/>";
							$row["repairs"] = $row["repairs"]."<span style='color:green;float:right;'>PAID : ".sprintf('%0.2f',$repair_paidamt)."<span></div>";
							
							$row["purchases"] = "<div><span style='color:red;float:right;'>UNPAID :".sprintf('%0.2f',$purchase_unpaidamt)."<span><br/>";
							$row["purchases"] = $row["purchases"]."<span style='color:green;float:right;'>PAID : ".sprintf('%0.2f',$purchase_paidamt)."<span></div>";
							
							$row["payments"] = "<div><span style='color:blue;float:right;'>".sprintf('%0.2f',$payments)."<span><br/>";
							$color = "color:red";
							if($supplier_balance>0){
								$color = "color:green";
							}
							$row["balance"] = "<div><span style='".$color.";float:right;'>".sprintf('%0.2f',$supplier_balance)."<span><br/>";
							
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["supplierreporttype"] == "payment"){
				if($values["creditsupplier"] == "0"){
					$creditSuppliers =  \CreditSupplier::OrderBy("supplierName")->get();
					foreach ($creditSuppliers as $creditSupplier){
						$row = array();
						$recs = DB::select( DB::raw("SELECT * FROM `temp_credit_supplier` where (paymentPaid='Yes' or entity='expensetransactions') and creditsupplier='".$creditSupplier->supplierName."'"));
						foreach($recs as  $rec) {
							$row["fuelstation"] = $creditSupplier->supplierName;
							$row["amount"] = $rec->amount;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
				else if($values["creditsupplier"] > 0){
					$creditSuppliers =  \CreditSupplier::where("id","=",$values["creditsupplier"])->get();
					foreach ($creditSuppliers as $creditSupplier){
						$row = array();
						$row["fuelstation"] = $creditSupplier->supplierName;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_credit_supplier` where (paymentPaid='Yes' or entity='expensetransactions') and creditsupplier='".$creditSupplier->supplierName."'"));
						foreach($recs as  $rec) {
							$row["amount"] = $rec->amount;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["fuelreporttype"] == "tracking"){
				if($values["fuelstation"] == "0"){
					$fuelstations =  \FuelStation::OrderBy("name")->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (entity='FUEL TRANSACTION') and fuelStation='".$fuelstation->name."'"));
						foreach($recs as  $rec) {
							$row["vehicle"] = $rec->veh_reg;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["ltrs"] = $rec->ltrs;
							$row["amount"] = $rec->amount;
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
				else if($values["fuelstation"] > 0){
					$fuelstations =  \FuelStation::where("id","=",$values["fuelstation"])->get();
					foreach ($fuelstations as $fuelstation){
						$row = array();
						$row["fuelstation"] = $fuelstation->name;
						$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (entity='FUEL TRANSACTION') and fuelStation='".$fuelstation->name."'"));
						foreach($recs as  $rec) {
							$row["vehicle"] = $rec->veh_reg;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["ltrs"] = $rec->ltrs;
							$row["amount"] = $rec->amount;
							$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
							$row["remarks"] = $rec->remarks;
							$row["createdBy"] = $rec->createdBy;
							$resp[] = $row;
						}
					}
				}
			}
			else if($values["fuelreporttype"] == "vehicleReport"){
				$recs = DB::select( DB::raw("SELECT * FROM `temp_fuel_transaction` where (entity='FUEL TRANSACTION') and vehicleId='".$values["vehicle"]."'"));
				foreach($recs as  $rec) {
					$row = array();
					$row["fuelstation"] = $rec->fuelStation;
					$row["vehicle"] = $rec->veh_reg;
					$row["date"] = date("d-m-Y",strtotime($rec->date));
					$row["ltrs"] = $rec->ltrs;
					$row["amount"] = $rec->amount;
					$row["info"] = "Source : ".$rec->entity."<br/>"."Payment Type : ". $rec->paymentType;
					$row["remarks"] = $rec->remarks;
					$row["createdBy"] = $rec->createdBy;
					$resp[] = $row;
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "creditsuppliers.id as id";
		$select_args[] = "creditsuppliers.supplierName as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \CreditSupplier::leftjoin("cities","cities.id","=","creditsuppliers.cityId")->select($select_args)->get();
		$branches_arr = array();
		$branches_arr["0"] = "ALL CREDIT SUPPLIERS";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->fname." (".$branch->cname.")";
		}
	
		$supplier_rep_arr = array();
		$supplier_rep_arr['balanceSheetNoDt'] = "Credit Supplier Balance Sheet";
		$supplier_rep_arr['balanceSheet'] = "Credit Supplier Range Sheet";
		$supplier_rep_arr['payment'] = "Credit Supplier Payments";
		$supplier_rep_arr['repairs'] = "Repairs";
		$supplier_rep_arr['purchase'] = "Purchases";
		$supplier_rep_arr['vehicleReport'] = "Track By Vehicle";
	
		$form_field = array("name"=>"supplierreporttype", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange","script"=>"showSelectionType(this.value)"), "options"=>$supplier_rep_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$add_form_fields = array();
		$vehs =  \Vehicle::where("status","=","ACTIVE")->get();
		$vehs_arr = array();
		foreach ($vehs as $veh){
			$vehs_arr[$veh->id] = $veh->veh_reg;
		}
		$form_field = array("name"=>"creditsupplier", "content"=>"by supplier", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"by vehicle", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$vehs_arr, "class"=>"form-control chosen-select");
		$add_form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$form_info["add_form_fields"] = $add_form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "bankdetails";
		return View::make('reports.creditsupplierreport', array("values"=>$values));
	}
	
	private function getStockPurchaseReport($values){
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Warehouse','Item Name', "Manufacturer", "Quantity", "Amouont", "Purchased Date", "Purchased From", "Incharge", "BillNo", "payment info", "comments", "Created By");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "creditsuppliers.id as id";
		$select_args[] = "creditsuppliers.supplierName as fname";
		$select_args[] = "cities.name as cname";
		
		$warehouses = \OfficeBranch::where("isWarehouse","=","Yes")->get();
		$warehouse_arr = array();
		foreach ($warehouses as $warehouse){
			$warehouse_arr[$warehouse->id] = $warehouse->name;
		}
		$form_field = array("name"=>"warehouse", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$warehouse_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "stockpurchase";
		return View::make('reports.reportsdatatable', array("values"=>$values));
	}
	
	private function getVehicleStockHistoryReport($values){
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('vehicle', 'Warehouse','Item Name', "Manufacturer", "Quantity", "Amouont", "Transaction Date", "Purchased Date", "Purchased From", "BillNo", "payment info", "comments", "Created By");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "creditsuppliers.id as id";
		$select_args[] = "creditsuppliers.supplierName as fname";
		$select_args[] = "cities.name as cname";
	
		$vehicles = \Vehicle::All();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
		}
		$form_field = array("name"=>"vehicle", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$vehicles_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "vehiclestockhistory";
		return View::make('reports.reportsdatatable', array("values"=>$values));
	}
	
	private function getRepairStockReport($values){
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Warehouse','Item Name', "Manufacturer", "Quantity", "Amouont", "Transaction Date",  "Repair To", "Incharge", "BillNo", "payment info", "comments", "Created By");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "creditsuppliers.id as id";
		$select_args[] = "creditsuppliers.supplierName as fname";
		$select_args[] = "cities.name as cname";
	
		$suppliers = \CreditSupplier::All();
		$suppliers_arr = array();
		foreach ($suppliers as $supplier){
			$suppliers_arr[$supplier->id] = $supplier->supplierName;
		}
		$items = \Items::All();
		$items_arr = array();
		$items_arr[0] = "All";
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$form_field = array("name"=>"creditsupplier", "content"=>"report for ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$suppliers_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"item", "content"=>"item ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$items_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "repairstock";
		return View::make('reports.reportsdatatable', array("values"=>$values));
	}
	
	private function getInventoryReport($values){
		if (\Request::isMethod('post'))
		{
			$frmDt = date("Y-m-d", strtotime($values["fromdate"]));
			$toDt = date("Y-m-d", strtotime($values["todate"]));
			$resp = array();
			$select_args = array();
			$select_args[] = "officebranch.name as officeBranchId";
			$select_args[] = "items.name as item";
			$select_args[] = "manufactures.name as manufacturer";
			$select_args[] = "purchased_items.qty as qty";
			$select_args[] = "purchased_items.purchasedQty as purchasedQty";
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
			if(isset($values["inventoryreporttype"])){
				if($values["inventoryreporttype"] == "find_available_items" ){
					$query = \PurchasedItems::where("purchased_items.status","=","ACTIVE")
								->where("purchase_orders.type","=","PURCHASE ORDER");
					if($values["warehouse"]>0 && ($values["item"]==0 || $values["item"]=="")){
						$query->where("purchase_orders.officeBranchId","=",$values["warehouse"]);
					}
					if($values["warehouse"]>0 && $values["item"]>0){
						$query->where("purchase_orders.officeBranchId","=",$values["warehouse"]);
						$query->where("items.id","=",$values["item"]);
					}
					if($values["warehouse"]==0 && $values["item"]>0){
						$query->where("items.id","=",$values["item"]);
					}
					$query->join("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
						->join("items","items.id","=","purchased_items.itemId")
						->join("manufactures","manufactures.id","=","purchased_items.manufacturerId")
						->join("officebranch","officebranch.id","=","purchase_orders.officeBranchId")
						->join("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
						->leftjoin("employee","employee.id","=","purchase_orders.createdBy")
						->leftjoin("employee as employee1","employee1.id","=","purchase_orders.inchargeId");
					$entities = $query->select($select_args)->orderBy("purchase_orders.orderDate","desc")->get();
					foreach ($entities as $entity){
						$row = array();
						$row["officeBranchId"] = $entity->officeBranchId;
						$row["item"] = $entity->item;
						$row["qty"] = $entity->qty;
						$row["manufacturer"] = $entity->manufacturer;
						$row["orderDate"] = date("d-m-Y",strtotime($entity->orderDate));
						$row["orderqty"] = $entity->purchasedQty;
						$row["billNumber"] = $entity->billNumber;
						if($entity->filePath != ""){
							$row["billNumber"] = "<a target='_blank' href='../app/storage/uploads/".$entity->filePath."'>".$entity->billNumber."</a>";
						}
						$row["creditSupplierId"] = $entity->creditSupplierId;
						$resp[] = $row;
					}
				}
				if(isset($values["inventoryreporttype"])){
					$select_args = array();
					$select_args[] = "officebranch.name as officebranch";
					$select_args[] = "items.name as item";
					$select_args[] = "manufactures.name as manufacturer";
					$select_args[] = "inventory_transaction.qty as qty";
					$select_args[] = "purchased_items.purchasedQty as purchasedQty";
					$select_args[] = "inventory_transaction.date as transactionDate";
					$select_args[] = "inventory_transaction.action as transactiontype";
					$select_args[] = "inventory_transaction.fromWareHouseId as fromWareHouseId";
					$select_args[] = "officebranch1.name as toWareHouseId";
					$select_args[] = "vehicle1.veh_reg as veh_reg1";
					$select_args[] = "inventory_transaction.fromActionId as fromActionId";
					$select_args[] = "inventory_transaction.toActionId as toActionId";
					$select_args[] = "inventory_transaction.remarks as remarks";
					$select_args[] = "purchase_orders.orderDate as orderDate";
					$select_args[] = "creditsuppliers.suppliername as creditSupplierId";
					$select_args[] = "purchase_orders.billNumber as billNumber";
					$select_args[] = "purchase_orders.id as id";
					$select_args[] = "purchase_orders.amountPaid as amountPaid";
					$select_args[] = "purchase_orders.paymentType as paymentType";
					$select_args[] = "employee.fullName as receivedBy";
					$select_args[] = "purchased_items.unitPrice as unitPrice";
					$select_args[] = "purchase_orders.filePath as filePath";
					$select_args[] = "vehicle.veh_reg as veh_reg";
					if($values["inventoryreporttype"] == "history" ){
						$fromdt = date("Y-m-d",strtotime($values['fromdate']));
						$todt = date("Y-m-d",strtotime($values['todate']));
						$query = \InventoryTransactions::where("inventory_transaction.status","=","ACTIVE")
							->whereBetween("inventory_transaction.date",array($fromdt,$todt));
						if($values["warehouse"]>0){
							$query->where("officebranch.id","=",$values["warehouse"]);
						}
						if($values["item"]>0){
							$query->where("items.id","=",$values["item"]);
						}
						$query->leftjoin("purchased_items","purchased_items.id","=","inventory_transaction.stockItemId")
							->leftjoin("purchase_orders","purchase_orders.id","=","purchased_items.purchasedOrderId")
							->leftjoin("items","items.id","=","purchased_items.itemId")
							->leftjoin("vehicle","vehicle.id","=","inventory_transaction.toVehicleId")
							->leftjoin("vehicle as vehicle1","vehicle1.id","=","inventory_transaction.fromVehicleId")
							->leftjoin("manufactures","manufactures.id","=","purchased_items.manufacturerId")
							->leftjoin("officebranch","officebranch.id","=","inventory_transaction.fromWareHouseId")
							->leftjoin("officebranch as officebranch1","officebranch1.id","=","inventory_transaction.toWareHouseId")
							->leftjoin("creditsuppliers","creditsuppliers.id","=","purchase_orders.creditSupplierId")
							->leftjoin("employee","employee.id","=","purchase_orders.createdBy");
						$entities = $query->select($select_args)->orderBy("inventory_transaction.date","desc")->get();
		
						foreach ($entities as $entity){
							$row = array();
							$row["officeBranchId"] = $entity->officebranch;
							$row["item"] = $entity->item;
							$row["qty"] = $entity->qty;
							$row["manufacturer"] = $entity->manufacturer;
							$row["transactionDate"] = date("d-m-Y",strtotime($entity->transactionDate));
							$transdetails = "";
							if($entity->transactiontype=="itemtovehicles"){
								$transdetails = $transdetails."Transaction Type : Items<br/> To Vehicle : ".$entity->veh_reg;
							}
							if($entity->transactiontype=="vehicletovehicle"){
								$transdetails = $transdetails."Transaction Type : Vehicle To Vehicle <br/>".$entity->veh_reg1." To ". $entity->veh_reg;
							}
							if($entity->transactiontype=="warehousetowarehouse"){
								$transdetails = $transdetails."Transaction Type : Warehouse To Warehouse <br/>".$entity->officebranch." To ". $entity->toWareHouseId;
							}
							$row["transinfo"] = $transdetails;
							$row["orderDate"] = date("d-m-Y",strtotime($entity->orderDate));
							$row["orderqty"] = $entity->purchasedQty;
							$row["creditSupplierId"] = $entity->creditSupplierId;
							$row["billNumber"] = $entity->billNumber;
							if($entity->filePath != ""){
								$row["billNumber"] = "<a target='_blank' href='../app/storage/uploads/".$entity->filePath."'>".$entity->veh_reg."</a>";
							}
							$row["remarks"] = $entity->remarks;
							$resp[] = $row;
							
						}
					}
				}
			}
			echo json_encode($resp);
			return;
		}
	
		$values['bredcum'] = strtoupper($values["reporttype"]);
		$values['home_url'] = 'masters';
		$values['add_url'] = 'getreport';
		$values['form_action'] = 'getreport';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		$form_info["reporttype"] = $values["reporttype"];
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "fuelstationdetails.id as id";
		$select_args[] = "fuelstationdetails.name as fname";
		$select_args[] = "cities.name as cname";
	
		$branches =  \OfficeBranch::where("isWarehouse","=","Yes")->get();
		$branches_arr = array();
		$branches_arr["0"] = "ALL WAREHOUSES";
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$items = \Items::All();
		$items_arr = array();
		$items_arr[0] = "All";
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$form_field = array("name"=>"warehouse", "content"=>"warehouse ", "readonly"=>"",  "required"=>"required","type"=>"select",  "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"item", "content"=>"item ", "readonly"=>"",  "required"=>"","type"=>"select",  "options"=>$items_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"inventoryreporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$values["provider"] = "dailysettlement";
		return View::make('reports.inventoryreport', array("values"=>$values));
	}
	
	private function getLoginLogInfo($values)
	{
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$select_args = array();
			$select_args[] = "login_log.user_full_name as name";
			$select_args[] = "login_log.emailId as emailId";
			$select_args[] = "login_log.ipaddress as ipaddress";
			$select_args[] = "login_log.logindate as logindate";
			$select_args[] = "login_log.logintime as logintime";
			
			if(!isset($values["fromdate"]) || !isset($values["todate"])){
				echo json_encode(array("total"=>0, "data"=>array()));
				return ;
			}
			
			$frmdt = date("Y-m-d",strtotime($values["fromdate"]));
			$todt = date("Y-m-d",strtotime($values["todate"]));
			$resp = array();
			if(isset($values["empname"]) && $values["empname"] == 0){
				$entities = \LoginLog::whereBetween("logindate",array($frmdt,$todt))->get();
				$total = \LoginLog::wherebetween("logindate",array($frmdt,$todt))->count();
			}
			elseif (isset($values["empname"]) && $values["empname"] > 0){
				$entities = \LoginLog::wherebetween("logindate",array($frmdt,$todt))->where("user_id","=",$values["empname"])->select($select_args)->sget();
				$total = \LoginLog::wherebetween("logindate",array($frmdt,$todt))->where("user_id","=",$values["empname"])->count();
			}
			foreach ($entities as $entity){
				$row = array();
				$row["empid"] = $entity->empid	;
				$row["user_full_name"] = $entity->user_full_name;
				$row["ipaddress"] = $entity->ipaddress;
				$row["logindate"] = date("d-m-Y",strtotime($entity->logindate));
				$row["logintime"] = $entity->logintime;
				$row["logouttime"] = $entity->logouttime;
				$resp[] = $row;
			}
			echo json_encode($resp);
			return;
			
		}
		$values = Input::all();
		$values['bredcum'] = "USER LOGIN INFORMATION";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'loginlog';
		$values['form_action'] = 'loginlog';
		$values['action_val'] = '#';
		$theads = array('user name','email', "IP Address", "login date", "login time", "logout time");
		$values["theads"] = $theads;
	
		//$values["test"];
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "users";
		$form_info["bredcum"] = "loginlog";
		$form_info["reporttype"] = $values["reporttype"];
	
	
		$emp_arr = array();
		$emp_arr[0] = "All";
		$emps = \Employee::where("status","=","ACTIVE")->orderby("fullName")->get();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName;
		}
	
		$form_fields = array();
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"empname", "content"=>"empname", "readonly"=>"", "required"=>"", "type"=>"select", "options"=>$emp_arr,  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$form_info["form_fields"] = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		//$values['provider'] = "loginlog";
	
		return View::make('reports.logininforeport', array("values"=>$values));
	}
	
	private function getVehicleMileage($values)
	{
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$select_args = array();
			$select_args[] = "vehicle.veh_reg as veh_reg";
			$select_args[] = "lookuptypevalues.name as vehicle_type";
			$select_args[] = "vehicle.yearof_pur as yearof_pur";
			$select_args[] = "fueltransactions.filledDate as startDate";
			$select_args[] = "fueltransactions.filledDate as endDate";
			$select_args[] = "fueltransactions.startReading as startReading";
			$select_args[] = "fueltransactions.litres as litres";
			$select_args[] = "vehicle.vehicle_type as mileage";
			$select_args[] = "fueltransactions.remarks as remarks";
				
			if(!isset($values["fromdate"]) || !isset($values["todate"])){
				echo json_encode(array("total"=>0, "data"=>array()));
				return ;
			}
				
			$frmdt = date("Y-m-d",strtotime($values["fromdate"]));
			$todt = date("Y-m-d",strtotime($values["todate"]));
			$resp = array();
				
			$entities = \Depot::whereBetween("filledDate",array($frmdt,$todt))
						->where("depots.id",$values["depot"])
						->leftjoin("contracts","contracts.depotId","=","depots.id")
						->leftjoin("fueltransactions","fueltransactions.contractId","=","contracts.id")
						->leftjoin("vehicle","vehicle.id","=","fueltransactions.vehicleId")
						->leftjoin("lookuptypevalues","lookuptypevalues.id","=","vehicle.vehicle_type")
						->select($select_args)->orderBy("filledDate","asc")->orderBy("fueltransactions.vehicleId","asc")->get();
			$total = \Depot::whereBetween("filledDate",array($frmdt,$todt))
						->where("depots.id",$values["depot"])
						->leftjoin("contracts","contracts.depotId","=","depots.id")
						->leftjoin("fueltransactions","fueltransactions.contractId","=","contracts.id")
						->count();
			
			$i=0;
			$veh = $entities[0]->veh_reg;
			for($i=0; $i<count($entities)-1; $i++){
				$row = array();
				if($veh == $entities[$i+1]->veh_reg){
					$row["veh_reg"] = $entities[$i]->veh_reg;
					$row["vehicle_type"] = $entities[$i]->vehicle_type;
					$row["yearof_pur"] = date("d-m-Y",strtotime($entities[$i]->yearof_pur));
					$row["startDate"] = date("d-m-Y",strtotime($entities[$i]->startDate));
					$row["endDate"] = date("d-m-Y",strtotime($entities[$i+1]->endDate));
					$row["distance"] = $entities[$i]->startReading-$entities[$i+1]->startReading;
					$row["litres"] = $entities[$i]->litres;
					$row["mileage"] = round(($entities[$i]->startReading-$entities[$i+1]->startReading)/$entities[$i]->litres, 2);
					$row["remarks"] = $entities[$i]->remarks." ";//.$entities[$i]->startReading;
				}
				else{
					$veh = $entities[$i+1]->veh_reg;
					$row["veh_reg"] = $entities[$i]->veh_reg;
					$row["vehicle_type"] = $entities[$i]->vehicle_type;
					$row["yearof_pur"] = date("d-m-Y",strtotime($entities[$i]->yearof_pur));
					$row["startDate"] = date("d-m-Y",strtotime($entities[$i]->startDate));
					$row["endDate"] = date("d-m-Y",strtotime($entities[$i]->endDate));
					$row["distance"] = $entities[$i]->startReading-$entities[$i]->startReading;
					$row["litres"] = $entities[$i]->litres;
					$row["mileage"] = round(($entities[$i]->startReading-$entities[$i]->startReading)/$entities[$i]->litres, 2);
					$row["remarks"] = $entities[$i]->remarks;//." ".$entities[$i]->startReading;
				}
				$resp[] = $row;
			}
			if(count($entities)>0){
				$row["veh_reg"] = $entities[$i]->veh_reg;
				$row["vehicle_type"] = $entities[$i]->vehicle_type;
				$row["yearof_pur"] = date("d-m-Y",strtotime($entities[$i]->yearof_pur));
				$row["startDate"] = date("d-m-Y",strtotime($entities[$i]->startDate));
				$row["endDate"] = date("d-m-Y",strtotime($entities[$i]->endDate));
				$row["distance"] = $entities[$i]->startReading-$entities[$i]->startReading;
				$row["litres"] = $entities[$i]->litres;
				$row["mileage"] = round(($entities[$i]->startReading-$entities[$i]->startReading)/$entities[$i]->litres, 2);
				$row["remarks"] = $entities[$i]->remarks;//." ".$entities[$i]->startReading;
				$resp[] = $row;
			}
			echo json_encode($resp);
			return;
				
		}
		$values['bredcum'] = "VEHICLE MILEAGE REPORT";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'loginlog';
		$values['form_action'] = 'loginlog';
		$values['action_val'] = '#';
		$theads = array('vehicle no','Vehicle Type', "Year of purchase", "start date", "end date", "total distance", "total fuel", "mileage", "remarks");
		$values["theads"] = $theads;
	
		//$values["test"];
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "users";
		$form_info["bredcum"] = "VEHILE MILEAGE REPORT";
		$form_info["reporttype"] = $values["reporttype"];
	
	
		$emp_arr = array();
		$emp_arr[0] = "All";
		$emps = \Employee::where("status","=","ACTIVE")->orderby("fullName")->get();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName;
		}
		
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
	
		$form_fields = array();
		$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$form_info["form_fields"] = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		//$values['provider'] = "loginlog";
	
		return View::make('reports.vehiclemileagereport', array("values"=>$values));
	}
	
	private function getVehiclePerformance($values)
	{
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$select_args = array();
	
			if(!isset($values["fromdate"]) || !isset($values["todate"])){
				echo json_encode(array("total"=>0, "data"=>array()));
				return ;
			}
	
			$frmDt = date("Y-m-d",strtotime($values["fromdate"]));
			$toDt = date("Y-m-d",strtotime($values["todate"]));
			$resp = array();
			$resp_obj = array();
			DB::statement(DB::raw("CALL vehicle_performance_report('".$frmDt."', '".$toDt."');"));
			$veh_arr = array();
			if($values["vehicle"]==0){
				$con_vehs = \Contract::where("contracts.depotId","=",$values["depot"])
									->where("contract_vehicles.status","=","ACTIVE")
									->join("contract_vehicles","contracts.id","=","contract_vehicles.contractId")
									->select(array("contract_vehicles.vehicleId as vehicleId"))->get();
				foreach ($con_vehs as $con_veh){
					$veh_arr[] = $con_veh->vehicleId; 
				}
			}
			else{
				$veh_arr[] = $values["vehicle"];
			}
			
			$branches = \OfficeBranch::All();
			$branches_arr = array();
			foreach ($branches as $branch){
				$branches_arr[$branch->id] = $branch->name;
			}
			
			$recs = DB::select( DB::raw("SELECT * FROM `temp_vehicle_performance` order by date desc"));
			$income_arr = array();
			$expense_arr = array();
			
			$all_veh_arr = array();
			$all_vehs = \Vehicle::All();
			foreach ($all_vehs as $all_veh){
				$all_veh_arr[$all_veh->id] = $all_veh->veh_reg;
			}
			$expense_sum = 0;
			$fuel_sum = 0;
			$stock_sum = 0;
			$repair_sum = 0;
			
			$repairs_veh_summery = array();
			$repairs_veh_summery_amt = 0;
			$repairs_veh_summery_veh = "";
			
			foreach($recs as  $rec) {
				$row = array();
				if($rec->type == "REPAIR TRANSACTION"){
					$veh_arr_lc = explode(",", $rec->name);
					foreach ($veh_arr_lc as $veh){
						if(in_array($veh, $veh_arr)){
							$row = array();
							$row["veh_reg"] = $all_veh_arr[$veh];
							$row["branch"] = "";
							if($rec->branchId != 0){
								$row["branch"] = $branches_arr[$rec->branchId];
							}
							$row["type"] = $rec->type;
							$row["purpose"] = $rec->entity;
							$row["date"] = date("d-m-Y",strtotime($rec->date));
							$row["transinfo"] = "Credit Supplier : ".$rec->entityValue."<br/>Bill No. - ".$rec->billNo;
							$row["amount"] = ($rec->amount)/(count($veh_arr_lc)-1);
							$repair_sum = $repair_sum+(($rec->amount)/(count($veh_arr_lc)-1));
							$row["remarks"] = $rec->remarks;
							$expense_arr[] = $row;
						}
					}
				}
				if(in_array($rec->vehicleId, $veh_arr)){
					if($rec->type == "income"){
						$row["veh_reg"] = $rec->entity;
						$row["branch"] = $branches_arr[$rec->branchId];
						$row["type"] = $rec->name;
						$row["date"] = date("d-m-Y",strtotime($rec->date));
						$row["transinfo"] = "Bill No. - ".$rec->billNo;
						$row["amount"] = $rec->amount;
						$row["remarks"] = $rec->remarks;
						$income_arr[] = $row;
					}
					else{
						$row["veh_reg"] = $rec->entity;
						$row["branch"] = "";
						if($rec->branchId != 0){
							$row["branch"] = $branches_arr[$rec->branchId];
						}
						$row["type"] = $rec->type;
						
						if($rec->type=="expense"){
							$expense_sum = $expense_sum+$rec->amount;
						}
						
						if($rec->type=="STOCK TRANSACTION"){
							$stock_sum = $stock_sum+$rec->amount;
						}
						
						$row["purpose"] = $rec->name;
						$row["date"] = date("d-m-Y",strtotime($rec->date));
						$row["transinfo"] = "";
						if($rec->name ==  "FUEL"){
							$row["transinfo"] = "Fuel Station : ".$rec->entityValue."<br/>Bill No. - ".$rec->billNo;
							$fuel_sum = $fuel_sum+$rec->amount;
						}
						$row["amount"] = $rec->amount;
						$row["remarks"] = $rec->remarks;
						$expense_arr[] = $row;
					}
				}
			}
			$resp_obj["incomes"] = $income_arr;
			$resp_obj["expenses"] = $expense_arr;
			$resp_obj["expenses_summary"] = array(array("emp_salary"=>"0.0"),array("fuel"=>sprintf('%0.2f',$fuel_sum)),array("repairs"=>sprintf('%0.2f',$repair_sum)),array("stock"=>sprintf('%0.2f',$stock_sum)),array("others"=>sprintf('%0.2f',$expense_sum)));
			
			
			$recs = DB::select( DB::raw("SELECT * FROM `temp_vehicle_performance`"));
			$summary_by_vehicle = array();
			foreach($veh_arr as  $veh_rec) {
				$expense_sum = 0;
				$fuel_sum = 0;
				$stock_sum = 0;
				$repair_sum = 0;
				foreach($recs as  $rec) {
					$row = array();
					if($rec->type == "REPAIR TRANSACTION"){
						$veh_arr_lc = explode(",", $rec->name);
						foreach ($veh_arr_lc as $veh){
							if($veh == $veh_rec){
								$repair_sum = $repair_sum+(($rec->amount)/(count($veh_arr_lc)-1));
							}
						}
					}
					else{
						if($veh_rec == $rec->vehicleId){
							if($rec->type == "expense"){
								$expense_sum = $expense_sum+$rec->amount;
							}
							if($rec->type == "FUEL"){
								$fuel_sum = $fuel_sum+$rec->amount;
							}
							if($rec->type == "STOCK TRANSACTION"){
								$stock_sum = $stock_sum+$rec->amount;
							}
						}
					}
				}
				$row = array();
				$row["vehicle"] = $all_veh_arr[$veh_rec];
				$row["fuel"] = $fuel_sum;
				$row["repair"] = $repair_sum;
				$row["purchases"] = "0.0";
				$row["stock"] = $stock_sum;
				$row["salaries"] = "0.0";
				$row["expense"] = $expense_sum;
				$summary_by_vehicle[] = $row;
			}
			
			$resp_obj["summary_by_vehicle"] = $summary_by_vehicle;
			echo json_encode($resp_obj);
			return;
	
		}
		$values['bredcum'] = "VEHICLE PERFORMANCE REPORT";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'loginlog';
		$values['form_action'] = 'loginlog';
		$values['action_val'] = '#';
		$theads = array('VEHICLE NO', 'BRANCH', "INCOME TYPE", "DATE", "TRANSACTION INFO", "AMOUNT",  "REMARKS");
		$values["theads"] = $theads;
		$theads = array('VEHICLE NO', 'BRANCH', "EXPENSES TYPE", 'PURPOSE', "DATE", "TRANSACTION INFO", "AMOUNT",  "REMARKS");
		$values["theads1"] = $theads;
		$theads = array('EXPENSES TYPE', "TOTAL AMOUNT");
		$values["theads2"] = $theads;
		$theads = array('VEHICLE NO','FUEL EXPENSE', "REPAIR EXPENSES", "PURCHASE EXPENSES", "STOCK EXPENSES", "SALARIES", "VEHICLE EXPENSES");
		$values["theads3"] = $theads;
	
		//$values["test"];
	
		$form_info = array();
		$form_info["name"] = "getreport";
		$form_info["action"] = "getreport";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "users";
		$form_info["bredcum"] = "VEHILE PERFORMANCE REPORT";
		$form_info["reporttype"] = $values["reporttype"];
	
	
		$emp_arr = array();
		$emp_arr[0] = "All";
		$emps = \Employee::where("status","=","ACTIVE")->orderby("fullName")->get();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName;
		}
	
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		//$clients_arr[0] = "All";
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
	
		$form_fields = array();
		$form_field = array("name"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"daterange", "content"=>"date range", "readonly"=>"",  "required"=>"required","type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"reporttype", "value"=>$values["reporttype"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$form_info["form_fields"] = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		//$values['provider'] = "loginlog";
	
		return View::make('reports.vehicleperformancereport', array("values"=>$values));
	}
}
