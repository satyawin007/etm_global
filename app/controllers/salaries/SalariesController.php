<?php namespace salaries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use settings\AppSettingsController;
class SalariesController extends \Controller {


	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function payDriversSalary()
	{
		$values = Input::all();
		$values['bredcum'] = "PAY DRIVERS/HELPERS SALARY";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;

		$form_info = array();
		$form_info["name"] = "payemployeesalary";
		$form_info["action"] = "payemployeesalary";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "PAY EMPLOYEE SALARY";
		
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
		
		$branch_val = "";     $month_val = "";   $pmtdate_val = "";      $pmttype_val = "cash";
		$clientname_val = ""; $depot_val = "";   $bankaccount_val = "";  $chequenumber_val = ""; 
		$fromdate_val = "";   $todate_val = "";  $incharage_val = "0";   $show_val = "false";
		$enableincharge_val = "NO";
		
		if(isset($values["show"])){
			$show_val = $values["show"];
		}
		if(isset($values["branch"])){
			$branch_val = $values["branch"];
		}
		if(isset($values["clientname"])){
			$clientname_val = $values["clientname"];
		}
		if(isset($values["depot"])){
			$depot_val = $values["depot"];
		}
		if(isset($values["month"])){
			$month_val = $values["month"];
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
		if(isset($values["fromdate"])){
			$fromdate_val = $values["fromdate"];
		}
		if(isset($values["todate"])){
			$todate_val = $values["todate"];
		}
		if(isset($values["incharge"])){
			$incharage_val = $values["incharge"];
		}
		if(isset($values["enableincharge"])){
			$enableincharge_val = $values["enableincharge"];
		}
		$casual_leaves = 2;
		if(isset($values["casualleaves"])){
			$casual_leaves = $values["casualleaves"];
		}
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		
		$depots_arr = array();
		if(isset($values["clientname"])){
			$emp_contracts = \Auth::user()->contractIds;
			if($emp_contracts == ""){
				$entities = \Depot::where("clientId","=",$values["clientname"])
								->where("depots.status","=","ACTIVE")
								->join("contracts", "depots.id", "=","contracts.depotId")
								->join("clients", "clients.id", "=","contracts.clientId")
								->select(array("depots.id as id","depots.name as name"))->get();
			}
			else{
				$emp_contracts = explode(",", $emp_contracts);
				$entities = \Depot::whereIn("depots.id",$emp_contracts)
								->where("clientId","=",$values["clientname"])
								->where("depots.status","=","ACTIVE")
								->join("contracts", "depots.id", "=","contracts.depotId")
								->join("clients", "clients.id", "=","contracts.clientId")
								->select(array("depots.id as id","depots.name as name"))->get();
			}
			foreach ($entities as $entity){
				$depots_arr[$entity->id] = $entity->name;
			}
		}
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
								->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		$form_field = array("name"=>"clientname", "value"=>$clientname_val, "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot(this.value);"), "class"=>"form-control chosen-select", "options"=>$clients_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"depot", "value"=>$depot_val, "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFormData(this.value);"), "class"=>"form-control chosen-select", "options"=>$depots_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"month", "value"=>$month_val, "content"=>"salary month", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$month_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentdate", "value"=>$pmtdate_val, "content"=>"payment date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branch", "value"=>$branch_val, "content"=>"payment branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"salarydates", "value"=>$fromdate_val.",".$todate_val, "content"=>"salary dates", "readonly"=>"",  "required"=>"required", "type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"casualleaves", "value"=>$casual_leaves, "content"=>"casual leaves", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"updateCasualLeaves(this.value)"), "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "value"=>$enableincharge_val, "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "value"=>$incharage_val, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"inchargebalance", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>$pmttype_val, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
		
		if(isset($values["chequenumber"])){
			$chequenumber_val = $values["chequenumber"];
			$form_field = array("name"=>"chequenumber", "value"=>$chequenumber_val, "content"=>"", "readonly"=>"", "type"=>"hidden");
			$form_fields[] = $form_field;
		}
		$form_field = array("name"=>"show", "value"=>$show_val, "content"=>"", "readonly"=>"", "type"=>"hidden");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		return View::make('salaries.driversalarydatatable', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function payOfficeEmployeeSalary()
	{
		$values = Input::all();
		$values['bredcum'] = "PAY OFFICE EMPLOYEE SALARY";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$form_info = array();
		$form_info["name"] = "payofficeemployeesalary";
		$form_info["action"] = "payofficeemployeesalary";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "PAY EMPLOYEE SALARY";
	
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
		
		$branch_val = "";     $month_val = "";   $pmtdate_val = "";      $pmttype_val = "cash";
		$clientname_val = ""; $depot_val = "";   $bankaccount_val = "";  $chequenumber_val = ""; 
		$fromdate_val = "";   $todate_val = "";  $incharage_val = ""; $show_val = "false";
		$enableincharge_val = "NO";
		
		if(isset($values["show"])){
			$show_val = $values["show"];
		}
		if(isset($values["branch"])){
			$branch_val = $values["branch"];
		}
		if(isset($values["clientname"])){
			$clientname_val = $values["clientname"];
		}
		if(isset($values["depot"])){
			$depot_val = $values["depot"];
		}
		if(isset($values["month"])){
			$month_val = $values["month"];
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
		if(isset($values["fromdate"])){
			$fromdate_val = $values["fromdate"];
		}
		if(isset($values["todate"])){
			$todate_val = $values["todate"];
		}
		if(isset($values["incharge"])){
			$incharage_val = $values["incharge"];
		}
		if(isset($values["enableincharge"])){
			$enableincharge_val = $values["enableincharge"];
		}
		$casual_leaves = 2;
		if(isset($values["casualleaves"])){
			$casual_leaves = $values["casualleaves"];
		}
		$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")
							->select(array("inchargeaccounts.empid as id","employee.fullName as name"))->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->name;
		}
		$form_field = array("name"=>"branch", "value"=>$branch_val, "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"month", "value"=>$month_val, "content"=>"salary month", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$month_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentdate", "value"=>$pmtdate_val, "content"=>"payment date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"salarydates", "value"=>$fromdate_val.",".$todate_val, "content"=>"salary dates", "readonly"=>"",  "required"=>"required", "type"=>"daterange", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"casualleaves", "value"=>$casual_leaves, "content"=>"casual leaves", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"updateCasualLeaves(this.value)"), "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"enableincharge", "value"=>$enableincharge_val, "content"=>"enable incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("YES"=>" YES","NO"=>" NO"), "action"=>array("type"=>"onchange","script"=>"enableIncharge(this.value)"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "value"=>$incharage_val, "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange", "script"=>"getInchargeBalance(this.value)"), "options"=>$incharges_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"inchargebalance", "content"=>"Incharge balance", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>$pmttype_val, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
		if(isset($values["chequenumber"])){
			$chequenumber_val = $values["chequenumber"];
			$form_field = array("name"=>"chequenumber", "value"=>$chequenumber_val, "content"=>"", "readonly"=>"", "type"=>"hidden");
			$form_fields[] = $form_field;
		}
		$form_field = array("name"=>"show", "value"=>$show_val, "content"=>"", "readonly"=>"", "type"=>"hidden");
		$form_fields[] = $form_field;
		
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		return View::make('salaries.officeemployeedatatable', array("values"=>$values));
	}
	
	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addEmployeeSalary()
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
			$message = "The following employees salary added successfully : <br/><b>";
			$url = "payemployeesalary?paymenttype=".$values["paymenttype"]."&branch=".$values["branch"]."&month=".$values["month"]."&paymentdate=".$values["paymentdate"];
			if(isset($values["clientname"]) && isset($values["depot"])){
				$url = $url;
			}
			else{
				$url = "payofficeemployeesalary?paymenttype=".$values["paymenttype"]."&branch=".$values["branch"]."&month=".$values["month"]."&paymentdate=".$values["paymentdate"];
			}
			if(isset($values["clientname"])){ $url = $url."&clientname=".$values["clientname"];}
			if(isset($values["show"])){ $url = $url."&show=".$values["show"];}
			if(isset($values["depot"])){ $url = $url."&depot=".$values["depot"];}
			if(isset($values["fromdate"])){ $url = $url."&fromdate=".$values["fromdate"];}
			if(isset($values["todate"])){ $url = $url."&todate=".$values["todate"];}
			if(isset($values["casualleaves"])){ $url = $url."&casualleaves=".$values["casualleaves"];}
			if(isset($values["bankaccount"])){ $url = $url."&bankaccount=".$values["bankaccount"];}
			if(isset($values["chequenumber"])){ $url = $url."&chequenumber=".$values["chequenumber"];}
			if(isset($values["bankname"])){ $url = $url."&bankname=".$values["bankname"];}
			if(isset($values["accountnumber"])){ $url = $url."&accountnumber=".$values["accountnumber"];}
			if(isset($values["issuedate"])){ $url = $url."&issuedate=".$values["issuedate"];}
			if(isset($values["transactiondate"])){ $url = $url."&transactiondate=".$values["transactiondate"];}
			foreach ($ids as $id){
				$id = $id%$values["dynamic-table_length"];
				$actualSalary = 0;
				$actualSalary = $values["emp_salary"][$id];
				
				$dueDeductions = $values['due_deductions'][$id];
				$dailyTripsAllowance = 0;
				if(isset($values["daily_trips_allowance"])){
					$dailyTripsAllowance = $values["daily_trips_allowance"][$id];
				}
				$leave_deductions = 0;
				if(isset($values["leave_deductions"])){
					$leave_deductions = $values["leave_deductions"][$id];
				}
				$pfOpted = $values['pfopted'][$id];
				$pf = 0;
				$esi = 0;
				$proftax = 0;
				if($pfOpted == 'Yes')
				{
					$pf = (($actualSalary *60/100)*12/100);
					$esi = ($actualSalary *1.75/100);
					if($actualSalary > 15000 && $actualSalary < 20000)
						$proftax = 150;
					else if($actualSalary > 20000)
						$proftax = 200;
				  	else
					  	$proftax = 0;
				}
				$salaryPaid = $actualSalary - ($pf + $esi + $proftax)+$dailyTripsAllowance;
				$salaryPaid = $salaryPaid - ($leave_deductions);
				if($dueDeductions != "0.00")
					$salaryPaid = $salaryPaid - ($dueDeductions);
				else
				 	$dueDeductions= 0;
				$salaryPaid = $salaryPaid+$values["other_amt"][$id];
				$values["pf"][$id] = $pf;
				$values["esi"][$id] = $esi;
				$values["proftax"][$id] = $proftax;
				$values["salarypaid"][$id] = $salaryPaid;
				$values["totalsalary"][$id] = $actualSalary;
				
				$field_names = array("id"=>"empId","casual_leaves"=>"casualLeaves", "totalsalary"=>"actualSalary","daily_trips_salary"=>"dailyTripsSalary","daily_trips_allowance"=>"dailyTripsAllowance","local_trips_salary"=>"localTripsSalary", "other_amt"=>"otherAmount", "leave_amount"=>"leaveAmount","due_deductions"=>"dueDeductions","leave_deductions"=>"leaveDeductions","salarypaid"=>"salaryPaid","pfopted"=>"pfOpted","pf"=>"pf","esi"=>"esi","proftax"=>"profTax","comments"=>"comments");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						$fields[$val] = $values[$key][$id];
					}
				}
				$field_names = array("month"=>"salaryMonth","branch"=>"branchId","paymentdate"=>"paymentDate","paymenttype"=>"paymentType","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","bankname"=>"bankName","accountnumber"=>"accountNumber","issuedate"=>"issueDate","transactiondate"=>"transactionDate","incharge"=>"inchargeId");
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "paymentdate" || $key == "issuedate" || $key == "transactiondate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else {
							$fields[$val] = $values[$key];
						}
					}
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "SalaryTransactions";
				\DB::beginTransaction();
				$recid = "";
				try{
					$recid = $db_functions_ctrl->insertRetId($table, $fields);
					$message = $message.$values["employeename"][$id].", ";
				}
				catch(\Exception $ex){
					\Session::put("message","Add salary : Operation Could not be completed, Try Again!");
					\DB::rollback();
					return \Redirect::to($url);
				}
				try{
					$db_functions_ctrl = new DBFunctionsController();
					$table = "EmpDueAmount";
					$values["duetype"][$id]= "Loan";
					$values["sourceentity"][$id]= "empsalarytransactions";
					$values["sourceentityid"][$id]= $recid;
					$values["due_deductions"][$id] = -1*$values["due_deductions"][$id];
					$fields = array();
					$field_names = array("id"=>"empId","duetype"=>"dueType","due_deductions"=>"amount","sourceentity"=>"sourceEntity","sourceentityid"=>"sourceEntityId");
					foreach ($field_names as $key=>$val){
						if(isset($values[$key]) && $key == "paymentdate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if(isset($values[$key])){
							$fields[$val] = $values[$key][$id];
						}
					}
					$field_names = array("branch"=>"branchId","paymentdate"=>"paymentDate");
					foreach ($field_names as $key=>$val){
						if(isset($values[$key]) && $key == "paymentdate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if(isset($values[$key])){
							$fields[$val] = $values[$key];
						}
					}
					if(isset($fields["amount"]) && $fields["amount"]>0){
						$db_functions_ctrl->insert($table, $fields);
					}
				}
				catch(\Exception $ex){
					\Session::put("message","Add Due amout : Operation Could not be completed, Try Again!");
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
	public function editSalaryTransaction()
	{
		if (\Request::isMethod('get'))
		{
			$values = Input::All();
			$actualSalary = 0;
			if(isset($values["daily_trips_salary"]) && isset($values["local_trips_salary"])){
				$actualSalary = $values["daily_trips_salary"] + $values["local_trips_salary"];
			}
			else{
				$actualSalary = $values["emp_salary"];
			}
			$dueDeductions = $values['deductions'];
			$dailyTripsAllowance = 0;
			if(isset($values["daily_trips_allowance"])){
				$dailyTripsAllowance = $values["daily_trips_allowance"];
			}
			$leave_deductions = 0;
			if(isset($values["leave_deductions"])){
				$leave_deductions = $values["leave_deductions"];
			}
			$pfOpted = $values['pfopted'];
			if($pfOpted=="undefined"){
				$values['pfopted'] = "No";
			}
			$pf = 0;
			$esi = 0;
			$proftax = 0;
			if($pfOpted == 'Yes')
			{
				$pf = (($actualSalary *60/100)*12/100);
				$esi = ($actualSalary *1.75/100);
				if($actualSalary > 15000 && $actualSalary < 20000)
					$proftax = 150;
				else if($actualSalary > 20000)
					$proftax = 200;
			  	else
				  	$proftax = 0;
			}
			$salaryPaid = $actualSalary - ($pf + $esi + $proftax)+$dailyTripsAllowance;
			
			if($dueDeductions != "0.00")
				$salaryPaid = $salaryPaid - ($dueDeductions+$leave_deductions);
			else
			 	$dueDeductions= 0;
			$salaryPaid = $salaryPaid+$values["other_amt"];
			$values["pf"] = $pf;
			$values["esi"] = $esi;
			$values["proftax"] = $proftax;
			$values["salarypaid"] = $salaryPaid;
			$values["totalsalary"] = $actualSalary;

			$field_names = array("id"=>"empId","totalsalary"=>"actualSalary","other_amt"=>"otherAmount", "daily_trips_salary"=>"dailyTripsSalary","daily_trips_allowance"=>"dailyTripsAllowance","local_trips_salary"=>"localTripsSalary","leave_amount"=>"leaveAmount","deductions"=>"dueDeductions","leave_deductions"=>"leaveDeductions","salarypaid"=>"salaryPaid","pfopted"=>"pfOpted","pf"=>"pf","esi"=>"esi","proftax"=>"profTax", "bankaccount"=>"bankAccount","cheque"=>"chequeNumber","comments"=>"comments");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "SalaryTransactions";
			$data = array("eid"=>$values["eid"], "month"=>$values["month"]);
			if($db_functions_ctrl->updateSalaryTransaction($table, $fields, $data)){
				$data = array("empId"=>$values["eid"], "salaryMonth"=>$values["month"]);
				$recid = $db_functions_ctrl->get($table, $data);
				if(count($recid)>0){
					$recid = $recid[0];
					$table = "EmpDueAmount";
					$data = array("id"=>$recid->id);
					$fields = array("amount"=>(-1*$values["deductions"]));
					if(isset($fields["amount"]) && $fields["amount"]>0){
						if($db_functions_ctrl->updateEmpDueAmout($table, $fields, $data)){
						    echo "success";
							return;
						}
					}
				}
			}
			echo "fail";
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
	
	public function getCalEmpSalary(){
		$values = Input::all();
		$empid = $values["eid"];
		$salaryMonth = $values["dt"];
		$noOfDays = date("t", strtotime($salaryMonth)) -1;
		$startDate = $salaryMonth;
		$endDate =  date('Y-m-d', strtotime($salaryMonth.'+ '.$noOfDays.' days'));
		$jsondata = array();
	
		$data = "0.00";
		$recs = DB::select( DB::raw("SELECT SUM(`amount`) amt FROM `empdueamount` WHERE empId = ".$empid." and deleted='No'") );
		foreach ($recs as $rec){
			$data = $rec->amt;
			if($data == ""){
				$data = "0.00";
			}
		}
		$jsondata["due"] = $data;
		
		\DB::statement(DB::raw('CALL calc_daily_trip_salary('.$empid.",'".$startDate."','".$endDate."');"));
		$recs = array();
		if($values["role"] == "HELPER")
			$recs = DB::select( DB::raw("SELECT SUM(`helperSalary`) amt FROM `temp_dailytripsalary`") );
		else
			$recs = DB::select( DB::raw("SELECT SUM(`driverSalary`) amt FROM `temp_dailytripsalary`") );
		$data = "0.00";
		foreach ($recs as $rec){
			$data = $rec->amt;
			if($data == ""){
				$data = "0.00";
			}
		}
		$jsondata['dailytrips'] = $data;
		echo json_encode($jsondata);
	}
	
	public function getCalOfficeEmpSalary(){
		$values = Input::all();
		$empid = $values["eid"];
		$salaryMonth = $values["dt"];
		$noOfDays = date("t", strtotime($salaryMonth)) -1;
		$startDate = $salaryMonth;
		$endDate =  date('Y-m-d', strtotime($salaryMonth.'+ '.$noOfDays.' days'));
		$jsondata = array();
	
		$data = "0.00";
		$recs = DB::select( DB::raw("SELECT SUM(`amount`) amt FROM `empdueamount` WHERE empId = ".$empid." and deleted='No'") );
		foreach ($recs as $rec){
			$data = $rec->amt;
			if($data == ""){
				$data = "0.00";
			}
		}
		$jsondata["due"] = $data;
		
		$data = "0.00";
		$recs = \SalaryDetails::where("empId","=",$empid)->where("status","=","ACTIVE")->get();
		foreach ($recs as $rec){
			$data = $rec->salary;
			if($data == ""){
				$data = "0.00";
			}
		}
		$salary = $data;
		$jsondata['salary'] = $data;
		
		$leaves = 0;
		$leaveamt = 0;
		$recs = DB::select( DB::raw("SELECT * from leaves where (fromDate BETWEEN '".$startDate."' and '".$endDate."' or toDate BETWEEN '".$startDate."' and '".$endDate."') and empId=".$empid." and deleted='No'"));
		foreach ($recs as $rec){
			$fdate = $rec->fromDate;
			$tdate = $rec->toDate;
			if((strtotime($startDate) <= strtotime($fdate) && strtotime($fdate) <= strtotime($endDate)) && (strtotime($startDate) <= strtotime($tdate) && strtotime($tdate) <= strtotime($endDate))){
				$leaves = $leaves+$rec->noOfLeaves;
			}
			else if((strtotime($fdate) < strtotime($endDate)) && (strtotime($fdate) > strtotime($startDate))){
				$dt = date("Y-m-d",strtotime($fdate));
				$dStart = new \DateTime($dt);
				$dt = date("Y-m-d",strtotime($endDate));
				$dEnd  = new \DateTime($dt);
				$dDiff = $dStart->diff($dEnd);
				$days =  (int)$dDiff->days;
				$leaves = $leaves+$days;
			}
			else if((strtotime($fdate) < strtotime($startDate)) && (strtotime($tdate) > strtotime($startDate))){
				$dt = date("Y-m-d",strtotime($tdate));
				$dStart = new \DateTime($dt);
				$dt = date("Y-m-d",strtotime($startDate));
				$dEnd  = new \DateTime($dt);
				$dDiff = $dStart->diff($dEnd);
				$days =  (int)$dDiff->days;
				$leaves = $leaves+$days;
			}
		}
		$leaveamt  = ($salary/30)*$leaves;
		$jsondata['leaves'] = $leaves;
		$jsondata['leaveamt'] = $leaveamt;
		echo json_encode($jsondata);
	}
	
	public function getTransactionAmount(){
		$values = Input::ALL();
		$depo_amt = 0;
		if($values["transid"]==""){
			$values["transid"] = "zzzz";
		}
		$recs = \ExpenseTransaction::where("status","=","ACTIVE")->where("chequeNumber","=",$values["transid"])->get();
		foreach($recs as $rec){
			$depo_amt = $depo_amt+$rec->amount;
		}
		$tot_amt = 0;
		$recs = \SalaryTransactions::where("chequeNumber","=",$values["transid"])->get();
		foreach($recs as $rec){
			$tot_amt = $tot_amt+$rec->salaryPaid;
		}
		$tot_amt = $depo_amt-$tot_amt;
		echo "BAL AMT : ".$tot_amt;
	}
	
}
