<?php
include('youtuberesolver.class.php');
$YTObj = new YouTubeResolverClass;
$YTObj->get_data($_GET['id']);// Provide the function getData() with video id.
print_r($YTObj->data);
?>
