<?php
require_once 'db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
                $role = mysqli_real_escape_string($conn, $_POST['role']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                
                $sql = "INSERT INTO users (username, email, password, full_name, role, status) 
                        VALUES ('$username', '$email', '$password', '$full_name', '$role', '$status')";
                if (mysqli_query($conn, $sql)) {
                    $success = "User added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['user_id'];
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
                $role = mysqli_real_escape_string($conn, $_POST['role']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                
                $sql = "UPDATE users SET username='$username', email='$email', full_name='$full_name', 
                        role='$role', status='$status' WHERE user_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "User updated successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['user_id'];
                $sql = "DELETE FROM users WHERE user_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "User deleted successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
        }
    }
}

$sql = "SELECT * FROM users ORDER BY full_name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Users - Inventory System</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            table th {
                text-align: center !important;
                vertical-align: middle !important;
            }
            .dataTable thead th {
                text-align: center !important;
                vertical-align: middle !important;
            }
            table td {
                text-align: center !important;
                vertical-align: middle !important;
            }
            .dataTable tbody td {
                text-align: center !important;
                vertical-align: middle !important;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <!-- ...existing navbar... -->
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.html">Inventory System</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="#!">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <div id="layoutSidenav">
            <!-- ...existing sidebar with users.php active... -->
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Inventory Management</div>
                            <a class="nav-link" href="inventory_items.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                                Inventory Items
                            </a>
                            <a class="nav-link" href="categories.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                                Categories
                            </a>
                            <a class="nav-link" href="locations.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-map-marker-alt"></i></div>
                                Locations
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Orders & Transactions</div>
                            <a class="nav-link" href="purchase_orders.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                                Purchase Orders
                            </a>
                            <a class="nav-link" href="sales_orders.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                                Sales Orders
                            </a>
                            <a class="nav-link" href="inventory_transactions.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                                Transactions
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Contacts</div>
                            <a class="nav-link" href="suppliers.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-industry"></i></div>
                                Suppliers
                            </a>
                            <a class="nav-link" href="customers.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Customers
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">System</div>
                            <a class="nav-link active" href="users.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                                Users
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Administrator
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Users</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-plus me-1"></i>
                                Add New User
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="username" name="username" type="text" required />
                                                <label for="username">Username</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="email" name="email" type="email" required />
                                                <label for="email">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="password" name="password" type="password" required />
                                                <label for="password">Password</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="fullName" name="full_name" type="text" required />
                                                <label for="fullName">Full Name</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="role" name="role" required>
                                                    <option value="user">User</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="manager">Manager</option>
                                                </select>
                                                <label for="role">Role</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Add User</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Users
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php 
                                                    switch($row['role']) {
                                                        case 'admin': echo 'bg-danger'; break;
                                                        case 'manager': echo 'bg-warning'; break;
                                                        default: echo 'bg-secondary';
                                                    }
                                                    ?>">
                                                    <?php echo ucfirst($row['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo ($row['status'] ?? 'inactive') == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($row['status'] ?? 'inactive'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $row['user_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <!-- ...existing footer... -->
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="user_id" id="editUserId">
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input class="form-control" id="editUsername" name="username" type="text" required />
                                    <label for="editUsername">Username</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input class="form-control" id="editEmail" name="email" type="email" required />
                                    <label for="editEmail">Email</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input class="form-control" id="editFullName" name="full_name" type="text" required />
                                    <label for="editFullName">Full Name</label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="editRole" name="role" required>
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                            <option value="manager">Manager</option>
                                        </select>
                                        <label for="editRole">Role</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="editStatus" name="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <label for="editStatus">Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" id="deleteUserId">
                            <p>Are you sure you want to delete this user?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script>
            function editUser(user) {
                document.getElementById('editUserId').value = user.user_id;
                document.getElementById('editUsername').value = user.username;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editFullName').value = user.full_name;
                document.getElementById('editRole').value = user.role;
                document.getElementById('editStatus').value = user.status;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }

            function deleteUser(id) {
                document.getElementById('deleteUserId').value = id;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            }
        </script>
    </body>
</html>
