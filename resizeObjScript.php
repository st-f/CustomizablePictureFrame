<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 2019-03-03
 * Time: 22:12
 * Compares two obj files and tries to resize based on the differences in values
 */
function parseAndSave($in, $width, $originalModelWidth, $file)
{
    $lines = [];
    $vLinesNumbers = [];
    $oneCmValues = [];
    $twoCmValues = [];
    $resizedLines = [];
    $lineIndex = 1;
    if ($in) {
        $firstFile = "base-in-0cm.obj";
        $secondFile = "base-in-1cm.obj";
    } else {
        $firstFile = "base-out-0cm.obj";
        $secondFile = "base-out-1cm.obj";
    }
    // parsing first file
    $handle = fopen($firstFile, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // process the line read.
            $values = preg_split("/ +/", $line);
            $type = $values[0];
            if ($type == "v") {
                array_push($oneCmValues, $line);
                array_push($vLinesNumbers, $lineIndex - 1);
            }
            array_push($lines, trim($line));
            $lineIndex++;
        }

        fclose($handle);
    } else {
        echo "ERROR WHILE PARSING FILE";
    }
    // parsing second file
    $handle = fopen($secondFile, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // process the line read.
            $values = preg_split("/ +/", $line);
            $type = $values[0];
            if ($type == "v") {
                array_push($twoCmValues, $line);
            }
        }

        fclose($handle);
    } else {
        echo "ERROR WHILE PARSING FILE";
    }
    //moving vertices

    for ($i = 0; $i < sizeof($oneCmValues); $i++) {
        $oneValue = $oneCmValues[$i];
        $oneValues = preg_split("/ +/", $oneValue);
        $twoValue = $twoCmValues[$i];
        $twoValues = preg_split("/ +/", $twoValue);
        $str = "v ";
        for ($p = 1; $p < sizeof($oneValues); $p++) {
            $floatvalOne = sprintf('%f', floatval($oneValues[$p]));
            $floatvalTwo = sprintf('%f', floatval($twoValues[$p]));
            $diff = ($floatvalTwo - $floatvalOne);
            $widthFinal = $floatvalOne + ($width - $originalModelWidth) * $diff;
            if ($diff != 0) {
                $str .= sprintf('%f', $widthFinal) . " ";
            } else {
                $str .= sprintf('%f', $floatvalOne) . " ";
            }
        }
        array_push($resizedLines, $str);
    }
    $finalArray = [];
    for ($i = 0; $i < sizeof($lines); $i++) {
        if (in_array($i, $vLinesNumbers)) {
            $index = array_search($i, $vLinesNumbers);
            $str = $resizedLines[$index];
        } else {
            $str = $lines[$i];
        }
        array_push($finalArray, $str);
    }
    file_put_contents($file, implode("\n", $finalArray));
    echo "<br/>Resizing picture frame : done, saved in $file\n";
}

