<?php namespace workflow;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use settings\AppSettingsController;
class WorkFlowController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	public function transactionsWorkFlow(){
		$values = Input::all();
		if(isset($values["type"]) && $values["type"]=="fueltransactions"){
			return $this->fuelTransactionsWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="repairtransactions"){
			return $this->repairTransactionsWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="inchargetransactions"){
			return $this->inchargeTransactionsWorkFlow($values);
		}
	}
	

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	private function fuelTransactionsWorkFlow($values)
	{
		$values['bredcum'] = "FUEL TRASACTIONS";
		$theads = array('contract/branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no', 'payment type', 'remarks', 'WF Status', 'WF Remarks', "Actions");
		$values["theads"] = $theads;

		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		$form_info["transactiontype"] = $values['type'];
		$form_info["table"] = "\FuelTransaction";

		$form_fields = array();
		$form_info["form_fields"] = $form_fields;

		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;

		$values['provider'] = "fuel";
		return View::make('workflow.lookupdatatable', array("values"=>$values));
	}
	

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	private function inchargeTransactionsWorkFlow($values)
	{
		$values['bredcum'] = "INCHARGE TRASACTIONS";
		$theads = array('branch', 'incharge', 'amount', 'transaction date', 'trans info', 'remarks', 'WF Status', 'WF Remarks', "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		$form_info["transactiontype"] = $values['type'];
		$form_info["table"] = "\InchargeTransaction";
	
		$form_fields = array();
		$form_info["form_fields"] = $form_fields;
	
		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$values['provider'] = "incharge";
		return View::make('workflow.lookupdatatable', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	private function repairTransactionsWorkFlow($values)
	{
		$values['bredcum'] = "REPAIR TRASACTIONS";
		$theads = array('Contract', 'Credit supplier', "date", "bill number", "payment paid", "payment Type", "total amount", "comments", "summary", 'WF Status', 'WF Remarks', "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		$form_info["transactiontype"] = "vehicle_repairs";
		$form_info["table"] = "\CreditSupplierTransactions"; 
	
		$form_fields = array();
		$form_info["form_fields"] = $form_fields;
	
		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$values['provider'] = "vehicle_repairs";
		return View::make('workflow.lookupdatatable', array("values"=>$values));
	}
	
	public function workFlowUpdate(){
		$values = Input::all();
		//$values["test"];
		$json_data = array();
		$json_data["status"] = "fail";
		$json_data["message"] = "operation could not be completed";
		if(isset($values["transactiontype"]) && isset($values["table"])){
// 			echo "Test";
// 			die();
			if(isset($values["action"])){
				$update_dt = array("workFlowStatus"=>$values["workflowstatus"], "workFlowRemarks"=>$values["remarks"]);
				$table = $values["table"];
				if($values["transactiontype"] == "inchargetransactions"){
					foreach($values["action"] as $rec){
						$table::where("transactionId","=",$rec)->update($update_dt);
					}
				}
				else{
					foreach($values["action"] as $rec){
						$table::where("id","=",$rec)->update($update_dt);
					}
				}
				$json_data["status"] = "success";
				$json_data["message"] = "operation completed successfully";
			}
		}
		echo json_encode($json_data);
	}
}
	
