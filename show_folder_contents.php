<?php
if (isset($_GET['folderId'])) {
    $folderId = $_GET['folderId'];
    
    // Retrieve folder contents from the database
    $conn = new PDO('mysql:host=localhost; dbname=myweb', 'root', '');
    $query = $conn->prepare("SELECT id, name FROM upload WHERE folder_id = :folder_id");
    $query->bindParam(':folder_id', $folderId);
    $query->execute();
    $contents = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Display folder contents
    if (!empty($contents)) {
        echo '<ul>';
        foreach ($contents as $content) {
            echo '<li>' . $content['name'] . '</li>';
        }
        echo '</ul>';
    } else {
        echo 'Folder is empty.';
    }
} else {
    echo 'Invalid request.';
}
?>
