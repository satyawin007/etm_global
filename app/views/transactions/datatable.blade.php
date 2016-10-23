<?php
use settings\AppSettingsController;
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
				margin-top: 3px;
			}
			.ace-file-input {
			    text-align: left !important;
			}
			.chosen-container{
			  width: 100% !important;
			}
		</style>
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen1.css" />
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
	<div id="previous_logs">
		<div class="row" style="margin-top: 0px;">
                        	<div class=" col-xs-offset-1 col-xs-10  widget-container-col ui-sortable">
										<div class="widget-box widget-color-blue3">
											<!-- #section:custom/widget-box.options -->
					<div class="widget-header">
						<h4 class="widget-title bigger lighter">
							<i class="ace-icon fa fa-table"></i>
							PREAVIOUS FUEL TRANSACTIONS
						</h4>

						<div class="widget-toolbar widget-toolbar-light no-border">
						</div>
					</div>

					<!-- /section:custom/widget-box.options -->
					<div class="widget-body">
						<div class="widget-main no-padding">
							<table class="table table-striped table-bordered table-hover">
								<thead class="thin-border-bottom">
									<tr>
										<th>
											FILLED DATE
										</th>
										<th>
											METER READING
										</th>
										<th>
											LITERS
										</th>
										<th>
											AMOUNT
										</th>
										<th>
											FULL TANK
										</th>
										<th>
											MILEAGE
										</th>
									</tr>
								</thead>
								<tbody id="previous_logs_data">
																								
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
       </div>
	</div>
	<?php $form_info = $values["form_info"]; ?>
	<?php $jobs = Session::get("jobs"); ?>
	<?php if(($values['bredcum'] == "INCOME TRANSACTIONS" && in_array(301, $jobs)) ||  
			($values['bredcum'] == "EXPENSES TRANSACTIONS" && in_array(303, $jobs)) || 
			($values['bredcum'] == "FUEL TRANSACTIONS" && in_array(305, $jobs)) || 
			($values['bredcum'] == "CONTRACT FUEL TRANSACTIONS" && in_array(409, $jobs)) || 
			($values['bredcum'] == "CONTRACT EXPENSE TRANSACTIONS" && in_array(409, $jobs)) ||
			($values['bredcum'] == "CONTRACT INCOME TRANSACTIONS" && in_array(409, $jobs))) {?>
		<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group" style="width: 99%;">			
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
						@include("transactions.add3colform",$form_info)						
					</div>
				</div>
			</div>
		</div>
	<?php }?>	
		</div>		
		
		<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: -10px;">MANAGE TRANSACTIONS</h3>		
		<div class="row" >
			<div>
				<div class="row col-xs-12" style="padding-left:2%; padding-top: 2%">
					<?php if(($values['bredcum'] == "CONTRACT EXPENSE TRANSACTIONS" || $values['bredcum'] == "EXPENSES TRANSACTIONS") && in_array(305, $jobs)){ ?>
					<div class="clearfix">
						<div class="col-xs-12 input-group">
							<form action="{{$values['form_action']}}" name="paginate" id="paginate">
							<div class="col-xs-4">
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1">DATE RANGE<span style="color:red;">*</span></label>
									<div class="col-xs-8">
										<div class="input-daterange input-group">
											<input type="text" id="fromdate"  style="padding-top: 15px;padding-bottom: 18px;" required="required" name="fromdate" <?php if(isset($values["fromdate"])) echo " value=".$values["fromdate"]." "; ?> class="input-sm form-control"/>
											<span class="input-group-addon">
												<i class="fa fa-exchange"></i>
											</span>
											<input type="text" class="input-sm form-control"  style="padding-top: 15px;padding-bottom: 18px;" id="todate" required="required" <?php if(isset($values["fromdate"])) echo " value=".$values["todate"]." "; ?>  name="todate"/>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<?php if(($values['bredcum'] == "CONTRACT FUEL TRANSACTIONS" && in_array(305, $jobs) || 
										$values['bredcum'] == "CONTRACT EXPENSE TRANSACTIONS" && in_array(305, $jobs) ||
										$values['bredcum'] == "CONTRACT INCOME TRANSACTIONS" && in_array(305, $jobs))){ ?>
									<div class="form-group">
										<div class="col-xs-6">
											<?php 
												$form_field = array("name"=>"clientname1", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot1(this.value);"), "class"=>"form-control chosen-select");
											?>
											<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
												<option value="">-- {{$form_field['name']}} --</option>
												<?php 
													$clients =  AppSettingsController::getEmpClients();
													foreach ($clients as $client){
														echo '<option value="'.$client['id'].'">'.$client['name'].'</option>'; 
													}
												?>
											</select>
										</div>
										<div class="col-xs-6">
											<?php 
												$form_field = array("name"=>"depot1", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array());
											?>
											<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
												<option value="">-- {{$form_field['name']}} --</option>
											</select>
										</div>
									</div>	
								<?php } else {?>
									<div class="form-group">
									<?php 
										$branches =  AppSettingsController::getEmpBranches();
										$branches_arr = array();
										foreach ($branches as $branch){
											$branches_arr[$branch["id"]] = $branch["name"];
										}
										if(!isset($values['branch1'])){
											$values["branch1"] = 0;
										}
									?>
									<?php $form_field = array("name"=>"branch1", "value"=>$values["branch1"], "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr); ?>
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
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
								<?php } ?>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<?php 
										$branches = LookupTypeValues::where("parentId","=",22)->get();
										$branches_arr = array();
										$branches_arr[0] = "ALL";
										foreach ($branches as $branch){
											$branches_arr[$branch["id"]] = $branch["name"];
										}
										if(!isset($values['branch1'])){
											$values["branch1"] = 0;
										}
										if(!isset($values['type1'])){
											$values["expensestype1"] = 0;
										}
									?>
									<?php $form_field = array("name"=>"expensestype1", "value"=>$values["expensestype1"], "content"=>"type", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr); ?>
									<label class="col-xs-2 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
									<div class="col-xs-10">
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
							<input type="hidden" name="transtype_h" id="transtype_h" value="{{$values['transtype']}}"/>
							<div class="col-xs-1" style="margin-top: 0px; margin-left:-20px; margin-bottom: -10px">
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
					<?php } else {?>
					<div class="clearfix">
						<div class="col-xs-12 input-group">
							<form action="{{$values['form_action']}}" name="paginate" id="paginate">
							<div class="col-xs-6">
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" for="form-field-1">DATE RANGE<span style="color:red;">*</span></label>
									<div class="col-xs-8">
										<div class="input-daterange input-group">
											<input type="text" id="fromdate"  style="padding-top: 15px;padding-bottom: 18px;" required="required" name="fromdate" <?php if(isset($values["fromdate"])) echo " value=".$values["fromdate"]." "; ?> class="input-sm form-control"/>
											<span class="input-group-addon">
												<i class="fa fa-exchange"></i>
											</span>
											<input type="text" class="input-sm form-control"  style="padding-top: 15px;padding-bottom: 18px;" id="todate" required="required" <?php if(isset($values["fromdate"])) echo " value=".$values["todate"]." "; ?>  name="todate"/>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<?php if(($values['bredcum'] == "CONTRACT FUEL TRANSACTIONS" && in_array(305, $jobs) || 
										$values['bredcum'] == "CONTRACT EXPENSE TRANSACTIONS" && in_array(305, $jobs) ||
										$values['bredcum'] == "CONTRACT INCOME TRANSACTIONS" && in_array(305, $jobs))){ ?>
									<div class="form-group">
										<div class="col-xs-6">
											<?php 
												$form_field = array("name"=>"clientname1", "content"=>"client name", "readonly"=>"",  "required"=>"required", "type"=>"select", "action"=>array("type"=>"onChange", "script"=>"changeDepot1(this.value);"), "class"=>"form-control chosen-select");
											?>
											<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
												<option value="">-- {{$form_field['name']}} --</option>
												<?php 
													$clients =  AppSettingsController::getEmpClients();
													foreach ($clients as $client){
														echo '<option value="'.$client['id'].'">'.$client['name'].'</option>'; 
													}
												?>
											</select>
										</div>
										<div class="col-xs-6">
											<?php 
												$form_field = array("name"=>"depot1", "content"=>"depot/branch name", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>array());
											?>
											<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
												<option value="">-- {{$form_field['name']}} --</option>
											</select>
										</div>
									</div>	
								<?php } else {?>
									<div class="form-group">
									<?php 
										$branches =  AppSettingsController::getEmpBranches();
										$branches_arr = array();
										foreach ($branches as $branch){
											$branches_arr[$branch["id"]] = $branch["name"];
										}
										if(!isset($values['branch1'])){
											$values["branch1"] = 0;
										}
									?>
									<?php $form_field = array("name"=>"branch1", "value"=>$values["branch1"], "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr); ?>
									<label class="col-xs-2 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
									<div class="col-xs-10">
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
								<?php } ?>
							</div>
							<input type="hidden" name="transtype_h" id="transtype_h" value="{{$values['transtype']}}"/>
							<div class="col-xs-1" style="margin-top: 0px; margin-left:-20px; margin-bottom: -10px">
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
					<?php }?>
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
		<script src="../assets/js/autosize.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});
			$("#branch").on("change",function(){$('#trantypebody').hide();  $("#transactionform").hide(); $('#incomebody').hide(); $('#expensebody').hide(); $('#verify').show();});
			$("#date").on("change",function(){$('#trantypebody').hide(); $("#transactionform").hide(); $('#incomebody').hide(); $('#expensebody').hide(); $('#verify').show();});
			$("#transtype").val(<?php echo "'".$values["transtype"]."'" ?>);
			transtype = <?php echo "'".$values["transtype"]."'" ?>;
			$("#previous_logs").hide();

			function enableIncharge(val){
				if(val == "YES"){
					$("#incharge").attr("disabled",false);
					$('.chosen-select').trigger('chosen:updated');
				}
				else{
					$("#incharge").attr("disabled",true);
					$('.chosen-select').trigger('chosen:updated');
				}
			}

			var showmessage = false;

			function getendreading(){
				$("#previousreading").val("");
				filleddate = $("#filleddate").val();
				if(filleddate == "" && showmessage){
					showmessage = true;
					alert("select filled date");
					return;
				}
				vehicleno = $("#vehicleno").val();
				if(vehicleno == ""){
					alert("select vehicle no");
					return;
				}
				$.ajax({
			      url: "getendreading?id="+vehicleno+"&date="+filleddate,
			      success: function(data) {
			    	  json_data = JSON.parse(data);
			    	  $("#previousreading").val(json_data.endReading);
			      },
			      type: 'GET'
			   });
			}

			function getpreviouslogs(val){
				var vehicleid = $("#vehicleno").val();
				$.ajax({
			      url: "getpreviouslogs?date="+val+"&vehicleid="+vehicleid,
			      success: function(data) {
			    	  $("#previous_logs").show();
			    	  $("#previous_logs_data").html(data);
			      },
			      type: 'GET'
			    });

				$("#previousreading").val("");
				filleddate = $("#filleddate").val();
				if(filleddate == ""){
					alert("select filled date");
					return;
				}
				vehicleno = $("#vehicleno").val();
				if(vehicleno == ""){
					alert("select vehicle no");
					return;
				}
				$.ajax({
			      url: "getendreading?id="+vehicleno+"&date="+filleddate,
			      success: function(data) {
			    	  json_data = JSON.parse(data);
			    	  $("#previousreading").val(json_data.endReading);
			      },
			      type: 'GET'
			   });
			   
			}
			
			function test(){;
				paginate(1);
			}

			function setTranType1Value(val){
				transtype = val;
			}

			function paginate(page){
				url="gettransactiondatatabledata?name=<?php echo $values["provider"] ?>";
				if(transtype == ""){
					alert("select transaction type");
					return;
				}	
				branch = $("#branch1").val();
				if(branch == ""){
					alert("select branch");
					return;
				}
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
				dt = fdt+" - "+tdt;				
				url = url+"&daterange="+dt;

				branch = $("#branch1").val();
				if(branch != undefined && branch != ""){
					url = url+'&branch1='+branch; 
				}

				expensestype1 = $("#expensestype1").val();
				if(expensestype1 != undefined && expensestype1 != ""){
					url = url+'&expensestype1='+expensestype1; 
				}
				
				client1 = $("#clientname1").val();
				if(client1 != undefined && client1 != ""){
					url = url+'&client='+client1; 
				}

				depot1 = $("#depot1").val();
				if(depot1 != undefined && depot1 != ""){
					url = url+'&depot='+depot1;
				}
				if(client1>0 && depot1>0){
					url = url+'&type=contracts'; 
				}

				$("#page").val(page);
				//alert(url);
				myTable.ajax.url(url).load();
				//$("#paginate").submit();				
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
				$.ajax({
			      url: "verifytransactiondateandbranch?branch="+branch+"&date="+dt,
			      success: function(data) {
				      if(data=="NO"){
				    	  bootbox.confirm('Sorry!!! Transactions closed for this date. Please contact Accountant.', function(result) {});
				    	  $('#verify').show();
				      }
				      else{
				    	 showTranType(<?php echo '"'.$values["transtype"].'"' ?>);
				      }
			      },
			      type: 'GET'
			   });	
				//$('#trantypebody').show();
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

			function deleteTransaction(id) {
				bootbox.confirm("Are you sure, you want to delete this transaction?", function(result) {
					if(result) {
						$.ajax({
					      url: "deletetransaction?id="+id+"&type="+transtype,
					      success: function(data) {
						      if(data=="success"){
						    	  bootbox.confirm('TRANSACTION SUCCESSFULLY DELETED!', function(result) {});
						      }
						      else{
						    	  bootbox.confirm('TRANSACTION COULD NOT BE DELETED!', function(result) {});
						      }
					      },
					      type: 'GET'
					   });	
					   location.reload();
					}
				});
			};

			function getContractFuelFields(val){
				$("#formbody").hide();
				$("#addfields").hide();
				transtype = val;

				client = $("#clientname").val();
				clientbranch = $("#depot").val();
							
				if(true){	
					$('#transactionform').show();				
					$('#expensebody').hide();
					$('#incomebody').hide();
					$("#formbody").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
					$("#formbody").show();	
					vehiclestatus =  $("input[name=vehiclestatus]:checked").val();
					if(vehiclestatus == undefined){
						vehiclestatus = "ACTIVE";
					}				
					$.ajax({
				      url: "getfueltransactionfields?client="+client+"&clientbranch="+clientbranch+"&vehiclestatus="+vehiclestatus,
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
						  $('.chosen-select a.chosen-single').focus();
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
						  $("#incharge").attr("disabled",true);
						  $("#enableincharge").val("NO");
						  $("#paymenttype").attr("disabled",true);
						  $('.chosen-select').trigger('chosen:updated');
						  $("#enableincharge").on("change",function(){
							  	val = $("#enableincharge").val();
							  	if(val == "YES"){
							  		$("#paymentpaid").val("Yes");
								  	$("#paymentpaid").attr("disabled",false);
								  	$("#paymenttype").attr("disabled",false);
									$("#incharge").attr("disabled",false);
									$('.chosen-select').trigger('chosen:updated');
								}
								else{
									$("#paymentpaid").val("No");
									$("#paymentpaid").attr("disabled",false);
									$("#paymenttype").attr("disabled",true);
									$("#incharge").attr("disabled",true);
									$('.chosen-select').trigger('chosen:updated');
								}
						  });

						  $('input[type=radio][name=fulltank]').change(function() {
							  	vehicleid = $("#vehicleno").val();
							  	date = $("#date").val();
							  	if(date==""){
						        	alert("enter date");
						        	$('input[type=radio][name=fulltank]').removeAttr("checked");
						        	return;
					        	}
						        if (this.value == 'YES') {
						        	vehicleid = $("#vehicleno").val();
						        	if(vehicleid==""){
							        	alert("select vehicle");
							        	$('input[type=radio][name=fulltank]').removeAttr("checked");
							        	return;
						        	}
						        	litres = $("#litres").val();
						        	if(litres==""){
							        	alert("enter litres");
							        	$('input[type=radio][name=fulltank]').removeAttr("checked");
							        	return;
						        	}
						        	startreading = $("#startreading").val();
						        	if(startreading==""){
							        	alert("enter startreading");
							        	$('input[type=radio][name=fulltank]').removeAttr("checked");
							        	return;
						        	}
						        	calculateMilage();
						        }
						    });
				    	  $("#formbody").show();
				    	  
				      },
				      type: 'GET'
				   });
				}	
			}

			function getContractVehicles(val){
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
				vehiclestatus =  $("input[name=vehiclestatus]:checked").val();
				if(vehiclestatus == undefined){
					vehiclestatus = "ACTIVE";
				}
				//alert(vehiclestatus);
				$.ajax({
			      url: "getvehiclecontractinfo?clientid="+clientId+"&depotid="+depotId+"&vehiclestatus="+vehiclestatus,
			      success: function(data) {
			    	  $("#vehicleno").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
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
				      url: "gettransactionfields1",
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
					branch = $("#branch").val();			
					$.ajax({
				      url: "getfueltransactionfields?branch="+branch,
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
						  $('.chosen-select a.chosen-single').focus();
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
						  $("#incharge").attr("disabled",true);
						  $("#enableincharge").val("NO");
						  $("#paymenttype").attr("disabled",true);
						  $('.chosen-select').trigger('chosen:updated');
						  $("#enableincharge").on("change",function(){
							  	val = $("#enableincharge").val();
							  	if(val == "YES"){
							  		$("#paymentpaid").val("Yes");
								  	$("#paymentpaid").attr("disabled",false);
								  	$("#paymenttype").attr("disabled",false);
									$("#incharge").attr("disabled",false);
									$('.chosen-select').trigger('chosen:updated');
								}
								else{
									$("#paymentpaid").val("No");
								  	$("#paymentpaid").attr("disabled",false);
								  	$("#paymenttype").attr("disabled",true);
									$("#incharge").attr("disabled",true);
									$('.chosen-select').trigger('chosen:updated');
								}
						  });

						  $('input[type=radio][name=fulltank]').change(function() {
							  	vehicleid = $("#vehicleno").val();
							  	date = $("#date").val();
						        if (this.value == 'YES') {
						        	vehicleid = $("#vehicleno").val();
						        	if(vehicleid==""){
							        	alert("select vehicle");
							        	$('input[type=radio][name=fulltank]').removeAttr("checked");
							        	return;
						        	}
						        	litres = $("#litres").val();
						        	if(litres==""){
							        	alert("enter litres");
							        	$('input[type=radio][name=fulltank]').removeAttr("checked");
							        	return;
						        	}
						        	startreading = $("#startreading").val();
						        	if(startreading==""){
							        	alert("enter startreading");
							        	$('input[type=radio][name=fulltank]').removeAttr("checked");
							        	return;
						        	}
						        	calculateMilage();
						        }
						    });
				    	  $("#formbody").show();
				    	  
				      },
				      type: 'GET'
				   });
				}	
			}

			function calculateMilage(){
				startreading = $("#startreading").val();
	        	if(startreading != ""){
	        		previousreading = $("#previousreading").val();
	        		previousreading = parseInt(previousreading);
	        		startreading = parseInt(startreading);
	        		if(previousreading>=startreading){
		        		alert("Current reading must be greater than previous reading");
		        		$("#startreading").val("");
		        		return;
	        		}
	        	}
				vehicleid = $("#vehicleno").val();
			  	date = $("#filleddate").val();
	        	vehicleid = $("#vehicleno").val();
	        	if(vehicleid==""){
		        	$('input[type=radio][name=fulltank]').removeAttr("checked");
		        	return;
	        	}
	        	litres = $("#litres").val();
	        	if(litres==""){
		        	$('input[type=radio][name=fulltank]').removeAttr("checked");
		        	return;
	        	}
	        	startreading = $("#startreading").val();
	        	if(startreading==""){
		        	$('input[type=radio][name=fulltank]').removeAttr("checked");
		        	return;
	        	}
	        	status = $('input[type=radio][name=fulltank]:checked').val() ;
	        	if(status == "YES"){
		            $.ajax({
					      url: "getvehiclelastreading?vehicleId="+vehicleid+"&date="+date,
					      success: function(prev_reading) {
						      mileage = (startreading-prev_reading)/litres;
						      mileage = parseFloat(mileage).toFixed(2);
						      $("#mileage").val(mileage);
					      }
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
					//document.getElementById('transactionform').appendChild(myin);
				}
				if(transtype == "expense"){ 
					var myin = document.createElement("input"); 
					myin.type='hidden'; 
					myin.name='type'; 
					myin.value=$("#expense").val(); 
					//document.getElementById('transactionform').appendChild(myin);
				} 
				if(transtype == "fuel"){ 
					var myin = document.createElement("input"); 
					myin.type='hidden'; 
					myin.name='type'; 
					myin.value="fuel"; 
					//document.getElementById('transactionform').appendChild(myin);
				} 
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='date1'; 
				myin.value=$("#date").val();
				document.getElementById('transactionform').appendChild(myin);
				$('#transactionform').show();

				$("#formbody").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				$("#formbody").show();						
				
				//$('#incomebody').show();
				//$('#expensebody').hide();
				url = "gettransactionfields?typeId="+val+"&transtype="+transtype;
				<?php 
					if($values['bredcum'] == "CONTRACT EXPENSE TRANSACTIONS"){
						echo "url = url+'&type=contracts';";
					}
				?>
				$.ajax({
			      url: url,
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
						
					  if(transtype != "income"){
					  	$("#incharge").attr("disabled",true);
					  }
					  $("#enableincharge").val("NO");
					  $('.chosen-select').trigger('chosen:updated');
					  $("#enableincharge").on("change",function(){
						  	val = $("#enableincharge").val();
						  	if(val == "YES"){
								$("#incharge").attr("disabled",false);
								$('.chosen-select').trigger('chosen:updated');
							}
							else{
								$("#incharge").attr("disabled",true);
								$('.chosen-select').trigger('chosen:updated');
							}
					  });
			    	  $("#formbody").show();
			    	  
			      },
			      type: 'GET'
			   });	

			}
			function showPaymentFields(val){
				$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');

				<?php if($values["transtype"] == "income") echo  "url = 'getpaymentfields?income=income&paymenttype=';"; else echo  "url = 'getpaymentfields?paymenttype=';";?>
				url = url+val;

				$.ajax({
				      url: url,
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
			<?php if(($values['bredcum'] == "CONTRACT FUEL TRANSACTIONS" && in_array(409, $jobs))){?>
				$('#transactionform').show();
			<?php }?>

			function changeDepot(val){
				$.ajax({
			      url: "getdepotsbyclientId?id="+val,
			      success: function(data) {
			    	  $("#depot").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			    });
			}

			function changeDepot1(val){
				$.ajax({
			      url: "getdepotsbyclientId?id="+val,
			      success: function(data) {
			    	  $("#depot1").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			    });

			}

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

			function getInchargeBalance(val){
				$.ajax({
			      url: "getinchargebalance?id="+val,
			      success: function(data) {
			    	  $("#inchargebalance").val(data);
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
					$("#paymenttype option:selected").removeAttr("selected");
					$("#paymenttype").attr("disabled",true);
					  $("#addfields").html("");
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
		
			$("#submit").on("click",function(){
				vehicleno = $("#vehicleno").val();
				if(vehicleno != undefined && vehicleno == ""){
					alert("select vehicleno");
					return false;
				}
				fuelstationname = $("#fuelstationname").val();
				if(fuelstationname != undefined && fuelstationname == ""){
					alert("select fuelstationname");
					return false;
				}
				amount = $("#amount").val();
				if(amount != undefined && amount == ""){
					alert("enter amount");
					return false;
				}
				bankAccount = $("#bankaccount").val();
				if(bankAccount != undefined && bankAccount == ""){
					alert("select Bank Account");
					return false;
				}
				bankid = $("#bankId").val();
				if(bankid != undefined && bankid == ""){
					alert("select Bank Account");
					return false;
				}
				chequeNumber = $("#chequenumber").val();
				if(chequeNumber != undefined && chequeNumber == ""){
					alert("enter transaction number");
					return false;
				}
				//alert("submit");
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='transtype'; 
				myin.value=$("#transtype_h").val(); 
				document.getElementById('transactionform').appendChild(myin);

				if($("#type").val()=="351"){ 
					temp = $("#bankId").val();
					temp1 = $("#bankaccount").val();
					$("#bankaccount").val(temp);
					$("#bankId").val(temp1);
					$.ajax({
	                    url: "{{$form_info['action']}}",
	                    type: "post",
	                    data: $("#{{$form_info['name']}}").serialize(),
	                    success: function(response) {
	                        //alert(response);
	                    	response = jQuery.parseJSON(response);	
	                        if(response.status=="success"){
	                        	//temp = $("#bankId").val();
								//temp1 = $("#bankaccount").val();
								$("#bankaccount").val(temp1);
								$("#bankId").val(temp);
	                        	var myin = document.createElement("input"); 
	            				myin.type='hidden'; 
	            				myin.name='transtype'; 
	            				myin.value="income"; 
	            				document.getElementById('transactionform').appendChild(myin);
	                        	$.ajax({
	        	                    url: "{{$form_info['action']}}",
	        	                    type: "post",
	        	                    data: $("#{{$form_info['name']}}").serialize(),
	        	                    success: function(response) {
	        	                        //alert(response);
	        	                    	response = jQuery.parseJSON(response);	
	        	                        if(response.status=="success"){
	        	                            bootbox.alert(response.message);
	                                		window.setTimeout(function(){location.reload();}, 2000 ); // 5 seconds
	        	                        	resetForm("{{$form_info['name']}}");
	        	                        }
	        	                        if(response.status=="fail"){
	        	                        	bootbox.alert(response.message);
	        	                        }
	        						}
	        	                });
	                        }
	                        if(response.status=="fail"){
	                        	bootbox.alert(response.message);
	                        }
						}
	                });
					
				}
				else{
					$.ajax({
	                    url: "{{$form_info['action']}}",
	                    type: "post",
	                    data: $("#{{$form_info['name']}}").serialize(),
	                    success: function(response) {
	                        //alert(response);
	                    	response = jQuery.parseJSON(response);	
	                        if(response.status=="success"){
	                            var formData = new FormData();
	                        	formData.append('id', response.id);
	                        	formData.append('table', response.table);
	                        	var fileName = $("#billfile").val();
	                        	if(fileName != ""){
		                        	formData.append('billfile', document.getElementById("billfile").files[0]);
		                        	$.ajax({
		                        	    type: "POST",
		                        	    url: "postfile",
		                        	    data: formData,
		                        	    processData: false,
		                        	    contentType: false,
		                        	    success: function(response1) {
	                            			bootbox.alert(response1.message);
	                            			window.setTimeout(function(){location.reload();}, 2000 ); // 5 seconds
		                        	    },
		                        	    error: function(errResponse) {
		                        	        console.log(errResponse);
		                        	    }
		                        	});
	                        	}
	                        	else{
                        			bootbox.alert(response.message);
                        			window.setTimeout(function(){location.reload();}, 2000 ); // 5 seconds
	                        	}
	                        	resetForm("{{$form_info['name']}}");
	                        }
	                        if(response.status=="fail"){
	                        	bootbox.alert(response.message);
	                        }
						}
	                });
				}
				return false;
				//$("#{{$form_info['name']}}").submit();
			});

			function resetForm(formid)
		    { 
	            form = $('#'+formid);
	            element = ['input','select','textarea'];
	            for(i=0; i<element.length; i++) 
	            {
                    $.each( form.find(element[i]), function(){  
                        switch($(this).attr('class')) {
                          case 'form-control chosen-select':
                          	$(this).find('option:first-child').attr("selected", "selected"); 
                            break;
                        }
                        switch($(this).attr('type')) {
                        case 'text':
                        case 'select-one':
                        case 'textarea':
                        	$(this).val('');
                        case 'hidden':
                        case 'file':
                        	$(this).val('');
                          break;
                        case 'checkbox':
                        case 'radio':
                        	$(this).attr('checked',false);
                          break;
                       
                      }
                    });
	            }
	            $('.chosen-select').trigger("chosen:updated");	
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
		                url :"gettransactiondatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
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