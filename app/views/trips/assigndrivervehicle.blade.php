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
			TRIPS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop
	
	<?php 
		$total_expenses = 0;
		$total_advance = 0;
		$total_fuel_amount = 0;
		$total_incomes = 0;
		$total = 0.0;
		
		$parentId = -1;
		$tripparticulars_arr = array();
		$parent = \LookupTypeValues::where("name","=","TRIP ADVANCES")->get();
		if(count($parent)>0){
			$parent = $parent[0];
			$parentId = $parent->id;
		}
		$tripparticulars =  \LookupTypeValues::where("parentId","=",$parentId)->where("status", "=", "ACTIVE")->get();
		foreach ($tripparticulars as $tripparticular){
			$tripparticulars_arr[] = $tripparticular->id;
		}
		$select_args = array();
		$select_args[] = "officebranch.name as branchId";
		$select_args[] = "tripparticulars.vehicleId as vehicleId";
		$select_args[] = "tripparticulars.amount as amount";
		$select_args[] = "tripparticulars.date as date";
		$select_args[] = "tripparticulars.remarks as remarks";
		$tripadvances = \TripParticulars::where("tripId","=",$values["id"])->where("tripType","=","LOCAL")->where("status","=","ACTIVE")->whereIn("lookupValueId",$tripparticulars_arr)->leftjoin("officebranch","officebranch.id","=","tripparticulars.branchId")->select($select_args)->get();
		foreach($tripadvances as $tripadvance){
			$total = $total+$tripadvance->amount;
		} 
		$total_advance = $total;
	?>

	@section('page_content')
		<div class="col-xs-offset-4 col-xs-8 ccordion-style1 panel-group">
			<div style="float:left;">
				<a class="btn btn-sm btn-primary" href="addlocaltrip">CREATE/ADD BOOKING</a> &nbsp;&nbsp;
				<a class="btn btn-sm  btn-inverse" href="managetrips?triptype=LOCAL">MANAGE TRIPS</a> &nbsp;&nbsp;
			</div>
			<div style="float:right;">
				<a href="tripclosingreport?tripid={{$values['id']}}" class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-print bigger-160"></i>
					CLOSING REPORT
				</a>
			</div>
		</div>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST1">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;BOOKING INFORMATION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST1">
					<div class="panel-body" style="padding: 0px">
					<div class="col-xs-offset-0 col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>SOURCE JOURNEY INFO</th>
									<th>RETURN JOURNEY INFO</th>
									<th>CUSTOMER INFO</th>
									<th>BOOKING AMOUNT INFO</th>
								</tr>
							</thead>
							<tbody>
								<tr>
							<?php 
								$source_busno = 0;
								$dest_busno = 0;
								if(isset($values["id"])){
									$entities = \BusBookings::where("id","=",$values["id"])->get();
									foreach ($entities as $entity){
										$entity["source_date"] = date("d-m-Y",strtotime($entity["source_date"]));
										$entity["dest_date"] = date("d-m-Y",strtotime($entity["dest_date"]));
										if($entity["dest_date"] == "01-01-1970"){
											$entity["dest_date"] = "";
										}
										$entity["sourcetrip"] = $entity["source_start_place"]."<br/> ".$entity["source_end_place"];
										$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>Date & Time &nbsp;: ".$entity["source_date"]." ".$entity["source_time"];
										$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>Bus Type  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$entity["source_bustype"];
										$entity["sourcetrip"] = $entity["sourcetrip"]."<br/>No of buses &nbsp;&nbsp;: ".$entity["source_busno"];
										$entity["returntrip"] = $entity["dest_start_place"]."<br/> ".$entity["dest_end_place"];
										$entity["returntrip"] = $entity["returntrip"]."<br/>Date & Time &nbsp;: ".$entity["dest_date"]." ".$entity["dest_time"];
										$entity["returntrip"] = $entity["returntrip"]."<br/>Bus Type  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$entity["dest_bustype"];
										$entity["returntrip"] = $entity["returntrip"]."<br/>No of buses &nbsp;&nbsp;: ".$entity["dest_busno"];
										
										$entity["custinfo"] = "Name : ".$entity["cust_name"]."<br/>Phone : ".$entity["cust_phone"];
										$entity["custinfo"] = $entity["custinfo"]."<br/><br/><br/><span>Fuel Charge By : ".$entity["fuel_charge_type"]."</span>";
										$entity["amount"] = "TOTAL &nbsp;&nbsp;&nbsp;   &nbsp;  : ".$entity["total_cost"]."<br/>ADVANCE : ".$total_advance."<br/>BALANCE : ".($entity["total_cost"]-$total_advance);
										$entity["amount"] = $entity["amount"]."<br/><br/><span style='font-size: 15px;font-weight: bold;color: red;'>BOOKING NO : ".$entity["booking_number"]."</span>";
										echo "<td>".$entity->sourcetrip."</td>";
										echo "<td>".$entity->returntrip."</td>";
										echo "<td>".$entity->custinfo."</td>";
										echo "<td>".$entity->amount."</td>";
										$source_busno = $entity->source_busno;
										$dest_busno = $entity->dest_busno;
									}
								}
							?>
								</tr>
							</tbody>
						</table>
					</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-offset-2 col-xs-10 ccordion-style1 panel-group">
			<a class="btn btn-sm  btn-purple" href="addlocaltripparticular?id={{$values['id']}}&type=advances">ADD TRIP ADVANCE</a>&nbsp;&nbsp;
			<a class="btn btn-sm btn-purple" href="addlocaltripparticular?id={{$values['id']}}&type=expenses_and_incomes">ADD TRIP EXPESENSES/INCOMES</a> &nbsp;&nbsp;
			<a class="btn btn-sm btn-purple" href="addlocaltripfuel?id={{$values['id']}}&triptype=LOCAL&transtype=fuel">ADD TRIP FUEL EXPENSES</a> &nbsp;&nbsp;
			<a class="btn btn-sm btn-purple" href="assigndrivervehicle?id={{$values['id']}}&triptype=LOCAL">ASSIGN DRIVER & VEHICLE</a> &nbsp;&nbsp;
			<a class="btn btn-sm btn-purple" href="bookingrefund?id={{$values['id']}}&triptype=LOCAL&transtype=bookingrefund">BOOKING REFUND</a> &nbsp;&nbsp;
		</div>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;ASSIGN DRIVER & VEHICLE
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
					</div>
					<div class="row">
						<div class="col-xs-12" style="padding:2%">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>ROUTE TYPE</th>
										<th>DRIVER1</th>
										<th>DRIVER2</th>
										<th>HELPER</th>
										<th>VEHICLE</th>
									</tr>
								</thead>
								<?php 
									$url = "adddailytrips";
								?>
								<form name="tripsform" action="assigndrivervehicle" method="post" onsubmit="return validateData();">
									<?php 
										$tripid = BusBookings::where("id","=",$values["id"])->first();
										$tripid = $tripid->booking_number;
									?>
									<input type="hidden" name="bookingnumber" value="{{$tripid}}" />
									<input type="hidden" name="tripid" value="{{$values['id']}}" />
									<tbody>
										<?php 
											$drivers = Employee::where("roleId","=",19)->get();
											$helpers = Employee::where("roleId","=",20)->get();
											$vehicles = Vehicle::All();
											$select_args = array();
											$select_args[] = "cities.name as sourceCity";
											$select_args[] = "cities1.name as destinationCity";
											$select_args[] = "servicedetails.serviceNo as serviceNo";
											$select_args[] = "servicedetails.active as active";
											$select_args[] = "servicedetails.serviceStatus as serviceStatus";
											$select_args[] = "servicedetails.id as id";
											$entities = array();
											$i=0;
											$j=0;
										for($j=0;$j<$source_busno; $j++){?>
										<tr>
											<td style="font-weight: bold; vertical-align: middle">
												<span style="color: red; font-weight: bold; font-size:14px;">SOURCE ROUTE#{{1}}</span>
												<input type="hidden" name="vehicleno[]" value="1" />
												<input type="hidden" name="tripfrom[]" value="source" />
											</td>
											<td>
												<select name="drivers1[]" id="driver1_{{$i}}" class="form-control chosen-select">
													<option value="">--select driver1--</option>
												<?php 
													
													foreach($drivers as $driver){
														echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
													}
												?>
												</select>
											</td>
											<td>	
												<select name="drivers2[]" id="driver2_{{$i}}" class="form-control chosen-select" >
												<option value="">--select driver2--</option>
												<?php 
													foreach($drivers as $driver){
														echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
													}
												?>
												</select>
											</td>
											<td>
												<select name="helper[]" id="helper_{{$i}}"  class="form-control chosen-select">
												<option value="">--select helper--</option>
												<?php 
													foreach($helpers as $helper){
														echo "<option value='".$helper->id."'>".$helper->fullName."</option>";
													}
												?>
												</select>
											</td>
											<td>	
												<select name="vehicle[]" id="vehicle_{{$i}}" class="form-control chosen-select" >
												<option value="">--select vehicle--</option>
												<?php 
													foreach($vehicles as $vehicle){
														echo "<option value='".$vehicle->id."'>".$vehicle->veh_reg."</option>";
													}
												?>
												</select>
											</td>
										</tr>
										<?php }
											for($j=0;$j<$dest_busno; $j++){?>
										<tr>
											<td style="font-weight: bold; vertical-align: middle">
												<span style="color: red; font-weight: bold; font-size:14px;">RETURN ROUTE#{{2}}</span>
												<input type="hidden" name="vehicleno[]" value="2" />
												<input type="hidden" name="tripfrom[]" value="destination" />
											</td>
											<td>
												<select name="drivers1[]" id="driver1_{{$i}}" class="form-control chosen-select">
													<option value="">--select driver1--</option>
												<?php 
													
													foreach($drivers as $driver){
														echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
													}
												?>
												</select>
											</td>
											<td>	
												<select name="drivers2[]" id="driver2_{{$i}}" class="form-control chosen-select" >
												<option value="">--select driver2--</option>
												<?php 
													foreach($drivers as $driver){
														echo "<option value='".$driver->id."'>".$driver->fullName."</option>";
													}
												?>
												</select>
											</td>
											<td>
												<select name="helper[]" id="helper_{{$i}}"  class="form-control chosen-select">
												<option value="">--select helper--</option>
												<?php 
													foreach($helpers as $helper){
														echo "<option value='".$helper->id."'>".$helper->fullName."</option>";
													}
												?>
												</select>
											</td>
											<td>	
												<select name="vehicle[]" id="vehicle_{{$i}}" class="form-control chosen-select" >
												<option value="">--select vehicle--</option>
												<?php 
													foreach($vehicles as $vehicle){
														echo "<option value='".$vehicle->id."'>".$vehicle->veh_reg."</option>";
													}
												?>
												</select>
											</td>
										</tr>
										<?php } ?>
									</tbody>
									
							</table>
							<div class="clearfix form-actions" style="margin-bottom: 0px;" >
								<div class="col-md-offset-4 col-md-8" style="margin-top: 0%; margin-bottom: 0%">
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
					</div>
				</div>
			</div>
		</div>
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
				$("#{{$form_info['name']}}").reset();
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
							alert("select complete information for service no : "+$("#servnos_"+i).val());
							return false;
						}
						if($("#date_"+i).val()==""){
							alert("select complete information for service no : "+$("#servnos_"+i).val());
							return false;
						}
					}
				    
				}
			};
			function modalEditAssignedValues(id,driver1, driver2, helper){
				//alert("This is test id: "+id+" This is test driver1: "+driver1+" This is test driver2: "+driver2+" This is test helper: "+helper);
				$("#driver11 option").each(function() {this.selected = (this.value == driver1); });
				$("#driver21 option").each(function() { this.selected = (this.value == driver2); });
				$("#helper1 option").each(function() { this.selected = (this.value == helper); });
				$("#id1").val(id);
				$('.chosen-select').trigger('chosen:updated');		
			}
			
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

			jQuery(function($) {		
				//initiate dataTables plugin
				var myTable = 
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
		                url :"gettripsdatatabledata?name=assigndrivervehicle&id={{$values['id']}}", // json datasource
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