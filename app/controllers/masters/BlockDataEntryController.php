<?php namespace masters;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class BlockDataEntryController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function addCity()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("cityname"=>"name","citycode"=>"code","statename"=>"stateId");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "City";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("cities");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("cities");
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
	public function editTransactionBlocking()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$url = "transactionblocking?date=".$values['date'];
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\BlockDataEntry"; 
			\DB::beginTransaction();
			$recid = "";
			try{
				$i=0;
				for($i=0; $i<count($values["branch"]); $i++){
					$recid = 0;
					$rec =\BlockDataEntry::where("branchId","=",$values["branch"][$i])->where("dataEntryDate","=",date("Y-m-d",strtotime($values['date'])))->get();
					if(count($rec)>0){
						$rec = $rec[0];
						$fields = array();
						$fields["branchId"] = $values["branch"][$i];
						$fields["status"] = $values["status"][$i];
						if($values["status"][$i] != $rec->status){
							$db_functions_ctrl->update($table, $fields, array("id"=>$rec->id));
						}
					}
					else{
						$fields = array();
						$fields["branchId"] = $values["branch"][$i];
						$fields["dataEntryDate"] = date("Y-m-d",strtotime($values['date']));
						$fields["status"] = $values["status"][$i];
						$db_functions_ctrl->insert($table, $fields);
					}
				}
			}
			catch(\Exception $ex){
				\Session::put("message","Update Date Block Failed : Operation Could not be completed, Try Again!");
				\DB::rollback();
				return \Redirect::to($url);
			}
			\DB::commit();
			
			\Session::put("message","Operation Completed Succesfully!");
			return \Redirect::to($url);
		}
	}
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function verifyTransactionDateandBranch()
	{
		if(true){
			echo "YES";
			return;
		}
		$values = Input::All();
		$blockBranch = $values['branch'];
		$blockDate = date("Y-m-d",strtotime($values['date']));
		$CTIME = 0;
		$rec = \Parameters::where("name","=","transaction_closing_time")->get();
		if(count($rec)>0){
			$rec = $rec[0];
			$CTIME = $rec->value;
		}
		$todayHours = date("H");
		$todayDate = date("Y-m-d");
		$yesterDay = date("Y-m-d", strtotime("-1 days"));
		if($blockDate == $todayDate)
		{
			echo "YES";
		}
		else if($blockDate == $yesterDay)
		{
			if($todayHours < $CTIME)
			{	
				echo "YES";
			}
			else
			{
				$rec =\BlockDataEntry::where("branchId","=",$blockBranch)->where("dataEntryDate","=",date("Y-m-d",strtotime($blockDate)))->get();
				$status = "";
				if(count($rec)>0){
					$rec = $rec[0];
					$status = $rec->status;
				}
				if($status == "OPEN")
					echo "YES";
				else
					echo "NO";		
			}
		}
		else
		{
			$rec =\BlockDataEntry::where("branchId","=",$blockBranch)->where("dataEntryDate","=",date("Y-m-d",strtotime($blockDate)))->get();
			$status = "";
			if(count($rec)>0){
				$rec = $rec[0];
				$status = $rec->status;
			}
			if($status == "OPEN")
				echo "YES";
			else
				echo "NO";
		}
	}	
	
	
	/**
	 * get all city based on stateId
	 *
	 * @return Response
	 */
	public function verifyTransactionDateandBranchLocally($values)
	{
		$blockBranch = $values['branch'];
		$blockDate = date("Y-m-d",strtotime($values['date']));
		$CTIME = 0;
		$rec = \Parameters::where("name","=","transaction_closing_time")->get();
		if(count($rec)>0){
			$rec = $rec[0];
			$CTIME = $rec->value;
		}
		$todayHours = date("H");
		$todayDate = date("Y-m-d");
		$yesterDay = date("Y-m-d", strtotime("-1 days"));
		return "YES";
		if($blockDate == $todayDate)
		{
			return "YES";
		}
		else if($blockDate == $yesterDay)
		{
			if($todayHours < $CTIME)
			{
				return "YES";
			}
			else
			{
				$rec =\BlockDataEntry::where("branchId","=",$blockBranch)->where("dataEntryDate","=",date("Y-m-d",strtotime($blockDate)))->get();
				$status = "";
				if(count($rec)>0){
					$rec = $rec[0];
					$status = $rec->status;
				}
				if($status == "OPEN")
					return "YES";
					else
						return "NO";
			}
		}
		else
		{
			$rec =\BlockDataEntry::where("branchId","=",$blockBranch)->where("dataEntryDate","=",date("Y-m-d",strtotime($blockDate)))->get();
			$status = "";
			if(count($rec)>0){
				$rec = $rec[0];
				$status = $rec->status;
			}
			if($status == "OPEN")
				return "YES";
				else
					return "NO";
		}
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
	public function getTransactionBlocking()
	{
		$values = Input::all();
		$values['bredcum'] = "PAY OFFICE EMPLOYEE SALARY";
		$values['home_url'] = 'masters';
		$values['add_url'] = '#';
		$values['form_action'] = '#';
		$values['action_val'] = '#';
	
		$actions = array();
		$action = array("url"=>"#edit", "type"=>"modal", "css"=>"inverse", "js"=>"modalEditServiceProvider(", "jsdata"=>array("id","branchId","provider","name","number","companyName","configDetails","address","refName","refNumber"), "text"=>"EDIT");
		$actions[] = $action;
		$values["actions"] = $actions;
	
		$form_info = array();
		$form_info["name"] = "payofficeemployeesalary";
		$form_info["action"] = "payofficeemployeesalary";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "masters";
		$form_info["bredcum"] = "PAY EMPLOYEE SALARY";
	
		$form_fields = array();
		$branches = \OfficeBranch::All();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$month_arr = array();
		$month_arr[date('Y', strtotime('-1 year'))."-12-01"] = 'Dec '.date('Y', strtotime('-1 year'));
		$month_arr[date('Y')."-01-01"] = 'Jan '.date('Y');
		$month_arr[date('Y')."-02-01"] = 'Feb '.date('Y');
		$month_arr[date('Y')."-03-01"] = 'March '.date('Y');
		$month_arr[date('Y')."-04-01"] = 'April '.date('Y');
		$month_arr[date('Y')."-05-01"] = 'May '.date('Y');
		$month_arr[date('Y')."-06-01"] = 'June '.date('Y');
		$month_arr[date('Y')."-07-01"] = 'July'.date('Y');
		$month_arr[date('Y')."-08-01"] = 'Aug '.date('Y');
		$month_arr[date('Y')."-09-01"] = 'Sep '.date('Y');
		$month_arr[date('Y')."-10-01"] = 'Oct '.date('Y');
		$month_arr[date('Y')."-11-01"] = 'Nov '.date('Y');
		$month_arr[date('Y')."-12-01"] = 'Dec '.date('Y');
		
		$branch_val = ""; $month_val = ""; $pmtdate_val = ""; $pmttype_val = "";
		if(isset($values["branch"])){
			$branch_val = $values["branch"];
		}
		if(isset($values["month"])){
			$month_val = $values["month"];
		}
		if(isset($values["paymentdate"])){
			$pmtdate_val = $values["paymentdate"];
		}
		if(isset($values["paymenttype"])){
			$pmttype_val = $values["paymenttype"];
		}
		$form_field = array("name"=>"branch", "value"=>$branch_val, "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"month", "value"=>$month_val, "content"=>"salary month", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control", "options"=>$month_arr);
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymentdate", "value"=>$pmtdate_val, "content"=>"payment date", "readonly"=>"",  "required"=>"required", "type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"paymenttype", "value"=>$pmttype_val, "content"=>"payment type", "readonly"=>"",  "action"=>array("type"=>"onchange","script"=>"showPaymentFields(this.value)"), "required"=>"required", "type"=>"select", "class"=>"form-control select2",  "options"=>array("cash"=>"CASH","advance"=>"FROM ADVANCE","cheque_debit"=>"CHEQUE (CREDIT)","cheque_credit"=>"CHEQUE (DEBIT)","ecs"=>"ECS","neft"=>"NEFT","rtgs"=>"RTGS","dd"=>"DD","credit_card"=>"CREDIT CARD","debit_card"=>"DEBIT CARD"));
		
		$form_fields[] = $form_field;
		
		
		$form_info["form_fields"] = $form_fields;
		$values["form_info"] = $form_info;
		$modals[] = $form_info;
			
		return View::make('masters.transactionblockingdatatable', array("values"=>$values));
	}
	
}
