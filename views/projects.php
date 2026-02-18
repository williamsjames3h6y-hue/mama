<?php $pageTitle = 'Projects - EarningsLLC'; ?>
<?php include 'header.php'; ?>

<div class="projects-container">
    <div class="page-header">
        <h1>Open Opportunities</h1>
    </div>

    <div class="projects-list">
        <?php if (empty($projects)): ?>
            <p class="empty-state">No projects available at the moment.</p>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <div class="project-header">
                        <h2><?php echo Helper::escape($project['title']); ?></h2>
                        <div class="project-rate">
                            <?php echo Helper::formatMoney($project['rate_min']); ?>–<?php echo Helper::formatMoney($project['rate_max']); ?>/hr
                        </div>
                    </div>

                    <p class="project-description"><?php echo Helper::escape($project['description']); ?></p>

                    <div class="project-meta">
                        <span class="badge"><?php echo Helper::escape($project['project_type']); ?></span>
                        <?php if (isset($userProjects[$project['id']])): ?>
                            <span class="status-badge status-<?php echo $userProjects[$project['id']]['status']; ?>">
                                ✓ <?php echo ucfirst($userProjects[$project['id']]['status']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!isset($userProjects[$project['id']])): ?>
                        <form method="POST" action="/projects/apply" class="inline-form">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <button type="submit" class="btn btn-primary">Apply Now</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
