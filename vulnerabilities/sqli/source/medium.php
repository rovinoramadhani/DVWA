<?php

if (isset($_POST['Submit'])) {
    // Get input
    $id = $_POST['id'];

    $id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id);

    switch ($_DVWA['SQLI_DB']) {
        case MYSQL:
            // Validasi input: pastikan $id adalah integer positif
            if (!filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                die('<pre>ID tidak valid.</pre>');
            }

            // Gunakan prepared statement dengan placeholder (?)
            $query = "SELECT first_name, last_name FROM users WHERE user_id = ?;";
            $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $query);


            if ($stmt === false) {
                // Jangan tampilkan error mentah ke user
                error_log(mysqli_error($GLOBALS["___mysqli_ston"]));
                die('<pre>Terjadi kesalahan pada server.</pre>');
            }

            // Bind parameter: "i" = integer (karena user_id bertipe angka)
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Get results
            while ($row = mysqli_fetch_assoc($result)) {
                // Display values
                $first = htmlspecialchars($row["first_name"], ENT_QUOTES, 'UTF-8');
                $last = htmlspecialchars($row["last_name"], ENT_QUOTES, 'UTF-8');

                // Feedback for end user
                $html .= "<pre>ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "<br />First name: {$first}<br />Surname: {$last}</pre>";
            }

            mysqli_stmt_close($stmt);
            break;
        case SQLITE:
            global $sqlite_db_connection;

            $query = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
            #print $query;
            try {
                $results = $sqlite_db_connection->query($query);
            } catch (Exception $e) {
                echo 'Caught exception: ' . $e->getMessage();
                exit();
            }

            if ($results) {
                while ($row = $results->fetchArray()) {
                    // Get values
                    $first = $row["first_name"];
                    $last = $row["last_name"];

                    // Feedback for end user
                    $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
                }
            } else {
                echo "Error in fetch " . $sqlite_db->lastErrorMsg();
            }
            break;
    }
}

// This is used later on in the index.php page
// Setting it here so we can close the database connection in here like in the rest of the source scripts
$query = "SELECT COUNT(*) FROM users;";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');
$number_of_rows = mysqli_fetch_row($result)[0];

mysqli_close($GLOBALS["___mysqli_ston"]);
?>
