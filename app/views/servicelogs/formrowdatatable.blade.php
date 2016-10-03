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
			th, td {
				white-space: normal;
			}
			.chosen-container{
			  width: 100% !important;
			}
		</style>
	@stop
	
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
	@stop
	
	@section('bredcum')	
		<small>
			CONTRACTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<div id="showerrormessage" style="text-align: center; font-size: 16px; font-weight: bold"></div>
			<div class="col-xs-offset-0 col-xs-12" style="max-width:98%;margin-left: 1%;">
				<?php $form_info = $values["form_info"];?>
				<?php $jobs = Session::get("jobs");?>
				<?php if(($form_info['action']=="addstate" && in_array(206, $jobs)) || 
						($form_info['action']=="addservicelog" && in_array(407, $jobs))
					  ){ ?>
					@include("servicelogs.tablerowform",$form_info)
				<?php } ?>
			</div>
		</div>
				
		<div class="row ">
		<div class="col-xs-offset-0 col-xs-12">
			<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">MANAGE {{$values["bredcum"]}}</h3>		
			<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
			<div class="clearfix">
				<div class="pull-left">
					
					<form action="{{$values['form_action']}}" name="paginate" id="paginate">
					<?php 
					if(isset($values['selects'])){
						$selects = $values['selects'];
						foreach($selects as $select){
						?>
						<label>{{ strtoupper($select["name"]) }}</label>
						<select class="form-control-inline" id="{{$select['name']}}" style="height: 33px; padding-top: 0px;" name="{{$select["name"]}}" onChage="paginate(1)">
							<?php 
								foreach($select["options"] as $key => $value){									
									$option =  "<option value='".$key."' ";
									if($key == $values[$select['name']]){
										$option = $option." selected='selected' ";
									}
									$option = $option.">".$value."</option>";
									echo $option;
								}
							?>
						</select> &nbsp; &nbsp;
					<?php }} ?>
					<input type="hidden" name="page" id="page" /> 
					<?php 
					if(isset($values['links'])){
						$links = $values['links'];
						foreach($links as $link){
							echo "<a class='btn btn-white btn-success' href=".$link['url'].">".$link['name']."</a> &nbsp; &nbsp; &nbsp";
						}
					}
					?>
					<?php echo "<input type='hidden' name='action' value='".$values['action_val']."'/>"; ?>					
					</form>
				</div>
				<div class="pull-right tableTools-container"></div>
			</div>
			<div class="table-header" style="margin-top: 10px;">
				Results for "{{$values['bredcum']}}"				 
				<div style="float:right;padding-right: 15px;padding-top: 6px;"><a style="color: white;" href="{{$values['home_url']}}"><i class="ace-icon fa fa-home bigger-200"></i></a> </div>				
			</div>
			<!-- div.table-responsive -->
			<!-- div.dataTables_borderWrap -->
			<div>
				<table id="dynamic-table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<?php 
								$theads = $values['theads'];
								foreach($theads as $thead){
									echo "<th>".strtoupper($thead)."</th>";
								}
							?>
						</tr>
					</thead>
				</table>								
			</div>
		</div>
		</div>

		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal);
		<?php }} ?>
		
		<div id="edit" class="modal" tabindex="-1">
			<div class="modal-dialog" style="width: 90%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="blue bigger">EDIT {{$values['bredcum']}}</h4>
					</div>
	
					<div class="modal-body" id="modal_body">
					
					</div>
	
					<div class="modal-footer">
						<button class="btn btn-sm" data-dismiss="modal">
							<i class="ace-icon fa fa-times"></i>
							Close
						</button>
					</div>
				</div>
			</div>
		</div><!-- PAGE CONTENT ENDS -->
		
		
		<div id="pendingservicelogs" class="modal" tabindex="-1" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" id="dismissmodal" class="close" data-dismiss="modal">x</button>
						<h4 class="blue bigger">PENDING SERVICE LOGS</h4>
					</div>
		
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
							<form name="pendingservicelogsform" id="pendingservicelogsform" class="form-horizontal" action="addservicelogrequest" method="post">	
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1">CLIENT<span style="color:red;">*</span> </label>
									<div class="col-xs-7">
										<input readonly="readonly" type="text" name="pendingclient" id="pendingclient" required="required" class="form-control">
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1">DEPOT/BRANCH<span style="color:red;">*</span> </label>
									<div class="col-xs-7">
										<input readonly="readonly" type="text" name="pendingdepot" id="pendingdepot" required="required"  class="form-control">
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1">VEHICLE<span style="color:red;">*</span> </label>
									<div class="col-xs-7">
										<input readonly="readonly" type="text" name="pendingvehicle"  id="pendingvehicle"  required="required"  class="form-control">
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> PENDING DATES </label>
									<div class="col-xs-7">
										<select class="form-control chosen-select" name="pendingdates[]" id="pendingdates" multiple="multiple">
											<option value="">-- pendingdate --</option>
										</select>
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1">CUSTOME DATE </label>
									<div class="col-xs-7">
										<input type="text" id="customdate" required="required" name="customdate" class="form-control date-picker">
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> COMMENTS </label>
									<div class="col-xs-7">
										<textarea id="pendingcomments" name="pendingcomments" class="form-control"></textarea>
									</div>			
								</div>
								<div class="modal-footer">
									<button class="btn btn-sm" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Cancel
									</button>
					
									<button id="pendingmodalsave" class="btn btn-sm btn-primary">
										<i class="ace-icon fa fa-check"></i>
										Save
									</button>
								</div>
		
								</form>
							</div>
						</div>
					</div>
		
					
				</div>
			</div>
		</div>
		<a  href="#pendingservicelogs" data-toggle="modal" id="pendinglogs" onclick="showPendingServiceLogs()"></a>
		
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
			$("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
			$("#starttime option").find('option:selected').attr("selected", "selected"); 
			$('.chosen-select').trigger("chosen:updated");	
			
			var app = angular.module('myApp', []);
			app.controller('myCtrl', function($scope, $http) {
				$scope.vehicles = [];
				$scope.ids = ['vehicle', 'servicedate', 'substitutevehicle', 'starttime', 'driver1', 'driver2', 'driver3', 'driver4', 'driver5', 'helper', 'penalitiestype'];
				$scope.vars = ['distance','repairkms', 'startreading', 'endreading', 'penalityamount', 'remarks' ];
				$scope.vehicles_text = [];
				exe_recs_text = [];
				$scope.addRow = function(){
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						if(entry != "vehicle"){
							$scope[entry] = text;
						}
					});	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" ||  typeof $scope.servicedate === "undefined" || typeof $scope.endreading === "undefined" || $scope.driver1 === "" || $scope.vehicle === "" || $scope.servicedate === "" || $scope.endreading == "") {
						alert("There are some required fields / Something went wrong");
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
					$("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
					$("#starttime option").find('option:selected').attr("selected", "selected"); 
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
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						text = text.replace("? string:", "");
						text = text.replace(" ?", "");
						if(entry != "vehicle"){
							$scope[entry] = text;
						}
					});	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" ||  typeof $scope.servicedate === "undefined" ||$scope.driver1 === "" || $scope.vehicle === "" || $scope.servicedate === "") {
						return;
					}	
					$scope.distance = $("#distance").val();	
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
					$("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
					$("#starttime option").find('option:selected').attr("selected", "selected"); 
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
            					window.setTimeout(function(){location.reload();}, 2000 );	
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
		            $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
					$("#starttime option").find('option:selected').attr("selected", "selected"); 
		            $('.chosen-select').trigger("chosen:updated");	
			    }
			});
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});
	
			function modalEditServiceLog(vehicleId, serviceDate, startTime, startReading, endReading, distance, repairkms, remarks, status, id){
				$("#vehicle1").val(vehicleId);	
				$("#servicedate1").val(serviceDate);				
				$("#startreading1").val(startReading);
				$("#endreading1").val(endReading);
				$("#repairkms1").val(repairkms);
				$("#remarks1").val(remarks);
				$("#distance1").val(distance);
				$("#starttime1 option").each(function() {this.selected = (this.text == startTime); });
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function showPendingLogs(data){
				bootbox.alert(data);
			}

			function showPendingServiceLogs (){
				$('.chosen-select').trigger("chosen:updated");	
			}		

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
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
				var depotname = $("#depotname").val();
				if(depotname != undefined && depotname ==""){
					alert("Please select depotname");
					return false;
				}
				submit_data="true";
				return false;
			});

			$("#pendingmodalsave").on("click",function(){
				clientname = $("#clientname").val();
				$('form#pendingservicelogsform').append('<input type="hidden" name="clientid" value="'+clientname+'" />');
				depot = $("#depot").val();
				$('form#pendingservicelogsform').append('<input type="hidden" name="depot" value="'+depot+'" />');
				vehicle = $("#vehicle").val();
				$('form#pendingservicelogsform').append('<input type="hidden" name="vehicle" value="'+vehicle+'" />');
				$.ajax({
                    url: $('#pendingservicelogsform').attr('action'),
                    type: "post",
                    data: $("#pendingservicelogsform").serialize(),
                    success: function(response) {
                    	response = jQuery.parseJSON(response);	
                        if(response.status=="success"){
                        	bootbox.alert(response.message, function(result) {});
        					$("#dismissmodal").click();	
                        }
                        if(response.status=="fail"){
                        	bootbox.alert(response.message, function(result) {});
                        }
                    }
                });
				return false;
			});

			function changeDepot(val){
				$.ajax({
			      url: "getdepotsbyclientId?id="+val,
			      success: function(data) {
			    	  $("#depot").html(data);
			    	  $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
					  $("#starttime option").find('option:selected').attr("selected", "selected"); 
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			    });

				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
			}

			function getFormData(val){
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
				$.ajax({
			      url: "getvehiclecontractinfo?clientid="+clientId+"&depotid="+depotId,
			      success: function(data) {
			    	  $("#vehicle").html(data);
			    	  $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
					  $("#starttime option").find('option:selected').attr("selected", "selected"); 
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			   myTable.ajax.url("getservicelogsdatatabledata?name=servicelogs&clientid="+clientId+"&depotid="+depotId).load();
			}

			function getStartReading(val){
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
				vehicleid = $("#vehicle").val();
				servicedate = $("#servicedate").val();
				url = "getstartreading?clientid="+clientId+"&depotid="+depotId+"&date="+val+"&vehicleid="+vehicleid+"&servicedate="+servicedate;
				$.ajax({
			      url: url,
			      success: function(data) {
			    	  data = JSON.parse(data);
			    	  if(data[0] == "0"){
				    	  alert("Please Set start meeter reading for the vehicle");
				    	  location.reload();
			    	  }
			    	  $("#startreading").val(data[0]);
			    	  $("#servicedate").html(data[1]);
			    	  angular.element('#myCtrl').scope().exe_recs_text =  data[2];
			    	  angular.element('#myCtrl').scope().$apply();
			    	  $("#startreading").trigger('input');
			    	  $("#endreading").val("");
					  $("#endreading").trigger('input');
					  $("#distance").val("");
					  $("#distance").trigger('input');
					  $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
					  $("#starttime option").find('option:selected').attr("selected", "selected"); 
					  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			}

			function getStartReadingSubstitute(val){
				//vehicleid = $("#substitutevehicle").val();
				subvehicleid = val;
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
				vehicleid = $("#vehicle").val();
				servicedate = $("#servicedate").val();
				url = "getstartreadingsubstitute?clientid="+clientId+"&depotid="+depotId+"&date="+val+"&subvehicleid="+subvehicleid+"&vehicleid="+vehicleid+"&servicedate="+servicedate;
				$.ajax({
			      url: url,
			      success: function(data) {
			    	  data = JSON.parse(data);
			    	  if(data[0] == "0"){
				    	  alert("Please Set start meeter reading for the vehicle");
				    	  //location.reload();
			    	  }
			    	  $("#startreading").val(data[0]);
			    	  $("#startreading").trigger('input');
			    	  $("#endreading").val("");
					  $("#endreading").trigger('input');
					  $("#distance").val("");
					  $("#distance").trigger('input');
					  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			}

			function getDriverHelper(val){
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
				$.ajax({
			      url: "checkpendingdates?clientid="+clientId+"&depotid="+depotId+"&vehicleid="+val,
			      success: function(data) {
			    	  data = JSON.parse(data);
			    	  if(data.status=="success"){			    	  
				    	  $.ajax({
						      url: "getdriverhelper?clientid="+clientId+"&depotid="+depotId+"&vehicleid="+val,
						      success: function(data) {
							      data = JSON.parse(data);
						    	  $("#driver1").html(data[0]);
						    	  $("#driver2").html(data[1]);
						    	  $("#driver3").html(data[2]);
						    	  $("#driver4").html(data[3]);
						    	  $("#driver5").html(data[4]);
						    	  $("#helper").html(data[5]);
						    	  $("#servicedate").html(data[6]);
						    	  $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
								  $("#starttime option").find('option:selected').attr("selected", "selected"); 
						    	  $('.chosen-select').trigger("chosen:updated");
						      },
						      type: 'GET'
						  });
				    	  $("#showerrormessage").html("");
			    	  }
			    	  else{
			    		  $("#servicedate").html("<option value=''>select service date</option>");
			    		  $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
						  $("#starttime option").find('option:selected').attr("selected", "selected"); 
			    		  $('.chosen-select').trigger("chosen:updated");
			    		  $("#showerrormessage").html(data.message);
			    	  }
			      },
			      type: 'GET'
			   });
			}
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
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

			
			var myTable = null;
			jQuery(function($) {
// 				test_data = [];
// 				test_data['vehicle'] = 'AP23'; 
// 				angular.element('#myCtrl').scope().exe_recs_text =  [test_data];
// 		    	angular.element('#myCtrl').scope().$apply();
				$("#div_substitutevehicle").hide();	
				$("#div_penalitiestype").hide();
				$("#div_penalityamount").hide();
				$("#distance").attr("readonly",true);	
				$("#div_driver1").hide();	
		    	$("#div_driver2").hide();
		    	$("#div_driver3").hide();
		    	$("#div_driver4").hide();
		    	$("#div_driver5").hide();
		    	$("#div_helper").hide();	

				$('#endreading').on('change', function() { 
					endreading = parseInt($('#endreading').val());
					startreading = parseInt($('#startreading').val());
					if(endreading<startreading){
						alert("End Reading must be greater than Start Reading");
						$('#endreading').val("");
						return;
					}
					dist = $('#endreading').val()-$('#startreading').val();
					$("#distance").val(dist);
					$("#distance").trigger('input');
				});

				$('#endreading1').on('change', function() { 
					er = parseInt($('#endreading1').val());
					sr = parseInt($('#startreading1').val());
					if(er<sr){
						alert("End Reading must be greater than Start Reading");
						$('#endreading1').val("");
						return;
					}
					dist = $('#endreading1').val()-$('#startreading1').val();
					$("#distance1").val(dist);
				});

				$('#substitutevehicleckbox').on('change', function() { 
				    if (this.checked) {
				    	$("#div_substitutevehicle").show();	
				    }
				    else{
				    	$("#div_substitutevehicle").hide();	
				    }
				});
				$('#fine').on('change', function() { 
				    // From the other examples
				    if (this.checked) {
				    	$("#div_penalityamount").show();	
						$("#div_penalitiestype").show();	
				    }
				    else{
				    	$("#div_penalityamount").hide();	
						$("#div_penalitiestype").hide();
				    }
				});

				$('#pendingservlogs').on('change', function() { 
					var clientname = $("#clientname").val();
					if(clientname != undefined && clientname ==""){
						alert("Please select clientname");
						$('#pendingservlogs').prop('checked', false); 
						return false;
					}
					var depot = $("#depot").val();
					if(depot != undefined && depot ==""){
						alert("Please select depot");
						$('#pendingservlogs').prop('checked', false); 
						return false;
					}
					var vehicle = $("#vehicle").val();
					if(vehicle != undefined && vehicle ==""){
						alert("Please select vehicle");
						$('#pendingservlogs').prop('checked', false); 
						return false;
					}
				    if (this.checked) {
				    	clientId =  $("#clientname").val();
						depotId = $("#depot").val();
						vehicle = $("#vehicle").val();

						$.ajax({
					      url: "getpendingservicelogs?clientid="+clientId+"&depotid="+depotId+"&vehicleid="+vehicle,
					      success: function(data) {
						      data = JSON.parse(data);
					    	  $("#pendingdates").html(data[0]);
					    	  $("#starttime option").each(function() {  this.selected =(this.text == "07:00 AM")});
							  $("#starttime option").find('option:selected').attr("selected", "selected"); 
					    	  $('.chosen-select').trigger("chosen:updated");
					      },
					      type: 'GET'
					    });
					    
				    	clientname = $("#clientname option:selected").text();
				    	depot = $("#depot option:selected").text();
				    	vehicle = $("#vehicle option:selected").text();
					    $("#pendingclient").val(clientname);
					    $("#pendingdepot").val(depot);
					    $("#pendingvehicle").val(vehicle);
					    $("#pendinglogs").click();
					    $('#pendingservlogs').prop('checked', false); 
				    }
				});
				
				$('#drv_helper').on('change', function() { 
				    if (this.checked) {
				    	$("#div_driver1").show();	
				    	$("#div_driver2").show();
				    	$("#div_driver3").show();
				    	$("#div_driver4").show();
				    	$("#div_driver5").show();
				    	$("#div_helper").show();
				    }
				    else{
				    	$("#div_driver1").hide();	
				    	$("#div_driver2").hide();
				    	$("#div_driver3").hide();
				    	$("#div_driver4").hide();
				    	$("#div_driver5").hide();				    	
				    	$("#div_helper").hide();
				    }
				});

				 $('#pendingservicelogsform').submit(function() {
					 alert("DF");
				        // DO STUFF
				        return false; // return false to cancel form action
				 });

				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
				url = "getservicelogsdatatabledata?name=servicelogs&clientid="+clientId+"&depotid="+depotId;

					
				//initiate dataTables plugin
				myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)

				//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
		
				.DataTable( {
					bJQueryUI: true,
					"bPaginate": true, "bDestroy": true,
					bInfo: true,
					"aoColumns": [
					  <?php $cnt=count($values["theads"]); for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
					],
					"aaSorting": [],
					oLanguage: {
				        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
				    },
					"bProcessing": true,
			        "bServerSide": true,
					"ajax":{
		                url :url,//"getservicelogsdatatabledata?name=<?php //echo $values["provider"] ?>",  json datasource
		                type: "post",  // method  , by default get
		                error: function(){  // error handling
		                    $(".employee-grid-error").html("");
		                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
		                    $("#employee-grid_processing").css("display","none");
		 
		                }
		            },
			
					//"sScrollY": "500px",
					//"bPaginate": false,
					"sScrollX" : "true",
					//"sScrollX": "300px",
					//"sScrollXInner": "120%",
					"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
			
					//"iDisplayLength": 50
			
			
					select: {
						style: 'multi'
					}
			    } );
			
				
				
				$.fn.dataTable.Buttons.swfPath = "../assets/js/dataTables/extensions/buttons/swf/flashExport.swf"; //in Ace demo ../assets will be replaced by correct assets path
				$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
				
				/*new $.fn.dataTable.Buttons( myTable, {
					buttons: [
					  {
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					  },
					  {
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: false,
						message: 'This print was produced using the Print button for DataTables'
					  }		  
					]
				} );
				myTable.buttons().container().appendTo( $('.tableTools-container') );
				*/
				
				//style the message box
				var defaultCopyAction = myTable.button(1).action();
				myTable.button(1).action(function (e, dt, button, config) {
					defaultCopyAction(e, dt, button, config);
					$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
				});
				
				
				var defaultColvisAction = myTable.button(0).action();
				myTable.button(0).action(function (e, dt, button, config) {
					
					defaultColvisAction(e, dt, button, config);
					
					
					if($('.dt-button-collection > .dropdown-menu').length == 0) {
						$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
					}
					$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
				});
			
				////
			
				setTimeout(function() {
					$($('.tableTools-container')).find('a.dt-button').each(function() {
						var div = $(this).find(' > div').first();
						if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
						else $(this).tooltip({container: 'body', title: $(this).text()});
					});
				}, 500);
				
				
				
				
				
				myTable.on( 'select', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
					}
				} );
				myTable.on( 'deselect', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
					}
				} );
			
			
			
			
				/////////////////////////////////
				//table checkboxes
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
				
				//select/deselect all rows according to table header checkbox
				$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$('#dynamic-table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) myTable.row(row).select();
						else  myTable.row(row).deselect();
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(!this.checked) myTable.row(row).deselect();
					else myTable.row(row).select();
				});
			
			
			
				$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});
				
				
				
				//And for the first simple table, which doesn't have TableTools or dataTables
				//select/deselect all rows according to table header checkbox
				var active_class = 'active';
				$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
						else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#simple-table').on('click', 'td input[type=checkbox]' , function(){
					var $row = $(this).closest('tr');
					if(this.checked) $row.addClass(active_class);
					else $row.removeClass(active_class);
				});
			
				
			
				/********************************/
				//add tooltip for small view action buttons in dropdown menu
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				
				//tooltip placement on right or left
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
				$('<button style="margin-top:-5px;" class="btn btn-minier btn-primary" id="refresh"><i style="margin-top:-2px; padding:6px; padding-right:5px;" class="ace-icon fa fa-refresh bigger-110"></i></button>').appendTo('div.dataTables_filter');
				$("#refresh").on("click",function(){ myTable.search( '', true ).draw(); });
			});
			
		</script>
	@stop