<?php
// This file contains candidate strength/weakness analysis

// Function to calculate strength/weakness metrics for a candidate
function calculateMetrics($support, $totalVoters, $opposition, $undecided) {
    $metrics = [];
    
    // Prevent division by zero
    $totalVoters = max(1, $totalVoters);
    
    // Calculate percentage of support
    $metrics['support_percentage'] = ($support / $totalVoters) * 100;
    
    // Calculate percentage of opposition
    $metrics['opposition_percentage'] = ($opposition / $totalVoters) * 100;
    
    // Calculate percentage of undecided
    $metrics['undecided_percentage'] = ($undecided / $totalVoters) * 100;
    
    // Determine areas of strength (where support is high)
    $metrics['strength_level'] = "";
    if ($metrics['support_percentage'] >= 70) {
        $metrics['strength_level'] = "strong";
    } elseif ($metrics['support_percentage'] >= 50) {
        $metrics['strength_level'] = "moderate";
    } else {
        $metrics['strength_level'] = "weak";
    }
    
    // Calculate support lead over opposition
    $metrics['support_lead'] = $metrics['support_percentage'] - $metrics['opposition_percentage'];
    
    // Calculate potential maximum (if all undecided vote for candidate)
    $metrics['potential_max'] = $metrics['support_percentage'] + $metrics['undecided_percentage'];
    
    // Calculate risk (how close opposition is to overtaking)
    if ($metrics['support_lead'] <= 0) {
        $metrics['risk'] = "critical";
    } elseif ($metrics['support_lead'] < 10) {
        $metrics['risk'] = "high";
    } elseif ($metrics['support_lead'] < 20) {
        $metrics['risk'] = "moderate";
    } else {
        $metrics['risk'] = "low";
    }
    
    return $metrics;
}

// Set up the candidates and their opponents
$candidates = [
    'congressman' => [
        'name' => 'Laynes',
        'support' => $cong_totals['Laynes']['total'],
        'opponents' => [
            ['name' => 'Rodriguez', 'support' => $cong_totals['Rodriguez']['total']],
            ['name' => 'Alberto', 'support' => $cong_totals['Alberto']['total']]
        ],
        'undecided' => $cong_totals['UndecidedCong']['total'] + $cong_blanks
    ],
    'governor' => [
        'name' => 'BossTe',
        'support' => $gov_totals['Bosste']['total'],
        'opponents' => [
            ['name' => 'Asanza', 'support' => $gov_totals['Asanza']['total']]
        ],
        'undecided' => $gov_totals['UndecidedGov']['total'] + $gov_blanks
    ],
    'vice_governor' => [
        'name' => 'Fernandez',
        'support' => $vgov_totals['Fernandez']['total'],
        'opponents' => [
            ['name' => 'Abundo', 'support' => $vgov_totals['Abundo']['total']]
        ],
        'undecided' => $vgov_totals['UndecidedVGov']['total'] + $vgov_blanks
    ]
];

// For Virac Mayor, only include if viewing Virac data
if ($mun == "VIRAC" || $mun == "") {
    $candidates['mayor'] = [
        'name' => 'Boboy',
        'support' => $mayor_totals['BossGov']['total'],
        'opponents' => [
            ['name' => 'Posoy', 'support' => $mayor_totals['Posoy']['total']],
            ['name' => 'Arcilla', 'support' => $mayor_totals['Arcilla']['total']]
        ],
        'undecided' => $mayor_totals['UndecidedMayor']['total'] + $mayor_blanks
    ];
}

// Calculate metrics for each candidate
foreach ($candidates as $position => &$candidate) {
    // Calculate total support for opponents
    $totalOpposition = 0;
    foreach ($candidate['opponents'] as $opponent) {
        $totalOpposition += $opponent['support'];
    }
    
    // Calculate total voters for this position
    $totalVoters = $candidate['support'] + $totalOpposition + $candidate['undecided'];
    
    // Calculate metrics
    $candidate['metrics'] = calculateMetrics(
        $candidate['support'],
        $totalVoters,
        $totalOpposition,
        $candidate['undecided']
    );
    
    // Sort opponents by support (highest first)
    usort($candidate['opponents'], function($a, $b) {
        return $b['support'] - $a['support'];
    });
    
    // Create a new array with properly calculated values to avoid reference issues
    $updatedOpponents = [];
    foreach ($candidate['opponents'] as $key => $opponent) {
        $updatedOpponent = $opponent;
        // Calculate percentage safely
        $updatedOpponent['percentage'] = ($opponent['support'] / $totalVoters) * 100;
        // Calculate and store the difference
        $updatedOpponent['difference'] = $candidate['metrics']['support_percentage'] - $updatedOpponent['percentage'];
        $updatedOpponents[] = $updatedOpponent;
    }
    
    // Replace the opponents array with our updated one
    $candidate['opponents'] = $updatedOpponents;
}
?>

<!-- Candidate Strength/Weakness Analysis -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Candidate Strength & Weakness Analysis</h5>
    </div>
    <div class="card-body">
        <?php foreach ($candidates as $position => $candidate): ?>
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <?php echo ucfirst(str_replace('_', ' ', $position)); ?>: 
                        <span class="text-primary"><?php echo $candidate['name']; ?></span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Strength Cards -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-left-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Strengths</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <?php if ($candidate['metrics']['support_percentage'] > 50): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Majority support at <strong><?php echo number_format($candidate['metrics']['support_percentage'], 1); ?>%</strong>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if ($candidate['metrics']['support_lead'] > 0): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-chart-line text-success me-2"></i>
                                                Leading by <strong><?php echo number_format($candidate['metrics']['support_lead'], 1); ?>%</strong> against opposition
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if ($candidate['metrics']['potential_max'] > 70): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-arrow-up text-success me-2"></i>
                                                Potential to reach <strong><?php echo number_format($candidate['metrics']['potential_max'], 1); ?>%</strong> with undecided voters
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php foreach ($candidate['opponents'] as $opponent): ?>
                                            <?php if (isset($opponent['difference']) && $opponent['difference'] > 20): ?>
                                                <li class="list-group-item">
                                                    <i class="fas fa-trophy text-success me-2"></i>
                                                    Strong lead over <?php echo $opponent['name']; ?> by <strong><?php echo number_format($opponent['difference'], 1); ?>%</strong>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Weakness Cards -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-left-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Areas of Concern</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <?php if ($candidate['metrics']['support_percentage'] < 50): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                Support below majority at <strong><?php echo number_format($candidate['metrics']['support_percentage'], 1); ?>%</strong>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if ($candidate['metrics']['undecided_percentage'] > 15): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-question-circle text-danger me-2"></i>
                                                High undecided rate at <strong><?php echo number_format($candidate['metrics']['undecided_percentage'], 1); ?>%</strong>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php foreach ($candidate['opponents'] as $opponent): ?>
                                            <?php if (isset($opponent['difference']) && $opponent['difference'] < 10 && $opponent['difference'] > -10): ?>
                                                <li class="list-group-item">
                                                    <i class="fas fa-balance-scale text-danger me-2"></i>
                                                    Close race with <?php echo $opponent['name']; ?> 
                                                    (<?php echo $opponent['difference'] > 0 ? "+" : ""; ?><?php echo number_format($opponent['difference'], 1); ?>%)
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($opponent['difference']) && $opponent['difference'] <= -10): ?>
                                                <li class="list-group-item">
                                                    <i class="fas fa-arrow-down text-danger me-2"></i>
                                                    Trailing behind <?php echo $opponent['name']; ?> by <strong><?php echo number_format(abs($opponent['difference']), 1); ?>%</strong>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        
                                        <?php if ($candidate['metrics']['risk'] == "high" || $candidate['metrics']['risk'] == "critical"): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-fire text-danger me-2"></i>
                                                <strong><?php echo ucfirst($candidate['metrics']['risk']); ?> risk level</strong> - needs immediate attention
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommendations -->
                    <div class="mt-3">
                        <h6 class="fw-bold text-primary">Strategic Recommendations:</h6>
                        <ul class="list-group mb-3">
                            <?php if ($candidate['metrics']['undecided_percentage'] > 10): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    Target undecided voters (<?php echo number_format($candidate['metrics']['undecided_percentage'], 1); ?>%) with focused messaging
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($candidate['metrics']['support_percentage'] < 45): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-users text-warning me-2"></i>
                                    Increase grassroots campaigning to boost base support
                                </li>
                            <?php endif; ?>
                            
                            <?php foreach ($candidate['opponents'] as $opponent): ?>
                                <?php if (isset($opponent['difference']) && $opponent['difference'] < 15 && $opponent['difference'] > -15): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-shield-alt text-warning me-2"></i>
                                        Strengthen differentiation against <?php echo $opponent['name']; ?> in key policy areas
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            
                            <?php if ($candidate['metrics']['support_percentage'] > 60): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-bullhorn text-warning me-2"></i>
                                    Focus on voter turnout rather than persuasion
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div> 