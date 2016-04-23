<?php namespace salaries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class SalaryAdvancesController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addSalaryAdvance()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["dsf"];
			$field_names = array("employeename"=>"empId", "branch"=>"branchId","date"=>"paymentDate", "amount"=>"amount", "incharge"=>"inchargeId", "remarks"=>"comments");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && $key=="date"){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$fields["sourceEntity"] = "salaryadvance";
			$fields["dueType"] = "Loan";
			$db_functions_ctrl = new DBFunctionsController();
			$table = "EmpDueAmount"; 
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("salaryadvances");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("salaryadvances");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editSalaryAdvance()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			//$values["sda"];
			$field_names = array("employeename1"=>"empId","branch1"=>"branchId","date1"=>"paymentDate","amount1"=>"amount","remarks1"=>"comments","status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && $key=="date1"){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$data = array('id'=>$values['id1']);			
			$table = "\EmpDueAmount";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("salaryadvances");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("salaryadvances");
			}
		}
		$form_info = array();
		$form_info["name"] = "editleave";
		$form_info["action"] = "editleave";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "leaves";
		$form_info["bredcum"] = "edit leave";
	
		$entity = \Leaves::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$emps = \Employee::all();
			$emp_arr = array();
			foreach ($emps as $emp){
				$emp_arr[$emp->id] = $emp->fullName." - ".$emp->empCode;
			}
			$form_fields = array();	
			$form_field = array("name"=>"employeename", "value"=>$entity->empId, "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fromdate","value"=>date("d-m-Y",strtotime($entity->fromDate)), "content"=>"from Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"frommngreve", "value"=>$entity->fromMrngEve, "content"=>"from Mor/Eve", "readonly"=>"",  "required"=>"required","type"=>"radio", "options"=>array("Morning"=>"Morning","Afternoon"=>"Afternoon"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"todate","value"=>date("d-m-Y",strtotime($entity->toDate)), "content"=>"to Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tomngreve", "value"=>$entity->toMrngEve, "content"=>"to Mor/Eve", "readonly"=>"",  "required"=>"required","type"=>"radio", "options"=>array("Morning"=>"Morning","Afternoon"=>"Afternoon"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"substitute", "value"=>$entity->substituteId, "content"=>"substitute employee", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks","value"=>$entity->remarks, "content"=>"remarks", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id","value"=>$values["id"], "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
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
	public function manageSalaryAdvances()
	{
		$values = Input::all();
		$values['bredcum'] = "EMPLOYEE SALARY ADVANCES";
		$values['home_url'] = '#';
		$values['add_url'] = 'addsalaryadvance';
		$values['form_action'] = 'salaryadvance';
		$values['action_val'] = '#';
		$theads = array('Emp Id','Emp Name', "advance amount", "from branch", "paid date", "comments", "status", "Actions");
		$values["theads"] = $theads;
			
		$form_info = array();
		$form_info["name"] = "addsalaryadvance";
		$form_info["action"] = "addsalaryadvance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "salaryadvances";
		$form_info["bredcum"] = "add salaryadvance";
		
		$emps = \Employee::all();
		$emp_arr = array();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName." - ".$emp->empCode;
		}
		
		$branches = \OfficeBranch::all();
		$branches_arr = array();
		foreach ($branches as $branch){
			$branches_arr[$branch->id] = $branch->name;
		}
		
		$select_args = array();
		$select_args[] = "inchargeaccounts.id as id";
		$select_args[] = "employee.fullName as fullName";
		$incharges =  \InchargeAccounts::join("employee","employee.id","=","inchargeaccounts.empId")->select($select_args)->get();
		$incharges_arr = array();
		foreach ($incharges as $incharge){
			$incharges_arr[$incharge->id] = $incharge->fullName;
		}
		
		$form_fields = array();		
		$form_field = array("name"=>"employeetype", "content"=>"employee type", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>array("office employee"=>"OFFICE EMPLOYEE","driver and helper"=>"DRIVER and HELPER"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"branch", "content"=>"office branch", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$branches_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"employeename", "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"incharge", "content"=>"incharge", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$incharges_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date", "content"=>"advance Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amount", "content"=>"advance amount <span style='font-size:11px;'><br/>(Enter <span style='color:red;'>negitive - </span> value for returned advance amount)</span>", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editsalaryadvance";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "states";
		$form_info["bredcum"] = "add state";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"employeename1", "value"=>"", "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"date1", "value"=>"", "content"=>"advance Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amount1", "value"=>"", "content"=>"advance amount", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks1", "value"=>"", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "salaryadvances";
		return View::make('salaries.lookupdatatable', array("values"=>$values));
	}	
	
	public function deleteSalaryAdvance(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$data = array('id'=>$values['id']);
		$table = "\EmpDueAmount";
		$fields = array("status"=>"DELETED", "deleted"=>"Yes");
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
			return;
		}
		echo "fail";
	}
	
	public function rejectLeave(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$data = array('id'=>$values['id']);
		$table = "\Leaves";
		$fields = array("status"=>"Rejected");
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
			return;
		}
		echo "fail";
	}
	
	public function leaveDetails(){
		$values = Input::all();
		$empid = $values["eid"];
		$salaryMonth = $values["dt"];
		$noOfDays = date("t", strtotime($salaryMonth)) -1;
		$startDate = $salaryMonth;
		$endDate =  date('Y-m-d', strtotime($salaryMonth.'+ '.$noOfDays.' days'));
		$jsondata = array();
	
		$recs = DB::select( DB::raw("SELECT * from leaves where (fromDate BETWEEN '".$startDate."' and '".$endDate."' or toDate BETWEEN '".$startDate."' and '".$endDate."') and empId=".$empid." and deleted='No'"));
		$data = "";
		foreach ($recs as $rec){
			$data = $data."<tr>";
			$data = $data."<td>".date("d-m-Y",strtotime($rec->fromDate))."</td>";
			$data = $data."<td>".$rec->fromMrngEve."</td>";
			$data = $data."<td>".date("d-m-Y",strtotime($rec->toDate))."</td>";
			$data = $data."<td>".$rec->toMrngEve."</td>";
			$data = $data."<td>".$rec->noOfLeaves."</td>";
			$data = $data."<td>".$rec->remarks."</td>";
			$data = $data."<td>".$rec->status."</td>";
		}
		$jsondata["tbody"] = $data;
		echo json_encode($jsondata);
	}
}
