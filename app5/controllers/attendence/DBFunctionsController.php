<?php namespace attendence;

class DBFunctionsController extends \Controller {

	/**
	 * add a record to a table.
	 *
	 * @return Response
	 */
	public function insert($table, $fields)
	{
		$entity = new $table();
		$allValues = "";
		foreach($fields as $key=>$value){
			$entity[$key] = $value;
			$allValues = $allValues.$value.", ";
		}
		$entity["createdBy"] = \Auth::user()->id;
		$ret_val = $entity->save();
		
		if($table == "\BlockDataEntry"){
			return $ret_val;
		}
		
		$fields = array();
		$fields['transactionType'] = "INSERT";
		$fields['tableName'] = $table;
		$fields['recId'] = $entity->id;
		$fields['oldValues'] = "";
		$fields['newValues'] = $allValues;
		$table = "\DBTransactions"; 
		$entity = new $table();
		foreach($fields as $key=>$value){
			$entity[$key] = $value;
		}
		$entity["createdBy"] = \Auth::user()->id;
		$fields['insertedBy'] = \Auth::user()->fullName;
		$entity->save();
		\Session::flash('email_message', $fields);
		return $ret_val;
	}
	
	/**
	 * add a record to a table.
	 *
	 * @return Response
	 */
	public function insertRetId($table, $fields)
	{
		$entity = new $table();
		foreach($fields as $key=>$value){
			$entity[$key] = $value;
		}
		$entity["createdBy"] = \Auth::user()->id;
		$entity->save();
		return $entity->id;
	}
	
	/**
	 * update a record of a table
	 *
	 * @return Response
	 */
	public function update($table, $fields, $data)
	{	
		//print_r($data); print_r($table); print_r($fields); die();
		$tfields = array();
		$newValues = "";
		$oldValues = "";
		foreach($fields as $key=>$value){
			$tfields[] = $key;
			$newValues = $newValues.$value.", ";
		}
		$rec = $table::where('id', "=", $data['id'])->select($tfields)->get();
		if(count($rec)>0){
			$rec = $rec[0];
			foreach ($tfields as $tfield){
				$oldValues = $oldValues.$rec[$tfield].", ";
			}
		}
		
		$fields["updatedBy"] = \Auth::user()->id;
		$ret_val = $table::where('id', $data['id'])->update($fields);
		
		$fields = array();
		$fields['transactionType'] = "UPDATE";
		$fields['tableName'] = $table;
		$fields['recId'] = $data['id'];
		$fields['oldValues'] = $oldValues;
		$fields['newValues'] = $newValues;
		$table = "\DBTransactions";
		$entity = new $table();
		foreach($fields as $key=>$value){
			$entity[$key] = $value;
		}
		$entity["createdBy"] = \Auth::user()->id;
		$fields['insertedBy'] = \Auth::user()->fullName;
		$entity->save();
		\Session::flash('email_message', $fields);
		return $ret_val;
	}
	
	/**
	* get a record from a table
	*
	* @return Response
	*/
	public function get($table, $fields)
	{
		//print_r($data); print_r($table); print_r($fields); die();
		return $table::where($fields)->get();
	}
	
	
}
