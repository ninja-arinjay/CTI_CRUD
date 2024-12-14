<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: error.php");
    exit;
}

require_once "config.php";
// Include PhpSpreadsheet autoloader
require_once 'vendor/autoload.php'; 

// Use PhpSpreadsheet classes
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// Initialize variables to track added, updated, and error records
$countAdded = $countUpdated = $countError = 0;

// Initialize a message variable for results
$message = "";

// Check if the form is submitted and a file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Check if there are no errors during the file upload
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {

        // Validate the file type
        $allowedFileTypes = [
            'application/vnd.ms-excel',
            'text/xls',
            'text/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        // Get the file MIME type
        $fileMimeType = $_FILES['file']['type'];

        // Check if the file is an Excel file based on MIME type
        if (in_array($fileMimeType, $allowedFileTypes)) {

            // Move the uploaded file to a target directory
            $targetPath = 'uploads/' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

            // Create a new Reader object
            $reader = new Xlsx();

            try {
                // Load the uploaded Excel file
                $spreadsheet = $reader->load($targetPath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                // Process each row of the Excel file
                foreach ($rows as $index => $row) {
                    // Skip the header row
                    if ($index === 0) {
                        continue;
                    }

                    // Extract fields from the Excel row
                    $serialno = $row[0] ?? null;
                    $productname = $row[1] ?? null;
                    $product_category = $row[2] ?? null;
                    $product_desc = $row[3] ?? null;

                    // Check for missing required fields
                    if (empty($serialno) || empty($productname) || empty($product_category) || empty($product_desc)) {
                        $countError++;
                        continue;
                    }

                    try {
                        // Check if the product already exists based on the serial number
                        $sql = "SELECT * FROM products WHERE serialno = ?";
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            // Bind the serial number to the prepared statement as a parameter
                            mysqli_stmt_bind_param($stmt, "s", $param_serialno);

                            // Set the parameter
                            $param_serialno = $serialno;

                            // Execute the prepared statement
                            mysqli_stmt_execute($stmt);

                            // Store the result to check the number of rows
                            mysqli_stmt_store_result($stmt);

                            if (mysqli_stmt_num_rows($stmt) > 0) {
                                // Product exists, prepare an UPDATE query
                                $query = "UPDATE products 
                                          SET productname = ?, product_category = ?, product_desc = ? 
                                          WHERE serialno = ?";
                                if ($update_stmt = mysqli_prepare($link, $query)) {
                                    // Bind variables to the prepared statement as parameters
                                    mysqli_stmt_bind_param($update_stmt, "ssss", $param_name, $param_category, $param_description, $param_serialno);

                                    // Set parameters
                                    $param_name = $productname;
                                    $param_category = $product_category;
                                    $param_description = $product_desc;
                                    $param_serialno = $serialno;

                                    // Execute the statement
                                    if (mysqli_stmt_execute($update_stmt)) {
                                        $countUpdated++;
                                    } else {
                                        $countError++;
                                    }

                                    // Close the update statement
                                    mysqli_stmt_close($update_stmt);
                                }
                            } else {
                                // Product does not exist, prepare an INSERT query
                                $query = "INSERT INTO products (productname, product_category, product_desc, serialno) 
                                          VALUES (?, ?, ?, ?)";
                                if ($insert_stmt = mysqli_prepare($link, $query)) {
                                    // Bind variables to the prepared statement as parameters
                                    mysqli_stmt_bind_param($insert_stmt, "ssss", $param_name, $param_category, $param_description, $param_serialno);

                                    // Set parameters
                                    $param_name = $productname;
                                    $param_category = $product_category;
                                    $param_description = $product_desc;
                                    $param_serialno = $serialno;

                                    // Execute the statement
                                    if (mysqli_stmt_execute($insert_stmt)) {
                                        $countAdded++;
                                    } else {
                                        $countError++;
                                    }

                                    // Close the insert statement
                                    mysqli_stmt_close($insert_stmt);
                                }
                            }

                            // Close the select statement
                            mysqli_stmt_close($stmt);
                        }
                    } catch (Exception $e) {
                        $countError++;
                    }
                }

                // Close the connection
                mysqli_close($link);

                // Prepare the import results message
                $message = "Import Results: $countAdded added, $countUpdated updated, $countError errors";

            } catch (Exception $e) {
                $message = "Error processing the Excel file. Please try again.";
            }

        } else {
            // Invalid file type
            $message = "Invalid file type. Please upload an Excel file.";
        }
    } else {
        // Error during file upload
        $message = "Error uploading the file. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php if (!empty($message)) { ?>
            <div class="alert alert-info">
                <p><?php echo $message; ?></p>
                <form action="home.php" method="get">
                    <button type="submit" class="btn btn-primary">OK</button>
                </form>
            </div>
        <?php } else { ?>
            <h2>Import Products from Excel</h2>
            <form action="import.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Upload Excel File:</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" name="import">Import</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
