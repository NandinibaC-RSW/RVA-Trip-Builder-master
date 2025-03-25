<?php

$post_id = $_GET['trip'];
$day = $_GET['day'];

    $trip_title = get_the_title($post_id);
    $current_route = "_route_" . $day;
    $prev_day_end_address = "_route_day_start_address_" .$day;
    $prev_day_end_lat = "_route_day_start_address_lat_" .$day;
    $prev_day_end_lng = "_route_day_start_address_lng_" .$day;
    $route_title_name = "_route_title_" .$day;
    $destination_category_name = "_route_destination_category_" .$day;
    $route_day_end_address_name = "_route_day_end_address_" .$day;
    $route_day_end_address_lat_name = "_route_day_end_address_lat_" .$day;
    $route_day_end_address_lng_name = "_route_day_end_address_lng_" .$day;
    $route_time_name = "_route_time_" .$day;
    $route_miles_name = "_route_miles_" .$day;
    $destination_name = "_route_destination_" .$day;
    $current_value = get_post_meta($post_id, $current_route, true);
    if ($current_value) {
        $trip_day_starting_address = get_post_meta($post_id, $prev_day_end_address, true);
        $trip_day_starting_lat = get_post_meta($post_id, $prev_day_end_lat, true);
        $trip_day_starting_lng = get_post_meta($post_id, $prev_day_end_lng, true);
        $destination_category = get_post_meta($post_id, $destination_category_name, true);
        $destination = get_post_meta($post_id, $destination_name, true);
        $route_day_end_address = get_post_meta($post_id, $route_day_end_address_name, true);
        $route_day_end_address_lat = get_post_meta($post_id, $route_day_end_address_lat_name, true);
        $route_day_end_address_lng = get_post_meta($post_id, $route_day_end_address_lng_name, true);
        $route_time = get_post_meta($post_id, $route_time_name, true);
        $route_miles = get_post_meta($post_id, $route_miles_name, true);
        $route_title = get_post_meta($post_id, $route_title_name, true);
    } else {
        echo '<script>window.location.href = "' . admin_url( 'admin.php?page=trip-day&tab=day_overview&trip=' . $post_id ) . '";</script>';
    }



echo '<h1>'. $trip_title .'</h1>';
echo '<h5>Stay Title : </h5>';
echo '<h3>'. $route_title .'</h3>';
echo '<h5>Starting Location  : </h5>';
echo '<h3>'. $trip_day_starting_address .'</h3>';
echo '<h5>End Point  : </h5>';
echo '<h3>'. $route_day_end_address .'</h3>';
echo '<h5>Expected Duration / Time  : </h5>';
echo '<h3>'. $route_miles .'&nbsp;/&nbsp; '. $route_time .'</h3>';


    
echo '<br/><br/>';


echo '<div>';
echo '<div id="map-canvas"></div>';
echo '</div>';
echo '<div class="card">';
render_document_upload_meta_box($post_id, $day);
echo '</div>';
echo '<script>
function initMap() {
    var pointA = new google.maps.LatLng('.$trip_day_starting_lat.', '.$trip_day_starting_lng.'),
        pointB = new google.maps.LatLng('.$route_day_end_address_lat.', '.$route_day_end_address_lng.'),
        myOptions = {
            zoom: 7,
            center: pointA
        },
        map = new google.maps.Map(document.getElementById("map-canvas"), myOptions),
        // Instantiate a directions service.
        directionsService = new google.maps.DirectionsService,
        directionsDisplay = new google.maps.DirectionsRenderer({
            map: map
        }),
        markerA = new google.maps.Marker({
            position: pointA,
            title: "point A",
            label: "A",
            map: map
        }),
        markerB = new google.maps.Marker({
            position: pointB,
            title: "point B",
            label: "B",
            map: map
        });

    // get route from A to B
    calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB);

}

function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
    directionsService.route({
        origin: pointA,
        destination: pointB,
        avoidTolls: true,
        avoidHighways: false,
        travelMode: google.maps.TravelMode.DRIVING
    }, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        } else {
            window.alert(status);
        }
    });
}

initMap();

</script>';