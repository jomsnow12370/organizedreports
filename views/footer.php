    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
    <script>
function selectMunicipality() {
    var selectedMunicipality = document.getElementById("municipality") ?
        document.getElementById("municipality").value :
        "";
    var selectedBarangay = document.getElementById("barangay") ?
        document.getElementById("barangay").value :
        "";

    if (selectedMunicipality === "" && selectedBarangay === "") {
        window.location.href = window.location.pathname;
        return;
    }

    var urlParams = [];

    if (selectedMunicipality !== "") {
        urlParams.push("mun=" + encodeURIComponent(selectedMunicipality));
    }

    if (selectedBarangay !== "") {
        urlParams.push("brgy=" + encodeURIComponent(selectedBarangay));
    }

    var newUrl =
        urlParams.length > 0 ?
        window.location.pathname + "?" + urlParams.join("&") :
        window.location.pathname;
    window.location.href = newUrl;
}

function loadBarangays() {
    var municipality = $("#municipality").val();
    if (municipality === "") {
        $("#barangay").html('<option value="">Select Barangay</option>');
        return;
    }
    $.ajax({
        url: "models/get_barangays.php",
        method: "POST",
        data: {
            municipality: municipality
        },
        dataType: "json",
        success: function(response) {
            console.log(response); // Debugging line to check the response
            if (response.length > 0) {
                var barangayOptions = '<option value="">Select Barangay</option>';
                $.each(response, function(index, barangay) {
                    barangayOptions +=
                        `<option value="${barangay.id}">${barangay.barangay}</option>`;
                });
                $("#barangay").html(barangayOptions);
            } else {
                $("#barangay").html('<option value="">No Barangays Found</option>');
            }
        },
        error: function() {
            alert("Error loading barangays. Please try again.");
        },
    });
}

document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== "undefined" && typeof $.fn.DataTable !== "undefined") {
        $("#turnoutTable").DataTable({
            paging: true,
            ordering: true,
            info: true,
            searching: true,
            pageLength: 10,
            order: [
                [5, "desc"]
            ], // Sort by completion column by default
        });
    }
});
    </script>