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

@extends('masters.modalmaster')
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
	@stop
	@section('inline_css')
		<style>
			label {
			    font-weight: normal;
			    font-size: 13px;
			}
		</style>
	@stop
	
	@section('bredcum')	
		<small>
			INCOME& EXPENSES
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{ strtoupper($form_info['bredcum'])}}			
		</small>
		
	@stop

	@section('page_content')		
		<div class="row col-xs-offset-0 col-xs-12">
		<div class="">
			<div class="">
				<div class="">
				<form style="padding-top:0px;" class="{{$form_info['class']}}" action="{{$form_info['action']}}" method="{{$form_info['method']}}" name="{{$form_info['name']}}"  id="{{$form_info['name']}}" enctype="multipart/form-data">
					<?php $form_fields = $form_info['form_fields'];?>	
					<?php foreach ($form_fields as $form_field) {?>
						<div class="col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
						<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<input {{$form_field['readonly']}}  type="{{$form_field['type']}}" value="{{$form_field['value']}}"  id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
							</div>			
						</div>
						<?php } ?>
						
						<?php if($form_field['type'] === "hidden"){ ?>
						<div class="form-group">
							<div class="col-xs-7">
								<input type="{{$form_field['type']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" />
							</div>			
						</div>
						<?php } ?>
						
						<?php if($form_field['type'] === "textarea"){ ?>				
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}">{{$form_field['value']}}</textarea>
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
										$selcted = "";
										if($form_field['value']==$key){
											$selcted = " checked='true' ";
										}
										echo "<label><input type='radio' $selcted name=\"".$form_field['name']."\"class='ace' value='$key'> <span class='lbl'>".$value."</span></label>&nbsp;&nbsp;";
									}
								?>
								</div>
							</div>			
						</div>
						<?php } ?>
						
						<?php if($form_field['type'] === "file"){ ?>				
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<input type="file" id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="form-control file"/>
								</div>			
							</div>
							<script><?php if(isset($form_field['value']) && $form_field['value'] != "") echo "fileExist = true; fileName='".$form_field['value']."'; "; else echo "fileExist = false;";?></script>
						<?php } ?>
						
						<?php if($form_field['type'] === "checkboxslide"){ ?>
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7" style="margin-top: 6px;">
									<input id="{{$form_field['name']}}"  name="{{$form_field['name']}}"  class="ace ace-switch ace-switch-5" <?php if(isset($form_field['value']) && $form_field['value'] == "Yes") echo ' checked="checked" '; ?> type="checkbox" />
									<span class="lbl"></span>
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
											if(isset($form_field['multiple']) && isset($form_field['value'])&& in_array($key, $form_field['value'])){
												echo "<option selected value='$key'>$value</option>";
											}
											else if(isset($form_field['value']) && $form_field['value']==$key){
												echo "<option selected value='$key'>$value</option>";
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
					
					<?php } ?>
					<div id="addfields">
					<?php $form_fields = $form_info['form_payment_fields'];?>	
					<?php foreach ($form_fields as $form_field) {?>
						<div class="col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
						<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<input {{$form_field['readonly']}}  type="{{$form_field['type']}}" value="{{$form_field['value']}}"  id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
							</div>			
						</div>
						<?php } ?>
						
						<?php if($form_field['type'] === "hidden"){ ?>
						<div class="form-group">
							<div class="col-xs-7">
								<input type="{{$form_field['type']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" />
							</div>			
						</div>
						<?php } ?>
						
						<?php if($form_field['type'] === "textarea"){ ?>				
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}">{{$form_field['value']}}</textarea>
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
						
						<?php if($form_field['type'] === "file"){ ?>				
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<input type="file" id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="form-control file"/>
								</div>			
							</div>
							<script><?php if(isset($form_field['value']) && $form_field['value'] != "") echo "fileExist = true; fileName='".$form_field['value']."'; "; ?></script>
						<?php } ?>
						
						<?php if($form_field['type'] === "checkboxslide"){ ?>
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7" style="margin-top: 6px;">
									<input id="{{$form_field['name']}}"  name="{{$form_field['name']}}"  class="ace ace-switch ace-switch-5" <?php if(isset($form_field['value']) && $form_field['value'] == "Yes") echo ' checked="checked" '; ?> type="checkbox" />
									<span class="lbl"></span>
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
											if(isset($form_field['multiple']) && isset($form_field['value'])&& in_array($key, $form_field['value'])){
												echo "<option selected value='$key'>$value</option>";
											}
											else if(isset($form_field['value']) && $form_field['value']==$key){
												echo "<option selected value='$key'>$value</option>";
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
					<?php } ?>	
					</div>
					<div class="clearfix" >
						<div class="col-md-12" style="background-color: #E6DFDF;border-top: 2px solid #D2CDCD; margin-top: 10px;">
						<div class="col-md-offset-4 col-md-8" style="margin-top: 2%; margin-bottom: 1%">
							<button id="reset" class="btn primary" type="submit" id="submit">
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
		</div>	
	@stop
	
	@section('page_js')
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/bootbox.js"></script>
		<script src="../assets/js/jquery.maskedinput.js"></script>
		<script src="../assets/js/chosen.jquery.js"></script>
	@stop
	
	@section('inline_js')
		<script>
			$("#transtype").attr("disabled",true);
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

			function changeCity(val){
				$.ajax({
			      url: "getfinancecompanybycityid?id="+val,
			      success: function(data) {
			    	  $("#financecompany").html(data);
			      },
			      type: 'GET'
			   });
			}
			
			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
				$("#{{$form_info['name']}}").submit();
			});

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
			if(fileExist)
				$('.file').ace_file_input('show_file_list', [fileName]);
		
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.confirm('".Session::pull('message')."', function(result) {});";
				}
			?>
			
			function paginate(page){
				//alert("page : "+page);
				return;
			}
			function showPaymentFields(val){
				url = "getpaymentfields?paymenttype="+val
				if($("#type1").val()=="income"){
					url = "getpaymentfields?income=income&paymenttype="+val
				}
				$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
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
		
		</script>
	@stop
