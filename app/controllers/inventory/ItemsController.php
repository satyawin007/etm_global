<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ItemsController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addItem()
	{
		if (\Request::isMethod('post'))
		{
			//$values["dsaf"];
			$values = Input::all();
			$field_names = array("name"=>"name","number"=>"number","shortname"=>"shortName","stocktype"=>"stockType",
						"description"=>"description","units"=>"unitsOfMeasure","tags"=>"tags",
						"model"=>"itemModel","itemtype"=>"itemTypeId","manufacturer"=>"manufactures",
						"itemactions"=>"itemActions","stockable"=>"stockable","expirable"=>"expirable",
						"needalert"=>"needAlert", "itemnumber"=>"itemNumber");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if((isset($values[$key]) && $key == "manufacturer") || (isset($values[$key]) && $key == "itemactions") || (isset($values[$key]) && $key == "itemtype")){
					$mans = "";
					foreach ($values[$key] as $i){
						$mans = $mans.$i.",";
					}
					$fields[$val] = $mans;
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "Items";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("items");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("items");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editItem()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("name"=>"name","number"=>"number","shortname"=>"shortName","description"=>"description",
								"units"=>"unitsOfMeasure","tags"=>"tags","model"=>"itemModel","status"=>"status","stocktype"=>"stockType",
								"itemtype"=>"itemTypeId","manufacturer"=>"manufactures","itemactions"=>"itemActions",
								"stockable"=>"stockable","expirable"=>"expirable","needalert"=>"needAlert", "itemnumber"=>"itemNumber");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if((isset($values[$key]) && $key == "manufacturer") || (isset($values[$key]) && $key == "itemactions") || (isset($values[$key]) && $key == "itemtype")){
					$mans = "";
					foreach ($values[$key] as $i){
						$mans = $mans.$i.",";
					}
					$fields[$val] = $mans;
				}
				else if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Items";
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("edititem?id=".$values['id']);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("edititem?id=".$values['id']);
			}
		}
		$form_info = array();
		$form_info["name"] = "edititem";
		$form_info["action"] = "edititem";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "EDIT ITEM";
		
		$entity = \Items::where("id","=",$values['id'])->get();
		if(count($entity)){
			$entity = $entity[0];
			$manufacturers_arr = array();
			$manufacturers = \Manufacturers::all();
			foreach ($manufacturers as $manufacturer){
				$manufacturers_arr[$manufacturer->id] = $manufacturer->name;
			}
			
			$itemtypes_arr = array();
			$item_types = \ItemTypes::all();
			foreach ($item_types as $item_type){
				$itemtypes_arr[$item_type->id] = $item_type->name;
			}
			
			$parentId = -1;
			$parent = \InventoryLookupValues::where("name","=","UNITS OF MEASUREMENT")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$units =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
			$units_arr = array();
			foreach ($units  as $unit){
				$units_arr[$unit['id']] = $unit->name;
			}
			
			$parentId = -1;
			$parent = \InventoryLookupValues::where("name","=","ITEM ACTIONS")->get();
			if(count($parent)>0){
				$parent = $parent[0];
				$parentId = $parent->id;
			}
			$units =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
			$itemactions_arr = array();
			foreach ($units  as $unit){
				$itemactions_arr[$unit['id']] = $unit->name;
			}
			
			$form_fields = array();		
			$form_field = array("name"=>"name", "value"=>$entity->name, "content"=>"item name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"number", "value"=>$entity->number,  "content"=>"item number", "readonly"=>"","required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"shortname", "value"=>$entity->shortName,  "content"=>"short name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"description", "value"=>$entity->description,  "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"units", "id"=>"units", "value"=>$entity->unitsOfMeasure,  "content"=>"units of measure", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>$units_arr, "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"tags", "value"=>$entity->tags,  "content"=>"tags", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"itemtype[]", "id"=>"itemtype", "value"=>explode(",", $entity->itemTypeId),  "content"=>"item type", "readonly"=>"",  "required"=>"", "type"=>"select", "multiple"=>"multiple", "options"=>$itemtypes_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"model", "value"=>$entity->itemModel,  "content"=>"item model", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"manufacturer[]", "id"=>"manufacturer", "value"=>explode(",", $entity->manufactures),  "content"=>"manufacturer", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "options"=>$manufacturers_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"itemactions[]", "id"=>"itemactions", "value"=>explode(",", $entity->itemActions),  "content"=>"item actions", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "options"=>$itemactions_arr, "class"=>"form-control chosen-select");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"stocktype", "value"=>$entity->stockType,  "content"=>"stock type", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("OFFICE"=>"OFFICE","NON OFFICE"=>"NON OFFICE"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"needalert", "value"=>$entity->needAlert,  "content"=>"need alert", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"stockable", "value"=>$entity->stockable,  "content"=>"stockable", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"expirable", "value"=>$entity->expirable,  "content"=>"expirable", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"itemnumber", "content"=>"item number", "value"=>$entity->itemNumber, "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"status", "id"=>"status", "value"=>$entity->status,  "content"=>"status", "readonly"=>"",  "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
			$form_fields[] = $form_field;
			$form_field = array("name"=>"id", "value"=>$entity->id,  "content"=>"", "readonly"=>"",  "required"=>"", "type"=>"hidden", "class"=>"form-control");
			$form_fields[] = $form_field;
		
			$form_info["form_fields"] = $form_fields;
			return View::make("inventory.edit2colmodalform",array("form_info"=>$form_info));
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageItems()
	{
		$values = Input::all();
		$values['bredcum'] = "ITEMS";
		$values['home_url'] = '#';
		$values['add_url'] = 'additem';
		$values['form_action'] = 'items';
		$values['action_val'] = '#';
		$theads = array('Name', "Description", "short name", "units", "tags", "item model", "item type", "manufacturer", "stockable", "expirable", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"edititem?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "additem";
		$form_info["action"] = "additem";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "items";
		$form_info["bredcum"] = "add item";
		
		$manufacturers_arr = array();
		$manufacturers = \Manufacturers::all();
		foreach ($manufacturers as $manufacturer){
			$manufacturers_arr[$manufacturer->id] = $manufacturer->name;
		}
		
		$itemtypes_arr = array();
		$item_types = \ItemTypes::all();
		foreach ($item_types as $item_type){
			$itemtypes_arr[$item_type->id] = $item_type->name;
		}
		
		$parentId = -1;
		$parent = \InventoryLookupValues::where("name","=","UNITS OF MEASUREMENT")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$units =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$units_arr = array();
		foreach ($units  as $unit){
			$units_arr[$unit['id']] = $unit->name;
		}
		
		$parentId = -1;
		$parent = \InventoryLookupValues::where("name","=","ITEM ACTIONS")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$units =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
		$itemactions_arr = array();
		foreach ($units  as $unit){
			$itemactions_arr[$unit['id']] = $unit->name;
		}
		
		$form_fields = array();		
		$form_field = array("name"=>"name", "content"=>"item name", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'Items')"),  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"number", "content"=>"item number", "readonly"=>"","required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"shortname", "content"=>"short name", "readonly"=>"",  "required"=>"","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"units", "content"=>"units of measure", "readonly"=>"",  "required"=>"required", "type"=>"select", "options"=>$units_arr, "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"tags", "content"=>"tags", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"itemtype[]", "content"=>"item type", "readonly"=>"",  "required"=>"required", "type"=>"select", "multiple"=>"multiple", "options"=>$itemtypes_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"model", "content"=>"item model", "readonly"=>"",  "required"=>"", "type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"manufacturer[]", "content"=>"manufacturer", "readonly"=>"",  "required"=>"required", "multiple"=>"multiple", "type"=>"select", "options"=>$manufacturers_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"itemactions[]", "content"=>"item actions", "readonly"=>"",  "required"=>"", "multiple"=>"multiple", "type"=>"select", "options"=>$itemactions_arr, "class"=>"form-control chosen-select");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"stocktype", "content"=>"stock type", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("OFFICE"=>"OFFICE","NON OFFICE"=>"NON OFFICE"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"needalert", "content"=>"need alert", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"stockable", "content"=>"stockable", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"expirable", "content"=>"expirable", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"itemnumber", "content"=>"item number", "readonly"=>"",  "required"=>"", "type"=>"radio", "options"=>array("Yes"=>"Yes","No"=>"No"), "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		$modals = array();
		$values["modals"] = $modals;
		
		$values['provider'] = "items";
		return View::make('inventory.lookupdatatable', array("values"=>$values));
	}	
}
