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

	@section('inline_css')
		<style>
			label {
			    font-weight: normal;
			    font-size: 13px;
			}
		</style>
	@stop
		<div class="col-xs-12" style="margin-top: -1%">
		<div class="row">
			<div class="col-xs-12">
				<div >
				<form style="padding-top:15px;" class="{{$form_info['class']}}" action="{{$form_info['action']}}" method="{{$form_info['method']}}" name="{{$form_info['name']}}"  id="{{$form_info['name']}}" enctype="multipart/form-data">
					<?php $form_fields = $form_info['form_fields'];?>
					<div id="formbody">	
					<?php foreach ($form_fields as $form_field) {?>
						<div class="col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
						<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<input {{$form_field['readonly']}} type="{{$form_field['type']}}" id="{{$form_field['name']}}" required="{{$form_field['required']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
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
						<?php if($form_field['type'] === "daterange"){ ?>				
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<div class="input-daterange input-group">
										<input type="text" id="fromdate"  style="padding-top: 15px;padding-bottom: 18px;" required="required" name="fromdate" <?php if(isset($values["fromdate"])) echo " value=".$values["fromdate"]." "; ?> class="input-sm form-control"/>
										<span class="input-group-addon">
											<i class="fa fa-exchange"></i>
										</span>
										<input type="text" class="input-sm form-control"  style="padding-top: 15px;padding-bottom: 18px;" id="todate" required="required" <?php if(isset($values["fromdate"])) echo " value=".$values["todate"]." "; ?>  name="todate"/>
									</div>
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
						<?php if($form_field['type'] === "selectgroup"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['id']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
									<option value="">-- {{$form_field['name']}} --</option>
									<?php 
										$options_arr = $form_field["options"];
										foreach($options_arr as $key => $values_arr){
											echo '<optgroup label="'.strtoupper($key).'">';
											foreach($values_arr as $key => $value){
												echo "<option value='$key'>$value</option>";
											}
											echo '</optgroup>';
										}
									?>
								</select>
							</div>			
						</div>				
						<?php } ?>
						</div>
					<?php } ?>
					<?php if($form_info['reporttype']== "fuel"){ ?>
						<?php $form_fields = $form_info['add_form_fields'];?>
						<?php foreach ($form_fields as $form_field) {?>
							<?php if($form_field['type'] === "select"){ ?>
							<div class="col-xs-6" style="margin-top: 10px; margin-bottom: -10px">
							<div class="form-group" id="{{$form_field['name'].'id'}}">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
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
							<?php } ?>
						<?php }?>
					<?php }?>
					<?php if($form_info['reporttype']== "creditsupplier"){ ?>
						<?php $form_fields = $form_info['add_form_fields'];?>
						<?php foreach ($form_fields as $form_field) {?>
							<?php if($form_field['type'] === "select"){ ?>
							<div class="col-xs-6" style="margin-top: 10px; margin-bottom: -10px">
							<div class="form-group" id="{{$form_field['name'].'id'}}">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
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
							<?php } ?>
						<?php }?>
					<?php }?>
					</div>
				</form>
			</div>
		</div>
		
		<div class="row col-xs-12" style="padding: 10px; padding-top:0px;">
			<?php if($form_info['reporttype'] == "dailytransactions"){?>
				<div class="col-xs-offset-1 col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  PRINT DAILY TRANSACTIONS  " onclick="getReport4()"/>
				</div>
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  TICKETS & CARGOS SUMMARY  " onclick="getReport1()"/>
				</div>
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  BRANCH SUMMARY  " onclick="getReport2()"/>
				</div>
				<div class="col-xs-1">
					<input class="btn btn-sm btn-primary" type="button" value="  TXN DETAILS  " onclick="getReport3()"/>
				</div>
			<?php } else if($form_info['reporttype'] == "inventory"){?>
				<div class="col-xs-offset-4 col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  FIND AVAILABLE ITEMS  " onclick="getReport1()"/>
				</div>
				<!-- 				
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  FIND ITEMS TO BE ORDERED  " onclick="getReport2()"/>
				</div>
				 -->
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  HISTORY " onclick="getReport3()"/>
				</div>
				<?php } else if($form_info['reporttype'] == "officeinventory"){?>
				<div class="col-xs-offset-4 col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  FIND AVAILABLE ITEMS  " onclick="getReport1()"/>
				</div>
				<!-- 				
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  FIND ITEMS TO BE ORDERED  " onclick="getReport2()"/>
				</div>
				 -->
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  HISTORY " onclick="getReport3()"/>
				</div>
			<?php } else if($form_info['reporttype'] == "servicelog"){?>
				<div class="col-xs-offset-2 col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  GENERATE REPORT  " onclick="generateReport()"/>
				</div>
								
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  VEHICLE SUMMARY  " onclick="getReport2()"/>
				</div>
				
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  WORKINGDAYS VEHICLE SUMMARY " onclick="getReport3()"/>
				</div>
			<?php } else if($form_info['reporttype'] == "clientholidaysworking"){?>
				<div class="col-xs-offset-4 col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value="  GENERATE REPORT  " onclick="generateReport()"/>
				</div>
								
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" value=" SUMMARY  " onclick="getReport2()"/>
				</div>
			<?php } else { ?>
				<div class="col-xs-offset-2 col-xs-3">
				</div>
				<div class="col-xs-3">
					<input class="btn btn-sm btn-primary" type="button" id="genreportbtn" value="  GENERATE REPORT  " onclick="generateReport()"/>
				</div>
				<div class="col-xs-2">
				</div>
			<?php } ?>
		</div>
		
	</div>
</div>
	