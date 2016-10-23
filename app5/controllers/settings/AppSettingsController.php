<?php namespace settings;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class AppSettingsController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function updateBannerSettings()
	{
		if (\Request::isMethod('post'))
		{
			$isSuccess = false;
			$values = Input::All();
			//$values["dsf"];
			if (isset($values["file"]) && Input::hasFile('file') && Input::file('file')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('file')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('file')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields = array();
				$fields["value"] = $fileName;
				$fields["updatedBy"] = \Auth::user()->id;
				\Parameters::where("name",'=',"banner")->update($fields);
				\Session::put("banner",$fileName);
			}
			if (isset($values["title"])) {
				$fields = array();
				$fields["value"] = $values["title"];
				$fields["updatedBy"] = \Auth::user()->id;
				\Parameters::where("name",'=',"title")->update($fields);
				\Session::put("title",$values["title"]);
			}
			if (isset($values["banner_type"])) {
				$fields = array();
				$fields["value"] = $values["banner_type"];
				$fields["updatedBy"] = \Auth::user()->id;
				\Parameters::where("name",'=',"banner type")->update($fields);
				\Session::put("banner_type",$values["banner_type"]);
			}
			if (isset($values["emails"])) {
				$fields = array();
				$fields["value"] = $values["emails"];
				$fields["updatedBy"] = \Auth::user()->id;
				\Parameters::where("name",'=',"emailIds")->update($fields);
			}
			if (isset($values["alertdays"])) {
				$fields = array();
				$fields["value"] = $values["alertdays"];
				$fields["updatedBy"] = \Auth::user()->id;
				\Parameters::where("name",'=',"alertdays")->update($fields);
			}
			if (isset($values["dashboardmessage"])) {
				$fields = array();
				$fields["value"] = $values["dashboardmessage"];
				$fields["updatedBy"] = \Auth::user()->id;
				\Parameters::where("name",'=',"dashboardmessage")->update($fields);
			}
			\Session::put("message","Operation completed Successfully");
			return \Redirect::to("settings");
		}
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editBankDetails()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("bankname"=>"bankName","branchname"=>"branchName","accountname"=>"accountName","accountno"=>"accountNo","accounttype"=>"accountType","balanceamount"=>"balanceAmount","cityname"=>"cityId","statename"=>"stateId","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BankDetails";
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editbankdetails?id=".$data['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editbankdetails?id=".$data['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editbankdetails?id";
		$form_info["action"] = "editbankdetails?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "bankdetails";
		$form_info["bredcum"] = "edit bank details";
	
		$form_fields = array();
	
		$entity = \BankDetails::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			
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
			
			$cities =  \City::where("stateId","=",$entity->stateId)->get();
			$cities_arr = array();
			foreach ($cities as $city){
				$cities_arr[$city['id']] = $city->name;
			}
			
			$form_field = array("name"=>"bankname", "id"=>"bankname", "value"=>$entity->bankName, "content"=>"bank name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$bank_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branchname", "value"=>$entity->branchName, "content"=>"Branch Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"accountname", "value"=>$entity->accountName, "content"=>"Account Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"accountno", "value"=>$entity->accountNo, "content"=>"account number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"accounttype", "id"=>"accounttype", "value"=>$entity->accountType, "content"=>"account type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$actype_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"balanceamount", "value"=>$entity->balanceAmount, "content"=>"Balance Amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statename", "id"=>"statename", "value"=>$entity->stateId, "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "id"=>"cityname", "value"=>$entity->cityId, "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$cities_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "id"=>"status", "value"=>$entity->status, "content"=>"status", "readonly"=>"",  "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE", "INACTIVE"=>"INACTIVE"), "class"=>"form-control");
			$form_fields[] = $form_field;
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
		
		$form_field = array("name"=>"bankname", "content"=>"bank name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$bank_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branchname", "content"=>"Branch Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accountname", "content"=>"Account Name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accountno", "content"=>"account number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"accounttype", "content"=>"account type", "readonly"=>"",  "required"=>"required",   "type"=>"select", "class"=>"form-control chosen-select", "options"=>$actype_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"Balance Amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control chosen-select", "options"=>$state_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		
		$values["provider"] = "bankdetails";
			
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
	public static function getEmpClients(){
		$entities = null;
		$emp_contracts = \Auth::user()->clientIds;
		if($emp_contracts=="" || $emp_contracts==0){
			$entities = \Client::where("status","=","ACTIVE")->get();
		}
		else{
			$emp_contracts = explode(",", $emp_contracts);
			 $entities = \Client::whereIn("id",$emp_contracts)
			 		->where("clients.status","=","ACTIVE")
			 		->select(array("clients.id as id","clients.name as name"))->groupBy("clients.id")->get();
		}
		 return $entities->toArray();
	}
	
	public static function getEmpBranches(){
		$entities = null;
		$emp_branches = \Auth::user()->officeBranchIds;
		if($emp_branches=="" || $emp_branches==0){
			$entities = \OfficeBranch::where("status","=","ACTIVE")->get();
		}
		else{
			$emp_branches = explode(",", $emp_branches);
			$entities = \OfficeBranch::where("status","=","ACTIVE")->whereIn("id",$emp_branches)->get();
		}
		return $entities->toArray();
	}
	
	public static function getNonContractVehicles(){
		$entities = \DB::select(\DB::raw("select vehicle.id, vehicle.veh_reg from vehicle where id not in(select vehicleId from contract_vehicles where status='ACTIVE')"));
		//print_r($entities); die();
		$recs = array();
		foreach ($entities as $entity){
			$rec['id'] = $entity->id;
			$rec['veh_reg'] = $entity->veh_reg;
			$recs[] = $rec;
		}
		return $recs;
	}
	
	public function checkDuplicateEntry(){
		$values = Input::All();
		
		if(!isset($values["table"])){
			echo "error";
			return;
		}
		$table = $values["table"];
		unset($values["table"]);
		$sql = $table::query();
		foreach($values as  $key=>$val){
			$sql->where($key,"=",$val);
		}
		$count =$sql->count();
		if($count>0){
			echo "exists";
			return;
		}
		echo "notexists";
		return;
	}
	
}
?>
