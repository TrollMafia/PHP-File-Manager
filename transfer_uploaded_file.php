<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileToTransferName = $_POST['fileToTransfer']; // Get the selected file name
    $destinationFolderName = $_POST['destinationFolder']; // Get the destination folder name

    // Your database connection setup here
    $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');

    // Check if the folder_id is null or not present
    $folderIdQuery = $conn->prepare("SELECT folder_id FROM upload WHERE name = :file_name");
    $folderIdQuery->bindParam(':file_name', $fileToTransferName);
    $folderIdQuery->execute();
    $folderIdRow = $folderIdQuery->fetch(PDO::FETCH_ASSOC);
    $folderId = $folderIdRow['folder_id'];

    // Construct the source file path
    $sourceFilePath = ($folderId === null) ? 'files/' . $fileToTransferName : 'files/' . $folderId . '/' . $fileToTransferName;

    // Check if it's a root-to-folder transfer
    if ($folderId === null) {
        // Construct the destination folder path
        $destinationFolderPath = 'files/' . $destinationFolderName . '/';
        $destinationFilePath = $destinationFolderPath . $fileToTransferName;

        // Transfer the file using copy
        if (copy($sourceFilePath, $destinationFilePath)) {
            // Update the database to reflect the file transfer
            $updateQuery = $conn->prepare("UPDATE upload
                                          SET folder_id = (SELECT id FROM folders WHERE name = :destination_folder_name)
                                          WHERE name = :file_name");
            $updateQuery->bindParam(':destination_folder_name', $destinationFolderName);
            $updateQuery->bindParam(':file_name', $fileToTransferName);

            if ($updateQuery->execute()) {
                // Delete the source file
                unlink($sourceFilePath);

                header('Location: index.php'); // Redirect back to the main page
                exit();
            } else {
                echo 'Error updating the database.';
            }
        } else {
            echo 'Error transferring the file.';
        }
    } else {
        $fileQuery = $conn->prepare("SELECT u.name AS file_name, f.name AS source_folder_name
                                 FROM upload u
                                 JOIN folders f ON u.folder_id = f.id
                                 WHERE u.name = :file_name");
    $fileQuery->bindParam(':file_name', $fileToTransferName);
    $fileQuery->execute();
    $fileRow = $fileQuery->fetch(PDO::FETCH_ASSOC);

    if ($fileRow) {
        $uploadedFileName = $fileRow['file_name'];
        $sourceFolderName = $fileRow['source_folder_name']; // Get source folder name
        $sourceFilePath = 'files/' . $sourceFolderName . '/' . $uploadedFileName;

        // Destination folder path
        $destinationFolderPath = 'files/' . $destinationFolderName . '/';
        $destinationFilePath = $destinationFolderPath . $uploadedFileName;

        // Transfer the file using copy
        if (copy($sourceFilePath, $destinationFilePath)) {
            // Update the database to reflect the file transfer
            $updateQuery = $conn->prepare("UPDATE upload
                                          SET folder_id = (SELECT id FROM folders WHERE name = :destination_folder_name)
                                          WHERE name = :file_name");
            $updateQuery->bindParam(':destination_folder_name', $destinationFolderName);
            $updateQuery->bindParam(':file_name', $uploadedFileName);

            if ($updateQuery->execute()) {
                // Delete the source file
                unlink($sourceFilePath);

                header('Location: index.php'); // Redirect back to the main page
                exit();
            } else {
                echo 'Error updating database.';
            }
        } else {
            echo 'Error transferring file.';
        }
    } else {
        echo 'File not found.';
    }
}
}
?>
