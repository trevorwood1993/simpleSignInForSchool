<?php session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "/dbConnect.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>Simpletown Residents</title>
	<script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.js"></script>
</head>
<style type="text/css">
	th,td{
		border: 1px solid black;
	}
	.pagination li{
		
		list-style: none;
		float:  left;
		height: 40px;width: 40px;line-height: 40px;text-align: center;
		border: 1px solid black;
	}
	.pagination .active{
		background: rgba(255,205,0,1);
	}
	.pagination li:hover:not(.active){
		background: blue;
		cursor: pointer;
	}
</style>
<body>

<?php 
$page = intval($_GET['page']);
$people_per_page = intval($_GET['peoplePerPage']);
if($page == 0){
	$page = 1;
}
if($people_per_page < 1 || $people_per_page > 100){
	$people_per_page = 5;
}
try{
  $results = $db->prepare("SELECT count(user_id) FROM users");
  $results->execute();
  $hold = $results->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo "Data could not be retrieved from the database.";
  exit();
}
$total_users = $hold[0]['count(user_id)'];


$limit2 = ($people_per_page*$page) -$people_per_page;



try{
  $results = $db->prepare("SELECT * FROM users LIMIT $limit2,$people_per_page");
  $results->execute();
  $hold = $results->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo "Data could not be retrieved from the database.";
  exit();
}
echo '<a href="/">index</a>';
echo " $total_users total users";
echo '<table>';
echo '<tr><th>user_id</th><th>username</th><th>userpass</th><th>userdate</th></tr>';
foreach ($hold as $key => $value) {
	$userdate = $value['userdate'];
	$userdate = date('F d, Y',$userdate);

	echo '<tr>';
	echo '<td>'.$value['user_id'].'</td>';
	echo '<td>'.$value['username'].'</td>';
	echo '<td>'.$value['userpass'].'</td>';
	echo '<td>'.$userdate.'</td>';
	echo '</tr>';
}

echo '</table>';


echo '<div class="pag_div"></div>';

?>
Users per page
<select class="usersPerPageSelect" onchange="reload()">
	<option <?php if($people_per_page == 1){echo 'selected';} ?>>1</option>
	<option <?php if($people_per_page == 5){echo 'selected';} ?>>5</option>
	<option <?php if($people_per_page == 10){echo 'selected';} ?>>10</option>
	<option <?php if($people_per_page == 25){echo 'selected';} ?>>25</option>
	<option <?php if($people_per_page == 50){echo 'selected';} ?>>50</option>
	<option <?php if($people_per_page == 100){echo 'selected';} ?>>100</option>
</select>








<script type="text/javascript">
	var page = <?php echo $page;?>,
			page = parseInt(page),
			itemsPerPage = <?php echo $people_per_page;?>,
			itemsPerPage = parseInt(itemsPerPage),
			totalItems = <?php echo $total_users; ?>,
			totalItems = parseInt(totalItems);
	
	function setPagination(page,itemsPerPage,totalItems){

		function listItem(number,active){
			var item = '<li value="'+number+'"';
			if(active == 1){
				item += ' class="active"';
			}
			item += '>'+number+'</li>';
			return item;
		}

		var pagesInFront = 3,
		 	 	pagesInBehind = 3;
		 	 	
		var data = '<ul class="pagination">';	
		var totalPages = Math.ceil(totalItems/itemsPerPage);

		if(totalPages <= (pagesInFront+pagesInBehind+1)){
			for (var i = 1; i <= totalPages; i++) {
				if(i == page){
					active = 1;
				}else{active = 0;}
				data += listItem(i,active);
			}
		}else{
			var front = pagesInFront,
			 	 	behind = pagesInBehind,
			 	 	pageHold = page;

			if(pageHold <= pagesInFront){
				//front
				while(pageHold <= pagesInFront){
					pageHold++;
					behind++;
					front--;
				}
			}else{
				//back
				x = behind + pageHold - totalPages; 
				while(x >= 1){
					behind--;
					front++;
					x--;
				}
			}
			
			//front
			var i = page-front;		
			while(i < page) {
				data += listItem(i,0);
				i++;
			}

			//active
			data += listItem(page,1);

			//behind
			var i = page+1, n = behind+i;
			while(i < n) {
				data += listItem(i,0);
				i++;
			}
		}

		
		data += "</ul>";

		$('.pag_div').append(data);

		$('.pag_div li').on("click",function(){
			if(!$(this).hasClass("active")){
				var page = parseInt($(this).val()),
						UPP = parseInt($('.usersPerPageSelect').val());
				var url = "/users.php?page="+page+"&peoplePerPage="+UPP;
				window.location.href = url;
			}
		});


	}
	setPagination(page,itemsPerPage,totalItems);


	function reload(){
		var page = parseInt($('.pag_div li.active').val()),
				UPP = parseInt($('.usersPerPageSelect').val());
		var url = "/users.php?page="+page+"&peoplePerPage="+UPP;
		window.location.href = url;
	}

</script>




</body>
</html>