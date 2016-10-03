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
			MASTERS
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
						($form_info['action']=="addcity" && in_array(210, $jobs)) or
						($form_info['action']=="addemployeebatta" && in_array(219, $jobs)) or
						($form_info['action']=="addservicedetails" && in_array(221, $jobs)) or
						($form_info['action']=="addlookupvalue" && in_array(225, $jobs)) or
						($form_info['action']=="addbankdetails" && in_array(227, $jobs)) or 
						($form_info['action']=="addcard" && in_array(227, $jobs)) or
						($form_info['action']=="addfinancecompany" && in_array(229, $jobs)) or 
						($form_info['action']=="addcreditsupplier" && in_array(231, $jobs)) or
						($form_info['action']=="addfuelstation" && in_array(235, $jobs)) or
						($form_info['action']=="addloan" && in_array(237, $jobs)) or
						($form_info['action']=="adddailyfinance" && in_array(239, $jobs)) or
						($form_info['action']=="addserviceprovider" && in_array(241, $jobs)) or
						($form_info['action']=="adddistrict" && in_array(208, $jobs)) or
						($form_info['action']=="addrole" && in_array(243, $jobs))||
						($form_info['action']=="addupload" && in_array(243, $jobs))
					  ){ ?>
					@include("masters.layouts.addlookupform",$form_info)
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
			$("#cardbankaccount").attr("disabled",true);
			
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

			function enableBankAccount(value){
				$("#cardbankaccount").attr("disabled",true);
				if(value=="DEBIT CARD"){
					$("#cardbankaccount").attr("disabled",false);
				}
				$('.chosen-select').trigger("chosen:updated");	
			}

			function showPaymentFields(val){
				//alert(val);
				$("#addfields").html('<div style="margin-left:600px; margin-top:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-125" style="font-size: 250% !important;"></i></div>');
				$.ajax({
			      url: "getmasterspaymentfields?paymenttype="+val,
			      success: function(data) {
			    	  $("#addfields").html(data);
			    	  $('.date-picker').datepicker({
						autoclose: true,
						todayHighlight: true
					  });
			    	  $("#addfields").show();
			      },
			      type: 'GET'
			   });
			}

			function checkvalidation(val,id,table){
				url = "";
				message ="";
				if(table == "State"){
					url = "checkvalidation?table="+table+"&name="+val;
					message = "This State Name: "+val+" is already existed";
				}
				else if(table == "District"){
					url = "checkvalidation?table="+table+"&name="+val;
					message = "This District Name: "+val+" is already existed";
				}
				else if(table == "City"){
					url = "checkvalidation?table="+table+"&name="+val;
					message = "This City Name: "+val+" is already existed";
				}
				else if(table == "ServiceDetails"){
					url = "checkvalidation?table="+table+"&serviceNo="+val;
					message = "This SERVICE NO: "+val+" is already existed";
				}
				else if(table == "BankDetails"){
					url = "checkvalidation?table="+table+"&accountNo="+val;
					message = "This Account NO: "+val+" is already existed";
				}
				else if(table == "FinanceCompany"){
					url = "checkvalidation?table="+table+"&name="+val;
					message = "This Finance Company Name: "+val+" is already existed";
				}
				else if(table == "CreditSupplier"){
					stateId = $("#statename").val();
					cityId = $("#cityname").val();
					if(stateId != undefined && stateId ==""){
						alert("Please select state");
						 $("#"+id).val("");
						return false;
					}
					if(cityId != undefined && cityId ==""){
						alert("Please select city");
						 $("#"+id).val("");
						return false;
					}
					url = "checkvalidation?table="+table+"&supplierName="+val+"&stateId="+stateId+"&cityId="+cityId;
					message = "This Credit Supplier Name: "+val+" is already existed";
				}
				else if(table == "FuelStation"){
					stateId = $("#statename").val();
					cityId = $("#cityname").val();
					if(stateId != undefined && stateId ==""){
						alert("Please select state");
						 $("#"+id).val("");
						return false;
					}
					if(cityId != undefined && cityId ==""){
						alert("Please select city");
						 $("#"+id).val("");
						return false;
					}
					url = "checkvalidation?table="+table+"&name="+val+"&stateId="+stateId+"&cityId="+cityId;
					message = "This Credit Supplier Name: "+val+" is already existed";
				}
				else if(table == "Loan"){
					financeCompanyId = $("#financecompany").val();
					if(financeCompanyId != undefined && financeCompanyId ==""){
						alert("Please select finance company");
						 $("#"+id).val("");
						return false;
					}
					url = "checkvalidation?table="+table+"&loanNo="+val+"&financeCompanyId="+financeCompanyId;
					message = "This Loan No: "+val+" is already existed";
				}
				else if(table == "LookupTypeValues"){
					parentId = $("#type").val();
					if(parentId != undefined && parentId ==""){
						alert("Please select type");
						 $("#"+id).val("");
						return false;
					}
					url = "checkvalidation?table="+table+"&name="+val+"&parentId="+parentId;
					message = "This VALUE: "+val+" is already existed";
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

			function modalEditServiceProvider(id, branchId, provider, name, number,companyName, configDetails, address, refName,refNumber, status){
				$("#provider1 option").each(function() { this.selected = (this.text == provider); });
				$("#branch1 option").each(function() { this.selected = (this.text == branchId); });
				$("#name1").val(name);				
				$("#number1").val(number);
				$("#companyname1").val(companyName);
				$("#configdetails1").val(configDetails);
				$("#address1").val(address);
				$("#referencename1").val(refName);
				$("#referencenumber1").val(refNumber);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditState(id, name, code, status){
				$("#statename1").val(name);				
				$("#statecode1").val(code);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
			}

			function modalEditFuelStation(name, balanceAmount, depoamt, paymentType,  pmtdate, cityId, stateId, status,  id){
				$("#fuelstationname1").val(name);				
				$("#balanceamount1").val(balanceAmount);
				$("#securitydepositamount1").val(depoamt);
				$("#securitypaymentdate1").val(pmtdate);
				$("#securitypaymenttype1 option").each(function() { this.selected = (this.value == paymentType); });
				$("#statename1 option").each(function() {this.selected = (this.text == stateId); });
				$("#cityname1 option").each(function() { this.selected = (this.text == cityId); });
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditRole(id, name, description, status){
				$("#rolename1").val(name);				
				$("#description1").val(description);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
			}



			function modalEditCity(id, name, code, state, status){
				$("#cityname1").val(name);				
				$("#citycode1").val(code);
				$("#statename1 option").each(function() {this.selected = (this.text == state); });
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

			function modalEditDistrict(id, name, code, state, status){
				$("#districtname1").val(name);				
				$("#districtcode1").val(code);
				$("#statename1 option").each(function() {this.selected = (this.text == state); });
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditCard(id, cardNumber, cardType, cardHolderName, bank, acctId, creditLimit, expireDate, status){
				
				$("#cardtype1 option").each(function() {this.selected = (this.text == cardType); });
				$("#bank1 option").each(function() { this.selected = (this.text == bank); });
				$("#cardbankaccount1 option").each(function() { this.selected = (this.text == (bank+"-"+acctId)); });
				$("#cardnumber1").val(cardNumber);
				$("#cardholdername1").val(cardHolderName);
				$("#creditlimit1").val(creditLimit);
				$("#expiredate1").val(expireDate);
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}
			
			function modalEditService(id, source, dest, serviceno, description, status, servstatus){
				$("#sourcecity1 option").each(function() {this.selected = (this.text == source); });
				$("#destinationcity1 option").each(function() { this.selected = (this.text == dest); });
				$("#description1").val(description);
				$("#serviceno1").val(serviceno);
				$("#active1 option").each(function() { this.selected = (this.text == status); });
				$("#servicestatus1 option").each(function() { this.selected = (this.text == servstatus); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function modalEditUpload(id, type, refId, lookupvalue, filePath, status){
				$("#uploadfor1").attr("disabled",true);
				if(type == "EMPLOYEE"){
					$("#employee1 option").each(function() {this.selected = (this.text == refId); });
					$("#vehicle1").attr("disabled",true);
				}
				else{
					$("#vehicle1 option").each(function() {this.selected = (this.text == refId); });
					$("#employee1").attr("disabled",true);
				}
				$("#uploadfor1 option").each(function() {this.selected = (this.text == type); });
				$("#documenttype1 option").each(function() { this.selected = (this.text == lookupvalue); });
				$("#status1 option").each(function() { this.selected = (this.text == status); });
				$("#id1").val(id);		
				$('.chosen-select').trigger("chosen:updated");	
			}

			function enableUploadFields(val){
				if(val=="EMPLOYEE"){
					$("#vehicle").attr("disabled",true);
					$("#employee").attr("disabled",false);
				}
				else{
					$("#employee").attr("disabled",true);
					$("#vehicle").attr("disabled",false);
				}
				$('.chosen-select').trigger("chosen:updated");	
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

				var status = $("#status").val();
				if(status != undefined && status ==""){
					alert("Please select status");
					return false;
				}

				var branchid = $("#branchid").val();
				if(branchid != undefined && branchid ==""){
					alert("Please select branchid");
					return false;
				}

				var provider = $("#provider").val();
				if(provider != undefined && provider ==""){
					alert("Please select provider");
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

				var uploadfor = $("#uploadfor").val();
				if(uploadfor != undefined && uploadfor ==""){
					alert("Please select uploadfor");
					return false;
				}

				var documenttype = $("#documenttype").val();
				if(documenttype != undefined && documenttype ==""){
					alert("Please select documenttype");
					return false;
				}

				if($("#uploadfor").val() == "EMPLOYEE"){
					var employee = $("#employee").val();
					if(employee != undefined && employee ==""){
						alert("Please select employee");
						return false;
					}
				}
				
				if($("#uploadfor").val() == "VEHICLE"){
					var vehicle = $("#vehicle").val();
					if(vehicle != undefined && vehicle ==""){
						alert("Please select vehicle");
						return false;
					}
				}
				
				$("#{{$form_info['name']}}").submit();
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

			function validateCard(val){
				$.ajax({
			      url: "validatecardnumber?cardnumber="+val,
			      success: function(data) {
				      if(data == "YES"){
					      alert("CARD NUMBER ALREADY EXISTS");
				    	  $("#cardnumber").val("");
				      }
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


			$('.file').ace_file_input({
				no_file:'No File ...',
				btn_choose:'Choose',
				btn_change:'Change',
				droppable:false,
				onchange:null,
				thumbnail:false //| true | large
				//whitelist:'gif|png|jpg|jpeg'
				//blacklist:'exe|php'
				//onchange:''
				//
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
			$('#cardnumber').mask('9999-9999-9999-9999');
			$('#cardnumber1').mask('9999-9999-9999-9999');

			jQuery(function($) {		
				//initiate dataTables plugin
				var myTable = 
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
		                url :"getdatatabledata?name=<?php echo $values["provider"] ?>", // json datasource
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