<?php
include('youtuberesolver.class.php');
$YTObj = new YouTubeResolver
$YTObj->getData($_GET['id']);// Provide the function getData() with video id.
echo $YTObj->data['title'].'<br>'.;
echo $YTObj->data['duration'];
?>
