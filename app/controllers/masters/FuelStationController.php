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
			         
			$field_names = array("fuelstationname"=>"name","balanceamount"=>"balanceAmount", "bankaccount"=>"bankAccount", "paymenttype"=>"paymentType","paymentexpectedday"=>"paymentExpectedDay","cityname"=>"cityId","statename"=>"stateId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
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
		
		$states =  \State::all();
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
		if (\Request::isMethod('post'))
		{
			$field_names = array("fuelstationname"=>"name","balanceamount"=>"balanceAmount","paymenttype"=>"paymentType","paymentexpectedday"=>"paymentExpectedDay","cityname"=>"cityId","statename"=>"stateId","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\FuelStation";
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editfuelstation?id=".$data['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editfuelstation?id=".$data['id']);
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
	
		$entity = \FuelStation::where("id","=",$values['id'])->get();
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
			
			$states =  \State::all();
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
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"));
			$form_fields[] = $form_field;
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
		
		$theads = array('Fuel Station Name','Payment TYpe', "Payment Expected Day", "Bank Account", "Account No", "City", "State", "status", "Actions");
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
		
		$states =  \State::all();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"balance Amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		
		$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>$banks_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_credit"=>"CHEQUE (CREDIT)","cheque_debit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentexpectedday", "content"=>"payment expected day", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		
		$values["provider"] = "fuelstations";
			
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
