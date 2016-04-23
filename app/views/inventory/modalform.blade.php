<div id="{{$modal['name']}}" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Please fill the following form fields</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
					<form name="{{$modal['action']}}" id="{{$modal['name']}}" class="form-horizontal" action="{{$modal['action']}}" method="post">	
						<?php $form_fields = $modal['form_fields'];?>	
						<?php foreach ($form_fields as $form_field) {?>
							<?php if($form_field['type'] === "text" || $form_field['type'] === "email" || $form_field['type'] === "password"){ ?>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
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
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<textarea {{$form_field['readonly']}} id="{{$form_field['name']}}" name="{{$form_field['name']}}" class="{{$form_field['class']}}"></textarea>
								</div>			
							</div>
							<?php } ?>
							
							<?php if($form_field['type'] === "select"){ ?>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-7">
									<select class="{{$form_field['class']}}" name="{{$form_field['name']}}" id="{{$form_field['name']}}"  <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
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
							<?php if($form_field['type'] === "checkbox"){ ?>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
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
							<?php if($form_field['type'] === "radio"){ ?>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
								<div class="col-xs-8">
									<?php 
										$options = $form_field["options"];
										foreach ($options as $key=>$value) {
									?>
									<div class="radio inline">
										<label>
											<input name="{{$form_field['content']}}" id="{{$value}}" value="{{$value}}" type="radio" class="ace">
											<span class="lbl">&nbsp;{{$value}} &nbsp;&nbsp;</span>
										</label>
									</div>
									<?php } ?>
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

						</form>
					</div>
				</div>
			</div>

			
		</div>
	</div>
</div><!-- PAGE CONTENT ENDS -->