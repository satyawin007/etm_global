<?php namespace billpayments;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
class DataTableController extends \Controller {

	/**
	 * add a new city.
	 *
	 * @return Response
	 */
	private $jobs;
	
	public function getDataTableData()
	{
		$this->jobs = \Session::get("jobs");
		$values = Input::All();
		$start = $values['start'];
		$length = $values['length'];
		$total = 0;
		$data = array();
		
		if(isset($values["name"]) && $values["name"]=="bills") {
			$ret_arr = $this->getBillPayments($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
		else if(isset($values["name"]) && $values["name"]=="leaves") {
			$ret_arr = $this->getLeaves($values, $length, $start);
			$total = $ret_arr["total"];
			$data = $ret_arr["data"];
		}
				
		$json_data = array(
				"draw"            => intval( $_REQUEST['draw'] ),
				"recordsTotal"    => intval( $total ),
				"recordsFiltered" => intval( $total ),
				"data"            => $data
			);
		echo json_encode($json_data);
	}
	
	
	private function getBillPayments($values, $length, $start){
		$total = 0;
		$data = array();
		$select_args = array();
		$select_args[] = "bill_payments.billNo as billNo";
		$select_args[] = "bill_payments.billDate as billDate";
		$select_args[] = "bill_payments.paidDate as paidDate";
		$select_args[] = "bill_payments.totalAmount as totalAmount";
		$select_args[] = "bill_payments.amountPaid as amountPaid";
		$select_args[] = "bill_payments.amountPaid as dueAmount";
		$select_args[] = "clients.name as name";
		$select_args[] = "bill_payments.billParticulars as billParticulars";
		$select_args[] = "bill_payments.transctionType as transctionType";
		$select_args[] = "bill_payments.remarks as remarks";
		$select_args[] = "bill_payments.status as status";
		$select_args[] = "bill_payments.id as id";
		$select_args[] = "bill_payments.clientId as clientId";
		$select_args[] = "bill_payments.parentBillId as parentBillId";
		$select_args[] = "bill_payments.filePath as filePath";
		$select_args[] = "bill_payments.billNo as billNo1";
			
		$actions = array();
		if(in_array(310, $this->jobs)){
			$action = array("url"=>"#edit", "type"=>"modal", "css"=>"primary", "js"=>"modalEditBillPayments(", "jsdata"=>array("billNo1","billDate","paidDate", "totalAmount", "amountPaid","name","billParticulars", "remarks" ,"status", "id","clientId","parentBillId","transctionType"), "text"=>"EDIT");
			$actions[] = $action;
		}
		$values["actions"] = $actions;
		
		$search = $_REQUEST["search"];
		$search = $search['value'];
		if($search != ""){
			$entities = \BillPayments::where("bill_payments.billNo", "like", "%$search%")
						->join("clients","bill_payments.clientId","=","clients.id")
						->select($select_args)->limit($length)->offset($start)->get();
			$total = \BillPayments::where("bill_payments.billNo", "like", "%$search%")->count();
		}
		else{
			$entities = \BillPayments::where("bill_payments.status","=","ACTIVE")
						->join("clients","bill_payments.clientId","=","clients.id")
						->select($select_args)->limit($length)->offset($start)->get();
			$total = \BillPayments::where("bill_payments.status","=","ACTIVE")->count();
		}
		
		$entities = $entities->toArray();
		foreach($entities as $entity){
			$destinationPath = '../app/storage/uploads/'.$entity["filePath"];
			if ($entity["filePath"] != ""){
				$entity["billNo"] = "<a href='".$destinationPath."' target='_blank'>".$entity["billNo"]."</a>";
			}
			if($entity["billDate"] != ""){
				$entity["billDate"] = date("d-m-Y",strtotime($entity["billDate"]));
				if($entity["billDate"] == "01-01-1970"){
					$entity["billDate"] = "";
				}
			}
			$paid_amt_tot = \BillPayments::where("billNo","=",$entity["billNo"])
										->where('paidDate',"<=",$entity["paidDate"])
										->where('clientId',"=",$entity["clientId"])
										->sum('amountPaid');
			if($entity["paidDate"] != ""){
				$entity["paidDate"] = date("d-m-Y",strtotime($entity["paidDate"]));
				if($entity["paidDate"] == "01-01-1970"){
					$entity["paidDate"] = "";
				}
			}
			$entity["dueAmount"] = $entity["totalAmount"]-$paid_amt_tot;
			$data_values = array_values($entity);
			$actions = $values['actions'];
			$action_data = "";
			foreach($actions as $action){
				if($action["type"] == "modal"){
					$jsfields = $action["jsdata"];
					$jsdata = "";
					$i=0;
					for($i=0; $i<(count($jsfields)-1); $i++){
						$jsdata = $jsdata." '".$entity[$jsfields[$i]]."', ";
					}
					$jsdata = $jsdata." '".$entity[$jsfields[$i]];
					$action_data = $action_data. "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
				else {
					$action_data = $action_data."<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
				}
			}
			$data_values[10] = $action_data;
			$data[] = $data_values;
		}
		return array("total"=>$total, "data"=>$data);
	}

}


