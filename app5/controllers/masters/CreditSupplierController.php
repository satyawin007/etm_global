<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class CreditSupplierController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addCreditSupplier()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();         
			$field_names = array("suppliername"=>"supplierName","contactperson"=>"contactPerson","contactphoneno"=>"contactPhoneNo","fulladdress"=>"fullAddress","statename"=>"stateId","cityname"=>"cityId","bankaccount"=>"bankAccount","paymenttype"=>"paymentType","balanceamount"=>"balanceAmount","paymentexpectedday"=>"paymentExpectedDay");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "CreditSupplier";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("creditsuppliers");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("creditsuppliers");
			}
		}
		
		
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editCreditSupplier()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("suppliername"=>"supplierName","contactperson"=>"contactPerson","contactphoneno"=>"contactPhoneNo","fulladdress"=>"fullAddress","statename"=>"stateId","cityname"=>"cityId","bankaccount"=>"bankAccount","paymenttype"=>"paymentType","balanceamount"=>"balanceAmount","paymentexpectedday"=>"paymentExpectedDay", "status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\CreditSupplier";
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editcreditsupplier?id=".$data['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editcreditsupplier?id=".$data['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editcreditsupplier?id";
		$form_info["action"] = "editcreditsupplier?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "creditsuppliers";
		$form_info["bredcum"] = "edit credit supplier";
	
		$form_fields = array();
	
		$entity = \CreditSupplier::where("id","=",$values['id'])->get();
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
				$type_arr [$type['id']] = $type->name;
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
			
			$form_field = array("name"=>"suppliername", "value"=>$entity->supplierName, "content"=>"supplier name", "readonly"=>"",  "required"=>"required","type"=>"text","class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contactperson", "value"=>$entity->contactPerson, "content"=>"contact person", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contactphoneno", "value"=>$entity->contactPhoneNo, "content"=>"contact phone no", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fulladdress", "value"=>$entity->fullAddress, "content"=>"full address", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statename", "id"=>"statename", "value"=>$entity->stateId, "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "id"=>"cityname", "value"=>$entity->cityId, "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$cities_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype", "value"=>$entity->paymentType, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
// 			$form_field = array("name"=>"bankaccount", "id"=>"bankaccount","value"=>$entity->bankAccount, "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$banks_arr);
// 			$form_fields[] = $form_field;
			$form_field = array("name"=>"balanceamount", "id"=>"balanceamount", "value"=>$entity->balanceAmount, "content"=>"balance amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
// 			$form_field = array("name"=>"paymentexpectedday", "id"=>"paymentexpectedday", "value"=>$entity->paymentExpectedDay, "content"=>"payment expected day", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
// 			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "id"=>"status", "value"=>$entity->status, "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"), "class"=>"form-control");
			$form_fields[] = $form_field;
			
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.edit2colform",array("form_info"=>$form_info));
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageCreditSupplier()
	{
		$values = Input::all();
		$values['bredcum'] = "CREDIT SUPPLIERS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addcreditsupplier';
		$values['form_action'] = 'creditsuppliers';
		$values['action_val'] = '';
		
		$theads = array('Supplier Name','Contact Person', "Contact No", "City", "Balance Amount", "Payment Type", "Bank Account", "Payment Expected Day", "status", "Actions");
		$values["theads"] = $theads;
			
		$form_info = array();
		$form_info["name"] = "addcreditsupplier";
		$form_info["action"] = "addcreditsupplier";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "creditsuppliers";
		$form_info["bredcum"] = "add credit supplier";
		
		$form_fields = array();
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
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
		
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"suppliername", "content"=>"supplier name", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'CreditSupplier')"),  "required"=>"required","type"=>"text","class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"contactperson", "content"=>"contact person", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"contactphoneno", "content"=>"contact phone no", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fulladdress", "content"=>"full address", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "id"=>"paymenttype",  "content"=>"payment type", "readonly"=>"",   "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"bankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$banks_arr);
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"balance amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"paymentexpectedday", "content"=>"payment expected day", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
// 		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"]= $form_info;
		$values["provider"] = "creditsuppliers";
			
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
