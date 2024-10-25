<?php

$dsn = "mysql:host=localhost;dbname=shop_product";
$dbusername = "root";
$dbpassword = "";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Array of products (CDs and books)
    $products = [
        ["Thriller", "Michael", "Jackson", 12.99, "CD"],
        ["Back in Black", "AC/DC", "", 10.99, "CD"],
        ["The Dark Side of the Moon", "Pink", "Floyd", 15.50, "CD"],
        ["Rumours", "Fleetwood", "Mac", 9.99, "CD"],
        ["Abbey Road", "The", "Beatles", 14.99, "CD"],
        ["1984", "George", "Orwell", 7.99, "Book"],
        ["To Kill a Mockingbird", "Harper", "Lee", 8.49, "Book"],
        ["The Great Gatsby", "F. Scott", "Fitzgerald", 10.99, "Book"],
        ["Moby Dick", "Herman", "Melville", 11.50, "Book"],
        ["Pride and Prejudice", "Jane", "Austen", 6.99, "Book"],
    ];

    // Prepare the SQL statement with placeholders
    $sql = "INSERT INTO products (title, producer_name, producer_surname, price, product_type) 
            VALUES (:title, :producer_name, :producer_surname, :price, :product_type)";
    $stmt = $pdo->prepare($sql);

    // Insert each product into the database
    foreach ($products as $product) {
        // Bind parameters and execute the statement for each product
        $stmt->bindParam(':title', $product[0]);
        $stmt->bindParam(':producer_name', $product[1]);
        $stmt->bindParam(':producer_surname', $product[2]);
        $stmt->bindParam(':price', $product[3]);
        $stmt->bindParam(':product_type', $product[4]);

        // Execute the statement
        $stmt->execute();
    }

    echo "All products inserted successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
