<?php
set_time_limit(0);

if(isset($_POST['btnSubmit'])) {

    $strContent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!-- TemplateBeginEditable name="doctitle" -->
		<title>Untitled Document</title>
		<!-- TemplateEndEditable -->
		<!-- TemplateBeginEditable name="head" -->
		<!-- TemplateEndEditable -->
		<style type="text/css">
		body p {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		}
		
		/*	.mainContainer{background-color:#2C3F64;}	*/
		.mainContainer{background-color:#fff;}
		/*	.mainContainer{background-color:#FEED77;}	*/
		.header{background-color:#2C3F64;font-family:\'Calibri\';font-size:16px;color:#FFF;text-align:left;padding: 0 5px;border-color:#d0ffff;border-style: dotted;border-width: 0 1px 1px 0;}
		.cells{font-family:\'Calibri\';font-size:14px;text-align:left;padding: 0 5px;border-color:#4DB3D4;border-style: dotted;border-width: 0 1px 1px 0;}
		.text {width:420px;margin: 0 125px;color:#CC0000;font-size: 18px;font-weight: bold;padding:0 50px}
		.textSmall {font-size: 14px;}
		
		</style>
		</head>
		
		<body>
		<div class="mainContainer" style="background-color:#fff;width:1000px;float:left;margin:5px;">';

    /* ------- Creating array for Country ------- */

    $csv = array();
    $lines = file('country.csv', FILE_IGNORE_NEW_LINES);

    foreach ($lines as $key => $value)
    {
        $csv[$key] = str_getcsv($value);
    }

    $countryArray = array();
    for($i=0; $i < count($csv); $i++)
    {
        $countryArray[$i] = $csv[$i][0];
    }

    /* ------- Creating array for Country ------- */

    /* ------- Creating array for CSV File Data ------- */

    if($_FILES['fileCSV']['name'] != '') {

        $strPath = "temp/".uniqid() . basename($_FILES['fileCSV']['name']);
        move_uploaded_file($_FILES['fileCSV']['tmp_name'], $strPath);

        $arrModel = array();
        $fArray = array();
        $cart = array();
        $notMatch = array();

        function ImportCSV2Array($filename)
        {

            $row = 0;
            $col = 0;

            $handle = @fopen($filename, "r");
            if ($handle) {
                while (($row = fgetcsv($handle))) {
                    if (empty($fields)) {
                        $fields = $row;
                        continue;
                    }

                    $exp = explode('|', $row[1]);
                    $row[1] = $exp[0];
                    $row[1] = trim($row[1]);

                    foreach ($row as $k => $value) {

                        $results[$col][$fields[$k]] = $value;
                    }
                    $col++;
                    unset($row);
                }
                if (!feof($handle)) {
                    echo "Error: unexpected fgets() failn";
                }
                fclose($handle);
            }

            return $results;
        }

        $filename = $_FILES['fileCSV']['name'];
        $csvArray = ImportCSV2Array($filename);

        /* ------- Creating array for CSV File Data ------- */

        /* --------------- Matched array values ----------- */
echo "ddddd";
        $matchedValues = array();
        $tempArray = array();
        for ($i = 0; $i < count($countryArray); $i++) {

            $keywords = $countryArray[$i];

            $date = "";
            $keyword = "";
            $impression = "";
            $clicks = "";
            $cost = "";
            $avg = "";
            $conversion = "";
            $view = "";

            $isExist = false;
            for ($j = 0; $j < count($csvArray); $j++) {

                if (in_array(trim($keywords), $csvArray[$j])) {
                    $date = $csvArray[$j]['Date'];
                    $impression = $impression + $csvArray[$j]['Impression'];
                    $clicks = $clicks + $csvArray[$j]['Clicks'];
                    $cost = $cost + $csvArray[$j]['Cost'];
                    $avg = $avg + $csvArray[$j]['Avg.CPC'];
                    $conversion = $conversion + $csvArray[$j]['Conversion'];
                    $view = $view + $csvArray[$j]['View Through Conversion'];

                    $isExist = true;
                }

            }

            if ($isExist == true) {
                $tempArray['Date'] = $date;
                $tempArray['Country Specific Keywords'] = $keywords;
                $tempArray['Impression'] = $impression;
                $tempArray['Clicks'] = $clicks;
                $tempArray['Cost'] = $cost;
                $tempArray['Avg.CPC'] = $avg;
                $tempArray['Conversion'] = $conversion;
                $tempArray['View Through Conversion'] = $view;

                array_push($matchedValues, $tempArray);
            }
        }

    /* --------------- Matched array values ----------- */


    /* --------------- Un Matched array values ----------- */

        $unMatchedValues = array();
        $tempArray = array();
        for ($j = 0; $j < count($csvArray); $j++) {

            $csvKeyword = $csvArray[$j]['Country Specific Keywords'];
            $csvKeywordN = $csvArray[$j]['Country Specific Keywords'];


            if (strpos($csvKeyword, '-') !== false) {
                $csvKeyword = explode('-', $csvKeyword);
            }
            else
            {
                $csvKeyword = explode('|', $csvKeyword);
            }

            $csvK = strtoupper($csvKeyword[0]);

            $date = $csvArray[$j]['Date'];
            $impression = $csvArray[$j]['Impression'];
            $clicks = $csvArray[$j]['Clicks'];
            $cost = $csvArray[$j]['Cost'];
            $avg = $csvArray[$j]['Avg.CPC'];
            $conversion = $csvArray[$j]['Conversion'];
            $view = $csvArray[$j]['View Through Conversion'];

            $isExist = true;

            if (in_array(trim($csvK), $countryArray)) {

                $isExist = false;

            }

            if ($isExist != false) {

                $tempArray['Date'] = $date;

                if (strpos($csvKeywordN, '-') !== false) {
                    $tempArray['Country Specific Keywords'] = $csvKeywordN;
                }else {
                    $tempArray['Country Specific Keywords'] = $csvK;

                }
                $tempArray['Impression'] = $impression;
                $tempArray['Clicks'] = $clicks;
                $tempArray['Cost'] = $cost;
                $tempArray['Avg.CPC'] = $avg;
                $tempArray['Conversion'] = $conversion;
                $tempArray['View Through Conversion'] = $view;

                array_push($unMatchedValues, $tempArray);
            }

        }

    /* --------------- Un Matched array values ----------- */

        /* --------------- Merging Two arrays ----------- */

            $gArray = array();
            $gArray = array_merge($matchedValues, $unMatchedValues);

        /* --------------- Merging Two arrays ----------- */


    /* --------- Creating Optimized CSV file -----------*/

        if(isset($_POST['btnSubmit'])) {

            $newFileName = uniqid() . basename($_FILES['fileCSV']['name']);

            $pathToGenerate = $newFileName; // your path and file name
            $header = null;
            $createFile = fopen($pathToGenerate, "w+");
            foreach ($gArray as $row) {

                if (!$header) {

                    fputcsv($createFile, array_keys($row));
                    fputcsv($createFile, $row);   // do the first row of data too
                    $header = true;
                } else {

                    fputcsv($createFile, $row);
                }
            }
            fclose($createFile);


            // output headers so that the file is downloaded rather than displayed
            header("Content-type: text/csv");
            header("Content-disposition: attachment; filename = " . $newFileName);
            readfile($newFileName);
        }

    }
    die();
    /* --------- Creating Optimized CSV file -----------*/
    ?>

		</div>
		</body>
		</html>


<?php

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Template</title>
    <style>
        body {
            background-color:#CCC
        }

        .form-container {
            border: 1px solid #f2e3d2;
            background: #c9b7a2;
            background: -webkit-gradient(linear, left top, left bottom, from(#f2e3d2), to(#c9b7a2));
            background: -webkit-linear-gradient(top, #f2e3d2, #c9b7a2);
            background: -moz-linear-gradient(top, #f2e3d2, #c9b7a2);
            background: -ms-linear-gradient(top, #f2e3d2, #c9b7a2);
            background: -o-linear-gradient(top, #f2e3d2, #c9b7a2);
            background-image: -ms-linear-gradient(top, #f2e3d2 0%, #c9b7a2 100%);
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            -webkit-box-shadow: rgba(000, 000, 000, 0.9) 0 1px 2px, inset rgba(255, 255, 255, 0.4) 0 0px 0;
            -moz-box-shadow: rgba(000, 000, 000, 0.9) 0 1px 2px, inset rgba(255, 255, 255, 0.4) 0 0px 0;
            box-shadow: rgba(000, 000, 000, 0.9) 0 1px 2px, inset rgba(255, 255, 255, 0.4) 0 0px 0;
            font-family: 'Helvetica Neue', Helvetica, sans-serif;
            text-decoration: none;
            vertical-align: middle;
            min-width:300px;
            padding:20px;
            width:300px;
            text-align:left;
        }
        .form-field {
            border: 1px solid #c9b7a2;
            background: #e4d5c3;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            color: #c9b7a2;
            -webkit-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(000, 000, 000, 0.7) 0 0px 0px;
            -moz-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(000, 000, 000, 0.7) 0 0px 0px;
            box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(000, 000, 000, 0.7) 0 0px 0px;
            padding:8px;
            margin-bottom:20px;
            width:280px;
        }
        .form-field:focus {
            background: #fff;
            color: #725129;
        }
        .form-container h2 {
            text-shadow: #fdf2e4 0 1px 0;
            font-size:18px;
            margin: 0 0 10px 0;
            font-weight:bold;
            text-align:center;
        }
        .form-title {
            margin-bottom:10px;
            color: #725129;
            text-shadow: #fdf2e4 0 1px 0;
        }
        .submit-container {
            margin:8px 0;
            text-align:center;
        }
        .submit-button {
            border: 1px solid #447314;
            background: #6aa436;
            background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
            background: -webkit-linear-gradient(top, #8dc059, #6aa436);
            background: -moz-linear-gradient(top, #8dc059, #6aa436);
            background: -ms-linear-gradient(top, #8dc059, #6aa436);
            background: -o-linear-gradient(top, #8dc059, #6aa436);
            background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(255, 255, 255, 0.4) 0 1px 0;
            -moz-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(255, 255, 255, 0.4) 0 1px 0;
            box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(255, 255, 255, 0.4) 0 1px 0;
            text-shadow: #addc7e 0 1px 0;
            color: #31540c;
            font-family: helvetica, serif;
            padding: 8.5px 18px;
            font-size: 14px;
            text-decoration: none;
            vertical-align: middle;
        }
        .submit-button:hover {
            border: 1px solid #447314;
            text-shadow: #31540c 0 1px 0;
            background: #6aa436;
            background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
            background: -webkit-linear-gradient(top, #8dc059, #6aa436);
            background: -moz-linear-gradient(top, #8dc059, #6aa436);
            background: -ms-linear-gradient(top, #8dc059, #6aa436);
            background: -o-linear-gradient(top, #8dc059, #6aa436);
            background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
            color: #fff;
        }
        .submit-button:active {
            text-shadow: #31540c 0 1px 0;
            border: 1px solid #447314;
            background: #8dc059;
            background: -webkit-gradient(linear, left top, left bottom, from(#6aa436), to(#6aa436));
            background: -webkit-linear-gradient(top, #6aa436, #8dc059);
            background: -moz-linear-gradient(top, #6aa436, #8dc059);
            background: -ms-linear-gradient(top, #6aa436, #8dc059);
            background: -o-linear-gradient(top, #6aa436, #8dc059);
            background-image: -ms-linear-gradient(top, #6aa436 0%, #8dc059 100%);
            color: #fff;
        }
    </style>
</head>
<body>
<div align="center" style="width:100%; margin-top:150px">
    <form class="form-container" enctype="multipart/form-data" method="post">
        <div class="form-title">
            <h2>Generate Template v 1.0</h2>
        </div>

        <!--<div class="form-title">New File Name</div>-->
        <!--<input class="form-field" type="text" name="strGenerate" />-->
        <br />
        <div class="form-title">Select File</div>
        <input class="form-field" type="file" name="fileCSV" />
        &nbsp;&nbsp;<span style="color:#F00; font-style:italic">(File Format should be CSV)</span><br />
        <br />
        <div class="submit-container">
            <input class="submit-button" type="submit" name="btnSubmit" value="Generate" />
        </div>
        <p id="showDownload" style="display: none;"> Your file is ready
        <button name="downloadCSV">Download File</button>
        </p>
    </form>
</div>
</body>
</html>