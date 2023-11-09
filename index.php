<?php

echo '<link rel="stylesheet" type="text/css" href="style.css">';

// skapa anslutning till databasen
function getConnection(){
$host = "localhost";
$port = 3306;
$database = "databas1";
$username = "root";
$password = "";

// create connection to database
$connection = new mysqli($host, $username, $password, $database, $port);
return $connection;

// control if connection succeded
if ($connection->connect_error) {
    die("Kunde inte ansluta till databasen: " . $connection->connect_error);
}

}

// Funktion för att hämta vilka produkter en kund har köpt (baserat på kundens id).
function getProductsForCustomer($connection){
    $customer_id = 6; 
    $customer_name = "Sven Månsson"; 

    // Hämta id (order-ID:er) för ordrar (i orders-tabellen) för specifika kunden
    $query12= "SELECT orders_id FROM orders WHERE customer_id = $customer_id;";
    $result = $connection->query($query12);

    if ($result === false) {
        die("Fel vid hämtning av order-ID: " . $connection->error);
    } else {
        echo "Framgångsrik hämtning av orders_id från orders-tabellen!" . "<br>";
    }

    // Varje rad i resultatet representeras av en associerad array
    while ($row = $result->fetch_assoc()) {
        // hämta order-ID:et (orders_id) från varje rad och lägg till det i $order_ids-arrayen.
        $order_ids[] = $row['orders_id'];
    }
    
    // alla order_id:s för kunden är nu hämtade

    // order_ids = array med hämtade order_id från order_item-tabellen. 
    // loopar igenom varje order_id
    // bearbeta varje order separat.
    foreach ($order_ids as $order_id) {

        // hämta info om produkterna i order_items-tabellen
        // hämta product_id och antalet (från order_item-tabellen) (för kundens order) genom orderns id.
        $query13 = "SELECT product_id, quantity FROM order_item WHERE order_id = $order_id";
        $result = $connection->query($query13);

    if ($result === false) {
            die("Fel vid hämtning av produkt-id från order_item-tabellen." . $connection->error);
        } else {
            echo "Framgångsrik hämtning av produkt-id från order_item-tabellen!<br>";
            echo "Hämtar produkter för order ID: $order_id<br><br>";

        // iterera genom resultatuppsättningen som returneras
        while ($row = $result->fetch_assoc()) {
            // hämta produkt-ID och kvantitet för varje rad
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
        
        // hämta produktens namn (genom produkt-ID) i produkt-tabellen.
        $query14 = "SELECT name FROM products WHERE product_id = $product_id";
        $product_result = $connection->query($query14);
        
            if ($product_result->num_rows > 0) {
                $product_row = $product_result->fetch_assoc();
                $product_name = $product_row['name'];

                // Generera HTML för varje produkt
                echo "<div class='show-order'>";
                echo "<h2>Produkter som är köpta av kunden: $customer_name.</h2>";
                echo "<h3>Produktnamn: $product_name</h3>";
                echo "<h4>Antal: $quantity</h4>";
                echo "</div>";
            }
        }
    }
}
}

// skapa databas-anslutning
$connection = getConnection();  

// Anropa funktionen med databasanslutningen
getProductsForCustomer($connection);   




// check connection
if($connection->connect_error != null){
    die("Anslutningen misslyckades: " . $connection->connect_error);
}else{
    echo "Anslutningen lyckades.";
}


// stäng anslutningen till databasen
$connection->close();
    
?>