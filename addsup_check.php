<?php
include 'checker_nav.php'; 
include 'topnav.php';
include 'includes/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sup_name = ucwords(strtolower($_POST['sup_name']));
    $sup_country = ucwords(strtolower($_POST['sup_country']));
    $sup_num = $_POST['sup_num'];
    $sup_brand = ucwords(strtolower($_POST['sup_brand']));

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the supplier already exists
    $check_stmt = $conn->prepare("SELECT * FROM suppliers WHERE sup_name = ? AND sup_country = ? AND sup_num = ? AND sup_brand = ?");
    $check_stmt->bind_param("ssss", $sup_name, $sup_country, $sup_num, $sup_brand);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        // Supplier already exists, redirect back to addsupplier.php with a message
        echo "<script>
                alert('Supplier already exists in the database!');
                window.location.href = 'addsupplier.php?message=exists';
              </script>";
        exit();
    } else {
        // Supplier doesn't exist, proceed with insertion
        $insert_stmt = $conn->prepare("INSERT INTO suppliers (sup_name, sup_country, sup_num, sup_brand) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $sup_name, $sup_country, $sup_num, $sup_brand);

        if ($insert_stmt->execute()) {
            echo "<script>
            alert('Supplier successfully added to the database!');
            window.location.href = 'suppcheck.php?message=exists';
          </script>";
            
        } else {
            $message = "Error adding supplier: " . $conn->error;
            // Log the error instead of echoing it
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/addsupplier.css">
    <title>Add New Supplier</title>
</head>

<body>
<div class="container">
    <form name="addSupplierForm" class="form" method="POST" onsubmit="return validateForm()" action="addsup_check.php">
    <div class="button_title">
    <h4>Add New Supplier</h4>
    <button type="button" class="custom-close-btn" style=" width: 40px;
    height: 40px;
    background: #f2af4a;
    border: none;
    outline: none;
    color: #FFFFFF;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-left: 10px;" onclick="window.location.href='suppcheck.php'">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
        <div class="input-box">
            <label>Supplier Name</label>
            <input type="text" name="sup_name" placeholder="Enter supplier name" required>
        </div>
        <div class="input-box">
            <label>Country</label>
            <input type="text" name="sup_country" placeholder="Enter country" required>
        </div>
        <div class="input-box">
            <label>Phone Number</label>
            <input type="text" name="sup_num" placeholder="Enter phone number" required>
        </div>
        <div class="input-box">
            <label>Brand</label>
            <input type="text" name="sup_brand" placeholder="Enter brand" required>
        </div>
        <button type="submit" style=" width: 20%;
    padding: 5px;
    background: #f2af4a;
    border: none;
    outline: none;
    color: #FFFFFF;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    border-radius: 10px;
    margin-left: 80%;">Submit</button>
    </form>
</div>

<script>
    function validateForm() {
        var supName = document.forms["addSupplierForm"]["sup_name"].value;
        var supCountry = document.forms["addSupplierForm"]["sup_country"].value;
        var supNum = document.forms["addSupplierForm"]["sup_num"].value;
        var supBrand = document.forms["addSupplierForm"]["sup_brand"].value;

        if (supName.match(/\d/)) {
            alert("Supplier name must not contain any digits.");
            return false;
        }

        if (supCountry.match(/\d/)) {
            alert("Country must not contain any digits.");
            return false;
        }

        if (!supNum.match(/^\d{11}$/)) {
            alert("Phone number must contain exactly 11 digits and no other characters.");
            return false;
        }

        return true;
    }
</script>
</body>

</html>
