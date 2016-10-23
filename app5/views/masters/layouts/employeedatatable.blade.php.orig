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
		</style>
		
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
	@stop
	
	@section('bredcum')	
		<small>
			ADMINISTRATION
			<i class="ace-icon fa fa-angle-double-right"></i>
			MASTERS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')	
		<div class="col-xs-12">
			<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
			<div class="clearfix">
				<div class="pull-left">
					<form action="{{$values['form_action']}}" name="paginate" id="paginate">
					<label>ENTRIES </label>
					<select class="form-control-inline" id="entries" style="height: 33px; padding-top: 0px;" name="entries" onChage="paginate(1)">
						<option <?php if($values['entries']=="10") echo 'selected="selected"' ?>  value="10">10</option>
						<option <?php if($values['entries']=="30") echo 'selected="selected"' ?> value="30">30</option>
						<option <?php if($values['entries']=="50") echo 'selected="selected"' ?> value="50">50</option>
						<option <?php if($values['entries']=="100") echo 'selected="selected"' ?> value="100">100</option>
					</select> &nbsp; &nbsp;
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
				<div style="float:right;padding-right: 15px;padding-top: 6px;"><a style="color: white;" href="{{$values['home_url']}}"><i class="ace-icon fa fa-home bigger-200"></i></a> &nbsp; &nbsp; &nbsp; <a style="color: white;"  href="{{$values['add_url']}}"><i class="ace-icon fa fa-plus-circle bigger-200"></i></a></div>				
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

					<tbody>
					<?php
						$entries = $values['entries'];
						$branch = $values['branch'];
						$page = $values['page'];
						$entities = $values['entities'];//Employee::paginate($entries);
						//$entities = 
						$total = $values["total"];					
						foreach($entities as $entity){ 
					?>
						<tr>
							<?php 
								$tds = $values['tds'];
								foreach($tds as $td){
									echo "<td>$entity[$td]</td>";
								}
								$actions = $values['actions'];
								echo "<td>";
								foreach($actions as $action){
									if($action["type"] == "modal"){
										$jsfields = $action["jsdata"];
										$jsdata = "";
										$i=0;
										/*for($i=0; $i<(count($jsfields)-1); $i++){
											$jsdata = $jsdata." '".$entity->$jsfields[$i]."', ";
										}
										*/
										$jsdata = $jsdata." '".$entity->id."', ";
										$jsdata = $jsdata." '".$entity->fullName."', ";
										$jsdata = $jsdata." '".$entity->empCode;
										echo "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."' data-toggle='modal' onClick=\"".$action['js'].$jsdata."')\">".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
									}
									else {
										echo "<a class='btn btn-minier btn-".$action["css"]."' href='".$action['url']."&id=".$entity['id']."'>".strtoupper($action["text"])."</a>&nbsp; &nbsp;" ;
									}
								}
								echo "</td>";
							?>
						</tr>
						<?php } ?>
					</tbody>					
				</table>								
			</div>
			<?php 
				$page=$page-1; $start = ($entries*$page);
				$end =$entries+$start;
				if(!($end<$total)){
					$end = $total;
				}
			?>
			<div class="row" style="margin-top: 10px;"><div style="float:left;font-size: 14px;margin-left: 10px;">{{$start+1}} TO {{$end}} OF {{$total}} RECORDS </div><div style="align:right;float: ri;float: right;margin-right: 12px;"><?php  echo $entities->links();  ?></div></div>
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
			function modalSellVehicle(id){
				$("#id1").val(id);
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
					echo "bootbox.confirm('".Session::pull('message')."', function(result) {});";
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
			
			jQuery(function($) {
				//initiate dataTables plugin
				var myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)

				//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
		
				.DataTable( {
					bJQueryUI: true,
					"bPaginate": false,
					bInfo: false,
					"aoColumns": [
					  <?php $cnt=count($values["tds"]); for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
					  { "bSortable": false }
					],
					"aaSorting": [],
					
					
					//"bProcessing": true,
			        //"bServerSide": true,
			        //"sAjaxSource": "http://127.0.0.1/table.php"	,
			
					//,
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
			});
			
		</script>
	@stop