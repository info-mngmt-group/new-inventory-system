<?php
include 'nav.php';
include 'includes/config.php';
include 'topnav.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    $brand_name = $_POST['brand_name'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];
    $staff = $_SESSION['name'];
    
    // Set the status to 'Pending'
    $status = 'Pending';

    $stmt = $conn->prepare("INSERT INTO `order` (product, brand, category, size, quantity, staff, order_date, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssisss", $product_id, $brand_name, $cat_id, $size, $quantity, $staff, $order_date, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Order successfully added');</script>";
        echo "<script>window.location.href = 'order.php';</script>";
        exit();
    } else {
        echo "Error executing query: " . $stmt->error;
    }

    $stmt->close();
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
    <title>Add New Order</title>
    <script>
        function validateForm() {
            var size = document.forms["orderForm"]["size"].value;
            var quantity = document.forms["orderForm"]["quantity"].value;
            if (isNaN(quantity) || quantity <= 0) {
                alert("Quantity must be a positive number.");
                return false;
            }
            return true;
        }

        function fetchSize(productId) {
            var sizeDropdown = document.forms["orderForm"]["size"];
            sizeDropdown.innerHTML = ""; // Clear previous options

            if (productId === "") {
                var option = document.createElement("option");
                option.text = "Select Size";
                option.disabled = true;
                option.selected = true;
                sizeDropdown.add(option);
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_size.php?product_id=" + productId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var sizes = JSON.parse(xhr.responseText);
                    if (sizes.length > 0) {
                        sizes.forEach(function(size) {
                            var option = document.createElement("option");
                            option.value = size;
                            option.text = size;
                            sizeDropdown.add(option);
                        });
                    } else {
                        var option = document.createElement("option");
                        option.text = "No sizes available";
                        option.disabled = true;
                        sizeDropdown.add(option);
                    }
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <div class="container">
        <form name="orderForm" action="addorder.php" method="post" class="form" onsubmit="return validateForm()">
            <h4>Add New Order</h4>
            <div class="row1">
                <div class="input-box">
                    <label>Product</label>
                    <select name="product_id" required onchange="fetchSize(this.value)">
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
                <div class="input-box">
                    <label>Category</label>
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
            <div class="row2">
                <div class="input-box">
                    <label>Size</label>
                    <select name="size" required>
                        <option value="" disabled selected>Select Size</option>
                    </select>
                </div>
                <div class="input-box">
                    <label>Brand Name</label>
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
            <div class="row3">
                <input type="hidden" name="status" value="Pending">
                <div class="input-box">
                    <label>Quantity</label>
                    <input type="number" name="quantity" placeholder="Enter Quantity" required>
                </div>
                <div class="input-box">
                    <label>Order Date</label>
                    <input type="date" name="order_date" required>
                </div>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        // Open .dropdown-container by default
        document.querySelector(".dropdown-container").style.display = "block";
    </script>
</body>
</html>
