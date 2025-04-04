<!-- Voters Modal -->
<div class="modal fade" id="votersModal" tabindex="-1" aria-labelledby="votersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="votersModalLabel">Voters Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Total Voters: <?php echo number_format(0); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Family Data Modal -->
<div class="modal fade" id="familyDataModal" tabindex="-1" aria-labelledby="familyDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="familyDataModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="familyModalContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Leaders Modal -->
<div class="modal fade" id="leadersModal" tabindex="-1" aria-labelledby="leadersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadersModalLabel">Leaders Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Total Leaders: <?php echo number_format(0); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Municipality Modal -->
<div class="modal fade" id="municipalityModal" tabindex="-1" aria-labelledby="municipalityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <?php if ($mun == ""): ?>
                    <h5 class="modal-title" id="municipalityModalLabel">Select Municipality</h5>
                <?php else: ?>
                    <h5 class="modal-title" id="municipalityModalLabel">Select Barangay</h5>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="municipalityForm">
                    <div class="mb-3">
                        <?php
                        $municipalities = get_array("SELECT DISTINCT municipality FROM barangays ORDER BY municipality ASC");
                        ?>
                        <label for="municipality" class="form-label">Municipality</label>
                        <select class="form-select" id="municipality" name="municipality" onchange="loadBarangays()" required>
                            <option value="">Select Municipality</option>
                            <?php foreach ($municipalities as $munItem): ?>
                                <option value="<?php echo $munItem['municipality']; ?>">
                                    <?php echo $munItem['municipality']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="barangay" class="form-label mt-3">Barangay</label>
                        <select class="form-select" id="barangay" name="barangay" required>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i>Leave municipality and barangay blank for provincewide dashboard.</i>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="selectMunicipality()">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>