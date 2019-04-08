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
    include_once("resizeObjScript.php");
    $width = $_POST['width']; //width in centimeters
    $height = $_POST['height']; //height in centimeters
    $widthPrinter = $_POST['printerWidth']; //width in centimeters
    $heightPrinter = $_POST['printerHeight']; //height in centimeters
    if (!is_numeric($width) || !is_numeric($height) || !is_numeric($widthPrinter) || !is_numeric($heightPrinter)) {
        echo "<p>Values incorrect. Please use numerical values only</p>";
        echo "<br><a href='index.html'>Go back</a>";
        die;
    }
    $hypotenuse = round(hypot($widthPrinter, $heightPrinter), 1) - 1;
    echo "<h1>$width cm x $height cm picture frame, $widthPrinter cm x $heightPrinter cm print bed, $hypotenuse</h1>";
    $doubleHypotenuse = $hypotenuse * 2;
    if ($width > $doubleHypotenuse || $height > $doubleHypotenuse) {
        echo "<p>Values are too big. Please use a maximum of $doubleHypotenuse cm for both width and height for a frame in 8 parts, or $hypotenuse cm for a frame in 4 parts.</p>";
        echo "<br><br><a href='index.html'>Go back</a>";
        die;
    }
    if ($width < 4 || $height < 4) {
        echo "<p>Values are too small. Please use a minimum of 4cm for both width and height</p>";
        echo "<br><br><a href='index.html'>Go back</a>";
        die;
    }
    $useTwoPartsWidth =  $width > $hypotenuse;
    $useTwoPartsHeight = $height > $hypotenuse;
    $zipFilename = "picture-frame-$width" . "x$height.zip";
    $zipFilenameReverse = "picture-frame-$height" . "x$width.zip";
    if (file_exists($zipFilename) || file_exists($zipFilenameReverse)) {
        echo "Files already generated.<br>";
        if (!file_exists($zipFilename) && file_exists($zipFilenameReverse)) {
            echo "<br><a href='$zipFilenameReverse'>Download all files</a>";
        } else {
            echo "<br><a href='$zipFilename'>Download all files</a>";
        }
    } else {
        $originalModelWidth = 1.7; //paper cut size in model in cm (1.2cm cube + 5mm margin inside corner)
        echo "Resizing picture frame : width: $width cm, height: $height cm...\n";
        if ($useTwoPartsWidth) {
            $file1 = "output-width-out.obj";
            $file2 = "output-width-in.obj";
            parseAndSave(0, $width / 2, $originalModelWidth, $file1, false);
            parseAndSave(1, $width / 2, $originalModelWidth, $file2, false);
        } else {
            $file1 = "output-width.obj";
            parseAndSave(0, $width, $originalModelWidth, $file1, true);
        }
        if ($useTwoPartsHeight) {
            $file3 = "output-height-out.obj";
            $file4 = "output-height-in.obj";
            parseAndSave(0, $height / 2, $originalModelWidth, $file3, false);
            parseAndSave(1, $height / 2, $originalModelWidth, $file4, false);
        } else {
            $file3 = "output-height.obj";
            parseAndSave(0, $height, $originalModelWidth, $file3, true);
        }
        $file5 = "nail.obj";
        $zip = new ZipArchive;
        if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($file1);
            if ($useTwoPartsWidth) {
                $zip->addFile($file2);
            }
            $zip->addFile($file3);
            if ($useTwoPartsWidth) {
                $zip->addFile($file4);
            }
            $zip->addFile($file5);
            $zip->close();
        }
        echo "<br><br><a href='$zipFilename'>Download all files</a>";
    }
    ?>
</p>
</body>
</html>