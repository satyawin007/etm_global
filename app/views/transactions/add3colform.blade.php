<?php 
	use settings\AppSettingsController;
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
		<?php 
			if(!isset($form_info['iscontractfuel'])){
		?>
		<div class="col-xs-offset-1 col-xs-10" style="margin-top: 1%; margin-bottom: 1%">
			<div class="col-xs-6">
				<div class="form-group">
					<?php 
						$branches = AppSettingsController::getEmpBranches();
						$branches_arr = array();
						foreach ($branches as $branch){
							$branches_arr[$branch["id"]] = $branch["name"];
						}
					?>
					<?php $form_field = array("name"=>"branch", "content"=>"branch", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$branches_arr); ?>
					<label class="col-xs-5 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-7">
						<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
							<option value="">-- {{$form_field['name']}} --</option>
							<?php 
								foreach($form_field["options"] as $key => $value){
									echo "<option value='$key'>$value</option>";
								}
							?>
						</select>
					</div>			
				</div>	
			</div>
			<div class="col-xs-5">
				<div class="form-group">
					<label style="margin-left: 40px;" class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper("date");  echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-6">
						<input type="text" id="date" required="required" name="date" class="form-control date-picker" />
					</div>			
				</div>
			</div>			
			<div class="col-xs-1" >
				<div class="form-group">
					<label class="col-xs-0 control-label no-padding-right" for="form-field-1"> </label>
					<div class="col-xs-5" id="verify">
						<input type="button" class="btn btn-sm btn-primary" value="VERIFY" onclick="verifyDate()"/>
					</div>			
				</div>
			</div>
			<div class="col-xs-10" id="trantypebody" style="margin-top: 0px; margin-bottom: -10px">
				<div class="form-group">
					<label class="col-xs-3 control-label no-padding-right" for="form-field-1" style="margin-top: 10px"> TRANSACTION TYPE :  </label>
					<div class="col-xs-9">
						<div class="control-group row">
							<div class="radio inline">
								<label>
									<input name="form-field-radio" name="trantype"  type="radio" value="income" class="ace" onclick="showTranType(this.value)">
									<span class="lbl"> &nbsp;INCOME &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
							</div>

							<div class="radio inline">
								<label>
									<input name="form-field-radio" name="trantype"  type="radio" value="expense" class="ace" onclick="showTranType(this.value)">
									<span class="lbl"> &nbsp;EXPENSE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
							</div>

							<div class="radio inline">
								<label>
									<input name="form-field-radio" name="trantype" type="radio" value="fuel" class="ace" onclick="showTranType(this.value)">
									<span class="lbl"> &nbsp;FUEL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</label>
							</div>
						</div>
					</div>			
				</div>	
			</div>
			
			<div class="col-xs-6" id="incomebody" style="margin-top: 15px; margin-bottom: -10px">
				<div class="form-group">
					<?php 
						$parentId = \LookupTypeValues::where("name", "=", "INCOME")->get();
						$incomes = array();
						if(count($parentId)>0){
							$parentId = $parentId[0];
							$parentId = $parentId->id;
							$incomes =  \LookupTypeValues::where("parentId","=",$parentId)->get();
							
						}
						$incomes_arr = array();
						foreach ($incomes as $income){
							$incomes_arr[$income->id] = $income->name;
						}
						//$incomes_arr["999"] = "PREPAID RECHARGE";
					?>
					<?php $form_field = array("name"=>"income", "content"=>"Income TYpe", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$incomes_arr); ?>
					<label class="col-xs-5 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-7">
						<select class="{{$form_field['class']}}"  onChange="showForm(this.value)"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
							<option value="">-- {{$form_field['name']}} --</option>
							<?php 
								foreach($form_field["options"] as $key => $value){
									echo "<option value='$key'>$value</option>";
								}
							?>
						</select>
					</div>			
				</div>	
			</div>
			<div class="col-xs-6" id="expensebody" style="margin-top: 15px; margin-bottom: -10px">
				<div class="form-group" id="expensetypes">
					<?php 
						$parentId = \LookupTypeValues::where("name", "=", "EXPENSE")->get();
						$expenses = array();
						if(count($parentId)>0){
							$parentId = $parentId[0];
							$parentId = $parentId->id;
							$expenses =  \LookupTypeValues::where("parentId","=",$parentId)->get();
							
						}
						$expenses_arr = array();
						foreach ($expenses as $expense){
							$expenses_arr[$expense->id] = $expense->name;
						}
						/*$expenses_arr["998"] = "CREDIT SUPPLIER PAYMENT";
						$expenses_arr["997"] = "FUEL STATION PAYMENT";
						$expenses_arr["996"] = "LOAN PAYMENT";
						$expenses_arr["995"] = "RENT";
						$expenses_arr["994"] = "INCHARGE ACCOUNT CREDIT";
						//$expenses_arr["993"] = "PREPAID RECHARGE";
						$expenses_arr["992"] = "ONLINE OPERATORS";*/
						$expenses_arr["989"] = "VEHICLE RENEWAL";
					?>
					<?php $form_field = array("name"=>"expense", "content"=>"expense TYpe", "readonly"=>"",  "required"=>"required", "type"=>"select", "class"=>"form-control chosen-select", "options"=>$expenses_arr); ?>
					<label class="col-xs-5 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
					<div class="col-xs-7">
						<select class="{{$form_field['class']}}"  onChange="showForm(this.value)"  {{$form_field['required']}}  name="{{$form_field['name']}}" id="{{$form_field['name']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
							<option value="">-- {{$form_field['name']}} --</option>
							<?php 
								foreach($form_field["options"] as $key => $value){
									echo "<option value='$key'>$value</option>";
								}
							?>
						</select>
					</div>			
				</div>	
			</div>
		</div>
		<?php }?>
	
		<div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%">
		<div class="">
			<div class="">
				<div >
				<form style="padding-top:0px;" class="{{$form_info['class']}}" action="{{$form_info['action']}}" method="{{$form_info['method']}}" name="{{$form_info['name']}}"  id="{{$form_info['name']}}" enctype="multipart/form-data">
					<?php $form_fields = $form_info['form_fields'];?>
					<div>	
					<?php foreach ($form_fields as $form_field) {?>
						<div class="col-xs-6" style="margin-top: 15px; margin-bottom: -10px">
						<?php if($form_field['type'] === "text" || $form_field['type'] === "email" ||$form_field['type'] === "number" || $form_field['type'] === "password"){ ?>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right" for="form-field-1"> <?php echo strtoupper($form_field['content']); if($form_field['required']=="required") echo '<span style="color:red;">*</span>'; ?> </label>
							<div class="col-xs-7">
								<input {{$form_field['readonly']}} type="{{$form_field['type']}}" id="{{$form_field['name']}}" <?php if($form_field['required']=="required") echo 'required="true"'; ?> name="{{$form_field['name']}}" class="{{$form_field['class']}}" <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?>>
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
								<select class="{{$form_field['class']}}"  {{$form_field['required']}}  name="{{$form_field['name']}}" <?php if(isset($form_field['id'])) {echo ' id="'.$form_field['id'].'"';} else { echo ' id="'.$form_field['name'].'"';}?> <?php if(isset($form_field['action'])) { $action = $form_field['action'];  echo $action['type']."=".$action['script']; }?> <?php if(isset($form_field['multiple'])) { echo " multiple "; }?>>
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
					</div>
					<div id="formbody">	</div>
					<div id="addfields"></div>
					<div class="clearfix">
						<div class="col-md-offset-0 col-md-12 form-actions" style="margin: 0px">
							<div class="col-md-offset-4 col-md-5">
							<button id="submit" class="btn primary">
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
		</div>
	