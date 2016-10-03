<?php
use settings\AppSettingsController;
?>
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
				white-space: normal;
			}
			.chosen-container{
			  width: 100% !important;
			}
			.form-control {
			    display: block;
			    width: 100%;
			    height: 34px;
		    }
		    .driversarea{
		     	position: absolute; 
  				left: -999em;
		    }
		</style>
	@stop
	
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
	@stop
	
	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			CONTRACTS 
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<div class="col-xs-offset-0 col-xs-12">
				<?php $form_info = $values["form_info"];?>
				<?php $jobs = Session::get("jobs");?>
				<?php if(($form_info['action']=="addstate" && in_array(206, $jobs)) || 
						($form_info['action']=="addcontract" && in_array(401, $jobs))
					  ){ ?>
					@include("contracts.tablerowform",$form_info)
				<?php } ?>
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
						<label>{{ strtoupper($select["name"]) }}</label>
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
							$clients =  AppSettingsController::getEmpClients();
							$clients_arr = array();
							foreach ($clients as $client){
								echo "<option value='".$client['id']."'>".$client['name']."</option>";
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
			<div class="modal-dialog" style="width: 98%">
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
			$("#updaterowbtn").hide();
			var app = angular.module('myApp', []);
			app.controller('myCtrl', function($scope, $http) {
				$scope.vehicles = [];
				$scope.ids = ['vehicle','vehicletype','driver1','driver2','driver3','driver4','driver5','helper','routes'];
				$scope.vehicles_text = [];
				$scope.addRow = function(){
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" || $scope.driver1 === "" || $scope.vehicle === "" || 
							$scope.driver1 == $scope.driver2 || $scope.driver1 == $scope.driver3 || $scope.driver1 == $scope.driver4 || $scope.driver1 == $scope.driver5 
					  )
					{
						alert("Duplicate or Wrong Value");
						return;
					}
					index =  -1;	
					var comArr = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === $scope.vehicle ||  
							comArr[i].driver1 === $scope.driver1 || 
							(comArr[i].driver2 === $scope.driver2 && $scope.driver2!="") || 
							(comArr[i].driver3 === $scope.driver3 && $scope.driver3!="") || 
							(comArr[i].driver4 === $scope.driver4 && $scope.driver4!="") || 
							(comArr[i].driver5 === $scope.driver5 && $scope.driver5!="") || 
							(comArr[i].helper === $scope.helper  && $scope.helper!="")
						   ) 
						{
							index = i;
							break;
						}
					}
					if( index  != -1 ) {
						alert( "duplicate value" );
						return;
					}
					if($scope.driver1!="" && ($scope.drv1dt=="" || typeof $scope.drv1dt === "undefined")){
						alert("Enter driver1 start date");
						return;
					}
					if($scope.driver2!="" && typeof $scope.driver2 !== "undefined" && ($scope.drv2dt=="" || typeof $scope.drv2dt === "undefined")){
						alert("Enter driver2 start date");
						return;
					}
					if($scope.driver3!="" && typeof $scope.driver3 !== "undefined" && ($scope.drv3dt=="" || typeof $scope.drv3dt === "undefined")){
						alert("Enter driver3 start date");
						return;
					}
					if($scope.driver4!="" && typeof $scope.driver4 !== "undefined" && ($scope.drv4dt=="" || typeof $scope.drv4dt === "undefined")){
						alert("Enter driver4 start date");
						return;
					}
					if($scope.driver5!="" && typeof $scope.driver5 !== "undefined" && ($scope.drv5dt=="" || typeof $scope.drv5dt === "undefined")){
						alert("Enter driver5 start date");
						return;
					}
					if($scope.helper!="" && typeof $scope.helper !== "undefined"  && ($scope.helperdt=="" || typeof $scope.helperdt === "undefined")){
						alert("Enter helper start date");
						return;
					}

					if(($scope.startdt=="" || typeof $scope.startdt === "undefined")){
						alert("Enter start date");
						return;
					}
					$scope.vehicles.unshift({ 
						'vehicle':$scope.vehicle, 
						'vehicletype':$scope.vehicletype, 
						'driver1': $scope.driver1, 
						'drv1dt':$scope.drv1dt,
						'driver2':$scope.driver2, 
						'drv2dt':$scope.drv2dt,
						'driver3':$scope.driver3,
						'drv3dt':$scope.drv3dt, 
						'driver4':$scope.driver4,
						'drv4dt':$scope.drv4dt, 
						'driver5':$scope.driver5,
						'drv5dt':$scope.drv5dt, 
						'helper':$scope.helper,
						'helperdt':$scope.helperdt, 
						'startdt':$scope.startdt, 
						'routes':$scope.routes, 
						'floorrate':$scope.floorrate 
					});

					text_arr = [];
					$scope.ids.forEach(function(entry) {
						text = $("#"+entry+" option:selected").text();
						$("#"+entry).find('option:selected').removeAttr("selected");
						text_arr[entry] = text;
					});
					text_arr['startdt'] = $scope.startdt;
					text_arr['drv1dt'] = $scope.drv1dt;
					text_arr['drv2dt'] = $scope.drv2dt;
					text_arr['drv3dt'] = $scope.drv3dt;
					text_arr['drv4dt'] = $scope.drv4dt;
					text_arr['drv5dt'] = $scope.drv5dt;
					text_arr['helperdt'] = $scope.helperdt;
					text_arr['floorrate'] = $scope.floorrate;
					$scope.vehicles_text.unshift(text_arr);
					$('.chosen-select').trigger("chosen:updated");
					$scope.vehicle='';
					$scope.vehicletype='';
					$scope.driver1='';
					$scope.driver2='';
					$scope.driver3='';
					$scope.driver4='';
					$scope.driver5='';
					$scope.helper='';
					$scope.drv1dt='';
					$scope.drv2dt='';
					$scope.drv3dt='';
					$scope.drv4dt='';
					$scope.drv5dt='';
					$scope.helperdt='';
					$scope.startdt='';
					$scope.floorrate='';
					$scope.routes='';
				};

				$scope.editRow = function(vehicle){	
					tempdata = [];
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
					$scope.ids.forEach(function(entry) {
						$("#"+entry+" option").each(function() { this.selected =(this.text == comArr[i][entry])});
						$("#"+entry).find('option:selected').attr("selected", "selected"); 
						$scope[entry]=comArr1[i][entry];
					});	
					$scope['startdt']=comArr[i]['startdt'];
					$scope['drv1dt']=comArr[i]['drv1dt'];
					$scope['drv2dt']=comArr[i]['drv2dt'];
					$scope['drv3dt']=comArr[i]['drv3dt'];
					$scope['drv4dt']=comArr[i]['drv4dt'];
					$scope['drv5dt']=comArr[i]['drv5dt'];
					$scope['helperdt']=comArr[i]['helperdt'];
					//$("#startdt").val(comArr1[i]['startdt']);
					$('.chosen-select').trigger("chosen:updated");
					$("#addrowbtn").hide();
					$("#updaterowbtn").show();	
				};

				$scope.updateRow = function(){	
					if(typeof $scope.vehicle === "undefined" || typeof $scope.driver1 === "undefined" || $scope.driver1 === "" || $scope.vehicle === "") {
						return;
					}
					var index = -1;		
					var comArr = eval( $scope.vehicles );
					for( var i = 0; i < comArr.length; i++ ) {
						if( comArr[i].vehicle === $scope.vehicle ) {
							index = i;
							$scope.vehicles[i]['driver1']=$scope.driver1;
							$scope.vehicles[i]['driver2']=$scope.driver2;
							$scope.vehicles[i]['driver3']=$scope.driver3;
							$scope.vehicles[i]['driver4']=$scope.driver4;
							$scope.vehicles[i]['driver5']=$scope.driver5;
							$scope.vehicles[i]['helper']=$scope.helper;

							$scope.ids.forEach(function(entry) {
								text = $("#"+entry+" option:selected").text();
								$("#"+entry).find('option:selected').removeAttr("selected");
								if(entry != "vehicle"){
									$scope.vehicles_text[index][entry] = text;
								}
							});
							$scope.vehicles_text[index]['startdt'] = $scope.startdt;
							$scope.vehicles_text[index]['drv1dt']=$scope.drv1dt;
							$scope.vehicles_text[index]['drv2dt']=$scope.drv2dt;
							$scope.vehicles_text[index]['drv3dt']=$scope.drv3dt;
							$scope.vehicles_text[index]['drv4dt']=$scope.drv4dt;
							$scope.vehicles_text[index]['drv5dt']=$scope.drv5dt;
							$scope.vehicles_text[index]['helperdt']=$scope.helperdt;
							break;
						}
					}
					if( index === -1 ) {
						alert( "Vehicle can not be updated / Something gone wrong" );
						return;
					}
					$scope.vehicle='';
					$scope.vehicletype='';
					$scope.driver1='';
					$scope.driver2='';
					$scope.driver3='';
					$scope.driver4='';
					$scope.driver5='';
					$scope.helper='';
					$scope.drv1dt='';
					$scope.drv2dt='';
					$scope.drv3dt='';
					$scope.drv4dt='';
					$scope.drv5dt='';
					$scope.helperdt='';
					$scope.startdt='';
					$scope.routes='';
					$scope.floorate='';
					alert("updated successfully");
					$('.chosen-select').trigger("chosen:updated");
					$("#addrowbtn").show();
					$("#updaterowbtn").hide();	
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
					noofvehicles  = $("#noofvehicles").val();
					if(noofvehicles != $scope.vehicles.length){
						alert("no of vehicles and added vehicles does not match");
						return;
					}
					$('#contractvehicles').val(JSON.stringify($scope.vehicles));
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
            					window.setTimeout(function(){location.reload();}, 2000);	
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
	
			function modalEditContract(id){
				//$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				url = "editcontract?id="+id;
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
				myTable.ajax.url("getcontractsdatatabledata?name=contracts&clientid="+clientid).load();
			})
			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$('#otherdrivers').on('change', function() { 
			    if (this.checked) {
			    	$(".driversarea").css('position','relative');
			    	$(".driversarea").css('left','0em');
			    	
			    }
			    else{
			    	 $(".driversarea").css('position','absolute');
			    	 $(".driversarea").css('left','-999em');
			    }
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
				/*var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}
				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}*/
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
			
		</script>
	@stop