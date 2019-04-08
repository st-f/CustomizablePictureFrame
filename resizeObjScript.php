<?php
/**
 * @param $in - in or out part if it's a double
 * @param $width - part width
 * @param $originalModelWidth - original model width
 * @param $file - OBJ file
 * @param $useSingle - use single parts or double
 */
function parseAndSave($in, $width, $originalModelWidth, $file, $useSingle)
{
    $lines = [];
    $vLinesNumbers = [];
    $oneCmValues = [];
    $twoCmValues = [];
    $resizedLines = [];
    $lineIndex = 1;
    if ($useSingle) {
        $firstFile = "model-single-0cm.obj";
        $secondFile = "model-single-1cm.obj";
    } else {
        if ($in) {
            $firstFile = "base-in-0cm.obj";
            $secondFile = "base-in-1cm.obj";
        } else {
            $firstFile = "base-out-0cm.obj";
            $secondFile = "base-out-1cm.obj";
        }
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
            $widthFinal = $floatvalOne + ($width - $originalModelWidth) * $diff; //for half frames
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
    echo "<br/>Resizing model done, saved in $file\n";
}

