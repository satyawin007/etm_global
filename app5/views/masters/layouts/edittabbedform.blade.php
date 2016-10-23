@extends('masters.master')
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
	@stop
	
	@section('inline_css')
		<style>
			.accordion-style1.panel-group .panel + .panel {
			    margin-top: 10px;
			}
		</style>
	@stop

	@section('bredcum')	
		<small>
			ADMINISTRATION
			<i class="ace-icon fa fa-angle-double-right"></i>
			MASTERS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{strtoupper($form_info['bredcum'])}}
		</small>
	@stop

	@section('page_content')
		<div class="row">
			<div class="col-xs-1"></div>
			<div class="col-xs-10">
				<div class="widget-box">
				<div class="widget-header">
					<h4 class="widget-title">{{strtoupper($form_info['bredcum'])}}</h4>
					<div style="float:right;padding-right: 2%; margin-top: 7px;"><a style="color: grey;" href="masters" title="masters"><span style="color:white"><i class="ace-icon fa fa-home bigger-200"></i></span></a> &nbsp; &nbsp;<a style="color: grey;" title="{{$form_info['back_url']}}" href="{{$form_info['back_url']}}"><span style="color:white"><i class="ace-icon fa fa-arrow-circle-left bigger-200"></i></span></a></div>
				</div>
				<div class="widget-body">
					<div class="widget-main">
						<!-- #section:elements.accordion -->
						<form class="form-horizontal" role="form" name="{{$form_info['name']}}" method="post" action="{{$form_info['name']}}" id="{{$form_info['name']}}">
						<div id="accordion" class="accordion-style1 panel-group">
							<?php 
								$i=0;
								$tabs = $form_info["tabs"];
								foreach ($tabs as $tab){
							?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#{{$tab['href']}}">
											<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
											&nbsp;{{$tab['heading']}}
										</a>
									</h4>
								</div>
								<div class="panel-collapse collapse in" id="{{$tab['href']}}">
									<div class="panel-body">
											<?php $form_fields = $tab['form_fields'];?>	
											<?php foreach ($form_fields as $form_field) {?>
											<div class="col-xs-6">
												<?php if($form_field['type'] === "text" || $form_field['type'] === "email" || $form_field['type'] === "password"){ ?>
												<div class="form-group">
													<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
													<div class="col-xs-8">
														<input {{$form_field['readonly']}} value="{{$form_field['value']}}" type="{{$form_field['type']}}" id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
													</div>			
												</div>
												<?php } ?>
												<?php if($form_field['type'] === "hidden"){ ?>
												<div class="form-group">
													<div class="col-xs-8">
														<input type="{{$form_field['type']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" >
													</div>			
												</div>
												<?php } ?>
												<?php if($form_field['type'] === "textarea"){ ?>				
												<div class="form-group">
													<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
													<div class="col-xs-8">
														<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}">{{$form_field['value']}}</textarea>
													</div>			
												</div>
												<?php } ?>
												<?php if($form_field['type'] === "radio"){ ?>				
												<div class="form-group">
													<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
													<div class="col-xs-8">
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
												<div class="col-xs-8">
													<select class="{{$form_field['class']}}" name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
														<option value="">-- {{$form_field['name']}} --</option>
														<?php 
															foreach($form_field["options"] as $key => $value){
																if($form_field['value'] == $key){
																	echo "<option selected='selected' value='$key'>$value</option>";
																}
																else {
																	echo "<option value='$key'>$value</option>";
																}
															}
														?>
													</select>
												</div>			
											</div>				
											<?php } ?>
		
										</div>
										<?php 
											}
										?>
										<div id="addfields{{$i}}"></div>								
									</div>
								</div>
							</div>
							<?php $i++; }?>
						</div>
						<div class="clearfix form-actions">
							<div class="col-md-offset-3 col-md-9">
								<button id="reset" class="btn" type="submit">
									SUBMIT
								</button>
								&nbsp; &nbsp; &nbsp;
								<button id="reset" class="btn" type="reset">
									<i class="ace-icon fa fa-undo bigger-110"></i>
									RESET
								</button>
							</div>
						</div>
						</form>
			</div>
				</div>
			</div>
				<!-- /section:elements.accordion -->
			</div><!-- /.col -->
		</div>
	@stop
	
	@section('page_js')
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/bootbox.js"></script>
	@stop
	
	@section('inline_js')
		<script>
			function changeState(val){
				$.ajax({
			      url: "getcitiesbystateid?id="+val,
			      success: function(data) {
			    	  $("#cityname").html(data);
			      },
			      type: 'GET'
			   });
			}

			function changeCity(val){
				$.ajax({
			      url: "getbranchbycityid?id="+val,
			      success: function(data) {
				      alert(data);
			    	  $("#branch").html(data);
			      },
			      type: 'GET'
			   });
			}

			function showPaymentFields(val){
				//alert(val);
				$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				$.ajax({
			      url: "getmasterspaymentfields?paymenttype="+val,
			      success: function(data) {
			    	  $("#addfields1").html(data);
			    	  $('.date-picker').datepicker({
						autoclose: true,
						todayHighlight: true
					  });
			    	  $("#addfields").show();
			      },
			      type: 'GET'
			   });
			}

			
			//datepicker plugin
			//link
			$('.date').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			//show datepicker when clicking on the icon
			.next().on(ace.click_event, function(){
				$(this).prev().focus();
			});
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
				}
			?>
			

			$("#submit").on("click",function(){
				$("#{{$form_info['name']}}").submit();
			});

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});
			
		</script>
	@stop
