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
				white-space: nowrap;
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
			WORKFLOW
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<div class="col-xs-offset-1 col-xs-10">
				<?php $form_info = $values["form_info"]; ?>
				<?php $jobs = Session::get("jobs");?>
				<?php if(($form_info['action']=="addstate" && in_array(206, $jobs)) or 
						($form_info['action']=="addclient" && in_array(403, $jobs)) ||
						($form_info['action']=="adddepot" && in_array(405, $jobs))
					  ){ ?>
					@include("contracts.addlookupform",$form_info)
				<?php } ?>
			</div>
		</div>
				
		<div class="row " style="max-width: 98%;margin-left: 1%;">
		<div class="col-xs-offset-0 col-xs-12">
			<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
			<div class="clearfix">
				<div class="pull-left">
				</div>
				<div class="pull-right tableTools-container"></div>
			</div>
			
			<?php if(isset($form_info["table"]) && $form_info["table"]=="\InchargeTransaction"){?>
				<div class="row">
					<div class="col-xs-offset-3 col-xs-8">
						<div class="col-xs-3">
							<select name="inchargereporttype" id="inchargereporttype" class="formcontrol chosen-select">
								<option value="Income">INCOME</option>
								<option value="Expense">EXPENSE</option>
							</select>
						</div>
						<div class="col-xs-3">
							<select name="incharge" id="incharge" class="formcontrol chosen-select">
								<option value="0">All</option>
								<?php 
									$incharges = InchargeAccounts::where("inchargeaccounts.status","=","ACTIVE")
													->join("employee","employee.id","=","inchargeaccounts.empid")
													->select("empid","fullName","empCode")->get();
									foreach ($incharges as $incharge){
										echo '<option value="'.$incharge->empid.'">'.$incharge->fullName.'('.$incharge->empCode.')'.'</option>';
									}
								?>
							</select>
						</div>
						<div class="col-xs-3">
							<select name="logstatus" id="logstatus" class="formcontrol chosen-select">
								<option value="All">All</option>
								<option value="Requested">Pending for approval</option>
								<option value="Sent for Approval">Sent for Approval</option>
								<option value="Approved">Approved</option>
								<option value="Rejected">Rejected</option>
								<option value="Hold">Hold</option>
							</select>
						</div>
						<div class="col-xs-2">
							<button class="btn btn-xs btn-primary" id="getbtn">&nbsp;&nbsp;GET&nbsp;&nbsp;</button>
						</div>
					</div>
				</div>
			<?php } else {?>
				<div class="row">
					<div class="col-xs-offset-4 col-xs-8">
						<div class="col-xs-4">
							<select name="logstatus" id="logstatus" class="formcontrol chosen-select">
								<option value="All">All</option>
								<option value="Requested">Pending for Approval</option>
								<option value="Sent for Approval">Sent for Approval</option>
								<option value="Approved">Approved</option>
								<option value="Rejected">Rejected</option>
								<option value="Hold">Hold</option>
							</select>
						</div>
						<div class="col-xs-3">
							<button class="btn btn-xs btn-primary" id="getbtn">&nbsp;&nbsp;GET&nbsp;&nbsp;</button>
						</div>
					</div>
				</div>
			<?php }?>
			<form action="test" method="post" name="workflowform" id="workflowform" onsubmit="return false;">
			<input type="hidden" id="transactiontype" name="transactiontype" value="{{$form_info['transactiontype']}}">
			<input type="hidden" id="table" name="table" value="{{$form_info['table']}}">
			<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 5px;">CHANGE STATUS OF {{$values["bredcum"]}} WORK FLOW</h3>
			<div class="row">
				<div class="col-xs-offset-1 col-xs-10">
					<div class="col-xs-offset-4 col-xs-4">
						<select name="workflowstatus" id="workflowstatus" class="formcontrol chosen-select">
							<option value="Sent for Approval">Send for Approval</option>
							<?php $jobs = Session::get("jobs");?>
							<?php if(in_array(507, $jobs)){?>
								<option value="Approved">Approved</option>
								<option value="Rejected">Rejected</option>
								<option value="Hold">Hold</option>
							<?php } ?>
						</select>
					</div>
<!-- 					<div class="col-xs-6"> -->
<!-- 						<input type="text" class="formcontrol col-xs-12" name="remarks" placeholder="enter comments (if any)"> -->
<!-- 					</div> -->
					<div class="col-xs-2">
						<button class="btn btn-xs btn-primary" id="updatebtn" onclick="postData()">&nbsp;&nbsp;UPDATE&nbsp;&nbsp;</button>
					</div>
				</div>
			</div>
			<div class="table-header" style="margin-top: 10px;">
				Results for "{{$values['bredcum']}}"				 
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
			</form>
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
		<script src="../assets/js/chosen.jquery.js"></script>
		<script src="../assets/js/jquery.maskedinput.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});

			$("#getbtn").on("click",function(){
				logstatus = $("#logstatus").val();
				url = "getworkflowdatatabledata?name={{$form_info['transactiontype']}}&logstatus="+logstatus;

				inchargereporttype = $("#inchargereporttype").val();
				if(inchargereporttype != undefined && inchargereporttype !=""){
					url = url+"&inchargereporttype="+inchargereporttype;
				}
				incharge = $("#incharge").val();
				if(incharge != undefined && incharge !=""){
					url = url+"&incharge="+incharge;
				}
				myTable.ajax.url(url).load();
			})

			function postData(evt){
				var transactiontype = $("#transactiontype").val();
				if(transactiontype == "inchargetransactions"){
					var type = $("#inchargereporttype").val();
					if(type == "Income"){
						$("#table").val("IncomeTransaction");
					}
					else{
						$("#table").val("ExpenseTransaction");
					}
				}
			 	$.post( 
                   "workflowupdate",
                   $("#workflowform").serialize(),
                   function(data) {
                       json_obj = JSON.parse(data);
                       bootbox.alert(json_obj.message);
                       window.setTimeout(function(){location.reload();}, 2000 );
                   }
                );
				return false;
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

			$('.input-daterange').datepicker({autoclose:true,todayHighlight: true});

			$('.input-mask-phone').mask('(999) 999-9999');
			
			var myTable = null;
			jQuery(function($) {		
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
		                url :"getservicelogsdatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
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