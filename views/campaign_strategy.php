<?php
// This file contains campaign strategy recommendations based on survey data

// Calculate key metrics
$total_surveyed_households = 0;
$total_surveyed_members = 0;
$total_all_households = 0;
$total_voters = 0;

if (isset($municipality_summary)) {
    foreach ($municipality_summary as $mun => $data) {
        if ($mun !== 'GRAND_TOTAL') {
            $total_surveyed_households += $data['surveyed_households'];
            $total_surveyed_members += $data['surveyed_members'];
            $total_all_households += $data['total_households'];
            $total_voters += $data['total_voters'];
        }
    }
} elseif (isset($barangay_summary)) {
    foreach ($barangay_summary as $brgy => $data) {
        if ($brgy !== 'GRAND_TOTAL') {
            $total_surveyed_households += $data['surveyed_households'];
            $total_surveyed_members += $data['surveyed_members'];
            $total_all_households += $data['total_households'];
            $total_voters += $data['total_voters'];
        }
    }
}

// Calculate overall survey coverage
$household_coverage = $total_all_households > 0 ? ($total_surveyed_households / $total_all_households) * 100 : 0;
$voter_coverage = $total_voters > 0 ? (($total_surveyed_households + $total_surveyed_members) / $total_voters) * 100 : 0;

// Set campaign phase based on coverage
$campaign_phase = '';
if ($household_coverage < 30) {
    $campaign_phase = 'Initial Survey';
} elseif ($household_coverage < 60) {
    $campaign_phase = 'Mid-Campaign';
} else {
    $campaign_phase = 'Final Push';
}

// Define strategies based on campaign phase
$strategies = [
    'Initial Survey' => [
        'priority' => 'Expand survey coverage to reach at least 60% of households',
        'actions' => [
            'Deploy additional survey teams to low-coverage areas',
            'Focus on identifying community leaders and influential households',
            'Establish baseline data for all candidates to inform later targeting',
            'Begin identifying undecided voters for future persuasion'
        ]
    ],
    'Mid-Campaign' => [
        'priority' => 'Target undecided voters and strengthen candidate positions',
        'actions' => [
            'Focus persuasion efforts on undecided voters and soft supporters',
            'Strengthen ground operations in areas where candidates have weak support',
            'Deploy targeted messaging to address specific community concerns',
            'Conduct follow-up surveys to track progress and adjust strategies'
        ]
    ],
    'Final Push' => [
        'priority' => 'Ensure turnout of identified supporters',
        'actions' => [
            'Focus on GOTV (Get Out The Vote) operations in key areas',
            'Mobilize community leaders to ensure turnout of supporters',
            'Continue targeted messaging to persuade remaining undecided voters',
            'Implement election day operations and monitoring'
        ]
    ]
];

// Calculate strength metric for each candidate
function calculateStrengthMetric($support, $opposition, $undecided, $total_voters) {
    if ($total_voters == 0) return 0;
    
    $support_pct = ($support / $total_voters) * 100;
    $opposition_pct = ($opposition / $total_voters) * 100;
    $undecided_pct = ($undecided / $total_voters) * 100;
    
    // Calculate strength score (0-100)
    // Higher if support is high and lead over opposition is substantial
    $lead = $support_pct - $opposition_pct;
    
    if ($lead <= 0) {
        // Trailing - score based on how close we are
        return max(0, 40 + $lead);
    } else if ($lead < 10) {
        // Slight lead - moderate strength
        return 50 + ($lead * 2);
    } else {
        // Substantial lead - strong position
        return min(95, 70 + $lead);
    }
}

// Calculate candidate strength metrics
$candidate_metrics = [
    'Congressman Laynes' => [
        'strength' => calculateStrengthMetric(
            $cong_totals['Laynes']['total'],
            $cong_totals['Rodriguez']['total'] + $cong_totals['Alberto']['total'],
            $cong_totals['UndecidedCong']['total'] + $cong_blanks,
            $total_voters
        ),
        'id' => 'laynes'
    ],
    'Governor BossTe' => [
        'strength' => calculateStrengthMetric(
            $gov_totals['Bosste']['total'],
            $gov_totals['Asanza']['total'],
            $gov_totals['UndecidedGov']['total'] + $gov_blanks,
            $total_voters
        ),
        'id' => 'bosste'
    ],
    'Vice Governor Fernandez' => [
        'strength' => calculateStrengthMetric(
            $vgov_totals['Fernandez']['total'],
            $vgov_totals['Abundo']['total'],
            $vgov_totals['UndecidedVGov']['total'] + $vgov_blanks,
            $total_voters
        ),
        'id' => 'fernandez'
    ]
];

// If viewing Virac, add mayor
if ($mun == "VIRAC" || $mun == "") {
    $candidate_metrics['Mayor Boboy'] = [
        'strength' => calculateStrengthMetric(
            $mayor_totals['BossGov']['total'],
            $mayor_totals['Posoy']['total'] + $mayor_totals['Arcilla']['total'],
            $mayor_totals['UndecidedMayor']['total'] + $mayor_blanks,
            $household_total_virac
        ),
        'id' => 'boboy'
    ];
}

// Sort candidates by strength (lowest first to prioritize weaker positions)
uasort($candidate_metrics, function($a, $b) {
    return $a['strength'] <=> $b['strength'];
});

// Generate candidate-specific strategies
$candidate_strategies = [];
foreach ($candidate_metrics as $name => $data) {
    $strength = $data['strength'];
    $strategy = [];
    
    if ($strength < 40) {
        $strategy['status'] = 'Critical';
        $strategy['color'] = 'danger';
        $strategy['actions'] = [
            'Deploy immediate resources to strengthen position',
            'Address negative perceptions through targeted messaging',
            'Increase candidate visibility in weak areas',
            'Consider defensive strategy to minimize losses'
        ];
    } elseif ($strength < 60) {
        $strategy['status'] = 'Challenged';
        $strategy['color'] = 'warning';
        $strategy['actions'] = [
            'Focus on persuading undecided voters',
            'Strengthen messaging on key policy areas',
            'Increase ground operations in competitive areas',
            'Conduct targeted follow-up surveys to identify movement'
        ];
    } elseif ($strength < 80) {
        $strategy['status'] = 'Competitive';
        $strategy['color'] = 'info';
        $strategy['actions'] = [
            'Solidify support base through reinforcement messaging',
            'Target soft supporters of opponents',
            'Prepare GOTV operations in stronghold areas',
            'Monitor for any negative trends'
        ];
    } else {
        $strategy['status'] = 'Strong';
        $strategy['color'] = 'success';
        $strategy['actions'] = [
            'Focus on turnout operations',
            'Consider allocating resources to help weaker candidates',
            'Maintain visibility but avoid unnecessary risks',
            'Continue monitoring to ensure position remains strong'
        ];
    }
    
    $candidate_strategies[$name] = $strategy;
}
?>

<!-- Campaign Strategy Section -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Campaign Strategy Recommendations</h5>
    </div>
    <div class="card-body">
        <!-- Campaign Phase Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 fw-bold">Current Campaign Phase: <?php echo $campaign_phase; ?></h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Household Survey Coverage</h6>
                                        <span class="text-muted small"><?php echo number_format($total_surveyed_households); ?> of <?php echo number_format($total_all_households); ?> households</span>
                                    </div>
                                    <div class="text-primary fw-bold"><?php echo round($household_coverage, 1); ?>%</div>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?php echo $household_coverage; ?>%" 
                                         aria-valuenow="<?php echo $household_coverage; ?>" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Voter Coverage</h6>
                                        <span class="text-muted small"><?php echo number_format($total_surveyed_households + $total_surveyed_members); ?> of <?php echo number_format($total_voters); ?> voters</span>
                                    </div>
                                    <div class="text-primary fw-bold"><?php echo round($voter_coverage, 1); ?>%</div>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?php echo $voter_coverage; ?>%" 
                                         aria-valuenow="<?php echo $voter_coverage; ?>" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-primary mb-3">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Strategic Priority:</h6>
                            <?php echo $strategies[$campaign_phase]['priority']; ?>
                        </div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-2">Recommended Actions:</h6>
                <ol class="list-group list-group-numbered mb-0">
                    <?php foreach ($strategies[$campaign_phase]['actions'] as $action): ?>
                    <li class="list-group-item"><?php echo $action; ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        
        <!-- Candidate-Specific Strategies -->
        <h6 class="fw-bold text-primary mb-3">Candidate-Specific Strategies</h6>
        <div class="row">
            <?php foreach ($candidate_strategies as $name => $strategy): ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-<?php echo $strategy['color']; ?> text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><?php echo $name; ?></h6>
                            <span class="badge bg-white text-<?php echo $strategy['color']; ?>"><?php echo $strategy['status']; ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">Position Strength</span>
                                <span class="fw-bold"><?php echo round($candidate_metrics[$name]['strength'], 0); ?>/100</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?php echo $strategy['color']; ?>" role="progressbar" 
                                     style="width: <?php echo $candidate_metrics[$name]['strength']; ?>%" 
                                     aria-valuenow="<?php echo $candidate_metrics[$name]['strength']; ?>" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-2">Strategy:</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($strategy['actions'] as $action): ?>
                            <li class="list-group-item px-0 py-1 border-0">
                                <i class="fas fa-angle-right text-<?php echo $strategy['color']; ?> me-2"></i>
                                <?php echo $action; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Timeline -->
        <div class="card mt-3 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">Campaign Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline-wrapper">
                    <div class="timeline-steps">
                        <div class="timeline-step <?php echo $campaign_phase == 'Initial Survey' ? 'active' : ($campaign_phase == 'Mid-Campaign' || $campaign_phase == 'Final Push' ? 'completed' : ''); ?>">
                            <div class="timeline-step-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <p class="mb-0 fw-bold">Initial Survey</p>
                            <p class="mb-0 text-muted small">Survey Coverage: <30%</p>
                        </div>
                        <div class="timeline-step <?php echo $campaign_phase == 'Mid-Campaign' ? 'active' : ($campaign_phase == 'Final Push' ? 'completed' : ''); ?>">
                            <div class="timeline-step-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="mb-0 fw-bold">Mid-Campaign</p>
                            <p class="mb-0 text-muted small">Survey Coverage: 30-60%</p>
                        </div>
                        <div class="timeline-step <?php echo $campaign_phase == 'Final Push' ? 'active' : ''; ?>">
                            <div class="timeline-step-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <p class="mb-0 fw-bold">Final Push</p>
                            <p class="mb-0 text-muted small">Survey Coverage: >60%</p>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-step-icon">
                                <i class="fas fa-vote-yea"></i>
                            </div>
                            <p class="mb-0 fw-bold">Election Day</p>
                            <p class="mb-0 text-muted small">GOTV Operations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-wrapper {
    position: relative;
    padding: 14px 0;
}
.timeline-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
}
.timeline-steps::before {
    content: '';
    position: absolute;
    background: #e5e5e5;
    width: 100%;
    height: 2px;
    top: 20px;
}
.timeline-step {
    text-align: center;
    position: relative;
    z-index: 1;
    padding: 0 20px;
}
.timeline-step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 10px;
}
.timeline-step.active .timeline-step-icon {
    background: #0d6efd;
    color: white;
}
.timeline-step.completed .timeline-step-icon {
    background: #198754;
    color: white;
}
</style> 