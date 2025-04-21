<?php
// This file identifies geographic weakness hotspots for candidates based on survey data

// Only process if we have municipality data
if (isset($municipality_summary) || isset($barangay_summary)) {

    // Function to identify hotspots based on survey data
    function identifyHotspots($data, $threshold = 40) {
        $hotspots = [];
        
        foreach ($data as $location => $info) {
            // Skip grand total
            if ($location === 'GRAND_TOTAL') continue;
            
            // Get location name
            $location_name = isset($info['barangay']) ? $info['barangay'] : $location;
            
            // Calculate various factors - household warding percentage is a key indicator
            $household_percentage = $info['household_percentage'] ?? 0;
            
            // If warding percentage is below threshold, mark as hotspot
            if ($household_percentage < $threshold) {
                $hotspots[$location] = [
                    'name' => $location_name,
                    'household_percentage' => $household_percentage,
                    'total_households' => $info['total_households'] ?? 0,
                    'surveyed_households' => $info['surveyed_households'] ?? 0,
                    'warded_percentage' => $info['warded_percentage'] ?? 0,
                    'priority' => $household_percentage < 25 ? 'Critical' : 'High'
                ];
            }
        }
        
        // Sort hotspots by household percentage (lowest first)
        uasort($hotspots, function($a, $b) {
            return $a['household_percentage'] <=> $b['household_percentage'];
        });
        
        return $hotspots;
    }
    
    // Identify hotspots
    $hotspots = [];
    
    if (isset($municipality_summary) && !isset($_GET["mun"])) {
        // Provincial view - identify municipality hotspots
        $hotspots = identifyHotspots($municipality_summary);
        $level = "Municipality";
    } elseif (isset($barangay_summary) && isset($_GET["mun"])) {
        // Municipality view - identify barangay hotspots
        $hotspots = identifyHotspots($barangay_summary);
        $level = "Barangay";
    }
    
    // Get candidate-specific weakness data if available
    // This would require custom SQL queries that we'll prepare but not execute here as placeholders
    
    $candidateHotspotQueries = [
        'congressman' => [
            'title' => 'Congressman Laynes - Weak Areas',
            'query' => "SELECT barangays.id, barangays.barangay, barangays.municipality, 
                      COUNT(DISTINCT head_household.fh_v_id) as total_households,
                      SUM(CASE WHEN politics.congressman = 660 THEN 1 ELSE 0 END) as laynes_supporters,
                      SUM(CASE WHEN politics.congressman IN (661, 678) THEN 1 ELSE 0 END) as opposition_supporters,
                      SUM(CASE WHEN politics.congressman = 679 OR politics.congressman IS NULL THEN 1 ELSE 0 END) as undecided
                      FROM head_household
                      INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                      INNER JOIN barangays ON barangays.id = v_info.barangayId
                      LEFT JOIN politics ON politics.v_id = v_info.v_id
                      WHERE record_type = 1
                      GROUP BY barangays.id
                      HAVING (laynes_supporters / total_households * 100) < 40
                      ORDER BY (laynes_supporters / total_households * 100) ASC"
        ],
        'governor' => [
            'title' => 'Governor BossTe - Weak Areas',
            'query' => "SELECT barangays.id, barangays.barangay, barangays.municipality, 
                      COUNT(DISTINCT head_household.fh_v_id) as total_households,
                      SUM(CASE WHEN politics.governor = 662 THEN 1 ELSE 0 END) as bosste_supporters,
                      SUM(CASE WHEN politics.governor = 663 THEN 1 ELSE 0 END) as opposition_supporters,
                      SUM(CASE WHEN politics.governor = 680 OR politics.governor IS NULL THEN 1 ELSE 0 END) as undecided
                      FROM head_household
                      INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                      INNER JOIN barangays ON barangays.id = v_info.barangayId
                      LEFT JOIN politics ON politics.v_id = v_info.v_id
                      WHERE record_type = 1
                      GROUP BY barangays.id
                      HAVING (bosste_supporters / total_households * 100) < 40
                      ORDER BY (bosste_supporters / total_households * 100) ASC"
        ],
        'vicegov' => [
            'title' => 'Vice Governor Fernandez - Weak Areas',
            'query' => "SELECT barangays.id, barangays.barangay, barangays.municipality, 
                      COUNT(DISTINCT head_household.fh_v_id) as total_households,
                      SUM(CASE WHEN politics.vicegov = 676 THEN 1 ELSE 0 END) as fernandez_supporters,
                      SUM(CASE WHEN politics.vicegov = 677 THEN 1 ELSE 0 END) as opposition_supporters,
                      SUM(CASE WHEN politics.vicegov = 681 OR politics.vicegov IS NULL THEN 1 ELSE 0 END) as undecided
                      FROM head_household
                      INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                      INNER JOIN barangays ON barangays.id = v_info.barangayId
                      LEFT JOIN politics ON politics.v_id = v_info.v_id
                      WHERE record_type = 1
                      GROUP BY barangays.id
                      HAVING (fernandez_supporters / total_households * 100) < 40
                      ORDER BY (fernandez_supporters / total_households * 100) ASC"
        ],
        'mayor' => [
            'title' => 'Mayor Boboy - Weak Areas',
            'query' => "SELECT barangays.id, barangays.barangay, barangays.municipality, 
                      COUNT(DISTINCT head_household.fh_v_id) as total_households,
                      SUM(CASE WHEN politics.mayor = 693 THEN 1 ELSE 0 END) as boboy_supporters,
                      SUM(CASE WHEN politics.mayor IN (694, 695) THEN 1 ELSE 0 END) as opposition_supporters,
                      SUM(CASE WHEN politics.mayor = 696 OR politics.mayor IS NULL THEN 1 ELSE 0 END) as undecided
                      FROM head_household
                      INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                      INNER JOIN barangays ON barangays.id = v_info.barangayId
                      LEFT JOIN politics ON politics.v_id = v_info.v_id
                      WHERE record_type = 1 AND barangays.municipality = 'VIRAC'
                      GROUP BY barangays.id
                      HAVING (boboy_supporters / total_households * 100) < 40
                      ORDER BY (boboy_supporters / total_households * 100) ASC"
        ]
    ];
?>

<!-- Candidate Weakness Hotspots -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Geographic Hotspots - Priority Areas</h5>
    </div>
    <div class="card-body">
        <?php if (count($hotspots) > 0): ?>
            <!-- Overall Household Survey Hotspots -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0 fw-bold"><?php echo $level; ?> Hotspots Based on Household Survey Coverage</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">These areas have low household survey completion rates and need immediate attention:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo $level; ?></th>
                                    <th>Priority</th>
                                    <th>Household Survey</th>
                                    <th>Warded %</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hotspots as $id => $hotspot): ?>
                                <tr>
                                    <td class="fw-bold">
                                        <?php if ($level == "Municipality"): ?>
                                            <a href="?mun=<?php echo urlencode($hotspot['name']); ?>" class="text-danger">
                                                <?php echo $hotspot['name']; ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="?mun=<?php echo urlencode($_GET['mun']); ?>&brgy=<?php echo urlencode($id); ?>" class="text-danger">
                                                <?php echo $hotspot['name']; ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $hotspot['priority'] == 'Critical' ? 'danger' : 'warning'; ?>">
                                            <?php echo $hotspot['priority']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                     style="width: <?php echo $hotspot['household_percentage']; ?>%" 
                                                     aria-valuenow="<?php echo $hotspot['household_percentage']; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-muted"><?php echo round($hotspot['household_percentage'], 1); ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-<?php echo $hotspot['warded_percentage'] < 30 ? 'danger' : 'warning'; ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo $hotspot['warded_percentage']; ?>%" 
                                                     aria-valuenow="<?php echo $hotspot['warded_percentage']; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-muted"><?php echo round($hotspot['warded_percentage'], 1); ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-chart-line me-1"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Action Required:</strong> These areas need immediate attention with focused household surveys and voter engagement.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Good news!</strong> No significant hotspots detected in the current view. All areas have survey coverage above threshold.
            </div>
        <?php endif; ?>
        
        <!-- Note about Candidate-Specific Hotspots -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> Candidate-specific hotspot data can be enabled by running the queries in this file.
            The system is prepared with the necessary queries to identify areas where each candidate has weak support.
        </div>
    </div>
</div>

<?php
}
?> 