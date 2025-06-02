<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: 401.html");
    exit();
}

require_once 'db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $so_number = mysqli_real_escape_string($conn, $_POST['so_number']);
                $customer_id = (int)$_POST['customer_id'];
                $order_date = mysqli_real_escape_string($conn, $_POST['order_date']);
                $shipping_date = mysqli_real_escape_string($conn, $_POST['shipping_date']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                
                $sql = "INSERT INTO sales_orders (so_number, customer_id, order_date, shipping_date, status, notes, created_by) 
                        VALUES ('$so_number', $customer_id, '$order_date', '$shipping_date', '$status', '$notes', 1)";
                if (mysqli_query($conn, $sql)) {
                    $success = "Sales Order added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['so_id'];
                $so_number = mysqli_real_escape_string($conn, $_POST['so_number']);
                $customer_id = (int)$_POST['customer_id'];
                $order_date = mysqli_real_escape_string($conn, $_POST['order_date']);
                $shipping_date = mysqli_real_escape_string($conn, $_POST['shipping_date']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                
                $sql = "UPDATE sales_orders SET so_number='$so_number', customer_id=$customer_id, 
                        order_date='$order_date', shipping_date='$shipping_date', status='$status', notes='$notes' 
                        WHERE so_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Sales Order updated successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['so_id'];
                $sql = "DELETE FROM sales_orders WHERE so_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Sales Order deleted successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
        }
    }
}

// Fetch sales orders with customer names
$sql = "SELECT so.*, c.customer_name FROM sales_orders so 
        LEFT JOIN customers c ON so.customer_id = c.customer_id 
        ORDER BY so.order_date DESC";
$result = mysqli_query($conn, $sql);

// Fetch customers for dropdown
$customers_sql = "SELECT * FROM customers ORDER BY customer_name";
$customers_result = mysqli_query($conn, $customers_sql);
$customers = [];
while ($customer = mysqli_fetch_assoc($customers_result)) {
    $customers[] = $customer;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Sales Orders - Inventory System</title>
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
                            <a class="nav-link active" href="sales_orders.php">
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
                        <h1 class="mt-4">Sales Orders</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sales Orders</li>
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
                                Add New Sales Order
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input class="form-control" id="soNumber" name="so_number" type="text" required />
                                                <label for="soNumber">SO Number</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" id="customerId" name="customer_id" required>
                                                    <option value="">Select Customer</option>
                                                    <?php foreach ($customers as $customer): ?>
                                                        <option value="<?php echo $customer['customer_id']; ?>"><?php echo htmlspecialchars($customer['customer_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="customerId">Customer</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="pending">Pending</option>
                                                    <option value="processing">Processing</option>
                                                    <option value="shipped">Shipped</option>
                                                    <option value="delivered">Delivered</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="orderDate" name="order_date" type="date" value="<?php echo date('Y-m-d'); ?>" required />
                                                <label for="orderDate">Order Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="shippingDate" name="shipping_date" type="date" />
                                                <label for="shippingDate">Shipping Date</label>
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
                                    <button class="btn btn-primary" type="submit">Add Sales Order</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Sales Orders List
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>SO Number</th>
                                            <th>Customer</th>
                                            <th>Order Date</th>
                                            <th>Shipping Date</th>
                                            <th>Status</th>
                                            <th>Total Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['so_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['customer_name'] ?? 'No Customer'); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
                                            <td><?php echo $row['shipping_date'] ? date('Y-m-d', strtotime($row['shipping_date'])) : ''; ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $row['status'] == 'pending' ? 'warning' : 
                                                        ($row['status'] == 'processing' ? 'info' : 
                                                        ($row['status'] == 'shipped' ? 'primary' : 
                                                        ($row['status'] == 'delivered' ? 'success' : 'danger'))); 
                                                ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editSO(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteSO(<?php echo $row['so_id']; ?>)">
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
                            <h5 class="modal-title">Edit Sales Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="so_id" id="editSOId">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input class="form-control" id="editSONumber" name="so_number" type="text" required />
                                        <label for="editSONumber">SO Number</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="editCustomerId" name="customer_id" required>
                                            <option value="">Select Customer</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?php echo $customer['customer_id']; ?>"><?php echo htmlspecialchars($customer['customer_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="editCustomerId">Customer</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="editStatus" name="status">
                                            <option value="pending">Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        <label for="editStatus">Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="editOrderDate" name="order_date" type="date" required />
                                        <label for="editOrderDate">Order Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="editShippingDate" name="shipping_date" type="date" />
                                        <label for="editShippingDate">Shipping Date</label>
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
                            <button type="submit" class="btn btn-primary">Update Sales Order</button>
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
                            <h5 class="modal-title">Delete Sales Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="so_id" id="deleteSOId">
                            <p>Are you sure you want to delete this sales order?</p>
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
            function editSO(so) {
                document.getElementById('editSOId').value = so.so_id;
                document.getElementById('editSONumber').value = so.so_number;
                document.getElementById('editCustomerId').value = so.customer_id || '';
                document.getElementById('editStatus').value = so.status;
                document.getElementById('editOrderDate').value = so.order_date;
                document.getElementById('editShippingDate').value = so.shipping_date || '';
                document.getElementById('editNotes').value = so.notes || '';
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }

            function deleteSO(id) {
                document.getElementById('deleteSOId').value = id;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            }
        </script>
    </body>
</html>
