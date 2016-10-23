@extends('masters.master')
	@section('inline_css')
		<style>
			.page-header h1 {
				padding: 0;
				margin: 0 3px;
				font-size: 12px;
				font-weight: lighter;
				color: #2679b5;
			}
			
			button, input, optgroup, select, textarea {
				color: inherit;
				font: inherit;
				margin: 10px;
				padding : 10px;
			}
			a{
				text-decoration:none;
			}
		</style>
	@stop

	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			DASHBOARD
		</small>
	@stop

	@section('page_content')
		<div class="col-xs-12 center">
			<div class="col-xs-12 center">
				<?php 
					$rec = Parameters::where("name","=","dashboardmessage")->get();
					$rec = $rec[0];
				?>
				<marquee>{{$rec->value}}</marquee>
			</div>
			<div class="col-xs-6">
				<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">VEHICLE RENEWALS</h3>
				<table id="dynamic-table1" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>VEHICLE REG NO</th>
							<th>RENEWALS INFO</th>
							<th>EXPIRED DAYS</th>
							<th>EXPIRED IN 10 DAYS</th>
							<th>EXPIRED IN 30 DAYS</th>
						</tr>
					</thead>
					<tbody>
					<?php 
						$select_args = array();
						$select_args[] = "vehicle.veh_reg as veh_reg";
						$select_args[] = "lookuptypevalues.name as name";
						$select_args[] = "expensetransactions.nextAlertDate as nextAlertDate";
						$entities = \Vehicle::where("vehicle.status","=","ACTIVE")
									->where("expensetransactions.nextAlertDate","!=","0000-00-00")
									->where("expensetransactions.nextAlertDate","!=","1970-01-01")
									->leftjoin("expensetransactions","expensetransactions.vehicleIds","=","vehicle.id")
									->leftjoin("lookuptypevalues","expensetransactions.lookupValueId","=","lookuptypevalues.id")
									->select($select_args)->orderBy("vehicle.id")->get();
						$cnt = 0;
						$today = date("Y-m-d");
						foreach ($entities as $entity){
							$date1=date_create($today);
							$date2=date_create($entity->nextAlertDate);
							$diff=date_diff($date1,$date2);
							// 				echo $diff->format("%R%a").", "; continue;
							$row = array();
							if($diff->format("%R%a") > 0 && $diff->format("%R%a") < 30){
								echo "<tr>";
								echo "<td>".$entity->veh_reg."</td>";
								echo "<td>".$entity->name."</td>";
								echo "<td></td>";
								if($diff->format("%R%a") > 0 && $diff->format("%R%a") < 10){
									echo "<td>".'<span class="badge badge-warning">'.($diff->format("%a")).'</span>'."</td>";
								}
								else{
									echo "<td></td>";
								}
								if($diff->format("%R%a") >= 10){
									echo "<td>".'<span class="badge badge-danger">'.($diff->format("%a")).'</span>'."</td>";
								}
								else{
									echo "<td></td>";
								}
								echo "</tr>";
							}
							else if($diff->format("%R%a") < 0){
								echo "<tr>";
								echo "<td>".$entity->veh_reg."</td>";
								echo "<td>".$entity->name."</td>";
								echo "<td>".'<span class="badge badge-inverse">'.$diff->format("%a").'</span>'."</td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "</tr>";
							}
						}
					?>
					</tbody>
				</table>								
			</div>
			
			<div class="col-xs-6">
				<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">EMPLOYEE LEAVES STATUS</h3>
				<table id="dynamic-table6" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>EMPLOYEE</th>
							<th>SENT FOR APP</th>
							<th>PENDING FOR APP</th>
							<th>APPROVED</th>
							<th>REJECTED</th>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>								
			</div>
		</div>
		<div class="col-xs-12 center">
			<div class="col-xs-6">
				<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">FEUL TRANSACTIONS STATUS</h3>
				<table id="dynamic-table2" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>EMPLOYEE</th>
							<th>SENT FOR APP</th>
							<th>PENDING FOR APP</th>
							<th>APPROVED</th>
							<th>REJECTED</th>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>								
			</div>
			
			<div class="col-xs-6">
				<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">REPAIR TRANSACTIONS STATUS</h3>
				<table id="dynamic-table3" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>EMPLOYEE</th>
							<th>SENT FOR APP</th>
							<th>PENDING FOR APP</th>
							<th>APPROVED</th>
							<th>REJECTED</th>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>								
			</div>
		</div>
		
		<div class="col-xs-12 center">
			<div class="col-xs-6">
				<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">PURCHASE ORDERS STATUS</h3>
				<table id="dynamic-table4" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>EMPLOYEE</th>
							<th>SENT FOR APP</th>
							<th>PENDING FOR APP</th>
							<th>APPROVED</th>
							<th>REJECTED</th>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>								
			</div>
			
			<div class="col-xs-6">
				<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">INCHARGE TRANSACTIONS STATUS</h3>
				<table id="dynamic-table5" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>EMPLOYEE</th>
							<th>EXPENSE TYPE</th>
							<th>SENT FOR APP</th>
							<th>PENDING FOR APP</th>
							<th>APPROVED</th>
							<th>REJECTED</th>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>								
			</div>
		</div>
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
		<script src="../assets/js/bootbox.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});
			$("#branch").on("change",function(){paginate(1);});

			function paginate(page){
				$("#page").val(page);
				$("#paginate").submit();				
			}
			function modalTerminateEmployee(id, name, empid){
				$("#empname").val(name);
				$("#id").val(id);
				$("#empid").val(empid);
				return;
				
			}
			function modalBlockEmployee(id, name, empid){
				$("#empname1").val(name);
				$("#id1").val(id);
				$("#empid1").val(empid);
				return;
				
			}
			function modalBlockVehicle(id, vehreg){
				$("#id1").val(id);
				$("#vehreg").val(vehreg);
				return;
				
			}
			function modalSellVehicle(id, vehreg){
				$("#id2").val(id);
				$("#vehreg1").val(vehreg);
				return;
				
			}
			function modalRenewVehicle(id){
				$("#id1").val(id);
				return;
				
			}
			function modalRenewVehicle(id){
				$("#id1").val(id);
				return;				
			}
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
				}
			?>

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

			$('.number').keydown(function(e) {
				this.value = this.value.replace(/[^0-9.]/g, ''); 
				this.value = this.value.replace(/(\..*)\./g, '$1');
			});
			
			jQuery(function($) {		
				//initiate dataTables plugin
				var myTable1 = 
					$('#dynamic-table1')
					//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
	
					//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
			
					.DataTable( {
						bJQueryUI: true,
						"bPaginate": true, "bDestroy": true,
						"bDestroy": true,
						bInfo: true,
						"aoColumns": [
						  <?php $cnt=5; for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
						],
						"aaSorting": [],
						oLanguage: {
					        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
					    },
						"bProcessing": false,
				        "bServerSide": false,
						/*"ajax":{
			                url :"getDashboardDataTableData?name=vehiclerenewals", // json datasource
			                type: "post",  // method  , by default get
			                error: function(){  // error handling
			                    $(".employee-grid-error").html("");
			                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
			                    $("#employee-grid_processing").css("display","none");
			 
			                }
			            },*/
				
						"sScrollX" : "true",
						"bScrollCollapse": true,
				    } );

					var myTable2 = 
						$('#dynamic-table2')
						//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
		
						//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
				
						.DataTable( {
							bJQueryUI: true,
							"bPaginate": true, "bDestroy": true,
							"bDestroy": true,
							bInfo: true,
							"aoColumns": [
							  <?php $cnt=5; for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
							],
							"aaSorting": [],
							oLanguage: {
						        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
						    },
							"bProcessing": true,
					        "bServerSide": true,
							"ajax":{
				                url :"getDashboardDataTableData?name=feultransactionsstatus", // json datasource
				                type: "post",  // method  , by default get
				                error: function(){  // error handling
				                    $(".employee-grid-error").html("");
				                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
				                    $("#employee-grid_processing").css("display","none");
				 
				                }
				            },
					
							"sScrollX" : "true",
							"bScrollCollapse": true,
					    } );

			var myTable3 = 
				$('#dynamic-table3')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)

				//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
		
				.DataTable( {
					bJQueryUI: true,
					"bPaginate": true, "bDestroy": true,
					"bDestroy": true,
					bInfo: true,
					"aoColumns": [
					  <?php $cnt=5; for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
					],
					"aaSorting": [],
					oLanguage: {
				        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
				    },
					"bProcessing": true,
			        "bServerSide": true,
					"ajax":{
		                url :"getDashboardDataTableData?name=repairtransactionsstatus", // json datasource
		                type: "post",  // method  , by default get
		                error: function(){  // error handling
		                    $(".employee-grid-error").html("");
		                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
		                    $("#employee-grid_processing").css("display","none");
		 
		                }
		            },
			
					"sScrollX" : "true",
					"bScrollCollapse": true,
			    } );

				var myTable4 = 
					$('#dynamic-table4')
					//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
	
					//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
			
					.DataTable( {
						bJQueryUI: true,
						"bPaginate": true, "bDestroy": true,
						"bDestroy": true,
						bInfo: true,
						"aoColumns": [
						  <?php $cnt=5; for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
						],
						"aaSorting": [],
						oLanguage: {
					        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
					    },
						"bProcessing": true,
				        "bServerSide": true,
						"ajax":{
			                url :"getDashboardDataTableData?name=purchaseordersstatus", // json datasource
			                type: "post",  // method  , by default get
			                error: function(){  // error handling
			                    $(".employee-grid-error").html("");
			                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
			                    $("#employee-grid_processing").css("display","none");
			 
			                }
			            },
				
						"sScrollX" : "true",
						"bScrollCollapse": true,
				    } );

					var myTable5 = 
						$('#dynamic-table5')
						//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
		
						//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
				
						.DataTable( {
							bJQueryUI: true,
							"bPaginate": true, "bDestroy": true,
							"bDestroy": true,
							bInfo: true,
							"aoColumns": [
							  <?php $cnt=6; for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
							],
							"aaSorting": [],
							oLanguage: {
						        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
						    },
							"bProcessing": true,
					        "bServerSide": true,
							"ajax":{
				                url :"getDashboardDataTableData?name=inchargetransactionsstatus", // json datasource
				                type: "post",  // method  , by default get
				                error: function(){  // error handling
				                    $(".employee-grid-error").html("");
				                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
				                    $("#employee-grid_processing").css("display","none");
				 
				                }
				            },
					
							"sScrollX" : "true",
							"bScrollCollapse": true,
					    } );

						var myTable6 = 
							$('#dynamic-table6')
							//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
			
							//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
					
							.DataTable( {
								bJQueryUI: true,
								"bPaginate": true, "bDestroy": true,
								"bDestroy": true,
								bInfo: true,
								"aoColumns": [
								  <?php $cnt=5; for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
								],
								"aaSorting": [],
								oLanguage: {
							        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
							    },
								"bProcessing": true,
						        "bServerSide": true,
								"ajax":{
					                url :"getDashboardDataTableData?name=employeeleaves", // json datasource
					                type: "post",  // method  , by default get
					                error: function(){  // error handling
					                    $(".employee-grid-error").html("");
					                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
					                    $("#employee-grid_processing").css("display","none");
					 
					                }
					            },
						
								"sScrollX" : "true",
								"bScrollCollapse": true,
						    } );
			
				
				$('<button style="margin-top:-5px;" class="btn btn-minier btn-primary" id="refresh"><i style="margin-top:-2px; padding:6px; padding-right:5px;" class="ace-icon fa fa-refresh bigger-110"></i></button>').appendTo('div.dataTables_filter');
				$("#refresh").on("click",function(){ myTable.search( '', true ).draw(); });
			});
			
		</script>
	@stop