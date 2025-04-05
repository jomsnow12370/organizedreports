<div class="dashboard-header">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow border-0">
                <div class="card-body bg-dark text-white rounded">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <?php
                                if (isset($_GET["mun"])) {
                                    if (isset($_GET["brgy"])) {
                                        echo $brgyName . ', ' . $mun;
                                    } else {
                                        echo $mun;
                                    }
                                } else {
                                    echo "Household Survey Province-wide";
                                }
                                ?>
                            </h2>
                            <button class="btn btn-sm btn-outline-success mt-2 removeonprint" data-bs-toggle="modal"
                                data-bs-target="#municipalityModal">
                                <i class="fa fa-repeat"></i> Select Address
                            </button>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-sm btn-outline-primary removeonprint" id="printBtn"
                                onclick="window.print()">
                                <i class="fa fa-print"></i> Print Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Row -->
    <div class="row mb-4">
        <div class="col-md-8 removeonprint">
            <h5 class="fw-bold">Search Lastname</h5>
        </div>
        <div class="col-md-4">
            <div class="input-group removeonprint">
                <input type="text" id="search" class="form-control" placeholder="Search by family name...">
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4">
    <!-- Total Families -->
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow border-0 bg-primary text-white">
            <div class="card-body text-center">
                <div class="mb-2 text-uppercase fw-bold">Total Families</div>
                <pre class="text-light"
                    style="font-size: 10px;">WHERE COUNT(*) > 10 | LIMITED TO <?php echo $limit; ?> LASTNAMES</pre>
                <div class="display-6 fw-bold" id="totalFamilies">0</div>
            </div>
        </div>
    </div>

    <!-- Avg. Warding Rate -->
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow border-0 bg-warning text-dark">
            <div class="card-body text-center">
                <div class="mb-2 text-uppercase fw-bold">Avg. Warding Rate</div>
                <div class="display-6 fw-bold" id="avgWardingRate">0%</div>
            </div>
        </div>
    </div>

    <!-- Low Warding Families -->
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow border-0 bg-danger text-white">
            <div class="card-body text-center">
                <div class="mb-2 text-uppercase fw-bold">Low Warding Families</div>
                <div class="display-6 fw-bold" id="lowWardingCount">0</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 removeonprint">
    <div class="btn-group">
        <button class="btn btn-outline-primary active" id="card-view-btn">Card View</button>
        <button class="btn btn-outline-primary" id="table-view-btn">Table View</button>
    </div>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown"
            aria-expanded="false">
            Sort By
        </button>
        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
            <li><a class="dropdown-item sort-option" data-sort="warding-asc" href="#">Warding Rate (Low
                    to
                    High)</a></li>
            <li><a class="dropdown-item sort-option" data-sort="warding-desc" href="#">Warding Rate
                    (High to
                    Low)</a></li>
            <li><a class="dropdown-item sort-option" data-sort="family-asc" href="#">Family Name
                    (A-Z)</a></li>
            <li><a class="dropdown-item sort-option" data-sort="voters-desc" href="#">Total Voters (High
                    to
                    Low)</a></li>
        </ul>
    </div>
</div>

<div id="card-container" class="row">
    <?php
    $r = get_array("SELECT v_lname AS lname, COUNT(*) AS cnt FROM v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 $munquery $brgyquery GROUP BY v_lname HAVING COUNT(*) > 10 ORDER BY COUNT(*) DESC LIMIT $limit");
    
    $totalFamilies = 0;
    $totalWardingRate = 0;
    $lowWardingCount = 0;
        
    foreach ($r as $key => $value) {
        $lname = trim($value["lname"]);
        $mnames = get_value("SELECT COUNT(*) AS cnt FROM v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND v_mname = '$lname' $munquery $brgyquery")[0];
            
        $totalVoters = $value["cnt"] + $mnames;
        
        $household = get_value("SELECT COUNT(*) from head_household INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE (TRIM(v_lname) = '$lname' OR TRIM(v_mname) = '$lname') and record_type = 1 $munquery $brgyquery")[0];
        $members = get_value("SELECT COUNT(*) from household_warding INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE (TRIM(v_lname) = '$lname' OR TRIM(v_mname) = '$lname') and record_type = 1 $munquery $brgyquery")[0];
        
        $wardedTotal = $household + $members;
        $wardingPercent = ($totalVoters > 0) ? round(($wardedTotal / $totalVoters) * 100) : 0;
        
        $cardClass = "";
        if ($wardingPercent < 30) {
            $cardClass = "warding-rate-low";
            $lowWardingCount++;
        } else if ($wardingPercent < 70) {
            $cardClass = "warding-rate-medium";
        } else {
            $cardClass = "warding-rate-high";
        }
        
        $totalFamilies++;
        $totalWardingRate += $wardingPercent;
    ?>
    <div class="col-md-4 family-card mb-2" data-lastname="<?php echo $lname; ?>"
        data-voters="<?php echo $totalVoters; ?>" data-warded="<?php echo $wardedTotal; ?>"
        data-percent="<?php echo $wardingPercent; ?>">
        <div class="card <?php echo $cardClass; ?>" style="cursor: pointer;" data-bs-toggle="modal"
            data-bs-target="#familyDataModal">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title"><strong><?php echo $lname; ?></strong></h5>
                    <span
                        class="badge <?php echo ($wardingPercent < 30) ? 'bg-danger' : (($wardingPercent < 70) ? 'bg-warning text-dark' : 'bg-success'); ?>">
                        <?php echo $wardingPercent; ?>%
                    </span>
                </div>
                <div class="card-text mt-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Total Voters:</span>
                        <strong><?php echo $totalVoters; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Warded Voters:</span>
                        <strong><?php echo $wardedTotal; ?></strong>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar <?php echo ($wardingPercent < 30) ? 'bg-danger' : (($wardingPercent < 70) ? 'bg-warning' : 'bg-success'); ?>"
                            role="progressbar" style="width: <?php echo $wardingPercent; ?>%"
                            aria-valuenow="<?php echo $wardingPercent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div id="table-container" class="row" style="display: none;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="table" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Family Name</th>
                        <th>Total Voters</th>
                        <th>Warded</th>
                        <th>Warding Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <?php
                    $counter = 1;
                    foreach ($r as $key => $value) {
                        $lname = trim($value["lname"]);
                        $mnames = get_value("SELECT COUNT(*) AS cnt FROM v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND v_mname = '$lname' $munquery $brgyquery")[0];
                            
                        $totalVoters = $value["cnt"] + $mnames;
                        
                        $household = get_value("SELECT COUNT(*) from head_household INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE (TRIM(v_lname) = '$lname' OR TRIM(v_mname) = '$lname') and record_type = 1 $munquery $brgyquery")[0];
                        $members = get_value("SELECT COUNT(*) from household_warding INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE (TRIM(v_lname) = '$lname' OR TRIM(v_mname) = '$lname') and record_type = 1 $munquery $brgyquery")[0];
                        
                        $wardedTotal = $household + $members;
                        $wardingPercent = ($totalVoters > 0) ? round(($wardedTotal / $totalVoters) * 100) : 0;
                        
                        $statusClass = "";
                        $statusText = "";
                        if ($wardingPercent < 30) {
                            $statusClass = "text-danger";
                            $statusText = "Low";
                        } else if ($wardingPercent < 70) {
                            $statusClass = "text-warning";
                            $statusText = "Medium";
                        } else {
                            $statusClass = "text-success";
                            $statusText = "High";
                        }
                    ?>
                    <tr class="family-row" data-lastname="<?php echo $lname; ?>"
                        data-voters="<?php echo $totalVoters; ?>" data-warded="<?php echo $wardedTotal; ?>"
                        data-percent="<?php echo $wardingPercent; ?>">
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo $lname; ?></td>
                        <td><?php echo $totalVoters; ?></td>
                        <td><?php echo $wardedTotal; ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 100px;">
                                    <div class="progress-bar <?php echo ($wardingPercent < 30) ? 'bg-danger' : (($wardingPercent < 70) ? 'bg-warning' : 'bg-success'); ?>"
                                        role="progressbar" style="width: <?php echo $wardingPercent; ?>%"
                                        aria-valuenow="<?php echo $wardingPercent; ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <span><?php echo $wardingPercent; ?>%</span>
                            </div>
                        </td>
                        <td><span
                                class="badge <?php echo ($wardingPercent < 30) ? 'bg-danger' : (($wardingPercent < 70) ? 'bg-warning text-dark' : 'bg-success'); ?>"><?php echo $statusText; ?></span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalFamilies = <?php echo $totalFamilies; ?>;
    const avgWardingRate = <?php echo ($totalFamilies > 0) ? round($totalWardingRate / $totalFamilies) : 0; ?>;
    const lowWardingCount = <?php echo $lowWardingCount; ?>;

    // Update statistics display
    document.getElementById('totalFamilies').textContent = totalFamilies;
    document.getElementById('avgWardingRate').textContent = avgWardingRate + '%';
    document.getElementById('lowWardingCount').textContent = lowWardingCount;

    // Card/Table view toggle
    document.getElementById('card-view-btn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('table-view-btn').classList.remove('active');
        document.getElementById('card-container').style.display = 'block';
        document.getElementById('table-container').style.display = 'none';
    });

    document.getElementById('table-view-btn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('card-view-btn').classList.remove('active');
        document.getElementById('table-container').style.display = 'block';
        document.getElementById('card-container').style.display = 'none';
    });

    // Search functionality
    document.getElementById('search').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();

        document.querySelectorAll('.family-card').forEach(card => {
            const lastName = card.dataset.lastname.toLowerCase();
            card.style.display = lastName.includes(value) ? '' : 'none';
        });

        document.querySelectorAll('.family-row').forEach(row => {
            const lastName = row.dataset.lastname.toLowerCase();
            row.style.display = lastName.includes(value) ? '' : 'none';
        });
    });

    // Sorting functionality
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const sortType = this.dataset.sort;

            // Sort cards
            const cardContainer = document.getElementById('card-container');
            const cards = Array.from(document.querySelectorAll('.family-card'));
            cards.sort((a, b) => sortFunction(a, b, sortType));
            cards.forEach(card => cardContainer.appendChild(card));

            // Sort table rows
            const rows = Array.from(document.querySelectorAll('.family-row'));
            rows.sort((a, b) => sortFunction(a, b, sortType));
            const tbody = document.getElementById('tbody');
            tbody.innerHTML = '';
            rows.forEach((row, index) => {
                row.querySelector('td').textContent = index + 1;
                tbody.appendChild(row);
            });

            document.getElementById('sortDropdown').textContent = 'Sort By: ' + this
                .textContent;
        });
    });

    function sortFunction(a, b, type) {
        switch (type) {
            case 'warding-asc':
                return a.dataset.percent - b.dataset.percent;
            case 'warding-desc':
                return b.dataset.percent - a.dataset.percent;
            case 'family-asc':
                return a.dataset.lastname.localeCompare(b.dataset.lastname);
            case 'voters-desc':
                return b.dataset.voters - a.dataset.voters;
            default:
                return 0;
        }
    }

    // Trigger default sort
    const defaultSort = document.querySelector('.sort-option[data-sort="voters-desc"]');
    if (defaultSort) defaultSort.click();
});
</script>