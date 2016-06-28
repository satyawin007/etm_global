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
			table {
				min-width: 100% !important;
			}
			panel-group .panel {
			    margin-bottom: 20px;
			    border-radius: 4px;
			}
			label{
				text-align: right;
				margin-top: 3px;
			}
			.ace-file-input {
			    text-align: left !important;
			}
			.chosen-container{
			  width: 100% !important;
			}
			#loading {
			  position: absolute;
			  top: 50%;
			  left: 50%;
			  width: 32px;
			  height: 32px;
			  /* 1/2 of the height and width of the actual gif */
			  margin: -16px 0 0 -16px;
			  z-index: 100;
			}
			.dt-buttons{
			  margin-top: 5px;
			}
		</style>
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../assets/css/chosen1.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.1.1/css/buttons.dataTables.min.css"/>
	@stop
		
	@stop
	
	@section('bredcum')	
		<small>
			ALERTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			EMPLOYEE INCREMENT ALERT
		</small>
	@stop

	@section('page_content')
		</div>	
		<div id="processing" class="modal-backdrop fade in"><div id = "loading" > <i  class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>	</div></div>
		
		
		<div class="row" >
			<div id="table1">
				<div class="row col-xs-12" style="padding-left:2%; padding-top: 1%">
					<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
					<div class="clearfix">
						<div id="tableTools-container1" class="pull-right tableTools-container"></div>
					</div>
					<div class="table-header" style="margin-top: 10px;">
						Results for <?php if(isset($values['reporttype'])){ echo '"'.strtoupper($values['reporttype'])." REPORT".'"';} ?>				 
					</div>
					<!-- div.table-responsive -->
					<!-- div.dataTables_borderWrap -->
					<div>
						<table id="dynamic-table1" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<td>EMPLOYEE ID</td>
									<td>EMPLOYEE NAME</td>
									<td>JOINING DATE</td>
									<td>ALERT DATE</td>
									<td>DAYS EXCEEDED</td>
								</tr>
							</thead>
							<tbody id="tbody1">
								<?php 
									$today = date("Y-m-d");
									$one_year_less_date = date('Y-m-d', strtotime('-12 month'));
									$last_year = date('Y', strtotime($one_year_less_date));
									$entities = Employee::where("status","=","ACTIVE")->get();
									$count = 0;
									foreach ($entities as $entity){
										
										$joindate_day = date('d', strtotime($entity->joiningDate));
										$joindate_month = date('m', strtotime($entity->joiningDate));
										$joiningdate_gen = date("Y")."-".$joindate_month."-".$joindate_day;
										
										$empsalarydet = \SalaryDetails::where("empId","=",$entity->id)->first();
										if(count($empsalarydet)>0){
											$incrementdate = $empsalarydet->increamentDate;
	 										if($joiningdate_gen < $incrementdate){
	 											continue;
	 										}
										}
										$edit = '&nbsp;&nbsp;<a class="btn btn-minier btn-primary" href="#edit" onclick="setemp('.$entity->id.')" data-toggle="modal">PAY INCREMENT</a>';
										$one_year_less_join_date = $last_year."-".$joindate_month."-".$joindate_day;
										$date1=date_create($one_year_less_join_date);
										$date2=date_create($today);
										$diff=date_diff($date1,$date2);
										$date3=date_create($entity->joiningDate);
										$date4=date_create($today);
										$diff1=date_diff($date3,$date4);
										if($diff->format("%a") >= 365 && $diff1->format("%a") >= 365){
											echo "<tr><td>".$entity->empCode."</td>".
													 "<td>".$entity->fullName."</td>".
													 "<td>".date("d-m-Y",strtotime($entity->joiningDate))."</td>".
													 "<td>".$joindate_day."-".$joindate_month."-".date("Y")."</td>";
											if(($diff->format("%a")-365)<5){
												echo "<td>"."<span class='label label-success'>".($diff->format("%a")-365)."</span>".$edit."</td></tr>";
											}
											else if(($diff->format("%a")-365)<=10){
												echo "<td>"."<span class='label label-warning'>".($diff->format("%a")-365)."</span>".$edit."</td></tr>";
											}
											else if(($diff->format("%a")-365)>10){
												echo "<td>"."<span class='label label-inverse'>".($diff->format("%a")-365)."</span>".$edit."</td></tr>";
											}
										}
									}
								?>
							</tbody>
						</table>								
					</div>
				</div>					
			</div>
		</div>

		<div id="edit" class="modal" tabindex="-1">
			<div class="modal-dialog" style="width: 50%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="blue bigger">Please fill the following form fields</h4>
					</div>
	
					<div class="modal-body" id="modal_body">
						<form name="modalform" id="modalform" class="form-horizontal" action="addincreament" onsubmit="return false">	
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> INCREAMENT DATE </label>
									<div class="col-xs-6">
										<input type="text" id="increamentdate" name="increamentdate" class="form-control date-picker">
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> INCREAMENT AMOUNT </label>
									<div class="col-xs-6">
										<input type="text" id="increamentamount" name="increamentamount" class="form-control">
									</div>			
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> ARREAR PAID </label>
									<div class="radio inline">
										<label>
											<input name="arrearpaid" id="arrearpaid" value="YES" type="radio" class="ace" onclick="showariaramount()">
											<span class="lbl">&nbsp;YES &nbsp;&nbsp;</span>
										</label>
									</div>	
									<div class="radio inline">
										<label>
											<input name="arrearpaid" id="arrearpaid" value="NO" type="radio" class="ace" checked onclick="hideariaramount()">
											<span class="lbl">&nbsp;NO &nbsp;&nbsp;</span>
										</label>
									</div>			
								</div>
								<div class="form-group" id="div_ariaramount">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> ARREAR AMOUNT </label>
									<div class="col-xs-6">
										<input type="text" id="ariaramount" name="ariaramount" class="form-control">
									</div>			
								</div>
								<input type="hidden" id="empid" name="empid" class="form-control">
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1">  </label>
									<div class="col-xs-6">
										<button class="btn btn-sm btn-primary" id="submitbutton">
											SUBMIT
										</button>
									</div>			
								</div>
								
						</form>
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
		
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/js/dataTables/jquery.dataTables.bootstrap.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/dataTables.buttons.js"></script>
		<script src="../assets/js/dataTables/extensions/buttons/buttons.print.js"></script>
		<script type="text/javascript" src="../assets/js/dataTables/js/jszip.min.js"></script>
		<script type="text/javascript" src="../assets/js/dataTables/js/pdfmake.min.js"></script>
		<script type="text/javascript" src="../assets/js/dataTables/js/vfs_fonts.js"></script>
		<script type="text/javascript" src="../assets/js/dataTables/js/buttons.html5.min.js"></script>
		<script type="text/javascript" src="../assets/js/dataTables/js/buttons.colVis.min.js"></script>
		<script src="../assets/js/date-time/moment.js"></script>
		<script src="../assets/js/date-time/daterangepicker.js"></script>		
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/bootbox.js"></script>
		<script src="../assets/js/chosen.jquery.js"></script>
		<script src="../assets/js/autosize.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#processing").hide();
			$("#div_ariaramount").hide();
			reporttype = "";

			$("#submitbutton").on("click",function(){
				var form = $("#modalform");
				$.ajax({
			        type:"POST",
			        url:form.attr("action"),
			        data:form.serialize(),
			        success: function(response){
			        	if(response == "Operation completed Successfully"){
					    	 bootbox.alert(response, function(result) {});
					    	 setTimeout(location.reload(), 3000);
					    }
			        	else{
			        		bootbox.alert(response, function(result) {});
			        	}
			        }
			    });
			});

			function generateReport(){
				reporttype = "ticket_corgos_summery";
				paginate(1);
			}

			function showariaramount(){
				$("#div_ariaramount").show();
			}

			function hideariaramount(){
				$("#ariaramount").val("");
				$("#div_ariaramount").hide();
			}

			function setemp(id){
				$("#empid").val(id);
			}

			function showSelectionType(val){
				     
				if(val=="balanceSheetNoDt" || val=="payment" || val=="balanceSheet" || val=="tracking"){
					$("#fuelstationid").show();
					$("#vehicleid").hide();
					$("#driverid").hide();
				}
				else if(val=="vehicleReport"){
					$("#fuelstationid").hide();
					$("#vehicleid").show();
					$("#driverid").hide();
				}
				else if(val=="employeeReport"){
					$("#fuelstationid").hide();
					$("#vehicleid").hide();
					$("#driverid").show();
				}
			}

			function paginate(page){
				reporttype = $("#employee").val();
				if(reporttype == ""){
					alert("select employee");
					return;
				}
				fdt = $("#fromdate").val();
				if(fdt == ""){
					alert("select daterange FROM date");
					return;
				}
				tdt = $("#fromdate").val();
				if(tdt == ""){
					alert("select daterange TO date");
					return;
				}
				dt = fdt+" - "+tdt;	
				var form=$("#getreport");	

				$("#processing").show();
				$.ajax({
			        type:"POST",
			        url:form.attr("action"),
			        data:form.serialize(),
			        success: function(response){
			           //alert(response);  
			           var json = JSON.parse(response);
			           var arr = [];
			           for(var i = 0; i < json.length; i++) {
			        	    var parsed = json[i];
			        	    var row = [];
			        	    for(var x in parsed){
			        	    	row.push(parsed[x]);
				            }
				            arr.push(row);
			        	}
						myTable1.clear().draw();
						myTable1.rows.add(arr); // Add new data
						myTable1.columns.adjust().draw(); // Redraw 
						$("#table1").show();
						$("#processing").hide();
			        }
			    });
			}

			function modalEditLookupValue(id, value){
				$("#value1").val(value);
				$("#id1").val(id);
				return;				
			}
			

			function modalEditTransaction(id){
				//$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				url = "edittransaction?type="+transtype+"&id="+id;
				var ifr=$('<iframe />', {
		            id:'MainPopupIframe',
		            src:url,
		            style:'seamless="seamless" scrolling="no" display:none;width:100%;height:423px; border:0px solid',
		            load:function(){
		                $(this).show();
		            }
		        });
	    	    $("#modal_body").html(ifr);
			}


			$("#provider").on("change",function(){
				val = $("#provider option:selected").html();
				window.location.replace('serviceproviders?provider='+val);
			});

			$('.number').keydown(function(e) {
				this.value = this.value.replace(/[^0-9.]/g, ''); 
				this.value = this.value.replace(/(\..*)\./g, '$1');
			});

			$('.file').ace_file_input({
				no_file:'No File ...',
				btn_choose:'Choose',
				btn_change:'Change',
				droppable:false,
				onchange:null,
				thumbnail:false //| true | large
				//whitelist:'gif|png|jpg|jpeg'
				//blacklist:'exe|php'
				//onchange:''
				//
			});
			//pre-show a file name, for example a previously selected file
			//$('#id-input-file-1').ace_file_input('show_file_list', ['myfile.txt'])
		
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

			var myTable1 = null;
			var myTable2 = null;

			jQuery(function($) {
					//initiate dataTables plugin
					myTable1 = $('#dynamic-table1')
					//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
					.DataTable( {
						dom: 'Bfrtip',
						buttons: [
							{
								extend:'colvis',
								text : "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>"
							},
							{
								extend: 'excelHtml5',
								text : "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
								exportOptions: {
									columns: ':visible'
								}
							},
							{
								extend: 'pdfHtml5',
								text : "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
								exportOptions: {
									columns: ':visible'
								}
							}
							
						], 
						bAutoWidth: false,
						"aoColumns": [
						  null, null, null,  null, null
						],
						"aaSorting": [],
						//"sScrollY": "500px",
						//"bPaginate": false,
						"sScrollX" : "true",
						//"sScrollX": "300px",
						//"sScrollXInner": "120%",
						"bScrollCollapse": true,
						select: {
							style: 'multi'
						}
				    } );
					
					////
					/* setTimeout(function() {
						$("#table1").hide();
					}, 500); */
				})
			
		</script>
	@stop