<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class UploadsController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addUpload()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("uploadfor"=>"type", "documenttype"=>"lookupValueId", "employee"=>"refId", "vehicle"=>"refId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			if (isset($values["file"]) && Input::hasFile('file') && Input::file('file')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('file')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('file')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Uploads"; 
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("uploads");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("uploads");
			}
		}
		
		$form_info = array();
		$form_info["name"] = "addcity";
		$form_info["action"] = "addcity";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cities";
		$form_info["bredcum"] = "add city";
		
		$form_fields = array();
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name; 	
		}
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"citycode", "content"=>"city code", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editUpload()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
		$field_names = array("uploadfor1"=>"type", "documenttype1"=>"lookupValueId", "employee1"=>"refId", "vehicle1"=>"refId", "status1"=>"status",);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			if (isset($values["file1"]) && Input::hasFile('file1') && Input::file('file1')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('file1')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('file1')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Uploads";
			$data = array("id"=>$values['id1']);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("uploads");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("uploads");
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editcity?id";
		$form_info["action"] = "editcity?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cities";
		$form_info["bredcum"] = "edit city";
	
		$form_fields = array();
	
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		$entity = \City::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"", "value"=>$entity->name, "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"citycode", "content"=>"city code", "readonly"=>"",  "value"=>$entity->code, "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "value"=>$entity->stateId, "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
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
		$response = "<option value=''> --select city-- </option>";
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
	public function getDepotsbyCityId()
	{
		$values = Input::all();
		$entities = \Depot::where("cityId","=",$values['id'])->get();
		$response = "<option value=''> --select depot-- </option>";
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
	public function getDepotsbyClientId()
	{
		$values = Input::all();
		$emp_contracts = \Auth::user()->contractIds;
		if($emp_contracts == ""){
			$entities = \Depot::where("clientId","=",$values['id'])
						->join("contracts", "depots.id", "=","contracts.depotId")
						->join("clients", "clients.id", "=","contracts.clientId")
						->select(array("depots.id as id","depots.name as name"))->get();
		}
		else{
			$emp_contracts = explode(",", $emp_contracts);
			$entities = \Depot::whereIn("depots.id",$emp_contracts)
						->where("clientId","=",$values['id'])
						->join("contracts", "depots.id", "=","contracts.depotId")
						->join("clients", "clients.id", "=","contracts.clientId")
						->select(array("depots.id as id","depots.name as name"))->get();
		}
		$response = "<option value=''> --select depot-- </option>";
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
	public function getfinanceCompanybyCityId()
	{
		$values = Input::all();
		$entities = \FinanceCompany::where("cityId","=",$values['id'])->get();
		$response = "<option> --select finance company-- </option>";
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
	public function manageUploads()
	{
		$values = Input::all();
		$values['bredcum'] = "UPLOADS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addupload';
		$values['form_action'] = 'uploads';
		$values['action_val'] = '';
		$theads = array('type','uploaded for', "upload type", "file", "status","Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editupload?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
	
		
		$form_info = array();
		$form_info["name"] = "addupload";
		$form_info["action"] = "addupload";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "uploads";
		$form_info["bredcum"] = "add upload";
		
		$form_fields = array();		
		$employees =  \Employee::Where("status","=","ACTIVE")->get();
		$employees_arr = array();
		foreach ($employees as $employee){
			$employees_arr[$employee['id']] = $employee['fullName']." (".$employee['empCode'].")";
		}
		
		$vehicles =  \Vehicle::Where("status","=","ACTIVE")->get();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
		}
		
		$parentId = \LookupTypeValues::where("name", "=", "UPLOAD DOCUMENT TYPES")->get();
		$upload_types = array();
		if(count($parentId)>0){
			$parentId = $parentId[0];
			$parentId = $parentId->id;
			$upload_types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		
		}
		$document_type_arr = array();
		foreach ($upload_types as $upload_type){
			$document_type_arr[$upload_type->id] = $upload_type->name;
		}
		
		$form_field = array("name"=>"uploadfor", "content"=>"upload for", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"enableUploadFields(this.value)"),  "required"=>"required", "type"=>"select", "options"=>array("EMPLOYEE"=>"EMPLOYEE","VEHICLE"=>"VEHICLE"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employee", "content"=>"employee", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$employees_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"documenttype", "content"=>"document type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$document_type_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"file", "content"=>"file", "readonly"=>"",  "required"=>"required", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editupload";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "uploads";
		$form_info["bredcum"] = "add upload";		
		$form_field = array("name"=>"uploadfor1", "content"=>"upload for", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"enableUploadFields(this.value)"),  "required"=>"required", "type"=>"select", "options"=>array("EMPLOYEE"=>"EMPLOYEE","VEHICLE"=>"VEHICLE"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employee1", "content"=>"employee", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$employees_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"documenttype1", "content"=>"document type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$document_type_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicle1", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"file1", "content"=>"file", "readonly"=>"",  "required"=>"required", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "uploads";	
		return View::make('masters.layouts.lookupdatatable', array("values"=>$values));
	}
	
}
