<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName = $_POST['folder_name']; // Get the folder name
    $folderPath = 'files/' . $folderName . '/'; // Use folder name for path
    
    $uploadedFile = $_FILES['folder_file'];
    $uploadedFileName = $uploadedFile['name'];
    $uploadedFilePath = $folderPath . $uploadedFileName;
    
    if (move_uploaded_file($uploadedFile['tmp_name'], $uploadedFilePath)) {
        $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');
        
        $currentDatetime = date('Y-m-d H:i:s'); // Get the current datetime
        
        // Retrieve the folder ID based on the provided folder name
        $query = $conn->prepare("SELECT id FROM folders WHERE name = :folder_name");
        $query->bindParam(':folder_name', $folderName);
        $query->execute();
        $folder = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($folder) {
            $folderId = $folder['id']; // Get the folder ID
            
            $insertQuery = $conn->prepare("INSERT INTO upload (name, folder_id, date) VALUES (:name, :folder_id, :upload_date)");
            $insertQuery->bindParam(':name', $uploadedFileName);
            $insertQuery->bindParam(':folder_id', $folderId); // Use the fetched folder ID
            $insertQuery->bindParam(':upload_date', $currentDatetime);
            
            if ($insertQuery->execute()) {
                header('Location: index.php'); // Redirect back to the main page
            } else {
                echo 'Error adding file to database.';
            }
        } else {
            echo 'Folder not found.';
        }
    } else {
        echo 'Error uploading file.';
    }
}



?>
