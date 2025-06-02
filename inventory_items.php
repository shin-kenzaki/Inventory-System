<?php
// filepath: c:\xampp\htdocs\Inventory-System\inventory_items.php
require_once 'db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $item_code = mysqli_real_escape_string($conn, $_POST['item_code']);
                $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $category_id = (int)$_POST['category_id'];
                $cost_price = (float)$_POST['cost_price'];
                $selling_price = (float)$_POST['selling_price'];
                $reorder_level = (int)$_POST['reorder_level'];
                $current_stock = (int)$_POST['current_stock'];
                
                $sql = "INSERT INTO inventory_items (item_code, item_name, description, category_id, cost_price, selling_price, reorder_level, current_stock) 
                        VALUES ('$item_code', '$item_name', '$description', $category_id, $cost_price, $selling_price, $reorder_level, $current_stock)";
                if (mysqli_query($conn, $sql)) {
                    $success = "Item added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['item_id'];
                $item_code = mysqli_real_escape_string($conn, $_POST['item_code']);
                $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $category_id = (int)$_POST['category_id'];
                $cost_price = (float)$_POST['cost_price'];
                $selling_price = (float)$_POST['selling_price'];
                $reorder_level = (int)$_POST['reorder_level'];
                $current_stock = (int)$_POST['current_stock'];
                
                $sql = "UPDATE inventory_items SET item_code='$item_code', item_name='$item_name', description='$description', 
                        category_id=$category_id, cost_price=$cost_price, selling_price=$selling_price, 
                        reorder_level=$reorder_level, current_stock=$current_stock WHERE item_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Item updated successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['item_id'];
                $sql = "DELETE FROM inventory_items WHERE item_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Item deleted successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;
        }
    }
}

// Fetch inventory items with category names
$sql = "SELECT i.*, c.category_name FROM inventory_items i 
        LEFT JOIN categories c ON i.category_id = c.category_id 
        ORDER BY i.item_name";
$result = mysqli_query($conn, $sql);

// Fetch categories for dropdown
$categories_sql = "SELECT * FROM categories ORDER BY category_name";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = [];
while ($cat = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $cat;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Inventory Items - Inventory System</title>
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
                        <li><a class="dropdown-item" href="#!">Logout</a></li>
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
                            <a class="nav-link" href="index.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Inventory Management</div>
                            <a class="nav-link active" href="inventory_items.php">
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
                        <h1 class="mt-4">Inventory Items</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Inventory Items</li>
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
                                Add New Item
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="itemCode" name="item_code" type="text" required />
                                                <label for="itemCode">Item Code</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" id="itemName" name="item_name" type="text" required />
                                                <label for="itemName">Item Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select" id="categoryId" name="category_id">
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="categoryId">Category</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" id="description" name="description" style="height: 100px"></textarea>
                                                <label for="description">Description</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="costPrice" name="cost_price" type="number" step="0.01" required />
                                                <label for="costPrice">Cost Price</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="sellingPrice" name="selling_price" type="number" step="0.01" required />
                                                <label for="sellingPrice">Selling Price</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="reorderLevel" name="reorder_level" type="number" value="10" required />
                                                <label for="reorderLevel">Reorder Level</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="currentStock" name="current_stock" type="number" value="0" required />
                                                <label for="currentStock">Current Stock</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Add Item</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Inventory Items
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Cost Price</th>
                                            <th>Selling Price</th>
                                            <th>Current Stock</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['item_code']); ?></td>
                                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'No Category'); ?></td>
                                            <td>$<?php echo number_format($row['cost_price'], 2); ?></td>
                                            <td>$<?php echo number_format($row['selling_price'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php echo $row['current_stock'] <= $row['reorder_level'] ? 'bg-warning' : 'bg-success'; ?>">
                                                    <?php echo $row['current_stock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editItem(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $row['item_id']; ?>)">
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
                            <h5 class="modal-title">Edit Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="item_id" id="editItemId">
                            <!-- Form fields similar to add form but with edit prefix -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input class="form-control" id="editItemCode" name="item_code" type="text" required />
                                        <label for="editItemCode">Item Code</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input class="form-control" id="editItemName" name="item_name" type="text" required />
                                        <label for="editItemName">Item Name</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <select class="form-select" id="editCategoryId" name="category_id">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="editCategoryId">Category</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="editDescription" name="description" style="height: 100px"></textarea>
                                        <label for="editDescription">Description</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editCostPrice" name="cost_price" type="number" step="0.01" required />
                                        <label for="editCostPrice">Cost Price</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editSellingPrice" name="selling_price" type="number" step="0.01" required />
                                        <label for="editSellingPrice">Selling Price</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editReorderLevel" name="reorder_level" type="number" required />
                                        <label for="editReorderLevel">Reorder Level</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control" id="editCurrentStock" name="current_stock" type="number" required />
                                        <label for="editCurrentStock">Current Stock</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Item</button>
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
                            <h5 class="modal-title">Delete Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="item_id" id="deleteItemId">
                            <p>Are you sure you want to delete this item?</p>
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
            function editItem(item) {
                document.getElementById('editItemId').value = item.item_id;
                document.getElementById('editItemCode').value = item.item_code;
                document.getElementById('editItemName').value = item.item_name;
                document.getElementById('editCategoryId').value = item.category_id || '';
                document.getElementById('editDescription').value = item.description || '';
                document.getElementById('editCostPrice').value = item.cost_price;
                document.getElementById('editSellingPrice').value = item.selling_price;
                document.getElementById('editReorderLevel').value = item.reorder_level;
                document.getElementById('editCurrentStock').value = item.current_stock;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }

            function deleteItem(id) {
                document.getElementById('deleteItemId').value = id;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            }
        </script>
    </body>
</html>