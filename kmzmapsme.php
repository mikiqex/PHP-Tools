<?php declare(strict_types=1);

// Change according to file name. "MyPlaces" will look for file "MyPlaces.kmz".
$kmzFileName = "Japonsko";

// Change styles of KML categories. Allowed are: green, red, yellow, blue, purple, pink, orange, brown.
$vars = [
	"airports"=>"orange",
	"rail"=>"orange",
	"subway"=>"orange",
	"parking_lot"=>"orange",
	"tram"=>"orange",
	"bus"=>"orange",
	"cabs"=>"orange",

	"info-i"=>"yellow",

	"grocery"=>"green",
	"coffee"=>"green",
	"dining"=>"green",
	"snack_bar"=>"green",
	"shopping"=>"green",

	"trail"=>"brown",
	"volcano"=>"brown",
	"parks"=>"brown",

	"lodging"=>"pink",

	"arts"=>"blue",
	"camera"=>"blue",

	"wht-blank"=>"red",
	"ylw-pushpin"=>"red",
	"ylw-pushpin_hl"=>"red",
	"trail"=>"red",
];

// Read KML from KMZ
$zip = new ZipArchive;
$res = $zip->open(getcwd()."/".$kmzFileName.".kmz");
if ($res !== true) exit("ZipArchive error #".$res);
$data = $zip->getFromName("doc.kml");
$zip->close();

// Parse KML and replace styleUrl
$lines = explode("\n", str_replace("\r", "", $data));
$out = [];
foreach ($lines as $l) {
	$l = trim($l);
	if ($l != "" && substr($l, 0, 10) === "<styleUrl>") {
		$ll = rtrim(substr($l, 11, -11), "-1234567890");
		if (($usp = strpos($ll, "_")) !== false) $ll = substr($ll, $usp + 1);
		echo $ll." = ".($vars[$ll] ?? "<span style=\"color:#c33;\">MISSING</span>")."<br>\n";
		$out[] = "<styleUrl>#placemark-".($vars[$ll] ?? "red")."</styleUrl>";
	} else $out[] = $l;
}

// Create output KMZ
$zip = new ZipArchive;
if (($res = $zip->open(getcwd()."/".$kmzFileName.".Maps.me.kmz", ZipArchive::CREATE)) !== true) exit("ZipArchive create error #".$res);
$zip->addFromString("doc.kml", implode("\n", $out));
$zip->close();
exit("<br>\n<a href=\"".$kmzFileName.".Maps.me.kmz\">".$kmzFileName.".Maps.me.kmz</a> created successfully.");
