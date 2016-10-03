<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class DailyFinanceController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addDailyFinance()
	{
		if (\Request::isMethod('post'))
		{
		
			$values = Input::All();
			$field_names = array("branchid"=>"branchId","financecompany"=>"financeCompanyId","firstinstallmentdate"=>"agmtDate","loanamount"=>"amountFinanced","installmentamount"=>"installmentAmount",
					"interestrate"=>"interestRate","frequency"=>"frequency","totalinstallments"=>"totalInstallments","noofinstallmentspaid"=>"paidInstallments","status"=>"status",
					"paymenttype"=>"paymentType","bankaccount"=>"bankAccountId");
			$fields = array();
			
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($val == "agmtDate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "DailyFinance";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("dailyfinances");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("dailyfinances");
			}
		}
		
		$form_info = array();
		$form_info["name"] = "adddailyfinance";
		$form_info["action"] = "adddailyfinance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "dailyfinances";
		$form_info["bredcum"] = "add daily finance";
		
		$form_fields = array();
		
		$banks =  \BankDetails::all();
		$bank_arr = array();
		foreach ($banks as $bank){
			$bank_arr[$bank['id']] = $bank->bankName."-".$bank->accountNo; 	
		}
		
		$actypes =  \LookupTypeValues::where("type","=","LOAN_FREQUENCY")->get();
		$actype_arr = array();
		foreach ($actypes as $actype){
			$actype_arr[$actype['id']] = $actype->value;
		}
		
		$paymenttypes =  \LookupTypeValues::where("type","=","PAYMENT_TYPE")->get();
		$pmttype_arr = array();
		foreach ($paymenttypes  as $paymenttype){
			$pmttype_arr[$paymenttype['id']] = $paymenttype->value;
		}
		
		$vehicles =  \OfficeBranch::All();

		$veh_arr = array();
		foreach ($vehicles as $vehicle){
			$veh_arr [$vehicle['id']] = $vehicle->name;
		}
	
		$purypes =  \LookupTypeValues::where("type","=","LOAN_PURPOSE")->get();
		$purtype_arr = array();
		foreach ($purypes  as $purype){
			$purtype_arr[$purype['value']] = $purype->value;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"statename", "id"=>"statename",  "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "id"=>"cityname",  "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeCity(this.value);"),  "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"financecompany", "id"=>"financecompany",  "content"=>"finance company", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>array());
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branchid", "id"=>"branchid",  "content"=>"loan for branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$veh_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"firstinstallmentdate", "id"=>"firstinstallmentdate",  "content"=>"first installment date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"loanamount", "id"=>"loanamount",  "content"=>"loan amount", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"installmentamount", "id"=>"installmentamount",  "content"=>"installment amount", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"interestrate", "id"=>"interestrate",  "content"=>"interest rate %", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"frequency", "id"=>"frequency",  "content"=>"frequency", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$actype_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalinstallments", "id"=>"totalinstallments",  "content"=>"total installments", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofinstallmentspaid", "id"=>"noofinstallmentspaid",  "content"=>"no of installments paid", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control  number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "id"=>"status",  "content"=>"status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>array("ACTIVE"=>"ACTIVE","CLOSED"=>"CLOSED"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "content"=>"Bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$bank_arr);
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.add2colform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editDailyFinance()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("branchid"=>"branchId","financecompany"=>"financeCompanyId","firstinstallmentdate"=>"agmtDate","loanamount"=>"amountFinanced","installmentamount"=>"installmentAmount",
					"interestrate"=>"interestRate","frequency"=>"frequency","totalinstallments"=>"totalInstallments","noofinstallmentspaid"=>"paidInstallments","status"=>"status",
					"paymenttype"=>"paymentType","bankaccount"=>"bankAccountId");
			$fields = array();
			
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($val == "agmtDate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\DailyFinance";
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editdailyfinance?id=".$data['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editdailyfinance?id=".$data['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editdailyfinance?id";
		$form_info["action"] = "editdailyfinance?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "dailyfinances";
		$form_info["bredcum"] = "edit daily finance";
	
		$form_fields = array();
	
		$entity = \DailyFinance::where("id","=",$values['id'])->get();
		if(count($entity)>0){
			$entity = $entity[0];
			
			$banks =  \BankDetails::all();
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","PAYMENT TYPE")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			$type_arr = array();
			foreach ($types as $type){
				$type_arr [$type['name']] = $type->name;
			}
			
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","LOAN FREQUENCY")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$loanfreqs =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			$loanfreqs_arr = array();
			foreach ($loanfreqs as $loanfreq){
				$loanfreqs_arr [$loanfreq['name']] = $loanfreq->name;
			}
			
			$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
			$banks_arr = array();
			foreach ($banks as $bank){
				$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
			}
			
			$vehicles =  \Vehicle::All();		
			$veh_arr = array();
			foreach ($vehicles as $vehicle){
				$veh_arr [$vehicle['id']] = $vehicle->veh_reg;
			}
			
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","LOAN PURPOSE")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$purypes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			$purtype_arr = array();
			foreach ($purypes as $purype){
				$purtype_arr [$purype['name']] = $purype->name;
			}
			
			$vehicles =  \OfficeBranch::All();
			
			$veh_arr = array();
			foreach ($vehicles as $vehicle){
				$veh_arr [$vehicle['id']] = $vehicle->name;
			}
			
			$states =  \State::Where("status","=","ACTIVE")->get();
			$state_arr = array();
			foreach ($states as $state){
				$state_arr[$state['id']] = $state->name;
			}
			
			$fincompanies =  \FinanceCompany::All();
			$fincompanies_arr = array();
			foreach ($fincompanies as $fincompany){
				$fincompanies_arr[$fincompany['id']] = $fincompany->name;
			}
			
			$city =  \FinanceCompany::where("id","=",$entity->financeCompanyId)->get();
			$cityId = $city[0]->cityId;
			$stateId = $city[0]->stateId;
				
			$fincompanies =  \FinanceCompany::where("cityId","=",$cityId)->get();
			$fincompanies_arr = array();
			foreach ($fincompanies as $fincompany){
				$fincompanies_arr[$fincompany['id']] = $fincompany->name;
			}
				
			$states =  \State::Where("status","=","ACTIVE")->get();
			$state_arr = array();
			foreach ($states as $state){
				$state_arr[$state['id']] = $state->name;
			}
			
			$cities =  \City::where("stateId","=",$stateId)->get();
			$cities_arr = array();
			foreach ($cities as $city){
				$cities_arr[$city['id']] = $city->name;
			}
				
			
			$form_field = array("name"=>"statename", "value"=>$stateId, "id"=>"statename",  "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "value"=>$cityId, "id"=>"cityname",  "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeCity(this.value);"),  "options"=>$cities_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"financecompany", "value"=>$entity->financeCompanyId, "id"=>"financecompany",  "content"=>"finance company", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$fincompanies_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branchid", "value"=>$entity->branchId, "id"=>"branchid",  "content"=>"loan for Branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$veh_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"firstinstallmentdate", "value"=>date("d-m-Y",strtotime($entity->agmtDate)), "id"=>"firstinstallmentdate",  "content"=>"first installment date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"loanamount", "value"=>$entity->amountFinanced, "id"=>"loanamount",  "content"=>"loan amount", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"installmentamount", "value"=>$entity->installmentAmount, "id"=>"installmentamount",  "content"=>"installment amount", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"interestrate", "value"=>$entity->interestRate, "id"=>"interestrate",  "content"=>"interest rate %", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"frequency", "value"=>$entity->frequency, "id"=>"frequency",  "content"=>"frequency", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$loanfreqs_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"totalinstallments", "value"=>$entity->totalInstallments, "id"=>"totalinstallments",  "content"=>"total installments", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"noofinstallmentspaid", "value"=>$entity->paidInstallments, "id"=>"noofinstallmentspaid",  "content"=>"no of insts paid", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control  number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "value"=>$entity->status, "id"=>"status",  "content"=>"status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>array("ACTIVE"=>"ACTIVE","CLOSED"=>"CLOSED"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
// 			$form_field = array("name"=>"bankaccount", "value"=>$entity->bankAccountId, "id"=>"bankaccount", "content"=>"Bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$banks_arr);
// 			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.edit2colform",array("form_info"=>$form_info));
		}
	}
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getCitiesbyStateId()
	{
		$values = Input::all();
		$entities = \City::where("stateId","=",$values['id'])->get();
		$response = "";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";			
		}
		echo $response;
	}	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getBranchbyCityId()
	{
		$values = Input::all();
		$entities = \OfficeBranch::where("Id","=",$values['id'])->get();
		$response = "";
		foreach ($entities as $entity){
			$response = $response."<option value='".$entity->id."'>".$entity->name."</option>";
		}
		echo $response;
	}

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageDailyFinances()
	{
		$values = Input::all();
		$values['bredcum'] = "DAILY FINANCES";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'adddailyfinance';
		$values['form_action'] = 'dailyfinances';
		$values['action_val'] = '';
		
		$theads = array('Branch','Finance Company', "Amt Financed", "Start Date", "Interest Rate %", "Freq", "Instmt Amount", "Total instmt", "Paid Insmt", "Payment type", "status", "Actions");
		$values["theads"] = $theads;
			
		$form_info = array();
		$form_info["name"] = "adddailyfinance";
		$form_info["action"] = "adddailyfinance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "dailyfinances";
		$form_info["bredcum"] = "add daily finance";
		
		$form_fields = array();
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","PAYMENT TYPE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['name']] = $type->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","LOAN FREQUENCY")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$loanfreqs =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		$loanfreqs_arr = array();
		foreach ($loanfreqs as $loanfreq){
			$loanfreqs_arr [$loanfreq['name']] = $loanfreq->name;
		}
		
		$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
		$banks_arr = array();
		foreach ($banks as $bank){
			$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
		}
		
		$vehicles =  \Vehicle::All();		
		$veh_arr = array();
		foreach ($vehicles as $vehicle){
			$veh_arr [$vehicle['id']] = $vehicle->veh_reg;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","LOAN PURPOSE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$purypes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		$purtype_arr = array();
		foreach ($purypes as $purype){
			$purtype_arr [$purype['name']] = $purype->name;
		}
		
		$vehicles =  \OfficeBranch::All();
		
		$veh_arr = array();
		foreach ($vehicles as $vehicle){
			$veh_arr [$vehicle['id']] = $vehicle->name;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$fincompanies =  \FinanceCompany::All();
		$fincompanies_arr = array();
		foreach ($fincompanies as $fincompany){
			$fincompanies_arr[$fincompany['id']] = $fincompany->name;
		}
		
		$form_field = array("name"=>"statename", "id"=>"statename",  "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "id"=>"cityname",  "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeCity(this.value);"),  "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"financecompany", "id"=>"financecompany",  "content"=>"finance company", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$fincompanies_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branchid", "id"=>"branchid",  "content"=>"loan for branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$veh_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"firstinstallmentdate", "id"=>"firstinstallmentdate",  "content"=>"first inst. date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"loanamount", "id"=>"loanamount",  "content"=>"loan amount", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"installmentamount", "id"=>"installmentamount",  "content"=>"installment amount", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"interestrate", "id"=>"interestrate",  "content"=>"interest rate %", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"frequency", "id"=>"frequency",  "content"=>"frequency", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$loanfreqs_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalinstallments", "id"=>"totalinstallments",  "content"=>"total installments", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"noofinstallmentspaid", "id"=>"noofinstallmentspaid",  "content"=>"no of inst. paid", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control  number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "id"=>"status",  "content"=>"status", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("ACTIVE"=>"ACTIVE","CLOSED"=>"CLOSED"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "content"=>"Bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$banks_arr);
// 		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;	
		
		$values["provider"] = "dailyfinances";			
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
