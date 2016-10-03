<?php namespace inventory;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class ItemTypesController extends \Controller {

	/**
	 * add a new state.
	 *
	 * @return Response
	 */
	public function addItemType()
	{
		if (\Request::isMethod('post'))
		{
			$values = Input::all();
			$field_names = array("name"=>"name","itemcategory"=>"itemCategoryId","description"=>"description");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "ItemTypes";
			$values = array();
			if($db_functions_ctrl->insert($table, $fields)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("itemtypes");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("itemtypes");
			}	
		}		
	}
	
	/**
	 * Edit a state.
	 *
	 * @return Response
	 */
	public function editItemType()
	{
		$values = Input::all();
		if (\Request::isMethod('post'))
		{
			$field_names = array("name1"=>"name","description1"=>"description","itemcategory1"=>"itemCategoryId","status1"=>"status");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
			}
			$data = array('id'=>$values['id1']);			
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\ItemTypes";
			$values = array();
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("itemtypes");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("itemtypes");
			}
		}
	}
	
	/**
	 * manage all states.
	 *
	 * @return Response
	 */
	public function manageItemTypes()
	{
		$values = Input::all();
		$values['bredcum'] = "ITEM TYPES";
		$values['home_url'] = '#';
		$values['add_url'] = 'additemtype';
		$values['form_action'] = 'itemtypes';
		$values['action_val'] = '#';
		$theads = array(' ID','Name', "Description", "status", "Actions");
		$values["theads"] = $theads;
			
		$actions = array();
		$action = array("url"=>"editemtype?","css"=>"primary", "type"=>"", "text"=>"Edit");
		$actions[] = $action;
		$values["actions"] = $actions;
			
		$form_info = array();
		$form_info["name"] = "additemtype";
		$form_info["action"] = "additemtype";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "itemtypes";
		$form_info["bredcum"] = "add item type";
		
		$item_cat_arr = array();
		$item_cats = \ItemCategories::all();
		foreach ($item_cats as $item_cat){
			$item_cat_arr[$item_cat->id] = $item_cat->name;
		}
		
		$form_fields = array();		
// 		$form_field = array("name"=>"itemcategory", "content"=>"item name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$item_cat_arr, "class"=>"form-control chosen-select");
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"name", "content"=>"item type name", "readonly"=>"","action"=>array("type"=>"onchange","script"=>"checkvalidation(this.value,this.id,'ItemTypes')"),  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"description", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
				
		$form_info["form_fields"] = $form_fields;
		$values['form_info'] = $form_info;
		
		
		$form_info = array();
		$form_info["name"] = "edit";
		$form_info["action"] = "edititemtype";
		$form_info["method"] = "post";
		$form_info["class"] = "form-horizontal";
		$form_info["back_url"] = "itemtypes";
		$form_info["bredcum"] = "add item type";
		
		$modals = array();
		$form_fields = array();
		$form_field = array("name"=>"name1", "content"=>"item type name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
		$form_fields[] = $form_field;
// 		$form_field = array("name"=>"itemcategory1", "content"=>"item name", "readonly"=>"",  "required"=>"required","type"=>"select", "options"=>$item_cat_arr, "class"=>"form-control chosen-select");
// 		$form_fields[] = $form_field;
		$form_field = array("name"=>"description1", "content"=>"description", "readonly"=>"",  "required"=>"","type"=>"textarea", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"id1", "value"=>"", "content"=>"", "readonly"=>"",  "required"=>"required","type"=>"hidden", "class"=>"form-control");
		$form_fields[] = $form_field;
		$form_field = array("name"=>"status1", "value"=>"", "content"=>"status", "readonly"=>"", "value"=>"", "required"=>"", "type"=>"select", "options"=>array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE"), "class"=>"form-control");
		$form_fields[] = $form_field;	
		
		$form_info["form_fields"] = $form_fields;
		$modals[] = $form_info;
		$values["modals"] = $modals;
		
		$values['provider'] = "itemtypes";

		return View::make('inventory.lookupdatatable', array("values"=>$values));
	}	
}
