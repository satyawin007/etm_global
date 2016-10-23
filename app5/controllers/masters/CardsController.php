<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class CardsController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addCard()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("cardnumber"=>"cardNumber","cardtype"=>"cardType","cardholdername"=>"cardHolderName","bank"=>"lookupValueId","creditlimit"=>"creditLimit","expiredate"=>"expireDate", "cardbankaccount"=>"bankAccountId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && $key=="expiredate"){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Cards";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("cards");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("cards");
			}	
		}		
		$form_info = array();
		$form_info["name"] = "addstate";
		$form_info["action"] = "addstate";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
		
		$form_fields = array();
		
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statecode", "content"=>"state code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function validateCardNumber()
	{
		$values = Input::all();
		$card = \Cards::where("cardNumber","=",$values["cardnumber"])->first();
		$ret_val = "NO";
		if(count($card)>0){
			$ret_val = "YES";
		}
		echo $ret_val;
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editCard()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("cardnumber1"=>"cardNumber","cardtype1"=>"cardType","cardholdername1"=>"cardHolderName","bank1"=>"lookupValueId","creditlimit1"=>"creditLimit","expiredate1"=>"expireDate", "cardbankaccount1"=>"bankAccountId", "status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && $key=="expiredate1"){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Cards";
			$values = array();
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("cards");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("cards");
			}
		}
		$form_info = array();
		$form_info["name"] = "editcard";
		$form_info["action"] = "editcard";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cards";
		$form_info["bredcum"] = "edit card";
	
		$entity = \State::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$form_fields = array();	
			$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"", "value"=>$entity->name,  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statecode", "content"=>"state code", "readonly"=>"",  "value"=>$entity->code, "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "content"=>"", "readonly"=>"",  "value"=>$entity->id, "required"=>"","type"=>"hidden", "value"=>$entity->id, "class"=>"form-control");
			$form_fields[] = $form_field;			
		
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.editform",array("form_info"=>$form_info));
		}
	}
	
		
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageCards()
	{
		$values = Input::all();
		$values['bredcum'] = "CARDS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addcard';
		$values['form_action'] = 'cards';
		$values['action_val'] = '#';
		$theads = array('Card number','card type', "card holder", "bank name", "account no", "credit limit", "expire date", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editcard?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "addcard";
		$form_info["action"] = "addcard";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cards";
		$form_info["bredcum"] = "add card";
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","BANK NAME")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$banks =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$bank_arr = array();
		foreach ($banks as $bank){
			$bank_arr [$bank->id] = $bank->name;
		}
		
		$bankacts =  \BankDetails::where("Status","=","ACTIVE")->get();
		$bankacts_arr = array();
		foreach ($bankacts as $bankact){
			$bankacts_arr[$bankact->id] = $bankact->bankName."-".$bankact->accountNo;
		}
		
		$form_fields = array();		
		$form_field = array("name"=>"cardnumber", "content"=>"card number", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"validateCard(this.value)"), "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cardtype", "content"=>"card type", "readonly"=>"",  "required"=>"required", "type"=>"select","action"=>array("type"=>"onchange","script"=>"enableBankAccount(this.value)"), "options"=>array("DEBIT CARD"=>"DEBIT CARD", "CREDIT CARD"=>"CREDIT CARD"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cardholdername", "content"=>"card holder name", "readonly"=>"", "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"bank", "content"=>"bank name", "readonly"=>"", "required"=>"required", "type"=>"select", "options"=>$bank_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cardbankaccount", "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"creditlimit", "content"=>"credit limit", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"expiredate", "content"=>"expire date", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editcard";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cards";
		$form_info["bredcum"] = "edit card";
		
		$modals = array();  
		$form_fields = array();
		$form_field = array("name"=>"cardnumber1", "content"=>"card number", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"validateCard(this.value)"), "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cardtype1", "content"=>"card type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("DEBIT CARD"=>"DEBIT CARD", "CREDIT CARD"=>"CREDIT CARD"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cardholdername1", "content"=>"card holder name", "readonly"=>"", "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"bank1", "content"=>"bank name", "readonly"=>"", "required"=>"required", "type"=>"select", "options"=>$bank_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cardbankaccount1", "content"=>"bank account", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$bankacts_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"creditlimit1", "content"=>"credit limit", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"expiredate1", "content"=>"expire date", "readonly"=>"", "required"=>"", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "cards";

		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}	
}
