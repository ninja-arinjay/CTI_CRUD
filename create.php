<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: error.php");
    exit;
}
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$serialno = $name = $category = $description = "";
$serialno_err = $name_err = $category_err = $description_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate serial no.
    $input_serialno = trim($_POST["serialno"]);
    if (empty($input_serialno)) {
        $serialno_err = "Please enter a Serial No.";
    } else {
        // Check if the serial number is unique
        $sql = "SELECT serialno FROM products WHERE serialno = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind the serial number to the prepared statement
            mysqli_stmt_bind_param($stmt, "s", $param_serialno);
            
            // Set the parameter
            $param_serialno = $input_serialno;
            
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Store the result
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $serialno_err = "This Serial No. is already taken.";
                } else {
                    $serialno = $input_serialno;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }


    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    }else{
        $name = $input_name;
    }
    
    // Validate category
    $input_category = trim($_POST["category"]);
    if(empty($input_category)){
        $category_err = "Please enter a category.";     
    } else{
        $category = $input_category;
    }
    
    // Validate description
    $input_description = trim($_POST["description"]);
    if(empty($input_description)){
        $description_err = "Please enter the description.";     
    }else{
        $description = $input_description;
    }
    
    // Check input errors before inserting in database
    if(empty($serialno_err) && empty($name_err) && empty($category_err) && empty($description_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO products (productname, product_category, product_desc, serialno) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_category, $param_description, $param_serialno);
            
            // Set parameters
            $param_name = $name;
            $param_category = $category;
            $param_description = $description;
            $param_serialno = $serialno;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: home.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add product record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Serial No.</label>
                            <input type="number" name="serialno" class="form-control <?php echo (!empty($serialno_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $serialno; ?>">
                            <span class="invalid-feedback"><?php echo $serialno_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $category; ?>">
                            <span class="invalid-feedback"><?php echo $category_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                            <span class="invalid-feedback"><?php echo $description_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="home.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
