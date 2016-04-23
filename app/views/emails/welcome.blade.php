<html>
<head>
	<style>
		table, td, th {
		    border: 1px solid black;
		}
		
		table {
		    border-collapse: collapse;
		    width: 100%;
		}
		
		th {
		    text-align: left;
		}
	</style>

</head>
<body>
    <table >
    <tr>
    	<th>Transaction Type</th>
    	<th>Table Name</th>
    	<th>Rec Id</th>
    	<th>Old Values</th>
    	<th>New Values</th>
    	<th>Created/Updated By</th>
    </tr>
    <tr>
    	<td>{{$transactionType}}</td>
    	<td>{{$tableName}}</td>
    	<td>{{$recId}}</td>
    	<td>{{$oldValues}}</td>
    	<td>{{$newValues}}</td>
    	<td>{{$insertedBy}}</td>
    </tr>
    </table>
</body>
</html>