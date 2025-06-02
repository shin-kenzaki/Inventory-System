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
                $item_id = (int)$_POST['item_id'];
                $transaction_type = mysqli_real_escape_string($conn, $_POST['transaction_type']);
                $quantity = (int)$_POST['quantity'];
                $reference_id = !empty($_POST['reference_id']) ? (int)$_POST['reference_id'] : NULL;
                $reference_type = mysqli_real_escape_string($conn, $_POST['reference_type']);
                $from_location_id = !empty($_POST['from_location_id']) ? (int)$_POST['from_location_id'] : NULL;
                $to_location_id = !empty($_POST['to_location_id']) ? (int)$_POST['to_location_id'] : NULL;
                $unit_cost = !empty($_POST['unit_cost']) ? (float)$_POST['unit_cost'] : NULL;
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                
                $sql = "INSERT INTO inventory_transactions (item_id, transaction_type, quantity, reference_id, reference_type, from_location_id, to_location_id, unit_cost, notes, created_by) 
                        VALUES ($item_id, '$transaction_type', $quantity, " . ($reference_id ? $reference_id : 'NULL') . ", '$reference_type', " . ($from_location_id ? $from_location_id : 'NULL') . ", " . ($to_location_id ? $to_location_id : 'NULL') . ", " . ($unit_cost ? $unit_cost : 'NULL') . ", '$notes', 1)";
                
                if (mysqli_query($conn, $sql)) {
                    // Update inventory stock based on transaction type
                    $stock_change = 0;
                    switch ($transaction_type) {
                        case 'purchase':
                        case 'adjustment':
                            $stock_change = $quantity;
                            break;
                        case 'sale':
                        case 'return':
                            $stock_change = -$quantity;
                            break;
                        case 'transfer':
                            // For transfers, don't change total stock
                            $stock_change = 0;
                            break;
                    }
                    
                    if ($stock_change != 0) {
                        $update_stock = "UPDATE inventory_items SET current_stock = current_stock + $stock_change WHERE item_id = $item_id";
                        mysqli_query($conn, $update_stock);
                    }
                    
                    $success = "Transaction added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['transaction_id'];
                $item_id = (int)$_POST['item_id'];
                $transaction_type = mysqli_real_escape_string($conn, $_POST['transaction_type']);
                $quantity = (int)$_POST['quantity'];
                $reference_id = !empty($_POST['reference_id']) ? (int)$_POST['reference_id'] : NULL;
                $reference_type = mysqli_real_escape_string($conn, $_POST['reference_type']);
                $from_location_id = !empty($_POST['from_location_id']) ? (int)$_POST['from_location_id'] : NULL;
                $to_location_id = !empty($_POST['to_location_id']) ? (int)$_POST['to_location_id'] : NULL;
                $unit_cost = !empty($_POST['unit_cost']) ? (float)$_POST['unit_cost'] : NULL;
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                
                $sql = "UPDATE inventory_transactions SET item_id=$item_id, transaction_type='$transaction_type', quantity=$quantity, 
                        reference_id=" . ($reference_id ? $reference_id : 'NULL') . ", reference_type='$reference_type', 
                        from_location_id=" . ($from_location_id ? $from_location_id : 'NULL') . ", 
                        to_location_id=" . ($to_location_id ? $to_location_id : 'NULL') . ", 
                        unit_cost=" . ($unit_cost ? $unit_cost : 'NULL') . ", notes='$notes' 
                        WHERE transaction_id=$id";
                
                if (mysqli_query($conn, $sql)) {
                    $success = "Transaction updated successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['transaction_id'];
                $sql = "DELETE FROM inventory_transactions WHERE transaction_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Transaction deleted successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
        }
    }
}

// Fetch transactions with item and location names
$sql = "SELECT t.*, i.item_name, i.item_code, 
               fl.location_name as from_location, 
               tl.location_name as to_location,
               u.full_name as created_by_name
        FROM inventory_transactions t 
        LEFT JOIN inventory_items i ON t.item_id = i.item_id 
        LEFT JOIN locations fl ON t.from_location_id = fl.location_id 
        LEFT JOIN locations tl ON t.to_location_id = tl.location_id 
        LEFT JOIN users u ON t.created_by = u.user_id 
        ORDER BY t.transaction_date DESC";
$result = mysqli_query($conn, $sql);

// Fetch items for dropdown
$items_sql = "SELECT * FROM inventory_items ORDER BY item_name";
$items_result = mysqli_query($conn, $items_sql);
$items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $items[] = $item;
}

// Fetch locations for dropdown
$locations_sql = "SELECT * FROM locations ORDER BY location_name";
$locations_result = mysqli_query($conn, $locations_sql);
$locations = [];
while ($location = mysqli_fetch_assoc($locations_result)) {
    $locations[] = $location;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Inventory Transactions - Inventory System</title>
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
                            <a class="nav-link" href="sales_orders.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                                Sales Orders
                            </a>
                            <a class="nav-link active" href="inventory_transactions.php">
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
                        <h1 class="mt-4">Inventory Transactions</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Inventory Transactions</li>
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
                                Add New Transaction
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" id="itemId" name="item_id" required>
                                                    <option value="">Select Item</option>
                                                    <?php foreach ($items as $item): ?>
                                                        <option value="<?php echo $item['item_id']; ?>"><?php echo htmlspecialchars($item['item_code'] . ' - ' . $item['item_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="itemId">Item</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" id="transactionType" name="transaction_type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="purchase">Purchase</option>
                                                    <option value="sale">Sale</option>
                                                    <option value="adjustment">Adjustment</option>
                                                    <option value="transfer">Transfer</option>
                                                    <option value="return">Return</option>
                                                </select>
                                                <label for="transactionType">Transaction Type</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input class="form-control" id="quantity" name="quantity" type="number" required />
                                                <label for="quantity">Quantity</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="referenceId" name="reference_id" type="number" />
                                                <label for="referenceId">Reference ID</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="referenceType" name="reference_type" type="text" />
                                                <label for="referenceType">Reference Type</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select" id="fromLocationId" name="from_location_id">
                                                    <option value="">Select Location</option>
                                                    <?php foreach ($locations as $location): ?>
                                                        <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="fromLocationId">From Location</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select" id="toLocationId" name="to_location_id">
                                                    <option value="">Select Location</option>
                                                    <?php foreach ($locations as $location): ?>
                                                        <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="toLocationId">To Location</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input class="form-control" id="unitCost" name="unit_cost" type="number" step="0.01" />
                                                <label for="unitCost">Unit Cost</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-floating">
                                                <textarea class="form-control" id="notes" name="notes" style="height: 100px"></textarea>
                                                <label for="notes">Notes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Add Transaction</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Transactions History
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Item</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Unit Cost</th>
                                            <th>Reference</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime($row['transaction_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['item_code'] . ' - ' . $row['item_name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $row['transaction_type'] == 'purchase' ? 'success' : 
                                                        ($row['transaction_type'] == 'sale' ? 'primary' : 
                                                        ($row['transaction_type'] == 'adjustment' ? 'warning' : 
                                                        ($row['transaction_type'] == 'transfer' ? 'info' : 'secondary'))); 
                                                ?>">
                                                    <?php echo ucfirst($row['transaction_type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $row['quantity']; ?></td>
                                            <td><?php echo htmlspecialchars($row['from_location'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['to_location'] ?? '-'); ?></td>
                                            <td><?php echo $row['unit_cost'] ? '$' . number_format($row['unit_cost'], 2) : '-'; ?></td>
                                            <td><?php echo $row['reference_type'] ? htmlspecialchars($row['reference_type'] . ' #' . $row['reference_id']) : '-'; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editTransaction(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteTransaction(<?php echo $row['transaction_id']; ?>)">
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
                            <h5 class="modal-title">Edit Transaction</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="transaction_id" id="editTransactionId">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="editItemId" name="item_id" required>
                                            <option value="">Select Item</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?php echo $item['item_id']; ?>"><?php echo htmlspecialchars($item['item_code'] . ' - ' . $item['item_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="editItemId">Item</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="editTransactionType" name="transaction_type" required>
                                            <option value="">Select Type</option>
                                            <option value="purchase">Purchase</option>
                                            <option value="sale">Sale</option>
                                            <option value="adjustment">Adjustment</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="return">Return</option>
                                        </select>
                                        <label for="editTransactionType">Transaction Type</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input class="form-control" id="editQuantity" name="quantity" type="number" required />
                                        <label for="editQuantity">Quantity</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editReferenceId" name="reference_id" type="number" />
                                        <label for="editReferenceId">Reference ID</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editReferenceType" name="reference_type" type="text" />
                                        <label for="editReferenceType">Reference Type</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="editFromLocationId" name="from_location_id">
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="editFromLocationId">From Location</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="editToLocationId" name="to_location_id">
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="editToLocationId">To Location</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input class="form-control" id="editUnitCost" name="unit_cost" type="number" step="0.01" />
                                        <label for="editUnitCost">Unit Cost</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="editNotes" name="notes" style="height: 100px"></textarea>
                                        <label for="editNotes">Notes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Transaction</button>
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
                            <h5 class="modal-title">Delete Transaction</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="transaction_id" id="deleteTransactionId">
                            <p>Are you sure you want to delete this transaction?</p>
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
            function editTransaction(transaction) {
                document.getElementById('editTransactionId').value = transaction.transaction_id;
                document.getElementById('editItemId').value = transaction.item_id;
                document.getElementById('editTransactionType').value = transaction.transaction_type;
                document.getElementById('editQuantity').value = transaction.quantity;
                document.getElementById('editReferenceId').value = transaction.reference_id || '';
                document.getElementById('editReferenceType').value = transaction.reference_type || '';
                document.getElementById('editFromLocationId').value = transaction.from_location_id || '';
                document.getElementById('editToLocationId').value = transaction.to_location_id || '';
                document.getElementById('editUnitCost').value = transaction.unit_cost || '';
                document.getElementById('editNotes').value = transaction.notes || '';
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }

            function deleteTransaction(id) {
                document.getElementById('deleteTransactionId').value = id;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            }
        </script>
    </body>
</html>
