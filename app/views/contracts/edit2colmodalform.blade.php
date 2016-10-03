@extends('masters.modalmaster')
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
			th, td {
				white-space: normal;
			}
			.chosen-container{
			  width: 100% !important;
			}
			.form-control {
			    display: block;
			    width: 100%;
			    height: 34px;
		    }
		    .driversarea{
		     	position: absolute; 
  				left: -999em;
		    }
		</style>
	@stop
	
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
	@stop
	
	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			CONTRACTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<div class="col-xs-offset-0 col-xs-12">
				<?php $form_info = $values["form_info"]; ?>
				<?php $jobs = Session::get("jobs");?>
				<?php if(($form_info['action']=="addstate" && in_array(206, $jobs)) or 
						($form_info['action']=="addcontract" && in_array(401, $jobs)) ||
						($form_info['action']=="editcontract" && in_array(402, $jobs)) or
						($form_info['action']=="editestimatepurchaseorder" && in_array(402, $jobs))
					  ){ ?>
					@include("contracts.edittablerowform",$form_info)
				<?php } ?>
			</div>
		</div>
				
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/angular-1.5.4/angular.min.js"></script>
		<script src="../assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/js/dataTables/jquery.dataTables.bootstrap.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/dataTables.buttons.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.flash.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.html5.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.print.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.colVis.js"></script>
		<script src="../assets/js/dataTables/extensions/select/dataTables.select.js"></script>
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/bootbox.js"></script>
		<script src="../assets/js/chosen.jquery.js"></script>
		<script src="../assets/js/jquery.maskedinput.js"></script>
	@stop
	
	@section('inline_js')
	
		<!-- inline scripts related to angular JS-->
		<script>
			submit_data = "false";
			$(".removerowbtn").hide();
			var app = angular.module('myApp', []);
			var editid = 0;
			app.controller('myCtrl', function($scope, $http) {
				<?php 
					$vehicles =  \Vehicle::all();
					$vehicles_arr = array();
					foreach ($vehicles as $vehicle){
						$vehicles_arr[$vehicle['id']] = $vehicle['veh_reg'];
					}
					$drivers =  \Employee::where("roleId","=",19)->get();
					$drivers_arr = array();
					foreach ($drivers as $driver){
						$drivers_arr[$driver['id']] = $driver['fullName']." (".$driver->empCode.")";
					}
					$helpers =  \Employee::where("roleId","=",20)->get();
					$helpers_arr = array();
					foreach ($helpers as $helper){
						$helpers_arr[$helper['id']] = $helper['fullName']." (".$helper->empCode.")";
					}
					
					$parentId = \LookupTypeValues::where("name", "=", "VEHICLE TYPE")->get();
					$vehtypes = array();
					if(count($parentId)>0){
						$parentId = $parentId[0];
						$parentId = $parentId->id;
						$vehtypes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
							
					}
					$vehtypes_arr = array();
					foreach ($vehtypes as $vehtype){
						$vehtypes_arr[$vehtype->id] = $vehtype->name;
					}
				
					$con_vehs = \ContractVehicle::where("contractId","=",$values["id"])->get();
					$con_vehs_str = "[";
					$con_vehs_text_str = "[";
					foreach ($con_vehs as $veh){
						$drv1 = "";
						$drv2 = "";
						$drv3 = "";
						$drv4 = "";
						$drv5 = "";
						$helper = "";
						if($veh->driver1Id != 0){
							if(isset($drivers_arr[$veh->driver1Id])){
								$drv1 = $drivers_arr[$veh->driver1Id];
							}
						}
						if($veh->driver2Id != 0){
							if(isset($drivers_arr[$veh->driver2Id])){
								$drv2 = $drivers_arr[$veh->driver2Id];
							}
						}
						if($veh->driver3Id != 0){
							if(isset($drivers_arr[$veh->driver3Id])){
								$drv3 = $drivers_arr[$veh->driver3Id];
							}
						}
						if($veh->driver4Id != 0){
							if(isset($drivers_arr[$veh->driver4Id])){
								$drv4 = $drivers_arr[$veh->driver4Id];
							}
						}
						if($veh->driver5Id != 0){
							if(isset($drivers_arr[$veh->driver5Id])){
								$drv5 = $drivers_arr[$veh->driver5Id];
							}
						}
						if($veh->helperId != 0){
							if(isset($helpers_arr[$veh->helperId])){
								$helper = $helpers_arr[$veh->helperId];
							}
						}
						if($veh->inActiveDate != "0000-00-00" && $veh->inActiveDate != "" && $veh->inActiveDate != "1970-01-01"){
							$veh->inActiveDate = date("d-m-Y",strtotime($veh->inActiveDate));
						}
						else{
							$veh->inActiveDate = "";
						}
						$veh_type = "";
						if(isset($vehtypes_arr[$veh->vehicleTypeId])){
							$veh_type = $vehtypes_arr[$veh->vehicleTypeId];
						}
						$veh_reg1 = "";
						if(isset($vehicles_arr[$veh->vehicleId])){
							$veh_reg1 = $vehicles_arr[$veh->vehicleId];
						}
						$con_vehs_text_str = $con_vehs_text_str."{ 'vehicle':'".$veh_reg1
																."', 'vehicletype':'".$veh_type
																."', 'driver1':'".$drv1
																."', 'driver2':'".$drv2
																."', 'driver3':'".$drv3
																."', 'driver4':'".$drv4
																."', 'driver5':'".$drv5
																."', 'helper':'".$helper
																."', 'drv1dt':'".date("d-m-Y",strtotime($veh->driver1startDate))
																."', 'drv2dt':'".date("d-m-Y",strtotime($veh->driver2startDate))
																."', 'drv3dt':'".date("d-m-Y",strtotime($veh->driver3startDate))
																."', 'drv4dt':'".date("d-m-Y",strtotime($veh->driver4startDate))
																."', 'drv5dt':'".date("d-m-Y",strtotime($veh->driver5startDate))
																."', 'helperdt':'".date("d-m-Y",strtotime($veh->helperstartDate))
																."', 'drv1edt':'".date("d-m-Y",strtotime($veh->driver1endDate))
																."', 'drv2edt':'".date("d-m-Y",strtotime($veh->driver2endDate))
																."', 'drv3edt':'".date("d-m-Y",strtotime($veh->driver3endDate))
																."', 'drv4edt':'".date("d-m-Y",strtotime($veh->driver4endDate))
																."', 'drv5edt':'".date("d-m-Y",strtotime($veh->driver5endDate))
																."', 'helperedt':'".date("d-m-Y",strtotime($veh->helperendDate))
																."', 'date':'".date("d-m-Y",strtotime($veh->inActiveDate))
																."', 'floorrate':'".$veh->floorRate
																."', 'routes':'', 'remarks':'".$veh->remarks
																."', 'startdt':'".date("d-m-Y",strtotime($veh->vehicleStartDate))
																."', 'status':'".$veh->status
																."', 'id':'".$veh->id."'},";
						
						$con_vehs_str = $con_vehs_str."{ 'vehicle':'".$veh->vehicleId
																."', 'vehicletype':'".$veh->vehicleTypeId
																."', 'driver1':'".$veh->driver1Id
																."', 'driver2':'".$veh->driver2Id
																."', 'driver3':'".$veh->driver3Id
																."', 'driver4':'".$veh->driver4Id
																."', 'driver5':'".$veh->driver5Id
																."', 'helper':'".$veh->helperId
																."', 'drv1dt':'".date("d-m-Y",strtotime($veh->driver1startDate))
																."', 'drv2dt':'".date("d-m-Y",strtotime($veh->driver2startDate))
																."', 'drv3dt':'".date("d-m-Y",strtotime($veh->driver3startDate))
																."', 'drv4dt':'".date("d-m-Y",strtotime($veh->driver4startDate))
																."', 'drv5dt':'".date("d-m-Y",strtotime($veh->driver5startDate))
																."', 'helperdt':'".date("d-m-Y",strtotime($veh->helperstartDate))
																."', 'drv1edt':'".date("d-m-Y",strtotime($veh->driver1endDate))
																."', 'drv2edt':'".date("d-m-Y",strtotime($veh->driver2endDate))
																."', 'drv3edt':'".date("d-m-Y",strtotime($veh->driver3endDate))
																."', 'drv4edt':'".date("d-m-Y",strtotime($veh->driver4endDate))
																."', 'drv5edt':'".date("d-m-Y",strtotime($veh->driver5endDate))
																."', 'helperedt':'".date("d-m-Y",strtotime($veh->helperendDate))
																
																."', 'floorrate':'".$veh->floorRate
																."', 'routes':'', 'status':'".$veh->status
																."', 'date':'".date("d-m-Y",strtotime($veh->inActiveDate))
																."', 'remarks':'".$veh->remarks
																."', 'startdt':'".date("d-m-Y",strtotime($veh->vehicleStartDate))
																."', 'id':'".$veh->id."'},";
					}
					$con_vehs_str = $con_vehs_str."]";
					$con_vehs_text_str = $con_vehs_text_str."]";
				?>
				$("#updaterowbtn").hide();
				$scope.vehicles = <?php echo $con_vehs_str; ?>;
				$scope.ids = ['vehicle','vehicletype','driver1','driver2','driver3','driver4','driver5','helper','status','routes'];
				$scope.vehicles_text = <?php echo $con_vehs_text_str; ?>;
				$scope.addRow = function(){
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" || $scope.driver1 === "" || $scope.vehicle === "" || 
							$scope.driver1 == $scope.driver2 || $scope.driver1 == $scope.driver3 || $scope.driver1 == $scope.driver4 || $scope.driver1 == $scope.driver5
						    /*$scope.driver2 == $scope.driver1 || $scope.driver2 == $scope.driver3 || $scope.driver2 == $scope.driver4 || $scope.driver2 == $scope.driver5 || 
					  	    $scope.driver3 == $scope.driver1 || $scope.driver3 == $scope.driver2 || $scope.driver3 == $scope.driver4 || $scope.driver3 == $scope.driver5 || 
					  	    $scope.driver4 == $scope.driver1 || $scope.driver4 == $scope.driver3 || $scope.driver4 == $scope.driver2 || $scope.driver4 == $scope.driver5 || 
					  	    $scope.driver5 == $scope.driver1 || $scope.driver5 == $scope.driver3 || $scope.driver5 == $scope.driver2 || $scope.driver5 == $scope.driver4*/
					  ) 
					{
						alert("Duplicate or Wrong Value");
						return;
					}	
					$scope.vehicles.push(
								{ 'vehicle':$scope.vehicle, 
								  'vehicletype':$scope.vehicletype, 
								  'driver1': $scope.driver1, 
								  'driver2':$scope.driver2, 
								  'driver3':$scope.driver3, 
								  'driver4':$scope.driver4, 
								  'driver5':$scope.driver5, 
								  'helper':$scope.helper, 
								  'drv1dt': $scope.drv1dt,
								  'drv2dt': $scope.drv2dt,
								  'drv3dt': $scope.drv3dt,
								  'drv4dt': $scope.drv4dt,
								  'drv5dt': $scope.drv5dt,
								  'helperdt': $scope.helperdt,
								  'drv1edt': $scope.drv1edt,
								  'drv2edt': $scope.drv2edt,
								  'drv3edt': $scope.drv3edt,
								  'drv4edt': $scope.drv4edt,
								  'drv5edt': $scope.drv5edt,
								  'helperedt': $scope.helperedt,
								  'date':$scope.date, 
								  'startdt':$scope.startdt,   
								  'floorrate':$scope.floorrate,  
								  'routes':$scope.routes, 
								  'status':'ACTIVE', 
								  'id':'-1' 
								});
					$scope.vehicle='';
					$scope.vehicletype='';
					$scope.driver1='';
					$scope.driver2='';
					$scope.driver3='';
					$scope.driver4='';
					$scope.driver5='';
					$scope.helper='';
					$scope.drv1dt='';
					$scope.drv2dt='';
					$scope.drv3dt='';
					$scope.drv4dt='';
					$scope.drv5dt='';
					$scope.helperdt='';
					$scope.drv1edt='';
					$scope.drv2edt='';
					$scope.drv3edt='';
					$scope.drv4edt='';
					$scope.drv5edt='';
					$scope.helperedt='';
					$scope.routes='';
					$scope.floorrate='';
					$scope.status='';
					$scope.date='';
					$scope.startdt='';
					$scope.remarks='';
					$scope.id='';

					text_arr = [];
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").text();
						$("#"+entry).find('option:selected').removeAttr("selected");
						text_arr[entry] = text;
					});
					text_arr['date']=$("#date").val();
					text_arr['startdt']=$("#startdt").val();
					text_arr['drv1dt']=$("#drv1dt").val();
					text_arr['drv2dt']=$("#drv2dt").val();
					text_arr['drv3dt']=$("#drv3dt").val();
					text_arr['drv4dt']=$("#drv4dt").val();
					text_arr['drv5dt']=$("#drv5dt").val();
					text_arr['helperdt']=$("#helperdt").val();
					text_arr['drv1edt']=$("#drv1edt").val();
					text_arr['drv2edt']=$("#drv2edt").val();
					text_arr['drv3edt']=$("#drv3edt").val();
					text_arr['drv4edt']=$("#drv4edt").val();
					text_arr['drv5edt']=$("#drv5edt").val();
					text_arr['helperedt']=$("#helperedt").val();
					text_arr['floorrate']=$("#floorrate").val();
					text_arr['remarks']=$("#remarks").val();
					$scope.vehicles_text.push(text_arr);
					$('.chosen-select').trigger("chosen:updated");
				};

				$scope.editRow = function(id){
					$("#addrowbtn").hide();
					$("#updaterowbtn").show();
					tempdata = [];
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					var comArr1 = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr1[i].id === id ) {
							index = i;
							editid = comArr1[i].id;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Something gone wrong" );
						return;
					}
					$scope.ids.forEach(function(entry) {
						$("#"+entry+" option").each(function() { if(this.selected==false) this.selected =(this.text == comArr[i][entry]);});
						$("#"+entry).find('option:selected').attr("selected", "selected"); 
						$scope[entry]=comArr1[i][entry];
					});	
					$scope['date']=comArr1[i]['date'];
					$scope['startdt']=comArr1[i]['startdt'];
					$scope['drv1dt']=comArr1[i]['drv1dt'];
					$scope['drv2dt']=comArr1[i]['drv2dt'];
					$scope['drv3dt']=comArr1[i]['drv3dt'];
					$scope['drv4dt']=comArr1[i]['drv4dt'];
					$scope['drv5dt']=comArr1[i]['drv5dt'];
					$scope['helperdt']=comArr1[i]['helperdt'];
					$scope['drv1edt']=comArr1[i]['drv1edt'];
					$scope['drv2edt']=comArr1[i]['drv2edt'];
					$scope['drv3edt']=comArr1[i]['drv3edt'];
					$scope['drv4edt']=comArr1[i]['drv4edt'];
					$scope['drv5edt']=comArr1[i]['drv5edt'];
					$scope['helperedt']=comArr1[i]['helperedt'];
					$scope['floorrate']=comArr1[i]['floorrate'];
					$scope['remarks']=comArr1[i]['remarks'];
					$('.chosen-select').trigger("chosen:updated");	
				};

				$scope.updateRow = function(){	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" || $scope.driver1 === "" || $scope.vehicle === "") {
						return;
					}
					tempdata = [];
					var index = -1;		
					var comArr = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].id == editid && comArr[i].vehicle==$scope.vehicle) {
							index = i;
							//alert($scope.helper);
							$scope.vehicles[i]['driver1']=$scope.driver1;
							$scope.vehicles[i]['vehicletype']=$scope.vehicletype;
							$scope.vehicles[i]['driver2']=$scope.driver2;
							$scope.vehicles[i]['driver3']=$scope.driver3;
							$scope.vehicles[i]['driver4']=$scope.driver4;
							$scope.vehicles[i]['driver5']=$scope.driver5;
							$scope.vehicles[i]['helper']=$scope.helper;
							$scope.vehicles[i]['drv1dt']=$scope.drv1dt;
							$scope.vehicles[i]['drv2dt']=$scope.drv2dt;
							$scope.vehicles[i]['drv3dt']=$scope.drv3dt;
							$scope.vehicles[i]['drv4dt']=$scope.drv4dt;
							$scope.vehicles[i]['drv5dt']=$scope.drv5dt;
							$scope.vehicles[i]['helperdt']=$scope.helperdt;
							$scope.vehicles[i]['drv1edt']=$scope.drv1edt;
							$scope.vehicles[i]['drv2edt']=$scope.drv2edt;
							$scope.vehicles[i]['drv3edt']=$scope.drv3edt;
							$scope.vehicles[i]['drv4edt']=$scope.drv4edt;
							$scope.vehicles[i]['drv5edt']=$scope.drv5edt;
							$scope.vehicles[i]['helperedt']=$scope.helperedt;
							$scope.vehicles[i]['status']=$scope.status;
							$scope.vehicles[i]['date']=$scope.date;
							$scope.vehicles[i]['startdt']=$scope.startdt;
							$scope.vehicles[i]['remarks']=$scope.remarks;
							$scope.vehicles[i]['floorrate']=$scope.floorrate;
							$scope.vehicles[i]['routes']=$scope.routes;

							$scope.ids.forEach(function(entry) {
								text = $("#"+entry+" option:selected").text();
								if(entry != "vehicle"){
									$scope.vehicles_text[index][entry] = text;
								}
								$("#"+entry).find('option:selected').removeAttr("selected");
							});
							$scope.vehicles_text[i]['date']=$scope.date;
							$scope.vehicles_text[i]['startdt']=$scope.startdt;
							$scope.vehicles_text[i]['drv1dt']=$scope.drv1dt;
							$scope.vehicles_text[i]['drv2dt']=$scope.drv2dt;
							$scope.vehicles_text[i]['drv3dt']=$scope.drv3dt;
							$scope.vehicles_text[i]['drv4dt']=$scope.drv4dt;
							$scope.vehicles_text[i]['drv5dt']=$scope.drv5dt;
							$scope.vehicles_text[i]['helperdt']=$scope.helperdt;
							$scope.vehicles_text[i]['drv1edt']=$scope.drv1edt;
							$scope.vehicles_text[i]['drv2edt']=$scope.drv2edt;
							$scope.vehicles_text[i]['drv3edt']=$scope.drv3edt;
							$scope.vehicles_text[i]['drv4edt']=$scope.drv4edt;
							$scope.vehicles_text[i]['drv5edt']=$scope.drv5edt;
							$scope.vehicles_text[i]['helperedt']=$scope.helperedt;
							$scope.vehicles_text[i]['remarks']=$scope.remarks;
							$scope.vehicles_text[i]['floorrate']=$scope.floorrate;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Vehicle can not be updated / Something gone wrong" );
						return;
					}
					$scope.vehicle='';
					$scope.driver1='';
					$scope.vehicletype='';
					$scope.driver2='';
					$scope.driver3='';
					$scope.driver4='';
					$scope.driver5='';
					$scope.helper='';
					$scope.drv1dt='';
					$scope.drv2dt='';
					$scope.drv3dt='';
					$scope.drv4dt='';
					$scope.drv5dt='';
					$scope.helperdt='';
					$scope.drv1edt='';
					$scope.drv2edt='';
					$scope.drv3edt='';
					$scope.drv4edt='';
					$scope.drv5edt='';
					$scope.helperedt='';
					$scope.status='';
					$scope.date='';
					$scope.startdt='';
					$scope.remarks='';
					$scope.floorrate='';
					$scope.routes='';
					alert("updated successfully");
					$('.chosen-select').trigger("chosen:updated");
					$("#addrowbtn").show();
					$("#updaterowbtn").hide();
	
				};
				
				$scope.removeRow = function(vehicle){	
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === vehicle ) {
							index = i;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Something gone wrong" );
						return;
					}
					$scope.vehicles.splice( index, 1 );	
					$scope.vehicles_text.splice( index, 1 );		
				};

				$scope.postData = function() {
					if(submit_data=="false"){
						return;
					}
					$('#contractvehicles').val(JSON.stringify($scope.vehicles));
					$.ajax({
                        url: "{{$form_info['name']}}",
                        type: "post",
                        data: $("#{{$form_info['name']}}").serialize(),
                        success: function(response) {
                        	response = jQuery.parseJSON(response);	
                            if(response.status=="success"){
                            	bootbox.confirm(response.message, function(result) {});
                            	//resetForm("{{$form_info['name']}}");
                            	//$scope.vehicles= [];	
            					//$scope.vehicles_text = [];		
                            }
                            if(response.status=="fail"){
                            	bootbox.confirm(response.message, function(result) {});
                            }
                        }
                    });
				};

				function resetForm(formid)
			    { 
		            form = $('#'+formid);
		            element = ['input','select','textarea'];
		            for(i=0; i<element.length; i++) 
		            {
	                    $.each( form.find(element[i]), function(){  
                            switch($(this).attr('class')) {
                              case 'form-control chosen-select':
                              	$(this).find('option:first-child').attr("selected", "selected"); 
                                break;
                            }
                            switch($(this).attr('type')) {
                            case 'text':
                            case 'select-one':
                            case 'textarea':
                            case 'hidden':
                            case 'file':
                            	$(this).val('');
                              break;
                            case 'checkbox':
                            case 'radio':
                            	$(this).attr('checked',false);
                              break;
                           
                          }
	                    });
		            }
		            $('.chosen-select').trigger("chosen:updated");	
			    }
			});
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
				
				var statename = $("#statename").val();
				if(statename != undefined && statename ==""){
					alert("Please select statename");
					return false;
				}

				var districtname = $("#districtname").val();
				if(districtname != undefined && districtname ==""){
					alert("Please select districtname");
					return false;
				}
				var clientname = $("#clientname").val();
				if(clientname != undefined && clientname ==""){
					alert("Please select clientname");
					return false;
				}
				var depot = $("#depot").val();
				if(depot != undefined && depot ==""){
					alert("Please select depot");
					return false;
				}
				var route = $("#route").val();
				if(route != undefined && route ==""){
					alert("Please select route");
					return false;
				}
				/*var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}
				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}*/
				var cityname = $("#cityname").val();
				if(cityname != undefined && cityname ==""){
					alert("Please select cityname");
					return false;
				}
				var depotname = $("#depotname").val();
				if(depotname != undefined && depotname ==""){
					alert("Please select depotname");
					return false;
				}
				submit_data="true";
				return false;
				
				//$("#{{$form_info['name']}}").submit();
			});

			function changeState(val){
				$.ajax({
			      url: "getcitiesbystateid?id="+val,
			      success: function(data) {
			    	  $("#cityname").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			}

			function changeCity(val){
				$.ajax({
			      url: "getdepotsbycityid?id="+val,
			      success: function(data) {
			    	  $("#depot").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			}

			$('#otherdrivers').on('change', function() { 
			    if (this.checked) {
			    	$(".driversarea").css('position','relative');
			    	$(".driversarea").css('left','0em');
			    	
			    }
			    else{
			    	 $(".driversarea").css('position','absolute');
			    	 $(".driversarea").css('left','-999em');
			    }
			});

			function verifyActiveStatus(val){
				id = $("#vehicle").val();
				if(id==""){
					alert("select vehicle");
					return;
				}
				if(val=="ACTIVE"){
					id = id.substr(9,id.length);
					id = id.substr(0,id.indexOf(" ?"));
					$.ajax({
				      url: "getvehicleactivestatus?id="+id,
				      success: function(data) {
					      if(data=="Yes"){
						      alert("You can not change Status to ACTIVE, as there is an ACTIVE Contract-Vehicle");
						      $("#updaterowbtn").hide();
					      }
				      },
				      type: 'GET'
				   });
				}
			}

			

			<?php 
				if(Session::has('message')){
					echo "bootbox.confirm('".Session::pull('message')."', function(result) {});";
				}
			?>

			if(!ace.vars['touch']) {
				$('.chosen-select').chosen({allow_single_deselect:true}); 
				//resize the chosen on window resize
		
				$(window)
				.off('resize.chosen')
				.on('resize.chosen', function() {
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				}).trigger('resize.chosen');
				//resize chosen on sidebar collapse/expand
				$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
					if(event_name != 'sidebar_collapsed') return;
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				});
		
		
				$('#chosen-multiple-style .btn').on('click', function(e){
					var target = $(this).find('input[type=radio]');
					var which = parseInt(target.val());
					if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
					 else $('#form-field-select-4').removeClass('tag-input-style');
				});
			}

			$('.number').keydown(function(e) {
				this.value = this.value.replace(/[^0-9.]/g, ''); 
				this.value = this.value.replace(/(\..*)\./g, '$1');
			});
		
			//datepicker plugin
			//link
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			//show datepicker when clicking on the icon
			.next().on(ace.click_event, function(){
				$(this).prev().focus();
			});

			//or change it into a date range picker
			$('.input-daterange').datepicker({autoclose:true,todayHighlight: true});

			$('.input-mask-phone').mask('(999) 999-9999');
			

			
		</script>
	@stop