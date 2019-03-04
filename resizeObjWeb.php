<html>
<head>
    <style>
        body {
            padding: 50px;
            font-family: Arial;
        }
    </style>
</head>
<body>
<p>
    <?php
    /**
     * Created by PhpStorm.
     * User: stef
     * Date: 2019-03-03
     * Time: 22:12
     * Compares two obj files and tries to resize based on the differences in values
     */
    include_once("resizeObjScript.php");
    $width = $_POST['width']; //width in millimeters
    $height = $_POST['height']; //height in millimeters
    echo "<h1>$width" ."cm x " . $height. "cm picture frame</h1>";
    $zipFilename = "picture-frame-$width" . "x$height.zip";
    $zipFilenameReverse = "picture-frame-$height" . "x$width.zip";
    if (file_exists($zipFilename) || file_exists($zipFilenameReverse)) {
        echo "Files already generated.<br>";
        if(!file_exists($zipFilename) && file_exists($zipFilenameReverse)) {
            echo "<br><a href='$zipFilenameReverse'>Download all files</a>";
        } else {
            echo "<br><a href='$zipFilename'>Download all files</a>";
        }
    } else {
        $originalModelWidth = 1.7; //paper cut size in model in cm (1.2cm cube + 5mm margin inside corner)
        echo "Resizing picture frame : width: $width cm, height: $height cm...\n";
        $file1 = "output-width-out.obj";
        $file2 = "output-width-in.obj";
        $file3 = "output-height-out.obj";
        $file4 = "output-height-in.obj";
        $file4 = "output-height-in.obj";
        $file5 = "nail.obj";
        parseAndSave(0, $width / 2, $originalModelWidth, $file1);
        parseAndSave(1, $width / 2, $originalModelWidth, $file2);
        parseAndSave(0, $height / 2, $originalModelWidth, $file3);
        parseAndSave(1, $height / 2, $originalModelWidth, $file4);
        $zip = new ZipArchive;
        if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($file1);
            $zip->addFile($file2);
            $zip->addFile($file3);
            $zip->addFile($file4);
            $zip->addFile($file5);
            $zip->close();
        }
        echo "<br><br><a href='$zipFilename'>Download all files</a>";
    }
    ?>
</p>
</body>
</html>