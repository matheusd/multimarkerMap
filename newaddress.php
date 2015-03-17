<?php include('header.php'); ?>

<div class="container">
<form>    
    <div class="form-group">
        <label for="location">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Secret password">
    </div>

    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" class="form-control" id="location" placeholder="Enter adress for geolocation" value="520, avenida 4, centro, orlândia, sp, brasil">
    </div>
    
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control" id="title" placeholder="Enter Title for marker">
    </div>
    
    <div class="form-group">
        <label for="content">Description</label>
        <input type="content" class="form-control" id="content" placeholder="Enter description for marker">
    </div>
    
    <button type="button" class="btn btn-default" id="btnGeocode">Get Location</button>
    <button type="button" class="btn btn-primary disabled" disabled="disabled" id="btnAddAddress">Add Address</button>
</form>
</div>

    <div class="container">
        <p><small id="msgStatus"></small></p>
    </div>

    <div  class="container" id='divMapa'>
        
    </div>

<script>
    var geocoder;
    var map;
    var infoWindow;
    var lastMarker;


    function showInfoAddress(ende) {
        var marker = this;        
        content = marker.address.content || 'content';
        
        infoWindow.setContent(content);

        if (lastMarker && (lastMarker != marker)) {
            lastMarker.setAnimation(null);
        }

        if (marker.getAnimation() != null) {
            marker.setAnimation(null);
            infoWindow.close();
        } else {            
            infoWindow.open(map, marker);
        }
        lastMarker = marker;
    }
    
    function addMarker(lat, lng) {        
        var newAddress = {
            GEOC_LAT: lat, GEOC_LNG: lng, title: $("#title").val(),
            content: $("#content").val(), location: $("#location").val()
        }
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng),
            title: newAddress.title,
            animation: google.maps.Animation.DROP,            
        });
        
        marker.setMap(map);
        marker.address = newAddress;
        google.maps.event.addListener(marker, 'click', $.proxy(showInfoAddress, marker));        
        lastMarker = marker;
        $("#btnAddAddress").removeAttr("disabled").removeClass("disabled");
    }
    
    
    function geocode() {
        if (lastMarker) {
            lastMarker.setMap(null);
            lastMarker = null;
        }
        
        var address = $("#location").val();
        geocoder.geocode( { 'address': address}, function(results, status) {            
            if (status == google.maps.GeocoderStatus.OK) {
                addMarker(results[0].geometry.location.lat(), results[0].geometry.location.lng());                
            } else {                
                alert("Geocode was not successful for the following reason: " + status);                
            }            
        });
        
    }
    
    function remotelyAddAddress() {
        $("#btnAddAddress").attr("disabled", "disabled").addClass("disabled");
        if (!lastMarker) return;
        
        $("#msgStatus").html("Trying to add");
        
        $.ajax({
            url: 'addNewAddress.php',
            data: $.extend({password: $("#password").val()}, lastMarker.address),
            method: "POST",
            success: function () {
                $("#location").val("").focus();
                $("#title").val("");
                $("#content").val("");
                $("#msgStatus").html("Added!");
            },
            error: function (xhr, status, code) { 
                alert("Error (" + code + "): \n\n" + xhr.responseText);
                $("#msgStatus").html("Error: " + xhr.responseText);
            },
        });
    }

    function initMaps() {
        $("#divMapa").css('height', '40em');

        var mapOptions = {
          center: new google.maps.LatLng(-15.47, -47.52),
          zoom: 4,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };        
        
        geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById("divMapa"),
            mapOptions);
        infoWindow = new google.maps.InfoWindow({
            content: ''
        });
                
                
        $("#btnGeocode").on("click", geocode);
        $("#btnAddAddress").on("click", remotelyAddAddress);
        

    }


</script>


<?php include('footer.php'); ?>