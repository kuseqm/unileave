

<?php $__env->startSection('title', 'Database Schema Viewer'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Database Schema', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Database Schema', 'url' => '#']
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
    
    <!-- Page Header -->
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'DATABASE SCHEMA VIEWER','description' => 'View database tables, columns, and relationships']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'DATABASE SCHEMA VIEWER','description' => 'View database tables, columns, and relationships']); ?>
        <div>
            <a href="<?php echo e(route('admin.database-schema.export-json')); ?>" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-file-export me-1"></i> Export JSON
            </a>
            <button type="button" onclick="window.print();" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-printer me-1"></i> Print
            </button>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e(count($tableNames)).'','label' => 'Total Tables','subtext' => 'in database','icon' => 'database']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e(count($tableNames)).'','label' => 'Total Tables','subtext' => 'in database','icon' => 'database']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($migrations->count()).'','label' => 'Migrations','subtext' => 'run','icon' => 'history']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($migrations->count()).'','label' => 'Migrations','subtext' => 'run','icon' => 'history']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($migrations->max('batch')).'','label' => 'Latest Batch','subtext' => 'migration batch','icon' => 'layers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($migrations->max('batch')).'','label' => 'Latest Batch','subtext' => 'migration batch','icon' => 'layers']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
    </div>
    
    <div class="row g-2">
        <!-- Left Column: Table List -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-table text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Database Tables</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="p-2 border-bottom">
                        <input type="text" id="tableSearch" class="form-control form-control-sm rounded-sm" placeholder="Search tables..." style="font-size: 0.7rem;">
                    </div>
                    <div class="table-list" style="max-height: 500px; overflow-y: auto;">
                        <?php $__currentLoopData = $tableNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="table-item d-flex justify-content-between align-items-center px-2 py-1 border-bottom" data-table="<?php echo e($table); ?>" style="cursor: pointer;" onclick="loadTableDetails('<?php echo e($table); ?>')">
                            <div>
                                <i class="ti ti-table text-primary me-1" style="font-size: 0.7rem;"></i>
                                <span style="font-size: 0.7rem; font-weight: 500;"><?php echo e($table); ?></span>
                            </div>
                            <div>
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">
                                    <?php echo e(number_format($schema[$table]['row_count'])); ?> rows
                                </span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Migration History Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Migration History</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.65rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Migration</th>
                                    <th class="py-1">Batch</th>
                                    <th class="pe-2 py-1">Run At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $migrations->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $migration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-2 py-1"><?php echo e(basename($migration->migration)); ?></td>
                                    <td class="py-1"><?php echo e($migration->batch); ?></td>
                                    <td class="pe-2 py-1">
                                        <?php if(isset($migration->created_at) && $migration->created_at): ?>
                                            <?php echo e(\Carbon\Carbon::parse($migration->created_at)->format('d M Y H:i')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Table Details -->
        <div class="col-lg-8">
            <div class="card rounded-sm" id="tableDetailsCard">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-details text-primary me-1" style="font-size: 0.7rem;"></i>
                            <span style="font-weight: 600; font-size: 0.7rem;">Table Details</span>
                        </div>
                        <span id="selectedTable" class="badge bg-primary rounded-sm" style="font-size: 0.6rem;">Select a table</span>
                    </div>
                </div>
                <div class="card-body p-2" id="tableDetailsContent">
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-table mb-2 d-block" style="font-size: 2rem;"></i>
                        <p style="font-size: 0.75rem;">Click on any table from the left to view its details</p>
                    </div>
                </div>
            </div>
            
            <!-- ER Diagram Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-share text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Entity Relationship Diagram (ERD)</span>
                    </div>
                </div>
                <div class="card-body p-2" style="overflow-x: auto;">
                    <div id="erdContainer" style="min-width: 800px; min-height: 500px; position: relative;">
                        <canvas id="erdCanvas" style="width: 100%; height: auto; background: #FDF8F0;"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted" style="font-size: 0.55rem;">
                            <i class="ti ti-info-circle me-1"></i>
                            Simplified ER diagram showing table relationships
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-item:hover {
    background-color: #F8FAFC;
}
.table-item.active {
    background-color: #EFF6FF;
    border-left: 3px solid #3B82F6;
}
</style>

<script>
let currentTable = null;

function loadTableDetails(tableName) {
    currentTable = tableName;
    
    // Update active state
    document.querySelectorAll('.table-item').forEach(item => {
        item.classList.remove('active');
        if (item.dataset.table === tableName) {
            item.classList.add('active');
        }
    });
    
    document.getElementById('selectedTable').innerText = tableName;
    
    fetch(`/admin/database-schema/table/${tableName}`)
        .then(response => response.json())
        .then(data => {
            renderTableDetails(data, tableName);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tableDetailsContent').innerHTML = `
                <div class="alert alert-danger py-1 px-2 rounded-sm" style="font-size: 0.7rem;">
                    <i class="ti ti-alert-circle me-1"></i>
                    Failed to load table details
                </div>
            `;
        });
}

function renderTableDetails(data, tableName) {
    let html = `
        <div class="mb-2">
            <h6 style="font-size: 0.75rem;">
                <i class="ti ti-table text-primary me-1"></i> 
                Table: <code>${tableName}</code>
                <span class="badge bg-secondary-subtle text-secondary rounded-sm ms-2">${data.row_count.toLocaleString()} rows</span>
            </h6>
        </div>
        
        <!-- Columns Table -->
        <div class="mb-2">
            <div class="fw-semibold mb-1" style="font-size: 0.65rem;">Columns</div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered" style="font-size: 0.65rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>Column</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </thead>
                        <tbody>
    `;
    
    data.columns.forEach(col => {
        html += `
            <tr>
                <td><code>${col.Field}</code></td>
                <td>${col.Type}</td>
                <td>${col.Null}</td>
                <td>${col.Key || '-'}</td>
                <td>${col.Default ?? '-'}</td>
                <td>${col.Extra || '-'}</td>
            </tr>
        `;
    });
    
    html += `
                        </tbody>
                    </table>
                </div>
            </div>
    `;
    
    // Foreign Keys
    if (data.foreign_keys && data.foreign_keys.length > 0) {
        html += `
            <div class="mb-2">
                <div class="fw-semibold mb-1" style="font-size: 0.65rem;">Foreign Keys</div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" style="font-size: 0.65rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>Column</th>
                                <th>References</th>
                                <th>Relationship</th>
                            </thead>
                            <tbody>
        `;
        
        data.foreign_keys.forEach(fk => {
            html += `
                <tr>
                    <td><code>${fk.COLUMN_NAME}</code></td>
                    <td><code>${fk.REFERENCED_TABLE_NAME}.${fk.REFERENCED_COLUMN_NAME}</code></td>
                    <td><span class="badge bg-info-subtle text-info rounded-sm">BELONGS TO</span></td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
        `;
    }
    
    // Sample Data
    if (data.sample_data && data.sample_data.length > 0) {
        const sampleKeys = Object.keys(data.sample_data[0]);
        html += `
            <div class="mb-2">
                <div class="fw-semibold mb-1" style="font-size: 0.65rem;">Sample Data (Last 5 records)</div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" style="font-size: 0.6rem;">
                        <thead class="bg-light">
                            <tr>
        `;
        
        sampleKeys.slice(0, 6).forEach(key => {
            html += `<th>${key}</th>`;
        });
        
        html += `</thead><tbody>`;
        
        data.sample_data.forEach(row => {
            html += `<tr>`;
            sampleKeys.slice(0, 6).forEach(key => {
                let value = row[key];
                if (value === null) value = '<em class="text-muted">NULL</em>';
                if (typeof value === 'object') value = JSON.stringify(value).substring(0, 30);
                html += `<td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${value}</td>`;
            });
            html += `</tr>`;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
        `;
    }
    
    document.getElementById('tableDetailsContent').innerHTML = html;
}

// Search functionality
document.getElementById('tableSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.table-item');
    
    items.forEach(item => {
        const tableName = item.dataset.table.toLowerCase();
        if (tableName.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Draw simple ER diagram on canvas
function drawERDiagram() {
    const canvas = document.getElementById('erdCanvas');
    const container = document.getElementById('erdContainer');
    
    if (!canvas || !container) return;
    
    canvas.width = container.clientWidth - 20;
    canvas.height = 500;
    
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    const tables = <?php echo json_encode($tableNames, 15, 512) ?>;
    const relationships = <?php echo json_encode($relationships, 15, 512) ?>;
    
    const relatedTables = new Set();
    relationships.forEach(rel => {
        relatedTables.add(rel.from_table);
        relatedTables.add(rel.to_table);
    });
    
    const mainTables = Array.from(relatedTables).slice(0, 12);
    
    if (mainTables.length === 0) {
        ctx.fillStyle = '#64748B';
        ctx.font = '12px Inter';
        ctx.textAlign = 'center';
        ctx.fillText('No relationships detected', canvas.width / 2, canvas.height / 2);
        return;
    }
    
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(canvas.width, canvas.height) / 2.5;
    const positions = [];
    
    mainTables.forEach((table, index) => {
        const angle = (index / mainTables.length) * Math.PI * 2;
        const x = centerX + radius * Math.cos(angle);
        const y = centerY + radius * Math.sin(angle);
        
        ctx.fillStyle = '#EFF6FF';
        ctx.strokeStyle = '#3B82F6';
        ctx.lineWidth = 1.5;
        ctx.fillRect(x - 80, y - 15, 160, 30);
        ctx.strokeRect(x - 80, y - 15, 160, 30);
        
        ctx.fillStyle = '#1E293B';
        ctx.font = 'bold 10px Inter';
        ctx.textAlign = 'center';
        ctx.fillText(table, x, y + 4);
        
        positions.push({ name: table, x: x, y: y });
    });
    
    relationships.forEach(rel => {
        const from = positions.find(p => p.name === rel.from_table);
        const to = positions.find(p => p.name === rel.to_table);
        
        if (from && to) {
            ctx.beginPath();
            ctx.moveTo(from.x, from.y);
            ctx.lineTo(to.x, to.y);
            ctx.strokeStyle = '#10B981';
            ctx.lineWidth = 1;
            ctx.stroke();
            
            const angle = Math.atan2(to.y - from.y, to.x - from.x);
            const arrowX = to.x - 10 * Math.cos(angle);
            const arrowY = to.y - 10 * Math.sin(angle);
            
            ctx.fillStyle = '#10B981';
            ctx.beginPath();
            ctx.moveTo(arrowX, arrowY);
            ctx.lineTo(arrowX - 5 * Math.cos(angle - Math.PI / 6), arrowY - 5 * Math.sin(angle - Math.PI / 6));
            ctx.lineTo(arrowX - 5 * Math.cos(angle + Math.PI / 6), arrowY - 5 * Math.sin(angle + Math.PI / 6));
            ctx.fill();
        }
    });
    
    ctx.fillStyle = '#64748B';
    ctx.font = '9px Inter';
    ctx.textAlign = 'center';
    ctx.fillText('Entity Relationship Diagram (Simplified)', canvas.width / 2, canvas.height - 10);
}

window.addEventListener('load', () => {
    setTimeout(drawERDiagram, 100);
});

window.addEventListener('resize', () => {
    setTimeout(drawERDiagram, 100);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/database-schema/index.blade.php ENDPATH**/ ?>