<div class="container">
    <h1 class="title"><?= $title ?></h1>
    
    <div class="columns">
        <div class="column is-3">
            <aside class="menu">
                <p class="menu-label">
                    Administration
                </p>
                <ul class="menu-list">
                    <li><a class="is-active" href="/admin/dashboard">Dashboard</a></li>
                    <li><a href="/admin/users">User Management</a></li>
                    <li><a href="/admin/content">Content Management</a></li>
                    <li><a href="/admin/settings">System Settings</a></li>
                </ul>
            </aside>
        </div>
        
        <div class="column">
            <div class="box">
                <h2 class="subtitle">System Overview</h2>
                
                <div class="columns">
                    <div class="column">
                        <div class="notification is-primary">
                            <p class="title"><?= count($users) ?></p>
                            <p class="subtitle">Users</p>
                        </div>
                    </div>
                    
                    <div class="column">
                        <div class="notification is-info">
                            <p class="title">0</p>
                            <p class="subtitle">Blog Posts</p>
                        </div>
                    </div>
                    
                    <div class="column">
                        <div class="notification is-success">
                            <p class="title">0</p>
                            <p class="subtitle">Tracks</p>
                        </div>
                    </div>
                    
                    <div class="column">
                        <div class="notification is-warning">
                            <p class="title">0</p>
                            <p class="subtitle">Playlists</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box">
                <h2 class="subtitle">Recent Users</h2>
                
                <table class="table is-fullwidth">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($users, 0, 5) as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['first_name'] . ' ' . $user['last_name'] ?></td>
                                <td><span class="tag is-info"><?= $user['role'] ?></span></td>
                                <td><?= (new DateTime($user['created_at']))->format('Y-m-d H:i') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="has-text-right">
                    <a href="/admin/users" class="button is-small">View All Users</a>
                </div>
            </div>
        </div>
    </div>
</div>