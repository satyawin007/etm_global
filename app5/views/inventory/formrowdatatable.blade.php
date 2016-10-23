@extends('masters.master')
	@section('inline_css')
		<style>
			label {
			    font-weight: normal;
			    font-size: 13px;
			}
			.chosen-container{
			  width: 100% !important;
			}
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
			.col-xs-12 {
				margin-bottom : 10px;
			}
		</style>
	@stop
	
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
	@stop
	
	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			STOCK & INVENTORY 
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<form action="addestimatepurchaseorder" name="addform" id="addform">
					<div class="form-group col-xs-offset-3 col-xs-3" style="margin-top: 15px; margin-bottom: -10px">
							<div class="form-group">
								<label class="col-xs-2 control-label no-padding-right" for="form-field-1"> Branch<span style="color:red;">*</span> </label>
								<div class="col-xs-10">
									<!-- <input  type="text" id="branch"  name="branch" class="form-control" > -->
									<select id="branch"  name="branch" class="form-control chosen-select" >
										<option value="">-- select branch--</option>
									<?php 
										$branch_arr = array();
										$branches = OfficeBranch::where("status","=","ACTIVE")->get();
										foreach ($branches as $branch){
											$branch_arr[$branch->id]=$branch->name;
											echo '<option value="'.$branch->id.'">'.$branch->name.'</option>';
										}
									?>
									</select>
								</div>			
							</div>
					</div>
					<div class="form-group col-xs-3" style="margin-top: 15px; margin-bottom: -10px">
							<div class="form-group">
								<label class="col-xs-2 control-label no-padding-right" for="form-field-1"> Date<span style="color:red;">*</span> </label>
								<div class="col-xs-10">
									<input  type="text" id="date"  name="date" class="form-control date-picker" >
								</div>			
							</div>
					</div>
					<input type="hidden" id="jsondata" name="jsondata">
				</form>
			<div class="col-xs-offset-0 col-xs-12">
				<?php $form_info = $values["form_info"];?>
				<?php $jobs = Session::get("jobs");?>
				<?php if(($form_info['action']=="addstate" && in_array(206, $jobs)) || 
						($form_info['action']=="addestimatepurchaseorder" && in_array(334, $jobs))
					  ){ ?>
					@include("inventory.tablerowform",$form_info)
				<?php } ?>
			</div>
			
			<div class="clearfix" >
						<div class="col-md-12" style="background-color: #E6DFDF;border-top: 2px solid #D2CDCD; margin-top: 10px;">
						<div class="col-md-offset-4 col-md-8" style="margin-top: 2%; margin-bottom: 1%">
							<button class="btn primary" id="submit" onClick="postData()">
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
				
		<div class="row ">
		<div class="col-xs-offset-0 col-xs-12">
			<h3 class="header smaller lighter blue" style="font-size: 15px; font-weight: bold;margin-bottom: 10px;">MANAGE {{$values["bredcum"]}}</h3>		
			<?php if(!isset($values['entries'])) $values['entries']=10; if(!isset($values['branch'])) $values['branch']=0; if(!isset($values['page'])) $values['page']=1; ?>
			<div class="clearfix">
				<div class="pull-left">
					
					<form action="{{$values['form_action']}}" name="paginate" id="paginate">
					<?php 
					if(isset($values['selects'])){
						$selects = $values['selects'];
						foreach($selects as $select){
						?>
						<label>Branch </label>
						<select class="form-control-inline" id="{{$select['name']}}" style="height: 33px; padding-top: 0px;" name="{{$select["name"]}}" onChage="paginate(1)">
							<?php 
								foreach($select["options"] as $key => $value){									
									$option =  "<option value='".$key."' ";
									if($key == $values[$select['name']]){
										$option = $option." selected='selected' ";
									}
									$option = $option.">".$value."</option>";
									echo $option;
								}
							?>
						</select> &nbsp; &nbsp;
					<?php }} ?>
					<input type="hidden" name="page" id="page" />
					<?php 
					if(isset($values['links'])){
						$links = $values['links'];
						foreach($links as $link){
							echo "<a class='btn btn-white btn-success' href=".$link['url'].">".$link['name']."</a> &nbsp; &nbsp; &nbsp";
						}
					}
					?>
					<?php echo "<input type='hidden' name='action' value='".$values['action_val']."'/>"; ?>				
					</form>
				</div>
				<div class="pull-right tableTools-container"></div>
			</div>
			<div class="row">
				<div class="col-xs-offset-4 col-xs-7">
					<div class="col-xs-7">
						<select name="clientid" id="clientid" class="formcontrol chosen-select">
							<option value="0">ALL</option>
						<?php 
								$branch_arr = array();
								$branches = OfficeBranch::where("status","=","ACTIVE")->get();
								foreach ($branches as $branch){
									$branch_arr[$branch->id]=$branch->name;
									echo '<option value="'.$branch->id.'">'.$branch->name.'</option>';
								}
						?>
						</select>
					</div>
					<div class="col-xs-4">
						<button class="btn btn-xs btn-primary" id="getbtn">&nbsp;&nbsp;GET&nbsp;&nbsp;</button>
					</div>
				</div>
			</div>
			<div class="table-header" style="margin-top: 10px;">
				Results for "{{$values['bredcum']}}"				 
				<div style="float:right;padding-right: 15px;padding-top: 6px;"><a style="color: white;" href="{{$values['home_url']}}"><i class="ace-icon fa fa-home bigger-200"></i></a> </div>				
			</div>
			<!-- div.table-responsive -->
			<!-- div.dataTables_borderWrap -->
			<div>
				<table id="dynamic-table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<?php 
								$theads = $values['theads'];
								foreach($theads as $thead){
									echo "<th>".strtoupper($thead)."</th>";
								}
							?>
						</tr>
					</thead>
				</table>								
			</div>
		</div>
		</div>

		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal);
		<?php }} ?>
		
		<div id="edit" class="modal" tabindex="-1">
			<div class="modal-dialog" style="width: 90%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="blue bigger">EDIT {{$values['bredcum']}}</h4>
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
		<script src="../assets/js/angular-1.5.4/angular.min.js"></script>
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
	
		<!-- inline scripts related to angular JS-->
		<script>
			submit_data = "false";
			var app = angular.module('myApp', []);
			app.controller('myCtrl', function($scope, $http) {
				$scope.ids = ['item', 'manufacturer', 'creditsupplier'];
				$scope.vars = ['quantity','unitprice', 'remarks' ];
				$scope.vehicles_text = [];
				//alert($scope.ids[0]);
				$scope.addRow = function(){
					alert("test");
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						if(entry != "item"){
							$scope[entry] = text;
						}
					});	
					
					if(typeof $scope.item === "undefined" || typeof $scope.quanity === "undefined" || $scope.item === "" ) {
						return;
					}
					alert("DSF");

					text_arr = [];
					veh_arr = {};
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").text();
						val = $("#"+entry+" option:selected").val();
						veh_arr[entry] = val;
						$("#"+entry).find('option:selected').removeAttr("selected");
						if(val==""){
							text="";
						}
						text_arr[entry] = text;
						$scope[entry] = '';
					});
					$scope.vars.forEach(function(entry) {
						text_arr[entry] = $scope[entry];
						veh_arr[entry] = $scope[entry];
						$scope[entry] = '';
					});

					$scope.vehicles_text.unshift(text_arr);
					$scope.vehicles.unshift(veh_arr);
					$('.chosen-select').trigger("chosen:updated");
					alert("END");
				};

				$scope.editRow = function(vehicle){	
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					var comArr1 = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === vehicle ) {
							index = i;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Something gone wrong" );
						return;
					}
					$scope.vars.forEach(function(entry) {
						$scope[entry]=comArr1[i][entry];
					});	
					$scope.ids.forEach(function(entry) {
						$("#"+entry+" option").each(function() {   this.selected =(this.text == comArr[i][entry])});
						$("#"+entry).find('option:selected').attr("selected", "selected"); 
						$scope[entry]=comArr1[i][entry];
					});	
					$('.chosen-select').trigger("chosen:updated");	
				};

				$scope.updateRow = function(){	
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").val();
						text = text.replace("? string:", "");
						text = text.replace(" ?", "");
						if(entry != "vehicle"){
							$scope[entry] = text;
						}
					});	
					if(typeof $scope.item === "undefined" || typeof $scope.qty === "undefined" || $scope.item === "" || $scope.quantity) {
						return;
					}	
					tempdata = [];
					var index = -1;		
					var comArr = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === $scope.vehicle ) {
							index = i;
							$scope.ids.forEach(function(entry) {
								text = $("#"+entry+" option:selected").text();
								$("#"+entry).find('option:selected').removeAttr("selected");
								if(entry != "item"){
									if(text != ""){
										$scope.vehicles_text[index][entry] = text;
									}
									$scope.vehicles[index][entry] = $scope[entry];
									$scope[entry] = '';
								}
							});
							$scope.vars.forEach(function(entry) {
								$scope.vehicles_text[index][entry] = $scope[entry];
								$scope.vehicles[index][entry] = $scope[entry];
								$scope[entry] = '';
							});
							break;
						}
					}
					if( index === -1 ) {
						alert( "Vehicle can not be updated / Something gone wrong" );
						return;
					}
					alert("updated successfully");
					$('.chosen-select').trigger("chosen:updated");
				};
				
				$scope.removeRow = function(vehicle){	
					var index = -1;		
					var comArr = eval( $scope.vehicles_text );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === vehicle ) {
							index = i;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Something gone wrong" );
						return;
					}
					$scope.vehicles.splice( index, 1 );	
					$scope.vehicles_text.splice( index, 1 );		
				};

				$scope.postData = function() {
					if(submit_data=="false"){
						return;
					}
					$('#jsondata').val(JSON.stringify($scope.vehicles));
					$.ajax({
                        url: "{{$form_info['name']}}",
                        type: "post",
                        data: $("#{{$form_info['name']}}").serialize(),
                        success: function(response) {
                        	response = jQuery.parseJSON(response);	
                            if(response.status=="success"){
                            	bootbox.alert(response.message, function(result) {});
                            	resetForm("{{$form_info['name']}}");
                            	$scope.vehicles= [];	
            					$scope.vehicles_text = [];		
                            }
                            if(response.status=="fail"){
                            	bootbox.alert(response.message, function(result) {});
                            }
                        }
                    });
				};

				function resetForm(formid)
			    { 
		            form = $('#'+formid);
		            element = ['input','select','textarea'];
		            for(i=0; i<element.length; i++) 
		            {
	                    $.each( form.find(element[i]), function(){  
                            switch($(this).attr('class')) {
                              case 'form-control chosen-select':
                              	$(this).find('option:first-child').attr("selected", "selected"); 
                                break;
                            }
                            switch($(this).attr('type')) {
                            case 'text':
                            case 'select-one':
                            case 'textarea':
                            case 'hidden':
                            case 'file':
                            	$(this).val('');
                              break;
                            case 'checkbox':
                            case 'radio':
                            	$(this).attr('checked',false);
                              break;
                           
                          }
	                    });
		            }
		            $('.chosen-select').trigger("chosen:updated");	
			    }
			});
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$("#entries").on("change",function(){paginate(1);});
	
			function modalEditEstimatePurchaseOrder(id){
				//$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				url = "editestimatepurchaseorder?id="+id;
				var ifr=$('<iframe />', {
		            id:'MainPopupIframe',
		            src:url,
		            style:'seamless="seamless" scrolling="no" display:none;width:100%;height:423px; border:0px solid',
		            load:function(){
		                $(this).show();
		            }
		        });
	    	    $("#modal_body").html(ifr);
			}
			
			function modalEditClient(id, name, code, city, state, status){
				$("#clientname1").val(name);				
				$("#clientcode1").val(code);
				$("#statename1 option").each(function() {this.selected = (this.text == state); });
				$("#cityname1 option").each(function() {this.selected = (this.text == city); });
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditDistrict(id, name, code, state, status){
				$("#districtname1").val(name);				
				$("#districtcode1").val(code);
				$("#statename1 option").each(function() {this.selected = (this.text == state); });
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			$("#getbtn").on("click",function(){
				clientid = $("#clientid").val();
				alert(clientid);
				myTable.ajax.url("getinventorydatatabledata?name=estimatepurchaseorder&branchid="+clientid).load();
			})
			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
				var statename = $("#statename").val();
				if(statename != undefined && statename ==""){
					alert("Please select statename");
					return false;
				}
				var districtname = $("#districtname").val();
				if(districtname != undefined && districtname ==""){
					alert("Please select districtname");
					return false;
				}
				var cityname = $("#cityname").val();
				if(cityname != undefined && cityname ==""){
					alert("Please select cityname");
					return false;
				}
				var clientname = $("#clientname").val();
				if(clientname != undefined && clientname ==""){
					alert("Please select clientname");
					return false;
				}
				var depot = $("#depot").val();
				if(depot != undefined && depot ==""){
					alert("Please select depot");
					return false;
				}
				var route = $("#route").val();
				if(route != undefined && route ==""){
					alert("Please select route");
					return false;
				}
				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}
				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}
				noofvehicles  = $("#noofvehicles").val();
				if(noofvehicles != undefined && noofvehicles ==""){
					alert("Please enter no of vehicles");
					return false;
				}
				
				submit_data="true";
				return false;
				
				//$("#{{$form_info['name']}}").submit();
			});

			$("#type").on("change",function(){
				if(this.value != ""){
					window.location.replace('lookupvalues?type='+this.value);
				}
			});

			$("#type").on("change",function(){
				if(this.value != ""){
					window.location.replace('lookupvalues?type='+this.value);
				}
			});

			$("#provider").on("change",function(){
				val = $("#provider option:selected").html();
				window.location.replace('serviceproviders?provider='+val);
			});

			function changeState(val){
				$.ajax({
			      url: "getcitiesbystateid?id="+val,
			      success: function(data) {
			    	  $("#cityname").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			}

			function getManufacturers(id){
				$("#div_itemnumbers").hide();
				$("#div_alertdate").hide();
				$("#div_itemactions").hide();
				$("#qty").attr("readonly",false);
				$.ajax({
			      url: "getmanufacturers?itemid="+id,
			      success: function(data) {
				      //alert(data);
			    	  var obj = JSON.parse(data);
			    	  if(obj.itemnumberstatus=="Yes"){
			    		  $("#qty").attr("readonly",true);
			    		  $("#div_itemnumbers").show();
			    	  }
			    	  $("#manufacturer").html(obj.manufactures);
			    	  $('.chosen-select').trigger('chosen:updated');
			      },
			      type: 'GET'
			   });
			}

			function changeCity(val){
				$.ajax({
			      url: "getdepotsbycityid?id="+val,
			      success: function(data) {
			    	  $("#depot").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			   });
			}
			
			<?php 
				if(Session::has('message')){
					echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
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

			//or change it into a date range picker
			$('.input-daterange').datepicker({autoclose:true,todayHighlight: true});

			$('.input-mask-phone').mask('(999) 999-9999');
			
			var myTable = null;
			jQuery(function($) {		
				//initiate dataTables plugin
				myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)

				//.wrap("<div id='tableData' style='width:300px; overflow: auto;overflow-y: hidden;-ms-overflow-y: hidden; position:relative; margin-right:5px; padding-bottom: 15px;display:block;'/>"); 
		
				.DataTable( {
					bJQueryUI: true,
					"bPaginate": true, "bDestroy": true,
					"bDestroy": true,
					bInfo: true,
					"aoColumns": [
					  <?php $cnt=count($values["theads"]); for($i=0; $i<$cnt; $i++){ echo '{ "bSortable": false },'; }?>
					],
					"aaSorting": [],
					oLanguage: {
				        sProcessing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-250"></i>'
				    },
					"bProcessing": true,
			        "bServerSide": true,
					"ajax":{
		                url :"getcontractsdatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
		                type: "post",  // method  , by default get
		                error: function(){  // error handling
		                    $(".employee-grid-error").html("");
		                    $("#dynamic-table").append('<tbody class="employee-grid-error"><tr>No data found in the server</tr></tbody>');
		                    $("#employee-grid_processing").css("display","none");
		 
		                }
		            },
			
					//"sScrollY": "500px",
					//"bPaginate": false,
					"sScrollX" : "true",
					//"sScrollX": "300px",
					//"sScrollXInner": "120%",
					"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
			
					//"iDisplayLength": 50
			
			
					select: {
						style: 'multi'
					}
			    } );
			
				
				
				$.fn.dataTable.Buttons.swfPath = "../assets/js/dataTables/extensions/buttons/swf/flashExport.swf"; //in Ace demo ../assets will be replaced by correct assets path
				$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
				
				/*new $.fn.dataTable.Buttons( myTable, {
					buttons: [
					  {
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					  },
					  {
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: false,
						message: 'This print was produced using the Print button for DataTables'
					  }		  
					]
				} );
				myTable.buttons().container().appendTo( $('.tableTools-container') );
				*/
				
				//style the message box
				var defaultCopyAction = myTable.button(1).action();
				myTable.button(1).action(function (e, dt, button, config) {
					defaultCopyAction(e, dt, button, config);
					$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
				});
				
				
				var defaultColvisAction = myTable.button(0).action();
				myTable.button(0).action(function (e, dt, button, config) {
					
					defaultColvisAction(e, dt, button, config);
					
					
					if($('.dt-button-collection > .dropdown-menu').length == 0) {
						$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
					}
					$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
				});
			
				////
			
				setTimeout(function() {
					$($('.tableTools-container')).find('a.dt-button').each(function() {
						var div = $(this).find(' > div').first();
						if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
						else $(this).tooltip({container: 'body', title: $(this).text()});
					});
				}, 500);
				
				
				
				
				
				myTable.on( 'select', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
					}
				} );
				myTable.on( 'deselect', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
					}
				} );
			
			
			
			
				/////////////////////////////////
				//table checkboxes
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
				
				//select/deselect all rows according to table header checkbox
				$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$('#dynamic-table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) myTable.row(row).select();
						else  myTable.row(row).deselect();
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(!this.checked) myTable.row(row).deselect();
					else myTable.row(row).select();
				});
			
			
			
				$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});
				
				
				
				//And for the first simple table, which doesn't have TableTools or dataTables
				//select/deselect all rows according to table header checkbox
				var active_class = 'active';
				$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
						else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#simple-table').on('click', 'td input[type=checkbox]' , function(){
					var $row = $(this).closest('tr');
					if(this.checked) $row.addClass(active_class);
					else $row.removeClass(active_class);
				});
			
				
			
				/********************************/
				//add tooltip for small view action buttons in dropdown menu
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				
				//tooltip placement on right or left
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
				$('<button style="margin-top:-5px;" class="btn btn-minier btn-primary" id="refresh"><i style="margin-top:-2px; padding:6px; padding-right:5px;" class="ace-icon fa fa-refresh bigger-110"></i></button>').appendTo('div.dataTables_filter');
				$("#refresh").on("click",function(){ myTable.search( '', true ).draw(); });
			});

			var ids = ['item', 'manufacturer', 'creditsupplier'];
			var vars = ['quantity','unitprice', 'remarks',"amount"];
			var entities_text = [];
			var entities = [];
			var hide_fields_text = [];
			var condition_elements = ['item','quantity'];
			var rowid=0;
			var editrowid=-1;
			var submit_data=false;
			
			function addRow(){
				//alert("in addRow "+condition_elements);
				/* ids.forEach(function(entry) {
					text = $("#"+entry+" option:selected").val();
				}); */
				add_condition = false;	
				var isReturn = false;
				condition_elements.forEach(function(entry) {
					itemm_val = $("#"+entry).val();
					if(typeof itemm_val === "undefined" || itemm_val == ""){
						alert("select "+entry);
						isReturn=true;
					}
					else if(entry=="qty" && itemm_val==0){
						alert("select "+entry);
						isReturn=true;
					}
				});
				if(isReturn){
					return;
				}
				text_arr = new Array();
				veh_arr = new Array();
				ids.forEach(function(entry) {
					text = $("#"+entry+" option:selected").text();
					if(entry=="itemnumbers"){
						text = "";
						$('#itemnumbers option:selected').each(function(){ 
							text = text+$(this).text()+","; 
						});
					}
					val = $("#"+entry+" option:selected").val();
					veh_arr[entry] = val;
					$("#"+entry).find('option:selected').removeAttr("selected");
					if(val==""){
						text="";
					}
					text_arr[entry] = text;
				});
				vars.forEach(function(entry) {
					text_arr[entry] = $("#"+entry).val();
					veh_arr[entry] = $("#"+entry).val();
					$("#"+entry).val("");
				});
				text_arr["rowid"]=rowid;
				rowid++;
				entities_text.unshift(text_arr);
				entities.unshift(veh_arr);
				$('.chosen-select').trigger("chosen:updated");
				drawTable()
			}

			function drawTable(){
				//alert("indraw Table: "+entities_text.length);
				table_data = "";
				comArr = entities_text;
				total = 0;
				for(i=0; i<entities_text.length; i++){
					table_data = table_data+"<tr>";
					ids.forEach(function(entry) {
						table_data = table_data+"<td>"+entities_text[i][entry]+"</td>"
					});
					vars.forEach(function(entry) {
						qty = 0;
						if(entry == "amount"){
							total = (Number(entities_text[i]['unitprice']) * Number(entities_text[i]['quantity']));
							table_data = table_data+"<td>"+total+"</td>"
						}
						else{
							table_data = table_data+"<td>"+entities_text[i][entry]+"</td>"
						}
					});
					table_data = table_data+"<td>"+
											'<span   style="margin:2px; color: #428bca" id="editrowbtn" onclick="editRow(\''+entities_text[i].rowid+'\')"><i class="ace-icon fa fa-pencil-square-o bigger-150"></i> </span>&nbsp;'+
											'<span   style="margin:2px;color: #d12723" id="removerowbtn" onclick="removeRow(\''+entities_text[i].rowid+'\')"><i class="ace-icon fa fa-trash-o bigger-150"></i></span>'
										+"</td>";
					table_data = table_data+"</tr>";
				}
				$("#rowtable").html(table_data);
			}
	
			function editRow(rowid1){	
				var index = -1;		
				var comArr = eval( entities_text );
				var comArr1 = eval( entities );
				for( var i = 0; i < comArr.length; i++ ) {
					//alert("editrow : "+comArr[i].rowid+" - "+rowid1);
					if( comArr[i].rowid == rowid1 ) {
						index = i;
						editrowid = rowid1;
						break;
					}
				}
				if( index === -1 ) {
					alert( "Something gone wrong" );
					return;
				}
				vars.forEach(function(entry) {
					$("#"+entry).val(comArr1[index][entry]);
				});	
				ids.forEach(function(entry) {
					$("#"+entry+" option").each(function() {   this.selected =(this.value == comArr1[index][entry])});
					$("#"+entry).find('option:selected').attr("selected", "selected"); 
				});	
				$('.chosen-select').trigger("chosen:updated");	
			};
	
			function updateRow(){	
				tempdata = [];
				var index = -1;		
				var comArr = eval( entities_text );
				for( var i = 0; i < comArr.length; i++ ) {
					if( comArr[i].rowid == editrowid ) {
						index = i;
						ids.forEach(function(entry) {
							text = $("#"+entry+" option:selected").text();
							if(entry != "item"){
								if(text != ""){
									entities_text[index][entry] = text;
								}
								entities[index][entry] = $("#"+entry).val();
							}
							$("#"+entry).find('option:selected').removeAttr("selected");
						});
						vars.forEach(function(entry) {
							entities_text[index][entry] = $("#"+entry).val();
							entities[index][entry] = $("#"+entry).val();
							$("#"+entry).val("");
						});
						break;
					}
				}
				if( index === -1 ) {
					alert( "Vehicle can not be updated / Something gone wrong" );
					return;
				}
				alert("updated successfully");
				$('.chosen-select').trigger("chosen:updated");
				drawTable()	
			};
			
			function removeRow(rowid1){	
				var index = -1;		
				var comArr = eval(entities_text);
				for( var i = 0; i < comArr.length; i++ ) {
					if( comArr[i].rowid == rowid1 ) {
						index = i;
						break;
					}
				}
				if( index === -1 ) {
					alert( "Something gone wrong" );
					return;
				}
				entities.splice( index, 1 );	
				entities_text.splice( index, 1 );	
				drawTable()	
			};
	
			function postData() {
				//alert("test");
				var jsonobj = {};
				for(i=0; i<entities.length; i++){
					var item = {} ;
					ids.forEach(function(entry) {
						if(entry=="itemnumbers"){
							item[entry] = entities_text[i][entry];
						}
						else{
							item[entry] = entities[i][entry];
						}
					});
					vars.forEach(function(entry) {
						if(entry == "amount"){
							item[entry] = (Number(entities[i]['unitprice']) * Number(entities[i]['quantity']));
						}
						else{
							item[entry] = entities[i][entry];
						}
					});
					jsonobj[i]=  item;
					
				}
				$('#jsondata').val(JSON.stringify(jsonobj));
				$.ajax({
	                url: "addestimatepurchaseorder",
	                type: "post",
	                data: $("#addform").serialize(),
	                success: function(response) {
	                	response = jQuery.parseJSON(response);	
	                    if(response.status=="success"){
	                    	bootbox.alert(response.message);
                        	window.setTimeout(function(){location.reload();}, 2000 );
	                    	resetForm("{{$form_info['name']}}");
	                    	entities= [];	
	                    	entities_text = [];	
	                    	drawTable();	
	                    }
	                    if(response.status=="fail"){
	                    	bootbox.alert(response.message, function(result) {});
	                    }
	                }
	            });
			};
	
			function resetForm(formid)
		    { 
	            form = $('#'+formid);
	            element = ['input','select','textarea'];
	            for(i=0; i<element.length; i++) 
	            {
	                $.each( form.find(element[i]), function(){
	                    switch($(this).attr('class')) {
	                      case 'form-control chosen-select':
	                      	$(this).find('option:first-child').attr("selected", "selected");
	                        break;
	                    }
	                    switch($(this).attr('type')) {
	                    case 'text':
	                    case 'select-one':
	                    case 'textarea':
	                    case 'hidden':
	                    case 'file':
	                    	$(this).val('');
	                      break;
	                    case 'checkbox':
	                    case 'radio':
	                    	$(this).attr('checked',false);
	                      break;
	                   
	                  }
	                });
	            }
	            $('.chosen-select').trigger("chosen:updated");	
		    }
			
		</script>
	@stop