<?php
// Beneficiaries list view with filtering and pagination
$additionalJS = ['/assets/js/beneficiaries.js'];
?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i>
            Filters
            <button class="btn btn-sm btn-outline-secondary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                <i class="bi bi-chevron-down"></i>
            </button>
        </h5>
    </div>
    <div class="collapse show" id="filtersCollapse">
        <div class="card-body">
            <form method="GET" action="/beneficiaries" id="filtersForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state" onchange="updateLocationFilters()">
                            <option value="">All States</option>
                            <?php foreach ($filterOptions['states'] as $state): ?>
                                <option value="<?php echo htmlspecialchars($state); ?>" 
                                        <?php echo $filters['state'] === $state ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($state); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="lga" class="form-label">LGA</label>
                        <select class="form-select" id="lga" name="lga" onchange="updateLocationFilters()">
                            <option value="">All LGAs</option>
                            <?php foreach ($filterOptions['lgas'] as $lga): ?>
                                <option value="<?php echo htmlspecialchars($lga); ?>" 
                                        <?php echo $filters['lga'] === $lga ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($lga); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="ward" class="form-label">Ward</label>
                        <select class="form-select" id="ward" name="ward" onchange="updateLocationFilters()">
                            <option value="">All Wards</option>
                            <?php foreach ($filterOptions['wards'] as $ward): ?>
                                <option value="<?php echo htmlspecialchars($ward); ?>" 
                                        <?php echo $filters['ward'] === $ward ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ward); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="community" class="form-label">Community</label>
                        <select class="form-select" id="community" name="community">
                            <option value="">All Communities</option>
                            <?php foreach ($filterOptions['communities'] as $community): ?>
                                <option value="<?php echo htmlspecialchars($community); ?>" 
                                        <?php echo $filters['community'] === $community ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($community); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tranche_status" class="form-label">Tranche Status</label>
                        <select class="form-select" id="tranche_status" name="tranche_status">
                            <option value="">All Statuses</option>
                            <?php foreach ($filterOptions['tranche_statuses'] as $status): ?>
                                <option value="<?php echo $status; ?>" 
                                        <?php echo $filters['tranche_status'] === $status ? 'selected' : ''; ?>>
                                    <?php echo $status; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="NIDHH, Household No, Address..." 
                               value="<?php echo htmlspecialchars($filters['search']); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>
                            Apply Filters
                        </button>
                        <a href="/beneficiaries" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>
                            Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Results Summary -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="mb-0">
            Showing <?php echo number_format(count($beneficiaries)); ?> of <?php echo number_format($pagination['total_count']); ?> beneficiaries
            <?php if (array_filter($filters)): ?>
                <span class="text-muted">(filtered)</span>
            <?php endif; ?>
        </h6>
    </div>
    
    <div class="d-flex align-items-center">
        <label for="per_page" class="form-label me-2 mb-0">Show:</label>
        <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="changePerPage(this.value)" style="width: auto;">
            <option value="25" <?php echo $pagination['per_page'] == 25 ? 'selected' : ''; ?>>25</option>
            <option value="50" <?php echo $pagination['per_page'] == 50 ? 'selected' : ''; ?>>50</option>
            <option value="100" <?php echo $pagination['per_page'] == 100 ? 'selected' : ''; ?>>100</option>
        </select>
    </div>
</div>

<!-- Beneficiaries Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($beneficiaries)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h4>No beneficiaries found</h4>
                <p class="text-muted">
                    <?php if (array_filter($filters)): ?>
                        Try adjusting your filters or 
                        <a href="/beneficiaries">clear all filters</a>
                    <?php else: ?>
                        Get started by 
                        <?php if (hasPermission('import_csv')): ?>
                            <a href="/import">importing a CSV file</a> or 
                        <?php endif; ?>
                        <?php if (hasPermission('create_beneficiaries')): ?>
                            <a href="/beneficiaries/create">adding a beneficiary manually</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NIDHH</th>
                            <th>Location</th>
                            <th>Household</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Created</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($beneficiaries as $beneficiary): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($beneficiary['nidhh']); ?></strong>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><strong><?php echo htmlspecialchars($beneficiary['State']); ?></strong></div>
                                        <div class="text-muted">
                                            <?php echo htmlspecialchars($beneficiary['LGA']); ?>, 
                                            <?php echo htmlspecialchars($beneficiary['Ward']); ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?php echo htmlspecialchars($beneficiary['Community']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <?php if ($beneficiary['HouseHoldNo']): ?>
                                            <div><strong>HH:</strong> <?php echo htmlspecialchars($beneficiary['HouseHoldNo']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['HAddress']): ?>
                                            <div class="text-muted"><?php echo htmlspecialchars(substr($beneficiary['HAddress'], 0, 50)); ?><?php echo strlen($beneficiary['HAddress']) > 50 ? '...' : ''; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge status-<?php echo strtolower($beneficiary['TrancheStatus']); ?>">
                                        <?php echo $beneficiary['TrancheStatus']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($beneficiary['TotalAmount'] > 0): ?>
                                        <strong>₦<?php echo number_format($beneficiary['TotalAmount'], 2); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">₦0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('M j, Y', strtotime($beneficiary['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="/beneficiaries/view?nidhh=<?php echo urlencode($beneficiary['nidhh']); ?>" 
                                           class="btn btn-outline-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <?php if (hasPermission('edit_beneficiaries')): ?>
                                            <a href="/beneficiaries/edit?nidhh=<?php echo urlencode($beneficiary['nidhh']); ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (hasPermission('delete_beneficiaries')): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteBeneficiary('<?php echo htmlspecialchars($beneficiary['nidhh']); ?>')" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
    <nav aria-label="Beneficiaries pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo !$pagination['has_prev'] ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo buildPaginationUrl($pagination['prev_page'], $filters, $pagination['per_page']); ?>">
                    <i class="bi bi-chevron-left"></i>
                    Previous
                </a>
            </li>
            
            <?php
            $startPage = max(1, $pagination['current_page'] - 2);
            $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
            
            if ($startPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo buildPaginationUrl(1, $filters, $pagination['per_page']); ?>">1</a>
                </li>
                <?php if ($startPage > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo buildPaginationUrl($i, $filters, $pagination['per_page']); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <?php if ($endPage < $pagination['total_pages']): ?>
                <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo buildPaginationUrl($pagination['total_pages'], $filters, $pagination['per_page']); ?>">
                        <?php echo $pagination['total_pages']; ?>
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo buildPaginationUrl($pagination['next_page'], $filters, $pagination['per_page']); ?>">
                    Next
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this beneficiary?</p>
                <p class="text-muted small">
                    <strong>NIDHH:</strong> <span id="deleteNidhh"></span>
                </p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    This action cannot be undone. All tranche information will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="/beneficiaries/delete" style="display: inline;">
                    <input type="hidden" name="nidhh" id="deleteNidhhInput">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Delete Beneficiary
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Helper function to build pagination URLs
 */
function buildPaginationUrl($page, $filters, $perPage) {
    $params = array_filter($filters);
    $params['page'] = $page;
    $params['per_page'] = $perPage;
    
    return '/beneficiaries?' . http_build_query($params);
}
?>
