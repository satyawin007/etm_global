@extends('masters.master')
	@section('inline_css')
		<style>
			.page-header h1 {
				padding: 0;
				margin: 0 3px;
				font-size: 12px;
				font-weight: lighter;
				color: #2679b5;
			}
			
			button, input, optgroup, select, textarea {
				color: inherit;
				font: inherit;
				margin: 10px;
				padding : 10px;
			}
			a{
				text-decoration:none;
			}
		</style>
	@stop

	@section('bredcum')	
		<small>
			HOME
			<i class="ace-icon fa fa-angle-double-right"></i>
			CONTRACTS
		</small>
	@stop

	@section('page_content')
		<div class="col-xs-12 center">
		<div class="row" style="margin-top: 20px;">
			<?php $jobs = Session::get("jobs");?>
			<?php if(in_array(151, $jobs)){?>
			<a href="contracts">
			<button >
				<i class="ace-icon fa fa-exchange bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  CONTRACTS &nbsp; &nbsp; &nbsp;
			</button>
			</a>
			<?php } if(in_array(152, $jobs)){?>
			<a href="clients">
			<button >
				<i class="ace-icon fa fa-users bigger-300"></i><BR/>
				 &nbsp; &nbsp; &nbsp; CLIENTS &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(152, $jobs)){?>
			<a href="depots">
			<button >
				<i class="ace-icon fa fa-map-pin bigger-300"></i><BR/>
				 &nbsp; &nbsp; &nbsp; DEPOTS &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(153, $jobs)){?>
			<a href="servicelogs">
			<button>
				<i class="ace-icon fa fa-pencil-square-o bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  SERVICE LOGS &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(154, $jobs)){?>
			<a href="vehiclemeeters">
			<button>
				<i class="ace-icon fa fa-tachometer bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  VEHICLE MEETER READING &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(155, $jobs)){?>
			<a href="clientholidays">
			<button style="PADDING-TOP: 16px;">
				<i class="ace-icon fa fa-bullhorn bigger-240"></i><BR/>
				&nbsp; &nbsp; &nbsp;  CLIENT HOLIDAYS &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(156, $jobs)){?>
			<a href="servicelogrequests">
			<button style="PADDING-TOP: 16px;">
				<i class="ace-icon fa fa-pencil-square-o bigger-240"></i><BR/>
				&nbsp;  SERVICE LOG REQUESTS  &nbsp; 
			</button>									
			</a>
			<?php }?>
		</div>
		
		
		</div>
	@stop