@extends('masters.master')
	@section('inline_css')
		<style>
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
	
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
	@stop
	
	@section('bredcum')	
		<small>
			ADMINISTRATION
			<i class="ace-icon fa fa-angle-double-right"></i>
			TRANSACTION BLOCKING
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<div class="col-xs-offset-1 col-xs-10">
				<?php 
					$form_info = $values["form_info"]; 
					$gtime = 0;
					$parameter_id  = 0;
					$rec = Parameters::where("name","=","transaction_closing_time")->get();
					if(count($rec)>0){
						$rec = $rec[0];
						$parameter_id = $rec->id;
						$gtime = $rec->value;
					}
				?>
			</div>
			<div class="row col-xs-offset-1 col-xs-10">							
				<!-- PAGE CONTENT BEGINS -->
				<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group" style="width: 99%;">			
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
									<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
									&nbsp;{{strtoupper("Branch Data Entry - Daily Closing Time")}}
								</a>
							</h4>
						</div>
						<div class="panel-collapse collapse in" id="TEST">
							<div class="panel-body" style="padding: 0px">
								<div class="col-xs-offset-1 col-xs-10" style="margin-top: 1%; margin-bottom: 1%">
									<div class="col-xs-offset-2 col-xs-6">
										<label class="col-xs-5 control-label no-padding-right" for="form-field-1"> {{strtoupper("Daily Closing Time")}}<span style="color:red;">*</span> </label>
										<div class="col-xs-7">
											<select class="form-control chosen-select" required="" name="globaltime" id="globaltime" >
												<option value="1" <?php if($gtime == 1) echo "selected"; ?> >01:00AM (Night)</option>
												<option value="2" <?php if($gtime == 2) echo "selected"; ?>>02:00AM (Night)</option>
												<option value="3" <?php if($gtime == 3) echo "selected"; ?>>03:00AM (Early Morning)</option>
												<option value="4" <?php if($gtime == 4) echo "selected"; ?>>04:00AM (Early Morning)</option>
												<option value="5" <?php if($gtime == 5) echo "selected"; ?>>05:00AM (Early Morning)</option>
												<option value="6" <?php if($gtime == 6) echo "selected"; ?>>06:00AM (Morning)</option>
												<option value="7" <?php if($gtime == 7) echo "selected"; ?>>07:00AM (Morning)</option>
												<option value="8" <?php if($gtime == 8) echo "selected"; ?>>08:00AM (Morning)</option>
												<option value="9" <?php if($gtime == 9) echo "selected"; ?>>09:00AM (Morning)</option>
												<option value="10" <?php if($gtime == 10) echo "selected"; ?>>10:00AM (Morning)</option>
												<option value="11" <?php if($gtime == 11) echo "selected"; ?>>11:00AM (Morning)</option>
												<option value="12" <?php if($gtime == 12) echo "selected"; ?>>12:00AM (Afternoon)</option>
												<option value="13" <?php if($gtime == 13) echo "selected"; ?>>01:00PM (Afternoon)</option>
												<option value="14" <?php if($gtime == 14) echo "selected"; ?>>02:00PM (Afternoon)</option>
												<option value="15" <?php if($gtime == 15) echo "selected"; ?>>03:00PM (Afternoon)</option>
												<option value="16" <?php if($gtime == 16) echo "selected"; ?>>04:00PM (Evening)</option>
												<option value="17" <?php if($gtime == 17) echo "selected"; ?>>05:00PM (Evening)</option>
												<option value="18" <?php if($gtime == 18) echo "selected"; ?>>06:00PM (Evening)</option>
												<option value="19" <?php if($gtime == 19) echo "selected"; ?>>07:00PM (Evening)</option>
												<option value="20" <?php if($gtime == 20) echo "selected"; ?>>08:00PM (Evening)</option>
												<option value="21" <?php if($gtime == 21) echo "selected"; ?>>09:00PM (Night)</option>
												<option value="22" <?php if($gtime == 22) echo "selected"; ?>>10:00PM (Night)</option>
												<option value="23" <?php if($gtime == 23) echo "selected"; ?>>11:00PM (Night)</option>
												<option value="24" <?php if($gtime == 24) echo "selected"; ?>>12:00PM (NIght)</option>
											</select>
										</div>			
									</div>				
									<div class="col-xs-2">
										<div class="form-group">
											<label class="col-xs-0 control-label no-padding-right" for="form-field-1"> </label>
											<div class="col-xs-5" id="verify">
												<input type="button" class="btn btn-sm btn-primary" value="SAVE CLOSING TIME" onclick="saveClosingTime({{$parameter_id}})">
											</div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- PAGE CONTENT BEGINS -->
				<div id="accordion1" class="col-xs-offset-0 col-xs-12 accordion-style1 panel-group" style="width: 99%;">			
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#TEST">
									<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
									&nbsp;{{strtoupper("Branch Data Entry Open for a Day")}}
								</a>
							</h4>
						</div>
						<div class="panel-collapse collapse in" id="TEST">
							<div class="panel-body" style="padding: 0px">
								<form action="transactionblocking">
									<div class="col-xs-offset-1 col-xs-10" style="margin-top: 1%; margin-bottom: 1%">
										<div class="col-xs-offset-1 col-xs-6">
											<label class="col-xs-5 control-label no-padding-right" for="form-field-1"> {{strtoupper("Data Entry Date")}}<span style="color:red;">*</span> </label>
											<div class="col-xs-7">
												<?php $date = ""; if(isset($values["date"])) $date =  $values["date"]; ?>
												<input type="text" id="date" required="" name="date" value="{{$date}}" class="form-control date-picker">
											</div>			
										</div>				
										<div class="col-xs-2">
											<div class="form-group">
												<label class="col-xs-0 control-label no-padding-right" for="form-field-1"> </label>
												<div class="col-xs-5" id="verify">
													<input type="submit" class="btn btn-sm btn-primary" value="SHOW BRANCH DATA ENTRY STATUS" onclick="showDataEntryStatus()">
												</div>			
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row " style="max-width: 99%; margin-left:0px;">
					<div class="col-xs-offset-0 col-xs-12">
						<div class="widget-box col-xs-12">
							<div class="widget-header">
								<h5 class="widget-title">{{strtoupper("Branch Data Entry Opening - Special Case")}}</h5>
							</div>
							<div class="widget-body">
								<?php 
									$values = Input::All();
								 	if(isset($values["date"])){
								?>
								<div class="widget-main no-padding">
									<form style="padding-top:20px;" class="form-horizontal" action="edittransactionblocking" method="post" name="addcity" id="addcity">
										<input type="hidden" name="date" value="{{$values['date']}}"/>
										<div>
											<?php 
											$sql = "SELECT officebranch.id, officebranch.name, blockdataentry.status
												FROM officebranch
												LEFT JOIN blockdataentry
												ON officebranch.id=blockdataentry.branchId and
													blockdataentry.dataEntryDate = '".date("Y-m-d",strtotime($values["date"]))."' ORDER BY officebranch.name";
												$recs = \DB::select(\DB::raw($sql));
												foreach ($recs as $rec){
											?>
											<div class="col-xs-6">
												<input class="getbranches" type="hidden" name="branch[]" id="<?php  echo $rec->id; ?>" value="<?php  echo $rec->id; ?>" />
												<div class="form-group">
													<label class="col-xs-6 control-label no-padding-right" for="form-field-1" style="background:#FEFAFA;color:<?php if($rec->status=='OPEN') echo 'red'; else echo 'green' ?>;font-weight:bold;font-size:15px;"><?php echo $rec->name; ?></label>
													<div class="col-xs-6">
														<select name="status[]" id="status<?php echo $rec->id; ?>" class='form-control'  style="width:100%;">
															<option value="CLOSE" <?php if($rec->status=='CLOSE') echo 'selected'; ?> >CLOSE</option>
															<option value="OPEN" <?php if($rec->status=='OPEN') echo 'selected'; ?> >OPEN</option>
														</select>
													</div>			
												</div>
											</div>
											<?php }?>
											<div class="clearfix">
												<div class="col-md-offset-0 col-md-12 form-actions" style="margin: 0px">
													<div class="col-md-offset-4 col-md-5">
														<button id="submit" class="btn primary" type="submit">
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
										</div>
									</form>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>
		</div>
				
		<div id="edit" class="modal" tabindex="-1">
			<div class="modal-dialog" style="width: 80%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="blue bigger">Please fill the following form fields</h4>
					</div>
	
					<div class="modal-body" id="modal_body">
					</div>
	
					<div class="modal-footer">
						<button class="btn btn-sm" data-dismiss="modal">
							<i class="ace-icon fa fa-times"></i>
							Close
						</button>
					</div>
				</div>
			</div>
		</div><!-- PAGE CONTENT ENDS -->
		
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/dataTables/jquery.dataTables.js"></script>
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
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});

			$('.chosen-select').focus(function(e){
			    e.preventDefault();
			});

			function saveClosingTime(rid){
				name = "transaction_closing_time";
				value = $("#globaltime").val();
				url = "editparameter?id="+rid+"&name="+name+"&value="+value;
				$.ajax({
			      url: url,
			      success: function(data) {
			    	  if(data=="success"){
			    		  bootbox.confirm("operation completed successfully!", function(result) {});
				   	  }
			    	  if(data=="fail"){
			    		  bootbox.confirm("operation could not be completed successfully!", function(result) {});
				   	  }
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
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.confirm('".Session::pull('message')."', function(result) {});";
				}
			?>

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
			
		</script>
	@stop