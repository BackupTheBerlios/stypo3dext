<?php
error_reporting(E_ALL);
ini_set("display_errors","1");
@set_time_limit(10000);
if(isset($_GET["action"]) && ($_GET["action"]=="pdf") && isset($_GET["codecheval"])){

require_once('html2ps_v214/public_html/config.inc.php');
require_once(HTML2PS_DIR.'pipeline.factory.class.php');

$url = null;
$typeDev="prod";

if($typeDev == "prod")
	$url = "www.haras-nationaux.fr/portail/index.php";
else if($typeDev == "dev")
	$url = "webmaster:stella19@www.haras-nationaux.fr/portail_dev/index.php";
else if($typeDev == "dev_ext")
	$url = "hn.projets.dlcube.com/portail/index.php";
else if($typeDev == "local")
	$url = "127.0.0.1/portail/index.php";

ini_set("user_agent", DEFAULT_USER_AGENT);

$g_baseurl = $url."?id=".$_GET["id"]."&action=getFiche4PDF&codecheval=".$_GET["codecheval"]."&no_cache=1";

if(isset($_GET["debug"]))
	echo $g_baseurl;

if ($g_baseurl === "") {
  die("Please specify URL to process!");
}

// Add HTTP protocol if none specified
if (!preg_match("/^https?:/",$g_baseurl)) {
  $g_baseurl = 'http://'.$g_baseurl;
}

$g_css_index = 0;

// Title of styleshee to use (empty if no preferences are set)
$g_stylesheet_title = "";

$g_config = array(
                  'cssmedia'      => "screen",
                  'media'         => "A4",
                  'scalepoints'   => true,
                  'renderimages'  => true,
                  'renderfields'  => true,
                  'renderforms'   => false,
                  'pslevel'       => 3,
                  'renderlinks'   => true,
                  'pagewidth'     => 800,
                  'landscape'     => false,
                  'method'        => "fpdf",
                  'margins'       => array(
                                           'left'    => 5,
                                           'right'   => 0,
                                           'top'     => 15,
                                           'bottom'  => 15,
                                           ),
                  'encoding'      => "",
                  'ps2pdf'        => false,
                  'compress'      => false,
                  'output'        => 0,
                  'pdfversion'    => "1.3",
                  'transparency_workaround' => false,
                  'imagequality_workaround' => false,
                  'draw_page_border'        => false,
                  'debugbox'      => false,
                  'html2xhtml'    => true,
                  'mode'          => 'html'
                  );

// ========== Entry point
parse_config_file(HTML2PS_DIR.'html2ps.config');

// validate input data
if ($g_config['pagewidth'] == 0) {
  die("Please specify non-zero value for the pixel width!");
};

// begin processing

$g_media = Media::predefined($g_config['media']);
$g_media->set_landscape($g_config['landscape']);
$g_media->set_margins($g_config['margins']);
$g_media->set_pixels($g_config['pagewidth']);

// Initialize the coversion pipeline
$pipeline = new Pipeline();

// Configure the fetchers
$pipeline->fetchers[] = new FetcherURL();

// Configure the data filters
$pipeline->data_filters[] = new DataFilterDoctype();
$pipeline->data_filters[] = new DataFilterUTF8($g_config['encoding']);
if ($g_config['html2xhtml']) {
  $pipeline->data_filters[] = new DataFilterHTML2XHTML();
} else {
  $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
};

$pipeline->parser = new ParserXHTML();

// "PRE" tree filters

$pipeline->pre_tree_filters = array();

$header_html    = "";
$footer_html    = "";
$pipeline->pre_tree_filters[] = new PreTreeFilterHeaderFooter($header_html, $footer_html);

if ($g_config['renderfields']) {
  $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
};

//

if ($g_config['method'] === 'ps') {
  $pipeline->layout_engine = new LayoutEnginePS();
} else {
  $pipeline->layout_engine = new LayoutEngineDefault();
};

$pipeline->post_tree_filters = array();

// Configure the output format
if ($g_config['pslevel'] == 3) {
  $image_encoder = new PSL3ImageEncoderStream();
} else {
  $image_encoder = new PSL2ImageEncoderStream();
};

switch ($g_config['method']) {
 case 'fastps':
   if ($g_config['pslevel'] == 3) {
     $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
   } else {
     $pipeline->output_driver = new OutputDriverFastPSLevel2($image_encoder);
   };
   break;
 case 'pdflib':
   $pipeline->output_driver = new OutputDriverPDFLIB16($g_config['pdfversion']);
   break;
 case 'fpdf':
   $pipeline->output_driver = new OutputDriverFPDF();
   break;
 case 'png':
   $pipeline->output_driver = new OutputDriverPNG();
   break;
 case 'pcl':
   $pipeline->output_driver = new OutputDriverPCL();
   break;
 default:
   die("Unknown output method");
};

// Setup watermark
$watermark_text = "";//DEVELOPPEMENT";
$pipeline->output_driver->set_watermark($watermark_text);
if ($g_config['debugbox']) {
  $pipeline->output_driver->set_debug_boxes(true);
}

if ($g_config['draw_page_border']) {
  $pipeline->output_driver->set_show_page_border(true);
}

if ($g_config['ps2pdf']) {
  $pipeline->output_filters[] = new OutputFilterPS2PDF($g_config['pdfversion']);
}

if ($g_config['compress'] && $g_config['method'] == 'fastps') {
  $pipeline->output_filters[] = new OutputFilterGZip();
}

$filename = $_GET["nomcheval"];

switch ($g_config['output']) {
 case 0:
   $pipeline->destination = new DestinationBrowser($filename);
   break;
 case 1:
   $pipeline->destination = new DestinationDownload($filename);
   break;
 case 2:
   $pipeline->destination = new DestinationFile($filename);
   break;
};

// Start the conversion

$time = time();
$status = $pipeline->process($g_baseurl, $g_media);

error_log(sprintf("Processing of '%s' completed in %u seconds", $g_baseurl, time() - $time));

if ($status == null) {
  print($pipeline->error_message());
  echo "error";
  error_log("Error in conversion pipeline");
  die();
}
	exit();
}
?>
