<?php include ('header.php'); ?>    

    <div id='divMapa' style='width: 100%; height: 100%'>
        
    </div>

<script>
    var map;
    var infoWindow;
    var markers = [];
    var lastMarker;
        
    function showInfoAddress() {
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

    function addAddressMarker(addrs) {
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(addrs.GEOC_LAT, addrs.GEOC_LNG),
            title: addrs.title || 'Teste',
            animation: google.maps.Animation.DROP,            
        });
        
        marker.setMap(map);
        marker.address = addrs;
        google.maps.event.addListener(marker, 'click', $.proxy(showInfoAddress, marker));
        markers.push(marker);
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
                
        
        $.getJSON('addresses.json',function(data){            
            for (var i = 0; i < data.length; i++) {
                var addr = data[i];          
                addAddressMarker(addr);
            }
        }).error(function(){
            console.log(arguments);
            alert("Error trying to load addresses");
        });    

    }


</script>


<?php include("footer.php"); ?>