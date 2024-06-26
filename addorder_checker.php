<?php
include 'checker_nav.php';
include 'topnav.php';
include 'includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    
}

if (!isset($_SESSION['username'])) {
    header('location: index.php');
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch inventory data
$inventory_sql = "SELECT inventory_id, product_name FROM inventory";
$inventory_result = $conn->query($inventory_sql);

// Fetch categories
$categories_sql = "SELECT cat_name FROM categories";
$categories_result = $conn->query($categories_sql);
if (!$categories_result) {
    die("Query failed: " . $conn->error);
}

// Fetch suppliers
$suppliers_sql = "SELECT sup_id, sup_brand FROM suppliers";
$suppliers_result = $conn->query($suppliers_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $cat_id = $_POST['cat_id'];
    $size = $_POST['size'];
    $price = $_POST['price']; // Price directly from user input
    $brand_name = $_POST['brand_name'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];
    $staff = $_SESSION['name'];

    $insert_sql = "INSERT INTO `order` (product, brand, category, size, price, quantity, staff, order_date) 
                   VALUES ('$product_id', '$brand_name', '$cat_id', '$size', '$price', '$quantity', '$staff', '$order_date')";

    if ($conn->query($insert_sql) === TRUE) {
        echo "<script>alert('Order successfully added');</script>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'ordcheck.php';
                }, 1000); // 1000 milliseconds delay
              </script>";
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/addorder.css">
    <title>Add New Stock-Order</title>
    <script>
        function validateForm() {
            var size = document.forms["orderForm"]["size"].value;
            var price = document.forms["orderForm"]["price"].value;
            var quantity = document.forms["orderForm"]["quantity"].value;
            if (isNaN(size) || size <= 0) {
                alert("Size must be a positive number.");
                return false;
            }
            if (isNaN(price) || price <= 0) {
                alert("Price must be a positive number.");
                return false;
            }
            if (isNaN(quantity) || quantity <= 0) {
                alert("Quantity must be a positive number.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <form name="orderForm" action="addorder_checker.php" method="post" class="form" onsubmit="return validateForm()">
        <div class="button_title">
    <h4>New Stock Order</h4>
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
    margin-left: 10px;" onclick="window.location.href='ordcheck.php'">
        <i class="fa-solid fa-xmark"></i>
    </button>
    </div>
            <div class="row1">
                <div class="input-box">
                    <label>Product</label>
                    <div class="column">
                    <div class="select-box">
                    <select name="product_id" required>
                        <option value="" disabled selected>Select Product</option>
                        <?php
                        if ($inventory_result->num_rows > 0) {
                            while($row = $inventory_result->fetch_assoc()) {
                                echo "<option value='" . $row['inventory_id'] . "'>" . htmlspecialchars($row['product_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                </div>
                </div>
                <div class="input-box">
                    <label>Category</label>
                    <div class="column">
                    <div class="select-box">
                    <select name="cat_id" required>
                        <option value="" disabled selected>Select Category</option>
                        <?php
                        if ($categories_result->num_rows > 0) {
                            while($row = $categories_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['cat_name']) . "'>" . htmlspecialchars($row['cat_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            </div>
            </div>
            <div class="row2">
                <div class="input-box">
                    <label>Size</label>
                    <input type="number" name="size" placeholder="Enter Size" required>
                </div>
                <div class="input-box">
                    <label>Brand Name</label>
                    <div class="column">
                    <div class="select-box">
                    <select name="brand_name" required>
                        <option value="" disabled selected>Select Brand</option>
                        <?php
                        if ($suppliers_result->num_rows > 0) {
                            while($row = $suppliers_result->fetch_assoc()) {
                                echo "<option value='" . $row['sup_id'] . "'>" . htmlspecialchars($row['sup_brand']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            </div>
            </div>
            <div class="row3">
                <div class="input-box">
                    <label>Price</label>
                    <input type="number" name="price" placeholder="Enter Price" required>
                </div>
                <div class="input-box">
                    <label>Quantity</label>
                    <input type="number" name="quantity" placeholder="Enter Quantity" required>
                </div>
                 <div class="input-box">
                    <label>Order Date</label>
                    <input type="date" name="order_date" required>
                </div>
            </div>
            <button type="submit" name="submit"style=" width: 20%;
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
        // Open .dropdown-container by default
        document.querySelector(".dropdown-container").style.display = "block";
    </script>
</body>
</html>

 