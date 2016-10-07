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
		if(isset($values["type"]) && $values["type"]=="purchaseorders"){
			return $this->purchaseOrdersWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="inchargetransactions"){
			return $this->inchargeTransactionsWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="expensetransactions"){
			return $this->expenseTransactionsWorkFlow($values);
		}
		if(isset($values["type"]) && $values["type"]=="employeeleaves"){
			return $this->employeeLeavesWorkFlow($values);
		}
	}
	

	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	private function fuelTransactionsWorkFlow($values)
	{
		$values['bredcum'] = "FUEL TRASACTIONS";  //"fulltank", 'mileage',
		//$theads = array('contract/branch', 'fuel station name', 'veh reg No', 'filled date', 'amount', 'bill no',  'payment type', 'remarks', "created By", 'WF Status', 'WF Remarks', "Actions");
		$theads = array('contract/branch', 'fuel station name', 'veh reg No', 'st reading', 'ltrs', 'full tank', 'mileage', 'incharge', 'filled date', 'amount', 'bill no',  'payment type', 'remarks', "created By", 'WF Status', 'WF Remarks', "Actions");
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
		$theads = array('branch', 'incharge', 'amount', 'transaction date', 'trans info', 'bill no', 'remarks',  "created By", 'WF Status', 'WF Remarks', "Actions");
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
	private function expenseTransactionsWorkFlow($values)
	{
		$values['bredcum'] = "EXPENSE TRASACTIONS";
		$theads = array('branch', 'amount', 'transaction date', 'trans info', 'bill no', 'remarks',  "created By", 'WF Status', 'WF Remarks', "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		$form_info["transactiontype"] = $values['type'];
		$form_info["table"] = "\ExpenseTransaction";
	
		$form_fields = array();
		$form_info["form_fields"] = $form_fields;
	
		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$values['provider'] = "expense";
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
		$theads = array('Branch', 'Credit supplier', "date", "bill number", "payment paid", "payment Type", "total amount", "comments", "summary", "created By", 'WF Status', 'WF Remarks', "Actions");
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
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	private function purchaseOrdersWorkFlow($values)
	{
		$values['bredcum'] = "PURCHASE ORDERS";
		$theads = array('Warehouse', 'Credit supplier', "date", "bill number", "payment paid", "payment Type", "total amount", "comments", "summary",  "created By", 'WF Status', 'WF Remarks', "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		$form_info["transactiontype"] = $values['type'];
		$form_info["table"] = "\PurchasedOrders"; 
	
		$form_fields = array();
		$form_info["form_fields"] = $form_fields;
	
		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$values['provider'] = "purchaseorders";
		return View::make('workflow.lookupdatatable', array("values"=>$values));
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	private function employeeLeavesWorkFlow($values)
	{
		$values['bredcum'] = "EMPLOYEE LEAVES";
		$theads = array('Empoyee', 'branch', "from date", "mor/eve", "to date", "mor/eve",  "leaves", "leaves tkn", "remarks", "reasons", "created By", 'WF Status', "WF Remarks", "Actions");
		$values["theads"] = $theads;
	
		$form_info = array();
		$form_info["name"] = "";
		$form_info["action"] = "";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "";
		$form_info["bredcum"] = "";
		$form_info["transactiontype"] = $values['type'];
		$form_info["table"] = "\Leaves";
	
		$form_fields = array();
		$form_info["form_fields"] = $form_fields;
	
		$form_fields =  array();
		$form_info["add_form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
	
		$values['provider'] = "employeeleaves";
		return View::make('workflow.lookupdatatable', array("values"=>$values));
	}
	
	public function workFlowUpdate(){
		$values = Input::all();
		//$values["test"];
		//print_r($values); die();
		$json_data = array();
		$json_data["status"] = "fail";
		$json_data["message"] = "operation could not be completed";
		if(isset($values["transactiontype"]) && isset($values["table"])){
			if(isset($values["action"])){
				$table = $values["table"];
				if($values["transactiontype"] == "inchargetransactions"){
					$i = 0;	
					foreach($values["action"] as $rec){
						$update_dt = array("workFlowStatus"=>$values["workflowstatus"], "workFlowRemarks"=>$values["remarks"][$rec], "updatedBy"=>\Auth::user()->id);
						$table::where("transactionId","=",$values["recid"][$rec])->update($update_dt);
						$i++;
					}
				}
				else if($values["transactiontype"] == "expensetransactions"){
					$i = 0;
					foreach($values["action"] as $rec){
						$update_dt = array("workFlowStatus"=>$values["workflowstatus"], "workFlowRemarks"=>$values["remarks"][$rec], "updatedBy"=>\Auth::user()->id);
						$table::where("transactionId","=",$values["recid"][$rec])->update($update_dt);
						$i++;
					}
				}
				else{
					$i = 0;	
					foreach($values["action"] as $rec){
						$update_dt = array("workFlowStatus"=>$values["workflowstatus"], "workFlowRemarks"=>$values["remarks"][$rec], "updatedBy"=>\Auth::user()->id);
						$table::where("id","=",$values["recid"][$rec])->update($update_dt);
						$i++;
					}
				}
				$json_data["status"] = "success";
				$json_data["message"] = "operation completed successfully";
			}
		}
		echo json_encode($json_data);
	}
}
	
