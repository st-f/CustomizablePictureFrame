<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 2019-03-03
 * Time: 22:12
 * Compares two obj files and tries to resize based on the differences in values
 */

include_once("resizeObjScript.php");
$width = $argv[1]; //width in millimeters
$height = $argv[2]; //height in millimeters
$originalModelWidth = 1.7; //paper cut size in model in cm (1.2cm cube + 5mm margin inside corner)
echo "Resizing picture frame : width: $width cm, height: $height cm\n";
parseAndSave(0, $width / 2, $originalModelWidth, "output-width-out.obj");
parseAndSave(1, $width / 2, $originalModelWidth, "output-width-in.obj");
parseAndSave(0, $height / 2, $originalModelWidth, "output-height-out.obj");
parseAndSave(1, $height / 2, $originalModelWidth, "output-height-in.obj");