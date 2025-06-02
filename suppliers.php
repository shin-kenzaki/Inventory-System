<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: 401.html");
    exit();
}

require_once 'db.php';

// Handle form submissions for suppliers CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
                $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
                $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
                $contact_phone = mysqli_real_escape_string($conn, $_POST['contact_phone']);
                $address = mysqli_real_escape_string($conn, $_POST['address']);
                $city = mysqli_real_escape_string($conn, $_POST['city']);
                $state = mysqli_real_escape_string($conn, $_POST['state']);
                $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
                $country = mysqli_real_escape_string($conn, $_POST['country']);
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                
                $sql = "INSERT INTO suppliers (supplier_name, contact_person, contact_email, contact_phone, address, city, state, postal_code, country, notes) 
                        VALUES ('$supplier_name', '$contact_person', '$contact_email', '$contact_phone', '$address', '$city', '$state', '$postal_code', '$country', '$notes')";
                if (mysqli_query($conn, $sql)) {
                    $success = "Supplier added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['supplier_id'];
                $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
                $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
                $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
                $contact_phone = mysqli_real_escape_string($conn, $_POST['contact_phone']);
                $address = mysqli_real_escape_string($conn, $_POST['address']);
                $city = mysqli_real_escape_string($conn, $_POST['city']);
                $state = mysqli_real_escape_string($conn, $_POST['state']);
                $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
                $country = mysqli_real_escape_string($conn, $_POST['country']);
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                
                $sql = "UPDATE suppliers SET supplier_name='$supplier_name', contact_person='$contact_person', 
                        contact_email='$contact_email', contact_phone='$contact_phone', address='$address', 
                        city='$city', state='$state', postal_code='$postal_code', country='$country', notes='$notes' 
                        WHERE supplier_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Supplier updated successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['supplier_id'];
                $sql = "DELETE FROM suppliers WHERE supplier_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Supplier deleted successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
        }
    }
}

// Fetch suppliers
$sql = "SELECT * FROM suppliers ORDER BY supplier_name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Suppliers - Inventory System</title>
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
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.php">Inventory System</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.php">
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
                            <a class="nav-link active" href="suppliers.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-industry"></i></div>
                                Suppliers
                            </a>
                            <a class="nav-link" href="customers.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Customers
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">System</div>
                            <a class="nav-link" href="users.php">
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
                        <h1 class="mt-4">Suppliers</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Suppliers</li>
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
                                Add New Supplier
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="supplierName" name="supplier_name" type="text" required />
                                                <label for="supplierName">Supplier Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="contactPerson" name="contact_person" type="text" />
                                                <label for="contactPerson">Contact Person</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="contactEmail" name="contact_email" type="email" />
                                                <label for="contactEmail">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="contactPhone" name="contact_phone" type="text" />
                                                <label for="contactPhone">Phone</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input class="form-control" id="address" name="address" type="text" />
                                                <label for="address">Address</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="city" name="city" type="text" />
                                                <label for="city">City</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="state" name="state" type="text" />
                                                <label for="state">State</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="postalCode" name="postal_code" type="text" />
                                                <label for="postalCode">Postal Code</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="country" name="country" type="text" />
                                                <label for="country">Country</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" id="notes" name="notes" style="height: 100px"></textarea>
                                                <label for="notes">Notes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Add Supplier</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Suppliers List
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Contact Person</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>City</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $row['supplier_id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact_person'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact_email'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact_phone'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['city'] ?? ''); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editSupplier(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteSupplier(<?php echo $row['supplier_id']; ?>)">
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
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Inventory System 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Supplier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="supplier_id" id="editSupplierId">
                            <!-- Form fields similar to add form -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="editSupplierName" name="supplier_name" type="text" required />
                                        <label for="editSupplierName">Supplier Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="editContactPerson" name="contact_person" type="text" />
                                        <label for="editContactPerson">Contact Person</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="editContactEmail" name="contact_email" type="email" />
                                        <label for="editContactEmail">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="editContactPhone" name="contact_phone" type="text" />
                                        <label for="editContactPhone">Phone</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input class="form-control" id="editAddress" name="address" type="text" />
                                        <label for="editAddress">Address</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editCity" name="city" type="text" />
                                        <label for="editCity">City</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editState" name="state" type="text" />
                                        <label for="editState">State</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editPostalCode" name="postal_code" type="text" />
                                        <label for="editPostalCode">Postal Code</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editCountry" name="country" type="text" />
                                        <label for="editCountry">Country</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="editNotes" name="notes" style="height: 100px"></textarea>
                                        <label for="editNotes">Notes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Supplier</button>
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
                            <h5 class="modal-title">Delete Supplier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="supplier_id" id="deleteSupplierId">
                            <p>Are you sure you want to delete this supplier?</p>
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
            function editSupplier(supplier) {
                document.getElementById('editSupplierId').value = supplier.supplier_id;
                document.getElementById('editSupplierName').value = supplier.supplier_name;
                document.getElementById('editContactPerson').value = supplier.contact_person || '';
                document.getElementById('editContactEmail').value = supplier.contact_email || '';
                document.getElementById('editContactPhone').value = supplier.contact_phone || '';
                document.getElementById('editAddress').value = supplier.address || '';
                document.getElementById('editCity').value = supplier.city || '';
                document.getElementById('editState').value = supplier.state || '';
                document.getElementById('editPostalCode').value = supplier.postal_code || '';
                document.getElementById('editCountry').value = supplier.country || '';
                document.getElementById('editNotes').value = supplier.notes || '';
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }

            function deleteSupplier(id) {
                document.getElementById('deleteSupplierId').value = id;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            }
        </script>
    </body>
</html>