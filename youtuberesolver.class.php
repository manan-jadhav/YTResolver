<?php
class YouTubeResolverClass
{
public $data,$status;
protected $prodata,$rawdata,$streams,$stream;
public function get_data($vid)
{
	if(empty($vid) || strlen($vid)!=11)//Check if $vid  is set and  it's length is 11 characters
	{
		$this->status[]=__METHOD__." : Video ID not set properly";
		return false;
	}
$ch=curl_init('http://www.youtube.com/get_video_info?video_id='.$vid);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//To get the raw data in a string, we must use this
$this->rawdata=curl_exec($ch);//Save the raw data in $rawdata
curl_close($ch);
if(empty($this->data))//Checking if data was successfully obtained
{
$this->status[]=__METHOD__." : Problem fechting video data from YouTube";
return false;
}
parse_str($this->rawdata,$this->prodata);//Process the raw data and create an array ($prodata) from it. $prodata stands for "processed data"
if(!isset($this->prodata['url_encoded_fmt_stream_map']))//Checking if video is protected.
{
$this->status[]=__METHOD__." : This video contains content from SME. It is restricted from playback on certain sites. This video can not be downloaded";
return false;
break;
}
$this->data['title']=$this->streams['title'];
$this->data['views']=$this->streams['view_count'];
$this->data['duration']=$this->streams['length_seconds'];
$this->data['language']=$this->streams['hl'];
$this->data['author']=$this->streams['author'];
$this->data['thumbnail_url']=$this->streams['thumbnail_url'];
$this->streams = explode(',',$this->prodata['url_encoded_fmt_stream_map']);//Split the stream map into streams.
$countdown=$this->data['numstreams']=count($this->streams);
       while ($countdown)
        {
                parse_str($this->streams[$countdown],$this->stream);//Select a stream "$stream" from the array "$streams" 
                $this->data['streams'][]['url']=$this->stream['url'].'&signature='.$this->stream['sig'];// Create the proper URL
                $this->data['streams'][]['is3D']=$this->data['streams'][]['isLive']=$this->data['streams'][]['isHD']=false;//Set default value as false
                switch($this->stream['itag'])//To extract data from itag value (Based on http://en.wikipedia.org/wiki/YouTube#Quality_and_codecs )
                {
                case '5' :      $this->data['streams'][]['type']="FLV (.flv) Video";
                					  $this->data['streams'][]['fmt']="flv";
                                $this->data['streams'][]['vres']="240";
                                $this->data['streams'][]['vcod']="H.263 (Sorenson)";
                                $this->data['streams'][]['vbit']="0.25";
                                $this->data['streams'][]['acod']="MP3";
                                $this->data['streams'][]['abit']="64";
                                $this->data['streams'][]['rawtype']="video/x-flv";
                                continue;
                case '6' :      $this->data['streams'][]['type']="FLV (.flv) Video";
                $this->data['streams'][]['fmt']="flv";
                                $this->data['streams'][]['vres']="270";
                                $this->data['streams'][]['vcod']="H.263 (Sorenson)";
                                $this->data['streams'][]['vbit']="0.8";
                                $this->data['streams'][]['acod']="MP3";
                                $this->data['streams'][]['abit']="64";
                                $this->data['streams'][]['rawtype']="video/x-flv";
                                continue;
                case '13' :      $this->data['streams'][]['type']="3GP (.3gp) Video";
                $this->data['streams'][]['fmt']="3gp";
                                $this->data['streams'][]['vres']="N/A";
                                $this->data['streams'][]['vcod']="MPEG-4 Visual";
                                $this->data['streams'][]['vbit']="0.5";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="N/A";
                                $this->data['streams'][]['rawtype']="video/3gpp";
                                continue;
                case '17' :     $this->data['streams'][]['type']="3GP (.3gp) Video";
                $this->data['streams'][]['fmt']="3gp";
                                $this->data['streams'][]['vres']="144";
                                $this->data['streams'][]['vcod']="MPEG-4 Visual";
                                $this->data['streams'][]['vbit']="0.05";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="24";
                                $this->data['streams'][]['rawtype']="video/3gpp";
                                continue;
                case '18' :     $this->data['streams'][]['type']="MP4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="360";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="0.5";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="96";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                continue;
                case '22' :     $this->data['streams'][]['type']="MP4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="720";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="2.4";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                continue;                
                case '34' :     $this->data['streams'][]['type']="FLV (.flv) Video";
                $this->data['streams'][]['fmt']="flv";
                                $this->data['streams'][]['vres']="360";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="0.5";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="128";
                                $this->data['streams'][]['rawtype']="video/x-flv";
                                continue;
                
                case '35' :     $this->data['streams'][]['type']="FLV (.flv) Video";
                $this->data['streams'][]['fmt']="flv";
                                $this->data['streams'][]['vres']="480";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="1";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="128";
                                $this->data['streams'][]['rawtype']="video/x-flv";
                                continue;
                case '36' :     $this->data['streams'][]['type']="3GP (.3gp) Video";
                $this->data['streams'][]['fmt']="3gp";
                                $this->data['streams'][]['vres']="240";
                                $this->data['streams'][]['vcod']="MPEG-4 Visual";
                                $this->data['streams'][]['vbit']="0.17";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="38";
                                $this->data['streams'][]['rawtype']="video/3gpp";
                                continue;
                case '37' :     $this->data['streams'][]['type']="MP4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="1080";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="3.7";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                continue;        
                case '38' :     $this->data['streams'][]['type']="MP4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="3072";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="4.5";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                continue;        
                case '43' :     $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="360";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="0.5";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="128";
                                $this->data['streams'][]['rawtype']="video/webm";
                                continue;        
                case '44' :     $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="480";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="1";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="128";
                                $this->data['streams'][]['rawtype']="video/webm";
                                continue;   
                case '45' :     $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="720";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="2";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/webm";
                                continue;   
                case '46' :     $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="1080";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="N/A";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/webm";
                                continue;
                case '82' :     $this->data['streams'][]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="360";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="0.5";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="96";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                $this->is3D[]=true;
                                continue;
                case '83' :     $this->data['streams'][]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="240";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="0.5";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="96";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                $this->is3D[]=true;
                                continue;
                case '84' :     $this->data['streams'][]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="720";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="2.4";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="152";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                $this->is3D[]=true;
                                continue;
                case '85' :     $this->data['streams'][]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][]['fmt']="mp4";
                                $this->data['streams'][]['vres']="520";
                                $this->data['streams'][]['vcod']="H.264";
                                $this->data['streams'][]['vbit']="2.4";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="152";
                                $this->data['streams'][]['rawtype']="video/mp4";
                                $this->is3D[]=true;
                                continue;
                case '100' :    $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="360";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="N/A";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="128";
                                $this->data['streams'][]['rawtype']="video/webm";
                                $this->is3D[]=true;
                                continue;
                case '101' :    $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="360";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="N/A";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/webm";
                                $this->is3D[]=true;
                                continue;
                case '102' :    $this->data['streams'][]['type']="WebM (.webm) Video";
                $this->data['streams'][]['fmt']="webm";
                                $this->data['streams'][]['vres']="720";
                                $this->data['streams'][]['vcod']="VP8";
                                $this->data['streams'][]['vbit']="N/A";
                                $this->data['streams'][]['acod']="Vorbis";
                                $this->data['streams'][]['abit']="192";
                                $this->data['streams'][]['rawtype']="video/webm";
                                $this->is3D[]=true;
                                continue;
                case '120' :    $this->data['streams'][]['type']="FLV (.flv) Video";
                						$this->data['streams'][]['fmt']="flv";
                              $this->data['streams'][]['vres']="720";
                                $this->data['streams'][]['vcod']="AVC";
                                $this->data['streams'][]['vbit']="2";
                                $this->data['streams'][]['acod']="AAC";
                                $this->data['streams'][]['abit']="128";
                                $this->data['streams'][]['rawtype']="video/x-flv";
                                $this->isLive[]=true;
                                continue;
                default :$this->status[]=__METHOD__." : Problem in getting data from itag value";// If this occurs to you, Send me an email  / PM with the video id
                					return false;
                					continue;
                } 
          if($this->data[]['vres']>=720)//Check if video is in HD
        $this->data[]['isHD']=true;
			$countdown--;        
        }
        $this->status[]=__METHOD__." : Successfully created links";
        $this->data['sucess']=true;
        return true; 
}
public function get_vid($vurl)
   {
	$vurl_exp=explode('?v=',$vurl);
	if($vurl_exp===$vurl) 
	{
	$this->status[]=__METHOD__." : Improper URL was given";		
		}
	$vid=$vurl_exp[1];
	$this->status[]=__METHOD__." : Video ID extracted successfully.";
	return $vid;
	}
public function search_key($haystack,$needle)
{
	$countdown=$this->data['numstreams'];
	while($countdown) 
{
  if($this->data['streams'][$countdown][$haystack]==$needle)
  {
   $this->status[]=__METHOD__." : Successfully found the Key with ".$haystack." as ".$needle;
   return $countdown;
   break;
  }
  $countdown--;
}
$this->status[]=__METHOD__." : Failure in finding Key with ".$haystack." as ".$needle;
return false;
}
public function stream_video($key)
{
$headers=get_headers($dld->url[6]);//Get headers from YouTube
foreach ($headers as $value)//For sending each and every header
{
	header($value);
}
$ch=curl_init($dld->url[$key]);//Initialize cURL 
curl_exec($ch);
curl_close($ch);
}
}
?>