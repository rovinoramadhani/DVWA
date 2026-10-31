<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
	// Get input
	$id = $_REQUEST[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			if (!filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
				die('ID tidak valid.');
			}

			$query = "SELECT first_name, last_name FROM users WHERE user_id = ?;";
			$stmt  = mysqli_prepare($GLOBALS["___mysqli_ston"], $query);

			if ($stmt === false) {
				error_log(mysqli_error($GLOBALS["___mysqli_ston"]));
				die('Terjadi kesalahan pada server.');
			}

			mysqli_stmt_bind_param($stmt, "i", $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			while ($row = mysqli_fetch_assoc($result)) {
				$first = htmlspecialchars($row["first_name"], ENT_QUOTES, 'UTF-8');
				$last  = htmlspecialchars($row["last_name"],  ENT_QUOTES, 'UTF-8');

				$html .= "<pre>ID: " . htmlspecialchars($id) . "<br />First name: {$first}<br />Surname: {$last}</pre>";
			}

			mysqli_stmt_close($stmt);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			#$sqlite_db_connection = new SQLite3($_DVWA['SQLITE_DB']);
			#$sqlite_db_connection->enableExceptions(true);

			$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
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
					$last  = $row["last_name"];

					// Feedback for end user
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				echo "Error in fetch ".$sqlite_db->lastErrorMsg();
			}
			break;
	} 
}

?>
