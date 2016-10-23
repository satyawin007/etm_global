<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ParameterController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addBankDetails()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("bankname"=>"bankName","branchname"=>"branchName","accountname"=>"accountName","accountno"=>"accountNo","accounttype"=>"accountType","balanceamount"=>"balanceAmount","cityname"=>"cityId","statename"=>"stateId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "BankDetails";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("bankdetails");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("bankdetails");
			}
		}
		
		$form_info = array();
		$form_info["name"] = "addbankdetails";
		$form_info["action"] = "addbankdetails";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		
		$form_fields = array();
		
		$banks =  \LookupTypeValues::where("typeName","=","BANK NAME")->get();
		$bank_arr = array();
		foreach ($banks as $bank){
			$bank_arr[$bank['value']] = $bank->value; 	
		}
		
		$actypes =  \LookupTypeValues::where("typeName","=","ACCOUNT TYPE")->get();
		$actype_arr = array();
		foreach ($actypes as $actype){
			$actype_arr[$actype['value']] = $actype->value;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$bank_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branchname", "content"=>"Branch Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accountname", "content"=>"Account Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accountno", "content"=>"account number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accounttype", "content"=>"account type", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$actype_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"Balance Amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
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
	public function editParameter()
	{
		$values = Input::all();
		if (\Request::isMethod('get'))
		{
			$field_names = array("name"=>"name","value"=>"value");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Parameters"; 
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				echo "success";
			}
			else{
				echo "fail";
			}
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
	public function manageBankDetails()
	{
		$values = Input::all();
		$values['bredcum'] = "BANK DETAILS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addbankdetails';
		$values['form_action'] = 'bankdetails';
		$values['action_val'] = '';
		$theads = array('Bank Name','Branch Name', "Account Name", "Account No", "Account Type", "Balance Amount", "status", "Actions");
		$values["theads"] = $theads;
		
		$form_info = array();
		$form_info["name"] = "addbankdetails";
		$form_info["action"] = "addbankdetails";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "add bank details";
		
		$form_fields = array();
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","BANK NAME")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$banks =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$bank_arr = array();
		foreach ($banks as $bank){
			$bank_arr [$bank['id']] = $bank->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","ACCOUNT TYPE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$actypes =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$actype_arr = array();
		foreach ($actypes as $actype){
			$actype_arr [$actype['id']] = $actype->name;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$bank_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branchname", "content"=>"Branch Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accountname", "content"=>"Account Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accountno", "content"=>"account number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accounttype", "content"=>"account type", "readonly"=>"",  "required"=>"required",   "type"=>"select", "class"=>"form-control", "options"=>$actype_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"Balance Amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		
		$values["provider"] = "bankdetails";
			
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
