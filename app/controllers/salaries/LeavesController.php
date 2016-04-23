<?php namespace salaries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class LeavesController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addLeave()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$dt = date("Y-m-d",strtotime($values["fromdate"]));
			$dStart = new \DateTime($dt);
			$dt = date("Y-m-d",strtotime($values["todate"]));
		   	$dEnd  = new \DateTime($dt);
		   	$dDiff = $dStart->diff($dEnd);
			$leaves =  $dDiff->days;
			if($values["frommngreve"]=="Afternoon" && $values["tomngreve"]=="Morning"){
				$leaves = $leaves-0.5;
			}
			if($values["frommngreve"]=="Morning" && $values["tomngreve"]=="Afternoon"){
				$leaves = $leaves+0.5;
			}
			$values["leaves"] = $leaves;
			$field_names = array("employeename"=>"empId","fromdate"=>"fromDate","todate"=>"toDate","frommngreve"=>"fromMrngEve","tomngreve"=>"toMrngEve","substitute"=>"substituteId","leaves"=>"noOfLeaves","remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && ($key=="fromdate" || $key=="todate")){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Leaves";
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("leaves");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("leaves");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editLeave()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$dt = date("Y-m-d",strtotime($values["fromdate"]));
			$dStart = new \DateTime($dt);
			$dt = date("Y-m-d",strtotime($values["todate"]));
		   	$dEnd  = new \DateTime($dt);
		   	$dDiff = $dStart->diff($dEnd);
			$leaves =  $dDiff->days;
			if($values["frommngreve"]=="Afternoon" && $values["tomngreve"]=="Morning"){
				$leaves = $leaves-0.5;
			}
			if($values["frommngreve"]=="Morning" && $values["tomngreve"]=="Afternoon"){
				$leaves = $leaves+0.5;
			}
			$values["leaves"] = $leaves;
			$field_names = array("employeename"=>"empId","fromdate"=>"fromDate","todate"=>"toDate","frommngreve"=>"fromMrngEve","tomngreve"=>"toMrngEve","substitute"=>"substituteId","leaves"=>"noOfLeaves","remarks"=>"remarks");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key]) && ($key=="fromdate" || $key=="todate")){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$data = array('id'=>$values['id']);			
			$table = "\Leaves";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("editleave?id=".$values['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("editleave?id=".$values['id']);
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
			$form_field = array("name"=>"employeename", "id"=>"employeename", "value"=>$entity->empId, "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"fromdate", "id"=>"fromdate", "value"=>date("d-m-Y",strtotime($entity->fromDate)), "content"=>"from Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"frommngreve", "id"=>"frommngreve", "value"=>$entity->fromMrngEve, "content"=>"from Mor/Eve", "readonly"=>"",  "required"=>"required","type"=>"radio", "options"=>array("Morning"=>"Morning","Afternoon"=>"Afternoon"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"todate", "id"=>"todate", "value"=>date("d-m-Y",strtotime($entity->toDate)), "content"=>"to Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tomngreve", "id"=>"tomngreve", "value"=>$entity->toMrngEve, "content"=>"to Mor/Eve", "readonly"=>"",  "required"=>"required","type"=>"radio", "options"=>array("Morning"=>"Morning","Afternoon"=>"Afternoon"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"substitute", "id"=>"substitute", "value"=>$entity->substituteId, "content"=>"substitute employee", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"remarks", "id"=>"remarks", "value"=>$entity->remarks, "content"=>"remarks", "readonly"=>"", "required"=>"required","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$values["id"], "content"=>"", "readonly"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		
			$form_info["form_fields"] = $form_fields;
			return View::make("transactions.edit2colmodalform",array("form_info"=>$form_info));
		}
	}
	
		
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageLeaves()
	{
		$values = Input::all();
		$values['bredcum'] = "EMPLOYEE LEAVES";
		$values['home_url'] = '#';
		$values['add_url'] = 'addleave';
		$values['form_action'] = 'leaves';
		$values['action_val'] = '#';
		$theads = array('Emp Id','Emp Name', "branch", "From", "Mor/Eve", "To", "Mor/Eve", "Leaves", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editleave?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "addleave";
		$form_info["action"] = "addleave";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "leaves";
		$form_info["bredcum"] = "add leave";
		
		$emps = \Employee::all();
		$emp_arr = array();
		foreach ($emps as $emp){
			$emp_arr[$emp->id] = $emp->fullName." - ".$emp->empCode;
		}
		
		$form_fields = array();		
		$form_field = array("name"=>"employeename", "content"=>"employee name", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"fromdate", "content"=>"from Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"frommngreve", "content"=>"from Mor/Eve", "readonly"=>"",  "required"=>"required","type"=>"radio", "options"=>array("Morning"=>"Morning","Afternoon"=>"Afternoon"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"todate", "content"=>"to Date", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control date-picker");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"tomngreve", "content"=>"to Mor/Eve", "readonly"=>"",  "required"=>"required","type"=>"radio", "options"=>array("Morning"=>"Morning","Afternoon"=>"Afternoon"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"substitute", "content"=>"substitute employee", "readonly"=>"", "required"=>"","type"=>"select", "options"=>$emp_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		$modals = array();
		$values["modals"] = $modals;
		
		$values['provider'] = "leaves";

		return View::make('salaries.lookupdatatable', array("values"=>$values));
	}	
	
	public function approveLeave(){
		$values = Input::all();
		$db_functions_ctrl = new DBFunctionsController();
		$data = array('id'=>$values['id']);
		$table = "\Leaves";
		$fields = array("status"=>"Approved");
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
