<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/mysqltest', function()
{
	$username="root";
	$password="";
	$database="globaletm";
	//$con=mysqli_connect("localhost","root","","globaletm");	
	$con = new mysqli("localhost","root","","etm_global_new");	
	// Check connection
	if (mysqli_connect_errno())
	{
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}	
	$file = fopen("veh_slogs_9582.csv","r");
	$i=0;
	while(!feof($file))
	{
		$line =  fgets($file);
		if($line=="")
			continue;
			$fields = explode(",", $line);
			//print_r($fields);
			//continue;
			$vehid = 0;
			if(count($fields)>0){
				if(true){
					$query = "SELECT vehicle1.veh_reg FROM vehicle1 join contract_vehicles1 on contract_vehicles1.vehicleId=vehicle1.veh_id where contract_vehicles1.id=".$fields[3];
					$veh_reg = "";
					if ($result=mysqli_query($con,$query))
					{
						$row=mysqli_fetch_row($result);
						mysqli_free_result($result);
						$query1 = "SELECT contract_vehicles.id, contractId, contract_vehicles.driver1Id, contract_vehicles.driver2Id, contract_vehicles.helperId FROM contract_vehicles join vehicle on contract_vehicles.vehicleId=vehicle.id where vehicle.veh_reg='".$row[0]."'";
						if ($result1 = mysqli_query($con,$query1))
						{
							//if($i==4000) break;
							$row1 = mysqli_fetch_row($result1);	
							//print_r($row1);
							//echo "<br/>";
							
							
							$serviceDate = date("Y-m-d",strtotime($fields[5]));
							$sql = "select count(*) from service_logs where serviceDate='$serviceDate' and startReading=$fields[7] and endReading=$fields[8] and contractVehicleId=$row1[0]";
							//echo $sql."<br/>";
							if ($result2 = mysqli_query($con,$sql))
							{
								//echo $row1[0]." - ".$row1[1]." - ".$row[0]."<br/>";
								$row2 = mysqli_fetch_row($result2);
								if($row2[0] == 0){
									$status = 'ACTIVE';
									if($fields[15]== "Yes"){
										$status = 'DELETED';
									}
									$created_at = date("Y-m-d",strtotime($fields[16]));
									$updated_at = date("Y-m-d",strtotime($fields[18]));
									$sql = "INSERT INTO service_logs (oldId, tripNumber, contractId, contractVehicleId, serviceDate, startTime, startReading, endReading, numberOfTrips, distance, repairkms, driver2Id, remarks, driver1Id, helperId, status, created_at, updated_at)
									VALUES ($fields[0],$fields[1],$row1[1],$row1[0],'$serviceDate','$fields[6]',$fields[7],$fields[8],0,".($fields[8]-$fields[7]).",$fields[11],$row1[3],'$fields[12]',$row1[2],$row1[4],'$status','$created_at','$updated_at')";
									echo $sql."<br/>";
									mysqli_query($con,$sql);
								}
								$i++;								
							}							
						}
					}
				}				
			}
	}
	mysqli_close($con);
});


Route::get('/mysqfueltrans', function()
{
	$username="root";
	$password="";
	$database="globaletm";
	//$con=mysqli_connect("localhost","root","","globaletm");
	$con = new mysqli("localhost","root","","etm_global_new");
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$file = fopen("fueltransactions2.csv","r");
	$i=0;
	while(!feof($file))
	{
		$line =  fgets($file);
		if($line=="")
			continue;
		$fields = explode(",", $line);
		//print_r($fields);
		//continue;
		$vehid = 0;
		if(count($fields)>0){
			if(true){
				$query = "SELECT vehicle1.veh_reg FROM vehicle1 where veh_id=".$fields[5];
				$veh_reg = "";
				if ($result=mysqli_query($con,$query))
				{
					$row=mysqli_fetch_row($result);
					mysqli_free_result($result);
					$query1 = "SELECT contractId, vehicle.id FROM contract_vehicles join vehicle on contract_vehicles.vehicleId=vehicle.id where vehicle.veh_reg='".$row[0]."'";
					if ($result1 = mysqli_query($con,$query1))
					{
						//if($i==4000) break;
						$row1 = mysqli_fetch_row($result1);
						//print_r($row1);
						//echo "<br/>";
							
							
						$filledDate = date("Y-m-d",strtotime($fields[10]));
						$sql = "select count(*) from fueltransactions where filledDate='$filledDate' and startReading=$fields[6] and vehicleId=$row1[1]";
						if ($result2 = mysqli_query($con,$sql))
						{
							$row2 = mysqli_fetch_row($result2);
							if(true){  //$row2[0] == 0
								$status = 'ACTIVE';
								if($fields[15]== "Yes"){
									$status = 'DELETED';
								}
								$inchargeId = 0;
								if(intval($fields[29])){
									$inchargeId = $fields[29];
								}
								$created_at = date("Y-m-d",strtotime($fields[26]));
								$updated_at = date("Y-m-d",strtotime($fields[27]));
								echo $row1[0]." - ".$row1[1]." - ".$row[0]."<br/>";
								$sql = "INSERT INTO fueltransactions(tripId, fuelStationId, branchId, clientId, contractId, vehicleId, inchargeId, startReading, litres, amount,   filledDate,    billNo,     filePath,      fullTank,    remarks,      deleted, created_at, updated_at)
								                          VALUES ($fields[0],$fields[2],$fields[3],$fields[4],$row1[0],$row1[1],$inchargeId,$fields[6],$fields[7],$fields[9],'$filledDate','$fields[15]','$fields[14]','$fields[8]','$fields[19]','$fields[22]','$created_at','$updated_at')";
								//echo $sql."<br/>";
								mysqli_query($con,$sql);
							}
							$i++;

						}
							
					}
				}
			}
		}
	}
	mysqli_close($con);
});

Route::get('/', function()
{
	return View::make('masters');
});

Route::get('/insertvehicle', function()
{
	$file = fopen("apsrtc_vehicles.csv","r");
	while(! feof($file))
	{
	  $line =  fgets($file);
	  if($line=="")
	  	continue;
	  $fields = explode(",", $line);
	  //print_r($fields);
	  //continue;
	  if(count($fields)>0){
		  $stateid = 0;
		  //$state = State::where("name","like","%".$fields[0]."%")->first();
		  $state = State::where("name","=",$fields[0])->first();
		  if(count($state)>0){
		  	$stateid = $state->id;
		  }
		  $cityid = 0;
		  $city = City::where("name","=",$fields[1])->first();
		  if(count($city)>0){
		  	$cityid = $city->id;
		  }
		  $vehicletypeid = 0;
		  $lookupvalues = LookupTypeValues::where("name","=",$fields[5])->first();
		  if(count($lookupvalues)>0){
		  	$vehicletypeid = $lookupvalues->id;
		  }
		  //echo $stateid.", ".$cityid.", ".$fields[2].", ".$fields[3].", ".$fields[4].", ".$vehicletypeid.", ".$fields[6].", ".$fields[7].", ".$fields[8].", ".$fields[9].", ".$fields[10].", ".$fields[11].", ".$fields[12].", ".$fields[13].", ".$fields[14]."<br/>";
		  $vehicle = new Vehicle();
		  $vehicle->vehicle_type = $vehicletypeid;
		  $vehicle->veh_reg = $fields[2];
		  $vehicle->eng_no = $fields[3];
		  $vehicle->chsno = $fields[4];
		  $vehicle->yearof_pur = $fields[6]."-01-01";
		  $vehicle->seat_cap = $fields[7];
		  $vehicle->purchase_amount = $fields[10];
		  $vehicle->dep_val = $fields[9];
		  $vehicle->actual_cost = $fields[11];
		  $vehicle->emi = $fields[14];		 
		  $vehicle->total_emis = $fields[12];
		  $vehicle->paid_emis = $fields[13];
		  $vehicle->remarks = $fields[8];
		  $vehicle->state_id = $stateid;
		  $vehicle->city_id = $cityid;
		  $vehicle->save();
		  $i=0;
		  $l = 15;
		  for($i=0; $i<5; $i++){
		  	$transid =  strtoupper(uniqid().mt_rand(100,999));
		  	$chars = array("a"=>"1","b"=>"2","c"=>"3","d"=>"4","e"=>"5","f"=>"6");
		  	foreach($chars as $k=>$v){
		  		$transid = str_replace($k, $v, $transid);
		  	}
		  	$expense = new ExpenseTransaction();
		  	$expense->transactionId = $transid;
		  	$expense->branchId = 1;
		  	if($l==15){
		  		$expense->lookupValueId = 299;
		  	}
		  	if($l==16){
		  		$expense->lookupValueId = 297;
		  	}
		  	if($l==17){
		  		$expense->lookupValueId = 300;
		  	}
		  	if($l==18){
		  		$expense->lookupValueId = 301;
		  	}
		  	if($l==19){
		  		$expense->lookupValueId = 302;
		  	}		  	
		  	$expense->name = "expense";
		  	$expense->date = date("Y-m-d");
		  	$expense->amount = 0;
		  	$expense->nextAlertDate = date("Y-m-d",strtotime($fields[$l]));
		  	$expense->vehicleId = $vehicle->id;
		  	$expense->vehicleIds = $vehicle->id;
		  	$expense->workFlowStatus = "Approved";
		  	//echo $expense->name.", ".$expense->date.", ".$expense->nextAlertDate.", ".$expense->vehicleId.", ".$expense->vehicleIds."<br/>";
		  	$expense->save();
		  	$l++;
		  }
	  }
	}
	fclose($file);
});

Route::get('/logout', function()
{
	$rec = LoginLog::where("user_id","=",\Auth::user()->id)->orderBy("id","desc")->first();
	LoginLog::where("id","=",$rec->id)->update(array("logouttime"=>date('H:i:s', time())));
	Auth::logout();
	Session::flush();
	return Redirect::to('/index');
});

Route::get('/mailtest', function()
{
	$fields = array();
	$fields['transactionType'] = "INSERT";
	$fields['tableName'] = "Test Table";
	$fields['recId'] = 1;
	$fields['oldValues'] = "no old values";
	$fields['newValues'] = "no new values";
	$fields['insertedBy'] = "Satya";
	Mail::queue('emails.welcome', $fields, function($message)
	{
		$subject = "ETM APPLICATION TRANSACTIONS ON : ".date("d-m-Y");
		$message->to('rayisatyanarayana22@gmail.com', 'Satya')->subject($subject);
	});
});

Route::get('/objtest', function()
{
	$table = "\DBTransactions";
	$data  = array();
	$data['id'] = 1;
	$table = new $table();
	$table_name = $table->getTable();
	$tfields = \DB::select("show fields from ".$table_name);
	$table = "\DBTransactions";
	$recs = $table::where('id', "=",$data['id'])->get();
	if(count($recs)>0){
		$recs = $recs[0];
		foreach ($tfields as $tfield){
			if($tfield->Field != "created_at" && $tfield->Field != "updated_at"){
				if($tfield->Field == "createdBy" || $tfield->Field == "updatedBy"){
					if($recs[$tfield->Field]>0){
						$emp = \Employee::where('id', "=",$recs[$tfield->Field])->get();
						if(count($emp)>0){
							$emp = $emp[0];
							$emp_name = $emp->fullName;
							$recs[$tfield->Field] = $emp_name;
						}						
					}
					else{
						$recs[$tfield->Field] = "";
					}
				}
				echo $recs[$tfield->Field]."<br/>";
			}
		}
	}
});

Route::post('/login', function()
{
	$values = Input::All();
	if (Auth::attempt(array('emailId' => $values["email"], 'password' => $values["password"])))
	{
	    if(Auth::user()->status != "ACTIVE"){
	    	Session::flash('message', 'wrong username/password');
	    	return View::make('masters.login');
	    }
		$roleid = Auth::user()->rolePrevilegeId;
	    $privileges = RolePrivileges::where("roleId","=",$roleid)->get();
	    $privileges_arr = array();
	    foreach ($privileges as $privilege){
	    	$privileges_arr[] = $privilege->jobId;
	    }
	    Session::put("jobs",$privileges_arr);
	    
	    $rec = Parameters::where("name","=","banner type")->get();
	    $rec = $rec[0];
	    Session::put("banner_type",$rec->value);
	    
	    $rec = Parameters::where("name","=","banner")->get();
	    $rec = $rec[0];
	    Session::put("banner",$rec->value);
	    
	    $rec = Parameters::where("name","=","title")->get();
	    $rec = $rec[0];
	    Session::put("title",$rec->value);
	    
	    $ip = "";
	    if(!empty($_SERVER["HTTP_CLIENT_IP"])){ $ip = $_SERVER["HTTP_CLIENT_IP"]; }
	    elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){ $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
	    else{$ip = $_SERVER["REMOTE_ADDR"]; }
	     
	    $fields = array();
	    $fields['user_id'] = Auth::user()->id;
	    $fields['empid'] = Auth::user()->empCode;
	    $fields['user_full_name'] = Auth::user()->fullName;
	    $fields['ipaddress'] = $ip;
	    $fields['logindate'] = date("Y-m-d");
	    $fields['logintime'] = date('H:i:s', time());
	    $db_functions_ctrl = new \masters\DBFunctionsController();
	    $table = "LoginLog";
	    $db_functions_ctrl->insert($table, $fields);
	    
		return Redirect::intended('dashboard');
	}
	else{
		Session::flash('message', 'wrong username/password');
		return View::make('masters.login');
	}
});

Route::get('/masters', function()
{
	return View::make('masters.masters');
});

Route::any('/getdatatabledata',"masters\DataTableController@getDataTableData");

Route::get('/printdailytransactions', function()
{
	return View::make('reports.printdailytransactions');
});


Route::any('/gettransactiondatatabledata',"transactions\DataTableController@getDataTableData");

Route::any('/gettripsdatatabledata',"trips\DataTableController@getDataTableData");

Route::get('/employees',"masters\EmployeeController@manageEmployees");

Route::post('/terminateemployee',"masters\EmployeeController@terminateEmployee");

Route::post('/blockemployee',"masters\EmployeeController@blockEmployee");

Route::post('/rejoinemployee',"masters\EmployeeController@rejoinEmployee");

Route::get('/addemployee', function()
{
	return View::make('masters.addemployee');
});

Route::get('/verifyemailid',"masters\EmployeeController@verifyEmailId");

Route::get('/getclientbranches',"masters\EmployeeController@getClientBranches");

Route::get('/getempid',"masters\EmployeeController@getEmpId");

Route::post('/addemployee',"masters\EmployeeController@addEmployee");

Route::post('/employee',"masters\EmployeeController@manageEmployees");

Route::get('/editemployee',"masters\EmployeeController@editEmployee");

Route::get('/states', "masters\StateController@manageStates");

Route::any('/addstate', "masters\StateController@addState");

Route::any('/editstate', "masters\StateController@editState");

Route::get('/districts', "masters\DistrictController@manageDistricts");

Route::any('/adddistrict', "masters\DistrictController@addDistrict");

Route::any('/editdistrict', "masters\DistrictController@editDistrict");

Route::get('/cities', "masters\CityController@manageCities");

Route::any('/addcity', "masters\CityController@addCity");

Route::any('/editcity', "masters\CityController@editCity");

Route::get('/getcitiesbystateid', "masters\CityController@getCitiesbyStateId");

Route::get('/getdepotsbycityid', "masters\CityController@getDepotsbyCityId");

Route::get('/getdepotsbyclientId', "masters\CityController@getDepotsbyClientId");

Route::get('/getbranchbycityid', "masters\CityController@getBranchbyCityId");

Route::get('/officebranches', "masters\OfficeBranchController@manageOfficeBranches");

Route::any('/addofficebranch', "masters\OfficeBranchController@addOfficeBranch");

Route::any('/editofficebranch', "masters\OfficeBranchController@editOfficeBranch");

Route::get('/vehicles', "masters\VehicleController@manageVehicles");

Route::any('/addvehicle', "masters\VehicleController@addVehicle");

Route::any('/editvehicle', "masters\VehicleController@editVehicle");

Route::any('/blockvehicle', "masters\VehicleController@blockVehicle");

Route::any('/sellvehicle', "masters\VehicleController@sellVehicle");

Route::any('/renewvehicle', "masters\VehicleController@renewVehicle");

Route::get('/employeebattas', "masters\EmployeeBattaController@manageEmployeeBattas");

Route::any('/addemployeebatta', "masters\EmployeeBattaController@addEmployeeBatta");

Route::any('/editemployeebatta', "masters\EmployeeBattaController@editEmployeeBatta");

Route::any('/validatedrivinglicense', "masters\EmployeeController@ValidateDrivingLicence");

Route::get('/servicedetails', "masters\ServiceDetailsController@manageServiceDetails");

Route::any('/addservicedetails', "masters\ServiceDetailsController@addServiceDetails");

Route::any('/editservicedetails', "masters\ServiceDetailsController@editServiceDetails");

Route::get('/lookupvalues', "masters\LookupValueController@manageLookupValues");

Route::any('/addlookupvalue', "masters\LookupValueController@addLookupValue");

Route::any('/editlookupvalue', "masters\LookupValueController@editLookupValue");

Route::get('/bankdetails', "masters\BankDetailsController@manageBankDetails");

Route::any('/addbankdetails', "masters\BankDetailsController@addBankDetails");

Route::any('/editbankdetails', "masters\BankDetailsController@editBankDetails");

Route::any('/getbankaccounts', "masters\BankDetailsController@getBankAccounts");

Route::get('/cards', "masters\CardsController@manageCards");

Route::any('/addcard', "masters\CardsController@addCard");

Route::any('/editcard', "masters\CardsController@editCard");

Route::any('/validatecardnumber', "masters\CardsController@validateCardNumber");

Route::get('/financecompanies', "masters\FinanceCompanyController@manageFinanceCompanies");

Route::any('/addfinancecompany', "masters\FinanceCompanyController@addFinanceCompany");

Route::any('/editfinancecompany', "masters\FinanceCompanyController@editFinanceCompany");

Route::get('/creditsuppliers', "masters\CreditSupplierController@manageCreditSupplier");

Route::any('/addcreditsupplier', "masters\CreditSupplierController@addCreditSupplier");

Route::any('/editcreditsupplier', "masters\CreditSupplierController@editCreditSupplier");

Route::get('/salarydetails', "masters\SalaryDetailsController@manageSalaryDetails");

Route::any('/addsalarydetails', "masters\SalaryDetailsController@addSalaryDetails");

Route::any('/editsalarydetails', "masters\SalaryDetailsController@editSalaryDetails");

Route::any('/addincreament', "masters\SalaryDetailsController@addIncreament");

Route::get('/fuelstations', "masters\FuelStationController@manageFuelStations");

Route::any('/addfuelstation', "masters\FuelStationController@addFuelStation");

Route::any('/editfuelstation', "masters\FuelStationController@editFuelStation");

Route::get('/loans', "masters\LoanController@manageLoans");

Route::any('/addloan', "masters\LoanController@addLoan");

Route::any('/editloan', "masters\LoanController@editLoan");

Route::get('/getfinancecompanybycityid', "masters\CityController@getfinanceCompanybyCityId");

Route::get('/dailyfinances', "masters\DailyFinanceController@manageDailyFinances");

Route::any('/adddailyfinance', "masters\DailyFinanceController@addDailyFinance");

Route::any('/editdailyfinance', "masters\DailyFinanceController@editDailyFinance");

Route::get('/serviceproviders', "masters\ServiceProviderController@manageServiceProviders");

Route::any('/addserviceprovider', "masters\ServiceProviderController@addServiceProvider");

Route::any('/editserviceprovider', "masters\ServiceProviderController@editServiceProvider");

Route::get('/uploads', "masters\UploadsController@manageUploads");

Route::any('/addupload', "masters\UploadsController@addUpload");

Route::any('/editupload', "masters\UploadsController@editUpload");

Route::any('/postfile', "transactions\TransactionController@postFile");

Route::get('/transactions', "transactions\TransactionController@manageTransactions");

Route::any('/clientincometransactions', "transactions\ClientIncomeController@manageClientIncome");

Route::any('/addclientincome', "transactions\ClientIncomeController@addClientIncome");

Route::get('/editclientincome', "transactions\ClientIncomeController@editClientIncome");

Route::get('/incometransactions', "transactions\TransactionController@manageIncomeTransactions");

Route::get('/expensetransactions', "transactions\TransactionController@manageExpenseTransactions");

Route::get('/fueltransactions', "transactions\TransactionController@manageFuelTransactions");

Route::get('/getendreading', "transactions\TransactionController@getEndReading");

Route::get('/getpreviouslogs', "transactions\TransactionController@getPreviousLogs");

Route::any('/addtransaction', "transactions\TransactionController@addTransaction");

Route::any('/edittransaction', "transactions\TransactionController@editTransaction");

Route::any('/deletetransaction', "transactions\TransactionController@deleteTransaction");

Route::get('/repairtransactions', "transactions\RepairTransactionController@manageRepairTransactions");

Route::any('/createrepairtransaction', "transactions\RepairTransactionController@createRepairTransaction");

Route::any('/addrepairtransaction', "transactions\RepairTransactionController@addRepairTransaction");

Route::any('/editrepairtransaction', "transactions\RepairTransactionController@editRepairTransaction");

Route::get('/viewrepairtransactionitems', "transactions\RepairTransactionItemController@manageRepairTransactionItems");

Route::any('/editrepairtransactionitem', "transactions\RepairTransactionItemController@editRepairTransactionItem");

Route::any('/deleterepairtransaction', "transactions\RepairTransactionController@deleteRepairTransaction");

Route::get('/getpaymentfields', "transactions\TransactionController@getPaymentFields");

Route::get('/getinchargebalance', "transactions\TransactionController@getInchargeBalance");

Route::get('/getmasterspaymentfields', "transactions\TransactionController@getMastersPaymentFields");

Route::get('/getfueltransactionfields', "transactions\TransactionController@getFuelTransactionFields");

Route::any('/getvehiclelastreading', "transactions\TransactionController@getVehicleLastReading");

Route::get('/gettransactionfields', "transactions\TransactionController@getTransactionFields");

Route::get('/dailytrips', "trips\TripsController@showDailyTrips");

Route::any('/adddailytrips', "trips\TripsController@addDailyTrips");

Route::any('/managetrips', "trips\TripsController@manageTrips");

Route::any('/canceldailytrip', "trips\TripsController@cancelDailyTrip");

Route::any('/tripcancelinfo', function() {
	return View::make('trips.tripcancelinfo');
});

Route::any('/uncanceldailytrip', "trips\TripsController@unCancelDailyTrip");

Route::any('/editdailytrip', "trips\TripsController@editDailyTrip");

Route::any('/edittripparticular', "trips\TripsController@editTripParticular");

Route::any('/addtripparticular', "trips\TripsController@addTripParticular");

Route::any('/gettripparticularfields', "trips\TripsController@getFields");

Route::any('/addtripfuel', "trips\TripsController@addTripFuel");

Route::any('/addlocaltripfuel', "trips\TripsController@addLocalTripFuel");

Route::any('/closetrip', "trips\TripsController@closeTrip");

Route::any('/tripclosingreport', "trips\TripsController@tripClosingReport");

Route::any('/addlocaltrip', "trips\TripsController@addLocalTrip");

Route::any('/cancellocaltrip', "trips\TripsController@cancelLocalTrip");

Route::any('/assigndrivervehicle', "trips\TripsController@assignDriverVehicle");

Route::any('/editassignedvehicle', "trips\TripsController@editassignedvehicle");

Route::any('/editlocaltrip', "trips\TripsController@editLocalTrip");

Route::any('/deletebooking', "trips\TripsController@deleteBooking");

Route::any('/printlocaltrip', "trips\TripsController@printLocalTrip");

Route::any('/addlocaltripparticular', "trips\TripsController@addLocalTripParticular");

Route::any('/bookingrefund', "trips\TripsController@bookingRefund");

Route::any('/roles', "rolejobs\RoleController@manageRoles");

Route::any('/addrole', "rolejobs\RoleController@addRole");

Route::any('/editrole', "rolejobs\RoleController@editRole");

Route::any('/jobs', "rolejobs\JobsController@manageJobs");

Route::any('/roleprivileges', "rolejobs\JobsController@rolePrivileges");

Route::any('/payemployeesalary', "salaries\SalariesController@payDriversSalary");

Route::any('/payofficeemployeesalary', "salaries\SalariesController@payOfficeEmployeeSalary");

Route::any('/getempsalary', "salaries\SalariesController@getEmpSalary");

Route::any('/getcalempsalary', "salaries\SalariesController@getCalEmpSalary");

Route::any('/getcalofficeempsalary', "salaries\SalariesController@getCalOfficeEmpSalary");

Route::any('/addemployeesalary', "salaries\SalariesController@addEmployeeSalary");

Route::any('/editsalarytransaction', "salaries\SalariesController@editSalaryTransaction");

Route::any('/gettransactionamount', "salaries\SalariesController@getTransactionAmount");

Route::any('/leaves', "salaries\LeavesController@manageLeaves");

Route::any('/addleave', "salaries\LeavesController@addLeave");

Route::any('/editleave', "salaries\LeavesController@editLeave");

Route::any('/getemployeesbyoffice', "salaries\LeavesController@getEmployeesByOffice");

Route::any('/getemployeesbydepot', "salaries\LeavesController@getEmployeesByDepot");

Route::any('/approveleave', "salaries\LeavesController@approveLeave");

Route::any('/rejectleave', "salaries\LeavesController@rejectLeave");

Route::any('/getleavedetails', "salaries\LeavesController@leaveDetails");

Route::any('/salaryadvances', "salaries\SalaryAdvancesController@manageSalaryAdvances");

Route::any('/addsalaryadvance', "salaries\SalaryAdvancesController@addSalaryAdvance");

Route::any('/editsalaryadvance', "salaries\SalaryAdvancesController@editSalaryAdvance");

Route::any('/deletesalaryadvance', "salaries\SalaryAdvancesController@deleteSalaryAdvance");

Route::any('/getsalarydatatabledata', "salaries\DataTableController@getDataTableData");

Route::get('/inventorylookupvalues', "inventory\LookupValueController@manageLookupValues");

Route::any('/addinventorylookupvalue', "inventory\LookupValueController@addLookupValue");

Route::any('/editinventorylookupvalue', "inventory\LookupValueController@editLookupValue");

Route::any('/purchaseorder', "inventory\PurchaseOrderController@managePurchaseOrders");

Route::any('/createpurchaseorder', "inventory\PurchaseOrderController@createPurchaseOrder");

Route::any('/addpurchaseorder', "inventory\PurchaseOrderController@addPurchaseOrder");

Route::any('/editpurchaseorder', "inventory\PurchaseOrderController@editPurchaseOrder");

Route::any('/deletepurchaseorder', "inventory\PurchaseOrderController@deletePurchaseOrder");

Route::any('/estimatepurchaseorders', "inventory\EstimatePurchaseOrderController@manageEstimatePurchaseOrders");

Route::any('/addestimatepurchaseorder', "inventory\EstimatePurchaseOrderController@addEstimatePurchaseOrders");

Route::any('/editestimatepurchaseorder', "inventory\EstimatePurchaseOrderController@editEstimatePurchaseOrder");

Route::any('/deleteestimatepurchaseorder', "inventory\EstimatePurchaseOrderController@deleteEstimatePurchaseOrder");

Route::any('/viewpurchaseditems', "inventory\purchaseOrderItemController@managePurchaseOrderItems");

Route::any('/editpurchaseditem', "inventory\purchaseOrderItemController@editPurchasedItem");

Route::any('/deletepurchaseorderitem', "inventory\purchaseOrderItemController@deletePurchaseOrderItem");

Route::get('/getmanufacturers', "inventory\PurchaseOrderController@getManufacturers");

Route::get('/getcreditsuppliersbystate', "inventory\PurchaseOrderController@getCreditSuppliersByState");

Route::get('/verifybillno', "inventory\PurchaseOrderController@verifyBillNo");

Route::any('/manufacturers', "inventory\ManufacturesController@manageManufacturers");

Route::any('/addmanufacturer', "inventory\ManufacturesController@addManufacturer");

Route::any('/editmanufacturer', "inventory\ManufacturesController@editManufacturer");

Route::any('/getinventorydatatabledata', "inventory\DataTableController@getDataTableData");

Route::any('/itemcategories', "inventory\ItemCategoriesController@manageItemCategories");

Route::any('/additemcategory', "inventory\ItemCategoriesController@addItemCategory");

Route::any('/edititemcategory', "inventory\ItemCategoriesController@editItemCategory");

Route::any('/itemtypes', "inventory\ItemTypesController@manageItemTypes");

Route::any('/additemtype', "inventory\ItemTypesController@addItemType");

Route::any('/edititemtype', "inventory\ItemTypesController@edItitemType");

Route::any('/items', "inventory\ItemsController@manageItems");

Route::any('/additem', "inventory\ItemsController@addItem");

Route::any('/edititem', "inventory\ItemsController@editItem");

Route::any('/useitems', "inventory\StockController@useItems");

Route::any('/addusedstock', "inventory\StockController@addInventoryTransaction");

Route::any('/deleteusedstockitem', "inventory\StockController@deleteUsedStockItem");

Route::any('/getitemsbyaction', "inventory\StockController@getFields");

Route::any('/getiteminfo', "inventory\StockController@getItemInfo");

Route::any('/getrepairitembysupplier', "inventory\StockController@getRepairItemsBySupplier");

Route::any('/getalertinfo', "inventory\StockController@getAlertInfo");

Route::get('/reports', function()
{
	return View::make('reports.reports');
});

Route::any('/report', "reports\ReportsController@getReport");

Route::any('/getreport', "reports\ReportsController@getReport");

Route::any('/carryforward', "reports\ReportsController@carryForward");

Route::any('/getreportsdatatabledata', "reports\DataTableController@getDataTableData");

Route::any('/processbranchsuspense', "reports\ReportsController@processBranchSuspense");

Route::any('/transactionblocking', "masters\BlockDataEntryController@getTransactionBlocking");

Route::any('/editparameter', "masters\ParameterController@editParameter");

Route::any('/edittransactionblocking', "masters\BlockDataEntryController@editTransactionBlocking");

Route::any('/verifytransactiondateandbranch', "masters\BlockDataEntryController@verifyTransactionDateandBranch");

Route::get('/showalerts', function() {
	return View::make('alerts.showalerts');
});

Route::get('/showempincreamentalerts', function() {
	return View::make('alerts.showempincrementalerts');
});

Route::get('/profile', function() {
	return View::make('settings.profile');
});

Route::get('/employeeprofile', function() {
	return View::make('settings.employeeprofile');
});

Route::get('/settings', function() {
	return View::make('settings.appsettings');
});

Route::any('/updateprofile', "settings\UserSettingsController@updateprofile");
	
Route::any('/updatepassword', "settings\UserSettingsController@updatepassword");

Route::any('/updateemployeeprofile', "settings\UserSettingsController@updateEmployeeProfile");

Route::any('/updateemployeepassword', "settings\UserSettingsController@updateEmployeePassword");
	
Route::any('/updatebannersettings', "settings\AppSettingsController@updateBannerSettings");

Route::any('/checkvalidation', "settings\AppSettingsController@checkDuplicateEntry");

Route::get('/contractsmenu', function() {
	return View::make('masters.contracts');
});

Route::any('/getcontractsdatatabledata', "contracts\DataTableController@getDataTableData");
	
Route::get('/clients', "contracts\ClientController@manageClients");

Route::any('/addclient', "contracts\ClientController@addClient");

Route::any('/editclient', "contracts\ClientController@editClient");

Route::get('/depots', "contracts\DepotController@manageDepots");

Route::any('/adddepot', "contracts\DepotController@addDepot");

Route::any('/editdepot', "contracts\DepotController@editDepot");

Route::get('/contracts', "contracts\ContractController@manageContracts");

Route::any('/addcontract', "contracts\ContractController@addContract");

Route::any('/editcontract', "contracts\ContractController@editContract");

Route::any('/getvehicleactivestatus', "contracts\ContractController@getVehicleActiveStatus");

Route::get('/servicelogs', "servicelogs\ServiceLogController@manageServiceLogs");

Route::any('/addservicelog', "servicelogs\ServiceLogController@addServiceLog");

Route::any('/editservicelog', "servicelogs\ServiceLogController@editServiceLog");

Route::get('/checkpendingdates', "servicelogs\ServiceLogController@checkPendingDates");

Route::get('/servicelogrequests', "servicelogs\ServiceLogRequestController@manageServiceLogRequests");

Route::any('/addservicelogrequest', "servicelogs\ServiceLogRequestController@addServiceLogRequest");

Route::any('/editservicelogrequest', "servicelogs\ServiceLogRequestController@editServiceLogRequest");

Route::any('/updateservicelogrequeststatus', "servicelogs\ServiceLogRequestController@updateServiceLogRequestStatus");

Route::any('/getvehiclecontractinfo', "servicelogs\ServiceLogController@getVehicleContractInfo");

Route::get('/getdriverhelper', "servicelogs\ServiceLogController@getDriverHelper");

Route::get('/getstartreading', "servicelogs\ServiceLogController@getStartReading");

Route::get('/getstartreadingsubstitute', "servicelogs\ServiceLogController@getStartReadingSubstitute");

Route::get('/getpendingservicelogs', "servicelogs\ServiceLogController@getPendingServiceLogs");

Route::any('/viewpendingservicelogs', "servicelogs\ServiceLogController@viewPendingServiceLogs");

Route::any('/getservicelogsdatatabledata', "servicelogs\DataTableController@getDataTableData");

Route::get('/vehiclemeeters', "contracts\VehicleMeeterController@manageVehicleMeeters");

Route::any('/addvehiclemeeter', "contracts\VehicleMeeterController@addVehicleMeeter");

Route::any('/editvehiclemeeter', "contracts\VehicleMeeterController@editVehicleMeeter");

Route::any('/getmeeterno', "contracts\VehicleMeeterController@getMeeterNo");

Route::get('/clientholidays', "contracts\ClientHolidaysController@manageClientHolidays");

Route::any('/addclientholidays', "contracts\ClientHolidaysController@addclientholidays");

Route::any('/editclientholidays', "contracts\ClientHolidaysController@editclientholidays");

Route::any('/updateclientholidaysrequeststatus', "contracts\ClientHolidaysController@updateClientHolidaysRequestStatus");

Route::any('/loanpayments',"transactions\LoanPaymentsController@manageLoanPayments");

Route::any('/addloanpayments',"transactions\LoanPaymentsController@addLoanPayments");

Route::any('/billpayments',"billpayments\BillPaymentsController@manageBillPayments");

Route::any('/addbillpayment',"billpayments\BillPaymentsController@addBillPayment");

Route::any('/editbillpayment',"billpayments\BillPaymentsController@editBillPayments");

Route::any('/getbillpaymentsdatatabledata', "billpayments\DataTableController@getDataTableData");

Route::get('/getbillno',"billpayments\BillPaymentsController@getBillNo");

Route::get('/gettotalamount',"billpayments\BillPaymentsController@getTotalAmount");

Route::get('/workflow',"workflow\WorkFlowController@transactionsWorkFlow");

Route::any('/getworkflowdatatabledata', "workflow\DataTableController@getDataTableData");

Route::any('/workflowupdate', "workflow\WorkFlowController@workFlowUpdate");

Route::any('/attendence', "attendence\AttendenceController@manageAttendence");

Route::any('/getattendencedatatabledata', "attendence\DataTableController@getDataTableData");

Route::any('/addattendence', "attendence\AttendenceController@addAttendence");

Route::any('/updateattendence', "attendence\AttendenceController@updateAttendence");

Route::any('/addattendencelog', "attendence\AttendenceController@addAttendenceLog");

Route::any('/getattendencelog', "attendence\AttendenceController@getAttendenceLog");

Route::any('/getdaytotalattendence', "attendence\AttendenceController@getDayTotalAttendence");

Route::get('/dashboard', function()
{
	return View::make('settings.dashboard');
});

Route::any('/getDashboardDataTableData', "settings\DashboardController@getDashboardDataTableData");

