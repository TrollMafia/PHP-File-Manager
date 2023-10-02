<?php
if (isset($_GET['folder_id']) && isset($_GET['filename'])) {
    $folderId = $_GET['folder_id'];
    $fileName = $_GET['filename'];

    // Your database connection setup here
    $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');

    // Retrieve folder information from the database
    $query = $conn->prepare("SELECT * FROM folders WHERE id = :folder_id");
    $query->bindParam(':folder_id', $folderId);
    $query->execute();
    $folder = $query->fetch(PDO::FETCH_ASSOC);

    if ($folder) {
        $folderPath = 'files/' . $folder['name'] . '/'; // Use folder name instead of ID
        $file_path = $folderPath . $fileName;

        if (file_exists($file_path)) {
            // Set appropriate headers for download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($file_path));

            // Output the file content
            readfile($file_path);
            exit;
        } else {
            echo 'File not found.';
        }
    } else {
        echo 'Folder not found.';
    }
} else {
    echo 'Invalid request.';
}

?>
