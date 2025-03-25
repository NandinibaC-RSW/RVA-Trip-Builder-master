document.addEventListener("DOMContentLoaded", function() { 

//Autocomplete For Destination City address
var isStartingAddressChange = false;
var destination_city = document.getElementById('get_address');
var autocomplete = new google.maps.places.Autocomplete(destination_city);
google.maps.event.addListener(autocomplete, 'place_changed', function () {
    var place = autocomplete.getPlace();
    jQuery('#address_lat').val(place.geometry.location.lat());
    jQuery('#address_lng').val(place.geometry.location.lng());
    IsSearchCityChange = true;
});

//Autocomplete For starting address
var IsSearchCityChange=false;
var inputStartingAddress = document.getElementById('trip_starting_address');
var autocompleteStartingAddress = new google.maps.places.Autocomplete(inputStartingAddress);
google.maps.event.addListener(autocompleteStartingAddress, 'place_changed', function () {
    var place = autocompleteStartingAddress.getPlace();
    jQuery('#starting_address_lat').val(place.geometry.location.lat());
    jQuery('#starting_address_lang').val(place.geometry.location.lng());
    isStartingAddressChange = true;
});

//Autocomplete For ending address
var isEndingAddressChange = false;
var inputEndingAddress = document.getElementById('trip_ending_address');
var autocompleteEndingAddress = new google.maps.places.Autocomplete(inputEndingAddress);
google.maps.event.addListener(autocompleteEndingAddress, 'place_changed', function () {
    var place = autocompleteEndingAddress.getPlace();
    jQuery('#ending_address_lat').val(place.geometry.location.lat());
    jQuery('#ending_address_lang').val(place.geometry.location.lng());
    isEndingAddressChange = true;
    });

//Autocomplete For destination popup
var isDestAddressChange = false;
var inputDestAddress = document.getElementById('dest_address');
var autocompleteDestAddress = new google.maps.places.Autocomplete(inputDestAddress);
google.maps.event.addListener(autocompleteDestAddress, 'place_changed', function () {
    var place = autocompleteDestAddress.getPlace();
    jQuery('#dest_address_lat').val(place.geometry.location.lat());
    jQuery('#dest_address_lng').val(place.geometry.location.lng());
    isDestAddressChange = true;
    });
});
jQuery(document).ready(function($){
    var mediaUploader;

    $('#upload-image-button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#destination-image').val(attachment.url);
            $('#image-preview').html('<img src="'+attachment.url+'" style="max-width: 200px; height: auto;" />');
        });

        mediaUploader.open();
    });
    
    $('#upload_document_button').on('click', function(e) {
        e.preventDefault();
        var customUploader = wp.media({
            title: 'Select Documents',
            button: {
                text: 'Select'
            },
            multiple: true
        });
        
        customUploader.on('select', function() {
            var attachments = customUploader.state().get('selection').toJSON();
            var attachmentIds = [];
            
            // Collect attachment IDs
            attachments.forEach(function(item) {
                attachmentIds.push(item.id);
            });
            
            // Update hidden input with attachment IDs
            $('#upload_document').val(attachmentIds.join(','));
            
            // AJAX request to update post meta
            var postId = $('#post_id').val();
            var day = $('#days').val();
            
            // Debug data before sending
            console.log('Sending AJAX with data:', {
                postId: postId,
                day: day,
                attachmentIds: attachmentIds.join(','),
                nonce: $('#document_upload_nonce').val()
            });
            
            var data = {
                action: 'upload_documents',
                post_id: postId,
                day: day,
                attachment_ids: attachmentIds.join(','),
                security: $('#document_upload_nonce').val()
            };
            
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                success: function(response) {
                    console.log('AJAX Response:', response);
                    if (response.success) {
                        // Immediately update the document preview table
                        var tableBody = $('#document_preview tbody');
                        tableBody.empty();
                        
                        attachments.forEach(function(attachment) {
                            var fileName = attachment.filename || attachment.title;
                            var fileUrl = attachment.url;
                            var fileId = attachment.id;
                            
                            tableBody.append(
                                '<tr>' +
                                    '<td><b>' + fileName + '</b></td>' +
                                    '<td class="wrap-right">' +
                                        '<a class="button view-button" href="' + fileUrl + '" target="_blank">View</a>&nbsp;' +
                                        '<a class="mt-1" href="admin.php?page=trip-day&tab=edit_day&trip=' + postId + '&day=' + day + '&file_delete=' + fileId + '"><span class="dashicons dashicons-trash"></span></a>&nbsp;' +
                                    '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        console.error('AJAX Error Response:', response);
                        alert('Error updating files. Please check console for details.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    alert('Error uploading files. Please check console for details.');
                }
            });
        });
        
        customUploader.open();
    });
    $('#upload_trip_document_button').click(function() {
        var customUploader = wp.media({
            title: 'Select Documents',
            button: {
                text: 'Select'
            },
            multiple: true
        });

        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').toJSON();
            var attachmentIds = [];
            attachment.forEach(function(item) {
                attachmentIds.push(item.id);
            });
            $('#upload_trip_document').val(attachmentIds.join(','));

            // AJAX request to update post meta
            var postId = $('#post_id').val();
            var data = {
                action: 'upload_trip_documents', // WordPress AJAX action hook
                post_id: postId,
                attachment_ids: attachmentIds.join(',')
            };

            $.ajax({
                type: 'POST',
                url: ajaxurl, // WordPress AJAX URL variable
                data: data,
                success: function(response) {
                    console.log('Post meta updated successfully.');
                    window.location.reload();
                }
            });
        });

        customUploader.open();
    });
});


jQuery(document).ready(function($) {
    $(".view-button").on("click", function(event) {
        event.preventDefault();
        var pdfUrl = $(this).attr("href");

        // Embed the PDF directly in the .pdf-container div
        $(".pdf-container").html('<embed src="' + pdfUrl + '" type="application/pdf" width="100%" height="600px" />');

        // Show the modal
        $(".modal").css("display", "block");
    });

    let stopVisible = false;

    $(".stops").on("click", function(event) {
        if (stopVisible) {
            $(".stops-div").css("display", "none");
        } else {
            $(".stops-div").css("display", "block");
        }

        stopVisible = !stopVisible;
    });

    let activitiesVisible = false;

    $(".activities").on("click", function(event) {
        if (activitiesVisible) {
            $(".activities-div").css("display", "none");
        } else {
            $(".activities-div").css("display", "block");
        }

        activitiesVisible = !activitiesVisible;
    });

    // Close the modal when the close button (×) is clicked
    $(".close").on("click", function() {
        $(".modal").css("display", "none");
        // Clear the .pdf-container content when closing the modal
        $(".pdf-container").html('');
    });

     $(".add-dest").on("click", function(event) {
        $(".add-dest-modal").css("display", "block");
        document.querySelector('#destination_category option[value="-1"]').value = '';
    });

    $(".closes").on("click", function() {
        $(".add-dest-modal").css("display", "none");
    });

    $('#destination-form').submit(function(e) {
    e.preventDefault();

    var formData = $(this).serialize();
     
     $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'add_destination',
                form_data: formData
            },
            success: function(response) {
                $('#destination-form')[0].reset();    
                update_dest(response.data.destination_category, response.data.post_id);
                update_dest_act(response.data.destination_category);
                $(".add-dest-modal").css("display", "none");
                 $('#dest_address_id').val(response.data.post_id);
                 $('#ending_address_lat').val(response.data.destination_lat);
                 $('#ending_address_lang').val(response.data.destination_lang);
                 $('#trip_ending_address').val(response.data.address);
            }
        });
    });

    let mapVisible = false;

    $(".display-map").on("click", function(event) {
        if (mapVisible) {
            $(".map-container").css("display", "none");
        } else {
            $(".map-container").css("display", "flex");
        }

        mapVisible = !mapVisible;
    });

      $('.delete-button').on('click', function (e) {
        e.preventDefault();

        var post_id = $(this).data('post-id');
        var day = $(this).data('day');
       
        $('#deleteConfirmationModal').show();

        $('#cancelButton').on('click', function () {
            $('#deleteConfirmationModal').hide();
        });

        $('#deleteButton').on('click', function () {
            window.location.href = 'admin.php?page=trip-day&tab=edit_day&trip=' + post_id + '&day_delete=' + day;
        });
    });
});

jQuery(document).ready(function($) {
    // Listen for changes in the customer selection
    $('#trip_customer_id').on('change', function() {
        var customerId = $(this).val();

        $.ajax({
            url: ajaxurl, 
            type: 'POST',
            data: {
                action: 'get_customer_data',
                customer_id: customerId
            },
            success: function(response) {
                // Parse the JSON response from the server
                var customerData = $.parseJSON(response);

                // Prefill the input fields with customer data
                $('#trip-customer-first-name').val(customerData.first_name);
                $('#trip-customer-last-name').val(customerData.last_name);
                $('#trip-email').val(customerData.email);
                $('#trip-phone').val(customerData.phone);
                $('#trip-password').val(customerData.password);
            }
        });
        if(customerId == 0){
            $(".customer-div").css("display", "block");
        }else{
            $(".customer-div").css("display", "none");
        }
    });


    $('#destination_categorys').on('change', function() {
        var destination_cat = $(this).val();

        update_dest(destination_cat);
    });
    $('#destination_category_activity').on('change', function() {
        var destination_cat_act = $(this).val();

        update_dest_act(destination_cat_act);
    });
    

    $('.add-ending-dests').on('change', function() {
        var destination_ids = $(this).val();
        var selectedOption = $("#destination option:selected");
        var dataAddress = selectedOption.data("address");
        var dataLat = selectedOption.data("lat");
        var dataLng = selectedOption.data("lng");

        jQuery('#trip_ending_address').val(dataAddress);
        jQuery('#dest_address_id').val(destination_ids);
        jQuery('#ending_address_lat').val(dataLat);
        jQuery('#ending_address_lang').val(dataLng);

    });
});

function update_dest(destination_cat, post_id = null) {

            $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'change_destinations',
                destination_cat: destination_cat,
                post_id: post_id
            },
            success: function(response) {
                  $('#destination').html(response);
                  if (post_id) {
                    var optionToSelect = document.querySelector('#destination_categorys option[value="'+destination_cat+'"]');
                    if (optionToSelect) {
                        optionToSelect.selected = true;
                    }
                  }
            }
        });
}
function update_dest_act(destination_cat_act) {

            $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'change_destinations',
                destination_cat: destination_cat_act
            },
            success: function(response) {
                  $('#destination_act').html(response);
            }
        });
}

// function initMap() {
//   const map = new google.maps.Map(document.getElementById("map"), {
//     zoom: 4,
//     center: { lat: 37.0902, lng: -95.7129 }, // USA.
//   });
//   const directionsService = new google.maps.DirectionsService();
//   const directionsRenderer = new google.maps.DirectionsRenderer({
//     draggable: true,
//     map,
//     panel: document.getElementById("panel"),
//   });

//   directionsRenderer.addListener("directions_changed", () => {
//     const directions = directionsRenderer.getDirections();

//     if (directions) {
//       computeTotalDistance(directions);
//     }
//   });
//     var startingAddress = document.getElementById("trip_starting_address").value;
//     var endingAddress = document.getElementById("trip_ending_address").value;
//     const selectElement = Object.values(selectedDestinations);

//     // Format the array in the desired structure
//     const waypoints = selectElement.map((destination, index) => {
//         return {
//             location: destination.address
//         };
//     });

//     displayRoute(
//       startingAddress,
//       endingAddress,
//       waypoints,
//       directionsService,
//       directionsRenderer
//     );
//     }

function displayRoute(origin, destination, waypoints, service, display) {
    waypoints = waypoints.filter((waypoint) => waypoint && waypoint.location);
    if (waypoints.length === 0) {
      service
        .route({
          origin: origin,
          destination: destination,
          travelMode: google.maps.TravelMode.DRIVING,
          avoidTolls: true,
        })
        .then((result) => {
          display.setDirections(result);
        })
        .catch((e) => {
          alert("Could not display directions due to: " + e);
        });
  }else{
  service
    .route({
      origin: origin,
      destination: destination,
      waypoints: waypoints,
      travelMode: google.maps.TravelMode.DRIVING,
      avoidTolls: true,
    })
    .then((result) => {
      display.setDirections(result);
    })
    .catch((e) => {
      alert("Could not display directions due to: " + e);
    });
  }
}

// function computeTotalDistance(result) {
//   let total = 0;
//   const myroute = result.routes[0];

//   if (!myroute) {
//     return;
//   }

//   for (let i = 0; i < myroute.legs.length; i++) {
//     total += myroute.legs[i].distance.value;
//   }

//   total = total / 1000;
//   document.getElementById("total").innerHTML = total + " km";
//   // const direction = result.routes[0].legs;
//   const direction = result.routes[0].legs;
//   // direction_data(direction);
// }

function direction_data(data) {
  const response = JSON.stringify(data);
  var postId = $('#post_id').val();
  var day = $('#day_count').val();
  var ajaxData = {
    'action': 'update_route_data',
    'postId': postId,
    'day': day,
    'data'  : response,
  }
  jQuery.post( ajax_object.ajaxurl, ajaxData, function(response){ 
    if(response){
      console.log('updated successfully');
    }else{  
    console.log('failed to update');  
  }
  });
}
// window.initMap = initMap;

$(document).ready(function() {
    // Populate selected destinations from the database
if (selectedDestinations.length !== 0) {
    var prev_destination = document.getElementById("trip_starting_address").value;
    $("#selected-options-container").append("<h4>Selected Stops:</h4>");

    Object.keys(selectedDestinations).forEach(function(destinationId, index) {
        var destination = selectedDestinations[destinationId];
        var destination_id = destination.destination_id; // simplified access
        var timeComponents = destination.time.split(":");
        var hour = parseInt(timeComponents[0]);
        var minute = parseInt(timeComponents[1]);


        if (destination && destination.address && destination_id) {
            // Update prev_destination before processing the next destination
            var listItem = $("<li>").addClass("selected-option").attr("value", destination_id).append("<div class='selected-options'>" + destination.address + "<span><a class='button edit-option' onclick=\"open_map('" + destination_id + "','" + prev_destination + "','" + destination.address + "')\">Edit Route</a> <button class='button remove-option' data-value='" + destination_id + "'>Remove</button></span></div><div id='container' class='map-containers'><div class='map' id='map" + destination_id + "'></div><div id='sidebar'><div id='panel" + destination_id + "'></div></div></div><div class='estimate-time'><input type='number' class='small-width' name='travel_time_hour_" + destination_id + "' value='" + hour + "' placeholder='Hours'><input type='number' class='small-width' placeholder='Minutes' name='travel_time_minute_" + destination_id + "' value='" + minute + "'></div>");
            
            // var listItems = $("<li>").addClass("maps").append("<div id='container' class='map-containers'><div class='map' id='map" + destination_id + "'></div><div id='sidebar'><div id='panel" + destination_id + "'></div></div></div><input type='number' class='small-width' name='travel_time_hour' placeholder='Hours'><input type='number' class='small-width' id='travel_time_minute'  placeholder='Minutes' name='travel_time_minute'>");

            $("#selected-options-container").append(listItem);

            // Disable the selected option in the dropdown
            $("#destination option[value='" + destination_id + "']").prop("disabled", true);

            // Update prev_destination for the next iteration
            prev_destination = destination.address;
        }
    });
}


    // Handle destination selection
$("#destination").on("change", function () {
    var selectedOptions = $("#destination option:selected");

    selectedOptions.each(function () {
        var optionValue = $(this).val();
        var destination = {
            address: $(this).data("address"),
            lat: $(this).data("lat"),
            lng: $(this).data("lng")
        };

        // Check if the option is already in the selected list
        if ($("#selected-options-container li[value='" + optionValue + "']").length === 0) {
            if ($("#selected-options-container li").length < 3) {
              
                var listItem = $("<li>").addClass("selected-option").attr("value", optionValue).append("<div class='selected-options'>" + destination.address + "<span><a class='button edit-option' onclick=\"open_map('" + optionValue + "','" + getLastDestinationAddress() + "','" + destination.address + "')\">Edit Route</a> <button class='button remove-option' data-value='" + optionValue + "'>Remove</button></span></div><div id='container' class='map-containers'><div class='map' id='map" + optionValue + "'></div><div id='sidebar'><div id='panel" + optionValue + "'></div></div></div><div class='estimate-time'><input type='number' class='small-width' name='travel_time_hour_" + optionValue + "' placeholder='Hours' value='0'><input type='number' class='small-width' placeholder='Minutes' name='travel_time_minute_" + optionValue + "' value='0'></div>");    
                $("#selected-options-container").append(listItem);
                // Disable the selected option in the dropdown
                $("#destination option[value='" + optionValue + "']").prop("disabled", true);
            }
        }
    });
});
    
function getLastDestinationAddress() {
    var lastDestination = $("#selected-options-container li.selected-option:last");
    var selectedOptions = lastDestination.find(".selected-options").clone();
    selectedOptions.find("span").remove();
    if (selectedOptions.text().trim()) {
         return selectedOptions.text().trim();
    }else{
        return document.getElementById("trip_starting_address").value;
    }
}

    // Handle remove button click event for destinations
    $(document).on("click", ".remove-option", function() {
        var optionValue = $(this).data("value");
        $(this).closest("li.selected-option").remove();
        $("#destination option[value='" + optionValue + "']").prop("disabled", false);
        var currentSelections = $("#destination").val() || [];
        var newSelections = currentSelections.filter(function(value) {
            return value !== optionValue;
        });
        $("#destination").val(newSelections).trigger('change.select2');
    });


// Handle activity selection
if (selectedActivities.length !== 0) {
    var endLocation = document.getElementById("trip_ending_address").value;
    // $("#selected-act-options-container").append("<h4>Selected Stops:</h4>");

    Object.keys(selectedActivities).forEach(function(destinationId, index) {
        var destination = selectedActivities[destinationId];
        var destination_id = destination.destination_id; // simplified access
        var timeComponents = destination.time.split(":");
        var hours = parseInt(timeComponents[0]);
        var minutes = parseInt(timeComponents[1]);

        if (destination && destination.address && destination_id) {
            // Update prev_destination before processing the next destination
            var listItem = $("<li>").addClass("selected-option").attr("value", destination_id).append("<div class='selected-options'>" + destination.address + "<span><a class='button edit-option' onclick=\"open_activity_map('" + destination_id + "','" + endLocation + "','" + destination.address + "')\">Edit Route</a> <button class='button remove-options' data-value='" + destination_id + "'>Remove</button></span></div><div id='container' class='map-containers'><div class='map' id='act_map" + destination_id + "'></div><div id='sidebar'><div id='act_panel" + destination_id + "'></div></div></div><div class='estimate-time'><input type='number' class='small-width' name='act_travel_time_hour_" + destination_id + "' placeholder='Hours' value='" + hours + "'><input type='number' class='small-width' placeholder='Minutes' name='act_travel_time_minute_" + destination_id + "' value='" + minutes + "'></div>");
            
            $("#selected-act-options-container").append(listItem);

            // Disable the selected option in the dropdown
            $("#destination_act option[value='" + destination_id + "']").prop("disabled", true);

        }
    });
}

$("#destination_act").on("change", function () {
    var selectedOptions = $("#destination_act option:selected");
    var endLocation = document.getElementById("trip_ending_address").value;

    selectedOptions.each(function () {
        var optionValue = $(this).val();
        var destination = {
            address: $(this).data("address"),
            lat: $(this).data("lat"),
            lng: $(this).data("lng")
        };

        // Check if the option is already in the selected list
        if ($("#selected-act-options-container li[value='" + optionValue + "']").length === 0) {
            if ($("#selected-act-options-container li").length < 3) {
              
                var listItem = $("<li>").addClass("selected-option").attr("value", optionValue).append("<div class='selected-options'>" + destination.address + "<span><a class='button edit-option' onclick=\"open_activity_map('" + optionValue + "','" + endLocation + "','" + destination.address + "')\">Edit Route</a> <button class='button remove-options' data-value='" + optionValue + "'>Remove</button></span></div><div id='container' class='map-containers'><div class='map' id='act_map" + optionValue + "'></div><div id='sidebar'><div id='act_panel" + optionValue + "'></div></div></div><div class='estimate-time'><input type='number' class='small-width' name='act_travel_time_hour_" + optionValue + "' placeholder='Hours' value='0'><input type='number' class='small-width' placeholder='Minutes' name='act_travel_time_minute_" + optionValue + "' value='0'></div>");    
                $("#selected-act-options-container").append(listItem);
                // Disable the selected option in the dropdown
                $("#destination_act option[value='" + optionValue + "']").prop("disabled", true);
            }
        }
    });
});

// Handle remove button click event for activities
$(document).on("click", ".remove-options", function() {
    var optionValue = $(this).data("value");
    $(this).closest("li.selected-option").remove();
    $("#destination_act option[value='" + optionValue + "']").prop("disabled", false);
    var currentSelections = $("#destination_act").val() || [];
    var newSelections = currentSelections.filter(function(value) {
        return value !== optionValue;
    });
    $("#destination_act").val(newSelections).trigger('change.select2');
});

jQuery(document).ready(function($) {
    // Initialize Select2 for destinations
    $('.select2-destinations').select2({
        placeholder: "Search and select destinations",
        allowClear: true,
        width: '100%'
    });
    
    // Initialize Select2 for activities
    $('#destination_act').select2({
        placeholder: "Search and select activities",
        allowClear: true,
        width: '100%'
    });
    
    // Handle category filtering for destinations
    $('#destination_categorys').on('change', function() {
        var categoryId = $(this).val();
        
        if (categoryId) {
            // AJAX call to get destinations filtered by category
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'filter_destinations_by_category',
                    category: categoryId,
                    security: $('#filter_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        var destinations = response.data;
                        var $select = $('#destination');
                        
                        // Save current selections
                        var selectedValues = $select.val() || [];
                        
                        // Clear and rebuild options
                        $select.empty();
                        
                        $.each(destinations, function(index, dest) {
                            var option = new Option(dest.title, dest.id, false, $.inArray(dest.id, selectedValues) !== -1);
                            $(option).data('address', dest.address);
                            $(option).data('lat', dest.lat);
                            $(option).data('lng', dest.lng);
                            $select.append(option);
                        });
                        
                        $select.trigger('change');
                    }
                }
            });
        }
    });
    

    $('#destination').on('select2:unselect', function(e) {
        var unselectedId = e.params.data.id;
        $("#selected-options-container li.selected-option[value='" + unselectedId + "']").remove();
        $("#destination option[value='" + unselectedId + "']").prop("disabled", false);
        e.params.originalEvent.stopPropagation();
    });

    // Similarly for activities
    $('#destination_act').on('select2:unselect', function(e) {
        var unselectedId = e.params.data.id;
        $("#selected-act-options-container li.selected-option[value='" + unselectedId + "']").remove();
        $("#destination_act option[value='" + unselectedId + "']").prop("disabled", false);
        e.params.originalEvent.stopPropagation();
    });
});


    // Update the selected values input before form submission
    $(".preview").on("click", function() {
        var selectedValuesArray = [];
        $("#selected-options-container li").each(function() {
            var value = $(this).attr("value");
            selectedValuesArray.push(value);
        });
        $("#selected-values-input").val(selectedValuesArray);
        var selectedActArray = [];
        $("#selected-act-options-container li").each(function() {
            var value = $(this).attr("value");
            selectedActArray.push(value);
        });
        $("#selected-values-activity").val(selectedActArray);
    });
});


function open_map(id, startingAddress, endingAddress){

    var mapContainers = $(document.getElementById("map" + id)).closest(".map-containers");
    var estimateTimeDiv = mapContainers.next(".estimate-time");

    if (mapContainers.css("display") === "flex") {
        mapContainers.css("display", "none");
        estimateTimeDiv.css("display", "none");
    } else {
        // Hide all other .map-containers
        $(".map-containers").css("display", "none");
        $(".estimate-time").hide();
        // Show the selected .map-containers
        mapContainers.css("display", "flex");
        estimateTimeDiv.css("display", "block");
    }

  const map = new google.maps.Map(document.getElementById("map"+id), {
    zoom: 4,
    center: { lat: 37.0902, lng: -95.7129 }, // USA.
  });
  const directionsService = new google.maps.DirectionsService();
  const directionsRenderer = new google.maps.DirectionsRenderer({
    draggable: true,
    map,
    panel: document.getElementById("panel"+id),
  });

  directionsRenderer.addListener("directions_changed", () => {
    const directions = directionsRenderer.getDirections();

    const map_direction = directions.routes[0].legs;
    direction_data(id, map_direction);
  });

  var waypoints = [];

    displayRoute(
      startingAddress,
      endingAddress,
      waypoints,
      directionsService,
      directionsRenderer
    );

}


function open_activity_map(id, startingAddress, endingAddress){

    var mapContainers = $(document.getElementById("act_map" + id)).closest(".map-containers");
    var estimateTimeDiv = mapContainers.next(".estimate-time");

    if (mapContainers.css("display") === "flex") {
        mapContainers.css("display", "none");
        estimateTimeDiv.css("display", "none");
    } else {
        // Hide all other .map-containers
        $(".map-containers").css("display", "none");
        $(".estimate-time").hide();
        // Show the selected .map-containers
        mapContainers.css("display", "flex");
        estimateTimeDiv.css("display", "block");
    }

  const map = new google.maps.Map(document.getElementById("act_map"+id), {
    zoom: 4,
    center: { lat: 37.0902, lng: -95.7129 }, // USA.
  });
  const directionsService = new google.maps.DirectionsService();
  const directionsRenderer = new google.maps.DirectionsRenderer({
    draggable: true,
    map,
    panel: document.getElementById("act_panel"+id),
  });

  directionsRenderer.addListener("directions_changed", () => {
    const directions = directionsRenderer.getDirections();

    const map_direction = directions.routes[0].legs;
    // direction_data(id, map_direction);
  });

  var waypoints = [];

    displayRoute(
      startingAddress,
      endingAddress,
      waypoints,
      directionsService,
      directionsRenderer
    );

}
function toggleDestinationFields() {
    var finalDayCheckbox = document.getElementById("final_day_checkbox");
    var destinationSection = document.getElementById("destination_section");
    var categoryDropdown = document.getElementById("destination_categorys");
    var destinationDropdown = document.getElementById("destination");
    var addDestButton = document.querySelector(".add-dest");

    if (finalDayCheckbox.checked) {
        destinationSection.classList.add("disabled-section");
        categoryDropdown.disabled = true;
        destinationDropdown.disabled = true;
        addDestButton.classList.add("disabled-element");
    } else {
        destinationSection.classList.remove("disabled-section");
        categoryDropdown.disabled = false;
        destinationDropdown.disabled = false;
        addDestButton.classList.remove("disabled-element");
    }
}