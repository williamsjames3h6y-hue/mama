<?php $pageTitle = 'Manage Projects - Admin'; ?>
<?php include __DIR__ . '/../header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/users" class="admin-nav-link">Users</a>
            <a href="/admin/projects" class="admin-nav-link active">Projects</a>
            <a href="/admin/payments" class="admin-nav-link">Payments</a>
            <a href="/admin/settings" class="admin-nav-link">Settings</a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="page-header">
            <h1>Manage Projects</h1>
            <button onclick="showAddProjectModal()" class="btn btn-success">Add New Project</button>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Rate Range</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo Helper::escape($project['title']); ?></td>
                            <td><?php echo Helper::formatMoney($project['rate_min']); ?> - <?php echo Helper::formatMoney($project['rate_max']); ?>/hr</td>
                            <td><?php echo Helper::escape($project['project_type']); ?></td>
                            <td><span class="badge badge-<?php echo $project['status']; ?>"><?php echo ucfirst($project['status']); ?></span></td>
                            <td><?php echo Helper::formatDate($project['created_at']); ?></td>
                            <td>
                                <a href="/admin/projects/edit/<?php echo $project['id']; ?>" class="btn-small btn-primary">Edit</a>
                                <form method="POST" action="/admin/projects/delete" class="inline-form">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="btn-small btn-danger" onclick="return confirm('Delete this project?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addProjectModal" class="modal">
    <div class="modal-content modal-large">
        <span class="close" onclick="closeModal('addProjectModal')">&times;</span>
        <h2>Add New Project</h2>
        <form method="POST" action="/admin/projects/add">
            <div class="form-group">
                <label for="title">Project Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="rate_min">Min Rate ($/hr)</label>
                    <input type="number" id="rate_min" name="rate_min" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="rate_max">Max Rate ($/hr)</label>
                    <input type="number" id="rate_max" name="rate_max" step="0.01" required>
                </div>
            </div>
            <div class="form-group">
                <label for="project_type">Project Type</label>
                <select id="project_type" name="project_type">
                    <option value="Remote">Remote</option>
                    <option value="Contract">Contract</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Full-time">Full-time</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-block">Add Project</button>
        </form>
    </div>
</div>

<script>
function showAddProjectModal() {
    document.getElementById('addProjectModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include __DIR__ . '/../footer.php'; ?>
