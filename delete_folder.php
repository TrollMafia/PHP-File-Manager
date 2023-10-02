<?php
if (isset($_POST['delete_folder']) && isset($_POST['folder_name'])) {
    $folderName = $_POST['folder_name'];

    // Your database connection setup here
    $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');

    // Retrieve folder information from the database
    $query = $conn->prepare("SELECT * FROM folders WHERE name = :folder_name");
    $query->bindParam(':folder_name', $folderName);
    $query->execute();
    $folder = $query->fetch(PDO::FETCH_ASSOC);

    if ($folder) {
        $folderPath = 'files/' . $folderName . '/';

        // Remove files from the folder
        $fileQuery = $conn->prepare("SELECT * FROM upload WHERE folder_id = :folder_id");
        $fileQuery->bindParam(':folder_id', $folder['id']);
        $fileQuery->execute();
        while ($file = $fileQuery->fetch(PDO::FETCH_ASSOC)) {
            $fileName = $file['name'];
            $file_path = $folderPath . $fileName;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Remove files from the database
        $deleteFilesQuery = $conn->prepare("DELETE FROM upload WHERE folder_id = :folder_id");
        $deleteFilesQuery->bindParam(':folder_id', $folder['id']);
        $deleteFilesQuery->execute();

        // Function to recursively delete a directory and its contents
        function deleteDirectory($dir) {
            if (!file_exists($dir)) {
                return true;
            }

            if (!is_dir($dir)) {
                return unlink($dir);
            }

            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }

                if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                    return false;
                }
            }

            return rmdir($dir);
        }

        // Call the function to delete the directory and its contents
        deleteDirectory($folderPath);

        // Remove the folder from the database
        $deleteFolderQuery = $conn->prepare("DELETE FROM folders WHERE id = :folder_id");
        $deleteFolderQuery->bindParam(':folder_id', $folder['id']);

        if ($deleteFolderQuery->execute()) {
            header('Location: index.php'); // Redirect back to the main page
        } else {
            echo 'Error deleting the folder from the database.';
        }
    } else {
        echo 'Folder not found.';
    }
} else {
    echo 'Invalid request.';
}
?>
