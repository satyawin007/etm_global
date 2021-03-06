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
			TRANSACTIONS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group">			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
							<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
							&nbsp;ADD TRANSACTION
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="TEST">
					<div class="panel-body" style="padding: 0px">
						<?php $form_info = $values["form_info"]; ?>
						@include("transactions.add3colform",$form_info)						
					</div>
				</div>
			</div>
		</div>	
		</div>		
		
		<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: -10px;">MANAGE TRANSACTIONS</h3>		
		<div class="row" >
			<div>
				<div class="col-xs-offset-1 col-xs-10" style="margin-top: 0%; margin-bottom: 1%">
					<div class="col-xs-12 row" style="margin-top: 15px; margin-bottom: 0px">
						<div class="form-group">
							<label class="col-xs-3 control-label no-padding-right" for="form-field-1" style="margin-top: 15px"> TRANSACTION TYPE :  </label>
							<div class="col-xs-9">
								<div class="control-group row">
									<div class="radio inline">
										<label>
											<input name="form-field-radio" name="trantype1"  type="radio" value="income" class="ace" onclick="setTranType1Value(this.value)">
											<span class="lbl"> &nbsp;INCOME &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
										</label>
									</div>
		
									<div class="radio inline">
										<label>
											<input name="form-field-radio" name="trantype1"  type="radio" value="expense" class="ace"  onclick="setTranType1Value(this.value)">
											<span class="lbl"> &nbsp;EXPENSE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
										</label>
									</div>
		
									<div class="radio inline">
										<label>
											<input name="form-field-radio" name="trantype1" type="radio" value="fuel" class="ace" onclick="setTranType1Value(this.value)">
											<span class="lbl"> &nbsp;FUEL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
										</label>
									</div>
								</div>
							</div>			
						</div>
					</div>
				</div>
								
				<div class="row col-xs-12" style="padding:1%; padding-top: 0%">
					<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
					<div class="clearfix">
						<div class="col-xs-12">
							<form action="{{$values['form_action']}}" name="paginate" id="paginate">
							<div class="col-xs-2">
								<div class="form-group">
								<label>ENTRIES </label>
								<select class="form-control-inline" id="entries" style="height: 33px; padding-top: 0px;" name="entries" onChage="paginate(1)">
									<option <?php if($values['entries']=="10") echo 'selected="selected"' ?>  value="10">10</option>
									<option <?php if($values['entries']=="30") echo 'selected="selected"' ?> value="30">30</option>
									<option <?php if($values['entries']=="50") echo 'selected="selected"' ?> value="50">50</option>
									<option <?php if($values['entries']=="100") echo 'selected="selected"' ?> value="100">100</option>
								</select> &nbsp; &nbsp;
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label style="" class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper("date");  echo '<span style="color:red;">*</span>'; ?> </label>
									<div class="col-xs-8">
										<input type="text" id="daterange" required="required" name="daterange" class="form-control date-range-picker" />
									</div>			
								</div>
							</div>	
							<div class="col-xs-5">
								<div class="form-group">
									<?php 
										$branches =  \OfficeBranch::All();
										$branches_arr = array();
										foreach ($branches as $branch){
											$branches_arr[$branch->id] = $branch->name;
										}
										if(!isset($values['branch1'])){
											$values["branch1"] = 0;
										}
									?>
									<?php $form_field = array("name"=>"branch1", "value"=>$values["branch1"], "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr); ?>
									<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
									<div class="col-xs-8">
										<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
											<option value="">-- {{$form_field['name']}} --</option>
											<?php 
												foreach($form_field["options"] as $key => $value){
													if(isset($form_field['value']) && $form_field['value']==$key) { 
														echo "<option selected='selected' value='$key'>$value</option>";
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
							<div class="col-xs-1" style="margin-top: 0px; margin-bottom: -10px">
								<div class="form-group">
									<label class="col-xs-0 control-label no-padding-right" for="form-field-1"> </label>
									<div class="col-xs-5">
										<input class="btn btn-sm btn-primary" type="button" value="GET" onclick="test()"/>
									</div>			
								</div>
							</div>
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
						Results for <?php if(isset($values['transtype'])){ echo '"'.strtoupper($values['transtype'])." TRANCTIONS".'"';} ?>				 
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
												for($i=0; $i<(count($jsfields)-1); $i++){
													$jsdata = $jsdata." '".$entity->$jsfields[$i]."', ";
												}
												$jsdata = $jsdata." '".$entity->$jsfields[$i];
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
					<div class="row" style="margin-top: 10px;"><div style="float:left;font-size: 14px;margin-left: 10px;">{{$start+1}} TO {{$end}} OF {{$total}} RECORDS </div><div style="align:right;float: ri;float: right;margin-right: 12px;"><?php  if($total>0) echo $entities->links();  ?></div></div>
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
		<script src="../assets/js/chosen.jquery.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});
			$("#branch").on("change",function(){$('#trantypebody').hide();  $("#transactionform").hide(); $('#incomebody').hide(); $('#expensebody').hide(); $('#verify').show();});
			$("#date").on("change",function(){$('#trantypebody').hide(); $("#transactionform").hide(); $('#incomebody').hide(); $('#expensebody').hide(); $('#verify').show();});
			
			transtype = "";

			function test(){;
				paginate(1);
			}

			function setTranType1Value(val){
				transtype = val;
			}

			function paginate(page){
				if(transtype == ""){
					alert("select transaction type");
					return;
				}	
				branch = $("#branch1").val();
				if(branch == ""){
					alert("select branch");
					return;
				}				
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='transtype'; 
				myin.value=transtype; 
				document.getElementById('paginate').appendChild(myin); 
				var myin = document.createElement("input"); 
				$("#page").val(page);
				$("#paginate").submit();				
			}

			function modalEditLookupValue(id, value){
				$("#value1").val(value);
				$("#id1").val(id);
				return;				
			}
			
			function verifyDate(){
				branch = $("#branch").val();
				dt = $("#date").val();
				if(branch == ""){
					alert("select branch office");
					return;
				}
				if(dt == ""){
					alert("select date");
					return;
				}
				$('#verify').hide();
				$('#trantypebody').show();
					
			}
			function showTranType(val){
				$("#formbody").hide();
				$("#addfields").hide();
				transtype = val;
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='transtype'; 
				myin.value=val;
				document.getElementById('transactionform').appendChild(myin); 

				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='branch'; 
				myin.value=$("#branch").val();
				document.getElementById('transactionform').appendChild(myin); 
				
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='date'; 
				myin.value=$("#date").val();
				document.getElementById('transactionform').appendChild(myin); 	
							
				if(val == "income"){
					$("#formbody").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
					$("#formbody").show();						
					
					$('#incomebody').show();
					$('#expensebody').hide();
					$.ajax({
				      url: "gettransactionfields?typeId=15",
				      success: function(data) {
				    	  $("#formbody").html(data);
				    	  $('.date-picker').datepicker({
							autoclose: true,
							todayHighlight: true
						  });
				    	  $('.number').keydown(function(e) {
							 this.value = this.value.replace(/[^0-9.]/g, ''); 
							 this.value = this.value.replace(/(\..*)\./g, '$1');
						  });
				    	  $('.chosen-select').chosen();
				    	  $("#formbody").show();
				    	  
				      },
				      type: 'GET'
				   });
				}
				else if(val == "expense"){					
					$("#formbody").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
					$("#formbody").show();					

					$('#expensebody').show();
					$('#incomebody').hide();
					$.ajax({
				      url: "gettransactionfields",
				      success: function(data) {
				    	  $("#formbody").html(data);
				    	  $('.date-picker').datepicker({
							autoclose: true,
							todayHighlight: true
						  });
				    	  $('.number').keydown(function(e) {
							 this.value = this.value.replace(/[^0-9.]/g, ''); 
							 this.value = this.value.replace(/(\..*)\./g, '$1');
						  });
				    	  $('.chosen-select').chosen();
				    	  $("#formbody").show();				    	  
				      },
				      type: 'GET'
				   });
				}	
				else if(val == "fuel"){	
					$('#transactionform').show();				
					$('#expensebody').hide();
					$('#incomebody').hide();
					$("#formbody").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
					$("#formbody").show();					
					$.ajax({
				      url: "getfueltransactionfields",
				      success: function(data) {
				    	  $("#formbody").html(data);
				    	  $('.date-picker').datepicker({
							autoclose: true,
							todayHighlight: true
						  });
				    	  $('.number').keydown(function(e) {
							 this.value = this.value.replace(/[^0-9.]/g, ''); 
							 this.value = this.value.replace(/(\..*)\./g, '$1');
						  });
						  $(".chosen-select").chosen();
						  $("#paymenttype").attr("disabled",true);
				    	  $("#formbody").show();
				    	  
				      },
				      type: 'GET'
				   });
				}	
			}
			function showForm(val){
				$('#addfields').hide(); 
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='branch'; 
				myin.value=$("#branch").val(); 
				document.getElementById('transactionform').appendChild(myin);

				if(transtype == "income"){ 
					var myin = document.createElement("input"); 
					myin.type='hidden'; 
					myin.name='type'; 
					myin.value=$("#income").val(); 
					document.getElementById('transactionform').appendChild(myin);
				}
				if(transtype == "expense"){ 
					var myin = document.createElement("input"); 
					myin.type='hidden'; 
					myin.name='type'; 
					myin.value=$("#expense").val(); 
					document.getElementById('transactionform').appendChild(myin);
				} 
				if(transtype == "fuel"){ 
					var myin = document.createElement("input"); 
					myin.type='hidden'; 
					myin.name='type'; 
					myin.value="fuel"; 
					document.getElementById('transactionform').appendChild(myin);
				} 
				 
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='date1'; 
				myin.value=$("#date").val();
				document.getElementById('transactionform').appendChild(myin);
				$('#transactionform').show();

				$("#formbody").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				$("#formbody").show();						
				
				$('#incomebody').show();
				$('#expensebody').hide();
				$.ajax({
			      url: "gettransactionfields?typeId="+val,
			      success: function(data) {
			    	  $("#formbody").html(data);
			    	  $('.date-picker').datepicker({
						autoclose: true,
						todayHighlight: true
					  });
			    	  $('.number').keydown(function(e) {
						 this.value = this.value.replace(/[^0-9.]/g, ''); 
						 this.value = this.value.replace(/(\..*)\./g, '$1');
					  });
			    	  $('.chosen-select').chosen();
			    	  $("#formbody").show();
			    	  
			      },
			      type: 'GET'
			   });	

			}
			function showPaymentFields(val){
				$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				$.ajax({
				      url: "getpaymentfields?paymenttype="+val,
				      success: function(data) {
				    	  $("#addfields").html(data);
				    	  $('.date-picker').datepicker({
							autoclose: true,
							todayHighlight: true
						  });
				    	  $("#addfields").show();
				      },
				      type: 'GET'
				   });
				
			}
			$('#trantypebody').hide();
			$('#incomebody').hide();
			$('#expensebody').hide();
			$('#transactionform').hide();
			

			function modalEditServiceProvider(id, branchId, provider, name, number,companyName, configDetails, address, refName,refNumber){
				$("#provider1 option").each(function() { this.selected = (this.text == provider); });
				$("#branch1 option").each(function() { this.selected = (this.text == branchId); });
				$("#name1").val(name);				
				$("#number1").val(number);
				$("#companyname1").val(companyName);
				$("#configdetails1").val(configDetails);
				$("#address1").val(address);
				$("#referencename1").val(refName);
				$("#referencenumber1").val(refNumber);
				$("#id1").val(id);		
			}

			function changeState(val){
				$.ajax({
			      url: "getcitiesbystateid?id="+val,
			      success: function(data) {
			    	  $("#cityname").html(data);
			      },
			      type: 'GET'
			   });
			}

			function calcTotal(){
				ltrs = $("#litres").val();
				price = $("#priceperlitre").val();
				$("#totalamount").val(ltrs*price);
			}

			function enablePaymentType(val){
				if(val == "Yes"){
					$("#paymenttype").attr("disabled",false);
				}
				else{
					$("#paymenttype").attr("disabled",true);
				}
			}
			function enableIncharge(val){
				if(val == "Yes"){
					$("#incharge").attr("disabled",false);
				}
				else{
					$("#incharge").attr("disabled",true);
				}
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
			.prev().on(ace.click_event, function(){
				$(this).next().focus();
			});

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
		
				.on( 'search.dt', function () { 
					value = $('.dataTables_filter input').val(); 
					 
				})
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