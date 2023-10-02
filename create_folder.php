<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['folderName'])) {
        $newFolderName = $_POST['folderName'];
        $basePath = 'files/'; // Your folder path
        $newFolderPath = $basePath . $newFolderName;

        if (!file_exists($newFolderPath)) {
            if (mkdir($newFolderPath, 0777, true)) {
                // Add folder record to the database
                $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $folderNameForDB = htmlspecialchars($newFolderName); // Sanitize folder name
                $creationDate = date('Y-m-d H:i:s');

                $query = $conn->prepare("INSERT INTO folders (name, date) VALUES (?, ?)");
                $query->execute([$folderNameForDB, $creationDate]);

                echo 'Folder created successfully.';
            } else {
                echo 'Error creating folder.';
            }
        } else {
            echo 'Folder already exists.';
        }
    }
}
?>
