<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class VehicleController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addVehicle()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("statename"=>"state_id", "cityname"=>"city_id", "vehicleregno"=>"veh_reg", "engineno"=>"eng_no", 
								 "chassisno"=>"chsno", "vehicletype"=>"vehicle_type", "yearofpur"=>"yearof_pur", "seatingcapacity"=>"seat_cap", "remarks"=>"remarks", 
								 "depreciationvalue"=>"dep_val", "purchaseamount"=>"purchase_amount", "actualcost"=>"actual_cost", "monthlyemi"=>"emi", 
								 "taxlastpaid_ts"=>"tax_last_paid_ts","taxlastpaid_ap"=>"tax_last_paid_ap","taxlastpaid_kta"=>"tax_last_paid_kta","taxlastpaid_tn"=>"tax_last_paid_tn", "insurancelastpaid"=>"insurance_last_paid", "fitnesslastpaid"=>"fit_last_paid", 
								 "polutionlastcheck"=>"pol_last_paid", "permitlastpaid"=>"permit_last_paid",
								 "emi_amount"=>"emi", "total_emis"=>"total_emis", "paid_emis"=>"paid_emis"
								);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="taxlastpaid_ts" || $key=="taxlastpaid_ap" || $key=="taxlastpaid_kta" || $key=="taxlastpaid_tn" || $key=="insurancelastpaid" || $key=="fitnesslastpaid" || $key=="polutionlastcheck" || $key=="polutionlastcheck" || $key=="permitlastpaid" || $key=="yearofpur"){
						$dt = date('Y-m-d', strtotime($values[$key]));
						echo $values[$key]." - ".$dt.", ";
						$fields[$val] = $dt;
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Vehicle";
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("addvehicle");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("addvehicle");
			}
		}
		
		$form_info = array();
		$form_info["name"] = "addvehicle";
		$form_info["action"] = "addvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "vehicles";
		$form_info["bredcum"] = "add vehicle";
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","VEHICLE TYPE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['id']] = $type->name;
		}
				
		$tabs = array();
		$form_fields = array();
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicleregno", "content"=>"Vehicle Regd No ", "readonly"=>"", "action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'Vehicle')"), "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"engineno", "content"=>"Engine No ", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"chassisno", "content"=>"Chassis No", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicletype", "content"=>"Vehicle Type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$type_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"yearofpur", "content"=>"Year of purchase", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"seatingcapacity", "content"=>"seating capacity", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabone";
		$tab['heading'] = strtoupper("Basic Information");
		$tabs[] = $tab;
		
		$form_fields = array();
		$form_field = array("name"=>"depreciationvalue", "content"=>"depreciation value", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"purchaseamount", "content"=>"purchase amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"actualcost", "content"=>"actual cost", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"emi_amount", "content"=>"emi amount", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"total_emis", "content"=>"total emis", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paid_emis", "content"=>"paid emis", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		/*$form_field = array("name"=>"taxlastpaid_ts", "content"=>"tax last paid date ts", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"taxlastpaid_ap", "content"=>"tax last paid date ap", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"taxlastpaid_kta", "content"=>"tax last paid date kta", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"taxlastpaid_tn", "content"=>"tax last paid date tn", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"insurancelastpaid", "content"=>"insurance last paid date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fitnesslastpaid", "content"=>"fitness last paid date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"polutionlastcheck", "content"=>"last polution check date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"permitlastpaid", "content"=>"permit last paid date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;*/
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabtwo";
		$tab['heading'] = strtoupper("other information");
		$tabs[] = $tab;
		$form_info["tabs"] = $tabs;
		return View::make("masters.layouts.addtabbedform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a city.
	 *
	 * @return Response
	 */
	public function editVehicle()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("statename"=>"state_id", "cityname"=>"city_id", "vehicleregno"=>"veh_reg", "engineno"=>"eng_no", 
								 "chassisno"=>"chsno", "vehicletype"=>"vehicle_type", "yearofpur"=>"yearof_pur", "seatingcapacity"=>"seat_cap", "remarks"=>"remarks", 
								 "depreciationvalue"=>"dep_val", "purchaseamount"=>"purchase_amount", "actualcost"=>"actual_cost", "monthlyemi"=>"emi", 
								 "taxlastpaid_ts"=>"tax_last_paid_ts","taxlastpaid_ap"=>"tax_last_paid_ap","taxlastpaid_kta"=>"tax_last_paid_kta","taxlastpaid_tn"=>"tax_last_paid_tn", "insurancelastpaid"=>"insurance_last_paid", "fitnesslastpaid"=>"fit_last_paid", 
								 "polutionlastcheck"=>"pol_last_paid", "permitlastpaid"=>"permit_last_paid", "status"=>"status",
								 "emi_amount"=>"emi", "total_emis"=>"total_emis", "paid_emis"=>"paid_emis"
								);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="taxlastpaid_ts" || $key=="taxlastpaid_ap" || $key=="taxlastpaid_kta" || $key=="taxlastpaid_tn" || $key=="insurancelastpaid" || $key=="fitnesslastpaid" || $key=="polutionlastcheck" || $key=="polutionlastcheck" || $key=="permitlastpaid" || $key=="yearofpur"){
						$dt = date('Y-m-d', strtotime($values[$key]));
						echo $values[$key]." - ".$dt.", ";
						$fields[$val] = $dt;
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Vehicle";
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editvehicle?id=".$values["id"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editvehicle?id=".$values["id"]);
			}
		}
		
		$form_info = array();
		$form_info["name"] = "editvehicle";
		$form_info["action"] = "editvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "vehicles";
		$form_info["bredcum"] = "edit vehicle";
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$parentId = -1;
		$parent = \LookupTypeValues::where("name","=","VEHICLE TYPE")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$types =  \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$type_arr = array();
		foreach ($types as $type){
			$type_arr [$type['id']] = $type->name;
		}
		
		$entity = \Vehicle::where("id", "=", $values['id'])->get();
		$entity = $entity[0];
		
		$cities =  \City::Where("status","=","ACTIVE")->Where("stateId","=",$entity->state_id)->get();
		$cities_arr = array();
		foreach ($cities as $city){
			$cities_arr[$city['id']] = $city->name;
		}
		
		$tabs = array();
		$form_fields = array();
		$form_field = array("name"=>"statename", "value"=>$entity->state_id, "content"=>"state name", "readonly"=>"",  "required"=>"required", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"),  "type"=>"select", "class"=>"form-control", "options"=>$state_arr);
		$form_fields[] = $form_field;		
		$form_field = array("name"=>"cityname", "value"=>$entity->city_id,  "content"=>"city name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$cities_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicleregno", "value"=>$entity->veh_reg,  "content"=>"Vehicle Regd No ", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"engineno", "value"=>$entity->eng_no,  "content"=>"Engine No ", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"chassisno", "value"=>$entity->chsno,  "content"=>"Chassis No", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"vehicletype",  "value"=>$entity->vehicle_type,  "content"=>"Vehicle Type", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$type_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"yearofpur",  "value"=>date("d-m-Y",strtotime($entity->yearof_pur)),  "content"=>"Year of purchase", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"seatingcapacity", "value"=>$entity->seat_cap,  "content"=>"seating capacity", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks",  "value"=>$entity->remarks, "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status",  "value"=>$entity->status, "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INCTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id",  "value"=>$entity->id, "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabone";
		$tab['heading'] = strtoupper("Basic Information");
		$tabs[] = $tab;
		
		$tax_last_paid_ts = date("d-m-Y",strtotime($entity->tax_last_paid_ts));
		if($tax_last_paid_ts == "00-00-0000" || $tax_last_paid_ts == "01-01-1970"){
			$tax_last_paid_ts = "";
		}
		$tax_last_paid_ap = date("d-m-Y",strtotime($entity->tax_last_paid_ap));
		if($tax_last_paid_ap == "00-00-0000" || $tax_last_paid_ap == "01-01-1970"){
			$tax_last_paid_ap = "";
		}
		$tax_last_paid_kta = date("d-m-Y",strtotime($entity->tax_last_paid_kta));
		if($tax_last_paid_kta == "00-00-0000" || $tax_last_paid_kta == "01-01-1970"){
			$tax_last_paid_kta = "";
		}
		$tax_last_paid_tn = date("d-m-Y",strtotime($entity->tax_last_paid_tn));
		if($tax_last_paid_tn == "00-00-0000" || $tax_last_paid_tn == "01-01-1970"){
			$tax_last_paid_tn = "";
		}
		
		$form_fields = array();
		$form_field = array("name"=>"depreciationvalue",  "value"=>$entity->dep_val,  "content"=>"depreciation value", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"purchaseamount",  "value"=>$entity->purchase_amount, "content"=>"purchase amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"actualcost", "value"=>$entity->actual_cost,  "content"=>"actual cost", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"emi_amount", "value"=>$entity->emi,  "content"=>"emi amount", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"total_emis", "value"=>$entity->total_emis,  "content"=>"total emis", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paid_emis", "value"=>$entity->paid_emis,  "content"=>"paid emis", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		/*$form_field = array("name"=>"taxlastpaid_ts", "value"=>$tax_last_paid_ts,  "content"=>"tax last paid date ts", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"taxlastpaid_ap", "value"=>$tax_last_paid_ap, "content"=>"tax last paid date ap", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"taxlastpaid_kta", "value"=>$tax_last_paid_kta, "content"=>"tax last paid date kta", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"taxlastpaid_tn", "value"=>$tax_last_paid_tn, "content"=>"tax last paid date tn", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"insurancelastpaid", "value"=>date("d-m-Y",strtotime($entity->insurance_last_paid)),  "content"=>"insurance last paid date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fitnesslastpaid", "value"=>date("d-m-Y",strtotime($entity->fit_last_paid)),  "content"=>"fitness last paid date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"polutionlastcheck", "value"=>date("d-m-Y",strtotime($entity->pol_last_paid)),  "content"=>"last polution check date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"permitlastpaid", "value"=>date("d-m-Y",strtotime($entity->permit_last_paid)),  "content"=>"permit last paid date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date");
		$form_fields[] = $form_field;*/
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabtwo";
		$tab['heading'] = strtoupper("other information");
		$tabs[] = $tab;
		$form_info["tabs"] = $tabs;
		return View::make("masters.layouts.edittabbedform",array("form_info"=>$form_info));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageVehicles()
	{
		$values = Input::all();
		$values['bredcum'] = "VEHICLES";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addvehicle';
		$values['form_action'] = 'vehicles';
		$values['action_val'] = '';
		
		$action_val = "";
		$links = array();
		if(isset($values['action']) && $values['action']=="all_vehicles") {
			$url = "vehicles?action=all_vehicles ";
			$link = array("url"=>$url, "name"=>"Load All Vehicles");
			$action_val = "all_vehicles";
			$links[] = $link;
		}
		else{
			$link = array("url"=>"vehicles?action=all_vehicles", "name"=>"Load All Vehicles");
			$links[] = $link;
		}
			
		if(isset($values['action']) && $values['action']=="blocked") {
			$url = "vehicles?action=blocked";
			$link = array("url"=>$url, "name"=>"Load Blocked Vehicles");
			$action_val = "blocked";
			$links[] = $link;
		}
		else{
			$link = array("url"=>"vehicles?action=blocked", "name"=>"Load Blocked Vehicles");
			$links[] = $link;
		}
			
		if(isset($values['action']) && $values['action']=="sell") {
			$url = "vehicles?action=sell";
			$link = array("url"=>$url, "name"=>"Load sold vehicles");
			$action_val = "sell";
			$links[] = $link;
		}
		else{
			$link = array("url"=>"vehicles?action=sell", "name"=>"Load sold vehicles");
			$links[] = $link;
		}

		$values['action_val'] = $action_val;
		$values['links'] = $links;
				
		$theads = array('Vehicle Reg No','Vehicle Type', "Year of purchase", "Seating Capacity", "Attachments", "Renewals (Expires On)", "status", "Actions");
		$values["theads"] = $theads;
			
		$tds = array('veh_reg','veh_type', "yearof_pur", "seat_cap");
		$values["tds"] = $tds;
			
		$actions = array();
		$action = array("url"=>"editvehicle?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		if(isset($values['action']) && $values['action']=="blocked") {
			$action = array("url"=>"#block", "type"=>"modal", "css"=>"purple", "js"=>"modalBlockVehicle(", "jsdata"=>array("id","veh_reg"), "text"=>"Unblock");
		}
		else{
			$action = array("url"=>"#block", "type"=>"modal", "css"=>"purple", "js"=>"modalBlockVehicle(", "jsdata"=>array("id","veh_reg"), "text"=>"block");
		}
		$actions[] = $action;
		if(isset($values['action']) && $values['action']=="sell") {
			$action = array("url"=>"#sell", "type"=>"modal", "css"=>"grey", "js"=>"modalSellVehicle(", "jsdata"=>array("veh_reg",""), "text"=>"sell");
		}
		else{
			$action = array("url"=>"#sell", "type"=>"modal", "css"=>"grey", "js"=>"modalSellVehicle(", "jsdata"=>array("veh_reg"), "text"=>"sell");
		}
		$actions[] = $action;
		if(isset($values['action']) && $values['action']=="renew") {
			$action = array("url"=>"#renew", "type"=>"modal", "css"=>"success", "js"=>"modalRenewVehicle(", "jsdata"=>array("veh_reg"), "text"=>"renew");
		}
		else{
			$action = array("url"=>"#renew", "type"=>"modal", "css"=>"success", "js"=>"modalRenewVehicle(", "jsdata"=>array("veh_reg"), "text"=>"renew");
		}
		$actions[] = $action;
		
		$values["actions"] = $actions;
	
		//Code to add modal forms
		$modals =  array();
			
		$form_info = array();
		$form_info["name"] = "block";			
		$form_info["action"] = "blockvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$form_fields = array();
		$form_field = array("name"=>"vehreg", "content"=>"Veh Reg No", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"blockeddate", "content"=>"blocked date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "readonly"=>"", "content"=>"remarks", "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
			
		$form_info = array();
		$form_info["name"] = "sell";
		$form_info["action"] = "sellvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
			
		$form_fields = array();
		$form_field = array("name"=>"vehreg1", "content"=>"Veh Reg No", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"soldto", "content"=>"sold to", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"address", "readonly"=>"", "content"=>"address", "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalcost", "content"=>"total cost", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paidamount", "content"=>"paid amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"solddate", "content"=>"sold date", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id2", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "readonly"=>"", "content"=>"remarks", "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;		
		$values["modals"] = $modals;
		
		$values["provider"] = "vehicles&action=".$action_val;			
		return View::make('masters.layouts.datatable', array("values"=>$values));
	}
	
	public function blockVehicle(){
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$blockStatus = "";
			if(isset($values["id1"])){
				$vehicle = \Vehicle::where("id","=",$values["id1"])->get();
				if(count($vehicle)>0){
					$vehicle = $vehicle[0];
					$blockStatus = $vehicle->status;
				}
			}
			
			if($blockStatus === "BLOCKED"){
				$field_names = array("id1"=>"vehId", "blockeddate"=>"date", "remarks"=>"remarks");
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key=="blockeddate"){
							$dt = date('Y-m-d', strtotime($values[$key]));
							$fields[$val] = $dt;
						}
						else {
							$fields[$val] = $values[$key];
						}
					}
				}
				$fields["status"] = "ACTIVATED";
				$db_functions_ctrl = new DBFunctionsController();
				$table = "\VehicleHistory";
				$db_functions_ctrl = new DBFunctionsController();
					
				if($db_functions_ctrl->insert($table, $fields)){
					$table = "\Vehicle";
					$fields = array();
					$fields["status"] = "ACTIVE";
					$data = array("id"=>$values["id1"]);
					if($db_functions_ctrl->update($table, $fields, $data)){
						\Session::put("message","Operation completed Successfully");
						return \Redirect::to("vehicles");
					}
					else{
						\Session::put("message","Operation Could not be completed, Try Again!");
						return \Redirect::to("vehicles");
					}
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("vehicles");
				}
			}
			
			$field_names = array("id1"=>"vehId", "blockeddate"=>"date", "remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="blockeddate"){
						$dt = date('Y-m-d', strtotime($values[$key]));
						$fields[$val] = $dt;
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$fields["status"] = "BLOKED";
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\VehicleHistory";
			$db_functions_ctrl = new DBFunctionsController();			
			
			if($db_functions_ctrl->insert($table, $fields)){
				$table = "\Vehicle";
				$fields = array();
				$fields["status"] = "BLOCKED";
				$data = array("id"=>$values["id1"]);
				if($db_functions_ctrl->update($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("vehicles");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("vehicles");
				}
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("vehicles");
			}
		}
	}
	
	public function sellVehicle(){
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("id2"=>"vehId", "solddate"=>"soldDate", "remarks1"=>"remarks",
								"soldto"=>"purchasedBy", "address"=>"purchaseeAddress", "totalcost"=>"totalCost",
								"paidamount"=>"paidAmount"
							);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="solddate"){
						$dt = date('Y-m-d', strtotime($values[$key]));
						$fields[$val] = $dt;
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\VehicleSaleDetails";
			$db_functions_ctrl = new DBFunctionsController();				
			if($db_functions_ctrl->insert($table, $fields)){
				$table = "\Vehicle";
				$fields = array();
				$fields["status"] = "SOLD";
				$data = array("id"=>$values["id2"]);
				if($db_functions_ctrl->update($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("vehicles");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("vehicles");
				}
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("vehicles");
			}
		}
	}
	
	public function renewVehicle(){
	
	}
	
}
