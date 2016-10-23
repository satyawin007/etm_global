<?php namespace settings;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
class UserSettingsController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	
	public function updateProfile(){
		if (\Request::isMethod('post')){
			//$values["test"];
			$values = Input::all();
			$field_names = array("fullname"=>"fullName","gender"=>"gender","city"=>"cityId",
					"password"=>"password", "workgroup"=>"workGroup","dateofbirth"=>"dob","age"=>"age",
					"fathername"=>"fatherName","religion"=>"religion","residance"=>"residance",
					"nonlocaldetails"=>"detailsForNonLocal",
					"phonenumber"=>"mobileNo","homenumber"=>"homePhoneNo", "idproof"=>"idCardName",
					"idproofnumber"=>"idCardNumber","presentaddress"=>"presentAddress","joiningdate"=>"joiningDate",
					"aadhdaarnumber"=>"aadharNumber","rationcardnumber"=>"rationCardNumber", "drivinglicence"=>"drivingLicence",
					"drivingliceneexpiredate"=>"drvLicenceExpDate","accountnumber"=>"accountNumber", "bankname"=>"bankName",
					"ifsccode"=>"ifscCode","branchname"=>"branchName"
			);
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					$fields[$val] = $values[$key];
				}
				if($val == "dob" || $val == "drvLicenceExpDate" || $val == "joiningDate"){
					$fields[$val] = date("Y-m-d",strtotime($values[$key]));
				}
			}
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Employee";
			$data = array("id"=>Auth::user()->id);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("profile");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("profile");
			}
		}
	}
	
	public function updateEmployeeProfile(){
		if (\Request::isMethod('post')){
			//$values["test"];
			$values = Input::all();
			$field_names = array("fullname"=>"fullName","gender"=>"gender","city"=>"cityId",
					"password"=>"password", "workgroup"=>"workGroup","dateofbirth"=>"dob","age"=>"age",
					"fathername"=>"fatherName","religion"=>"religion","residance"=>"residance",
					"nonlocaldetails"=>"detailsForNonLocal","employeetype"=>"typeId",
					"phonenumber"=>"mobileNo","homenumber"=>"homePhoneNo", "idproof"=>"idCardName",
					"idproofnumber"=>"idCardNumber","presentaddress"=>"presentAddress","joiningdate"=>"joiningDate",
					"aadhdaarnumber"=>"aadharNumber","rationcardnumber"=>"rationCardNumber", "drivinglicence"=>"drivingLicence",
					"drivingliceneexpiredate"=>"drvLicenceExpDate","accountnumber"=>"accountNumber", "bankname"=>"bankName",
					"ifsccode"=>"ifscCode", "branchname"=>"branchName", "officebranch"=>"officeBranchIds", "clients"=>"clientIds", 
					"empbranches"=>"officeBranchIds", "clientbranches"=>"contractIds", "roleprevilage"=>"rolePrevilegeId",
					"emailid"=>"emailId","salarycardno"=>"salaryCardNo"
			);
			$fields = array();
			$fields["officeBranchIds"] = "";
			$fields["contractIds"] = "";
			$fields["clientIds"] = "";
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($val == "dob" || $val == "drvLicenceExpDate" || $val == "joiningDate"){
						$fields[$val] = date("Y-m-d",strtotime($values[$key]));
					}
					else if($key == "clientbranches" || $key == "empcontracts" || $key == "officebranch" || $key == "clients"){
						$field_val = "";
						$i = 0;
						for($i=0; $i<count($values[$key]); $i++){
							if($i==(count($values[$key])-1)){
								$field_val = $field_val.$values[$key][$i];
								break;
							}
							$field_val = $field_val.$values[$key][$i].",";
						}
						$fields[$val] = $field_val;
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
				
			}
			$fields["roleId"] = $fields["rolePrevilegeId"];
			if (isset($values["billfile"]) && Input::hasFile('billfile') && Input::file('billfile')->isValid()) {
				$destinationPath = storage_path().'/uploads/'; // upload path
				$extension = Input::file('billfile')->getClientOriginalExtension(); // getting image extension
				$fileName = uniqid().'.'.$extension; // renameing image
				Input::file('billfile')->move($destinationPath, $fileName); // upl1oading file to given path
				$fields["filePath"] = $fileName;
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Employee";
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				
				$roleid = $values["roleprevilage"];
				$privileges = \RolePrivileges::where("roleId","=",$roleid)->get();
				$privileges_arr = array();
				foreach ($privileges as $privilege){
					$privileges_arr[] = $privilege->jobId;
				}
				if($roleid == 3){
					$empid = \Employee::where("id","=",$values["id"])->first();
					$table = "InchargeAccounts";
					$fields = array("empid"=>$empid->id,"status"=>"Active");
					$db_functions_ctrl->insert($table, $fields);
				}
				//\Session::put("jobs",$privileges_arr);
				
				return \Redirect::to("employeeprofile?id=".$values["id"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("employeeprofile?id=".$values["id"]);
			}
		}
	}
	
	public function updatePassword(){
	if (\Request::isMethod('post')){
			//$values["test"];
			$values = Input::all();
			$field_names = array("pass1"=>"password");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($val == "password"){
						$fields[$val] = \Hash::make($values[$key]);
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
				
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Employee";
			$data = array("id"=>Auth::user()->id);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("profile");
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("profile");
			}
		}
	}
	
	public function updateEmployeePassword(){
		if (\Request::isMethod('post')){
			//$values["test"];
			$values = Input::all();
			$field_names = array("pass1"=>"password");
			$fields = array();
			foreach ($field_names as $key=>$val){
				if(isset($values[$key])){
					if($val == "password"){
						$fields[$val] = \Hash::make($values[$key]);
					}
					else{
						$fields[$val] = $values[$key];
					}
				}
			}
			$db_functions_ctrl = new DBFunctionsController();
			$table = "\Employee";
			$data = array("id"=>$values["id"]);
			if($db_functions_ctrl->update($table, $fields, $data)){
				\Session::put("message","Operation completed Successfully");
				return \Redirect::to("employeeprofile?id=".$values["id"]);
			}
			else{
				\Session::put("message","Operation Could not be completed, Try Again!");
				return \Redirect::to("employeeprofile?id=".$values["id"]);
			}
		}
	}

}
