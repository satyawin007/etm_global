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
			CONTRACTS
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$values['bredcum']}}
		</small>
	@stop

	@section('page_content')
		<div class="row ">
			<div class="col-xs-offset-1 col-xs-10">
				<?php $form_info = $values["form_info"]; ?>
				<?php $jobs = Session::get("jobs");?>
				<?php if(($form_info['action']=="addstate" && in_array(206, $jobs)) or 
						($form_info['action']=="addclient" && in_array(403, $jobs)) ||
						($form_info['action']=="adddepot" && in_array(405, $jobs)) ||
						($form_info['action']=="addvehiclemeeter" && in_array(413, $jobs)) ||
						($form_info['action']=="addclientholidays" && in_array(415, $jobs))
					  ){ ?>
					@include("contracts.addlookupform",$form_info)
				<?php } ?>
			</div>
		</div>
				
		<div class="row ">
		<div class="col-xs-offset-1 col-xs-10">
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
			
			<?php if((isset($values["showsearchrow"]) && $values["showsearchrow"]="servlogrequests")){?>
			<div class="row" style="margin-bottom: 2%;">
				<div class="col-xs-offset-3 col-xs-9">
					<div class="col-xs-4">
						<select name="clientid" id="clientid" class="formcontrol chosen-select">
						<!-- <option value="0">ALL</option> -->
						<?php 
							$clients =  AppSettingsController::getEmpClients();
							$clients_arr = array();
							foreach ($clients as $client){
								$clients_arr[$client['id']] = $client['name'];
							}
							foreach ($clients_arr as $key=>$val){
								echo "<option value='".$key."'>".$val."</option>";
							}
						?>
						</select>
					</div>
					<div class="col-xs-4">
						<select name="logstatus" id="logstatus" class="formcontrol chosen-select">
							<option value="All">All</option>
							<option value="Send for Approval">Send for Approval</option>
							<option value="Requested">Requested</option>
							<option value="Open">Open</option>
							<option value="Closed">Closed</option>
						</select>
					</div>
					<div class="col-xs-3">
						<button class="btn btn-xs btn-primary" id="getbtn">&nbsp;&nbsp;GET&nbsp;&nbsp;</button>
					</div>
				</div>
			</div>
			<?php }?>
			
			
			<form action="test" method="post" name="workflowform" id="workflowform" onsubmit="return false;">
			<?php if((isset($values["showsearchrow"]) && $values["showsearchrow"]="servlogrequests")){?>
			<div class="row">
				<div class="col-xs-offset-4 col-xs-7">
					<div class="col-xs-4">
						<select name="updatelogstatus" id="updatelogstatus" class="formcontrol chosen-select">
							<option value="Requested">Requested</option>
							<?php 
								$jobs = \Session::get("jobs"); 
								if(in_array(416, $jobs)){
							?>
							<option value="Send for Approval">Send for Approval</option>
							<option value="Open">Open</option>
							<option value="Closed">Closed</option>
							<?php }?>
						</select>
					</div>
					<div class="col-xs-3">
						<button class="btn btn-xs btn-primary" id="updatebtn" onclick="postData()">&nbsp;&nbsp;UPDATE&nbsp;&nbsp;</button>
					</div>
				</div>
			</div>
			<?php }?>
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
		</form>

		<?php 
			if(isset($values['modals'])) {
				$modals = $values['modals'];
				foreach ($modals as $modal){
		?>
				@include('masters.layouts.modalform', $modal);
		<?php }} ?>
		
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

			function paginate(page){
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='type'; 
				myin.value=$("#type").val(); 
				document.getElementById('paginate').appendChild(myin); 
				var myin = document.createElement("input"); 
				myin.type='hidden'; 
				myin.name='id'; 
				myin.value=$("#type").val(); 
				document.getElementById('paginate').appendChild(myin); 
				$("#page").val(page);
				$("#paginate").submit();				
			}

			function checkvalidation(val,id,table){
				url = "";
				message ="";
				if(table == "Client"){
					url = "checkvalidation?table="+table+"&name="+val;
					message = "This Client Name: "+val+" is already existed";
				}
				else if(table == "Depot"){
					stateId = $("#statename").val();
					districtId = $("#districtname").val();
					cityId = $("#cityname").val();
					if(stateId != undefined && stateId ==""){
						alert("Please select state");
						 $("#"+id).val("");
						return false;
					}
					if(districtId != undefined && districtId ==""){
						alert("Please select district");
						 $("#"+id).val("");
						return false;
					}
					if(cityId != undefined && cityId ==""){
						alert("Please select city");
						 $("#"+id).val("");
						return false;
					}
					url = "checkvalidation?table="+table+"&name="+val+"&stateId="+stateId+"&cityId="+cityId+"&districtId="+districtId;
					message = "This Depot Name: "+val+" is already existed";
				}
				$.ajax({
				      url: url,
				      success: function(data) {
					      if(data == "exists"){
					    	  bootbox.alert(message, function(result) {});
					    	  $("#"+id).val("");
					      }
				      },
				      type: 'GET'
				   });
			}

			function modalEditLookupValue(id, value, remarks, modules, fields, enabled, status){
				$("#value1").val(value);
				$("#id1").val(id);
				$("#remarks1").text(remarks);
				var array = modules.split(",");	
				$("#modules1 option").each(function() {this.selected=false});
				$("#modules1 option").each(function() {
					for(i=0; i<array.length; i++) {
						if(this.text == array[i]){
							this.selected = true;
						}
					}
				});
				var array = fields.split(",");	
				$("#showfields1 option").each(function() {this.selected=false});
				$("#showfields1 option").each(function() {
					for(i=0; i<array.length; i++) {
						if(this.text == array[i]){
							this.selected = true;
						}
					}
				});

				$('.chosen-select').trigger("chosen:updated");			

				if(status=="ACTIVE") {
					$("#ACTIVE").prop("checked",true);
				}
				else{
					$("#INACTIVE").prop("checked",true);
				}
				if(enabled=="YES") {
					$("#YES").prop("checked",true);
				}
				else{
					$("#NO").prop("checked",true);
				}
				return;				
			}

			function postData(){
				var updatelogstatus = $("#updatelogstatus").val();
				if(updatelogstatus == ""){
					alert("select update status");
					return;
				}
			 	$.post( 
                   "updateclientholidaysrequeststatus",
                   $("#workflowform").serialize(),
                   function(data) {
                       json_obj = JSON.parse(data);
                       bootbox.alert(json_obj.message);
                       window.setTimeout(function(){location.reload();}, 2000 );
                   }
                );
				return false;
			}

			function modalEditVehicleMeeter(id, vehicleId, meeterno, startdate, enddate, startreading, endreading, status){
				$("#vehicle1").val(vehicleId);				
				$("#meeterno1").val(meeterno);
				$("#startdate1").val(startdate);
				$("#enddate1").val(enddate);
				$("#startreading1").val(startreading);
				$("#endreading1").val(endreading);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditClientHolidays(id, clientname, depotname, fromDate, toDate, comments, status, deleted){
				$("#clientname1").val(clientname);				
				$("#depot1").val(depotname);
				$("#fromdate1").val(fromDate);
				$("#todate1").val(toDate);
				$("#comments1").val(comments);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#deleted1 option").each(function() { this.selected = (this.text == deleted); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditRole(id, name, description, status){
				$("#rolename1").val(name);				
				$("#description1").val(description);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
			}


			function modalEditClient(id, name, code, status){
				$("#clientname1").val(name);				
				$("#clientcode1").val(code);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditDepot(id, name, code, parent, city, district, state, status){
				$("#depotname1").val(name);				
				$("#depotcode1").val(code);
				$("#statename1 option").each(function() {this.selected = (this.text == state); });
				$("#districtname1 option").each(function() {this.selected = (this.text == district); });
				$("#parentofficebranch1 option").each(function() {this.selected = (this.text == parent); });
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

			function changeDepot(val){
				$.ajax({
			      url: "getdepotsbyclientId?id="+val,
			      success: function(data) {
			    	  $("#depot").html(data);
			    	  $('.chosen-select').trigger("chosen:updated");
			      },
			      type: 'GET'
			    });
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
			}

			function getMeeterNo(val){
				$.ajax({
			      url: "getmeeterno?vehicleid="+val,
			      success: function(data) {
			    	  $("#meeterno").val(data);
			      },
			      type: 'GET'
			    });
				clientId =  $("#clientname").val();
				depotId = $("#depot").val();
			}

			$("#reset").on("click",function(){
				$("#{{$form_info['name']}}").reset();
			});

			$("#submit").on("click",function(){
				
				var statename = $("#statename").val();
				if(statename != undefined && statename ==""){
					alert("Please select statename");
					return false;
				}

				var sourcecity = $("#sourcecity").val();
				if(sourcecity != undefined && sourcecity ==""){
					alert("Please select sourcecity");
					return false;
				}

				var destinationcity = $("#destinationcity").val();
				if(destinationcity != undefined && destinationcity ==""){
					alert("Please select destinationcity");
					return false;
				}

				var vehicletype = $("#vehicletype").val();
				if(vehicletype != undefined && vehicletype ==""){
					alert("Please select vehicletype");
					return false;
				}

				var type = $("#type").val();
				if(type != undefined && type==""){
					alert("Please select type");
					return false;
				}

				var cityname = $("#cityname").val();
				if(cityname != undefined && cityname ==""){
					alert("Please select cityname");
					return false;
				}

				var bankname = $("#bankname").val();
				if(bankname != undefined && bankname ==""){
					alert("Please select bankname");
					return false;
				}

				var accounttype = $("#accounttype").val();
				if(accounttype != undefined && accounttype ==""){
					alert("Please select accounttype");
					return false;
				}

				var paymenttype = $("#paymenttype").val();
				if(paymenttype != undefined && paymenttype ==""){
					alert("Please select paymenttype");
					return false;
				}

				var bankaccount = $("#bankaccount").val();
				if(bankaccount != undefined && bankaccount ==""){
					alert("Please select bankaccount");
					return false;
				}

				var financecompany = $("#financecompany").val();
				if(financecompany != undefined && financecompany ==""){
					alert("Please select financecompany");
					return false;
				}

				var frequency = $("#frequency").val();
				if(frequency != undefined && frequency ==""){
					alert("Please select frequency");
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

				var status = $("#status").val();
				if(status != undefined && status ==""){
					alert("Please select status");
					return false;
				}
				
				var loanforvehicle = $("#loanforvehicle").val();
				if((loanforvehicle != undefined && loanforvehicle =="") || (loanforvehicle != undefined && loanforvehicle == null)){
					alert("Please select loanforvehicle");
					return false;
				}

				var loanpurpose = $("#loanpurpose").val();
				if((loanpurpose != undefined && loanpurpose =="") || (loanforvehicle != undefined && loanpurpose == null)){
					alert("Please select loanpurpose");
					return false;
				}

				var vehicle = $("#vehicle").val();
				if((vehicle != undefined && vehicle =="")){
					alert("Please select vehicle");
					return false;
				}
				$("#{{$form_info['name']}}").submit();
			});

			$("#type").on("change",function(){
				if(this.value != ""){
					window.location.replace('lookupvalues?type='+this.value);
				}
			});

			$("#getbtn").on("click",function(){
				clientid = $("#clientid").val();
				logstatus = $("#logstatus").val();
				myTable.ajax.url("getcontractsdatatabledata?name=clientholidays&depotid=0&clientid="+clientid+"&logstatus="+logstatus).load();
			})

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