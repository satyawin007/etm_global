<?php
use Illuminate\Support\Facades\Input;
?>
@extends('masters.master')
	@section('inline_css')
		<style>
			.pagination {
			    display: inline-block;
			    padding-left: 0;
			    padding-bottom:10px;
			    margin: 0px 0;
			    border-radius: 4px;
			}
			.dataTables_wrapper .row:last-child {
			    border-bottom: 0px solid #e0e0e0;
			    padding-top: 5px;
			    padding-bottom: 0px;
			    background-color: #EFF3F8;
			}
			th {
			    white-space: nowrap;
			}
			td {
			    white-space: nowrap;
			}
			panel-group .panel {
			    margin-bottom: 20px;
			    border-radius: 4px;
			}
			label{
				text-align: right;
				margin-top: 5px;
			}
			.table {
			    width: 100%;
			    max-width: 100%;
			    margin-bottom: 0px;
			}
			.form-actions {
			    display: block;
			    background-color: #F5F5F5;
			    border-top: 1px solid #E5E5E5;
			    /* margin-bottom: 20px; 
			    margin-top: 20px;
			    padding: 19px 20px 20px;*/
			}
		</style>
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
		<link rel="stylesheet" href="../assets/css/daterangepicker.css" />
	@stop
		
	@stop
	
	@section('bredcum')	
		<small>
			ROLES & PRIVILAGES
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<!-- PAGE CONTENT BEGINS -->
			<div class="col-xs-12">
                <div style="height: 10px;"></div>
                <div class="" role="alert"></div>
                <?php 
                   $values = Input::All();	
                   $role = Role::where("id","=",$values['id'])->first();
                   $roleid = $role->id;
                   $role = $role->roleName;
                   $jobs = \RolePrivileges::where("roleId","=",$roleid)->get();
                   $jobs_arr = array();
                   foreach ($jobs as $job){
                   	$jobs_arr[] = $job->jobId;
                   }
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading" style="background: #438eb9;">
                        <h3 class="panel-title ng-binding" style="color: #F8FFE4; margin-left: 4px;">Roles &amp; Privileges for {{$role}}</h3>
                    </div>
                    <div class="panel-body" style="padding-top: 5px;">
                        <div class="row" style="margin-top: 0px;">
                            <form name="rolesCreate" action="roleprivileges" method="post" class="ng-pristine ng-valid">
                            	<input type="hidden" name="roleid" value="{{$roleid}}">
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab1" class="ng-pristine ng-untouched ng-valid">
                                                    Main Tabs
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                            	<?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "Show ADMINISTRATION Tab";
                                            		$menu_arr[] = "Show INCOME & EXPENSES Tab";
                                            		$menu_arr[] = "Show TRIPS & SERVICES Tab";
                                            		$menu_arr[] = "Show CONTRACTS Tab";
                                            		$menu_arr[] = "Show OTHERS Tab";
                                            		$menu_arr[] = "Show STOCK & INVENTORY Tab";
                                            		$menu_arr[] = "Show REPORTS Tab";
                                            		$menu_arr[] = "Show SETTINGS Tab";
                                            		$menu_arr[] = "Show WORK FLOW Tab";
                                            		$i=1;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab2" class="ng-pristine ng-untouched ng-valid">
                                                    Sub Tabs
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                            	<?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "Show MASTERS Tab";
                                            		$menu_arr[] = "Show VERIFY BRANCH DAILY SETLEMENTS Tab";
                                            		$menu_arr[] = "Show TRANSACTION BLOCKING Tab";
                                            		$menu_arr[] = "Show MANAGE PREVILAGES Tab";
                                            		
                                            		$menu_arr[] = "Show INCOME TRANSACTIONS Tab";
                                            		$menu_arr[] = "Show EXPENSE TRANSACTIONS Tab";
                                            		$menu_arr[] = "Show FUEL TRANSACTIONS Tab";
                                            		$menu_arr[] = "Show REPAIR TRANSACTIONS Tab";
                                            		$menu_arr[] = "Show EMPLOYEE SALARY Tab";
                                            		$menu_arr[] = "Show BILLS & PAYMENTS Tab";
                                            		
                                            		$menu_arr[] = "Show LOCAL TRIPS Tab";
                                            		$menu_arr[] = "Show DAILY TRIPS Tab";
                                            		
                                            		$menu_arr[] = "Show EMPLOYEE LEAVES Tab";
                                            		$menu_arr[] = "Show SALARY ADVANCES Tab";
                                            		
                                            		$menu_arr[] = "Show LOOKUP VALUES Tab";
                                            		$menu_arr[] = "Show MANUFACTURERS Tab";
                                            		$menu_arr[] = "Show ITEM CATEGORIES Tab";
                                            		$menu_arr[] = "Show ITEM TYPES Tab";
                                            		$menu_arr[] = "Show ITEMS Tab";
                                            		$menu_arr[] = "Show PURCHASE ORDERS Tab";
                                            		$menu_arr[] = "Show USE STOCK ITEMS Tab";
                                            		$menu_arr[] = "Show ESTIMATE PURCHASE ORDER Tab";
                                            		$i=101;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                 <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab3" class="ng-pristine ng-untouched ng-valid">
                                                    Master Items
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "Show EMPLOYEES ";
                                            		$menu_arr[] = "Show STATES";
                                            		$menu_arr[] = "Show DISTRICTS";
                                            		$menu_arr[] = "Show CITIES";
                                            		$menu_arr[] = "Show OFFICE BRANCHES";
                                            		$menu_arr[] = "Show VEHICLES";
                                            		$menu_arr[] = "Show DRIVER/HELPER BATTA";
                                            		$menu_arr[] = "Show SERVICE NOs";
                                            		$menu_arr[] = "Show MEETER READING";
                                            		$menu_arr[] = "Show LOOKUP DATA";
                                            		$menu_arr[] = "Show BANK ACCOUNTS";
                                            		$menu_arr[] = "Show   FINANCIAL COMPANIES ";
                                            		$menu_arr[] = "Show CREDIT SUPPLIERS";
                                            		$menu_arr[] = "Show  SALARY DETAILS";
                                            		$menu_arr[] = "Show  FUEL STATIONS ";
                                            		$menu_arr[] = "Show  LOANS ";
                                            		$menu_arr[] = "Show  DAILY FINANCES ";
                                            		$menu_arr[] = "Show  MANAGE SERVICE PROVIDERS ";
                                            		$i=151;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab4" class="ng-pristine ng-untouched ng-valid">
                                                    Masters operations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                 <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "create EMPLOYEE ";
                                            		$menu_arr[] = "salary add/edit of EMPLOYEE";
                                            		$menu_arr[] = "edit OFFICE EMPLOYEE";
                                            		$menu_arr[] = "edit NON OFFICE EMPLOYEE";
                                            		$menu_arr[] = "block  EMPLOYEE";
                                            		$menu_arr[] = "create STATE";
                                            		$menu_arr[] = "edit STATE";
                                            		$menu_arr[] = "create DISTRICT";
                                            		$menu_arr[] = "edit DISTRICT";
                                            		$menu_arr[] = "create CITY";
                                            		$menu_arr[] = "edit CITY";
                                            		$menu_arr[] = "create OFFICE BRANCH";
                                            		$menu_arr[] = "edit OFFICE BRANCHES";
                                            		$menu_arr[] = "create VEHICLE";
                                            		$menu_arr[] = "edit VEHICLE";
                                            		$menu_arr[] = "block VEHICLE";
                                            		$menu_arr[] = "sell VEHICLE";
                                            		$menu_arr[] = "renew VEHICLE";
                                            		$menu_arr[] = "create DRIVER/HELPER BATTA";
                                            		$menu_arr[] = "edit DRIVER/HELPER BATTAS";
                                            		$menu_arr[] = "create SERVICE NO.s";
                                            		$menu_arr[] = "edit SERVICE NO.s";
                                            		$menu_arr[] = "create MEETER READING  ";
                                            		$menu_arr[] = "edit MEETER READING";
                                            		$menu_arr[] = "create LOOKUP VALUE";
                                            		$menu_arr[] = "edit LOOKUP VALUE";
                                            		$menu_arr[] = "create BANK ACCOUNTS";
                                            		$menu_arr[] = "edit BANK ACCOUNTS";
                                            		$menu_arr[] = "create FINANCIAL COMPANIES ";
                                            		$menu_arr[] = "edit FINANCIAL COMPANIES";
                                            		$menu_arr[] = "create CREDIT SUPPLIER ";
                                            		$menu_arr[] = "edit CREDIT SUPPLIER";
                                            		$menu_arr[] = "create SALARY DETAILS";
                                            		$menu_arr[] = " edit SALARY DETAILS";
                                            		$menu_arr[] = "create FUEL STATIONS";
                                            		$menu_arr[] = "edit FUEL STATIONS";
                                            		$menu_arr[] = "create LOAN";
                                            		$menu_arr[] = "edit LOAN ";
                                            		$menu_arr[] = "create DAILY FINANCES";
                                            		$menu_arr[] = "edit DAILY FINANCES";
                                            		$menu_arr[] = "create SERVICE PROVIDERS";
                                            		$menu_arr[] = "edit SERVICE PROVIDERS";
                                            		$menu_arr[] = "add ROLE";
                                            		$menu_arr[] = "edit ROLE";
                                            		$i=201;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab6" class="ng-pristine ng-untouched ng-valid">
                                                    Contract Items
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "Show CONTRACTS ";
                                            		$menu_arr[] = "Show CLIENTS";
                                            		$menu_arr[] = "Show DEPOTS";
                                            		$menu_arr[] = "Show SERVICE LOGS";
                                            		$menu_arr[] = "Show FUEL TRANSACTIONS";
                                            		$menu_arr[] = "Show REPAIR TRANSACTIONS";
                                            		$menu_arr[] = "Show VEHICLE MEETER READING";
                                            		$menu_arr[] = "Show CLIENT HOLIDAYS";
                                            		$menu_arr[] = "Show SERVICE LOG REQUESTS";
                                            		$i=451;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                 <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab7" class="ng-pristine ng-untouched ng-valid">
                                                    Contract operations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                 <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "create CONTRACT ";
                                            		$menu_arr[] = "manage  CONTRACT";
                                            		$menu_arr[] = "create CLIENT";
                                            		$menu_arr[] = "edit CLIENT";
                                            		$menu_arr[] = "create  DEPOT";
                                            		$menu_arr[] = "edit  DEPOT";
                                            		$menu_arr[] = "create SERVICELOG";
                                            		$menu_arr[] = "manage SERVICELOG";
                                            		$menu_arr[] = "create CONTRACT FUEL TRANSACTIONS";
                                            		$menu_arr[] = "edit CONTRACT FUEL TRANSACTIONS";
                                            		$menu_arr[] = "create CONTRACT REPAIR TRANSACTION";
                                            		$menu_arr[] = "edit CONTRACT REPAIR TRANSACTION";
                                            		$menu_arr[] = "create VEHICLE MEETERS";
                                            		$menu_arr[] = "edit VEHICLE MEETERS";
                                            		$menu_arr[] = "create CLIENTHOLIDAYS";
                                            		$menu_arr[] = "edit CLIENTHOLIDAYS";
                                            		$menu_arr[] = "edit SERVICE LOG REQUESTS";
                                            		$i=401;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab5" class="ng-pristine ng-untouched ng-valid">
                                                    Transaction, Trips & Stocks Operations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                 <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "create INCOME TRANSACTION";
                                            		$menu_arr[] = "manage INCOME TRANSACTION";
                                            		$menu_arr[] = "create EXPENSE TRANSACTION";
                                            		$menu_arr[] = "manage EXPENSE TRANSACTION";
                                            		$menu_arr[] = "create FUEL TRANSACTION";
                                            		$menu_arr[] = "manage FUEL TRANSACTION";
                                            		$menu_arr[] = "create REPAIR TRANSACTION";
                                            		$menu_arr[] = "manage REPAIR TRANSACTION";
                                            		$menu_arr[] = "create BILL PAYMENTS";
                                            		$menu_arr[] = "manage BILL PAYMENTS";
                                            		
                                            		$menu_arr[] = "create DAILY TRIP";
                                            		$menu_arr[] = "edit DAILY TRIP";
                                            		$menu_arr[] = "manage DAILY TRIP";
                                            		$menu_arr[] = "create LOCAL TRIP";
                                            		$menu_arr[] = "edit LOCAL TRIP";
                                            		$menu_arr[] = "delete LOCAL TRIP";
                                            		$menu_arr[] = "cancel LOCAL TRIP";
                                            		$menu_arr[] = "print LOCAL TRIP";
                                            		$menu_arr[] = "manage LOCAL TRIP";
                                            		
                                            		$menu_arr[] = "create INVENTORY LOOKUP VALUES";
                                            		$menu_arr[] = "manage INVENTORY LOOKUP VALUES";
                                            		$menu_arr[] = "create MANUFACTURES";
                                            		$menu_arr[] = "manage MANUFACTURES";
                                            		$menu_arr[] = "create ITEM CATEGORIES";
                                            		$menu_arr[] = "manage ITEM CATEGORIES";
                                            		$menu_arr[] = "create ITEM TYPES";
                                            		$menu_arr[] = "manage ITEM TYPES";
                                            		$menu_arr[] = "create ITEMS";
                                            		$menu_arr[] = "manage ITEMS";
                                            		$menu_arr[] = "create PURCHASE ORDERS";
                                            		$menu_arr[] = "manage PURCHASE ORDERS";
                                            		$menu_arr[] = "create USE STOCK ITEMS";
                                            		$menu_arr[] = "manage USE STOCK ITEMS";
                                            		$menu_arr[] = "create ESTIMATE PURCHASE ORDER";
                                            		$menu_arr[] = "manage ESTIMATE PURCHASE ORDER";
                                            		
                                            		$menu_arr[] = "add ATTENDENCE";
                                            		$menu_arr[] = "manage ATTENDENCE";
                                            		$menu_arr[] = "pay DRIVERS/HELPERS SALARY";
                                            		$menu_arr[] = "pay OFFICE EMPLOYEES SALARY ";
                                            		$menu_arr[] = "create SALARY ADVANCES";
                                            		$menu_arr[] = "manage SALARY ADVANCES";
                                            		$menu_arr[] = "create EMPLOYEE LEAVES";
                                            		$menu_arr[] = "manage EMPLOYEE LEAVES";
                                            		$i=301;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){       
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab8" class="ng-pristine ng-untouched ng-valid">
                                                    Work Flow Operations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                 <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "workflow of FUEL TRANSACTION";
                                            		$menu_arr[] = "workflow of REPAIR TRANSACTION";
                                            		$menu_arr[] = "workflow of PURCHASE TRANSACTION";
                                            		$menu_arr[] = "workflow of INCHARGE TRANSACTION";
                                            		$menu_arr[] = "workflow of EXPENSE TRANSACTION";
                                            		$menu_arr[] = "workflow of EMPLOYEE LEAVES";
                                            		$menu_arr[] = "workflow of AUTHORIZATION";
                                            		
                                            		$i=501;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div ng-repeat="categry in categoty" class="ng-scope">
                                    <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; ">
                                        <div style="margin-top: 10px; background-color:#666666; color: #ffffff; margin-bottom: 0px; margin-left: -1px; margin-right: -1px;">
                                            <div class="checkbox" style=" margin-bottom: 0px;padding-left: 40px;">
                                                <label class="ng-binding">
                                                    <input type="checkbox" id="tab9" class="ng-pristine ng-untouched ng-valid">
                                                    Reports Operations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 ng-scope" ng-repeat="privlge in privlage">
                                            <div ng-if="categry.id===privlge.id" class="col-sm-12 ng-scope">
                                                 <?php 
                                            		$menu_arr = array();
                                            		$menu_arr[] = "DAILY TRANSACTION";
                                            		$menu_arr[] = "DAILY FINANCE REPORT";
                                            		$menu_arr[] = "DAILY FINANCE DETAILS REPORT";
                                            		$menu_arr[] = "FUEL TRACKING REPORT";
                                            		$menu_arr[] = "CREDIT SUPPLIER REPORT";
                                            		$menu_arr[] = "VEHICLE MILEAGE REPORT";
                                            		$menu_arr[] = "VEHICLE MILEAGE REPORT (FULL)";
                                            		$menu_arr[] = "VEHICLE PERFORMANCE REPORT";
                                            		$menu_arr[] = "CLIENT VEHICLE TRIPS REPORT";
                                            		$menu_arr[] = "VEHICLE VERIFICATION REPORT";
                                            		$menu_arr[] = "HOLIDAYS RUNNING REPORT";
                                            		$menu_arr[] = "EXTRA KMS REPORT";
                                            		$menu_arr[] = "SALARY ADVANCES";
                                            		$menu_arr[] = "SALARY REPORT";
                                            		$menu_arr[] = "ESTIMATED SALARY REPORT";
                                            		$menu_arr[] = "ATTENDENCE";
                                            		$menu_arr[] = "ATTENDENCE DETAILED";
                                            		$menu_arr[] = "INCHARGE TRANSACTIONS";
                                            		$menu_arr[] = "LOANS REPORT";
                                            		$menu_arr[] = "BANK POSITION REPORT";
                                            		$menu_arr[] = "LOGIN DETAILS";
                                            		$menu_arr[] = "EMPLOYEE MAIN LOGIN DETAILS";
                                            		$menu_arr[] = "STOCK PURCHASE REPORT";
                                            		$menu_arr[] = "VEHICLE-STOCK HISTORY REPORT";
                                            		$menu_arr[] = "INVENTORY REPORT";
                                            		$menu_arr[] = "OFFICE INVENTORY REPORT";
                                            		$menu_arr[] = "REPAIR STOCK REPORT";
                                            		$menu_arr[] = "SERVICE LOG DETAILS";
                                            		$menu_arr[] = "CLIENT HOLIDAYS WORKING REPORT";
                                            		
                                            		$i=601;
                                            		foreach ($menu_arr as $menu_item){
                                            			$chk = "";
                                            			if(in_array($i, $jobs_arr)){
                                            				$chk = " checked ";
                                            			}
                                            	?>
                                                <div class="col-sm-6 ng-scope" ng-repeat="subPrivlge in privlage[$index].sub_priv">
                                                    <div class="checkbox" style="margin-bottom: 0px; margin-top: 2px;">
                                                        <label class="ng-binding">
                                                        	<input type="checkbox" name="ids[]" value="{{$i}}"  id="{{$i}}" onclick="changeVal(this.value)"  {{$chk}} class="ng-pristine ng-untouched ng-valid">
                                                            {{$menu_item}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php $i++; }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>

                                <div >
									<div class="col-md-offset-4 col-md-8" style="margin-top: 2%; margin-bottom: 1%">
										<button id="reset" class="btn primary" type="submit" id="submit">
											<i class="ace-icon fa fa-check bigger-110"></i>
											SUBMIT
										</button>
										<!--  <input type="submit" class="btn btn-info" type="button" value="SUBMIT"> -->
										&nbsp; &nbsp; &nbsp;
										<button id="reset" class="btn" type="reset">
											<i class="ace-icon fa fa-undo bigger-110"></i>
											RESET
										</button>
									</div>
								</div>
                            </form>

                        </div> <!-- close row -->

                    </div>  <!-- panel body -->
                </div> <!--Panel -->
            </div>
		
		
		<!-- PAGE CONTENT ENDS -->
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/bootbox.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			function changeVal(val){
				ck = $("#"+val).prop("checked");
			    if(!ck){
				   if(val<10){
					   $("#tab1").prop("checked",false);
				   }
				   else if(val<150){
					   $("#tab2").prop("checked",false);
				   }
				   else if(val<200){
					   $("#tab3").prop("checked",false);
				   }
				   else if(val<250){
					   $("#tab4").prop("checked",false);
				   }
				   else if(val<350){
					   $("#tab5").prop("checked",false);
				   }
				}
			}
			
			jQuery(function($){
			   $("#tab1").on("click",function(){
				   val = $("#tab1").prop("checked");
				   if(val){
					 for(i=1;i<10; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=1;i<10; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab2").on("click",function(){
				   val = $("#tab2").prop("checked");
				   if(val){
					 for(i=101;i<150; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=101;i<150; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab3").on("click",function(){
				   val = $("#tab3").prop("checked");
				   if(val){
					 for(i=151;i<200; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=151;i<200; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab4").on("click",function(){
				   val = $("#tab4").prop("checked");
				   if(val){
					 for(i=201;i<300; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=201;i<300; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab5").on("click",function(){
				   val = $("#tab5").prop("checked");
				   if(val){
					 for(i=301;i<350; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=301;i<350; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab6").on("click",function(){
				   val = $("#tab6").prop("checked");
				   if(val){
					 for(i=451;i<500; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=451;i<500; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab7").on("click",function(){
				   val = $("#tab7").prop("checked");
				   if(val){
					 for(i=401;i<450; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=401;i<450; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab8").on("click",function(){
				   val = $("#tab8").prop("checked");
				   if(val){
					 for(i=501;i<550; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=501;i<550; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});
				
			   $("#tab9").on("click",function(){
				   val = $("#tab9").prop("checked");
				   if(val){
					 for(i=601;i<650; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=601;i<650; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   <?php 
					if(Session::has('message')){
						echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
					}
				?>
			
			});
		</script>
	@stop