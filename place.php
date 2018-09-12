<?php 
	if(isset($_GET['location'])){
		$location = urlencode($_GET['location']);
		$location_search = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$location&key=AIzaSyDcPmk9sHYh8FEtMugUYbkk660CKC-4Rik");
		$location_search_arr = json_decode($location_search, true);
		if(empty($location_search_arr['results'])) {
			$lat = null;
			$lon = null;
		}else {
			$lat = $location_search_arr['results'][0]['geometry']['location']['lat'];
			$lon = $location_search_arr['results'][0]['geometry']['location']['lng'];
		}
	} elseif(isset($_GET['lat']) && isset($_GET['lon'])){
		$lat = $_GET['lat'];
		$lon = $_GET['lon'];
	} 
?>

<?php
	if(isset($_GET['radius']) && isset($_GET['category']) && isset($_GET['keyword'])):
		$radius = 1609 * floatval($_GET['radius']);
		$category = $_GET['category'];
		$keyword = $_GET['keyword'];	
		$nearby_search = file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lon&radius=$radius&type=$category&keyword=$keyword&key=AIzaSyDcPmk9sHYh8FEtMugUYbkk660CKC-4Rik");
		$nearby_search_arr = json_decode($nearby_search);
		$nearby_search_arr -> startLoc = new StdClass();
		$nearby_search_arr -> startLoc -> lat = floatval($lat);
		$nearby_search_arr -> startLoc -> lng = floatval($lon);
		$nearby_search = json_encode($nearby_search_arr);
		echo $nearby_search;
	elseif(isset($_GET['placeid'])):
		$placeid = $_GET['placeid'];
		$place_details_search = file_get_contents("https://maps.googleapis.com/maps/api/place/details/json?placeid=$placeid&key=AIzaSyDcPmk9sHYh8FEtMugUYbkk660CKC-4Rik");
		$place_details_search_arr = json_decode($place_details_search,true);
		for($i = 0; $i < 5; $i++) {
			if(empty($place_details_search_arr['result']['photos'][$i])) {
				break;
			}
			$photo_reference = $place_details_search_arr['result']['photos'][$i]['photo_reference'];
			$place_photos = file_get_contents("https://maps.googleapis.com/maps/api/place/photo?maxwidth=1000&photoreference=$photo_reference&key=AIzaSyDcPmk9sHYh8FEtMugUYbkk660CKC-4Rik");
		file_put_contents("$i.jpg", $place_photos);
		}
		echo $place_details_search;
	else:
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Travel and Entertainment Search</title>
	<style type="text/css">
		body {
			font-family: "Times New Roman";
		}

		ul {
			margin: 0;
			padding: 0;
		}
		li {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		a {
			margin-left: 10px;
			text-decoration: none;
		}
		a:link, a:visited {
			color: black;
		}

		table {
			border-collapse: collapse;
			margin-top: 25px;
		}
		table, th, td {
			border: 3px solid rgb(200,200,200);
		}

		div#wholeForm {
			margin: auto;
			width: 630px;
			background-color: rgb(250,250,250);
			padding: 0px 10px 50px 10px;
			border: 3px solid rgb(200,200,200);
		}

		hr {
			color: rgb(200,200,200);
		}

		div#show {
			width: 95%;
			margin: auto;
		}
		h1 {
			text-align: center;
			margin: 0;
		}
		h3, p {
			text-align: center;
			margin-top: 20px;
			margin-bottom: 5px;
		}

		form {
			line-height: 25px;
		}
			form li {
				float: left;
			}

			form label {
				font-weight: bold;
			}

			form div#buttons {
				margin-left: 67px;
				float: left;
			}

			input[type="radio"] {
				vertical-align: middle;
			}
		

		table.nearbyResults {
			width: 1200px;
		}

		table.noResults {
			background-color: rgb(245,245,245);
			width: 750px;	
			font-size: large;
		}
			table.noResults p{
				margin: auto;
			}

		img.arrows {
			width: 3%;
			margin-left: auto;
			margin-right: auto;
			display: block;
		}

		#arrowDownReviews a, #reviews a {
			margin-left: 0;
		}

		table.placeReviews, table.placePhotos {
			width: 650px;
			margin: auto;
			font-size: large;
		}
			table.placeReviews tr.headers {
				text-align: center;
			}
			table.placeReviews img {
				width:5%;
			}
			table.placePhotos img {
				width: 650px;
				padding: 15px 15px 15px 5px;
			}

		#reviews, #photos{
			display: none;
		}	

		h3.noPlaceDetails {
			margin: 0;
			font-size: initial;
		}

		#mapCanvas {
			position: absolute;
			left: 10px;
		}

			#mapCanvas #map {
				width: 400px;
				height: 300px;
				z-index: 1;
			}

			#mapCanvas ul {
				position: absolute;
				z-index: 99;
			}

				div.modeButton {
					background-color: rgb(240,240,240);
					line-height: 50px;
					padding-right: 10px;
				}
				div.modeButton:hover {
					background-color: rgb(220,220,220);
				}

		.address {
			position: relative;
		}
	</style>


</head>
<body>
	<div id="wholeForm">
		<h1><i>Travel and Entertainment Search</i></h1>
		<hr>
		<form >
			<label>Keyword</label> 
			<input type="text" name="keyword" id="keyword" required>
			<br>
			<label>Category</label> 
			<select name="category" id="category">
				<option value="default">default</option>
				<option value="cafe">cafe</option>
				<option value="bakery">bakery</option>
				<option value="restaurant">restaurant</option>	
				<option value="beauty_salon">beauty salon</option>
				<option value="casino">casino</option>
				<option value="movie_theater">movie theater</option>
				<option value="lodging">lodging</option>
				<option value="airport">airport</option>
				<option value="train_station">train station</option>
				<option value="subway_station">subway station</option>
				<option value="bus_station">bus station</option>
			</select>
			
			<ul>
				<li>
					<div id="rightPart">
						<label>Distance(miles)</label>
						<input type="text" name="radius" id="radius" placeholder="10">
						<label>from</label>
					</div>
				</li>
				<li>
					<div id="radioButton">
						<input type="radio" id="here" name="startLoc" onclick="uninputLoc()" checked="true"><label>Here</label>
						<br>
						<input type="radio" name="startLoc" onclick="inputLoc()">
						<input type="text" name="location" id="location" placeholder="location" disabled="true" required>
					</div>
				</li>
			</ul>
			<br>
			<br>
			<div id="buttons">
				<input type="submit" name="search" value="Search" id="search"  disabled="true">
				<input type="reset" name="reset" value="Clear" onclick="clearAll()">
			</div>
			<input type="text" name="lat" id="lat" value="0" hidden>
			<input type="text" name="lon" id="lon" value="0" hidden>			
		</form>
	</div>
	<div id="show"></div>
	<div id="mapCanvas">
		<ul>
			<li><div class="modeButton" onclick="calculateAndDisplayRoute(directionService, directionsDisplay, 'WALKING')"><a href="javascript:void(0)">Walk there</a></div></li>
			<li><div class="modeButton" onclick="calculateAndDisplayRoute(directionService, directionsDisplay, 'BICYCLING')"><a href="javascript:void(0)">Bike there</a></div></li>
			<li><div class="modeButton" onclick="calculateAndDisplayRoute(directionService, directionsDisplay, 'DRIVING')"><a href="javascript:void(0)">Drive there</a></div></li>
		</ul>
		<div id='map'></div>
	</div>
	
	<script type="text/javascript">
		var modes = document.getElementById("mapCanvas");
		modes.parentElement.removeChild(modes);

		function request(url) {
			var xhttp = new XMLHttpRequest();
			xhttp.open("GET",url,false);
			xhttp.send();
			if(xhttp.readyState === 4 && xhttp.status === 200) {
				return xhttp.responseText;
			}
		}

		window.onload = function() {
			jsonObj = JSON.parse(request("http://ip-api.com/json"));
			console.log(jsonObj);
			jsonObj.onload = undisableBtn();
		}

		function undisableBtn() {
			document.getElementById("search").disabled = false;
		}

		document.forms[0].onsubmit = function(event){
			event.preventDefault();
			Search();
		}

		function inputLoc() {
			document.getElementById("location").disabled = false;
		}
		function uninputLoc() {
			document.getElementById("location").disabled = true;
		}

		function clearAll() {
			document.getElementById("location").disabled = true;
			document.getElementById("show").innerHTML = ""; 
		}

		function Search() {
			if(document.getElementById("radius").value == "") {
					document.getElementById("radius").value = 10;
			}
			var str = "?keyword=" + document.getElementById("keyword").value + "&category=" + document.getElementById("category").value + "&radius=" + document.getElementById("radius").value + "&startLoc=on";
			if(document.getElementById("here").checked == true) {
				document.getElementById("lat").value = jsonObj.lat;
				document.getElementById("lon").value = jsonObj.lon;
				str += "&search=Search&lat=" + document.getElementById("lat").value + "&lon=" + document.getElementById("lon").value;
			}else {
				str += "&location=" + document.getElementById("location").value + "&search=Search";
			}
			var jsonFile = JSON.parse(request(str));
			console.log(jsonFile);
			startLoc = jsonFile.startLoc;
			var html = "<table align='center'";
			var results = jsonFile.results;
			if(!results || results.length == 0) {
				html += " class='noResults'>";
				html += "<tr>";
				html += "<td><p>No Records has been found</p></td>";
				html += "</tr>";
			}else {
				html += " class='nearbyResults'><tr>";
				html += "<th>Category</th>";
				html += "<th>Name</th>";
				html += "<th>Address</th>";
				for(var i = 0; i < results.length; i++) {
					var content = results[i];
					html += "<tr>";
					html += "<td><img src='" + content.icon + "'></td>";
					html += "<td><a href='javascript:void(0)' onclick=\"placeDetails('" + content.place_id + "')\">" + content.name + "</a></td>";
					html += "<td class='address' id='" + content.place_id + "'><a href='javascript:void(0)' onclick=\"initMap('" +  content.geometry.location.lat + "','" + content.geometry.location.lng + "','" + content.place_id +"')\">" + content.vicinity + "</a></td>";
					html += "</tr>";
				}
			}
			html += "</table>";
			document.getElementById("show").innerHTML = html;
		}

		function placeDetails(placeid) {
			var str = "?placeid=" + placeid;
			var jsonFile = JSON.parse(request(str));
			console.log(jsonFile);
			var html = "<h3>" + jsonFile.result.name + "</h3>";
			html += "<br>";
			html += "<div id='arrowDownReviews'>";
			html += "<a href='javascript:void(0)' onclick='showReviews()'><p>click to show reviews</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' class='arrows'></a>";
			html += "</div>";
			html += "<div id='reviews'>";
			html += "<a href='javascript:void(0)' onclick='hideReviews()'><p>click to hide reviews</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png' class='arrows'></a>";
			html += "<table class='placeReviews'>";
			if(!jsonFile.result.reviews || jsonFile.result.reviews.length == 0) {
				html += "<tr>";
				html += "<td><h3 class='noPlaceDetails'>No Reviews Found</h3></td>";
				html += "</tr>";
			} else {
				var reviews = jsonFile.result.reviews;
				for(var i = 0; i < 5 && i < reviews.length; i++) {
					var review = reviews[i];
					html += "<tr class='headers'>";
					html += "<td><img src='";
					if(review.profile_photo_url) {
						html += review.profile_photo_url;
					}
					html += "'><b>";
					if(review.author_name) {
						html += review.author_name;
					}
					html += "</b></td>";
					html += "</tr>";
					if(review.text) {
						html += "<tr><td>" + review.text + "</td></tr>";
					}
				}
			}
			html += "</table>";
			html += "</div>";
			html += "<div id='arrowDownPhotos'>";
			html += "<a href='javascript:void(0)' onclick='showPhotos()'><p>click to show photos</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' class='arrows'></a>";
			html += "</div>";
			html += "<div id='photos'>";
			html += "<a href='javascript:void(0)' onclick='hidePhotos()'><p>click to hide photos</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png' class='arrows'></a>";
			html += "<table class='placePhotos'>";
			if(!jsonFile.result.photos || jsonFile.result.photos.length == 0) {
				html += "<tr>";
				html += "<td><h3 class='noPlaceDetails'>No Photos Found</h3></td>";
				html += "</tr>";
			}else {
				var photos = jsonFile.result.photos;
				for(var i = 0; i < 5 && i < photos.length; i++) {
					var photo = photos[i];
					html += "<tr>";
					html += "<td><a href='" + i + ".jpg' target='view_window'><img src='" + i + ".jpg?photo_reference=" + photo.photo_reference + "'></a></td>";
					html += "</tr>";
				}
			}
			html += "</table>";
			html += "</div>";
			document.getElementById("show").innerHTML = html;
		}

		function showReviews() {
			if(document.getElementById("photos").style.display != "none") {
				hidePhotos();
			}
			document.getElementById("reviews").style.display= "block";
			document.getElementById("arrowDownReviews").style.display = "none";
		}
		function hideReviews() {
			document.getElementById("reviews").style.display= "none";
			document.getElementById("arrowDownReviews").style.display = "block";
		}

		function showPhotos() {
			if(document.getElementById("reviews").style.display != "none") {
				hideReviews();
			}
			document.getElementById("photos").style.display= "block";
			document.getElementById("arrowDownPhotos").style.display = "none";
		}
		function hidePhotos() {
			document.getElementById("photos").style.display= "none";
			document.getElementById("arrowDownPhotos").style.display = "block";
		}

		place_id = "0";
		function initMap(lati,long,placeid) {
			latitude = Number(lati);
			longitude = Number(long);
			if(place_id == placeid) {
				place_id = "0";
				modes.style.display = "none";
				return;
			}
			modes.style.display = "block";
			document.getElementById(placeid).appendChild(modes);

			directionsDisplay = new google.maps.DirectionsRenderer;
			directionService = new google.maps.DirectionsService;
			var position = {lat: latitude, lng: longitude};
			var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 14,
				center: position
			});
			directionsDisplay.setMap(map);

			marker = new google.maps.Marker({
				position: position,
				map: map
			});
			place_id = placeid;
		}

		function calculateAndDisplayRoute(directionService,directionsDisplay,mode) {
			directionService.route({
				origin: startLoc,
				destination: {lat: latitude, lng: longitude},
				travelMode: mode
			}, function(response, status) {
				if (status == 'OK') {
					directionsDisplay.setDirections(response);
					marker.setMap(null);
				} else {
					window.alert('Directions request failed due to ' + status);
				}
			});
		}
	</script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcPmk9sHYh8FEtMugUYbkk660CKC-4Rik"></script>
</body>
</html>
<?php endif;?>