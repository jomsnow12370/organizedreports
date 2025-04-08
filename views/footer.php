<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
</script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

<!-- Add Chart.js script at the bottom of the page before </footer> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Age Distribution Chart
    const ageCtx = document.getElementById('ageDistributionChart').getContext('2d');

    const ageChart = new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: <?php echo $age_labels; ?>,
            datasets: [{
                label: 'Number of Voters',
                data: <?php echo $age_counts; ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Non-Warded Voters by Age Group',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' (' + <?php echo $age_percentages; ?>[
                                context.dataIndex] + '%)';
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Voters'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Age Groups'
                    }
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Age Distribution Chart
    const ageCtx = document.getElementById('ageDistributionChartWarded').getContext('2d');

    const ageChart = new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: <?php echo $age_labels; ?>,
            datasets: [{
                label: 'Number of Voters',
                data: <?php echo $age_counts; ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Non-Warded Voters by Age Group',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' (' + <?php echo $age_percentages; ?>[
                                context.dataIndex] + '%)';
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Voters'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Age Groups'
                    }
                }
            }
        }
    });
});
</script>
<script>
// Add this JavaScript code
document.addEventListener('DOMContentLoaded', function() {
    const familyCards = document.querySelectorAll('.family-card');

    familyCards.forEach(card => {
        card.querySelector('.card').addEventListener('click', function() {
            const lastname = this.closest('.family-card').getAttribute('data-lastname');
            const voters = this.closest('.family-card').getAttribute('data-voters');
            const warded = this.closest('.family-card').getAttribute('data-warded');
            const percent = this.closest('.family-card').getAttribute('data-percent');

            // Update modal title with lastname
            document.getElementById('familyDataModalLabel').textContent = lastname +
                ' Family';

            // Make AJAX request to get PHP-generated content
            fetch('models/get_family_details.php?lastname=' + encodeURIComponent(lastname) +
                    '&munquery=' + encodeURIComponent("<?php echo $munquery; ?>") +
                    '&brgyquery=' + encodeURIComponent("<?php echo $brgyquery; ?>"))
                .then(response => response.text())
                .then(data => {
                    document.getElementById('familyModalContent').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching family details:', error);
                    document.getElementById('familyModalContent').innerHTML =
                        'Error loading data';
                });
        });
    });
});
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

<script>
$(document).ready(function() {
    $('#barangaySummaryTable').DataTable({
        "paging": false, // Disable pagination for simplicity
        "info": false, // Hide "Showing X of Y entries"
        "order": [], // Initially no sorting
        "responsive": true,
        "columnDefs": [{
                "orderable": true,
                "targets": 0
            } // Disable sorting on the # column
        ],
    });

    // Add print classes to avoid printing DataTables elements
    $('.dataTables_filter').addClass('removeonprint');
});

$(document).ready(function() {
    $('#barangaySummaryTableforWarded').DataTable({
        "paging": false, // Disable pagination for simplicity
        "info": false, // Hide "Showing X of Y entries"
        "order": [], // Initially no sorting
        "responsive": true,
        "columnDefs": [{
                "orderable": true,
                "targets": 0
            } // Disable sorting on the # column
        ],
    });

    // Add print classes to avoid printing DataTables elements
    $('.dataTables_filter').addClass('removeonprint');
});
</script>