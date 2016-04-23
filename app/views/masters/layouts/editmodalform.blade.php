						<?php $form_fields = $modal['form_fields'];?>	
						<?php foreach ($form_fields as $form_field) {?>
							<?php if($form_field['type'] === "text" || $form_field['type'] === "email" || $form_field['type'] === "password"){ ?>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
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
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}"></textarea>
								</div>			
							</div>
							<?php } ?>
							<?php if($form_field['type'] === "radio"){ ?>				
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
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
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<select class="{{$form_field['class']}}" name="{{$form_field['name']}}" {{$form_field['multiple']}} <?php if(isset($form_field['id'])) { echo " id='".$form_field['id']."' " ;}?> <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>  >
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
						
						<?php } ?>
						
						<div class="modal-footer">
							<button class="btn btn-sm" data-dismiss="modal">
								<i class="ace-icon fa fa-times"></i>
								Cancel
							</button>
			
							<button class="btn btn-sm btn-primary">
								<i class="ace-icon fa fa-check"></i>
								Save
							</button>
						</div>

