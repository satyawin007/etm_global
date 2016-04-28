<?php
use Illuminate\Support\Facades\Input;
?>
	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="../assets/css/bootstrap.css" />
	<link rel="stylesheet" href="../assets/css/font-awesome.css" />

	<!-- page specific plugin styles -->

	<!-- text fonts -->
	<link rel="stylesheet" href="../assets/css/ace-fonts.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="../assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />

	<!--[if lte IE 9]>
		<link rel="stylesheet" href="../assets/css/ace-part2.css" class="ace-main-stylesheet" />
	<![endif]-->

	<!--[if lte IE 9]>
	  <link rel="stylesheet" href="../assets/css/ace-ie.css" />
	<![endif]-->

	<!-- inline styles related to this page -->

	<!-- ace settings handler -->
	<script src="../assets/js/ace-extra.js"></script>

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
			font-size:13px;
		    white-space: nowrap;
		}
		panel-group .panel {
		    margin-bottom: 20px;
		    border-radius: 4px;
		}
		label{
			text-align: right;
			margin-top: 2px;
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
		    margin-top: 0px;
		    /* margin-bottom: 20px; 
		    margin-top: 20px;
		    padding: 19px 20px 20px;*/
		}
		.chosen-container{
		  width: 100% !important;
		}
		body{
			background: white;
		}
	</style>
	<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
	<?php 
		$values = Input::All();
		$total_expenses = 0;
		$total_advance = 0;
		$total_fuel_amount = 0;
		$total_incomes = 0;
	?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST1">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP INFORMATION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST1">
					<div class="panel-body" style="padding: 0px">
					<div class="col-xs-offset-0 col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>VEHICLE REG NO</th>
									<th>START DATE</th>
									<th>ROUTE INFORMATION</th>
									<th>DRIVER1</th>
									<th>DRIVER2</th>
									<th>HELPER</th>
									<th>CLOSE DATE</th>
									<th>ROUTES</th>
								</tr>
							</thead>
							<tbody>
								<tr>
							<?php 
								$select_args[] = "vehicle.veh_reg as vehicleId";
								$select_args[] = "tripdetails.tripStartDate as tripStartDate";
								$select_args[] = "tripdetails.id as routeInfo";
								$select_args[] = "tripdetails.tripCloseDate as tripCloseDate";
								$select_args[] = "tripdetails.routeCount as routes";
								$select_args[] = "tripdetails.id as id";
								
								if(isset($values["id"])){
									$entities = \TripDetails::where("tripdetails.id","=",$values["id"])->leftjoin("vehicle", "vehicle.id","=","tripdetails.vehicleId")->select($select_args)->get();
									foreach ($entities as $entity){
										$entity["tripStartDate"] = date("d-m-Y",strtotime($entity["tripStartDate"]));
										$entity["tripCloseDate"] = date("d-m-Y",strtotime($entity["tripCloseDate"]));
										if($entity["tripCloseDate"] == "01-01-1970"){
											$entity["tripCloseDate"] = "NOT CLOSED";
										}
										$entity["fuelamount"] = 0;
										$entity["routeInfo"] = "";
										$entity["totalAdvance"] = \TripAdvances::where("tripID","=",$entity->id)->where("deleted","=","No")->sum("amount");
										$routeInfo = "";
										$driver1 = "";
										$driver2 = "";
										$helper = "";
										$employees = \Employee::where("roleId","=","19")->orWhere("roleId","=","20")->get();
										$tripservices = \TripServiceDetails::where("tripId","=",$entity->id)->where("status","=","Running")->get();
										foreach($tripservices as $tripservice){
											$select_args = array();
											$select_args[] = "cities.name as sourceCity";
											$select_args[] = "cities1.name as destinationCity";
											$select_args[] = "servicedetails.serviceNo as serviceNo";
											$select_args[] = "servicedetails.active as active";
											$select_args[] = "servicedetails.serviceStatus as serviceStatus";
											$select_args[] = "servicedetails.id as id";
											$service = \ServiceDetails::where("servicedetails.id","=",$tripservice->serviceId)->join("cities","cities.id","=","servicedetails.sourceCity")->join("cities as cities1","cities1.id","=","servicedetails.destinationCity")->select($select_args)->get();
											if(count($service)>0){
												$service = $service[0];
												$routeInfo = $routeInfo."<span style='font-size:13px; font-weight:bold; color:red;'>".$service->serviceNo."</span> - &nbsp; ".$service->sourceCity." TO ".$service->destinationCity."<br/>";
											}
											foreach ($employees as $employee){
												if($employee->id == $tripservice->driver1){
													$driver1 = $employee->fullName."<br/>".$driver1;
												}
												else if($employee->id == $tripservice->driver2){
													$driver2 = $employee->fullName."<br/>".$driver2;
												}
												else if($employee->id == $tripservice->helper){
													$helper = $employee->fullName."<br/>".$helper;
												}
											}
										}
										$entity["routeInfo"] = $routeInfo;
										echo "<td>".$entity->vehicleId."</td>";
										echo "<td>".$entity->tripStartDate."</td>";
										echo "<td>".$routeInfo."</td>";
										echo "<td>".$driver1."</td>";
										echo "<td>".$driver2."</td>";
										echo "<td>".$helper."</td>";
										echo "<td>".$entity->tripCloseDate."</td>";
										echo "<td>".$entity->routes."</td>";
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
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST1">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP INFORMATION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST1">
					<div class="panel-body" style="padding: 0px">
					<div class="col-xs-offset-0 col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>PARTICULAR NAME</th>
									<th>TYPE</th>
									<th>DATE</th>
									<th>AMOUNT</th>
									<th>BRANCH/INCHARGE</th>
									<th>REMARKS</th>
									<th>STATUS</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								$total = 0;
								$data = array();
								$select_args = array();
								$select_args[] = "tripparticulars.tripId as tripId";
								$select_args[] = "tripparticulars.lookupValueId as lookupValueId";
								$select_args[] = "tripparticulars.lookupValueId as type";
								$select_args[] = "tripparticulars.date as date";
								$select_args[] = "tripparticulars.amount as amount";
								$select_args[] = "tripparticulars.branchId as branchId";
								$select_args[] = "tripparticulars.remarks as remarks";
								$select_args[] = "tripparticulars.status as status";
								$select_args[] = "tripparticulars.id as id";
								$select_args[] = "tripparticulars.inchargeId as inchargeId";
								
								$entities = \TripParticulars::where("tripId","=",$values["id"])->where("status","=","ACTIVE")->where("tripType","=","DAILY")->select($select_args)->get();
								foreach ($entities as $entity){
									$entity["date"] = date("d-m-Y",strtotime($entity["date"]));
									$parentId = -1;
									$parent = \LookupTypeValues::where("id","=",$entity->lookupValueId)->get();
									if(count($parent)>0){
										$parent = $parent[0];
										$parentId = $parent->parentId;
										$entity->lookupValueId = $parent->name;
									}
									$parent = \LookupTypeValues::where("id","=",$parentId)->get();
									if(count($parent)>0){
										$parent = $parent[0];
										$parentId = $parent->id;
										$entity->type = $parent->name;
									}
									if($entity->branchId>0){
										$branch = \OfficeBranch::where("id","=",$entity->branchId)->get();
										if(count($branch)>0){
											$branch = $branch[0];
											$entity->branchId = $branch->name;
										}
									}
									else{
										$entity->branchId = "";
									}
									if($entity->inchargeId>0){
										$select_args = array();
										$select_args[] = "employee.fullName as name";
										$select_args[] = "inchargeaccounts.id as id";
										$incharges = \InchargeAccounts::where("inchargeaccounts.id","=",$entity->inchargeId)->join("employee","employee.id","=","inchargeaccounts.empid")->select($select_args)->get();
										if(count($incharges)>0){
											$incharges = $incharges[0];
											$entity->branchId = $incharges->name;
										}
									}
									$value_name_arr = array("9999"=>"DEBITED FROM BRANCH", "8888"=>"CREDITED TO BRANCH", "9001"=>"Last Closing Reading", "9002"=>"Initial Reading", "9003"=>"Closing Reading", "9004"=>"Wasted Meters", "9005"=>"Meter Reading Remarks");
									$name_arr = array("9999", "8888", "9001", "9002", "9003", "9004", "9005");
									if(in_array($entity->lookupValueId,$name_arr)){
										$entity->lookupValueId = $value_name_arr[$entity->lookupValueId];
									}
								}
								foreach($entities as $entity){
								?>
									<tr>
										<td>{{$entity->lookupValueId}}</td>
										<td>{{$entity->type}}</td>
										<td>{{$entity->date}}</td>
										<td>{{$entity->amount}}</td>
										<td>{{$entity->branchId}}</td>
										<td>{{$entity->remarks}}</td>
										<td>{{$entity->status}}</td>
									</tr>
								<?php 
								}
								?>
							</tbody>
						</table>
					</div>
					</div>
				</div>
			</div>
		</div>
		<!-- div.dataTables_borderWrap -->
	
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
		<script src="../assets/js/chosen.jquery.js"></script>
	
		<!-- inline scripts related to this page -->
		<script type="text/javascript">

			function modalEditTripParticulars(id,lookupvalue, date, amount, branch, remarks, status){
				$("#branch2").prop("disabled",false);
				$("#incharge1").prop("disabled",false);
				$("#lookupvalue1 option").each(function() {this.selected = (this.text == lookupvalue); });	
				$("#lookupvalue1").prop("disabled",true);	
				$("#date1").val(date);
				$("#amount1").val(amount);
				$("#remarks1").text(remarks);
				if(branch==""){
					$("#branch2").prop("disabled",true);
					$("#incharge1").prop("disabled",true);
				}
				else{
					$("#branch2 option").each(function() {this.selected = (this.text == branch); });
					$("#incharge1 option").each(function() {this.selected = (this.text == branch); });
				}
				
				$("#amou option").each(function() { this.selected = (this.value == driver2id); });
				$("#helper option").each(function() { this.selected = (this.value == helperid); });
				$("#status option").each(function() { this.selected = (this.value == status); });
				$("#id1").val(id);
				$('.chosen-select').trigger('chosen:updated');		
			}

			function validateData(){
				var ids = document.forms['tripsform'].elements[ 'ids[]' ];
				for(i=0; i<ids.length;i++){
					if(ids[i].checked){
						if($("#amount_"+i).val()==""){
							alert("select complete information for trip particular : "+$("#names_"+i).val());
							return false;
						}
						if($("#remarks_"+i).val()==""){
							alert("select complete information for trip particular : "+$("#names_"+i).val());
							return false;
						}
					}
				    
				}
			};

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
					],
					"aaSorting": [],
					oLanguage: {
				        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
				    },
					"bProcessing": false,
			        "bServerSide": false,
					"ajax":{
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
