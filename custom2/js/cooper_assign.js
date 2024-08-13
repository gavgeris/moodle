var baseurl = "/custom2/cooper_assign.php?id=2617&";
$().ready(function() {
	var url = baseurl + "action=getKey"
	console.log(url);
	loadWholePage(url, getKeyCallback);

	$( "#saveBtn" ).click(function() {
		if ($('#sharedkey').val() != "") {
			var url = baseurl + "action=save&sharedkey=" + $('#sharedkey').val()
			console.log(url);
			loadWholePage(url, saveCallback);
		} else {
			swal({
				  title: "Error",
				  text: "Το πεδίο δεν μπορεί να είναι κενο",
				  timer: 1000,
				  showConfirmButton: false
				});
		}
	});
	
	
	$( "#findPartnerBtn" ).click(function() {
		if ($('#sharedkey').val() != "") {
			var url = baseurl + "action=find"
			console.log(url);
			loadWholePage(url, findPartnerCallback);
		} else {
			swal({
				  title: "Error",
				  text: "Πρώτα πρέπει να αποθηκεύσετε τον δικό σας κωδικό",
				  timer: 1000,
				  showConfirmButton: false
				});
		}
	});
});
function getKeyCallback(response)	{
	console.log(response);
	var respArray = JSON.parse(response);
	$('#sharedkey').val(respArray["sharedkey"]);
	if (respArray["sharedkey"] == "") {
		$('#findPartnerBtn').show();
	} else {
		$('#findPartnerBtn').hide();
		$("#findPartnerBtn").trigger( "click" );
	}
}
function saveCallback(response)	{
	var respArray = JSON.parse(response);
	if (respArray["status"] == "OK") {
		swal({
			  title: "Status",
			  text: "Αποθηκεύτηκε",
			  timer: 2000,
			  showConfirmButton: false
			});
	} else {
		alert("Error");
	}
}
function findPartnerCallback(response)	{
	var respArray = JSON.parse(response);
	console.log(respArray);
	
	var number_of_rows = respArray.length;
	var k = 0;
	var table_body = '<table class="table-striped" border="1"><tbody>';
	for(k=0;k<number_of_rows;k++){
		table_body+='<tr>';
		table_body +='<td>';
		table_body +=respArray[k].shared_key;
		table_body +='</td>';
		   
		table_body+='</tr>';
	}
	table_body+='</tbody></table>';
	$('#partners').html(table_body);
	
}