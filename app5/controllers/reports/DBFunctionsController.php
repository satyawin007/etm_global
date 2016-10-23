<?php namespace reports;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
class DBFunctionsController extends \Controller {

	/**
	 * add a record to a table.
	 *
	 * @return Response
	 */
	public function insert($table, $fields)
	{
		$entity = new $table();
		foreach($fields as $key=>$value){
			$entity[$key] = $value;
		}
		$entity["createdBy"] = \Auth::user()->id;
		return $entity->save();
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
		$fields["updatedBy"] = \Auth::user()->id;
		return $table::where('id', $data['id'])->update($fields);
	}
	
	/**
	 * update a record of a table
	 *
	 * @return Response
	 */
	public function updatetrans($table, $fields, $data)
	{
		//print_r($data); print_r($table); print_r($fields); die();
		return $table::where('transactionId', $data['id'])->update($fields);
	}
	
	/**
	 * update a record of a table
	 *
	 * @return Response
	 */
	public function updatesuspense($table, $fields, $data)
	{
		//print_r($data); print_r($table); print_r($fields); die();
		return $table::where('branchId', $data['branchId'])->where('reportDate', $data['reportDate'])->update($fields);
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
