<?php
// CSV Import Upload Form
$additionalJS = ['/assets/js/import.js'];
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-upload me-2"></i>
                    Upload CSV File
                </h5>
            </div>
            <div class="card-body">
                <form id="csvUploadForm" action="/import/upload" method="POST" enctype="multipart/form-data">
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="text-center">
                            <i class="bi bi-cloud-upload display-1 text-muted mb-3"></i>
                            <h4>Drop your CSV file here</h4>
                            <p class="text-muted mb-3">or click to browse and select a file</p>
                            <input type="file" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv" 
                                   class="d-none" 
                                   required>
                            <button type="button" 
                                    class="btn btn-primary" 
                                    onclick="document.getElementById('csv_file').click()">
                                <i class="bi bi-folder2-open me-1"></i>
                                Choose File
                            </button>
                        </div>
                        
                        <div id="fileInfo" class="mt-3 d-none">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    <div>
                                        <strong id="fileName"></strong><br>
                                        <small id="fileSize" class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success w-100" id="uploadBtn" disabled>
                                    <i class="bi bi-upload me-1"></i>
                                    Upload and Validate
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="/import/template" class="btn btn-outline-info w-100">
                                    <i class="bi bi-download me-1"></i>
                                    Download Template
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Import Guidelines
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-check-circle text-success me-1"></i> File Requirements</h6>
                    <ul class="list-unstyled small">
                        <li>• CSV format only</li>
                        <li>• Maximum size: <?php echo (MAX_UPLOAD_SIZE / 1024 / 1024); ?>MB</li>
                        <li>• Maximum rows: <?php echo number_format(CSV_MAX_ROWS); ?></li>
                        <li>• UTF-8 encoding recommended</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-exclamation-triangle text-warning me-1"></i> Important Notes</h6>
                    <ul class="list-unstyled small">
                        <li>• NIDHH must be unique</li>
                        <li>• Required fields: State, LGA, Ward, Community, NIDHH</li>
                        <li>• Dates in YYYY-MM-DD format</li>
                        <li>• Phone numbers: 7-15 digits</li>
                        <li>• Age: 0-120 years</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-gear text-primary me-1"></i> Process</h6>
                    <ol class="list-unstyled small">
                        <li>1. Upload CSV file</li>
                        <li>2. System validates data</li>
                        <li>3. Preview import data</li>
                        <li>4. Confirm and import</li>
                        <li>5. View results</li>
                    </ol>
                </div>
                
                <div class="alert alert-warning small">
                    <i class="bi bi-shield-exclamation me-1"></i>
                    <strong>Data Security:</strong> Files are processed securely and deleted after import.
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Expected Headers
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="headersAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#basicFields">
                                Basic Information (5 fields)
                            </button>
                        </h2>
                        <div id="basicFields" class="accordion-collapse collapse" data-bs-parent="#headersAccordion">
                            <div class="accordion-body small">
                                <ul class="list-unstyled">
                                    <li>• State <span class="text-danger">*</span></li>
                                    <li>• LGA <span class="text-danger">*</span></li>
                                    <li>• Ward <span class="text-danger">*</span></li>
                                    <li>• Community <span class="text-danger">*</span></li>
                                    <li>• nidhh <span class="text-danger">*</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#householdFields">
                                Household Details (3 fields)
                            </button>
                        </h2>
                        <div id="householdFields" class="accordion-collapse collapse" data-bs-parent="#headersAccordion">
                            <div class="accordion-body small">
                                <ul class="list-unstyled">
                                    <li>• HouseHoldNo</li>
                                    <li>• HAddress</li>
                                    <li>• TrancheStatus</li>
                                    <li>• TotalAmount</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trancheFields">
                                Tranche Fields (24 fields)
                            </button>
                        </h2>
                        <div id="trancheFields" class="accordion-collapse collapse" data-bs-parent="#headersAccordion">
                            <div class="accordion-body small">
                                <p class="text-muted">Each tranche has 8 fields:</p>
                                <ul class="list-unstyled">
                                    <li>• [Tranche]Recipient</li>
                                    <li>• [Tranche]AccountNumber</li>
                                    <li>• [Tranche]BankName</li>
                                    <li>• [Tranche]PaymentDate</li>
                                    <li>• [Tranche]Phone</li>
                                    <li>• [Tranche]Gender</li>
                                    <li>• [Tranche]Age</li>
                                    <li>• [Tranche]IDType</li>
                                </ul>
                                <small class="text-muted">Where [Tranche] = First, Second, Third</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        Total: <?php echo count($expectedHeaders); ?> columns required
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="uploadProgressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-upload me-2"></i>
                    Processing File
                </h5>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Uploading and validating your CSV file...</p>
                <p class="text-muted small">This may take a few moments for large files.</p>
            </div>
        </div>
    </div>
</div>
