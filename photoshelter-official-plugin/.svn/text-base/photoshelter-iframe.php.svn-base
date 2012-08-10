<style type="text/css">
<?php include( WP_PLUGIN_DIR . '/photoshelter-official-plugin/style.css');?>
</style>

<?php
require_once( WP_PLUGIN_DIR . '/photoshelter-official-plugin/photoshelter-psiframe.php');
global $psc;
$iframe = new PSIFrame($psc);

if ($_GET["G_ID"] && !$_GET['I_ID'] && !$_GET['embedGallery']) {
	$iframe->listImages($_GET['G_ID'], $_GET['G_NAME']);
} else if ($_GET["I_ID"]) {
	$iframe->embedImg($_GET["I_ID"], $_GET['G_ID'], $_GET['G_NAME']);
} else if ($_POST['I_ID']){
	$iframe->insertImg($_POST['I_ID'], $_POST['G_ID'], $_POST['WIDTH'], $_POST['F_HTML'], $_POST['F_BAR'], $_POST['G_NAME'], $_POST['F_CAP']);
} else if ($_GET['G_ID'] && $_GET['embedGallery']){
	$iframe->embedGallery($_GET['G_ID'], $_GET['G_NAME']);
} else if ($_GET['embedGallery'] && $_POST['G_ID']) {
	$iframe->insertGallery($_POST['G_ID'], $_POST['D_ID'], $_POST['G_NAME']);
} else if ($_GET['embedGalleryStatic']) { 
	$iframe->insertGalleryImage($_POST['G_ID'], $_POST['G_NAME'], $_POST["WIDTH"]);
} else if ($_POST["I_DSC"] || $_GET['I_DSC']) {
	$term = $_POST["I_DSC"] ? $_POST["I_DSC"] : $_GET["I_DSC"];
	$iframe->searchImages($term);
} else if ($_GET['recent']) {
	$iframe->recent_images();
} else {
	$iframe->listGalleries();
}
?>