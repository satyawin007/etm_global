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
			REPORTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group" style="width: 99%;">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;SEARCH BY
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						@include("reports.add3colform",$form_info)						
					</div>
				</div>
			</div>
		</div>	
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
						Results for {{$values['entries']}}				 
					</div>
					<!-- div.table-responsive -->
					<!-- div.dataTables_borderWrap -->
					<div>
						<table id="dynamic-table" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
								<?php 
									$theads = $values["theads1"];
									foreach ($theads as $thead){
										echo "<th>".strtoupper($thead)."</th>";
									}
								?>
								</tr>
							</thead>
							<tbody id="tbody">
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
			reporttype = "";

			function generateReport(){
				$("#reportfor").val("getreport") ;
				//reporttype = "ticket_corgos_summery";
				paginate(1);
			}

			function getReport2(){
				$("#reportfor").val("summary") ;
				//reporttype = "ticket_corgos_summery";
				paginate(1);
			}
			

			function changeDepot(val){
				$.ajax({
			      url: "getdepotsbyclientId?id="+val,
			      success: function(data) {
			    	  data = "<option value='0'> ALL </option>"+data;
			    	  $("#depot").html(data);
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
			      url: "getvehiclecontractinfo?clientid="+clientId+"&depotid="+depotId+"&type=vehicleids",
			      success: function(data) {
			    	  $("#vehicle").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			   //myTable1.ajax.url("getservicelogsdatatabledata?name=servicelogs&clientid="+clientId+"&depotid="+depotId).load();
			}

			function paginate(page){

				reporttype = $("#reportfor").val();
				if(clientname == ""){
					alert("select clientname");
					return;
				}
				depot = $("#depot").val();
				if(depot == ""){
					alert("select depot");
					return;
				}
				fdt = $("#fromdate").val();
				if(fdt == ""){
					alert("select daterange FROM date");
					return;
				}
				tdt = $("#todate").val();
				empname = $("#empname").val();
				if(tdt == ""){
					alert("select daterange TO date");
					return;
				}
				dt = fdt+" - "+tdt;	
				var form = $("#getreport");
				/* alert(fdt);
				alert(tdt);
				alert(empname); */

				$("#processing").show();
				$.ajax({
			        type:"POST",
			        url:form.attr("action"),//+"?fromdate="+fdt+"&todate="+tdt+"&empname="+empname,
			        data:form.serialize(),
			        success: function(response){
			           var json = JSON.parse(response);
			           //$("#totalexepenses").html(json["total_expenses"]);
			           var data1 = json;
			           var arr = [];
			           for(var i = 0; i < data1.length; i++) {
			        	    var parsed = data1[i];
			        	    var row = [];
			        	    for(var x in parsed){
			        	    	row.push(parsed[x]);
				            }
				            arr.push(row);
			        	}
			           if(reporttype == "getreport"){
							myTable1.clear().draw();
							myTable1.rows.add(arr); // Add new data
							myTable1.columns.adjust().draw(); // Redraw 
							$("#table1").show();
							$("#table2").hide();
							$("#table3").hide();
							//$("#table3").hide();
							/* if(refresh1 == 0){
								refresh1 = 1;
								myTable1.draw();
							}	 */	
			        	}
			           else if(reporttype == "vehiclesummary"){
			        	    myTable2.clear().draw();
							myTable2.rows.add(arr); // Add new data
							myTable2.columns.adjust().draw(); // Redraw 
							$("#table1").hide();
							$("#table2").show();
							$("#table3").hide();
			           }
			           else if(reporttype == "workingdaysvehiclesummary"){
			        	    myTable3.clear().draw();
							myTable3.rows.add(arr); // Add new data
							myTable3.columns.adjust().draw(); // Redraw 
							$("#table1").hide();
							$("#table2").hide();
							$("#table3").show();
			           }
						
						/* var data2 = json["data2"];
			            var arr1 = [];
			            for(var i = 0; i < data2.length; i++) {
			        	    var parsed = data2[i];
			        	    var row = [];
			        	    for(var x in parsed){
			        	    	row.push(parsed[x]);
				            }
				            arr1.push(row);
			        	}
						myTable2.clear().draw();
						myTable2.rows.add(arr1); // Add new data
						myTable2.columns.adjust().draw(); // Redraw 
						$("#table2").show(); */
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

			var myTable1 = null;
			var myTable2 = null;

			jQuery(function($) {
					//initiate dataTables plugin
					myTable1 = $('#dynamic-table')
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
								orientation: 'landscape',
								text : "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
								exportOptions: {
									columns: ':visible'
								}
							}
							
						], 
						bAutoWidth: false,
						"aoColumns": [
						  null, null, null, null, null, null, null,  null, null
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

					//initiate dataTables plugin
					myTable2 = $('#dynamic-table2')
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
						  null, null, null
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
						//$("#table2").hide();
					}, 1000);
				})
			
		</script>
	@stop