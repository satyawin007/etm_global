<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use settings\AppSettingsController;
class LoanPaymentsController extends \Controller {


	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageLoanPayments()
	{
		$values = Input::all();
		$values['bredcum'] = "LOAN PAYMENTS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;

		$form_info = array();
		$form_info["name"] = "loanpayments";
		$form_info["action"] = "loanpayments";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "loan payments";
		
		$form_fields = array();
		$branches = AppSettingsController::getEmpBranches();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch["id"]] = $branch["name"];
		}
		
		$month_arr = array();
		$month_arr[date('Y', strtotime('-1 year'))."-12-01"] = 'Dec '.date('Y', strtotime('-1 year'));
		$month_arr[date('Y')."-01-01"] = 'Jan '.date('Y');
		$month_arr[date('Y')."-02-01"] = 'Feb '.date('Y');
		$month_arr[date('Y')."-03-01"] = 'March '.date('Y');
		$month_arr[date('Y')."-04-01"] = 'April '.date('Y');
		$month_arr[date('Y')."-05-01"] = 'May '.date('Y');
		$month_arr[date('Y')."-06-01"] = 'June '.date('Y');
		$month_arr[date('Y')."-07-01"] = 'July'.date('Y');
		$month_arr[date('Y')."-08-01"] = 'Aug '.date('Y');
		$month_arr[date('Y')."-09-01"] = 'Sep '.date('Y');
		$month_arr[date('Y')."-10-01"] = 'Oct '.date('Y');
		$month_arr[date('Y')."-11-01"] = 'Nov '.date('Y');
		$month_arr[date('Y')."-12-01"] = 'Dec '.date('Y');
		
		$date_val = "";     $month_val = "";   $pmtdate_val = "";      $pmttype_val = "cash";
		$branch_val = ""; $expensetype_val = "";   $bankaccount_val = "";  $chequenumber_val = ""; 
		$loan_val = "";   $todate_val = "";  $billno_val = "";   $typeofloan_val = ""; $incharge_val="0";
		$enableincharge_val = "NO";
		$values["show"] = "true";
		if(isset($values["show"])){
			$show_val = $values["show"];
		}
		if(isset($values["date"])){
			$date_val = $values["date"];
		}
		if(isset($values["branch"])){
			$branch_val = $values["branch"];
		}
		if(isset($values["billno"])){
			$billno_val = $values["billno"];
		}
		if(isset($values["entity_date"])){
			$month_val = $values["entity_date"];
		}
		if(isset($values["paymentdate"])){
			$pmtdate_val = $values["paymentdate"];
		}
		if(isset($values["paymenttype"])){
			$pmttype_val = $values["paymenttype"];
		}
		if(isset($values["bankaccount"])){
			$bankaccount_val = $values["bankaccount"];
		}
		if(isset($values["chequenumber"])){
			$chequenumber_val = $values["chequenumber"];
		}
		if(isset($values["expensetype"])){
			$expensetype_val = $values["expensetype"];
		}
		if(isset($values["todate"])){
			$todate_val = $values["todate"];
		}
		if(isset($values["financecompany"])){
			$loan_val = $values["financecompany"];
		}
		if(isset($values["enableincharge"])){
			$enableincharge_val = $values["enableincharge"];
		}
		if(isset($values["incharge"])){
			$incharge_val = $values["incharge"];
		}
		if(isset($values["typeofloan"])){
			$typeofloan_val = $values["typeofloan"];
		}
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
								->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		
		$entities =  \Loan::leftJoin("financecompanies","financecompanies.id","=","loans.financeCompanyId")
							->where("loans.status","=","ACTIVE")
							->select(array("financecompanies.name as finName","financecompanies.id as id"))
							->groupBy("financecompanies.name")->get();
		$entity_arr = array();
		foreach ($entities as $entity){
			$entity_arr[$entity->id] = $entity->finName;
		}
		
		$parentId = \LookupTypeValues::where("name", "=", "LOAN PURPOSE")->get();
		$loantypes = array();
		$loantypes_arr = array();
		if(count($parentId)>0){
			$parentId = $parentId[0];
			$parentId = $parentId->id;
			$loantypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		
		}
		foreach ($loantypes as $loantype){
			$loantypes_arr[$loantype->name] = $loantype->name;
		}
		
		$form_field = array("name"=>"branch", "value"=>$branch_val, "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "value"=>$date_val, "content"=>"date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"entity_date", "value"=>$month_val, "content"=>"for month", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$month_arr); 
		$form_fields[] = $form_field;
		$form_field = array("name"=>"expensetype", "value"=>$expensetype_val, "content"=>"expense type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("loanpayment"=>"LOAN PAYMENT", "loaninterestpayment"=>"LOAN INTEREST PAYMENT", "late_fee_charges"=>"LATE FEE CHARGES"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"financecompany", "value"=>$loan_val, "content"=>"finance company", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$entity_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"typeofloan", "value"=>$typeofloan_val, "content"=>"type of loan", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$loantypes_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "id"=>"enableincharge","content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("NO"=>"NO","YES"=>"YES"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "id"=>"incharge", "value"=>$incharge_val, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"inchargebalance", "value"=>"",  "content"=>"Incharge balance", "value"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>$pmttype_val, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array(""=>"NOT PAID", "cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		return View::make('transactions.loanpaymentsdatatable', array("values"=>$values));
	}
	
		
	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addLoanPayments()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["sdf"];
			if(!isset($values["ids"])){
				$values["ids"] = array();
			}
			$ids = $values["ids"];
			$i = 0;
			foreach ($ids as $id){
				if($id == -1)
					unset($ids[$i]);
				$i++;
			}
			$message = "The following Loan Numbers added successfully : <br/><b>";
			$url = "loanpayments";

			foreach ($ids as $id){
				$id = $id%$values["dynamic-table_length"];
				$field_names = array("amount"=>"amount",
									 "vehid"=>"entityValue",									 
									 "remarks"=>"remarks");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						$fields[$val] = $values[$key][$id];
					}
				}
				$field_names = array("branch"=>"branchId",
									"incharge"=>"inchargeId",
									"entity_date"=>"entityDate",
									"date"=>"date",
									"paymenttype"=>"paymentType",
									"bankaccount"=>"bankAccount",
									"chequenumber"=>"chequeNumber",
									"bankname"=>"bankName",
									"accountnumber"=>"accountNumber",
									"issuedate"=>"issueDate",
									"transactiondate"=>"transactionDate");
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "date" || $key == "entity_date"  || $key == "issuedate" || $key == "transactiondate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else {
							$fields[$val] = $values[$key];
						}
					}
				} 
				$fields["name"] = "expense";
				if(isset($values["expensetype"]) && $values["expensetype"]=="loanpayment"){
					$fields["entity"] = "LOAN PAYMENT";
					$fields["lookupValueId"] = "147";					
				}
				else if(isset($values["expensetype"]) && $values["expensetype"]=="late_fee_charges"){
					$fields["entity"] = "LATE FEE/OTHER CHARGES";
					$fields["lookupValueId"] = "355";
				}
				else if(isset($values["expensetype"]) && $values["expensetype"]=="loaninterestpayment"){
					$fields["entity"] = "LOAN INTEREST PAYMENT";
					$fields["lookupValueId"] = "336";
				}
				$db_functions_ctrl = new DBFunctionsController();
				
				$transid =  strtoupper(uniqid().mt_rand(100,999));
				$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
				foreach($chars as $k=>$v){
					$transid = str_replace($k, $v, $transid);
				}
				$fields["transactionId"] = $transid;
				$fields["source"] = "loan payments";
				
				$table = "ExpenseTransaction";
				\DB::beginTransaction();
				$recid = "";
				try{
					$recid = $db_functions_ctrl->insert($table, $fields);
					if(isset($values["incharge"]) && $values["incharge"]>0){
						$incharge_acct = \InchargeAccounts::where("empid","=",$values["incharge"])->first();
						$balance_amount = $incharge_acct->balance;
						$balance_amount = $balance_amount-$fields["amount"];
						\InchargeAccounts::where("empid","=",$values["incharge"])->update(array("balance"=>$balance_amount));
					}
					$message = $message.$values["loanno"][$id].", ";
				}
				catch(\Exception $ex){
					\Session::put("message","Add Loan Payment : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}				
				\DB::commit();
			}
			$message= $message."</b>";
			\Session::put("message",$message);
			return \Redirect::to($url);
		}
	}
	
		
	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function editClientIncome()
	{
		if (\Request::isMethod('get'))
		{
			$values = Input::all();
			$field_names = array("vehid"=>"vehicleId",
								 "depotid"=>"depotId", 
								 "veh_gross"=>"gross",
								 "veh_tds"=>"tds",
								 "veh_emi"=>"emi",
								 "veh_stopped"=>"stopped", 
								 "veh_otherincome"=>"otherIncome", 
								 "veh_otherdeductions"=>"otherDeductions",
								 "veh_netamount"=>"netAmount",
								 "veh_clientamount"=>"clientAmount",
								 "veh_remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$field_names = array("month"=>"month",
								"clientname"=>"clientId",
								"paymentdate"=>"paymentDate",
								"billdate"=>"billDate",
								"billno"=>"billNo",
								"paymenttype"=>"paymentType",
								"bankaccount"=>"bankAccount",
								"chequenumber"=>"chequeNumber",
								"bankname"=>"bankName",
								"accountnumber"=>"accountNumber",
								"issuedate"=>"issueDate",
								"transactiondate"=>"transactionDate",
								"tdspercentage"=>"tdsPercentage");
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "paymentdate" || $key == "billdate"  || $key == "issuedate" || $key == "transactiondate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ClientIncome";
			\DB::beginTransaction();
			try{
				$recid = $db_functions_ctrl->update($table, $fields, array("id"=>$values["rid"]));
			}
			catch(\Exception $ex){
				\DB::rollback();
				echo "fail";
				return;
			}				
			\DB::commit();
			echo "success";
		}
	}
	
	public function getEmpSalary(){
		$values = Input::all();
		$empid = $values["eid"];
		$salaryMonth = $values["dt"];
		$noOfDays = date("t", strtotime($salaryMonth)) -1;
		$startDate = $salaryMonth;
		$endDate =  date('Y-m-d', strtotime($salaryMonth.'+ '.$noOfDays.' days'));
		$jsondata = array();
		
		$data = "0";
		$recs = DB::select( DB::raw("SELECT SUM(`amount`) amt FROM `empdueamount` WHERE empId = ".$empid." and deleted='No'") );
		foreach ($recs as $rec){
			$data = "&nbsp;&nbsp;<b>".$rec->amt."</b>";
			if($rec->amt == ""){
				$data = "0.00";
			}
		}
		$total_days = 31;
		$table_data = "";
		$table_data = "<td>".$total_days."</td>";
		$table_data = "<td>".$data."</td>";
		$table_data = "<td>".$leave_amt."</td>";
		$jsondata["due"] = $data;
		
		\DB::statement(DB::raw('CALL calc_daily_trip_salary_info('.$empid.",'".$startDate."','".$endDate."');"));
		$recs = DB::table('temp_dailytripsalary_info')->get();
		$data = "";
		foreach ($recs as $rec){
			$data = $data."<tr>";
			$data = $data."<td>".date("d-m-Y",strtotime($rec->serviceDate))."</td>";
			$data = $data."<td>".date("d-m-Y",strtotime($rec->serviceDate))."</td>";
			$data = $data."<td>".$rec->serviceNo."</td>";
			$data = $data."<td>".$rec->veh_reg."</td>";
			$data = $data."<td>".$rec->name."</td>";
			if($values["role"] == "DRIVER")
				$data = $data."<td>".$rec->driverSalary."</td>";
			else 
				$data = $data."<td>".$rec->helperSalary."</td>";
			$data = $data."<td>"."0.00"."</td>";
			$data = $data."</tr>";
		}
		$jsondata['dailytrips'] = $data;
		$data = "";
		$recs = DB::select( DB::raw("SELECT b.booking_number, b.source_date, b.source_time, b.source_busno, b.source_bustype, b.source_start_place, b.source_end_place, b.dest_start_place, b.dest_date, b.dest_time, b.dest_busno, b.dest_bustype, b.dest_start_place, b.dest_end_place,  vehicle.veh_reg, name FROM `bookingvehicles` bv JOIN busbookings b on b.booking_number=bv.booking_number JOIN vehicle on vehicle.id=vehicleId JOIN lookuptypevalues lv on lv.id=vehicle.vehicle_type where b.source_date BETWEEN '".$startDate."' and '".$endDate."' and (bv.driver1=".$empid." or bv.driver2=".$empid." or bv.helper=".$empid.")") );
		foreach ($recs as $rec){
			$data = $data."<tr>";
			$data = $data."<td> ";
				$data = $data."<b>Booking Number : </b>".$rec->booking_number." <br/>";
				$data = $data."<b>Source Trip : </b>".date("d-m-Y",strtotime($rec->source_date)).", ".$rec->source_time." - ".$rec->source_busno." ".$rec->source_bustype." <br/>";
				$data = $data.$rec->source_start_place." TO ".$rec->source_end_place." <br/>";
				$data = $data."<b>Ruturn Trip : </b>".date("d-m-Y",strtotime($rec->dest_date)).", ".$rec->dest_time." - ".$rec->dest_busno." ".$rec->dest_bustype." <br/>";
				$data = $data.$rec->dest_start_place." TO ".$rec->dest_end_place." <br/>";
			$data = $data." </td> ";
			$data = $data."<td>".$rec->veh_reg."</td>";
			$data = $data."<td>".$rec->name."</td>";
			$data = $data."</tr>";
		}
		$jsondata['localtrips'] = $data;
		echo json_encode($jsondata);
	}	
}
