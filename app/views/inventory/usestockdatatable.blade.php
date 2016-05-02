<?php 
	/*
	$form_info = array();
	$form_info["name"] = "addstate";
	$form_info["action"] = "addstate";
	$form_info["method"] = "post";
	$form_info["class"] = "form-horizontal";
	$form_info["back_url"] = "states";
	$form_fields = array();
	$form_field = array("name"=>"fullname", "content"=>"full name", "readonly"=>"",  "required"=>"required","type"=>"text", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"lastname", "content"=>"last name", "readonly"=>"", "required"=>"","type"=>"email", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"age", "content"=>"age", "readonly"=>"", "required"=>"required","type"=>"password", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"id", "content"=>"id", "readonly"=>"", "required"=>"", "type"=>"hidden", "value"=>"1", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"date", "content"=>"date", "readonly"=>"", "required"=>"required", 	"type"=>"text", "class"=>"form-control date-picker");
	$form_fields[] = $form_field;
	$form_field = array("name"=>"State", "readonly"=>"", "content"=>"state", "class"=>"form-control", "required"=>"required", "type"=>"select",
			"options"=>array("1"=>"test1","2"=>"test2", "3"=>"test3"),
			"action"=>array("type"=>"onChange", "script"=>"paginate(1)"));
	$form_fields[] = $form_field;
	$form_field = array("name"=>"gender", "readonly"=>"","content"=>"gender", "required"=>"required","type"=>"radio", "class"=>"form-control", "options"=>array("male"=>"male", "female"=>"female"));
	$form_fields[] = $form_field;
	$form_field = array("name"=>"address", "readonly"=>"", "content"=>"address", "required"=>"required", "type"=>"textarea", "class"=>"form-control");
	$form_fields[] = $form_field;
	$form_info["form_fields"] = $form_fields;
	return View::make("masters.layouts.addform",array("form_info"=>$form_info));
	*/
?>

@extends('masters.master')
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
	@stop
	@section('inline_css')
		<style>
			label {
			    font-weight: normal;
			    font-size: 13px;
			}
			.chosen-container{
			  width: 100% !important;
			}
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
	<?php $form_info = $values["form_info"]; ?>
	@section('bredcum')	
		<small>
			INVENTORY
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{ strtoupper($form_info['bredcum'])}}			
		</small>
		
	@stop

	@section('page_content')		
		<div class="row col-xs-offset-0 col-xs-12">
		<?php 
			$jobs = \Session::get("jobs");
			if(in_array(330, $jobs)){
		?>
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">{{ strtoupper($form_info['bredcum'])}}</h4>
				<div style="float:right;padding-right: 2%; margin-top: 1%"><a style="color: grey;" href="purchaseorder" title="purchaseorder"><span style="color:white"><i class="ace-icon fa fa-home bigger-200"></i></span></a> &nbsp; &nbsp;<a style="color: grey;"  title="{{$form_info['back_url']}}" href="{{$form_info['back_url']}}"><span style="color:white;"><i class="ace-icon fa fa-arrow-circle-left bigger-200"></i></span></a></div>
			</div>
			<div class="widget-body">
				<div class="widget-main no-padding">
				<form style="padding-top:0px;" class="{{$form_info['class']}}" action="{{$form_info['action']}}" method="{{$form_info['method']}}" name="{{$form_info['name']}}"  id="{{$form_info['name']}}" enctype="multipart/form-data">
					<?php $form_fields = $form_info['form_fields'];?>	
					<?php foreach ($form_fields as $form_field) {?>
						<div class="form-group col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
						<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<input {{$form_field['readonly']}} type="{{$form_field['type']}}" id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
							</div>			
						</div>
						<?php } ?>
						<?php if($form_field['type'] === "hidden"){ ?>
						<div class="form-group">
							<div class="col-xs-7">
								<input type="{{$form_field['type']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" >
							</div>			
						</div>
						<?php } ?>
						<?php if($form_field['type'] === "textarea"){ ?>				
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}"></textarea>
							</div>			
						</div>
						<?php } ?>
						<?php if($form_field['type'] === "radio"){ ?>				
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<div class="radio">
								<?php 
									foreach($form_field["options"] as $key => $value){
										echo "<label><input type='radio' name=\"".$form_field['name']."\"class='ace' value='$key'> <span class='lbl'>".$value."</span></label>&nbsp;&nbsp;";
									}
								?>
								</div>
							</div>			
						</div>
						<?php } ?>
						<?php if($form_field['type'] === "select"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['id']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
									<option value="">-- {{$form_field['name']}} --</option>
									<?php 
										foreach($form_field["options"] as $key => $value){
											echo "<option value='$key'>$value</option>";
										}
									?>
								</select>
							</div>			
						</div>				
						<?php } ?>
						</div>
					<?php } ?>
					<div class="form-group col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
						<div class="form-group" id="repairbuttons">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> REPAIR TYPE<span style="color:red;">*</span> </label>
							<div class="col-xs-7">
								<div class="radio">
								<?php 
									$form_field["options"] = array("TO WAREHOUSE"=>"TO WAREHOUSE","TO CREDIT SUPPLIER"=>"TO CREDIT SUPPLIER");
									foreach($form_field["options"] as $key => $value){
										echo "<label><input type='radio' onchange='getItems(\"".$key."\")' name=\"repairtype\" class='ace' value='$key'> <span class='lbl'>".$value."</span></label>&nbsp;&nbsp;";
									}
								?>
								</div>
							</div>			
						</div>
					</div>
					<div class="form-group col-xs-6" style="margin-top: 10px; margin-bottom: -10px">
						<div class="form-group" id="repairreturnbuttons">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> RETURN TO <span style="color:red;">*</span> </label>
							<div class="col-xs-7">
								<div class="radio">
								<?php 
									$form_field["options"] = array("TO VEHICLE1"=>" TO VEHICLE","TO WAREHOUSE1"=>" TO WAREHOUSE");
									foreach($form_field["options"] as $key => $value){
										echo "<label><input type='radio' onchange='getItems(\"".$key."\")' name=\"repairreturntype\" class='ace' value='$key'> <span class='lbl'>".$value."</span></label>&nbsp;&nbsp;";
									}
								?>
								</div>
							</div>			
						</div>
					</div>
					
					<div id="addfields"></div>
					<div id="oherfields"></div>
					</div>
					<div class="clearfix" >
						<div class="col-md-12" style="background-color: #E6DFDF;border-top: 2px solid #D2CDCD; margin-top: 10px;">
						<div class="col-md-offset-4 col-md-8" style="margin-top: 2%; margin-bottom: 1%">
							<button class="btn primary" id="submit" onClick="postData()">
								<i class="ace-icon fa fa-check bigger-110"></i>
								SUBMIT
							</button>
							<!--  <input type="submit" class="btn btn-info" type="button" value="SUBMIT"> -->
							&nbsp; &nbsp; &nbsp;
							<button id="reset" class="btn" type="reset">
								<i class="ace-icon fa fa-undo bigger-110"></i>
								RESET
							</button>
						</div>
						</div>
					</div>
				</form>
				</div>
			</div>
			<?php }?>
			<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: -10px;">MANAGE TRANSACTIONS</h3>		
			<div class="row" >
				<div>
					<div class="row col-xs-12" style="padding-left:2%; padding-top: 2%">
						<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
						<div class="clearfix">
							<div class="col-xs-12 input-group">
								<form action="{{$values['form_action']}}" name="paginate" id="paginate">
								<div class="col-xs-offset-1 col-xs-5">
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
									<div class="form-group">
										<?php 
											$branches_arr = array();
											$warehouses = \OfficeBranch::where("isWareHouse","=","Yes")->get();
											foreach ($warehouses as $warehouse){
												$branches_arr[$warehouse->id] = $warehouse->name;
											}
											if(!isset($values['warehouse1'])){
												$values["warehouse1"] = 0;
											}
										?>
										<?php $form_field = array("name"=>"warehouse1", "value"=>$values["warehouse1"], "content"=>"warehouse", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select1", "options"=>$branches_arr); ?>
										<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
										<div class="col-xs-9">
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
								<div class="col-xs-1" style="margin-top: 0px; margin-left:-20px; margin-bottom: -10px">
									<div class="form-group">
										<label class="col-xs-0 control-label no-padding-right" for="form-field-1"> </label>
										<div class="col-xs-5">
											<input class="btn btn-sm btn-primary" type="button" value="GET" onclick="test()"/>
										</div>			
									</div>
								</div>
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
							</table>								
						</div>
					</div>					
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
		<script src="../assets/js/angular-1.5.4/angular.min.js"></script>
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
	
		<!-- inline scripts related to angular JS-->
		<script>
			submit_data = "false";
			var app = angular.module('myApp', []);
			app.controller('myCtrl', function($scope, $http) {
				$scope.vehicles = [];
				$scope.ids = ['vehicle', 'servicedate', 'substitutevehicle', 'starttime', 'driver1', 'driver2', 'helper', 'penalitiestype'];
				$scope.vars = ['distance','repairkms', 'startreading', 'endreading', 'penalityamount', 'remarks' ];
				$scope.vehicles_text = [];
				exe_recs_text = [];
				$scope.addRow = function(){
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						if(entry != "vehicle"){
							$scope[entry] = text;
						}
					});	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" ||  typeof $scope.servicedate === "undefined" ||$scope.driver1 === "" || $scope.vehicle === "" || $scope.servicedate === "") {
						return;
					}
					$scope.distance = $("#distance").val();	

					text_arr = [];
					veh_arr = {};
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").text();
						val = $("#"+entry+" option:selected").val();
						veh_arr[entry] = val;
						$("#"+entry).find('option:selected').removeAttr("selected");
						if(val==""){
							text="";
						}
						text_arr[entry] = text;
						$scope[entry] = '';
					});
					$scope.vars.forEach(function(entry) {
						text_arr[entry] = $scope[entry];
						veh_arr[entry] = $scope[entry];
						$scope[entry] = '';
					});

					$scope.vehicles_text.unshift(text_arr);
					$scope.vehicles.unshift(veh_arr);
					$('.chosen-select').trigger("chosen:updated");
				};

				$scope.editRow = function(vehicle){	
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					var comArr1 = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === vehicle ) {
							index = i;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Something gone wrong" );
						return;
					}
					$scope.vars.forEach(function(entry) {
						$scope[entry]=comArr1[i][entry];
					});	
					$scope.ids.forEach(function(entry) {
						$("#"+entry+" option").each(function() {   this.selected =(this.text == comArr[i][entry])});
						$("#"+entry).find('option:selected').attr("selected", "selected"); 
						$scope[entry]=comArr1[i][entry];
					});	
					$('.chosen-select').trigger("chosen:updated");	
				};

				$scope.updateRow = function(){	
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						text = text.replace("? string:", "");
						text = text.replace(" ?", "");
						if(entry != "vehicle"){
							$scope[entry] = text;
						}
					});	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" ||  typeof $scope.servicedate === "undefined" ||$scope.driver1 === "" || $scope.vehicle === "" || $scope.servicedate === "") {
						return;
					}	
					$scope.distance = $("#distance").val();	
					tempdata = [];
					var index = -1;		
					var comArr = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === $scope.vehicle ) {
							index = i;
							$scope.ids.forEach(function(entry) {
								text = $("#"+entry+" option:selected").text();
								$("#"+entry).find('option:selected').removeAttr("selected");
								if(entry != "vehicle"){
									if(text != ""){
										$scope.vehicles_text[index][entry] = text;
									}
									$scope.vehicles[index][entry] = $scope[entry];
									$scope[entry] = '';
								}
							});
							$scope.vars.forEach(function(entry) {
								$scope.vehicles_text[index][entry] = $scope[entry];
								$scope.vehicles[index][entry] = $scope[entry];
								$scope[entry] = '';
							});
							break;
						}
					}
					if( index === -1 ) {
						alert( "Vehicle can not be updated / Something gone wrong" );
						return;
					}
					alert("updated successfully");
					$('.chosen-select').trigger("chosen:updated");
				};
				
				$scope.removeRow = function(vehicle){	
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === vehicle ) {
							index = i;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Something gone wrong" );
						return;
					}
					$scope.vehicles.splice( index, 1 );	
					$scope.vehicles_text.splice( index, 1 );		
				};

				$scope.postData = function() {
					if(submit_data=="false"){
						return;
					}
					$('#jsondata').val(JSON.stringify($scope.vehicles));
					$.ajax({
                        url: "{{$form_info['name']}}",
                        type: "post",
                        data: $("#{{$form_info['name']}}").serialize(),
                        success: function(response) {
                        	response = jQuery.parseJSON(response);	
                            if(response.status=="success"){
                            	bootbox.alert(response.message, function(result) {});
                            	resetForm("{{$form_info['name']}}");
                            	$scope.vehicles= [];	
            					$scope.vehicles_text = [];		
                            }
                            if(response.status=="fail"){
                            	bootbox.alert(response.message, function(result) {});
                            }
                        }
                    });
				};

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
			});
		</script>
	
	
		<script>
			$("#repairbuttons").hide();
			$("#repairreturnbuttons").hide();

			var count = 0;
			function getItems(val){
				$("#repairbuttons").hide();
				$("#repairreturnbuttons").hide();
				action = $("#action").val();
				warehouse = $("#warehouse").val();
				if(action==""){
					alert("select action");
					return;
				}
				if(action=="itemtovehicles"){
					ids = ["item", "itemnumbers", "itemactions", "vehicle"];
					vars = ["qty", "alertdate", "remarks"];
					hide_fields_text = ["itemactions"];
					entities_text = [];
					entities = [];
					exe_recs_text = [];
					condition_elements = ["item","qty"];
				}
				if(action=="warehousetowarehouse"){
					ids = ["item", "itemnumbers", "towarehouse"];
					vars = ["qty", "remarks"];
					entities_text = [];
					entities = [];
					exe_recs_text = [];
					condition_elements = ["item","qty"];
				}
				if(action=="vehicletowarehouse"){
					$("#addfields").html("");
					count = 1;
					$("#repairbuttons").show();
					if(val == "TO WAREHOUSE" || val == "TO CREDIT SUPPLIER"){
						action = val;
					}
					else if(val == 0){}
					else{
						return;
					}
				}
				if(action=="creditsuppliertowarehouse"){
					$("#addfields").html("");
					count = 1;
					$("#repairreturnbuttons").show();
					if(val == "TO WAREHOUSE1" || val == "TO VEHICLE1"){
						action = val;
					}
					else if(val == 0){}
					else{
						return;
					}
				}
				if(warehouse==""){
					if(count == 0) {
						count = 1;
						return;
					}
					$('#repairbuttons').find('input').removeAttr('checked');
					alert("select warehouse");
					return;
				}
				$.ajax({
			      url: "getitemsbyaction?action="+action+"&warehouseid="+warehouse,
			      success: function(data) {
			    	  $("#addfields").html(data);
			    	  $("#div_itemnumbers").hide();
					  $("#div_alertdate").hide();
					  $("#div_itemactions").hide();
			    	  $("#units").attr("disabled",true);
			    	  $('.chosen-select').chosen();
			    	  $('.chosen-select').trigger('chosen:updated');
			      },
			      type: 'GET'
			   });
			}

			function getManufacturers(value, id){
				id = id.replace("item","units");
				$.ajax({
			      url: "getmanufacturers?itemid="+value,
			      success: function(data) {
			    	  $("#"+id).html(data);
					  $('.chosen-select').trigger('chosen:updated');
			      },
			      type: 'GET'
			   });
			}

			function getCreditSupplierItems(value, id){
				id = id.replace("creditsupplier","item");
				$.ajax({
			      url: "getrepairitembysupplier?itemid="+value,
			      success: function(data) {
				      alert(id);
			    	  $("#"+id).html(data);
					  $('.chosen-select').trigger('chosen:updated');
			      },
			      type: 'GET'
			   });
			}

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

			function showPaymentFields(val){
				$("#paymentfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				$.ajax({
				      url: "getpaymentfields?paymenttype="+val,
				      success: function(data) {
				    	  $("#paymentfields").html(data);
				    	  $('.date-picker').datepicker({
							autoclose: true,
							todayHighlight: true
						  });
				    	  $("#paymentfields").show();
				      },
				      type: 'GET'
				   });
				
			}

			function enablePaymentType(val){
				if(val == "Yes"){
					$("#paymenttype").attr("disabled",false);
				}
				else{
					$("#paymenttype").val("");
					$("#paymenttype").attr("disabled",true);
					//$("#addfields").hide();
				}
			}

			function qtyChange(id){
				var item = id.substring(3, id.length);
				item = "#item"+item;
				itemVal = $(item+" option:selected").text();
				start = itemVal.indexOf("qty(");
				start = start+4;
				itemQty = itemVal.substring(start,itemVal.indexOf(")"));
				itemQty = parseInt(itemQty);
				reqQty = parseInt($("#"+id).val());
				if(itemQty<reqQty){
					alert("Available Item Quantity : "+itemQty);
					$("#"+id).val("");
					return;
				}
			}
				
			function test(){
				branch = $("#warehouse1").val();
				if(branch == ""){
					alert("select warehouse");
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
				$("#paginate").submit();		
			}
			

			function getItemInfo(id){
				$("#div_itemnumbers").hide();
				$("#div_alertdate").hide();
				$("#div_itemactions").hide();
				$.ajax({
			      url: "getiteminfo?id="+id,
			      success: function(data) {
				      //alert(data);
			    	  var obj = JSON.parse(data);
			    	  $("#units").val(obj.units);
			    	  if(obj.itemactionsstatus=="Yes"){
			    		  $("#itemactions").html(obj.itemactions);
			    		  $("#div_itemactions").show();
			    	  }
			    	  if(obj.itemnumberstatus=="Yes"){
			    		  $("#itemnumbers").html(obj.itemnumbers);
			    		  $("#div_itemnumbers").show();
			    	  }
			    	  if(obj.alertstatus=="Yes"){
			    		  $("#div_alertdate").show();
			    	  }
			    	  $("#bill").attr('href', "../app/storage/uploads/"+obj.filePath);
			    	  $('.date-picker').datepicker({
						autoclose: true,
						todayHighlight: true
					  });
			    	  $('.chosen-select').trigger('chosen:updated');
			      },
			      type: 'GET'
			   });
			}

			function deleteUsedStockItem(id) {
				bootbox.confirm("Are you sure, you want to delete this used stock item?", function(result) {
					if(result) {
						$.ajax({
					      url: "deleteusedstockitem?id="+id,
					      success: function(data) {
						      if(data=="success"){
						    	  bootbox.alert('ADDED STOCK ITEM SUCCESSFULLY DELETED!', function(result) {});
						      }
						      else{
						    	  bootbox.alert('ADDED STOCK ITEM COULD NOT BE DELETED!', function(result) {});
						      }
						      location.reload();	
					      },
					      type: 'GET'
					   });	
					}
				});
			};

			function getItemInfo1(val){
				$.ajax({
			      url: "getiteminfo?action=vehicletovehicle&id="+val,
			      success: function(data) {
			    	  var obj = JSON.parse(data);
			    	  $("#units").val(obj.units);
			    	  $("#toaction").html(obj.itemactions);
			    	  $("#fromaction").html(obj.itemactions);
			    	  $('.chosen-select').chosen();
			    	  $('.chosen-select').trigger('chosen:updated');
			      },
			      type: 'GET'
			   });
			}
			
			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
				date = $("#date").val();
				if(date==""){
					alert("entter date");
					return;
				}
				submit_data=true;
				return false;
				
				//$("#{{$form_info['name']}}").submit();
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

			//or change it into a date range picker
			$('.input-daterange').datepicker({autoclose:true,todayHighlight: true });

			$('.input-mask-phone').mask('(999) 999-9999');

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
		
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
				}
			?>

			function showTirePositions(id) {
				message_data = '<label>SELECT TIRE CHANGING POSITION : </label><br/><select class="form-control" required="" name="tireposition" id="tireposition">';
				<?php 
					$parentId = -1;
					$parent = \InventoryLookupValues::where("name","=","ITEM ACTIONS")->get();
					if(count($parent)>0){
						$parent = $parent[0];
						$parentId = $parent->id;
					}
					$veh_actions_arr = array();
					$veh_actions =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
					$veh_actions_data = "";
					foreach ($veh_actions  as $veh_action){
						$veh_actions_data = $veh_actions_data.'<option value="'.$veh_action['id'].'">'.$veh_action->name.'</option>';
					}
					echo "message_data=message_data+'".$veh_actions_data."';";
				?>
				message_data = message_data+'</select>';							
				bootbox.confirm({
					message: message_data,
					buttons: {
					  confirm: {
						 label: "OK",
						 className: "btn-primary btn-sm",
					  },
					  cancel: {
						 label: "Cancel",
						 className: "btn-sm",
					  }
					},
					callback: function(result) {
						if(result){
							val= $("#tireposition").val();
							 $("#position"+id).val(val);
						}
					}
				  }
				);
			}
		

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
			
			function validateQuantity(qty){
				text = $("#item option:selected").text();
				if(text.indexOf("qty") > -1){
					endindex = text.indexOf("qty(");
					text = text.substring(endindex+4, endindex+10);
					endindex = text.indexOf(")");
					avalqty = text.substring(0, endindex);
					avalqty = parseInt(avalqty);
					qty = parseInt(qty);
					if(avalqty<qty){
						alert("Entered Quantity not available");
						$("#qty").val("");
					}
				}
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
					  <?php $cnt=count($values["theads"]); for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
					],
					"aaSorting": [],
					oLanguage: {
				        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
				    },
					"bProcessing": true,
			        "bServerSide": true,
					"ajax":{
		                url :"getinventorydatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
		                type: "get",  // method  , by default get
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
		
		<script>
			var ids = [];
			var vars = [];
			var entities_text = [];
			var entities = [];
			var hide_fields_text = [];
			var condition_elements = [];
			var rowid=0;
			var editrowid=-1;
			var submit_data=false;
			function addRow(){
				//alert("in addRow "+condition_elements);
				ids.forEach(function(entry) {
					text = $("#"+entry+" option:selected").val();
				});
				add_condition = false;	
				var isReturn = false;
				condition_elements.forEach(function(entry) {
					itemm_val = $("#"+entry).val();
					if(typeof itemm_val === "undefined" || itemm_val == ""){
						alert("select "+entry);
						isReturn=true;
					}
				});
				if(isReturn){
					return;
				}
				text_arr = new Array();
				veh_arr = new Array();
				ids.forEach(function(entry) {
					text = $("#"+entry+" option:selected").text();
					if(entry=="itemnumbers"){
						text = "";
						$('#itemnumbers option:selected').each(function(){ 
							text = text+$(this).text()+","; 
						});
					}
					val = $("#"+entry+" option:selected").val();
					veh_arr[entry] = val;
					$("#"+entry).find('option:selected').removeAttr("selected");
					if(val==""){
						text="";
					}
					if(entry=="item"){
						if(text.indexOf("qty") > -1){
							endindex = text.indexOf(" -");
							text = text.substring(0, endindex);
						}
					}
					text_arr[entry] = text;
				});
				vars.forEach(function(entry) {
					text_arr[entry] = $("#"+entry).val();
					veh_arr[entry] = $("#"+entry).val();
					$("#"+entry).val("");
				});
				text_arr["rowid"]=rowid;
				rowid++;
				entities_text.unshift(text_arr);
				entities.unshift(veh_arr);
				$('.chosen-select').trigger("chosen:updated");
				drawTable()
			}

			function drawTable(){
				//alert("indraw Table: "+entities_text.length);
				table_data = "";
				comArr = entities_text;
				for(i=0; i<entities_text.length; i++){
					table_data = table_data+"<tr>";
					ids.forEach(function(entry) {
						table_data = table_data+"<td>"+entities_text[i][entry]+"</td>"
					});
					vars.forEach(function(entry) {
						table_data = table_data+"<td>"+entities_text[i][entry]+"</td>"
					});
					table_data = table_data+"<td>"+
											'<span   style="margin:2px; color: #428bca" id="editrowbtn" onclick="editRow(\''+entities_text[i].rowid+'\')"><i class="ace-icon fa fa-pencil-square-o bigger-150"></i> </span>&nbsp;'+
											'<span   style="margin:2px;color: #d12723" id="removerowbtn" onclick="removeRow(\''+entities_text[i].rowid+'\')"><i class="ace-icon fa fa-trash-o bigger-150"></i></span>'
										+"</td>";
					table_data = table_data+"</tr>";
				}
				$("#rowtable").html(table_data);
			}
	
			function editRow(rowid1){	
				var index = -1;		
				var comArr = eval( entities_text );
				var comArr1 = eval( entities );
				for( var i = 0; i < comArr.length; i++ ) {
					alert("editrow : "+comArr[i].rowid+" - "+rowid1);
					if( comArr[i].rowid == rowid1 ) {
						index = i;
						editrowid = rowid1;
						break;
					}
				}
				if( index === -1 ) {
					alert( "Something gone wrong" );
					return;
				}
				vars.forEach(function(entry) {
					$("#"+entry).val(comArr1[index][entry]);
				});	
				ids.forEach(function(entry) {
					$("#"+entry+" option").each(function() {   this.selected =(this.value == comArr1[index][entry])});
					$("#"+entry).find('option:selected').attr("selected", "selected"); 
				});	
				$('.chosen-select').trigger("chosen:updated");	
			};
	
			function updateRow(){	
				tempdata = [];
				var index = -1;		
				var comArr = eval( entities_text );
				for( var i = 0; i < comArr.length; i++ ) {
					alert("updateRow :"+comArr[i].rowid+" - "+editrowid);
					if( comArr[i].rowid == editrowid ) {
						index = i;
						ids.forEach(function(entry) {
							text = $("#"+entry+" option:selected").text();
							if(entry != "item"){
								if(text != ""){
									entities_text[index][entry] = text;
								}
								entities[index][entry] = $("#"+entry).val();
							}
							$("#"+entry).find('option:selected').removeAttr("selected");
						});
						vars.forEach(function(entry) {
							entities_text[index][entry] = $("#"+entry).val();
							entities[index][entry] = $("#"+entry).val();
							$("#"+entry).val("");
						});
						break;
					}
				}
				if( index === -1 ) {
					alert( "Vehicle can not be updated / Something gone wrong" );
					return;
				}
				alert("updated successfully");
				$('.chosen-select').trigger("chosen:updated");
				drawTable()	
			};
			
			function removeRow(rowid1){	
				var index = -1;		
				var comArr = eval(entities_text);
				for( var i = 0; i < comArr.length; i++ ) {
					alert("removeRow :"+comArr[i].rowid+" - "+rowid1);
					if( comArr[i].rowid == rowid1 ) {
						index = i;
						break;
					}
				}
				if( index === -1 ) {
					alert( "Something gone wrong" );
					return;
				}
				entities.splice( index, 1 );	
				entities_text.splice( index, 1 );	
				drawTable()	
			};
	
			function postData() {
				if(!submit_data){
					return;
				}
				var jsonobj = {};
				for(i=0; i<entities.length; i++){
					var item = {} ;
					ids.forEach(function(entry) {
						if(entry=="itemnumbers"){
							item[entry] = entities_text[i][entry];
						}
						else{
							item[entry] = entities[i][entry];
						}
					});
					vars.forEach(function(entry) {
						item[entry] = entities[i][entry];
					});
					jsonobj[i]=  item;
				}
				$('#jsondata').val(JSON.stringify(jsonobj));
				$.ajax({
	                url: "{{$form_info['name']}}",
	                type: "post",
	                data: $("#{{$form_info['name']}}").serialize(),
	                success: function(response) {
	                	response = jQuery.parseJSON(response);	
	                    if(response.status=="success"){
	                    	bootbox.alert(response.message, function(result) {});
	                    	resetForm("{{$form_info['name']}}");
	                    	entities= [];	
	                    	entities_text = [];	
	                    	drawTable();	
	                    }
	                    if(response.status=="fail"){
	                    	bootbox.alert(response.message, function(result) {});
	                    }
	                }
	            });
			};
	
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
	
		</script>
	@stop
