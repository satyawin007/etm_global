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
						($form_info['action']=="addcontract") ||
						($form_info['action']=="editservicelog")
					  ){ ?>
					@include("contracts.tablerowform",$form_info)
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
					$con_vehs = \ServiceLog::where("id","=",$values["id"])->get();
					$con_vehs_str = "[";
					$con_vehs_text_str = "[";
					foreach ($con_vehs as $veh){
						//$con_vehs_text_str = $con_vehs_text_str."{ 'vehicle':'".$vehicles_arr[$veh->contractVehicleId]."', 'driver1':'".$drivers_arr[$veh->driver1Id]."', 'driver2':'".$drivers_arr[$veh->driver2Id]."', 'helper':'".$helpers_arr[$veh->helperId]."', 'status':'".$veh->status."'},";
						$con_vehs_text_str = $con_vehs_text_str."{ 'vehicle':'".$vehicles_arr[$veh->contractVehicleId]."', 'driver1':'".$veh->driver2Id."', 'driver2':'".$veh->helperId."', 'helper':'".$helpers_arr[$veh->helperId]."', 'status':'".$veh->status."'},";
						$con_vehs_str = $con_vehs_str."{ 'vehicle':'".$veh->vehicleId."', 'driver1':'".$veh->driver1Id."', 'driver2':'".$veh->driver2Id."', 'helper':'".$veh->helperId."', 'status':'".$veh->status."', 'id':'".$veh->id."'},";
					}
					$con_vehs_str = $con_vehs_str."]";
					$con_vehs_text_str = $con_vehs_text_str."]";
				?>
				$("#updaterowbtn").hide();
				$scope.vehicles = <?php echo $con_vehs_str; ?>;
				$scope.vehicles_text = <?php echo $con_vehs_text_str; ?>;
				$scope.addRow = function(){
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						if(entry != "vehicle"){
							$scope[entry] = text;
						}
					});	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" ||  typeof $scope.servicedate === "undefined" ||$scope.driver1 === "" || $scope.vehicle === "" || $scope.servicedate === "") {
						return;
					}
					$scope.distance = $("#distance").val();	

					text_arr = [];
					veh_arr = {};
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").text();
						val = $("#"+entry+" option:selected").val();
						veh_arr[entry] = val;
						$("#"+entry).find('option:selected').removeAttr("selected");
						if(val==""){
							text="";
						}
						text_arr[entry] = text;
						$scope[entry] = '';
					});
					$scope.vars.forEach(function(entry) {
						text_arr[entry] = $scope[entry];
						veh_arr[entry] = $scope[entry];
						$scope[entry] = '';
					});

					$scope.vehicles_text.unshift(text_arr);
					$scope.vehicles.unshift(veh_arr);
					$('.chosen-select').trigger("chosen:updated");
				};

				$scope.editRow = function(vehicle){	
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					var comArr1 = eval( $scope.vehicles );
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
					$scope.vars.forEach(function(entry) {
						$scope[entry]=comArr1[i][entry];
					});	
					$scope.ids.forEach(function(entry) {
						$("#"+entry+" option").each(function() {   this.selected =(this.text == comArr[i][entry])});
						$("#"+entry).find('option:selected').attr("selected", "selected"); 
						$scope[entry]=comArr1[i][entry];
					});	
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
						if( comArr[i].vehicle === $scope.vehicle ) {
							index = i;
							$scope.ids.forEach(function(entry) {
								text = $("#"+entry+" option:selected").text();
								$("#"+entry).find('option:selected').removeAttr("selected");
								if(entry != "vehicle"){
									if(text != ""){
										$scope.vehicles_text[index][entry] = text;
									}
									$scope.vehicles[index][entry] = $scope[entry];
									$scope[entry] = '';
								}
							});
							$scope.vars.forEach(function(entry) {
								$scope.vehicles_text[index][entry] = $scope[entry];
								$scope.vehicles[index][entry] = $scope[entry];
								$scope[entry] = '';
							});
							break;
						}
					}
					if( index === -1 ) {
						alert( "Vehicle can not be updated / Something gone wrong" );
						return;
					}
					alert("updated successfully");
					$('.chosen-select').trigger("chosen:updated");
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
					$('#jsondata').val(JSON.stringify($scope.vehicles));
					$.ajax({
                        url: "{{$form_info['name']}}",
                        type: "post",
                        data: $("#{{$form_info['name']}}").serialize(),
                        success: function(response) {
                        	response = jQuery.parseJSON(response);	
                            if(response.status=="success"){
                            	bootbox.alert(response.message, function(result) {});
                            	resetForm("{{$form_info['name']}}");
                            	$scope.vehicles= [];	
            					$scope.vehicles_text = [];		
                            }
                            if(response.status=="fail"){
                            	bootbox.alert(response.message, function(result) {});
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
				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}
				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}
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