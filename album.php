<?php
require 'vendor/autoload.php';
 
use Google\Cloud\Storage\StorageClient;
 
$mavid = 'pxb2056';
 
$storage = new StorageClient([
    'keyFilePath' => 'cse5335-404004-eaaa7031cd45.json',
    'suppressKeyFileNotice' => true
]);
 
$bucket = $storage->bucket("cse5335_$mavid");
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['userfile'])) {
    $uploadedFile = $_FILES['userfile'];
 
    if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
        $objectName = $uploadedFile['name'];
        $object = $bucket->upload(
            fopen($uploadedFile['tmp_name'], 'r'),
            [
                'name' => $objectName
            ]
        );
 
        echo "File uploaded successfully.";
    } else {
        echo "File upload failed.";
    }
}
 
$objects = $bucket->objects();
 
if (isset($_GET['delete'])) {
    $imageName = $_GET['delete'];
    $object = $bucket->object($imageName);
    $object->delete();
 
    echo "File deleted successfully.";
}
 
echo '<form enctype="multipart/form-data" action="" method="POST">';
echo 'Choose an image to upload: <input name="userfile" type="file">';
echo '<input type="submit" value="Upload Image">';
echo '</form>';
 
echo '<ul>';
foreach ($objects as $object) {
    $imageName = $object->name();
    echo '<li>';
    echo '<a href="?display=' . $imageName . '" download>' . $imageName . '</a>';
    echo ' | ';
    echo '<a href="?delete=' . $imageName . '">Delete</a>';
    echo '</li>';
}
echo '</ul>';
 
if (isset($_GET['display'])) {
    $imageName = $_GET['display'];
    $object = $bucket->object($imageName);
    $tmpFilePath = 'tmp/' . $imageName;
    file_put_contents($tmpFilePath, $object->downloadAsString());
    echo '<img src="' . $tmpFilePath . '" alt="' . $imageName . '">';
}
?>