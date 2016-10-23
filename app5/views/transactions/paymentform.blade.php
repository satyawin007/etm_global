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
		<?php $form_fields = $form_info['form_fields'];?>	
		<?php foreach ($form_fields as $form_field) {?>
			<div class="col-xs-6" >
			<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<input {{$form_field['readonly']}} type="{{$form_field['type']}}" id="{{$form_field['name']}}" <?php if($form_field['required']=="required") echo 'required="required"'; ?> name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
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
			
			<?php if($form_field['type'] === "file"){ ?>				
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<input type="file" id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="form-control file"/>
				</div>			
			</div>
			<?php } ?>
			
			<?php if($form_field['type'] === "checkboxslide"){ ?>
				<div class="form-group">
					<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-7" style="margin-top: 6px;">
						<input id="{{$form_field['name']}}" name="{{$form_field['name']}}"  class="ace ace-switch ace-switch-5" type="checkbox" />
						<span class="lbl"></span>
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
			<?php if($form_field['type'] === "checkbox"){ ?>
				<div class="form-group">
					<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-8">
						<?php 
						$options = $form_field["options"];
						foreach ($options as $key=>$value) {
						?>
						<div class="checkbox inline">
							<label>
								<input name="{{$key}}" id="{{$key}}" value="YES" type="checkbox" class="ace">
								<span class="lbl">&nbsp;{{$value}} &nbsp;&nbsp;</span>
							</label>
						</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>	
			
			<?php if($form_field['type'] === "select"){ ?>
			<div class="form-group">
				<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
				<div class="col-xs-7">
					<select class="{{$form_field['class']}}" {{$form_field['required']}} {{$form_field['readonly']}} {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
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
		<?php } ?>