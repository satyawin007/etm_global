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
		<link rel="stylesheet" href="../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../assets/css/jquery.gritter.css" />
		<link rel="stylesheet" href="../assets/css/select2.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-datepicker3.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-editable.css" />
		
	@stop
		
	@stop
	
	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			APP SETTINGS
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
                        <h3 class="panel-title ng-binding" style="color: #F8FFE4; margin-left: 4px;">APPLICATION SETTINGS</h3>
                    </div>
                    <div class="panel-body" style="padding-top: 5px;">
                        <div class="row" style="margin-top: 0px;">
							<div class="tabbable">
								<ul class="nav nav-tabs padding-16">
									<li class="active">
										<a data-toggle="tab" href="#edit-basic" aria-expanded="true">
											<i class="green ace-icon fa fa-bookmark bigger-125"></i>
											Banner
										</a>
									</li>

									<li class="">
										<a data-toggle="tab" href="#edit-email" aria-expanded="false">
											<i class="purple ace-icon fa fa-envelope bigger-125"></i>
											email
										</a>
									</li>
									
									<li class="">
										<a data-toggle="tab" href="#edit-alert" aria-expanded="false">
											<i class="red ace-icon fa fa-bell bigger-125"></i>
											alerts
										</a>
									</li>
									
									<li class="">
										<a data-toggle="tab" href="#edit-dashboardmessage" aria-expanded="false">
											<i class="green ace-icon fa fa-sticky-note bigger-125"></i>
											Dashboard message
										</a>
									</li>
								</ul>

								<div class="tab-content profile-edit-tab-content">
									
									<div id="edit-basic" class="tab-pane active">
										<form class="form-horizontal" action="updatebannersettings" method="post" enctype="multipart/form-data">
											<h4 class="header blue bolder smaller">Banner and Title </h4>
											<div class="row">
												<div class="col-xs-6">
													<label class="ace-file-input ace-file-multiple"><input name="file" type="file"/></label>
												</div>
												<div class="col-xs-6">
													<h4 class="header green">Enter Title and Choose which to use :</h4>
													<div class="form-group">
														<label class="col-sm-2 control-label no-padding-right" for="form-field-username">Title</label>
														<div class="col-sm-10">
															<?php 
																$rec = Parameters::where("name","=","title")->get();
																$rec = $rec[0];
															?>
															<input class="col-xs-12" name="title" type="text" id="form-field-username" placeholder="title" value="{{$rec->value}}" >
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label no-padding-right" for="form-field-username">Which to use</label>
														<div class="col-sm-10">
															<div class="radio">
																<label>
																	<input name="banner_type" type="radio" value="title" class="ace">
																	<span class="lbl"> &nbsp;&nbsp;&nbsp;Title&nbsp;&nbsp;&nbsp;</span>
																</label>
																<label>
																	<input name="banner_type" type="radio" value="banner" class="ace">
																	<span class="lbl"> &nbsp;Banner</span>
																</label>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix form-actions">
												<div class="col-md-offset-3 col-md-9">
													<button class="btn btn-info" type="submit">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Save
													</button>
				
													&nbsp; &nbsp;
													<button class="btn" type="reset">
														<i class="ace-icon fa fa-undo bigger-110"></i>
														Reset
													</button>
												</div>
											</div>
										</form>
									</div>
									
									<div id="edit-email" class="tab-pane">
										<form class="form-horizontal" action="updatebannersettings" method="post">
											<h4 class="header blue bolder smaller">Provide Email Addresses for DB Updates </h4>
											<div class="row">
												<div class="col-xs-6">
													<div class="form-group">
														<span class="col-sm-10 no-padding-right" >Emails (Seperated by Commas)</span>
														<div class="col-sm-10">
															<?php 
																$rec = Parameters::where("name","=","emailIds")->get();
																$rec = $rec[0];
															?>
															<input class="col-xs-12" name="emails" type="text" id="form-field-username" placeholder="test@gmail.com,test1@gmail.com" value="{{$rec->value}}" >
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix form-actions">
												<div class="col-md-offset-3 col-md-9">
													<button class="btn btn-info" type="submit">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Save
													</button>
				
													&nbsp; &nbsp;
													<button class="btn" type="reset">
														<i class="ace-icon fa fa-undo bigger-110"></i>
														Reset
													</button>
												</div>
											</div>
										</form>
									</div>
									
									<div id="edit-alert" class="tab-pane">
										<form class="form-horizontal" action="updatebannersettings" method="post">
											<h4 class="header blue bolder smaller">Provide Alert Days </h4>
											<div class="row">
												<div class="col-xs-6">
													<div class="form-group">
														<span class="col-sm-10 no-padding-right" >How many days before alert is needed?</span>
														<div class="col-sm-10">
															<?php 
																$rec = Parameters::where("name","=","alertdays")->get();
																$rec = $rec[0];
															?>
															<input class="col-xs-12" name="alertdays" type="text" id="form-field-username" placeholder="0" value="{{$rec->value}}" >
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix form-actions">
												<div class="col-md-offset-3 col-md-9">
													<button class="btn btn-info" type="submit">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Save
													</button>
				
													&nbsp; &nbsp;
													<button class="btn" type="reset">
														<i class="ace-icon fa fa-undo bigger-110"></i>
														Reset
													</button>
												</div>
											</div>
										</form>
									</div>
									
									<div id="edit-dashboardmessage" class="tab-pane">
										<form class="form-horizontal" action="updatebannersettings" method="post">
											<h4 class="header blue bolder smaller">DASHBOARD MESSAGE </h4>
											<div class="row">
												<div class="col-xs-12">
													<div class="form-group">
														<span class="col-sm-10 no-padding-right" >Please enter the text to update your dashboard message...</span>
														<div class="col-sm-10">
															<?php 
																$rec = Parameters::where("name","=","dashboardmessage")->get();
																$rec = $rec[0];
															?>
															<div class="wysiwyg-editor" id="editor1">{{$rec->value}}</div>
														</div>
													</div>
													<input type="hidden" id="dashboardmessage" name="dashboardmessage" value=""/>
												</div>
											</div>
											<div class="clearfix form-actions">
												<div class="col-md-offset-3 col-md-9">
													<button class="btn btn-info" onclick="getContent()" type="submit">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Save
													</button>
				
													&nbsp; &nbsp;
													<button class="btn" type="reset">
														<i class="ace-icon fa fa-undo bigger-110"></i>
														Reset
													</button>
												</div>
											</div>
										</form>
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
		
		<!--[if lte IE 8]>
		  <script src="../assets/js/excanvas.js"></script>
		<![endif]-->
		<script src="../assets/js/jquery-ui.custom.js"></script>
		<script src="../assets/js/jquery.ui.touch-punch.js"></script>
		<script src="../assets/js/jquery.gritter.js"></script>
		<script src="../assets/js/bootbox.js"></script>
		<script src="../assets/js/jquery.easypiechart.js"></script>
		<script src="../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../assets/js/jquery.hotkeys.js"></script>
		<script src="../assets/js/bootstrap-wysiwyg.js"></script>
		<script src="../assets/js/select2.js"></script>
		<script src="../assets/js/fuelux/fuelux.spinner.js"></script>
		<script src="../assets/js/x-editable/bootstrap-editable.js"></script>
		<script src="../assets/js/x-editable/ace-editable.js"></script>
		<script src="../assets/js/jquery.maskedinput.js"></script>
		<script src="../assets/js/ace/elements.fileinput.js"></script>
	@stop
	
	@section('inline_js')
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
		<?php 
			if(Session::has('message')){
				echo "bootbox.hideAll();";echo "bootbox.alert('".Session::pull('message')."', function(result) {});";
			}
		?>

		function getContent(){
			content = $("#editor1").html();
			//alert(content);
			$("#dashboardmessage").val(content);
		}
		
		jQuery(function($) {
			//editables on first profile page
		$.fn.editable.defaults.mode = 'inline';
		$.fn.editableform.loading = "<div class='editableform-loading'><i class='ace-icon fa fa-spinner fa-spin fa-2x light-blue'></i></div>";
	    $.fn.editableform.buttons = '<button type="submit" class="btn btn-info editable-submit"><i class="ace-icon fa fa-check"></i></button>'+
	                                '<button type="button" class="btn editable-cancel"><i class="ace-icon fa fa-times"></i></button>';    
		
		//editables 
		
		//text editable
	    $('#username')
		.editable({
			type: 'text',
			name: 'username'		
	    });
	
		
		//select2 editable
		var countries = [];
	    $.each({ "CA": "Canada", "IN": "India", "NL": "Netherlands", "TR": "Turkey", "US": "United States"}, function(k, v) {
	        countries.push({id: k, text: v});
	    });
	
		var cities = [];
		cities["CA"] = [];
		$.each(["Toronto", "Ottawa", "Calgary", "Vancouver"] , function(k, v){
			cities["CA"].push({id: v, text: v});
		});
		cities["IN"] = [];
		$.each(["Delhi", "Mumbai", "Bangalore"] , function(k, v){
			cities["IN"].push({id: v, text: v});
		});
		cities["NL"] = [];
		$.each(["Amsterdam", "Rotterdam", "The Hague"] , function(k, v){
			cities["NL"].push({id: v, text: v});
		});
		cities["TR"] = [];
		$.each(["Ankara", "Istanbul", "Izmir"] , function(k, v){
			cities["TR"].push({id: v, text: v});
		});
		cities["US"] = [];
		$.each(["New York", "Miami", "Los Angeles", "Chicago", "Wysconsin"] , function(k, v){
			cities["US"].push({id: v, text: v});
		});
		
		var currentValue = "NL";
	    $('#country').editable({
			type: 'select2',
			value : 'NL',
			//onblur:'ignore',
	        source: countries,
			select2: {
				'width': 140
			},		
			success: function(response, newValue) {
				if(currentValue == newValue) return;
				currentValue = newValue;
				
				var new_source = (!newValue || newValue == "") ? [] : cities[newValue];
				
				//the destroy method is causing errors in x-editable v1.4.6+
				//it worked fine in v1.4.5
				/**			
				$('#city').editable('destroy').editable({
					type: 'select2',
					source: new_source
				}).editable('setValue', null);
				*/
				
				//so we remove it altogether and create a new element
				var city = $('#city').removeAttr('id').get(0);
				$(city).clone().attr('id', 'city').text('Select City').editable({
					type: 'select2',
					value : null,
					//onblur:'ignore',
					source: new_source,
					select2: {
						'width': 140
					}
				}).insertAfter(city);//insert it after previous instance
				$(city).remove();//remove previous instance
				
			}
	    });
	
		$('#city').editable({
			type: 'select2',
			value : 'Amsterdam',
			//onblur:'ignore',
	        source: cities[currentValue],
			select2: {
				'width': 140
			}
	    });
	
	
		
		//custom date editable
		$('#signup').editable({
			type: 'adate',
			date: {
				//datepicker plugin options
				    format: 'yyyy/mm/dd',
				viewformat: 'yyyy/mm/dd',
				 weekStart: 1
				 
				//,nativeUI: true//if true and browser support input[type=date], native browser control will be used
				//,format: 'yyyy-mm-dd',
				//viewformat: 'yyyy-mm-dd'
			}
		})
	
	    $('#age').editable({
	        type: 'spinner',
			name : 'age',
			spinner : {
				min : 16,
				max : 99,
				step: 1,
				on_sides: true
				//,nativeUI: true//if true and browser support input[type=number], native browser control will be used
			}
		});
		
	
	    $('#login').editable({
	        type: 'slider',
			name : 'login',
			
			slider : {
				 min : 1,
				  max: 50,
				width: 100
				//,nativeUI: true//if true and browser support input[type=range], native browser control will be used
			},
			success: function(response, newValue) {
				if(parseInt(newValue) == 1)
					$(this).html(newValue + " hour ago");
				else $(this).html(newValue + " hours ago");
			}
		});
	
		$('#about').editable({
			mode: 'inline',
	        type: 'wysiwyg',
			name : 'about',
	
			wysiwyg : {
				//css : {'max-width':'300px'}
			},
			success: function(response, newValue) {
			}
		});
		
		
		
		// *** editable avatar *** //
		try {//ie8 throws some harmless exceptions, so let's catch'em
	
			//first let's add a fake appendChild method for Image element for browsers that have a problem with this
			//because editable plugin calls appendChild, and it causes errors on IE at unpredicted points
			try {
				document.createElement('IMG').appendChild(document.createElement('B'));
			} catch(e) {
				Image.prototype.appendChild = function(el){}
			}
	
			var last_gritter
			$('#avatar').editable({
				type: 'image',
				name: 'avatar',
				value: null,
				//onblur: 'ignore',  //don't reset or hide editable onblur?!
				image: {
					//specify ace file input plugin's options here
					btn_choose: 'Change Avatar',
					droppable: true,
					maxSize: 110000,//~100Kb
	
					//and a few extra ones here
					name: 'avatar',//put the field name here as well, will be used inside the custom plugin
					on_error : function(error_type) {//on_error function will be called when the selected file has a problem
						if(last_gritter) $.gritter.remove(last_gritter);
						if(error_type == 1) {//file format error
							last_gritter = $.gritter.add({
								title: 'File is not an image!',
								text: 'Please choose a jpg|gif|png image!',
								class_name: 'gritter-error gritter-center'
							});
						} else if(error_type == 2) {//file size rror
							last_gritter = $.gritter.add({
								title: 'File too big!',
								text: 'Image size should not exceed 100Kb!',
								class_name: 'gritter-error gritter-center'
							});
						}
						else {//other error
						}
					},
					on_success : function() {
						$.gritter.removeAll();
					}
				},
			    url: function(params) {
					// ***UPDATE AVATAR HERE*** //
					//for a working upload example you can replace the contents of this function with 
					//examples/profile-avatar-update.js
	
					var deferred = new $.Deferred
	
					var value = $('#avatar').next().find('input[type=hidden]:eq(0)').val();
					if(!value || value.length == 0) {
						deferred.resolve();
						return deferred.promise();
					}
	
	
					//dummy upload
					setTimeout(function(){
						if("FileReader" in window) {
							//for browsers that have a thumbnail of selected image
							var thumb = $('#avatar').next().find('img').data('thumb');
							if(thumb) $('#avatar').get(0).src = thumb;
						}
						
						deferred.resolve({'status':'OK'});
	
						if(last_gritter) $.gritter.remove(last_gritter);
						last_gritter = $.gritter.add({
							title: 'Avatar Updated!',
							text: 'Uploading to server can be easily implemented. A working example is included with the template.',
							class_name: 'gritter-info gritter-center'
						});
						
					 } , parseInt(Math.random() * 800 + 800))
	
					return deferred.promise();
					
					// ***END OF UPDATE AVATAR HERE*** //
				},
				
				success: function(response, newValue) {
				}
			})
		}catch(e) {}
		
		/**
		//let's display edit mode by default?
		var blank_image = true;//somehow you determine if image is initially blank or not, or you just want to display file input at first
		if(blank_image) {
			$('#avatar').editable('show').on('hidden', function(e, reason) {
				if(reason == 'onblur') {
					$('#avatar').editable('show');
					return;
				}
				$('#avatar').off('hidden');
			})
		}
		*/
	
		//another option is using modals
		$('#avatar2').on('click', function(){
			var modal = 
			'<div class="modal fade">\
			  <div class="modal-dialog">\
			   <div class="modal-content">\
				<div class="modal-header">\
					<button type="button" class="close" data-dismiss="modal">&times;</button>\
					<h4 class="blue">Change Avatar</h4>\
				</div>\
				\
				<form class="no-margin">\
				 <div class="modal-body">\
					<div class="space-4"></div>\
					<div style="width:75%;margin-left:12%;"><input type="file" name="file-input" /></div>\
				 </div>\
				\
				 <div class="modal-footer center">\
					<button type="submit" class="btn btn-sm btn-success"><i class="ace-icon fa fa-check"></i> Submit</button>\
					<button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cancel</button>\
				 </div>\
				</form>\
			  </div>\
			 </div>\
			</div>';
			
			
			var modal = $(modal);
			modal.modal("show").on("hidden", function(){
				modal.remove();
			});
	
			var working = false;
	
			var form = modal.find('form:eq(0)');
			var file = form.find('input[type=file]').eq(0);
			file.ace_file_input({
				style:'well',
				btn_choose:'Click to choose new avatar',
				btn_change:null,
				no_icon:'ace-icon fa fa-picture-o',
				thumbnail:'small',
				before_remove: function() {
					//don't remove/reset files while being uploaded
					return !working;
				},
				allowExt: ['jpg', 'jpeg', 'png', 'gif'],
				allowMime: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']
			});
	
			form.on('submit', function(){
				if(!file.data('ace_input_files')) return false;
				
				file.ace_file_input('disable');
				form.find('button').attr('disabled', 'disabled');
				form.find('.modal-body').append("<div class='center'><i class='ace-icon fa fa-spinner fa-spin bigger-150 orange'></i></div>");
				
				var deferred = new $.Deferred;
				working = true;
				deferred.done(function() {
					form.find('button').removeAttr('disabled');
					form.find('input[type=file]').ace_file_input('enable');
					form.find('.modal-body > :last-child').remove();
					
					modal.modal("hide");
	
					var thumb = file.next().find('img').data('thumb');
					if(thumb) $('#avatar2').get(0).src = thumb;
	
					working = false;
				});
				
				
				setTimeout(function(){
					deferred.resolve();
				} , parseInt(Math.random() * 800 + 800));
	
				return false;
			});
					
		});
	
		
	
		//////////////////////////////
		$('#profile-feed-1').ace_scroll({
			height: '250px',
			mouseWheelLock: true,
			alwaysVisible : true
		});
	
		$('a[ data-original-title]').tooltip();
	
		$('.easy-pie-chart.percentage').each(function(){
		var barColor = $(this).data('color') || '#555';
		var trackColor = '#E2E2E2';
		var size = parseInt($(this).data('size')) || 72;
		$(this).easyPieChart({
			barColor: barColor,
			trackColor: trackColor,
			scaleColor: false,
			lineCap: 'butt',
			lineWidth: parseInt(size/10),
			animate:false,
			size: size
		}).css('color', barColor);
		});
	  
		///////////////////////////////////////////
	
		//right & left position
		//show the user info on right or left depending on its position
		$('#user-profile-2 .memberdiv').on('mouseenter touchstart', function(){
			var $this = $(this);
			var $parent = $this.closest('.tab-pane');
	
			var off1 = $parent.offset();
			var w1 = $parent.width();
	
			var off2 = $this.offset();
			var w2 = $this.width();
	
			var place = 'left';
			if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) place = 'right';
			
			$this.find('.popover').removeClass('right left').addClass(place);
		}).on('click', function(e) {
			e.preventDefault();
		});
	
	
		///////////////////////////////////////////
		$('#edit-basic')
		.find('input[type=file]').ace_file_input({
			style:'well',
			btn_choose:'Change avatar',
			btn_change:null,
			no_icon:'ace-icon fa fa-picture-o',
			thumbnail:'large',
			droppable:true,
			
			allowExt: ['jpg', 'jpeg', 'png', 'gif'],
			allowMime: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']
		})
		.end().find('button[type=reset]').on(ace.click_event, function(){
			$('#uedit-basic input[type=file]').ace_file_input('reset_input');
		})
		.end().find('.date-picker').datepicker().next().on(ace.click_event, function(){
			$(this).prev().focus();
		})
		$('.input-mask-phone').mask('(999) 999-9999');

		<?php 
			$rec = Parameters::where("name","=","banner")->get();
			$rec = $rec[0];
			$rec->value = "'../app/storage/uploads/".$rec->value."'";
		?>
	
		$('#edit-basic').find('input[type=file]').ace_file_input('show_file_list', [{type: 'image', name: {{$rec->value}}}]);
	
	
		////////////////////
		//change profile
		$('[data-toggle="buttons"] .btn').on('click', function(e){
			var target = $(this).find('input[type=radio]');
			var which = parseInt(target.val());
			$('.user-profile').parent().addClass('hide');
			$('#user-profile-'+which).parent().removeClass('hide');
		});

		$('#editor1').ace_wysiwyg();
		
		
		
		/////////////////////////////////////
		$(document).one('ajaxloadstart.page', function(e) {
			//in ajax mode, remove remaining elements before leaving page
			try {
				$('.editable').editable('destroy');
			} catch(e) {}
			$('[class*=select2]').remove();
		});
	});
		</script>
	@stop