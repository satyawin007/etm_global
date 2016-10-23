<?php
use Illuminate\Support\Facades\Input;
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
			th {
			    white-space: nowrap;
			}
			td {
			    white-space: nowrap;
			}
			panel-group .panel {
			    margin-bottom: 20px;
			    border-radius: 4px;
			}
			label{
				text-align: right;
				margin-top: 5px;
			}
			.table {
			    width: 100%;
			    max-width: 100%;
			    margin-bottom: 0px;
			}
			.form-actions {
			    display: block;
			    background-color: #F5F5F5;
			    border-top: 1px solid #E5E5E5;
			    /* margin-bottom: 20px; 
			    margin-top: 20px;
			    padding: 19px 20px 20px;*/
			}
		</style>
	@section('page_css')
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css"/>
		<link rel="stylesheet" href="../assets/css/chosen.css" />
		<link rel="stylesheet" href="../assets/css/daterangepicker.css" />
	@stop
		
	@stop
	
	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			ALERTS
		</small>
	@stop

	@section('page_content')
		<!-- PAGE CONTENT BEGINS -->
			<div class="col-xs-12">
                <div style="height: 10px;"></div>
                <div class="" role="alert"></div>
                <?php 
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading" style="background: #438eb9;">
                        <h3 class="panel-title ng-binding" style="color: #F8FFE4; margin-left: 4px;">ALL ALERTS</h3>
                    </div>
                    <div class="panel-body" style="padding-top: 5px;">
                        <div class="row" style="margin-top: 0px;">
                        	<div class="col-xs-12 col-sm-12 widget-container-col ui-sortable">
										<div class="widget-box widget-color-blue3">
											<!-- #section:custom/widget-box.options -->
											<div class="widget-header">
												<h4 class="widget-title bigger lighter">
													<i class="ace-icon fa fa-table"></i>
													Vehicle Tyre Change Alerts
												</h4>

												<div class="widget-toolbar widget-toolbar-light no-border">
												</div>
											</div>

											<!-- /section:custom/widget-box.options -->
											<div class="widget-body">
												<div class="widget-main no-padding">
													<table class="table table-striped table-bordered table-hover">
														<thead class="thin-border-bottom">
															<tr>
																<th>
																	VEHICLE
																</th>
																<th>
																	CHANGED ON
																</th>
																<th>
																	FROM ACTION
																</th>
																<th>
																	TO ACTION
																</th>
																<th>
																	REMARKS
																</th>
																<th>
																	ALERT DATE
																</th>
															</tr>
														</thead>

														<tbody>
															<?php
																$parentId = -1;
																$parent = \InventoryLookupValues::where("name","=","ITEM ACTIONS")->get();
																if(count($parent)>0){
																	$parent = $parent[0];
																	$parentId = $parent->id;
																}
																$veh_actions_arr = array();
																$veh_actions =  \InventoryLookupValues::where("parentId","=",$parentId)->where("status","=","ACTIVE")->get();
																$veh_actions_arr = array();
																foreach ($veh_actions  as $veh_action){
																	$veh_actions_arr[$veh_action['id']] = $veh_action->name;
																}
																$today = date("Y-m-d");
																$tires_alert_data="";
																$next_month = date('Y-m-d', strtotime('+1 month'));
																$sql = "select vehicle.veh_reg, inventory_transaction.date, inventory_transaction.fromActionId, inventory_transaction.toActionId, inventory_transaction.alertDate, inventory_transaction.remarks from inventory_transaction join vehicle on vehicle.id=inventory_transaction.toVehicleId where alertDate BETWEEN '".$today."' and '".$next_month."'";
																$recs = \DB::select(\DB::raw($sql));
																foreach ($recs as $rec){
																	$rec->alertDate = date("d-m-Y",strtotime($rec->alertDate));
																	$rec->date = date("d-m-Y",strtotime($rec->date));
																	if(isset($veh_actions_arr[$rec->fromActionId])){
																		$rec->fromActionId = $veh_actions_arr[$rec->fromActionId];
																	}
																	else{
																		$rec->fromActionId = "";
																	}
																	if(isset($veh_actions_arr[$rec->toActionId])){
																		$rec->toActionId = $veh_actions_arr[$rec->toActionId];
																	}
																	else{
																		$rec->toActionId = "";
																	}
															?>
															<tr>
																<td>{{$rec->veh_reg}}</td>
																<td>{{$rec->date}}</td>
																<td>{{$rec->fromActionId}}</td>
																<td>{{$rec->toActionId}}</td>
																<td>{{$rec->remarks}}</td>
																<td><span class="label label-warning">{{$rec->alertDate}}</span></td>
															</tr>
															<?php 
																}
															?>
															
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>

                        </div> <!-- close row -->

                    </div>  <!-- panel body -->
                </div> <!--Panel -->
            </div>
		
		
		<!-- PAGE CONTENT ENDS -->
	@stop
	
	@section('page_js')
		<!-- page specific plugin scripts -->
		<script src="../assets/js/bootbox.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			function changeVal(val){
				ck = $("#"+val).prop("checked");
			    if(!ck){
				   if(val<10){
					   $("#tab1").prop("checked",false);
				   }
				   else if(val<150){
					   $("#tab2").prop("checked",false);
				   }
				   else if(val<200){
					   $("#tab3").prop("checked",false);
				   }
				   else if(val<250){
					   $("#tab4").prop("checked",false);
				   }
				   else if(val<350){
					   $("#tab5").prop("checked",false);
				   }
				}
			}
			
			jQuery(function($){
			   $("#tab1").on("click",function(){
				   val = $("#tab1").prop("checked");
				   if(val){
					 for(i=1;i<10; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=1;i<10; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab2").on("click",function(){
				   val = $("#tab2").prop("checked");
				   if(val){
					 for(i=101;i<150; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=101;i<150; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab3").on("click",function(){
				   val = $("#tab3").prop("checked");
				   if(val){
					 for(i=151;i<200; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=151;i<200; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab4").on("click",function(){
				   val = $("#tab4").prop("checked");
				   if(val){
					 for(i=201;i<300; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=201;i<300; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   $("#tab5").on("click",function(){
				   val = $("#tab5").prop("checked");
				   if(val){
					 for(i=301;i<350; i++){
						 $("#"+i).prop("checked",true);
					 }
				   }
				   else{
					   for(i=301;i<350; i++){
						 $("#"+i).prop("checked",false);
					   }
				   }
				});

			   <?php 
					if(Session::has('message')){
						echo "bootbox.confirm('".Session::pull('message')."', function(result) {});";
					}
				?>
			
			});
		</script>
	@stop