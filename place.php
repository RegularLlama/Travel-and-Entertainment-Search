<!DOCTYPE html>
<html>
<head><title>Search</title>
</head>
<body>
<style>
table{
	border-collapse: collapse;
}
form#search-box{
	border: 3px solid rgb(206, 206, 206);
	margin: 0 auto;
    width: 60%;
    padding: 1%;
    background-color: rgb(250, 250, 250);
}
form #distance{
	margin-right: 5px;
}
h1{
	text-align: center;
	font-style: italic;
	margin: 0;
}
hr{
	border: none;
	height: 2px;
	background-color: rgb(206, 206, 206);
}
input[type="text"], select{
	margin: 0.5%;
}
input[type="submit"]{
	margin-left: 9%;
}
.style-user-location{
	display: inline-grid;
	width: 25%;
}
.style-user-location input{
	margin: 2% 0.5%;
}
#result-table{
	margin-top: 1%;
}
table#results{ 
	margin: 0 auto;
	width: 90%;
}
table#results th,table#results td{
	border: 3px solid #dedede;
}
table#results td span{
	margin-left: 2%;
}
table#results td span.address:hover{
	color: #F0F0F0;
}
#direction-container{
	z-index: 999;
	position: absolute;
}
#direction-container button{
	background-color: rgb(240, 240, 240);
    padding: 10px;
    cursor: pointer;
    width: 100px;
    border: none;
    display: block;
    outline: none;
}
#direction-container button:hover{
    background-color: rgb(220, 220, 220);
}
#no-result-label{
	border: 4px solid rgb(226,226,226); 
	background: rgb(239,239,239); 
	text-align: center;
	margin: 0 auto;
	width: 90%;
}
#map{
	width: 400px;
	height: 300px;
	position: absolute;
	z-index: 99;
	bottom: 0;
	right: 0;
	display: none;
}
form .place-name{
	margin-left: 2%;
    border: none;
    background-color: #ffffff;
    cursor: pointer;
    background:none;
    outline: none;
}
#rp-table,#reviews-table,#photos-table{
	display: none;
}
#rp-table{
	text-align: center;
    width: 62%;
    margin: 0 auto;
}
#photos-table table{
	margin: 0 auto;
	width: 100%;
}
#photos-table table td, #photos-table table th{
	padding: 2%;
	border: 2px solid rgb(224,224,224);
}
#reviews-table table td{
	text-align: left;
}
#reviews-table table img{
	padding-right: 2px;
}
#reviews-table table th,#reviews-table table td{
	border: 2px solid rgb(224,224,224);
}
#place-name-label{
	margin-bottom: 3%;
	font-weight: bold;
}
#reviews-table{
	padding-bottom: 2%;
}
#reviews-table table{
	width: 100%;
}
.margin-top-2{
	margin-top: 2%;
}
#review-arrow,#photo-arrow{
	cursor: pointer;
}
#pano{
	height: 300px;
}
</style>	
<form id="search-box" method="post" action="">
<h1>Travel and Entertainment Search</h1> <hr>
<b>Keyword</b> <input type="text" name="keyword" id="keyword" required><br />
<b>Category</b> 
<select name="category" id="category">
	<option value="">default</option>
	<option value="cafe">cafe</option>
	<option value="bakery">bakery</option>
	<option value="restaurant">restaurant</option>
	<option value="beauty_salon">beauty salon</option>
	<option value="casino">casino</option>
	<option value="movie_theatre">movie theatre</option>
	<option value="lodging">lodging</option>
	<option value="airport">airport</option>
	<option value="train_station">train station</option>
	<option value="subway_station">subway station</option>
	<option value="bus_station">bus station</option>	
</select><br />
<b>Distance (miles)</b> <input type="text" pattern="^[+-]?(\d*\.)?\d+$" title="Enter distance in digits" name="distance" id="distance" placeholder="10"><b>from</b>
<div class="style-user-location">
<div><input type="radio" name="place" value="here" id="radio-here" checked><label for="radio-here">Here</label></div>
<div><input type="radio" name="place" id="radio-user-location" value="location"><input type="text" name="user-input-location" id="user-location" placeholder="location" required="true" disabled="true"></div>
</div>
<br />
<input type="submit" name="Search" value="Search" id="search-button" disabled>
<input type="reset" name="clear-search" onclick="clearData()" value="Clear">
<input type="hidden" name="checkSubmit" id="checkSubmit" value="0">
</form>
<div id="pano"></div>
<div id="right-panel"></div>
<div id="map"></div>
<div id="direction-container" style="display:none;">
  <button value="WALKING">Walk there</button>
  <button value="BICYCLING">Bike there</button>
  <button value="DRIVING">Drive there</button>
</div>
<div id="result-table"></div>
<div id="rp-table">
	<div id="place-name-label"></div>
	<div>click to <span id="review-text">show</span> reviews</div>
	<img id="review-arrow" data-flag="down" data-text="review-text" data-image="photo-arrow" data-sub="reviews-table" data-opp="photos-table" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" width="40" onclick="toggleArrow(this);">
	<div id="reviews-table"></div>
	<div class="margin-top-2">click to <span id="photo-text">show</span> photos</div>
	<img id="photo-arrow" data-flag="down" data-text="photo-text" data-image="review-arrow" data-sub="photos-table" data-opp="reviews-table" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" width="40" onclick="toggleArrow(this);">
	<div id="photos-table"></div>
</div>
<?php 
$key = "AIzaSyB9onym3Nx00wpelhy-bwgcHsx-qqtY3p8";
	$arrContextOptions=array(
	    "ssl"=>array(
	        "verify_peer"=>false,
	        "verify_peer_name"=>false,
	    ),
	);  
if(isset($_POST['Search'])) {
	$keyword = $_POST['keyword']; 
	$category = $_POST['category'];
	$distanceM = $_POST['distance']; 
	if(empty($distanceM)){
		$distance = 10*1609.344;
	}else{
		$distance = $distanceM*1609.344;
	}
	$place = $_POST['place']; 
	$inputLoc = null;
	if($place == "location"){
		$inputLoc = $_POST['user-input-location'];
		$geoCodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($inputLoc)."&key=".$key;
		$geoCodeResponseJson = json_decode(file_get_contents($geoCodeUrl, false, stream_context_create($arrContextOptions)));
		if($geoCodeResponseJson -> status == "OK"){
			$geoCodeLocation = $geoCodeResponseJson -> results[0] -> geometry -> location;
			$location = $geoCodeLocation -> lat .",".$geoCodeLocation -> lng;
		}
	}else{
		$location = $_GET['ip-api'];
	}
	if(isset($location)){
		$placesUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$location."&radius=".$distance."&keyword=".urlencode($keyword)."&type=".$category."&key=".$key;
		$placesResponse = file_get_contents($placesUrl, false, stream_context_create($arrContextOptions));
	}
	?>
	<script type="text/javascript">
		hereLat = 0, hereLng = 0;
		var keyword = document.getElementById("keyword").value = <?php echo json_encode($keyword); ?>;
		var sel = document.getElementById("category");
		var selIndex = 0;
	    for(var i = 0, j = sel.options.length; i < j; ++i) {
	        if(sel.options[i].value ===  '<?php echo $category; ?>') {
	           sel.selectedIndex = i;
	           selIndex = i;
	           break;
	        }
	    }
	    var distance = document.getElementById("distance").value = <?php echo json_encode($distanceM); ?>;
	    var inputLoc = "";

	    <?php if(isset($location)){ if(isset($inputLoc)){ ?>
	    	inputLoc = '<?php echo $inputLoc; ?>';
			document.getElementById("radio-user-location").checked = true;
			document.getElementById("user-location").disabled = false;
			document.getElementById("user-location").required = true;
			document.getElementById("user-location").value = <?php echo json_encode($inputLoc);?>;
			hereLat = <?php echo $geoCodeLocation -> lat; ?>;
			hereLng = <?php echo $geoCodeLocation -> lng; ?>;
	    <?php }else{ $coord = explode(",", $location);?>
	    	document.getElementById("radio-here").checked = true;
	    	document.getElementById("user-location").disabled = true;
			document.getElementById("user-location").required = false;
			hereLat = <?php echo $coord[0]; ?>;
			hereLng = <?php echo $coord[1]; ?>;
	    <?php }}?>	
	</script>
	<?php } ?>
<script type="text/javascript">
	function clearData(){
		document.getElementById("keyword").value = "";
		document.getElementById("category").selectedIndex = 0;
		document.getElementById("distance").value = "";
		document.getElementById("radio-user-location").checked = true;
		document.getElementById("user-location").disabled = true;
		document.getElementById("result-table").innerHTML = "";
		document.getElementById("rp-table").innerHTML = "";
		document.getElementById("map").innerHTML = "";
		document.getElementById("direction-container").style.display = "none";
	}
	function fetchGeoLocation(geoLocationJson){
		window.history.replaceState(null, null, "?ip-api=" + geoLocationJson.lat+","+geoLocationJson.lon);
		document.getElementById('search-button').disabled=false;
	}	
	document.getElementById("radio-user-location").addEventListener('change', function(){
    	document.getElementById("user-location").disabled = !this.checked ;
    	document.getElementById("user-location").required = this.checked ;

	});	
	document.getElementById("radio-here").addEventListener('change', function(){
    	document.getElementById("user-location").disabled = this.checked ;
    	document.getElementById("user-location").required = !this.checked ;
	});																										
</script>
<script src="http://ip-api.com/json?callback=fetchGeoLocation" type="text/javascript"></script>
<script type="text/javascript">
	thisLat = 0, thisLng = 0;
	clickedEle = "";
	function toggleArrow(ele){
		if(ele.dataset.flag == "down"){
			ele.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png";
			document.getElementById(ele.dataset.image).src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
			document.getElementById(ele.dataset.image).dataset.flag = "down";
			document.getElementById(document.getElementById(ele.dataset.image).dataset.text).innerHTML = "show";
			document.getElementById(ele.dataset.opp).style.display = "none";
			document.getElementById(ele.dataset.sub).style.display = "block";
			document.getElementById(ele.dataset.text).innerHTML = "hide";
			ele.dataset.flag = "up";

		}else{
			ele.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
			document.getElementById(ele.dataset.text).innerHTML = "show";
			document.getElementById(ele.dataset.sub).style.display = "none";
			ele.dataset.flag = "down";
		}
	}

	function setAddressParams(ele, lat, lng){
		thisLat = lat;
		thisLng = lng;
		var rect = ele.getBoundingClientRect();
		document.getElementById('map').style.left = rect.left+"px";
		document.getElementById('direction-container').style.left = rect.left+"px";
		document.getElementById('map').style.top = (rect.bottom+document.documentElement.scrollTop)+"px";
		document.getElementById('direction-container').style.top = document.getElementById('map').style.top;
		if(document.getElementById('map').style.display === "block" && clickedEle == ele.id){
			document.getElementById('map').style.display = "none";
			document.getElementById('direction-container').style.display = "none";
		}else{
			document.getElementById('map').style.display = "block";
			document.getElementById('direction-container').style.display = "block";
		}
		clickedEle = ele.id;
		initMap();
	}

	function initMap() {
        var coords = {lat: thisLat, lng: thisLng};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: coords
        });
        var marker = new google.maps.Marker({
          position: coords,
          map: map
        });
    }

    var buttons  = document.getElementsByTagName("button");
	for(var i=0; i<buttons.length;i++){
		buttons[i].addEventListener("click", function(event){
		    calculateAndDisplayRoute(event.target.value);
		});
	}

    function calculateAndDisplayRoute(selectedMode) {
    	var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 14,
          center: {lat: hereLat, lng: hereLng}
        });
        directionsDisplay.setMap(map);
        directionsDisplay.setPanel(document.getElementById('right-panel'));

        directionsService.route({
          origin: {lat: hereLat, lng: hereLng}, 
          destination: {lat: thisLat, lng: thisLng},  
          travelMode: google.maps.TravelMode[selectedMode],
          provideRouteAlternatives: true
        }, function(response, status) {
          if (status == 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
		var coords = {lat: thisLat, lng: thisLng};
        var panorama = new google.maps.StreetViewPanorama(
	      document.getElementById('pano'), {
	        position: coords,
        pov: {
          heading: 34,
          pitch: 10
        }
	      });
	  map.setStreetView(panorama);
    }

	var resultTable = "";
	var key = "AIzaSyB9onym3Nx00wpelhy-bwgcHsx-qqtY3p8";
	<?php if(isset($placesResponse)) {?>
	var places = <?php echo json_encode($placesResponse); ?>;
	var placeJson = JSON.parse(places);
	if(placeJson.status == "OK"){
		resultTable += "<table id='results'><tr><th>Category</th><th>Name</th><th>Address</th></tr>";
		for (i = 0; i < placeJson.results.length; i++) {
			thisLat = placeJson.results[i].geometry.location.lat;
			thisLng = placeJson.results[i].geometry.location.lng;
			resultTable +="<tr>";
			resultTable +="<td><span><img src='"+placeJson.results[i].icon+"' height='40'></span></td>";
			resultTable +="<td><form action='' method='POST'><input type='hidden' name='place-id' value='"+placeJson.results[i].place_id+"'><input type='hidden' name='keyword' value='"+keyword+"'><input type='hidden' name='category' value='"+selIndex+"'><input type='hidden' name='distance' value='"+distance+"'><input type='hidden' name='user-location' value='"+inputLoc+"'><input class='place-name' type='submit' name='place-name-submit' value=\""+placeJson.results[i].name+"\"></form></td>";
			resultTable +="<td><span class='address' id='"+i+"' style='cursor:pointer' onclick='setAddressParams(this,"+thisLat+","+thisLng+");'>"+placeJson.results[i].vicinity+"</span></td>";
			resultTable +="</tr>";
		}
		resultTable +="</table>";
	}else{
		resultTable +="<div id='no-result-label'>No Records has been found</div>"
	}
	<?php }else if(isset($_POST['Search'])){ ?>
	resultTable +="<div id='no-result-label'>No Records has been found</div>";
	<?php } ?>
	document.getElementById("result-table").innerHTML = resultTable;
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9onym3Nx00wpelhy-bwgcHsx-qqtY3p8&callback=initMap"></script>
<?php 
	if(isset($_POST['place-name-submit'])) {
	 ?>
<script type="text/javascript">
		document.getElementById("keyword").value = '<?php echo $_POST['keyword'];?>';
		document.getElementById("category").selectedIndex = '<?php echo $_POST['category'];?>';
		document.getElementById("distance").value = '<?php echo $_POST['distance'];?>';
		var userLocation = '<?php echo $_POST['user-location'];?>';
		if(userLocation){
			document.getElementById("radio-user-location").checked = true;
			document.getElementById("user-location").disabled = false;
			document.getElementById("user-location").required = true;
			document.getElementById("user-location").value = userLocation;
		}else{
			document.getElementById("radio-here").checked = true;
	    	document.getElementById("user-location").disabled = true;
			document.getElementById("user-location").required = false;
		}
		document.getElementById("rp-table").style.display= "block";
		document.getElementById("place-name-label").innerHTML = "<?php echo $_POST['place-name-submit'] ?>";
		var rtext = "<table>";	var ptext = "<table>";
<?php   $photoPresent = false; $pCount = 0;
		$placeDetailsUrl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$_POST['place-id']."&key=".$key;
		$placeDetailsResponse = file_get_contents($placeDetailsUrl, false, stream_context_create($arrContextOptions));
		$placeDetailsResponseJson = json_decode($placeDetailsResponse);
		if($placeDetailsResponseJson -> status == "OK"){
			if(isset($placeDetailsResponseJson -> result -> photos) && !empty($placeDetailsResponseJson -> result -> photos)){
				for($i = 0; $i < 5; $i++) {
					if(isset($placeDetailsResponseJson -> result -> photos[$i]) && isset($placeDetailsResponseJson -> result -> photos[$i] -> photo_reference) && !empty($placeDetailsResponseJson -> result -> photos[$i] -> photo_reference)){
						$photoPresent = true;
		            	$photoUrl = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photoreference=".$placeDetailsResponseJson -> result -> photos[$i] -> photo_reference."&key=".$key;
		            	file_put_contents(($i+1).".jpeg",file_get_contents($photoUrl, false, stream_context_create($arrContextOptions)));
		            	$pCount++;
	            	}
         		}
         	}
         		?>
	var placeDetails = <?php echo json_encode($placeDetailsResponse) ?>;
	var placeDetailsJson = JSON.parse(placeDetails);
	var rCount = 0;
	
	if(placeDetailsJson.result.reviews){
		for(var i=0; i<placeDetailsJson.result.reviews.length; i++){
			rCount += 1;
			var review = placeDetailsJson.result.reviews[i];
			var authorName = review.author_name ? review.author_name : "";
			var profilePhoto = review.profile_photo_url ? review.profile_photo_url : "";
			var text = review.text ? review.text : "";
			rtext += "<tr><th><img src='"+profilePhoto+"' width='30'>"+authorName+"</th></tr><tr><td>"+text+"</td></tr>";
			if(rCount == 5){
				break;
			}
		}
	}else{
		rtext +="<tr><th style='padding:0'>No Reviews Found</th></tr>";
	}
		<?php if($photoPresent){ ?>
			ptext = "<table>";
			var pLength = <?php echo $pCount; ?>;
				for(var i=0; i<pLength; i++){
					ptext += "<tr><td><a href='"+(i+1)+".jpeg' target='_blank'><img border='0' width='100%' height='500px' src='"+(i+1)+".jpeg'/></a></td></tr>";
				}
		<?php } else{?>
			ptext += "<tr><th style='padding:0'>No Photos Found</th></tr>";
		<?php }
		}else{ ?>
			rtext +="<tr><th style='padding:0'>No Reviews Found</th></tr>";
			ptext += "<tr><th style='padding:0'>No Photos Found</th></tr>";
		<?php } ?>
	rtext += "</table>";
	document.getElementById("reviews-table").innerHTML = rtext;
	ptext += "</table>";
	document.getElementById("photos-table").innerHTML = ptext;
	</script>
		<?php
	}
?> 
</body>
</html>


