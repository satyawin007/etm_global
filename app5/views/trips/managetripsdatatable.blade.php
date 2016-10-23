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
			label{
				text-align: right;
				margin-top: 8px;
			}
		</style>
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
	@stop
	
	@section('bredcum')	
		<small>
			TRIPS & SERVICES
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<?php $jobs = Session::get("jobs"); ?>
		<?php if($values['triptype'] == "DAILY"){ ?>
		<div class="col-xs-12">
			<div class="col-xs-offset-4 col-xs-8 ccordion-style1 panel-group">
				<?php if(in_array(309, $jobs)){ ?>
				<a class="btn btn-sm btn-primary" href="dailytrips">CREATE/ADD SERVICES</a> &nbsp;&nbsp;
				<?php } if(in_array(310, $jobs)){ ?>
				<a class="btn btn-sm  btn-inverse" href="managetrips?triptype={{$values['triptype']}}">MANAGE TRIPS</a> &nbsp;&nbsp;
				<?php }?>
			</div>
		<?php } if($values['triptype'] == "LOCAL"){ ?>
		<div class="col-xs-12">
			<div class="col-xs-offset-4 col-xs-8 ccordion-style1 panel-group">
				<?php if(in_array(312, $jobs)){ ?>
				<a class="btn btn-sm btn-primary" href="addlocaltrip">CREATE NEW BOOKING</a> &nbsp;&nbsp;
				<?php } if(in_array(317, $jobs)){ ?>
				<a class="btn btn-sm  btn-inverse" href="managetrips?triptype={{$values['triptype']}}">MANAGE  BOOKING</a> &nbsp;&nbsp;
				<?php }?>
			</div>
		<?php } ?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;TRIP SERVICE DATE RANGE
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; 
							if($values['triptype'] == "DAILY"){ ?>
							<div class="col-xs-offset-2 col-xs-8" style="margin-top: 1%; margin-bottom: 1%">
								<div class="col-xs-12">
									<div class="form-group">
										<label class="col-xs-3 control-label no-padding-right" for="form-field-1">DATE RANGE<span style="color:red;">*</span></label>
										<div class="col-xs-6">
											<div class="input-daterange input-group">
												<input type="text" style="padding-top: 15px;padding-bottom: 18px;" id="fromdate" required="required" name="fromdate" <?php if(isset($values["fromdate"])) echo " value=".$values["fromdate"]." "; ?> class="input-sm form-control"/>
												<span class="input-group-addon">
													<i class="fa fa-exchange"></i>
												</span>
												<input type="text" style="padding-top: 15px;padding-bottom: 18px;" class="input-sm form-control" id="todate" required="required" <?php if(isset($values["fromdate"])) echo " value=".$values["todate"]." "; ?>  name="todate"/>
											</div>
										</div>
										<div class="col-xs-3" id="verify">
											<input type="button" class="btn btn-sm btn-primary" value="GET" onclick="verifyDate()"/>
										</div>	
									</div>
								</div>
							</div>
						<?php } if($values['triptype'] == "LOCAL"){ ?>
							<div class="col-xs-offset-0 col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
								<div class="col-xs-12">
									<div class="form-group">
										<label class="col-xs-3 control-label no-padding-right" for="form-field-1">DATE RANGE<span style="color:red;">*</span></label>
										<div class="col-xs-4">
											<div class="input-daterange input-group">
												<input type="text" style="padding-top: 15px;padding-bottom: 18px;" id="fromdate" required="required" name="fromdate" <?php if(isset($values["fromdate"])) echo " value=".$values["fromdate"]." "; ?> class="input-sm form-control"/>
												<span class="input-group-addon">
													<i class="fa fa-exchange"></i>
												</span>
												<input type="text" style="padding-top: 15px;padding-bottom: 18px;" class="input-sm form-control" id="todate" required="required" <?php if(isset($values["fromdate"])) echo " value=".$values["todate"]." "; ?>  name="todate"/>
											</div>
										</div>
										<div class="col-xs-2">
											<select class="form-control" name="bookingtype" id="bookingtype">
												<option selected value="activebookings" >ACTIVE BOOKINGS</option>
												<option value="cancelledbookings" >CANCELLED BOOKINGS</option>
											</select>
										</div>
										<div class="col-xs-2" id="verify">
											<input type="button" class="btn btn-sm btn-primary" value="GET" onclick="verifyDate()"/>
										</div>	
									</div>
								</div>	
							</div>
						<?php }?>
					</div>
				</div>
			</div>
			</div>
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
			<div style="padding-left:1%; padding-top: 1%; max-width:99%">
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
			</div>
		</div>

		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal);
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
		<script src="../assets/js/bootbox.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			function verifyDate(){
				fdt = $("#fromdate").val();
				if(fdt == ""){
					alert("select FROM date");
					return;
				}
				tdt = $("#todate").val();
				if(tdt == ""){
					alert("select TO date");
					return;
				}
				btype = $("#bookingtype").val();
				$('#verify').hide();
				dt = fdt+" - "+tdt;
				if(btype == "undefined"){
					location.replace("managetrips?triptype={{$values['triptype']}}&daterange="+dt);
				}
				else{
					location.replace("managetrips?triptype={{$values['triptype']}}&daterange="+dt+"&bookingtype="+btype);
				}
			}
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
				}
			?>

			function deletebooking(id) {
				bootbox.confirm("Are you sure, you want to delete this booking?", function(result) {
					if(result) {
						$.ajax({
					      url: "deletebooking?bookingid="+id,
					      success: function(data) {
						      if(data=="success"){
						    	  bootbox.confirm('BOOKING SUCCESSFULLY DELETED!', function(result) {});
						      }
						      else{
						    	  bootbox.confirm('BOOKING COULD NOT BE DELETED!', function(result) {});
						      }
					      },
					      type: 'GET'
					   });	
						location.reload();	
					}
				});
			};

			function cancelbooking(id) {
				bootbox.confirm("Are you sure, you want to cancel this booking?", function(result) {
					if(result) {
						$.ajax({
					      url: "cancellocaltrip?id="+id,
					      success: function(data) {
						      if(data=="success"){
						    	  bootbox.confirm('BOOKING SUCCESSFULLY CANCELLED!', function(result) {});
						      }
						      else{
						    	  bootbox.confirm('BOOKING COULD NOT BE CANCELLED!', function(result) {});
						      }
					      },
					      type: 'GET'
					   });	
						location.reload();	
					}
				});
			};

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

			$('.number').keydown(function(e) {
				this.value = this.value.replace(/[^0-9.]/g, ''); 
				this.value = this.value.replace(/(\..*)\./g, '$1');
			});
			
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
		                url :"gettripsdatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
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