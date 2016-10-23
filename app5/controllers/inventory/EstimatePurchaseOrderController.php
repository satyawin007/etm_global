<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use settings\AppSettingsController;
class EstimatePurchaseOrderController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addContract()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["test"];
			$extrecs = \Contract::where("clientId","=",$values["clientname"])->where("depotId","=",$values["depot"])->count();
			if($extrecs>0){
				return json_encode(['status' => 'fail', 'message' => 'Duplicate Client name and depot/branch combination!']);
			}
			$field_names = array(
							"statename"=>"stateId","districtname"=>"districtId","cityname"=>"cityId","clientname"=>"clientId","depot"=>"depotId",
							"route"=>"routeId","vehicletype"=>"vehicleTypeId","distance"=>"distance","contracttype"=>"contractType",
							"fuelcharges"=>"fuelCharges","repaircharges"=>"repairCharges","fromdate"=>"startDate","todate"=>"endDate",
							"noofvehicles"=>"noofVehicles","floorrate"=>"floorRate","noofdrivers"=>"noofDrivers","noofhelpers"=>"noofHelpers"
						   );
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="fromdate" || $key=="todate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Contract";
			$recid = "";
			$recid = $db_functions_ctrl->insertRetId($table, $fields);
			if($recid>0){
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ContractVehicle";
				$jsonitems = json_decode($values["contractvehicles"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["contractId"] = $recid;
					$fields["vehicleId"] = $jsonitem->vehicle;
					$fields["driver1Id"] = $jsonitem->driver1;
					if(isset($jsonitem->driver2)){
						$fields["driver2Id"] = $jsonitem->driver2;
					}
					if(isset($jsonitem->helper)){
						$fields["helperId"] = $jsonitem->helper;
					}
					$db_functions_ctrl->insert($table, $fields);
				}
				
				return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
			}
			else{
				return json_encode(['status' => 'fail', 'message' => 'Operation Could not be completed, Try Again!']);
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
	public function editContract()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array(
							"statename"=>"stateId","districtname"=>"districtId","cityname"=>"cityId",
							"route"=>"routeId","vehicletype"=>"vehicleTypeId","distance"=>"distance","contracttype"=>"contractType",
							"fuelcharges"=>"fuelCharges","repaircharges"=>"repairCharges","fromdate"=>"startDate","todate"=>"endDate",
							"noofvehicles"=>"noofVehicles","floorrate"=>"floorRate","noofdrivers"=>"noofDrivers","noofhelpers"=>"noofHelpers"
						   );
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="fromdate" || $key=="todate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Contract";
			$recid = $db_functions_ctrl->update($table, $fields, array("id"=>$values["id1"]));
			if($recid){
				$db_functions_ctrl = new DBFunctionsController();
				$table = "ContractVehicle";
				$jsonitems = json_decode($values["contractvehicles"]);
				foreach ($jsonitems as $jsonitem){
					if($jsonitem->id != "-1"){
						$fields = array();
						$fields["driver1Id"] = $jsonitem->driver1;
						if(isset($jsonitem->driver2)){
							$fields["driver2Id"] = $jsonitem->driver2;
						}
						if(isset($jsonitem->helperId)){
							$fields["helperId"] = $jsonitem->helper;
						}
						$fields["status"] = $jsonitem->status;
						$db_functions_ctrl->update($table, $fields, array("id"=>$jsonitem->id));
					}
					else{
						$fields = array();
						$fields["contractId"] = $values["id1"];
						$fields["vehicleId"] = $jsonitem->vehicle;
						$fields["driver1Id"] = $jsonitem->driver1;
						if(isset($jsonitem->driver2)){
							$fields["driver2Id"] = $jsonitem->driver2;
						}
						if(isset($jsonitem->helperId)){
							$fields["helperId"] = $jsonitem->helper;
						}
						$db_functions_ctrl->insert($table, $fields);
					}
				}
				return json_encode(['status' => 'success', 'message' => 'Operation completed Successfully']);
			}
			else{
				return json_encode(['status' => 'fail', 'message' => 'Operation Could not be completed, Try Again!']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editcontract?id";
		$values["form_action"] = "editcontract?id=".$values['id'];
		$form_info["method"] = "post";
		$form_info["action"] = "editcontract";
		$values["action_val"] = "test";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "contracts";
		$values["bredcum"] = "EDIT CONTRACT";
	
		$form_fields = array();
	
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		$entity = \Contract::where("id","=",$values['id'])->get();
		if(count($entity)>0){
			$entity = $entity[0];
			$form_info = array();
			$form_info["name"] = "editcontract";
			$form_info["action"] = "editcontract";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "contracts";
			$form_info["bredcum"] = "edit contract";
			
			$form_fields = array();		
			$states =  \State::Where("status","=","ACTIVE")->get();
			$state_arr = array();
			foreach ($states as $state){
				$state_arr[$state['id']] = $state['name'];
			}
			
			$cities =  \City::Where("status","=","ACTIVE")->get();
			$citie_arr = array();
			foreach ($cities as $city){
				$citie_arr[$city['id']] = $city['name'];
			}
			
			$districts =  \District::all();
			$districts_arr = array();
			foreach ($districts as $district){
				$districts_arr[$district['id']] = $district['name'];
			}
			
			$clients =  AppSettingsController::getEmpClients();
			$clients_arr = array();
			foreach ($clients as $client){
				$clients_arr[$client['id']] = $client['name'];
			}
			
			$depots =  \Depot::all();
			$depot_arr = array();
			foreach ($depots as $depot){
				$depot_arr[$depot['id']] = $depot['name'];
			}
			
			$services =  \DB::select(\DB::raw("select servicedetails.id as id, city1.name as name1, city2.name as name2, servicedetails.description from servicedetails join cities as city1 on city1.id=servicedetails.sourceCity join cities as city2 on servicedetails.destinationCity=city2.id"));
			$services_arr = array();
			foreach ($services as $service){
				$desc = "";
				if($service->description != ""){
					$desc = " ".$service->description;
				}
				$services_arr[$service->id] = $service->name1."-".$service->name2.$desc;
			}
			
			$parentId = \LookupTypeValues::where("name", "=", "VEHICLE TYPE")->get();
			$vehtypes = array();
			if(count($parentId)>0){
				$parentId = $parentId[0];
				$parentId = $parentId->id;
				$vehtypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
			
			}
			$vehtypes_arr = array();
			foreach ($vehtypes as $vehtype){
				$vehtypes_arr[$vehtype->id] = $vehtype->name;
			}
			
			$form_field = array("name"=>"statename", "value"=>$entity->stateId, "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeState(this.value);"), "class"=>"form-control chosen-select", "options"=>$state_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"districtname", "value"=>$entity->districtId, "content"=>"district name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$districts_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"cityname", "value"=>$entity->cityId, "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "action"=>array("type"=>"onChange", "script"=>"changeCity(this.value);"), "options"=>$citie_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"clientname",  "value"=>$entity->clientId,"content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$clients_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"depot", "value"=>$entity->depotId, "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$depot_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"route", "value"=>$entity->routeId, "content"=>"route", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$services_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"vehicletype", "value"=>$entity->vehicleTypeId, "content"=>"vehicle type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehtypes_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"distance", "value"=>$entity->distance, "content"=>"distance", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contracttype", "value"=>$entity->contractType, "content"=>"contract type", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("Monthly"=>"Monthly","Querterly"=>"Querterly","Halfyearly"=>"Halfyearly","Yearly"=>"Yearly"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fuelcharges", "value"=>$entity->fuelCharges, "content"=>"fuel charges", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("Included"=>"Included","Not Included"=>"Not Included"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"repaircharges", "value"=>$entity->repairCharges, "content"=>"repair charges", "readonly"=>"", "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("Included"=>"Included","Not Included"=>"Not Included"));
			$form_fields[] = $form_field;
			$entity->startDate = date("d-m-Y",strtotime($entity->startDate));
			$entity->endDate = date("d-m-Y",strtotime($entity->endDate));
			$form_field = array("name"=>"contractyear", "value"=>array($entity->startDate,$entity->endDate), "content"=>"contract year", "readonly"=>"",  "required"=>"","type"=>"daterange", "class"=>"form-control date-range-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"noofvehicles", "value"=>$entity->noofVehicles, "content"=>"no of vehicles", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"floorrate", "value"=>$entity->floorRate, "content"=>"floor rate", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"noofdrivers", "value"=>$entity->noofDrivers, "content"=>"no of drivers", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"noofhelpers", "value"=>$entity->noofHelpers, "content"=>"no of helpers", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contractvehicles", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>$entity->id, "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			
			$form_info["form_fields"] = $form_fields;
			$ex_Vehicles = \ContractVehicle::where("status","=","ACTIVE")->get();
			$ex_Vehicles_arr = array();
			foreach ($ex_Vehicles as $ex_Vehicle){
				$ex_Vehicles_arr[] = $ex_Vehicle['vehicleId'];
			}
			
			$vehicles =  \Vehicle::all();
			$vehicles_arr = array();
			foreach ($vehicles as $vehicle){
				if(!in_array($vehicle['id'],$ex_Vehicles_arr)){
					$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
				}
			}
			$drivers =  \Employee::where("roleId","=",19)->get();
			$drivers_arr = array();
			foreach ($drivers as $driver){
				$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
			}
			$helpers =  \Employee::where("roleId","=",20)->get();
			$helpers_arr = array();
			foreach ($helpers as $helper){
				$helpers_arr[$helper['id']] = $helper['fullName']." (".$helper->empCode.")";
			}
			$form_fields =  array();
			$form_field = array("name"=>"vehicle", "content"=>"vehicle", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$vehicles_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"driver1", "content"=>"driver1", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"driver2", "content"=>"driver2", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"helper", "content"=>"helper", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$helpers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_info["add_form_fields"] = $form_fields;
			$values['form_info'] = $form_info;
			
			return View::make('contracts.edit2colmodalform', array("values"=>$values));
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
		$response = "<option> --select city-- </option>";
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
	
	public function addEstimatePurchaseOrders()
	{
		if (\Request::isMethod('post'))
		{
			//$values["DSF"];
			$values = Input::all();
			$url = "purchaseorder";
			$field_names = array("branch"=>"branchId","date"=>"date");
					
			$fields = array();
			foreach ($field_names as $key=>$val){
				 if(isset($values[$key])){
					if($key == "date"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				} 
			}
// 			if(isset($values["incharge"]) && $values["incharge"]>0){
// 				DB::statement(DB::raw("CALL update_incharge_amount(".$values["incharge"].", -".$values["totalamount"].");"));
// 			}
			/* if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			} */
			$db_functions_ctrl = new DBFunctionsController();
			$table = "EstimatePurchaseOrder";
			\DB::beginTransaction();
			$recid = "";
			try{
				$recid = $db_functions_ctrl->insertRetId($table, $fields);
			}
			catch(\Exception $ex){
				\DB::rollback();
				$json_resp = array();
				$json_resp["status"] = "fail";
				$json_resp["message"] = "Add Estimate Purchase Order Operation Could not be completed, Try Again!";
				echo json_encode($json_resp);
				return;
			}
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$table = "EstimatePurchaseOrderDetails";
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["estimate_purchase_order_id"] = $recid;
					$fields["itemId"] = $jsonitem->item;
					$fields["manufactureId"] = $jsonitem->manufacturer;
					$fields["quantity"] = $jsonitem->quantity;
					$fields["unitprice"] = $jsonitem->unitprice;
					$fields["creditsupplierId"] = $jsonitem->creditsupplier;
					$fields["remarks"] = $jsonitem->remarks;
					$db_functions_ctrl->insert($table, $fields);
				}
				
			}
			catch(\Exception $ex){
				$json_resp = array();
				$json_resp["status"] = "fail";
				$json_resp["message"] = "Add Estimate Purchase Order Details Operation Could not be completed, Try Again!";
				echo json_encode($json_resp);
				return;
			}
			\DB::commit();
		}
		$json_resp = array();
		$json_resp["status"] = "success";
		$json_resp["message"] = "Operation completed successfully";
		echo json_encode($json_resp);
		return;
	}
	
	public function editEstimatePurchaseOrder()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["DSF"];
			$values = Input::all();
			$url = "purchaseorder";
			$field_names = array("branch"=>"branchId","date"=>"date");
					
			$fields = array();
			foreach ($field_names as $key=>$val){
				 if(isset($values[$key])){
					if($key == "date"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
				} 
			}
// 			if(isset($values["incharge"]) && $values["incharge"]>0){
// 				DB::statement(DB::raw("CALL update_incharge_amount(".$values["incharge"].", -".$values["totalamount"].");"));
// 			}
			/* if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			} */
			$db_functions_ctrl = new DBFunctionsController();
			$table = "EstimatePurchaseOrder";
			\DB::beginTransaction();
			$recid = "";
			try{
				$recid = $db_functions_ctrl->insertRetId($table, $fields);
			}
			catch(\Exception $ex){
				\DB::rollback();
				$json_resp = array();
				$json_resp["status"] = "fail";
				$json_resp["message"] = "Add Estimate Purchase Order Operation Could not be completed, Try Again!";
				echo json_encode($json_resp);
				return;
			}
			try{
				$db_functions_ctrl = new DBFunctionsController();
				$table = "EstimatePurchaseOrderDetails";
				$jsonitems = json_decode($values["jsondata"]);
				foreach ($jsonitems as $jsonitem){
					$fields = array();
					$fields["estimate_purchase_order_id"] = $recid;
					$fields["itemId"] = $jsonitem->item;
					$fields["manufactureId"] = $jsonitem->manufacturer;
					$fields["quantity"] = $jsonitem->quantity;
					$fields["unitprice"] = $jsonitem->unitprice;
					$fields["creditsupplierId"] = $jsonitem->creditsupplier;
					$fields["remarks"] = $jsonitem->remarks;
					$db_functions_ctrl->insert($table, $fields);
				}
				
			}
			catch(\Exception $ex){
				$json_resp = array();
				$json_resp["status"] = "fail";
				$json_resp["message"] = "Add Estimate Purchase Order Details Operation Could not be completed, Try Again!";
				echo json_encode($json_resp);
				return;
			}
			\DB::commit();
			$json_resp = array();
			$json_resp["status"] = "success";
			$json_resp["message"] = "Operation completed successfully";
			echo json_encode($json_resp);
			return;
		}
	
		$form_info = array();
		$form_info["name"] = "editestimatepurchaseorder";
		$values["form_action"] = "editestimatepurchaseorder";
		$form_info["method"] = "post";
		$form_info["action"] = "editcontract";
		$values["action_val"] = "test";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "contracts";
		$values["bredcum"] = "EDIT CONTRACT";
	
		$form_fields = array();
	
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		$entity = \EstimatePurchaseOrder::where("id","=",$values['id'])->get();
// 		echo "test";
// 		die();
		if(count($entity)>0){
			$entity = $entity[0];
			$form_info = array();
			$form_info["name"] = "editestimatepurchaseorder";
			$form_info["action"] = "editestimatepurchaseorder";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "contracts";
			$form_info["bredcum"] = "edit estimate purchase order";
			$form_info['btn_action_type']="edit";
				
			$form_fields = array();
			
				
			$branch_arr = array();
			$branches = \OfficeBranch::where("status","=","ACTIVE")->get();
			foreach ($branches as $branch){
				$branch_arr[$branch->id]=$branch->name;
			}
			
			$form_field = array("name"=>"branch", "value"=>$branch_arr[$entity->branchId], "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branch_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"date", "value"=>date("d-m-Y",strtotime($entity->date)), "content"=>"date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"contractvehicles", "content"=>"", "readonly"=>"", "value"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"", "value"=>$entity->id, "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
				
			$form_info["form_fields"] = $form_fields;
			
			$items =  \Items::all();
			$items_arr = array();
			foreach ($items as $item){
				$items_arr[$item->id] = $item->name;
			}
			$suppliers_arr = array();
			$suppliers =  \CreditSupplier::all();
			foreach ($suppliers as $supplier){
				$suppliers_arr[$supplier->id] = $supplier->supplierName;
			}
			
			$form_fields =  array();
			$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>$items_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"manufacturer", "id"=>"manufacturer",  "content"=>"manufacturer", "readonly"=>"readonly",  "required"=>"", "type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"unitprice", "content"=>"unit price", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"creditsupplier", "content"=>"creditsupplier", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$suppliers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["add_form_fields"] = $form_fields;
			$values['form_info'] = $form_info;
				
			return View::make('contracts.edit2colmodalform', array("values"=>$values));
		}
	}

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageEstimatePurchaseOrders()
	{
		$values = Input::all();
		$values['bredcum'] = "ESTIMATE PURCHASE ORDER";
		$values['home_url'] = 'contractsmenu';
		$values['add_url'] = '';
		$values['form_action'] = 'estmatepurchaseorder';
		$values['action_val'] = '';
		$theads = array("Item","manufacturer","creditsupplier","quantity","unitprice","remarks","amount","actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
		
		$form_info = array();
		$form_info["name"] = "addestimatepurchaseorder";
		$form_info["action"] = "addestimatepurchaseorder";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "estimatepurchaseorders";
		$form_info["bredcum"] = "add estimatepurchaseorder";
		
		$form_fields = array();		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state['name'];
		}
		
		$cities =  \City::Where("status","=","ACTIVE")->get();
		$citie_arr = array();
		foreach ($cities as $city){
			//$citie_arr[$city['id']] = $city['name'];
		}
		
		$districts =  \District::all();
		$districts_arr = array();
		foreach ($districts as $district){
			$districts_arr[$district['id']] = $district['name'];
		}
		
		$clients =  AppSettingsController::getEmpClients();
		$clients_arr = array();
		foreach ($clients as $client){
			$clients_arr[$client['id']] = $client['name'];
		}
		
		$services =  \DB::select(\DB::raw("select servicedetails.id as id, city1.name as name1, city2.name as name2, servicedetails.description from servicedetails join cities as city1 on city1.id=servicedetails.sourceCity join cities as city2 on servicedetails.destinationCity=city2.id"));
		$services_arr = array();
		foreach ($services as $service){
			$desc = "";
			if($service->description != ""){
				$desc = " ".$service->description;
			}
			$services_arr[$service->id] = $service->name1."-".$service->name2.$desc;
		}
		
		$parentId = \LookupTypeValues::where("name", "=", "VEHICLE TYPE")->get();
		$vehtypes = array();
		if(count($parentId)>0){
			$parentId = $parentId[0];
			$parentId = $parentId->id;
			$vehtypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		
		}
		$vehtypes_arr = array();
		foreach ($vehtypes as $vehtype){
			$vehtypes_arr[$vehtype->id] = $vehtype->name;
		}
		
		$form_info["form_fields"] = $form_fields;
		
		$items =  \Items::all();
		$items_arr = array();
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$suppliers_arr = array();
		$suppliers =  \CreditSupplier::all();
		foreach ($suppliers as $supplier){
			$suppliers_arr[$supplier->id] = $supplier->supplierName;
		}
		
		$form_fields =  array();
		$form_field = array("name"=>"item", "id"=>"item",  "content"=>"item", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "options"=>$items_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"manufacturer", "id"=>"manufacturer",  "content"=>"manufacturer", "readonly"=>"readonly",  "required"=>"", "type"=>"select", "options"=>array(), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"unitprice", "content"=>"unit price", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"creditsupplier", "content"=>"creditsupplier", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$suppliers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$form_info["theads"] = $theads;
		$values['form_info'] = $form_info;
		
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit1";
		$form_info["action"] = "editclient";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "clients";
		$form_info["bredcum"] = "add client";

		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "estimatepurchaseorders";	
		return View::make('inventory.formrowdatatable', array("values"=>$values));
	}
	
}
