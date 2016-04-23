<?php $form_fields = $form_info['form_fields'];?>	
<?php foreach ($form_fields as $form_field) {?>
	<div class="col-xs-6">
	<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
	<div class="form-group" >
		<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
		<div class="col-xs-8">
			<input {{$form_field['readonly']}} type="{{$form_field['type']}}" id="{{$form_field['name']}}" {{$form_field['required']}} name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
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
			<input type="{{$form_field['type']}}" id="{{$form_field['name']}}" name="{{$form_field['name']}}" value="{{$form_field['value']}}" >
		</div>			
	</div>
	<?php } ?>
	<?php if($form_field['type'] === "textarea"){ ?>				
	<div class="form-group">
		<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
		<div class="col-xs-8">
			<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}"></textarea>
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
			<select class="{{$form_field['class']}}" {{$form_field['required']}} name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>  <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
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
						<input name="{{$key}}" value="YES" type="checkbox" class="ace">
						<span class="lbl">&nbsp;{{$key}} &nbsp;&nbsp;</span>
					</label>
				</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>							
<?php } ?>
	