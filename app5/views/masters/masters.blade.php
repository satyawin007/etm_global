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
			ADMINISTRATION
			<i class="ace-icon fa fa-angle-double-right"></i>
			MASTERS
		</small>
	@stop

	@section('page_content')
		<div class="col-xs-12 center">
		<div class="row" style="margin-top: 20px;">
			<?php $jobs = Session::get("jobs");?>
			<?php if(in_array(151, $jobs)){?>
			<a href="employees">
			<button >
				<i class="ace-icon fa fa-user bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  EMPLOYEES &nbsp; &nbsp; &nbsp;
			</button>
			</a>
			<?php } if(in_array(152, $jobs)){?>
			<a href="states">
			<button >
				<i class="ace-icon fa fa-globe bigger-300"></i><BR/>
				 &nbsp; &nbsp; &nbsp; STATES &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(153, $jobs)){?>
			<a href="districts">
			<button>
				<i class="ace-icon fa fa-tag bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  DISTRICTS &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(154, $jobs)){?>
			<a href="cities">
			<button>
				<i class="ace-icon fa fa-map-marker bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  CITIES &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(155, $jobs)){?>
			<a href="officebranches">
			<button>
				<i class="ace-icon fa fa-home bigger-300"></i><BR/>
				&nbsp; &nbsp; &nbsp;  OFFICE BRANCHS &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(156, $jobs)){?>
			<a href="vehicles">
			<button style="PADDING-TOP: 16px;">
				<i class="ace-icon fa fa-bus bigger-240"></i><BR/>
				&nbsp; &nbsp; &nbsp;  VEHICLES &nbsp; &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(157, $jobs)){?>
			<!-- 
			<a href="employeebattas">
			<button style="PADDING-TOP: 16px;">
				<i class="ace-icon fa fa-rupee bigger-240"></i><BR/>
				&nbsp;  DRIVER/HELPER BATTA  &nbsp; 
			</button>									
			</a>
			 -->
			<?php }?>
		</div>
		
		<div class="row" style="margin-top: 20px;">
			<?php if(in_array(158, $jobs)){?>
			<a href="servicedetails"><button >
				<i class="ace-icon fa fa-road bigger-300"></i><BR/>
				&nbsp; SERVICE NO.s &nbsp; 
			</button>
			</a>
			<?php } if(in_array(159, $jobs)){?>
			<!-- 
			<a href="meeterreading">
			<button >
				<i class="ace-icon fa fa-dashboard bigger-300"></i><BR/>
				 &nbsp;  MEETER READING &nbsp; 
			</button>
			</a>
			 -->
			<?php } if(in_array(160, $jobs)){?>
			<a href="lookupvalues">
			<button>
				<i class="ace-icon fa fa-search bigger-300"></i><BR/>
				&nbsp; &nbsp; LOOKUP DATA &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(161, $jobs)){?>
			<a href="bankdetails">
			<button>
				<i class="ace-icon fa fa-bank bigger-300"></i><BR/>
				&nbsp; BANK ACCOUNTS &nbsp; 
			</button>
			</a>
			<?php } if(in_array(161, $jobs)){?>
			<a href="cards">
			<button>
				<i class="ace-icon fa fa-credit-card bigger-300"></i><BR/>
				&nbsp; CARDS &nbsp; 
			</button>
			</a>
			<?php } if(in_array(162, $jobs)){?>
			<a href="financecompanies">
			<button>
				<i class="ace-icon fa fa-building bigger-300"></i><BR/>
				&nbsp; FINANCIAL COMPANIES &nbsp; 
			</button>
			</a>
			<?php } if(in_array(163, $jobs)){?>
			<a href="creditsuppliers">
			<button>
				<i class="ace-icon fa fa-sign-in bigger-300"></i><BR/>
				&nbsp; CREDIT SUPPLIERS  &nbsp; 
			</button>				
			</a>
			<?php }?>
		</div>
		
		<div class="row" style="margin-top: 20px;">
			<?php if(in_array(164, $jobs)){?>
			<a href="salarydetails"><button >
				<i class="ace-icon fa fa-file-text bigger-300"></i><BR/>
				&nbsp; SALARY DETAILS &nbsp; 
			</button>
			</a>
			<?php } if(in_array(165, $jobs)){?>
			<a href="fuelstations">
			<button >
				<i class="ace-icon fa fa-flask bigger-300"></i><BR/>
				 &nbsp;  FUEL STATIONS &nbsp; 
			</button>
			</a>
			<?php } if(in_array(166, $jobs)){?>
			<a href="loans">
			<button>
				<i class="ace-icon fa fa-exchange bigger-300"></i><BR/>
				&nbsp; &nbsp; LOANS &nbsp; &nbsp; 
			</button>
			</a>
			<?php } if(in_array(167, $jobs)){?>
			<a href="dailyfinances">
			<button>
				<i class="ace-icon fa fa-clipboard bigger-300"></i><BR/>
				&nbsp; DAILY FINANCES &nbsp; 
			</button>
			</a>
			<?php } if(in_array(168, $jobs)){?>
			<a href="serviceproviders">			
			<button style="PADDING-TOP: 16px;">
				<i class="ace-icon fa fa-users bigger-240"></i><BR/>
				&nbsp; MANAGE SERVICE PROVIDERS &nbsp; 
			</button>	
			</a>
			<?php } if(in_array(168, $jobs)){?>
			<a href="uploads">			
			<button style="PADDING-TOP: 16px;">
				<i class="ace-icon fa fa-upload bigger-240"></i><BR/>
				&nbsp; UPLOADS &nbsp; 
			</button>	
			</a>
			<?php }?>				
		</div>
		</div>
	@stop