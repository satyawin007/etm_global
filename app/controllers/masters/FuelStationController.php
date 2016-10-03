<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class FuelStationController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addFuelStation()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			         
			$field_names = array("fuelstationname"=>"name","balanceamount"=>"balanceAmount", "bankaccount"=>"bankAccount",
									"paymenttype"=>"paymentType","paymentexpectedday"=>"paymentExpectedDay","cityname"=>"cityId",
									"statename"=>"stateId","securitydepositamount"=>"securityDepositAmount","securitypaymenttype"=>"securityPaymentType",
									"securitypaymentdate"=>"securityPaymentDate","bankname"=>"bankName","accountnumber"=>"accountNumber",
									"chequenumber"=>"chequeNumber","issuedate"=>"issueDate","transactiondate"=>"transactionDate"
								);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if ($key == "transactiondate" || $key=="securitypaymentdate" || $key=="issuedate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "FuelStation";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("fuelstations");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("fuelstations");
			}
		}
		
		$form_info = array();
		$form_info["name"] = "addfuelstation";
		$form_info["action"] = "addfuelstation";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "fuelstations";
		$form_info["bredcum"] = "add fuel station";
		
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
		
		$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
		$banks_arr = array();
		foreach ($banks as $bank){
			$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"balance Amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$banks_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "content"=>"payment type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$type_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentexpectedday", "content"=>"payment expected day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editFuelStation()
	{
		$values = Input::all();
		//$values["dsaf"];
		if (\Request::isMethod('post'))
		{
			$field_names = array("fuelstationname1"=>"name","balanceamount1"=>"balanceAmount", "bankaccount1"=>"bankAccount",
									"paymenttype1"=>"paymentType","paymentexpectedday1"=>"paymentExpectedDay","status1"=>"status","cityname1"=>"cityId",
									"statename1"=>"stateId","securitydepositamount1"=>"securityDepositAmount","securitypaymenttype1"=>"securityPaymentType",
									"securitypaymentdate1"=>"securityPaymentDate","bankname1"=>"bankName","accountnumber1"=>"accountNumber",
									"chequenumber1"=>"chequeNumber","issuedate1"=>"issueDate","transactiondate1"=>"transactionDate"
								);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){http://localhost/etm_global/public/fuelstations#edit
					if ($key == "transactiondate" || $key=="securitypaymentdate1" || $key=="issuedate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\FuelStation";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("fuelstations");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("fuelstations");
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editfuelstation?id";
		$form_info["action"] = "editfuelstation?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "fuelstations";
		$form_info["bredcum"] = "edit fuel station";
	
		$form_fields = array();
	
		$entity = \FuelStation::where("id","=",$values['id1'])->get();
		if(count($entity)){
			$entity = $entity[0];
			
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
			
			$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
			$banks_arr = array();
			foreach ($banks as $bank){
				$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
			}
			
			$states =  \State::Where("status","=","ACTIVE")->get();
			$state_arr = array();
			foreach ($states as $state){
				$state_arr[$state['id']] = $state->name;
			}
			
			$cities =  \City::where("stateId","=",$entity->stateId)->get();
			$cities_arr = array();
			foreach ($cities as $city){
				$cities_arr[$city['id']] = $city->name;
			}
			
			$form_field = array("name"=>"fuelstationname", "value"=>$entity->name, "content"=>"fuel station name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"balanceamount", "value"=>$entity->balanceAmount, "content"=>"balance Amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;					
			$form_field = array("name"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$banks_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			if($entity->paymentType === "cheque_credit"){
				//die();
				$bankacts =  \BankDetails::All();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "cheque_debit"){
				$bankacts =  \BankDetails::All();
				$bankacts_arr = array();
				foreach ($bankacts as $bankact){
					$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
				}
				$form_field = array("name"=>"bankaccount",  "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control",  "options"=>$bankacts_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"cheque number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "dd"){
				$form_field = array("name"=>"bankname", "id"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"ddnumber", "id"=>"ddnumber","value"=>$entity->ddNumber, "content"=>"dd number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"issuedate", "id"=>"issuedate", "value"=>date("d-m-Y",strtotime($entity->issueDate)),"content"=>"issue date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "ecs" || $entity->paymentType === "neft" || $entity->paymentType === "rtgs"){
				$form_field = array("name"=>"bankname", "id"=>"bankname","value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"accountnumber", "id"=>"accountnumber","value"=>$entity->accountNumber, "content"=>"account number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber","value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "credit_card"){
				$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","CREDIT CARD")->get();
				$cards_arr = array();
				foreach ($cards as $card){
					$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"credit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			if($entity->paymentType === "debit_card"){
				$cards =  \Cards::where("Status","=","ACTIVE")->where("cardType","=","DEBIT CARD")->get();
				$cards_arr = array();
				foreach ($cards as $card){
					$cards_arr[$card->id] = $card->cardNumber." (".$card->cardHolderName.")";
				}
				$form_field = array("name"=>"bankaccount", "id"=>"bankaccount", "value"=>$entity->bankAccount, "content"=>"debit card", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$cards_arr);
				$form_fields[] = $form_field;
				$form_field = array("name"=>"chequenumber", "id"=>"chequenumber", "value"=>$entity->chequeNumber, "content"=>"transaction number", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"paymentexpectedday", "value"=>$entity->paymentExpectedDay, "content"=>"payment expected day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statename", "value"=>$entity->stateId, "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "value"=>$entity->cityId, "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$cities_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "value"=>$entity->status, "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.editform",array("form_info"=>$form_info));
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
	public function manageFuelStations()
	{
		$values = Input::all();
		$values['bredcum'] = "FUEL STATIONS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addfuelstation';
		$values['form_action'] = 'fuelstations';
		$values['action_val'] = '';
		
		$theads = array('Fuel Station Name', "balance amt", "deposit amt", 'security Pmt Type', 'pmt date', "City", "State", "status", "Actions");
		$values["theads"] = $theads;
			
		
		$form_info = array();
		$form_info["name"] = "addfuelstation";
		$form_info["action"] = "addfuelstation";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "fuelstations";
		$form_info["bredcum"] = "add fuel station";
		
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
		
		$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
		$banks_arr = array();
		foreach ($banks as $bank){
			$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"", "action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'FuelStation')"), "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"balance Amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>$banks_arr, "class"=>"form-control chosen-select");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>""), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"));
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"securitydepositamount", "content"=>"security deposit amt", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"securitypaymenttype", "id"=>"securitypaymenttype",  "content"=>"security payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"securitypaymentdate", "content"=>"security payment date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		
		
		
		
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editfuelstation";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "edit fuel station";
		
		$banks =  \BankDetails::where("bankdetails.status", "=", "ACTIVE")->join("lookuptypevalues","lookuptypevalues.id","=","bankdetails.bankName")->select("bankdetails.id as id", "bankdetails.accountNo as accountNo", "lookuptypevalues.name as name")->get();
		$banks_arr = array();
		foreach ($banks as $bank){
			$banks_arr [$bank['id']] = $bank->name." - ".$bank->accountNo;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
			
		$cities =  \City::Where("status","=","ACTIVE")->get();
		$cities_arr = array();
		foreach ($cities as $city){
			$cities_arr[$city['id']] = $city->name;
		}
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fuelstationname1", "value"=>"", "content"=>"station name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount1", "value"=>"", "content"=>"balance Amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;					
// 		$form_field = array("name"=>"bankaccount1", "value"=>"", "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$banks_arr, "class"=>"form-control");
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"paymenttype1", "value"=>"",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"));
// 		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"paymentexpectedday1", "value"=>"", "content"=>"payment expected day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename1", "id"=>"statename1", "value"=>"", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname1", "id"=>"cityname1", "value"=>"", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$cities_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"securitydepositamount1", "content"=>"sec. depo. amt", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"securitypaymenttype1", "id"=>"securitypaymenttype1",  "content"=>"security pmt type", "readonly"=>"", "required"=>"", "type"=>"select", "class"=>"form-control  chosen-select",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"securitypaymentdate1", "content"=>"security pmt date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control  chosen-select");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values["provider"] = "fuelstations";
			
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
