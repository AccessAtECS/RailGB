<?

// Get path requested
$request = $_SERVER["REQUEST_URI"];

// Get mobile detect class
$detect = new Mobile_Detect();

if($detect->isMobile()){
	header("Location: http://m.railgb.org.uk");
}