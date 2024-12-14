<?php
    session_start();
    if (!isset($_SESSION['logged_in'])) {
        header("Location: error.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .wrapper{
            width: 800px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 120px;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Product Management</h2>
                        <div class="pull-right" style="display: flex; gap: 10px;">
                            <a href="create.php" class="btn btn-success"><i class="fa fa-plus"></i> Add New Product</a>
                            <a href="import.php" class="btn btn-primary"><i class="fa fa-upload"></i> Import Excel</a>
                            <a href="logout.php" class="btn btn-danger"><i class="fa fa-sign-out"></i> Logout</a>
                        </div>
                    </div>
                    <form method="get" class="mb-4">
                        <div class="form-row">
                            <div class="col">
                                <input type="text" name="productname" class="form-control" placeholder="Search by Product Name" 
                                value="<?php echo isset($_GET['productname']) ? $_GET['productname'] : ''; ?>">
                            </div>
                            <div class="col">
                                <input type="text" name="product_category" class="form-control" placeholder="Search by Product Category" 
                                value="<?php echo isset($_GET['product_category']) ? $_GET['product_category'] : ''; ?>">
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="home.php" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <?php
                    // Include config file
                    require_once "config.php";
                    require_once "helper.php";

                    // Initialize search variables
                    $search_product_name = isset($_GET['productname']) ? trim($_GET['productname']) : '';
                    $search_product_category = isset($_GET['product_category']) ? trim($_GET['product_category']) : '';

                    // Prepare the base SQL query
                    $sql = "SELECT * FROM products WHERE 1=1";

                    // Append conditions based on search inputs
                    if (!empty($search_product_name)) {
                        $sql .= " AND productname LIKE ?";
                    }
                    if (!empty($search_product_category)) {
                        $sql .= " AND product_category LIKE ?";
                    }

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind parameters dynamically based on search inputs
                        $types = '';
                        $params = [];

                        if (!empty($search_product_name)) {
                            $types .= 's';
                            $params[] = '%' . $search_product_name . '%';
                        }
                        if (!empty($search_product_category)) {
                            $types .= 's';
                            $params[] = '%' . $search_product_category . '%';
                        }

                        if (!empty($types)) {
                            mysqli_stmt_bind_param($stmt, $types, ...$params);
                        }

                        // Execute the statement
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($result) > 0) {
                                echo '<table class="table table-bordered table-striped">';
                                    echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>Serial No.</th>";
                                            echo "<th>Name</th>";
                                            echo "<th>Category</th>";
                                            echo "<th>Description</th>";
                                            echo "<th>Action</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                        echo "<tr>";
                                            echo "<td>" . $row['serialno'] . "</td>";
                                            echo "<td>" . $row['productname'] . "</td>";
                                            echo "<td>" . $row['product_category'] . "</td>";
                                            echo "<td>" . $row['product_desc'] . "</td>";
                                            echo "<td>";
                                                echo '<a href="read.php?id=' . encrypt_url($row['serialno']) . '" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                                echo '<a href="update.php?id=' . encrypt_url($row['serialno']) . '" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                                echo '<a href="delete.php?id=' . encrypt_url($row['serialno']) . '" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody>";
                                echo "</table>";
                                // Free result set
                                mysqli_free_result($result);
                            } else {
                                echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                            }
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                        // Close statement
                        mysqli_stmt_close($stmt);
                    }

                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
