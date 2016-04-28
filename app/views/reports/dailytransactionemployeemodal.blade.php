@extends('masters.noheadermaster')
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
			REPORTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<?php $form_info = $values["form_info"]; ?>
		<div id="processing" class="modal-backdrop fade in"><div id = "loading" > <i  class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>	</div></div>
		
		<form name="getreport" id="getreport">
			<input name="branch" type="hidden" value="{{$values['branch']}}" />
			<input name="fromdate" type="hidden" value="{{$values['fromdate']}}" />
			<input name="todate" type="hidden" value="{{$values['todate']}}" />
		</form>
		
		<div class="row" >
			<div id="table1">
				<div class="row col-xs-12" style="padding-left:2%; padding-top: 1%">
					<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
					<div class="clearfix">
						<div id="tableTools-container" class="pull-right tableTools-container"></div>
					</div>
					<div class="table-header" style="margin-top: 10px;">
						Results for <?php if(isset($values['type'])){ echo '"'.strtoupper($values['type'])." REPORT".'"';} ?>				 
					</div>
					<!-- div.table-responsive -->
					<!-- div.dataTables_borderWrap -->
					<div>
						<table id="dynamic-table" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<td>BRANCH</td>
									<td>TICKETS</td>
									<td>TICKET CANCEL</td>
									<td>CARGOS</td>
									<td>CARGOS CANCEL</td>
									<td>CARGOS SIMPLY</td>
									<td>TOTAL</td>
								</tr>
							</thead>
							<tbody id="tbody1">
							</tbody>
						</table>								
					</div>
				</div>					
			</div>
			<div id="table2">
				<div class="row col-xs-12" style="padding-left:2%; padding-top: 1%">
					<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
					<div class="clearfix">
						<div id="tableTools-container1" class="pull-right tableTools-container"></div>
					</div>
					<div class="table-header" style="margin-top: 10px;">
						Results for <?php if(isset($values['type'])){ echo '"'.strtoupper($values['type'])." REPORT".'"';} ?>				 
					</div>
					<!-- div.table-responsive -->
					<!-- div.dataTables_borderWrap -->
					<div>
						<table id="dynamic-table1" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<td>BRANCH</td>
									<td>TRANSACTION TYPE</td>
									<td>DATE</td>
									<td>AMOUNT</td>
									<td>PURPOSE</td>
									<td>PAID TO/REC. FROM</td>
									<td>COMMENTS</td>
									<td>CREATED BY</td>
								</tr>
							</thead>
							<tbody id="tbody1">
							</tbody>
						</table>								
					</div>
				</div>					
			</div>
		</div>

		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal)
		<?php }} ?>
		
		<div id="edit" class="modal" tabindex="-1">
			<div class="modal-dialog" style="width: 80%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="blue bigger">Please fill the following form fields</h4>
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
			var refresh1 = 0;
			var refresh2 = 0;

			paginate(1);
			function paginate(page){
				reporttype = "ticket_corgos_summery";
				branch = $("#branch").val();
				if(branch == ""){
					alert("select branch");
					return;
				}
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
			        	if(reporttype == "ticket_corgos_summery"){
							myTable.clear();
							myTable.rows.add(arr); // Add new data
							myTable.draw(); // Redraw 
							$("#table1").show();
							$("#table2").hide();	
							if(refresh1 == 0){
								myTable.draw();
								refresh1 = 1;
							}	
			        	}
			        	else if(reporttype == "txn_details"){
							myTable1.clear().draw();
							myTable1.rows.add(arr); // Add new data
							myTable1.columns.adjust().draw(); // Redraw theDataTable
							$("#table1").hide();
							$("#table2").show();
							if(refresh2 == 0){
								myTable1.draw();
								refresh2 = 1;
							}			
			        	}
						$("#processing").hide();
			        }
			    });
			}

			function modalEditLookupValue(id, value){
				$("#value1").val(value);
				$("#id1").val(id);
				return;				
			}
			

			function modalGetInfo(branch, fromdt, todt, empid, reportfor){
				//$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				url = "edittransaction?type=fuel&id="+1;
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

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
				//$("#{{$form_info['name']}}").submit();
			});

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

			var myTable = null;
			var myTable1 = null;

			jQuery(function($) {
					//initiate dataTables plugin
					myTable = 
					$('#dynamic-table')
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
						  { "bSortable": false },
						  null, null,null, null, null,
						  { "bSortable": false }
						],
						"aaSorting": [],
						//"sScrollY": "500px",
						//"bPaginate": false,
						//"sScrollX" :true,
						"sScrollX": "300px",
						//"sScrollXInner": "120%",
						"bScrollCollapse": true,
						select: {
							style: 'multi'
						}
				    } );
					
					//initiate dataTables plugin
					myTable1 = 
					$('#dynamic-table1')
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
								 title: 'reportTitle',
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
						  { "bSortable": false },
						  null, null, null, null, null, null,
						  { "bSortable": false }
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
					setTimeout(function() {
						//$("#table1").hide();
						$("#table2").hide();
					}, 50);
				})
			
		</script>
	@stop