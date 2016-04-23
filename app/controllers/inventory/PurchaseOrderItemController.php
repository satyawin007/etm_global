<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class PurchaseOrderItemController extends \Controller {

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
	public function editPurchasedItem()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("item"=>"itemId","manufacturer"=>"manufacturerId","qty"=>"qty","unitprice"=>"unitPrice","itemstatus"=>"itemStatus","status"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\PurchasedItems";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				$entity = $db_functions_ctrl->get($table, array("id"=>$values["id1"]));
				$entity = $entity[0];
				return \Redirect::to("viewpurchaseditems?id=".$entity->purchasedOrderId);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				$entity = $db_functions_ctrl->get($table, array("id"=>$values["id1"]));
				$entity = $entity[0];
				return \Redirect::to("viewpurchaseditems?id=".$entity->purchasedOrderId);
			}
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function managePurchaseOrderItems()
	{
		$values = Input::all();
		$values['bredcum'] = "PURCHASE ORDER ITEMS";
		$values['home_url'] = '#';
		$values['add_url'] = '#';
		$values['form_action'] = 'vehicles';
		$values['action_val'] = '';
	
		$action_val = "";
		$links = array();
		$values['action_val'] = $action_val;
		$values['links'] = $links;
	
		$theads = array('Item name', "manufacturer", "quantity", "Unit price", "item status", "status", "Actions");
		$values["theads"] = $theads;
	
		//Code to add modal forms
		$modals =  array();
			
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "editpurchaseditem";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		
		$items_arr = array();
		$items = \Items::where("status","=","ACTIVE")->get();
		foreach ($items as $item){
			$items_arr[$item->id] = $item->name;
		}
		
		$mans_arr = array();
		$mans = \Manufacturers::where("status","=","ACTIVE")->get();
		foreach ($mans as $man){
			$mans_arr[$man->id] = $man->name;
		}
	
		$form_fields = array();
		$form_field = array("name"=>"item", "content"=>"item name", "readonly"=>"readonly",  "required"=>"", "type"=>"select", "options"=>$items_arr, "action"=>array("type"=>"onchange","script"=>"getManufacturers(this.value)"), "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"manufacturer", "content"=>"manufacturer", "readonly"=>"", "required"=>"required","type"=>"select", "options"=>$mans_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"qty", "content"=>"quantity", "readonly"=>"",  "required"=>"required", "type"=>"text",  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"unitprice", "content"=>"unit price", "readonly"=>"",  "required"=>"required", "type"=>"text",  "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"itemstatus", "content"=>"item status", "readonly"=>"readonly",  "required"=>"required", "type"=>"select", "options"=>array("Old"=>"Old","New"=>"New"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status", "content"=>"item status", "readonly"=>"readonly",  "required"=>"required", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "content"=>"", "readonly"=>"readonly",  "required"=>"", "type"=>"hidden", "value"=>"", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
	
		$values["provider"] = "getpurchaseorderitems&id=".$values["id"];
		return View::make('inventory.purchaseordermodaldatatable', array("values"=>$values));
	}
	
	public function deletePurchaseOrderItem(){
		$values = Input::all();
		$fields = array("status"=>"DELETED");
		$data = array('id'=>$values['id']);
		$db_functions_ctrl = new DBFunctionsController();
		$table = "\PurchasedItems";  
		if($db_functions_ctrl->update($table, $fields, $data)){
			echo "success";
		}
		else{
			echo "fail";
		}
	}
}
