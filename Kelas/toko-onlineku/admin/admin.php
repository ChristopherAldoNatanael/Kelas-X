<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s;
            z-index: 999;
            color: white;
            padding: 20px;
        }

        #sidebar.active {
            margin-left: -250px;
        }

        #content {
            width: calc(100% - 250px);
            margin-left: 250px;
            transition: all 0.3s;
        }

        #content.active {
            width: 100%;
            margin-left: 0;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #343a40;
            border-bottom: 1px solid #47484a;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 10px;
            font-size: 1.1em;
            display: block;
            color: #ffffff;
            text-decoration: none;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            color: #7386D5;
            background: rgba(255, 255, 255, 0.1);
        }

        #sidebar ul li.active>a {
            color: #007bff;
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar" class="bg-dark">
            <div class="sidebar-header">
                <h3><i class="bi bi-shop"></i> Toko Online</h3>
            </div>
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#dashboard" data-bs-toggle="tab"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li>
                    <a href="#products" data-bs-toggle="tab"><i class="bi bi-box-seam"></i> Product Management</a>
                </li>
                <li>
                    <a href="#users" data-bs-toggle="tab"><i class="bi bi-people"></i> User Management</a>
                </li>
                <li>
                    <a href="#orders" data-bs-toggle="tab"><i class="bi bi-cart-check"></i> Order Management</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="ms-auto">
                        <a href="#" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </div>
                </div>
            </nav>

            <div class="tab-content">
                <div class="tab-pane active" id="dashboard">
                    <h1>Admin Dashboard</h1>
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Products</h5>
                                    <p class="card-text display-4">154</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text display-4">2,345</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Orders</h5>
                                    <p class="card-text display-4">42</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Total Revenue</h5>
                                    <p class="card-text display-4">$54K</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="products">
                    <h1>Product Management</h1>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            Product List
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="bi bi-plus"></i> Add Product
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Product rows would be dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="users">
                    <h1>User Management</h1>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            User List
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-plus"></i> Add User
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- User rows would be dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="orders">
                    <h1>Order Management</h1>
                    <div class="card">
                        <div class="card-header">Order List</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Order rows would be dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select">
                                <option>Admin</option>
                                <option>Manager</option>
                                <option>Staff</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                content.classList.toggle('active');
            });

            // Tab switching without page reload
            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetTab = this.getAttribute('href');
                    const tabPanes = document.querySelectorAll('.tab-pane');
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    document.querySelector(targetTab).classList.add('active');

                    // Update active sidebar link
                    tabLinks.forEach(l => l.parentElement.classList.remove('active'));
                    this.parentElement.classList.add('active');
                });
            });
        });
    </script>
</body>

</html>