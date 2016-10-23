<?php namespace trips;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class TripsController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addDailyTrips()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();	
			$ids = array();
			if(isset($values["ids"])){
				$ids = $values["ids"];
			}
			$url = "dailytrips";
			if(isset($values["city"]) && isset($values["date"])){
				$url = $url."?city=".$values["city"]."&date=".$values["date"];
			}
			$message = "";
			foreach($ids as $id){
				if(isset($values["newtrip"]) && in_array($values["id"][$id], $values["newtrip"])){
					$fields = array();
					$fields["tripStartDate"] = date("Y-m-d",strtotime($values["dates"][$id]));
					$fields["vehicleId"] = $values["vehicle"][$id];
					$fields["status"] = "running";
					$fields["routeCount"] = 1;
					
					\DB::beginTransaction();
					$db_functions_ctrl = new DBFunctionsController();
					$table = "TripDetails";
					try{
						$tripid = $db_functions_ctrl->insertRetId($table, $fields);
						if($tripid <= 0){
							\Session::put("message","Duplicate Trip Details : Operation Could not be completed, Try Again!");
							\DB::rollback();
							return \Redirect::to($url);
						}
					}
					catch(\Exception $ex){
						\Session::put("message","Duplicate Trip Details : Operation Could not be completed, Try Again!");
						\DB::rollback();
						return \Redirect::to($url);
					}
					
					$fields = array();
					$fields["status"] = "NOT STARTED";
					$fields["tripID"] = $tripid;
					$table = "TripExpenses";
					try{
						$tripexpid = $db_functions_ctrl->insertRetId($table, $fields);
						if($tripexpid <= 0){
							\Session::put("message","Duplicate Trip Expenses : Operation Could not be completed, Try Again!");
							\DB::rollback();
							return \Redirect::to($url);
						}
					}
					catch(\Exception $ex){
						\Session::put("message","Duplicate Trip Expenses : Operation Could not be completed, Try Again!");
						\DB::rollback();
						return \Redirect::to($url);
					}
					$fields = array();
					$fields["serviceId"] = $values["id"][$id];
					$fields["serviceDate"] = date("Y-m-d",strtotime($values["date"]));
					$fields["driver1"] = $values["drivers1"][$id];
					$fields["driver2"] = $values["drivers2"][$id];
					$fields["helper"] = $values["helper"][$id];
					$fields["vehicleId"] = $values["vehicle"][$id];
					$fields["tripId"] = $tripid;
					$fields["tripRouteNo"] = 1;
					$fields["status"] = "Running";
					$table = "TripServiceDetails";
					try{
						$tripexpid = $db_functions_ctrl->insertRetId($table, $fields);
						if($tripexpid <= 0){
							\Session::put("message","Duplicate Trip Service Details : Operation Could not be completed, Try Again!");
							\DB::rollback();
							return \Redirect::to($url);
						}
					}
					catch(\Exception $ex){
						\Session::put("message","Duplicate Trip Service Details : Operation Could not be completed, Try Again!");
						\DB::rollback();
						return \Redirect::to($url);
					}
					$message = $message."<br/>Service No - ".$values["servnos"][$id]." sucessfully Added.";
					\DB::commit();
				}
				else{
					$addmessage = false;
					$fields = array("vehicleId"=>$values["vehicle"][$id],"tripStartDate"=>date("Y-m-d",strtotime($values["dates"][$id])),"status"=>"running");
					\DB::beginTransaction();
					$db_functions_ctrl = new DBFunctionsController();
					$table = "TripDetails";
					try{
						$tripid = $db_functions_ctrl->get($table, $fields);
						if(count($tripid)>0){
							$addmessage = true;
							$tripid = $tripid[0];
							$fields = array();
							$fields["serviceId"] = $values["id"][$id];
							$fields["serviceDate"] = date("Y-m-d",strtotime($values["date"]));
							$fields["driver1"] = $values["drivers1"][$id];
							$fields["driver2"] = $values["drivers2"][$id];
							$fields["helper"] = $values["helper"][$id];
							$fields["vehicleId"] = $values["vehicle"][$id];
							$fields["tripId"] = $tripid->id;
							$fields["tripRouteNo"] = ($tripid->routeCount)+1;
							$fields["status"] = "Running";
							$table = "TripServiceDetails";
							try{
								$tripexpid = $db_functions_ctrl->insertRetId($table, $fields);
								$table = "TripDetails";
								$fields = array("routeCount"=>($tripid->routeCount)+1);
								$data = array("id"=>$tripid->id);
								$tripexpid = $db_functions_ctrl->update($table, $fields, $data);
							}
							catch(\Exception $ex){
								\Session::put("message","Operation Could not be completed, Try Again!");
								\DB::rollback();
								return \Redirect::to($url);
							}
						}
						else{
							$message = $message."<br/>Service No - ".$values["servnos"][$id]." not Added as there is no existing service for given date";
						}
					}
					catch(\Exception $ex){
						\Session::put("message","Operation Could not be completed, Try Again!");
						\DB::rollback();
						return \Redirect::to($url);
					}
					if($addmessage){
						$message = $message."<br/>A new route added for Service No - ".$values["servnos"][$id]." to existing service trip";
					}
					\DB::commit();
				}
			}
			\Session::put("message",$message);
			return \Redirect::to($url);
		}
	}
	
	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addLocalTrip()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("customername"=>"cust_name","phone"=>"cust_phone","altphone"=>"alternatephone","email"=>"cust_email",
							"adddress"=>"cust_address","sourcefrom"=>"source_start_place","sourceto"=>"source_end_place",
							"sourcejourneydatetime"=>"source_date","sourcebustype"=>"source_bustype","sourcenoofbuses"=>"source_busno",
							"destfrom"=>"dest_start_place","destto"=>"dest_end_place","destjourneydatetime"=>"dest_date",
							"destbustype"=>"dest_bustype","destnoofbuses"=>"dest_busno","totalamount"=>"total_cost",
							"fuelchargetype"=>"fuel_charge_type","bookingdate"=>"booking_date","bookingbranch"=>"booking_branch","routeinfo"=>"booking_descr");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "sourcejourneydatetime"){
						$dt_tm_arr = explode(" ", $values[$key]);
						$fields["source_date"] = date("Y-m-d",strtotime($dt_tm_arr[0]));
						$fields["source_time"] = $dt_tm_arr[1]." ".$dt_tm_arr[2];
					}
					else if($key == "destjourneydatetime"){
						$dt_tm_arr = explode(" ", $values[$key]);
						$fields["dest_date"] = date("Y-m-d",strtotime($dt_tm_arr[0]));
						$fields["dest_time"] = $dt_tm_arr[1]." ".$dt_tm_arr[2];
					}
					else if($key == "bookingdate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BusBookings";
			if(($id=$db_functions_ctrl->insertRetId($table, $fields))>0){
				$booking_number = "MST".date("y").date("m");
				if($id<10){
					$booking_number = $booking_number."0000".$id;
				}
				else if($id<100){
					$booking_number = $booking_number."000".$id;
				}
				else if($id<1000){
					$booking_number = $booking_number."00".$id;
				}
				else if($id<10000){
					$booking_number = $booking_number."0".$id;
				}
				else if($id<100000){
					$booking_number = $booking_number.$id;
				}
				$data = array("id"=>$id);
				$fields = array("booking_number"=>$booking_number);
				$db_functions_ctrl->update($table, $fields, $data);
	
				if(isset($values["advanceamount"])){
					$table = "\TripParticulars";
					$fields = array();
					$fields["amount"] = $values["advanceamount"];
					$fields["tripId"] = $id;
					$fields["tripType"] = "LOCAL";
					$fields["lookupValueId"] = 58;
					$fields["amount"] = $values["advanceamount"];
					if(isset($values["advancepaiddate"])){ $fields["date"] = date("Y-m-d", strtotime($values["advancepaiddate"])); }
					if(isset($values["creditedbranch"])){ $fields["branchId"] = $values["creditedbranch"]; }
					$db_functions_ctrl->insert($table, $fields);
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("addlocaltrip");
				}
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editlocaltrip?id=".$values['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "addlocaltrip";
		$form_info["action"] = "addlocaltrip";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "managetrips?triptype=LOCAL";
		$form_info["bredcum"] = "add new local trip";
	
		$form_fields = array();
	
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
		$paymenttypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		$vehtype_arr = array();
		foreach ($paymenttypes  as $paymenttype){
			$vehtype_arr[$paymenttype['name']] = $paymenttype->name;
		}
		
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches  as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
	
		$tabs = array();
		$form_fields = array();
		$form_field = array("name"=>"customername", "content"=>"customer name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"phone", "content"=>"phone number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"altphone", "content"=>"alternate  number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control input-mask-phone");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"email", "content"=>"email", "readonly"=>"",  "required"=>"","type"=>"email", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"adddress", "content"=>"customer address", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabone";
		$tab['heading'] = strtoupper("customer information");
		$tabs[] = $tab;
	
		$form_fields = array();
		$form_field = array("name"=>"sourcefrom", "content"=>"from", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"sourceto", "content"=>"to", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"sourcejourneydatetime", "content"=>"journey date & time", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-time-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"sourcebustype", "content"=>"bus type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$vehtype_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"sourcenoofbuses", "content"=>"no of buses", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"returntrip", "content"=>"return trip", "readonly"=>"",  "required"=>"required","type"=>"radio", "class"=>"form-control", "options"=>array("YES"=>"YES","NO"=>"NO"));
		$form_fields[] = $form_field;
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabtwo";
		$tab['heading'] = strtoupper("Source trip information");
		$tabs[] = $tab;
		
		$form_fields = array();
		$form_field = array("name"=>"destfrom", "content"=>"from", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"destto", "content"=>"to", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"destjourneydatetime", "content"=>"journey date & time", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-time-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"destbustype", "content"=>"bus type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$vehtype_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"destnoofbuses", "content"=>"no of buses", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabthree";
		$tab['heading'] = strtoupper("return trip information");
		$tabs[] = $tab;
		
		$form_fields = array();
		$form_field = array("name"=>"routeinfo", "content"=>"route information (any)", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"bookingdate", "content"=>"booking date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"bookingbranch", "content"=>"booking created branch", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fuelchargetype", "content"=>"fuel charge type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("customer"=>"CUSTOMER","company"=>"COMPANY"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"advanceamount", "content"=>"advance amount", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"advancepaiddate", "content"=>"advance paid date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"creditedbranch", "content"=>"advace credited branch", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$tab = array();
		$tab['form_fields'] = $form_fields;
		$tab['href'] = "tabfour";
		$tab['heading'] = strtoupper("booking & advance information");
		$tabs[] = $tab;
		
		$form_info["tabs"] = $tabs;
		return View::make("trips.addtabbedform",array("form_info"=>$form_info));
	}
	
	public function editLocalTrip()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("customername"=>"cust_name","phone"=>"cust_phone","altphone"=>"alternatephone","email"=>"cust_email",
					"adddress"=>"cust_address","sourcefrom"=>"source_start_place","sourceto"=>"source_end_place",
					"sourcejourneydatetime"=>"source_date","sourcebustype"=>"source_bustype","sourcenoofbuses"=>"source_busno",
					"destfrom"=>"dest_start_place","destto"=>"dest_end_place","destjourneydatetime"=>"dest_date",
					"destbustype"=>"dest_bustype","destnoofbuses"=>"dest_busno","totalamount"=>"total_cost",
					"fuelchargetype"=>"fuel_charge_type","bookingdate"=>"booking_date","bookingbranch"=>"booking_branch","routeinfo"=>"booking_descr");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key == "sourcejourneydatetime"){
						$dt_tm_arr = explode(" ", $values[$key]);
						$fields["source_date"] = date("Y-m-d",strtotime($dt_tm_arr[0]));
						$fields["source_time"] = $dt_tm_arr[1]." ".$dt_tm_arr[2];
					}
					else if($key == "destjourneydatetime"){
						$dt_tm_arr = explode(" ", $values[$key]);
						$fields["dest_date"] = date("Y-m-d",strtotime($dt_tm_arr[0]));
						$fields["dest_time"] = $dt_tm_arr[1]." ".$dt_tm_arr[2];
					}
					else if($key == "bookingdate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BusBookings";
			$data = array("id"=>$values['id']);
			if($db_functions_ctrl->update($table,$fields,$data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editlocaltrip?id=".$values['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editlocaltrip?id=".$values['id']);
			}
		}
	
		$form_info = array();
		$form_info["name"] = "editlocaltrip";
		$form_info["action"] = "editlocaltrip";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "managetrips?triptype=LOCAL";
		$form_info["bredcum"] = "edit local trip";
	
		$form_fields = array();
	
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
		$paymenttypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
		$vehtype_arr = array();
		foreach ($paymenttypes  as $paymenttype){
			$vehtype_arr[$paymenttype['name']] = $paymenttype->name;
		}
	
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches  as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$values = Input::All();
		$entity = \BusBookings::where("id","=",$values["id"])->get();
		if(count($entity)>0){
			$entity = $entity[0];
			$tabs = array();
			$form_fields = array();
			$form_field = array("name"=>"customername", "value"=>$entity->cust_name, "content"=>"customer name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"phone", "value"=>$entity->cust_phone, "content"=>"phone number", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control input-mask-phone");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"altphone", "value"=>$entity->alternatephone, "content"=>"alternate  number", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control input-mask-phone");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"email", "value"=>$entity->cust_email, "content"=>"email", "readonly"=>"",  "required"=>"","type"=>"email", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"adddress", "value"=>$entity->cust_address, "content"=>"customer address", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$tab = array();
			$tab['form_fields'] = $form_fields;
			$tab['href'] = "tabone";
			$tab['heading'] = strtoupper("customer information");
			$tabs[] = $tab;
		
			$form_fields = array();
			$form_field = array("name"=>"sourcefrom", "value"=>$entity->source_start_place, "content"=>"from", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"sourceto", "value"=>$entity->source_end_place, "content"=>"to", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"sourcejourneydatetime", "value"=>date("d-m-Y", strtotime($entity->source_date))." ".$entity->source_time, "content"=>"journey date & time", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-time-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"sourcebustype", "value"=>$entity->source_bustype, "content"=>"bus type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$vehtype_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"sourcenoofbuses", "value"=>$entity->source_busno, "content"=>"no of buses", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"returntrip", "value"=>"YES", "content"=>"return trip", "readonly"=>"",  "required"=>"required","type"=>"radio", "class"=>"form-control", "options"=>array("YES"=>"YES","NO"=>"NO"));
			$form_fields[] = $form_field;
			$tab = array();
			$tab['form_fields'] = $form_fields;
			$tab['href'] = "tabtwo";
			$tab['heading'] = strtoupper("Source trip information");
			$tabs[] = $tab;
		
			$form_fields = array();
			$form_field = array("name"=>"destfrom", "value"=>$entity->dest_start_place, "content"=>"from", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"destto", "value"=>$entity->dest_end_place, "content"=>"to", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control location");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"destjourneydatetime", "value"=>date("d-m-Y", strtotime($entity->dest_date))." ".$entity->dest_time, "content"=>"journey date & time", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-time-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"destbustype", "value"=>$entity->dest_bustype, "content"=>"bus type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$vehtype_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"destnoofbuses", "value"=>$entity->dest_busno, "content"=>"no of buses", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$tab = array();
			$tab['form_fields'] = $form_fields;
			$tab['href'] = "tabthree";
			$tab['heading'] = strtoupper("return trip information");
			$tabs[] = $tab;
		
			$form_fields = array();
			$form_field = array("name"=>"routeinfo", "value"=>$entity->booking_descr, "content"=>"route information (any)", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"bookingdate", "value"=>date("d-m-Y",strtotime($entity->booking_date)), "content"=>"booking date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"bookingbranch", "value"=>$entity->booking_branch, "content"=>"booking created branch", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"totalamount", "value"=>$entity->total_cost, "content"=>"total amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fuelchargetype", "value"=>$entity->fuel_charge_type, "content"=>"fuel charge type", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array("customer"=>"CUSTOMER","company"=>"COMPANY"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$values['id'], "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$tab = array();
			$tab['form_fields'] = $form_fields;
			$tab['href'] = "tabfour";
			$tab['heading'] = strtoupper("booking & advance information");
			$tabs[] = $tab;
		
			$form_info["tabs"] = $tabs;
			return View::make("trips.edittabbedform",array("form_info"=>$form_info));
		}
	}
	
	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function editIncomeTransaction()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			if(isset($values["transtype"]) && $values["transtype"] == "income" ){
				$field_names = array("branch"=>"branchId","amount"=>"amount","paymenttype"=>"paymentType", "transtype"=>"name", "type"=>"lookupValueId",
						"branch1"=>"branchId1","incharge"=>"inchargeId","employee"=>"employeeId","vehicle"=>"vehicleIds",
						"remarks"=>"remarks","bankaccount"=>"bankAccount","chequenumber"=>"chequeNumber","issuedate"=>"issueDate",
						"transactiondate"=>"transactionDate","date1"=>"date","accountnumber"=>"accountNumber","bankname"=>"bankName"
				);
				$fields = array();
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						if($key == "transactiondate" || $key=="date1" || $key=="issuedate"){
							$fields[$val] = date("Y-m-d",strtotime($values[$key]));
						}
						else if($key == "vehicle"){
							$vehids = "";
							foreach ($values[$key] as $vehid){
								$vehids = $vehids.",".$vehid;
							}
							$vehids = substr($vehids, 1);
							$fields[$val] = $vehids;
						}
						else{
							$fields[$val] = $values[$key];
						}
					}
				}
				$transid =  strtoupper(uniqid().mt_rand(100,999));
				$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
				foreach($chars as $k=>$v){
					$transid = str_replace($k, $v, $transid);
				}
				$fields["transactionId"] = $transid;
				$fields["source"] = "income transaction";
				$db_functions_ctrl = new DBFunctionsController();
				$table = "IncomeTransaction";
				if($db_functions_ctrl->insert($table, $fields)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("transactions");
				}
				else{
					\Session::put("message","Operation Could not be completed, Try Again!");
					return \Redirect::to("transactions");
				}
			}
		}
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		
		$entity = \IncomeTransaction::where("transactionId","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$form_fields = array();	
			$form_field = array("name"=>"branch", "id"=>"branchId","value"=>$entity->branchId, "value"=>"cash", "content"=>"branch", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","neft"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"type", "id"=>"type",  "value"=>$entity->lookupValueId, "content"=>"name", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"transtype", "id"=>"transtype",  "value"=>$entity->name, "content"=>"transaction type", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"transactiondate", "id"=>"transactiondate",  "value"=>$entity->date, "content"=>"transaction date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			if($entity->inchargeId != ""){
				$form_field = array("name"=>"incharge", "id"=>"incharge",  "value"=>$entity->inchargeId, "content"=>"incharge", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
				$form_fields[] = $form_field;
			}
			$form_field = array("name"=>"amount", "id"=>"amount",  "value"=>$entity->amount, "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amount", "id"=>"amount",  "value"=>$entity->amount, "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"paymenttype", "id"=>"paymenttype","value"=>$entity->paymentType,  "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "id"=>"remarks", "value"=>$entity->remarks, "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;			
		
			$form_info["form_fields"] = $form_fields;
			return View::make("masters.layouts.edit2colform",array("form_info"=>$form_info));
		}
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editServiceProvider()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("provider1"=>"provider","branch1"=>"branchId","name1"=>"name",
					"number1"=>"number","companyname1"=>"companyName","address1"=>"address","referencename1"=>"refName",
					"referencenumber1"=>"refNumber","internetconfigurationdetails1"=>"configDetails"
				);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ServiceProvider";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("serviceproviders?provider=".$values["provider1"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("serviceproviders?provider=".$values["provider1"]);
			}
		}
	}
	
		

	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function getFields()
	{
		$values = Input::all();
		$form_fields = array();
		$form_info = array();
		$showfields = \LookupTypeValues::where("id", "=", $values["lookupvalueid"])->get();
		if(count($showfields)>0){
			$showfields = $showfields[0];
			$fields = explode(",", $showfields->fields);
			if(in_array("INCHARGE",$fields)){
				$incharges =  \InchargeAccounts::leftjoin("employee", "employee.id","=","inchargeaccounts.empid")->select(array("inchargeaccounts.id as id","employee.fullName as name"))->get();
				$incharges_arr = array();
				foreach ($incharges as $incharge){
					$incharges_arr[$incharge->id] = $incharge->name;
				}
				$form_field = array("name"=>"incharge", "content"=>"Incharge name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$incharges_arr);
				$form_fields[] = $form_field;
			}
			if(in_array("VEHICLE",$fields)){
				$vehicles =  \Vehicle::All();
				$vehicles_arr = array();
				foreach ($vehicles as $vehicle){
					$vehicles_arr[$vehicle->id] = $vehicle->veh_reg;
				}
				$form_field = array("name"=>"vehicle[]", "content"=>"vehicle reg no", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
				$form_fields[] = $form_field;
			}
			if(in_array("BRANCH",$fields)){
				$branches =  \OfficeBranch::All();
				$branches_arr = array();
				foreach ($branches as $branch){
					$branches_arr[$branch->id] = $branch->name;
				}
				$form_field = array("name"=>"branch1", "content"=>"Branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$branches_arr);
				$form_fields[] = $form_field;
			}
			if(in_array("EMPLOYEE",$fields)){
				$employees =  \OfficeBranch::All();
				$employees_arr = array();
				foreach ($employees as $employee){
					$employees_arr[$employee->id] = $employee->empCode." - ".$employee->fullName;
				}
				$form_field = array("name"=>"employee", "content"=>"Branch name", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$employees_arr);
				$form_fields[] = $form_field;
			}
		}
		$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		return view::make("trips.paymentform",array("form_info"=>$form_info));
	}
	
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function getFuelTransactionFields()
	{
		$form_fields = array();
		$form_info = array();
		
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$states =  \State::Where("status","=","ACTIVE")->get();
		$state_arr = array();
		foreach ($states as $state){
			$state_arr[$state['id']] = $state->name;
		}
		
		$vehicles =  \Vehicle::all();
		$vehicles_arr = array();
		foreach ($vehicles as $vehicle){
			$vehicles_arr[$vehicle['veh_id']] = $vehicle->veh_reg;
		}
		
		$select_fields = array();
		$select_fields[] = "fuelstationdetails.name as name";
		$select_fields[] = "cities.name as cityname";
		$select_fields[] = "fuelstationdetails.id as id";
		
		$fuelstations =  \FuelStation::leftjoin("cities","cities.id","=","fuelstationdetails.cityId")->select($select_fields)->get();
		$fuelstations_arr = array();
		foreach ($fuelstations as $fuelstation){
			$fuelstations_arr[$fuelstation['id']] = $fuelstation->name." - ".$fuelstation->cityname;
		}

		/*
		$form_field = array("name"=>"transactionbranch", "content"=>"transaction branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control",  "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"filldate", "content"=>"fill date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		*/
		$form_field = array("name"=>"vehicleno", "content"=>"vehicle number", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select",  "options"=>$vehicles_arr);
		$form_fields[] = $form_field;
		/*
		$form_field = array("name"=>"statename", "content"=>"state name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange","script"=>"changeState(this.value)"), "options"=>$state_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"cityname", "content"=>"city name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>array(), "class"=>"form-control");
		$form_fields[] = $form_field;
		*/
		$form_field = array("name"=>"fuelstationname", "content"=>"fuel station name", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$fuelstations_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"startreading", "content"=>"start reading", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"litres", "content"=>"litres", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"priceperlitre", "content"=>"price per litre", "readonly"=>"",  "required"=>"required", "type"=>"text", "action"=>array("type"=>"onChange","script"=>"calcTotal()"), "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control number");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"required", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentpaid", "value"=>"No", "content"=>"payment paid", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "action"=>array("type"=>"onChange","script"=>"enablePaymentType(this.value)"), "options"=>array("Yes"=>"YES","No"=>"NO"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>"cash", "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		$form_fields[] = $form_field;
			
		$form_info["form_fields"] = $form_fields;
		return view::make("transactions.paymentform",array("form_info"=>$form_info));
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function cancelDailyTrip()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$url = "editdailytrip?id=".$values["id"]."&triptype=DAILY";
			\DB::beginTransaction();
			$db_functions_ctrl = new DBFunctionsController();
			$table = "TripDetails";
			try{
				$table = "\TripDetails";
				$fields = array("status"=>"Cancelled");
				$data = array("id"=>$values["id"]);
				$tripexpid = $db_functions_ctrl->update($table, $fields, $data);
				$table = "\TripServiceDetails";
				$fields = array("status"=>"Cancelled");
				$data = array("id"=>$values["id"]);
				$tripexpid = $db_functions_ctrl->update($table, $fields, $data);
				\TripParticulars::where('tripId',$values["id"])->where('tripType',"DAILY")->update(['status' => "INACTIVE"]);
			}
			catch(\Exception $ex){
				\Session::put("message","Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			\DB::commit();
			\Session::put("message","Operation completed successfully!");
			return \Redirect::to($url);
		}
	}
	
	public function unCancelDailyTrip()
	{
		$values = Input::all();
		if (\Request::isMethod('get'))
		{
			$url = "editdailytrip?id=".$values["id"]."&triptype=DAILY";
			\DB::beginTransaction();
			$db_functions_ctrl = new DBFunctionsController();
			$table = "TripDetails";
			try{
				$table = "\TripDetails";
				$fields = array("status"=>"Running");
				$data = array("id"=>$values["id"]);
				$tripexpid = $db_functions_ctrl->update($table, $fields, $data);
				$table = "\TripServiceDetails";
				$fields = array("status"=>"Running");
				$data = array("id"=>$values["id"]);
				$tripexpid = $db_functions_ctrl->update($table, $fields, $data);
				\TripParticulars::where('tripId',$values["id"])->where('tripType',"DAILY")->update(['status' => "ACTIVE"]);
			}
			catch(\Exception $ex){
				\Session::put("message","Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			\DB::commit();
			\Session::put("message","Operation completed successfully!");
			return \Redirect::to($url);
		}
	}
	
	public function cancelLocalTrip()
	{
		$values = Input::all();
		if (\Request::isMethod('get'))
		{
			$fields = array("status"=>"Cancelled");
			if(isset($values["action"]) && $values["action"]=="uncancel"){
				$fields = array("status"=>"Pending");
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BusBookings";
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				echo "success";
				return;
			}
			echo "fail";
			return;
		}
	}
	

	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function editDailyTrip()
	{
		$values = Input::all();
		
		if (\Request::isMethod('post'))
		{
			$field_names = array("service"=>"serviceId", "driver1"=>"driver1","driver2"=>"driver2","helper"=>"helper","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\TripServiceDetails";
			$data = array("id"=>$values["id1"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				$tripid = \TripServiceDetails::where("id","=",$values["id1"])->first();
				$tripid = $tripid->tripId;
				$trip = \TripDetails::where("id","=",$tripid)->first();
				$tripcount = ($trip->routeCount)-1;
				$table = "\TripDetails";
				$data = array("id"=>$tripid);
				$fields = array("routeCount"=>$tripcount);
				if($db_functions_ctrl->update($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("editdailytrip?id=".$values["tripid"]."&triptype=".$values["triptype"]);
				}
			}
			\Session::put("message","Operation Could not be completed, Try Again!");
			return \Redirect::to("editdailytrip?id=".$values["tripid"]."&triptype=".$values["triptype"]);
		}
		
		$form_fields = array();
		$select_args = array();
		$select_args[] = "vehicle.veh_reg as veh_reg";
		$select_args[] = "tripdetails.tripStartDate as tripStartDate";
		$select_args[] = "tripdetails.tripStartDate as tripStartDate";
		$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
		$select_args[] = "tripdetails.routeCount as routeCount";
		$select_args[] = "tripdetails.id as id";
		
		$entity = \TripDetails::where("tripdetails.id","=", $values['id'])->leftjoin("vehicle","vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
		if(count($entity)){
			$entity = $entity[0];
			$tripCloseDate = date("d-m-Y",strtotime($entity->tripCloseDate));
			if($tripCloseDate == "01-01-1970"){
				$tripCloseDate = "NOT CLOSED";
			}
			
			$values = Input::all();
			$values['bredcum'] = "DAILY TRIP ROUTES";
			$values['home_url'] = 'dailytrips';
			$values['add_url'] = 'managedailytips';
			$values['form_action'] = 'editdailytrip';
			$values['action_val'] = '';
			$theads = array('Route no#','service no','service date', "driver1", "driver2", "helper","Actions");
			$values["theads"] = $theads;
			
			$form_info = array();
			$form_info["name"] = "canceldailytrip";
			$form_info["action"] = "canceldailytrip";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "managedailytrips";
			$form_info["bredcum"] = "vehicle trip details";
			
			$form_fields = array();
			
			$form_fields = array();
			$form_field = array("name"=>"vehicle", "value"=>$entity->veh_reg,  "content"=>"vehicle reg no", "readonly"=>"readonly",  "required"=>"", "type"=>"text",  "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden" );
			$form_fields[] = $form_field;
			$form_field = array("name"=>"routecount", "value"=>$entity->routeCount, "content"=>"Trip route count", "readonly"=>"readonly",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tripstartdate", "value"=>date("d-m-Y",strtotime($entity->tripStartDate)), "content"=>"trip start date", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tripclosedate", "value"=>$tripCloseDate, "content"=>"trip close date", "readonly"=>"readonly",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			$values['form_info'] = $form_info;
			
			$select_args = array();
			$select_args[] = "cities.name as sourceCity";
			$select_args[] = "cities1.name as destinationCity";
			$select_args[] = "servicedetails.serviceNo as serviceNo";
			$select_args[] = "servicedetails.active as active";
			$select_args[] = "servicedetails.serviceStatus as serviceStatus";
			$select_args[] = "servicedetails.id as id";
			$services = \ServiceDetails::where("active","=","Yes")->where("serviceStatus","=","ACTIVE")->join("cities","cities.id","=","servicedetails.sourceCity")->join("cities as cities1","cities1.id","=","servicedetails.destinationCity")->select($select_args)->get();
			$services_arr = array();
			foreach ($services as $service){
				$services_arr[$service['id']] = $service->serviceNo." (".$service->sourceCity." - ".$service->destinationCity.")";
			}
			$drivers = \Employee::where("roleId","=",19)->get();
			$helpers = \Employee::where("roleId","=",20)->get();
			$drivers_arr = array();
			foreach ($drivers as $driver){
				$drivers_arr[$driver['id']] = $driver['fullName'];
			}
			$helpers_arr = array();
			foreach ($helpers as $helper){
				$helpers_arr[$helper['id']] = $helper['fullName'];
			}
			$form_info = array();
			$form_fields = array();
			$form_info["name"] = "edit";
			$form_info["action"] = "editdailytrip?triptype=".$values["triptype"];
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "cities";
			$form_info["bredcum"] = "add city";
			$form_field = array("name"=>"service", "content"=>"service no", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$services_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"servicedate", "content"=>"service date", "readonly"=>"readonly",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"driver1", "content"=>"driver1", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"driver2", "content"=>"driver2", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"helper", "content"=>"helper", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$helpers_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status",  "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array("Running"=>"Running","Cancelled"=>"Cancel"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tripid",  "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			
			$form_info["form_fields"] = $form_fields;
			$modals = array();
			$modals[] = $form_info;
			$values["modals"] = $modals;
			
			$values['provider'] = "serviceroutes&tripid=".$values["id"];
			return View::make('trips.lookupdatatable', array("values"=>$values));
		}
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function editTripParticular()
	{
		$values = Input::all();
		//$values["Sdf"];
		if (\Request::isMethod('post'))
		{
			if(isset($values["date1"])) {$values["date1"] = date("Y-m-d",strtotime($values["date1"]));}
			$field_names = array("lookupvalue1"=>"lookupValueId", "amount1"=>"amount","remarks1"=>"remarks","branch2"=>"branchId","date1"=>"date","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\TripParticulars";
			
			$triptype = "";
			$triptype = $table::where("id","=",$values["id1"])->get();
			if(count($triptype)>0){
				$triptype = $triptype[0];
				$triptype = $triptype->tripType;
			}
			
			$data = array("id"=>$values["id1"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				if($triptype == "LOCAL"){
					return \Redirect::to("addlocaltripparticular?id=".$values["tripid"]."&type=".$values['type']);
				}
				else{
					return \Redirect::to("addtripparticular?id=".$values["tripid"]."&type=".$values['type']);
				}
			}
			\Session::put("message","Operation Could not be completed, Try Again!");
			if($triptype == "LOCAL"){
				return \Redirect::to("addlocaltripparticular?id=".$values["tripid"]."&type=".$values['type']);
			}
			else{
				return \Redirect::to("addtripparticular?id=".$values["tripid"]."&type=".$values['type']);
			}
			
			
		}
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function closeTrip()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["dsf"];
			$field_names = array("lastreading"=>"9001", "initialreading"=>"9002","closingreading"=>"9003","wastedmeters"=>"9004");
			if(isset($values["triptype"]) &&  $values["triptype"]=="local"){
				$i= 0;
				while($i<count($values["vehicleid"])){
					foreach ($field_names as $key=>$val){
						if(isset($values[$key])){
							$fields = array();
							$fields["lookupValueId"] = $val;
							$fields["date"] = date("Y-m-d",strtotime($values["closingdate"][$i]));
							$fields["tripId"] = $values["tripid"];
							$fields['tripType'] = "LOCAL";
							$fields['remarks'] = $values["remarks"][$i];
							$fields['vehicleId'] = $values["vehicleid"][$i];
							$db_functions_ctrl = new DBFunctionsController();
							$table = "\TripParticulars";
							if(!$db_functions_ctrl->insert($table, $fields)){
								\Session::put("message","Operation Could not be completed, Try Again!");
								return \Redirect::to("addlocaltripparticular?id=".$values["tripid"]."&type=expenses_and_incomes");
							}
						}
					}
					$i++;
				}
				/*$db_functions_ctrl = new DBFunctionsController();
				$table = "\TripDetails";
				$fields = array();
				$fields["tripCloseDate"] = date("Y-m-d",strtotime($values['closingdate']));
				$fields["status"] = "Closed";
				$data = array("id"=>$values["tripid"]);
				if($db_functions_ctrl->update($table, $fields, $data)){
				}
				*/
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("addlocaltripparticular?id=".$values["tripid"]."&type=expenses_and_incomes");
				
			}
			else{
				foreach ($field_names as $key=>$val){
					if(isset($values[$key])){
						$fields = array();
						$fields["lookupValueId"] = $val;
						$fields["date"] = date("Y-m-d");
						$fields["tripId"] = $values["tripid"];
						$fields['remarks'] = $values[$key];
						$fields['tripType'] = "DAILY";
						$db_functions_ctrl = new DBFunctionsController();
						$table = "\TripParticulars";
						$db_functions_ctrl->insert($table, $fields);
					}
				}
				$db_functions_ctrl = new DBFunctionsController();
				$table = "\TripDetails";
				$fields = array();
				$fields["tripCloseDate"] = date("Y-m-d",strtotime($values['closingdate']));
				$fields["status"] = "Closed";
				$data = array("id"=>$values["tripid"]);
				if($db_functions_ctrl->update($table, $fields, $data)){
					\Session::put("message","Operation completed Successfully");
					return \Redirect::to("addtripparticular?id=".$values["tripid"]."&type=expenses_and_incomes");
				}
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("addtripparticular?id=".$values["tripid"]."&type=expenses_and_incomes");
			}
		}
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function addTripParticular()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$message = "Following Items successfully Added : <br/><span style=\"font-weight:bold;color:green;\">";
			if(isset($values["lookupvalue"])){
				$ids = $values["lookupvalue"];
				$i = 0;
				foreach ($ids as $id){
					if(isset($values["lookupvalue"][$i]) && $values["lookupvalue"][$i]!="" && isset($values["amount"][$i]) && $values["amount"][$i]!=""){
						$field_names = array("lookupvalue"=>"lookupValueId", "amount"=>"amount","remarks"=>"remarks","id"=>"tripId","branch"=>"branchId","incharge"=>"inchargeId","bank"=>"bankId","vehicle"=>"vehicleId");
						$fields = array();
						foreach ($field_names as $key=>$val){
							if(isset($values[$key][$i])){
								$fields[$val] = $values[$key][$i];
							}
						}
						$fields["date"] = date("Y-m-d");
						if(isset($values["date"][$i])){
							$fields["date"] = date("Y-m-d",strtotime($values["date"][$i]));
						}
						$fields["tripId"] = $values["tripid"];
						$fields["tripType"] = $values["triptype"];
						$db_functions_ctrl = new DBFunctionsController();
						$table = "\TripParticulars";
						if($db_functions_ctrl->insert($table, $fields)){
							$item = \LookupTypeValues::where("id","=",$values["lookupvalue"][$i])->get();
							if(count($item)>0){
								$item = $item[0];
								$message = $message.$item->name.", ";
							}
						}
					}
					$i++;
				}
			}
			$message = $message."</span>";
			\Session::put("message",$message);
			return \Redirect::to("addtripparticular?id=".$values["tripid"]."&type=".$values['type']);
		}
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "vehicle.veh_reg as veh_reg";
		$select_args[] = "tripdetails.tripStartDate as tripStartDate";
		$select_args[] = "tripdetails.tripStartDate as tripStartDate";
		$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
		$select_args[] = "tripdetails.routeCount as routeCount";
		$select_args[] = "tripdetails.id as id";
	
		$entity = \TripDetails::where("tripdetails.id","=", $values['id'])->leftjoin("vehicle","vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
		if(count($entity)){
			$entity = $entity[0];
			$tripCloseDate = date("d-m-Y",strtotime($entity->tripCloseDate));
			if($tripCloseDate == "01-01-1970"){
				$tripCloseDate = "NOT CLOSED";
			}
				
			$values = Input::all();
			$values['bredcum'] = "MANAGE TRIP PARTICULARS";
			$values['home_url'] = 'dailytrips';
			$values['add_url'] = 'addtripparticular';
			$values['form_action'] = 'addtripparticular';
			$values['action_val'] = '';
				
			$form_info = array();
			$form_info["name"] = "addtripparticular";
			$form_info["action"] = "addtripparticular";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "managedailytrips";
			$form_info["bredcum"] = "add TRIP PARTICULARS";
				
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","TRIP EXPENSES")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			$tripparticulars_arr = array();
			foreach ($tripparticulars as $tripparticular){
				$tripparticulars_arr [$tripparticular['id']] = $tripparticular->name;
			}
			$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			foreach ($tripparticulars as $tripparticular){
				$tripparticulars_arr [$tripparticular['id']] = $tripparticular->name;
			}
			$parent = \LookupTypeValues::where("name","=","TRIP INCOMES")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			foreach ($tripparticulars as $tripparticular){
				$tripparticulars_arr [$tripparticular['id']] = $tripparticular->name;
			}
			$value_name_arr = array("9999"=>"DEBITED FROM BRANCH", "8888"=>"CREDITED TO BRANCH", "9001"=>"Last Closing Reading", "9002"=>"Initial Reading", "9003"=>"Closing Reading", "9004"=>"Wasted Meters", "9005"=>"Meter Reading Remarks");
			foreach ($value_name_arr as $key=>$value){
				$tripparticulars_arr [$key] = $value;
			}	
			$form_fields = array();
			$form_field = array("name"=>"lookupvalue", "content"=>"Trip Particular name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFields(this.value);"), "options"=>$tripparticulars_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden" );
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			$values['form_info'] = $form_info;
				
			$branches =  \OfficeBranch::All();
			$branches_arr = array();
			foreach ($branches as $branch){
				$branches_arr[$branch->id] = $branch->name;
			}
			$select_args = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "inchargeaccounts.id as id";
			$incharges = \InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empid")->select($select_args)->get();
			$incharges_arr = array();
			foreach ($incharges as $incharge){
				$incharges_arr[$incharge->id] = $incharge->name;
			}
			$form_info = array();
			$form_fields = array();
			$form_info["name"] = "edit";
			$form_info["action"] = "edittripparticular";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "cities";
			$form_info["bredcum"] = "add city";
			$form_field = array("name"=>"lookupvalue1", "content"=>"particular name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$tripparticulars_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"date1", "content"=>"date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amount1", "content"=>"amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch2", "content"=>"branch", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"incharge1", "content"=>"incharge", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$incharges_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks1", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status",  "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tripid",  "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"type",  "value"=>$values["type"], "content"=>"", "readonly"=>"readonly",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
				
			$form_info["form_fields"] = $form_fields;
			$modals = array();
			$modals[] = $form_info;
			$values["modals"] = $modals;
			
			$theads = array('Trip id','particular name','type', "date", "amount", "branch/incharge", "remarks", "status", "Actions");
			$values["theads"] = $theads;
			
			if(isset($values['type']) && $values['type'] == "advances"){
				$values["advances"] = 1;
			}
			if(isset($values['type']) && $values['type'] == "expenses_and_incomes"){
				$values["expenses"] = 1;
			}
			
			$values['provider'] = "tripparticulars&tripid=".$values["id"];
			return View::make('trips.tripparticularsdatatable', array("values"=>$values));
		}
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function addLocalTripParticular()
	{
		$values = Input::all();
		
		if (\Request::isMethod('post'))
		{
			//$values["dsf"];
			$message = "Following Items successfully Added : <br/><span style=\"font-weight:bold;color:green;\">";
			if(isset($values["lookupvalue"])){
				$ids = $values["lookupvalue"];
				$i = 0;
				foreach ($ids as $id){
					if(isset($values["lookupvalue"][$i]) && $values["lookupvalue"][$i]!="" && isset($values["amount"][$i]) && $values["amount"][$i]!=""){
						$field_names = array("lookupvalue"=>"lookupValueId", "amount"=>"amount","remarks"=>"remarks","id"=>"tripId","branch"=>"branchId","incharge"=>"inchargeId","bank"=>"bankId","vehicle"=>"vehicleId");
						$fields = array();
						foreach ($field_names as $key=>$val){
							if(isset($values[$key][$i])){
								$fields[$val] = $values[$key][$i];
							}
						}
						$fields["date"] = date("Y-m-d");
						if(isset($values["date"][$i])){
							$fields["date"] = date("Y-m-d",strtotime($values["date"][$i]));
						}
						$fields["tripId"] = $values["tripid"];
						$fields["tripType"] = $values["triptype"];
						$db_functions_ctrl = new DBFunctionsController();
						$table = "\TripParticulars";
						if($db_functions_ctrl->insert($table, $fields)){
							$item = \LookupTypeValues::where("id","=",$values["lookupvalue"][$i])->get();
							if(count($item)>0){
								$item = $item[0];
								$message = $message.$item->name.", ";
							}
						}
					}
					$i++;
				}
			}
			$message = $message."</span>";
			\Session::put("message",$message);
			return \Redirect::to("addlocaltripparticular?id=".$values["tripid"]."&bookingid=".$values["tripid"]."&type=".$values['type']);
		}
	
		$form_fields = array();
		$select_args = array();
		$select_args[] = "busbookings.booking_number as booking_number";
		$select_args[] = "busbookings.cust_name as sourcetrip";
		$select_args[] = "busbookings.cust_name as returntrip";
		$select_args[] = "busbookings.cust_name as custinfo";
		$select_args[] = "busbookings.booking_number as journeyinfo";
		$select_args[] = "busbookings.booking_number as amount";
		$select_args[] = "busbookings.source_start_place as source_start_place";
		$select_args[] = "busbookings.source_end_place as source_end_place";
		$select_args[] = "busbookings.dest_start_place as dest_start_place";
		$select_args[] = "busbookings.dest_end_place as dest_end_place";
		$select_args[] = "busbookings.cust_name as cust_name";
		$select_args[] = "busbookings.cust_phone as cust_phone";
		$select_args[] = "busbookings.source_date as source_date";
		$select_args[] = "busbookings.source_time as source_time";
		$select_args[] = "busbookings.dest_date as dest_date";
		$select_args[] = "busbookings.dest_time as dest_time";
		$select_args[] = "busbookings.total_cost as total_cost";
		$select_args[] = "busbookings.id as id";
	
		$entity = \BusBookings::where("id","=",$values['id'])->select($select_args)->get();
		if(count($entity)){
			$entity = $entity[0];
			$tripCloseDate = date("d-m-Y",strtotime($entity->tripCloseDate));
			if($tripCloseDate == "01-01-1970"){
				$tripCloseDate = "NOT CLOSED";
			}
	
			$values = Input::all();
			$values['bredcum'] = "MANAGE TRIP PARTICULARS";
			$values['home_url'] = 'localtrips';
			$values['add_url'] = 'addlocaltripparticular';
			$values['form_action'] = 'addlocaltripparticular';
			$values['action_val'] = '';
	
			$form_info = array();
			$form_info["name"] = "addlocaltripparticular";
			$form_info["action"] = "addlocaltripparticular";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "managelocaltrips";
			$form_info["bredcum"] = "add TRIP PARTICULARS";
	
			$parentId = -1;
			$parent = \LookupTypeValues::where("name","=","TRIP EXPENSES")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			$tripparticulars_arr = array();
			foreach ($tripparticulars as $tripparticular){
				$tripparticulars_arr [$tripparticular['id']] = $tripparticular->name;
			}
			$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			foreach ($tripparticulars as $tripparticular){
				$tripparticulars_arr [$tripparticular['id']] = $tripparticular->name;
			}
			$parent = \LookupTypeValues::where("name","=","TRIP INCOMES")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
			foreach ($tripparticulars as $tripparticular){
				$tripparticulars_arr [$tripparticular['id']] = $tripparticular->name;
			}
			$value_name_arr = array("9999"=>"DEBITED FROM BRANCH", "8888"=>"CREDITED TO BRANCH", "9001"=>"Last Closing Reading", "9002"=>"Initial Reading", "9003"=>"Closing Reading", "9004"=>"Wasted Meters", "9005"=>"Meter Reading Remarks");
			foreach ($value_name_arr as $key=>$value){
				$tripparticulars_arr [$key] = $value;
			}
			$form_fields = array();
			$form_field = array("name"=>"lookupvalue", "content"=>"Trip Particular name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"getFields(this.value);"), "options"=>$tripparticulars_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden" );
			$form_fields[] = $form_field;
			$form_info["form_fields"] = $form_fields;
			$values['form_info'] = $form_info;
	
			$branches =  \OfficeBranch::All();
			$branches_arr = array();
			foreach ($branches as $branch){
				$branches_arr[$branch->id] = $branch->name;
			}
			$select_args = array();
			$select_args[] = "employee.fullName as name";
			$select_args[] = "inchargeaccounts.id as id";
			$incharges = \InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empid")->select($select_args)->get();
			$incharges_arr = array();
			foreach ($incharges as $incharge){
				$incharges_arr[$incharge->id] = $incharge->name;
			}
			$form_info = array();
			$form_fields = array();
			$form_info["name"] = "edit";
			$form_info["action"] = "edittripparticular";
			$form_info["method"] = "post";
			$form_info["class"] = "form-horizontal";
			$form_info["back_url"] = "cities";
			$form_info["bredcum"] = "add city";
			$form_field = array("name"=>"lookupvalue1", "content"=>"particular name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$tripparticulars_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"date1", "content"=>"date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"amount1", "content"=>"amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control number");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"branch2", "content"=>"branch", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"incharge1", "content"=>"incharge", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$incharges_arr);
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks1", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status",  "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"));
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tripid",  "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"type",  "value"=>$values["type"], "content"=>"", "readonly"=>"readonly",  "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
	
			$form_info["form_fields"] = $form_fields;
			$modals = array();
			$modals[] = $form_info;
			$values["modals"] = $modals;
				
			$theads = array('Trip id','particular name','type', "date", "amount", "branch/incharge", "bank", "vehicle", "remarks", "status", "Actions");
			$values["theads"] = $theads;
				
			if(isset($values['type']) && $values['type'] == "advances"){
				$values["advances"] = 1;
			}
			if(isset($values['type']) && $values['type'] == "expenses_and_incomes"){
				$values["expenses"] = 1;
			}
				
			$values['provider'] = "localtripparticulars&tripid=".$values["id"];
			return View::make('trips.localtripparticularsdatatable', array("values"=>$values));
		}
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function bookingRefund()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$url = "bookingrefund?id=".$values["tripid"]."&triptype=LOCAL&transtype=bookingrefund";
			$entities = \BusBookings::where("id","=",$values["tripid"])->get();
			foreach ($entities as $entity){
				$values["booking_number"] = $entity->booking_number;
				$values["status"] = "Approved";
			}
			$field_names = array("booking_number"=>"booking_number","totaladvance"=>"advance_amount","returnedamount"=>"returnedAmount",
					"branch"=>"debited_branch","paymentdate"=>"payment_date","status"=>"status","remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($key=="paymentdate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else {
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BusBookingReturnedAmounts"; 
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to($url);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to($url);
			}
		}
		
		$values['bredcum'] = "MANAGE LOCAL TRIPS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&";
			if(isset($values["id"])){
				$url = $url."tripid=".$values["id"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();;
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "bookingrefund";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		$form_info["tripid"] = $values["id"];
	
		$form_fields = array();
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$branches =  \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		$form_field = array("name"=>"totaladvance", "content"=>"customer advance", "readonly"=>"readonly",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"returnedamount", "content"=>"returned amount", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branch", "content"=>"debited branch", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentdate", "content"=>"payment date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"", "type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('trips.bookingrefunddatatable', array("values"=>$values));
	}
	
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function addLocalTripFuel()
	{
		$values = Input::all();
		$values['bredcum'] = "MANAGE LOCAL TRIPS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&";
			if(isset($values["id"])){
				$url = $url."tripid=".$values["id"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();;
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		$form_info["tripid"] = $values["id"];
	
		$form_fields = array();
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('trips.localtripfueldatatable', array("values"=>$values));
	}
	/**
	 * edit a Office Branch.
	 *
	 * @return Response
	 */
	public function addTripFuel()
	{
		$values = Input::all();
		$values['bredcum'] = "MANAGE DAILY TRIPS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		if(isset($values["transtype"]) && $values["transtype"]=="fuel"){
			$theads = array('branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', "Actions");
			$values["theads"] = $theads;
			$url = "fuel&";
			if(isset($values["id"])){
				$url = $url."tripid=".$values["id"];
			}
			$values["provider"]= $url;
		}
		else{
			$values["theads"] = array();
			$values["tds"] = array();;
			$entities = array();
			$total = 0;
		}
			
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		$form_info["tripid"] = $values["id"];
	
		$form_fields = array();
		$val = "";
		if(!isset($values["provider"])){
			$values["provider"] = "";
		}
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		$values["modals"] = $modals;
		return View::make('trips.tripfueldatatable', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function assignDriverVehicle()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$message = "The following vehicles assigned successfully : <br/>";
			$vehicles = $values["vehicle"];
			$vehnames = \Vehicle::all();
			$vehnames_arr = array();
			foreach ($vehnames as $vehname){
				$vehnames_arr[$vehname->id] = $vehname->veh_reg;
			}
			for($i=0; $i<count($vehicles); $i++){
				if($vehicles[$i] != ""){
					$field_names = array("vehicle"=>"vehicleId","vehicleno"=>"vehicleno","tripfrom"=>"tripFrom","drivers1"=>"driver1","drivers2"=>"driver2","helper"=>"helper");
					$fields = array();
					foreach ($field_names as $key=>$val){
						$fields[$val] = $values[$key][$i];
					}
					$fields["booking_number"] = $values["bookingnumber"];
					$fields["status"] = "Pending";
					$db_functions_ctrl = new DBFunctionsController();
					$table = "\BookingVehicles";
					if($db_functions_ctrl->insert($table,$fields)){
						$message = $message.$vehnames_arr[$values["vehicle"][$i]].", ";
					}
				}
			}
			\Session::put("message",$message);
			return \Redirect::to("assigndrivervehicle?id=".$values['tripid']);
		}
		
		$values['bredcum'] = "assign driver vehicle";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "assigndrivervehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "#";
		$form_info["bredcum"] = "assign driver vehicle";
		$values["form_info"] = $form_info;
		
		$theads = array('Booking num#','Vehicle', "Driver1", "Driver2", "helper", "Actions");
		$values["theads"] = $theads;
		
		$drivers = \Employee::where("roleId","=",19)->get();
		$drivers_arr = array();
		foreach($drivers as $driver){
			$drivers_arr[$driver->id] = $driver->fullName;
		}
		$helpers = \Employee::where("roleId","=",20)->get();
		$helpers_arr = array();
		foreach($helpers as $helper){
			$helpers_arr[$helper->id] = $helper->fullName;
		}
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editassignedvehicle";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_field = array("name"=>"driver11", "content"=>"Driver1", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"driver21", "content"=>"driver2", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$drivers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"helper1", "content"=>"helper", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$helpers_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"tripid",  "value"=>$values["id"], "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;

		$modals = array();
		$form_info["form_fields"] = $form_fields;
		$modals["form_info"] = $form_info;
		$values["modals"] = $modals;
			
		return View::make('trips.assigndrivervehicle', array("values"=>$values));
	}
	
	public function editassignedvehicle()
	{
		$values = Input::all();
		//$values["test"];
		if (\Request::isMethod('post'))
		{
			$field_names = array("driver11"=>"driver1","driver21"=>"driver2","helper1"=>"helper","id1"=>"id");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BookingVehicles";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("assigndrivervehicle?id=".$values['tripid']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("assigndrivervehicle?id=".$values['tripid']);
			}
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function showDailyTrips()
	{
		$values = Input::all();
		$values['bredcum'] = "DAILYTRIPS";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
		
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;

		$form_info = array();
		$form_info["name"] = "transactionform";
		$form_info["action"] = "addtransaction";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "add transaction";
		
		$form_fields = array();
		$form_info["form_fields"] = $form_fields;		
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		return View::make('trips.datatable', array("values"=>$values));
	}
	
/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageTrips()
	{
		$values = Input::all();
		$values['bredcum'] = "MANAGE ".$values['triptype']." TRIPS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addvehicle';
		$values['form_action'] = 'vehicles';
		$values['action_val'] = '';
		
		$action_val = "";
		if(isset($values["daterange"])){
			$action_val = "&daterange=".$values['daterange'];
		}
		$links = array();

		$values['action_val'] = $action_val;
		$values['links'] = $links;
			
		if($values['triptype'] == "DAILY"){
			$theads = array('Vehicle Reg No','Start Date', "Route Information", "Close Date", "Routes", "Advance", "Fuel", "Expenses", "Income", "Actions");
			$values["theads"] = $theads;
		}
		if($values['triptype'] == "LOCAL"){
			$theads = array('booking no','trip information', "return trip Information", "Customer", "journey date & time", "amount", "Actions");
			$values["theads"] = $theads;
		}
			
		$actions = array();
		$action = array("url"=>"editvehicle?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		//Code to add modal forms
		$modals =  array();
			
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
		$values["form_info"] = $form_info;
		if($values["triptype"] == "DAILY"){
			$values["provider"] = "dailytrips".$action_val;
		}
		if($values["triptype"] == "LOCAL"){
			$values["provider"] = "localtrips".$action_val;
			if(isset($values['bookingtype'])){
				$values["provider"] = $values["provider"]."&bookingtype=".$values['bookingtype'];
			}
		}
				
		return View::make('trips.managetripsdatatable', array("values"=>$values));
	}
	
	public function tripClosingReport(){
		return View::make('trips.tripclosingreport');
	}
	
	public function printLocalTrip(){
		return View::make('trips.printlocaltrip');
	}
	
	public function deleteBooking(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$table = "\BusBookings"; 
		$fields = array("status"=>"Deleted");
		$data = array("id"=>$values["bookingid"]);
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
			return;
		}
		echo "fail";
		return;
	}
	
}
