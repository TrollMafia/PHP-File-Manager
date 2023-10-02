<?php
if (isset($_GET['folder_name']) && isset($_GET['fileid'])) {
    $folderName = $_GET['folder_name'];
    $fileId = $_GET['fileid'];

    // Your database connection setup here
    $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');

    // Retrieve file information from the database
    $query = $conn->prepare("SELECT * FROM upload WHERE folder_id = (SELECT id FROM folders WHERE name = :folder_name) AND id = :file_id");
    $query->bindParam(':folder_name', $folderName);
    $query->bindParam(':file_id', $fileId);
    $query->execute();
    $file = $query->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $folderPath = 'files/' . $folderName . '/';
        $fileName = $file['name'];
        $file_path = $folderPath . $fileName;

        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                // Remove file from database
                $deleteQuery = $conn->prepare("DELETE FROM upload WHERE folder_id = (SELECT id FROM folders WHERE name = :folder_name) AND id = :file_id");
                $deleteQuery->bindParam(':folder_name', $folderName);
                $deleteQuery->bindParam(':file_id', $fileId);
                if ($deleteQuery->execute()) {
                    // Redirect back to the main page and show an alert using JavaScript
                    echo '<script>alert("File deleted successfully."); window.location.href = "index.php";</script>';
                } else {
                    echo 'Error removing the file from the database.';
                }
            } else {
                echo 'Error removing the file.';
            }
        } else {
            echo 'File not found.';
        }
    } else {
        echo 'File not found in the database.';
    }
} else {
    echo 'Invalid request.';
}

?>
