<?php namespace transactions;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class RepairTransactionItemController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addItemCategory()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("name"=>"name","description"=>"description");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ItemCategories";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("itemcategories");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("itemcategories");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editRepairTransactionItem()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("item"=>"repairedItem","quantity"=>"quantity","amount"=>"amount","remarks"=>"comments","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\CreditSupplierTransDetails";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				$entity = $db_functions_ctrl->get($table, array("id"=>$values["id1"]));
				$entity = $entity[0];
				return \Redirect::to("viewrepairtransactionitems?id=".$entity->creditSupplierTransId);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				$entity = $db_functions_ctrl->get($table, array("id"=>$values["id1"]));
				$entity = $entity[0];
				return \Redirect::to("viewrepairtransactionitems?id=".$entity->creditSupplierTransId);
			}
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageRepairTransactionItems()
	{
		$values = Input::all();
		$values['bredcum'] = "REPAIR TRANSACTION ITEMS";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = 'vehicles';
		$values['action_val'] = '';
		$values['form_info'] = array();
	
		$action_val = "";
		$links = array();
		$values['action_val'] = $action_val;
		$values['links'] = $links;
	
		$theads = array('repair type', "quantity", "amount", "comments", "status", "Actions");
		$values["theads"] = $theads;
	
		//Code to add modal forms
		$modals =  array();
			
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editrepairtransactionitem";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$parentId = -1;
		$types =  \LookupTypeValues::where("name", "=", "VEHICLE REPAIRS")->get();
		if(count($types)>0){
			$parentId = $types[0];
			$parentId = $parentId->id;
		}
		$items_arr = array();
		$items = \LookupTypeValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		$item_info_arr = array("1"=>"info1","2"=>"info2");
		$form_fields = array();
		$form_field = array("name"=>"item", "content"=>"item", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$items_arr,  "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"quantity", "content"=>"quantity", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"amount", "content"=>"amount", "readonly"=>"", "required"=>"required","type"=>"text", "class"=>"form-control ");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"remarks", "content"=>"remarks", "readonly"=>"", "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "content"=>"status", "readonly"=>"", "required"=>"","type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE","DELETED"=>"DELETED"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"",  "value"=>"", "required"=>"","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		$values["provider"] = "getrepairtransactionitems&id=".$values["id"];
		return View::make('transactions.repairsmodaldatatable', array("values"=>$values));
	}	
}
