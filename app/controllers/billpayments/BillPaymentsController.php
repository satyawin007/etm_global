<?php namespace billpayments;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
class BillPaymentsController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addBillPayment()
	{
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$values = Input::all();
			$field_names = array("billno"=>"billNo","billdate"=>"billDate","paiddate"=>"paidDate",
					"totalamount"=>"totalAmount","amountpaid"=>"amountPaid","clientname"=>"clientId","parentbill"=>"parentBillId",
					"billparticulars"=>"billParticulars","remarks"=>"remarks");
			$fields = array();
			
			foreach ($field_names as $key=>$val){
			if(isset($values[$key])){
					if ($key == "billdate" || $key=="paiddate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
					
				}
			}
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			if(isset($values["existing_bills"]) && $values["existing_bills"] == "YES"){
				$fields["transctionType"] = "Existing Bills";
			}
			else if(isset($values["bulk_payment"]) && $values["bulk_payment"] == "YES"){
				$fields["transctionType"] = "Bulk Payment";
			}
			else{
				$fields["transctionType"] = "";
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "BillPayments";
			
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("billpayments");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("billpayments");
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
	public function editBillPayments()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			//$values["test"];
			$values = Input::all();
			$field_names = array("billno1"=>"billNo","billdate1"=>"billDate","paiddate1"=>"paidDate",
					"totalamount1"=>"totalAmount","amountpaid1"=>"amountPaid","clientname1"=>"clientId","parentbill1"=>"parentBillId",
					"billparticulars1"=>"billParticulars","remarks1"=>"remarks","status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
			if(isset($values[$key])){
					if ($key == "billdate1" || $key=="paiddate1"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else{
						$fields[$val] = $values[$key];
					}
					
				}
			}
			if (isset($values["billfile1"]) && Input::hasFile('billfile1') && Input::file('billfile1')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile1')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile1')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$data = array('id'=>$values['id1']);
			$db_functions_ctrl = new DBFunctionsController();
			$table = "BillPayments";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("billpayments");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("billpayments");
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
	
	public function manageBillPayments()
	{
		$values = Input::all();
		$values['bredcum'] = "BILLS & PAYMENTS";
		$values['home_url'] = 'masters';
		$values['add_url'] = 'addbill';
		$values['form_action'] = 'bill';
		$values['action_val'] = '';
		$theads = array('Bill No','Bill Date', "Paid Date", "Total Amount", "Amount Paid","Due Amount","Client","Bill Particulars","Transaction","Remarks","Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editcity?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		if(!isset($values['entries'])){
			$values['entries'] = 10;
		}
	
		$form_info = array();
		$form_info["name"] = "addbillpayment";
		$form_info["action"] = "addbillpayment";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "billpayments";
		$form_info["bredcum"] = "add bill payment";
	
		$form_fields = array();
		$clients = AppSettingsController::getEmpClients();
		$client_arr = array();
		foreach ($clients as $client){
			$client_arr[$client['id']] = $client['name'];
		}
		
		$parentbills = \BillPayments::where("bill_payments.status","=","ACTIVE")
						->join("clients","bill_payments.clientId","=","clients.id")
						->groupBy('bill_payments.billNo')
						->groupBy('clients.name')
						->select(array("clients.name as name", "bill_payments.billNo as billNo", "bill_payments.id as id"))->get();
		$parentbills_arr = array();
		foreach ($parentbills as $parentbill){
			$parentbills_arr[$parentbill['id']] = $parentbill["billNo"]." ( ".$parentbill['name']." ) ";
		}
		$form_field = array("name"=>"parentbill","id"=>"parentbill", "content"=>"parent bill", "readonly"=>"",  "required"=>"","action"=>array("type"=>"onchange","script"=>"getbillno(this.value)"), "type"=>"select", "class"=>"form-control chosen-select", "options"=>$parentbills_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billno", "content"=>"bill no", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billdate", "content"=>"bill date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paiddate", "content"=>"paid date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount", "content"=>"total amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"balanceamount", "content"=>"balance amount", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"clientname","id"=>"clientname", "content"=>"client name", "readonly"=>"",  "required"=>"required","action"=>array("type"=>"onchange","script"=>"gettotalamount(this.value)"), "type"=>"select", "class"=>"form-control chosen-select", "options"=>$client_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid", "content"=>"amount paid", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billparticulars", "content"=>"bill particulars", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"show", "content"=>"show", "readonly"=>"",  "required"=>"","type"=>"checkbox", "class"=>"form-control","options"=>array("existing_bills"=>"existing bills", "bulk_payment"=>"bulk payment"));
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billfile", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$form_info = array();
		$form_fields = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editbillpayment";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "cities";
		$form_info["bredcum"] = "add billpayments";
		$form_field = array("name"=>"billno1", "content"=>"bill no", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billdate1", "content"=>"bill date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paiddate1", "content"=>"paid date", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"totalamount1", "content"=>"total amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amountpaid1", "content"=>"amount paid", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"clientname1", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$client_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"parentbill1", "content"=>"parent bill", "readonly"=>"",  "required"=>"", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$parentbills_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billparticulars1", "content"=>"bill particulars", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "content"=>"remarks", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"billfile1", "content"=>"upload bill", "readonly"=>"", "required"=>"", "type"=>"file", "class"=>"form-control file");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1",  "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
	
		$form_info["form_fields"] = $form_fields;
		$modals = array();
		$modals[] = $form_info;
		$values["modals"] = $modals;
	
		$values['provider'] = "bills";
		return View::make('billpayments.lookupdatatable', array("values"=>$values));
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
		$emp_contracts = explode(",", $emp_contracts);
		$entities = \Depot::whereIn("depots.id",$emp_contracts)
						->where("clientId","=",$values['id'])
						->join("contracts", "depots.id", "=","contracts.depotId")
						->join("clients", "clients.id", "=","contracts.clientId")
						->select(array("depots.id as id","depots.name as name"))->get();
		
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
	public function getBillNo()
	{
		$values = Input::all();
		
		$select_args = array();
		$select_args[] = "bill_payments.billNo as billNo";
		$select_args[] = "bill_payments.totalAmount as totalAmount";
		$select_args[] = "bill_payments.billDate as billDate";
		$select_args[] = "bill_payments.clientId as clientId";
		$select_args[] = "bill_payments.billParticulars as billParticulars";
		
		
		$json_resp = array();
		$entity = \BillPayments::where("bill_payments.Id","=",$values['id'])
								->join("clients","bill_payments.clientId","=","clients.id")
								->first();
		$json_resp["billNo"] = $entity->billNo;
		$json_resp["totalAmount"] = $entity->totalAmount;
		$json_resp["billDate"] = date("d-m-Y",strtotime($entity->billDate));
		$json_resp["clientId"] = $entity->clientId;
		$json_resp["billParticulars"] = $entity->billParticulars;
		$paid_amt_tot = \BillPayments::where("billNo","=",$entity->billNo)
										->where("clientId","=",$entity->clientId)
										->select($select_args)
										->sum('amountPaid');
		$json_resp["balance_amt"] = $entity->totalAmount-$paid_amt_tot;
		echo json_encode($json_resp);
	}
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function getTotalAmount()
	{
		$values = Input::all();
		$json_resp = array();
		$entity = \DB::select(\DB::raw("select sum(totalAmount) as totalAmount, sum(amountPaid) as paidAmount from bill_payments where clientId=".$values['id']));
		$entity = $entity[0];
		$json_resp["totalAmount"] = $entity->totalAmount-$entity->paidAmount;
		$json_resp["paidAmount"] = $entity->paidAmount;
		echo json_encode($json_resp);
	}

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	
}
