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
				margin-top: 8px;
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
			TRIPS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<?php $jobs = Session::get("jobs"); ?>
		<div class="col-xs-offset-4 col-xs-8 ccordion-style1 panel-group">
			<?php if(in_array(309, $jobs)){ ?>
				<a class="btn btn-sm btn-primary" href="dailytrips">CREATE/ADD SERVICES</a> &nbsp;&nbsp;
			<?php } if(in_array(310, $jobs)){ ?>
				<a class="btn btn-sm  btn-inverse" href="managetrips?triptype=DAILY">MANAGE TRIPS</a> &nbsp;&nbsp;
			<?php }?>
		</div>
		<?php if(in_array(309, $jobs)){ ?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;ADD NEW TRIP SERVICE
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						<div class="col-xs-offset-0 col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
							<div class="col-xs-6">
								<div class="form-group">
									<?php 
										$select_args = array();
										$select_args[] = "cities.name as sourceCity";
										$select_args[] = "servicedetails.serviceNo as serviceNo";
										$select_args[] = "servicedetails.active as active";
										$select_args[] = "servicedetails.serviceStatus as serviceStatus";
										$select_args[] = "servicedetails.sourceCity as id";
										$branches = \ServiceDetails::where("serviceStatus","=","ACTIVE")->where("active","=","Yes")->join("cities","cities.id","=","servicedetails.sourceCity")->groupBy("sourceCity")->select($select_args)->get();
										$branches_arr = array();
										foreach ($branches as $branch){
											$branches_arr[$branch->id] = $branch->sourceCity;
										}
									?>
									<?php $form_field = array("name"=>"city", "content"=>"Starting city of the Services", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr); ?>
									<label class="col-xs-6 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
									<div class="col-xs-6">
										<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
											<option value="">-- {{$form_field['name']}} --</option>
											<?php 
												foreach($form_field["options"] as $key => $value){
													if(isset($values["city"]) && $values["city"]!= "" && $values["city"] == $key){
														echo "<option selected value='$key'>$value</option>";
													}
													else{
														echo "<option value='$key'>$value</option>";
													}
												}
											?>
										</select>
									</div>			
								</div>	
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<label style="margin-left: 40px;" class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper("trip Start date");  echo '<span style="color:red;">*</span>'; ?> </label>
									<div class="col-xs-6">
										<input type="text" id="date" required="required" name="date" <?php if(isset($values["date"])) echo "value='".$values["date"]."'"; ?> class="form-control date-picker" />
									</div>			
								</div>
							</div>			
							<div class="col-xs-1" >
								<div class="form-group">
									<label class="col-xs-0 control-label no-padding-right" for="form-field-1"> </label>
									<div class="col-xs-5" id="verify">
										<input type="button" class="btn btn-sm btn-primary" value="GET" onclick="verifyDate()"/>
									</div>			
								</div>
							</div>
						</div>
												
					</div>
					<div class="row">
						<?php 
							$values = Input::All();
							if(isset($values["city"]) && isset($values["date"])){		
						?>				
						<div class="col-xs-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="center">
											<label class="pos-rel">
												<span class="lbl"></span>
											</label>
										</th>
										<th>SERVICE NOs</th>
										<th>DRIVERS</th>
										<th class="hidden-480">HELPER & VEHICLE</th>

										<th>
											<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
											TRIP DETAILS
										</th>
									</tr>
								</thead>
								<?php 
									$url = "adddailytrips";
									if(isset($values["city"]) && isset($values["date"])){
										$url = $url."?city=".$values["city"]."&date=".$values["date"];
									}
								?>
								<form name="tripsform" action="{{$url}}" method="post" onsubmit="return validateData();">
									<tbody>
										<?php 
											$drivers = array();
											$helpers =array();
											$vehicles = array();
											$select_args = array();
											$select_args[] = "cities.name as sourceCity";
											$select_args[] = "cities1.name as destinationCity";
											$select_args[] = "servicedetails.serviceNo as serviceNo";
											$select_args[] = "servicedetails.active as active";
											$select_args[] = "servicedetails.serviceStatus as serviceStatus";
											$select_args[] = "servicedetails.id as id";
											$entities = array();
											$assignedValues = \TripServiceDetails::where("serviceDate","=",date("Y-m-d",strtotime($values['date'])))->get();
											$assignedDrivers_arr = array();
											$assignedVehicles_arr = array();
											$assignedHelpers_arr = array();
											foreach ($assignedValues as $assignedValue){
												$assignedDrivers_arr[] = $assignedValue->driver1;
												$assignedDrivers_arr[] = $assignedValue->driver2;
												$assignedVehicles_arr[] = $assignedValue->vehicleId;
												$assignedHelpers_arr[] = $assignedValue->helper;
											}
											$drivers = Employee::where("roleId","=",19)->get();
											$helpers = Employee::where("roleId","=",20)->get();
											$vehicles = Vehicle::All();
											
											$entities = \ServiceDetails::where("sourceCity","=",$values["city"])->where("serviceStatus","=","ACTIVE")->where("active","=","Yes")->join("cities","cities.id","=","servicedetails.sourceCity")->join("cities as cities1","cities1.id","=","servicedetails.destinationCity")->select($select_args)->get();
											$i = 0;
											foreach($entities as $entity){
												$tripservice = \TripServiceDetails::where("serviceId","=",$entity->id)->where("serviceDate","=",date("Y-m-d",strtotime($values["date"])))->get();
												if(count($tripservice)>0){
													$tripservice = $tripservice[0];
										?>
										<tr>
											<td class="center" style="font-weight: bold; vertical-align: middle">
												<label class="pos-rel">
													<input type="checkbox" class="ace" name="ids[]" id="ids_{{$i}}" value="{{$i}}" disabled="disabled"/>
													<span class="lbl"></span>
												</label>
												<input type="hidden" name="id[]" id="id_{{$i}}" value="{{$entity->id}}" />
												<input type="hidden" name="servnos[]" id="servnos_{{$i}}" value="{{$entity->serviceNo}}" />
											</td>
	
											<td style="font-weight: bold; vertical-align: middle">
												<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->serviceNo}}</span> - {{$entity->sourceCity}} <span style="font-weight: bold; font-size:10px;">TO</span> {{$entity->destinationCity}}
											</td>
											<td>
												<input type="hidden" name="drivers1[]" value="{{$tripservice->driver1}}"/>
												<select name="drivers1[]" id="driver1_{{$i}}" class="form-control chosen-select" disabled="disabled">
												<?php 
													
													foreach($drivers as $driver){
														if($driver->id == $tripservice->driver1){
															echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
														}
													}
												?>
												</select>
												<br/>	
												<input type="hidden" name="drivers2[]" value="{{$tripservice->driver2}}"/>										
												<select name="drivers2[]" id="driver2_{{$i}}" class="form-control chosen-select" disabled="disabled">
												<?php 
													foreach($drivers as $driver){
														if($driver->id == $tripservice->driver2){
															echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
														}
													}
												?>
												</select>
											</td>
											<td>
												<input type="hidden" name="helper[]" value="{{$tripservice->helper}}"/>	
												<select name="helper[]" id="helper_{{$i}}"  class="form-control chosen-select" disabled="disabled">
												<?php 
													foreach($helpers as $helper){
														if($helper->id == $tripservice->helper){
															echo "<option value='".$helper->id."'>".$helper->fullName."</option>";
														}
													}
												?>
												</select>
												<br/>	
												<input type="hidden" name="vehicle[]" value="{{$tripservice->vehicleId}}"/>											
												<select name="vehicle[]" id="vehicle_{{$i}}" class="form-control chosen-select" disabled="disabled">
												<?php 
													foreach($vehicles as $vehicle){
														if($vehicle->id == $tripservice->vehicleId){
															echo "<option value='".$vehicle->id."'>".$vehicle->veh_reg."</option>";
														}
													}
												?>
												</select>
											</td>
											<td class="center" style="font-weight: bold; vertical-align: middle">
												<input type="hidden" name="dates[]" value="{{$tripservice->vehicleId}}"/>
												<?php 
													$trip = \TripDetails::where("id","=",$tripservice->tripId)->get();
													if(count($trip)>0){
														$trip = $trip[0];
														echo "TRIP START DATE : ".$trip->tripStartDate." -- TRIP ROUTE : ".$trip->routeCount;
													}
												?>
											</td>
										</tr>
										<?php } else {?>
										<tr>
											<td class="center" style="font-weight: bold; vertical-align: middle">
												<label class="pos-rel">
													<input type="checkbox" class="ace" name="ids[]" id="ids_{{$i}}" value="{{$i}}"/>
													<span class="lbl"></span>
												</label>
												<input type="hidden" name="id[]" id="id_{{$i}}" value="{{$entity->id}}" />
												<input type="hidden" name="servnos[]" id="servnos_{{$i}}" value="{{$entity->serviceNo}}" />
											</td>
	
											<td style="font-weight: bold; vertical-align: middle">
												<span style="color: red; font-weight: bold; font-size:14px;">{{$entity->serviceNo}}</span> - {{$entity->sourceCity}} <span style="font-weight: bold; font-size:10px;">TO</span> {{$entity->destinationCity}}
											</td>
											<td>
												<select name="drivers1[]" id="driver1_{{$i}}" class="form-control chosen-select">
												<option value="">driver1</option>
												<?php 
													foreach($drivers as $driver){
														if(!in_array($driver->id, $assignedDrivers_arr)){
															echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
														}
													}
												?>
												</select>
												<br/>											
												<select name="drivers2[]" id="driver2_{{$i}}" class="form-control chosen-select">
												<option value="">driver2</option>
												<?php 
													foreach($drivers as $driver){
														if(!in_array($driver->id, $assignedDrivers_arr)){
															echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
														}
													}
												?>
												</select>
											</td>
											<td>
												<select name="helper[]" id="helper_{{$i}}"  class="form-control chosen-select">
												<option value="">helper</option>
												<?php 
													foreach($helpers as $helper){
														if(!in_array($helper->id, $assignedHelpers_arr)){
															echo "<option value='".$helper->id."'>".$helper->fullName."</option>";
														}
													}
												?>
												</select>
												<br/>											
												<select name="vehicle[]" id="vehicle_{{$i}}" class="form-control chosen-select">
												<option value="" >vehicle</option>
												<?php 
													foreach($vehicles as $vehicle){
														if(!in_array($vehicle->id, $assignedVehicles_arr)){
															echo "<option value='".$vehicle->id."'>".$vehicle->veh_reg."</option>";
														}
													}
												?>
												</select>
											</td>
											<td class="center" style="font-weight: bold; vertical-align: middle">
												<input type="checkbox" class="ace" name="newtrip[]" value="{{$entity->id}}" id="trip_{{$i}}" onclick="changeDate({{$i}})" />
												<span class="lbl"> NEW TRIP</span><br/>
												<span class="lbl">TRIP START DATE</span>
												<input type="text" class="form-control date-picker" name="dates[]" id="date_{{$i}}" />
											</td>
										</tr>
										<?php }?>
										<?php $i++; }?>
									</tbody>
									
							</table>
							<div class="clearfix form-actions" style="margin-bottom: 0px;" >
								<div class="col-md-offset-4 col-md-8" style="margin-top: 2%; margin-bottom: 1%">
									<button id="submit" class="btn primary" type="submit" id="submit">
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
						</div><!-- /.span -->
						<?php } ?>
					</div>
				</div>
			</div>
		</div>	
		<?php }?>
		</div>		
		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal)
		<?php }} ?>
		
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/js/dataTables/jquery.dataTables.bootstrap.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/dataTables.buttons.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.flash.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.html5.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.print.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.colVis.js"></script>
		<script src="../assets/js/dataTables/extensions/select/dataTables.select.js"></script>
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/date-time/moment.js"></script>
		<script src="../assets/js/date-time/daterangepicker.js"></script>		
		<script src="../assets/js/bootbox.js"></script>
		<script src="../assets/js/chosen2.jquery.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			function changeDate(val){
				if(document.getElementById("trip_"+val).checked){
					var today = new Date();
				    var dd = today.getDate();
				    var mm = today.getMonth()+1; //January is 0!

				    var yyyy = today.getFullYear();
				    if(dd<10){
				        dd='0'+dd
				    } 
				    if(mm<10){
				        mm='0'+mm
				    } 
				    var today = dd+'-'+mm+'-'+yyyy;
					$("#date_"+val).val($("#date").val());
					$("#date_"+val).prop("readonly",true);
				}
				else{
					$("#date_"+val).val("");
					$("#date_"+val).prop("readonly",false);
				}
			}

			function verifyDate(){
				city = $("#city").val();
				dt = $("#date").val();
				if(city == ""){
					alert("select Service City");
					return;
				}
				if(dt == ""){
					alert("select date");
					return;
				}
				$('#verify').hide();
				location.replace("dailytrips?city="+city+"&date="+dt);
			}

			$("#reset").on("click",function(){
				<?php if(in_array(309, $jobs)){ ?>
					$("#{{$form_info['name']}}").reset();
				<?php } ?>
			});

			function validateData(){
				var ids = document.forms['tripsform'].elements[ 'ids[]' ];
				for(i=0; i<ids.length;i++){
					if(ids[i].checked){
						if($("#driver1_"+i).val()==""){
							alert("select complete information for service no : "+$("#servnos_"+i).val());
							return false;
						}
						if($("#vehicle_"+i).val()==""){
							alert("select complete information for service no : "+$("#servnos_"+i).val());
							return false;
						}
						if($("#helper_"+i).val()==""){
							//alert("select complete information for service no : "+$("#servnos_"+i).val());
							//return false;
						}
						if($("#date_"+i).val()==""){
							alert("select complete information for service no : "+$("#servnos_"+i).val());
							return false;
						}
					}
				    
				}
			};

			$("#provider").on("change",function(){
				val = $("#provider option:selected").html();
				window.location.replace('serviceproviders?provider='+val);
			});

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

			//$('.input-mask-phone').mask('(999) 999-9999');
			
			

			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
				}
			?>

			//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
			$('.date-range-picker').daterangepicker({
				'applyClass' : 'btn-sm btn-success',
				'cancelClass' : 'btn-sm btn-default',	
				locale: {
					applyLabel: 'Apply',
					cancelLabel: 'Cancel',
				}
			})
			<?php 
				if(isset($values["daterange"])){
					echo "$('.date-range-picker').val('".$values["daterange"]."')";
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
		</script>
	@stop