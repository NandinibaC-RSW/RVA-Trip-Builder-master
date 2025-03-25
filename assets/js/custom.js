var input = document.getElementById( 'file-upload' );
var infoArea = document.getElementById( 'file-upload-filename' );

// input.addEventListener( 'change', showFileName );

function showFileName( event ) {
  
  // the change event gives us the input it occurred in 
  var input = event.srcElement;
  
  // the input has an array of files in the `files` property, each one has a name that you can use. We're just using the name here.
  var fileName = input.files[0].name;
  
  // use fileName however fits your app best, i.e. add it into a div
  infoArea.textContent = 'File name: ' + fileName;
}

const accordionContent = document.querySelectorAll(".accordion-content");
accordionContent.forEach((item, index) => {
    let header = item.querySelector("header");
    header.addEventListener("click", () =>{
        item.classList.toggle("open");
        let description = item.querySelector(".description");
        if(item.classList.contains("open")){
            description.style.height = `${description.scrollHeight}px`; //scrollHeight property returns the height of an element including padding , but excluding borders, scrollbar or margin
            item.querySelector("i").classList.replace("fa-angle-down", "fa-angle-up");
        }else{
            description.style.height = "0px";
            item.querySelector("i").classList.replace("fa-angle-up", "fa-angle-down");
        }
        removeOpen(index); //calling the funtion and also passing the index number of the clicked header
    })
})
function removeOpen(index1){
    accordionContent.forEach((item2, index2) => {
        if(index1 != index2){
            item2.classList.remove("open");
            let des = item2.querySelector(".description");
            des.style.height = "0px";
            item2.querySelector("i").classList.replace("fa-angle-up", "fa-angle-down");
        }
    })
}

    document.addEventListener("DOMContentLoaded", function() {
        var arrow = document.querySelector('.icon i');
        var content = document.querySelector('.parent-tab .content');
        arrow.addEventListener('click', function() {
            content.classList.toggle('open');
            var isOpen = content.classList.contains('open');
            if (isOpen) {
                arrow.classList.remove('fa-chevron-down');
                arrow.classList.add('fa-chevron-up');
            } else {
                arrow.classList.remove('fa-chevron-up');
                arrow.classList.add('fa-chevron-down');
            }
        });
    });

     $(document).ready(function () {
           $(".remove_popup_btn").click(function () {
    var destinationId = $(this).data("destination-id");
    var destinationTitle = $(this).closest(".dest_box").find(".dest_box_content h6").text();
    var startLocation = $(this).closest(".dest_box").find("#start_latlng").val();
    var endLocation = $(this).closest(".dest_box").find("#end_latlng").val();
    // Update remove_popup content with current destination details
    $(".remove_popup .remove-popup-content span.destination-title").text(destinationTitle);
    $(".remove_popup input[name='destination_id']").val(destinationId);
    $(".remove_popup input[name='start_latlng']").val(startLocation);
    $(".remove_popup input[name='end_latlng']").val(endLocation);

    $(".remove_popup").show();
    });

    $(".cancel_remove_btn").click(function () {
        $(".remove_popup").hide();
    });


            $("#uploadButton").click(function () {
                $(".download_popup").show();
            });
            var download_modal = document.querySelector('.download_popup');
            var closeButton = download_modal.querySelector('.close')

            document.getElementById('uploadButton').addEventListener('click', function() {
                $(".download_popup").show();
            });
            closeButton.addEventListener('click', function() {
                $(".download_popup").hide();
            });
            window.addEventListener('click', function(event) {
                if (event.target == download_modal) {
                    $(".download_popup").hide();
                }
            }); 

        });

$(document).ready(function(){
    $('.directions_section h6').on('click', function(){
        $(this).toggleClass('active');
        $(this).find('.circle-icon i').toggleClass('fa-chevron-down fa-chevron-up');
        $(this).next('.directions_content').slideToggle();
        $('.directions_content').not($(this).next()).slideUp();
        $('.directions_section h6').not($(this)).removeClass('active').find('.circle-icon i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });


    $(".download_guide_btn").click(function () {
        $("#guide_doc_model").show();
    });

    var download_modal = document.getElementById('guide_doc_model');
    var closeButton = download_modal.querySelector('.close');

    document.getElementById('guide_doc').addEventListener('click', function() {
        $("#guide_doc_model").show();
    });

    closeButton.addEventListener('click', function() {
        $("#guide_doc_model").hide();
    });

    window.addEventListener('click', function(event) {
        if (event.target == download_modal) {
            $("#guide_doc_model").hide();
        }
    });

});
