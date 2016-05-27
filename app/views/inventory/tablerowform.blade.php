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

	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
	@stop
	@section('inline_css')
		<style>
			label {
			    font-weight: normal;
			    font-size: 13px;
			}
		</style>
	@stop	
			<div class="row"  ng-app="myApp" ng-controller="myCtrl">
				<div class="">
					<div class="col-xs-12">
					<h3 style="margin-top:-10px;" class="header smaller lighter blue">&nbsp;</h3>
						<div class="col-xs-4">
						<?php $form_fields = $form_info['form_fields'];?>	
						<?php foreach ($form_fields as $form_field) {?>
							<div class="col-xs-12" id="div_{{$form_field['name']}}">
							<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
							<div class="form-group" >
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-8">
									<input {{$form_field['readonly']}} ng-model="{{$form_field['name']}}"  type="{{$form_field['type']}}" id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
								</div>			
							</div>
							<?php } ?>
							<?php if($form_field['type'] === "empty" ){ ?>
							<div class="form-group" >
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-8">
									<label class="control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								</div>			
							</div>
							<?php } ?>
							<?php if($form_field['type'] === "hidden"){ ?>
							<div class="form-group">
								<div class="col-xs-8">
									<input type="{{$form_field['type']}}" ng-model="{{$form_field['name']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" >
								</div>			
							</div>
							<?php } ?>
							<?php if($form_field['type'] === "textarea"){ ?>				
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-8">
									<textarea {{$form_field['readonly']}} ng-model="{{$form_field['name']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}"></textarea>
								</div>			
							</div>
							<?php } ?>
							<?php if($form_field['type'] === "link"){ ?>				
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-8" style="padding-top: 7px;">
									<span><a href="nobill.png"  target="_blank" id="{{$form_field['name']}}" name="{{$form_field['name']}}" >Click here to see bill</a></span>
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
									<select class="{{$form_field['class']}}" ng-model="{{$form_field['name']}}" <?php if(isset($form_field['id'])) { echo 'id="'.$form_field['id'].'"'; } else { echo 'id="'.$form_field['name'].'"'; }?>  <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>  <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
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
								<div class="col-xs-8">
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
												<input name="{{$key}}" ng-model="{{$form_field['name']}}" value="YES" type="checkbox" class="ace">
												<span class="lbl">&nbsp;{{$key}} &nbsp;&nbsp;</span>
											</label>
										</div>
										<?php } ?>
									</div>
								</div>
							<?php } ?>						
						</div>
						<?php } ?>
						<div>
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> &nbsp; </label>
							<div class="col-xs-2"><input class="btn btn-xs" type="button" id="addrowbtn" onClick="addRow()" value="  ADD  "/></div>
							<div class="col-xs-2"><input class="btn btn-xs" type="button" id="updaterowbtn" onClick="updateRow()" value="  UPDATE  "/></div>
						</div>
						</div>
						<div class="col-xs-8">
							<table  class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
									<?php 
										$ths = $form_info['theads'];
										foreach ($ths as $th){
											echo "<th>".$th."</th>";
										}
									?>
									</tr>
								</thead>
								<tbody id="rowtable">
									<tr ng-repeat="vehicle in vehicles_text">
									</tr>
								</tbody>
							</table>
						</div>
					</div>	
				</div>
			</div>
	