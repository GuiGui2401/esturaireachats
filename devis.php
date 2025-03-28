<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish connection to your database
    $servername = "your_servername";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database_name";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $details = $_POST["details"];
    $quantity = $_POST["quantite"];
    $delivery = $_POST["delivery"];
    $description = $_POST["description"];
    $addressType = $_POST["AddressType"];
    $nationality = $_POST["Nationality"];
    $state = $_POST["State"];
    $district = $_POST["District"];
    $blockNumber = $_POST["BlockNumber"];
    $wardNumber = $_POST["WardNumber"];

    // SQL query to insert data into 'devis' table
    $sql = "INSERT INTO devis (name, email, phone, details, quantity, delivery, description, addressType, nationality, state, district, blockNumber, wardNumber)
    VALUES ('$name', '$email', '$phone', '$details', '$quantity', '$delivery', '$description', '$addressType', '$nationality', '$state', '$district', '$blockNumber', '$wardNumber')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
