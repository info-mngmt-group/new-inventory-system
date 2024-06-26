<?php
include 'topnav.php';
include 'cashier_nav.php';
include_once 'includes/config.php';
include_once 'addprodprocess_cashier.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch supplier brands and default categories
$suppliers_sql = "SELECT sup_brand FROM suppliers";
$suppliers_result = $conn->query($suppliers_sql);

$categories_sql = "SELECT cat_name FROM categories";
$categories_result = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet" href="css/addproduct.css">
<title>Add Product</title>

<script>
    function validateForm() {
        var productName = document.forms["productForm"]["product_name"].value.trim();
        var size = document.forms["productForm"]["size"].value.trim();
        var quantity = document.forms["productForm"]["quantity"].value.trim();

        var productNameRegex = /^[a-zA-Z0-9\s]+$/;
        var sizeRegex = /^\d{1,3}$/;
        var quantityRegex = /^\d{1,3}$/;

        if (!productNameRegex.test(productName)) {
            alert("Product Name can only contain letters and numbers.");
            return false;
        }
        if (!sizeRegex.test(size)) {
            alert("Size must be an integer with a maximum of 3 digits.");
            return false;
        }
        if (!quantityRegex.test(quantity)) {
            alert("Quantity must be an integer with a maximum of 3 digits.");
            return false;
        }

        return true;
    }
</script>
</head>

<body>
<div class="container">
    <form name="productForm" action="cashier_addprod.php" method="post" onsubmit="return validateForm()">
    <div class="button_title">
    <h4>Add Product</h4>
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
    margin-left: 10px;" onclick="window.location.href='cashier_inventory.php'">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
        <div class="row1">
            <div class="input-box">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" placeholder="Enter product name" required>
            </div>
            <div class="input-box">
                <label for="size">Size</label>
                <input type="text" id="size" name="size" placeholder="Enter size" required>
            </div>
        </div>
        <div class="row2">
            <div class="input-box">
                <label for="quantity">Quantity</label>
                <input type="text" id="quantity" name="quantity" placeholder="Enter Quantity" required>
            </div>
            <div class="input-box">
                <label for="category">Category</label>
                <div class="column">
                    <div class="select-box">
                        <select id="category" name="category" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php
                            if ($categories_result->num_rows > 0) {
                                while($row = $categories_result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['cat_name']) . "'>" . htmlspecialchars($row['cat_name']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No categories available</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row3">
            <div class="input-box">
                <label for="brand_name">Brand Name</label>
                <div class="column">
                    <div class="select-box">
                        <select id="brand_name" name="brand_name" required>
                            <option value="" disabled selected>Select a brand</option>
                            <?php
                            if ($suppliers_result->num_rows > 0) {
                                while($row = $suppliers_result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['sup_brand']) . "'>" . htmlspecialchars($row['sup_brand']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No brands available</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
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
</body>
</html>

<?php
$conn->close();
?>
