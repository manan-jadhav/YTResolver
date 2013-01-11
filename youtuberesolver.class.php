<?php
class YouTubeResolverClass
{
public $data,$status,$vid;
protected $prodata,$rawdata,$streams,$stream,$ch,$rcount,$count,$vurl_exp,$headers,$value;
public function get_data($vid)
{
	if(empty($vid) || strlen($vid)!=11)//Check if $vid  is set and  it's length is 11 characters
	{
		$this->status[]=__METHOD__." : Video ID not set properly";
		return false;
	}
$this->ch=curl_init('http://www.youtube.com/get_video_info?video_id='.$vid);
curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);//To get the raw data in a string, we must use this
$this->rawdata=curl_exec($this->ch);//Save the raw data in $rawdata
curl_close($this->ch);
if(empty($this->rawdata))//Checking if data was successfully obtained
{
$this->status[]=__METHOD__." : Problem fechting video data from YouTube";
return false;
}
else
{
	$this->status[]=__METHOD__." :Successfully fetched video data from YouTube";
}
parse_str($this->rawdata,$this->prodata);//Process the raw data and create an array ($prodata) from it. $prodata stands for "processed data"
if(!isset($this->prodata['url_encoded_fmt_stream_map']))//Checking if video is protected.
{
$this->status[]=__METHOD__." : This video contains content from SME. It is restricted from playback on certain sites. This video can not be downloaded";
return false;
break;
}
$this->data['title']=$this->prodata['title'];
$this->data['views']=$this->prodata['view_count'];
$this->data['duration']=$this->prodata['length_seconds'];
$this->data['language']=$this->prodata['hl'];
$this->data['author']=$this->prodata['author'];
$this->data['thumbnail_url']=$this->prodata['thumbnail_url'];
$this->streams = explode(',',$this->prodata['url_encoded_fmt_stream_map']);//Split the stream map into streams.
$this->count=$this->data['numstreams']=count($this->streams);
$this->rcount=0;
       while ($this->rcount<$this->count)
        {
                parse_str($this->streams[$this->rcount],$this->stream);//Select a stream "$stream" from the array "$streams" 
                $this->data['streams'][$this->rcount]['url']=$this->stream['url'].'&signature='.$this->stream['sig'];// Create the proper URL
                $this->data['streams'][$this->rcount]['is3D']=$this->data['streams'][$this->rcount]['isLive']=$this->data['streams'][$this->rcount]['isHD']=false;//Set default value as false               
                switch($this->stream['itag'])//To extract data from itag value (Based on http://en.wikipedia.org/wiki/YouTube#Quality_and_codecs )
                {
                case '5' :      $this->data['streams'][$this->rcount]['type']="FLV (.flv) Video";
                					  $this->data['streams'][$this->rcount]['fmt']="flv";
                                $this->data['streams'][$this->rcount]['vres']="240";
                                $this->data['streams'][$this->rcount]['vcod']="H.263 (Sorenson)";
                                $this->data['streams'][$this->rcount]['vbit']="0.25";
                                $this->data['streams'][$this->rcount]['acod']="MP3";
                                $this->data['streams'][$this->rcount]['abit']="64";
                                $this->data['streams'][$this->rcount]['rawtype']="video/x-flv";
                                continue;
                case '6' :      $this->data['streams'][$this->rcount]['type']="FLV (.flv) Video";
                $this->data['streams'][$this->rcount]['fmt']="flv";
                                $this->data['streams'][$this->rcount]['vres']="270";
                                $this->data['streams'][$this->rcount]['vcod']="H.263 (Sorenson)";
                                $this->data['streams'][$this->rcount]['vbit']="0.8";
                                $this->data['streams'][$this->rcount]['acod']="MP3";
                                $this->data['streams'][$this->rcount]['abit']="64";
                                $this->data['streams'][$this->rcount]['rawtype']="video/x-flv";
                                continue;
                case '13' :      $this->data['streams'][$this->rcount]['type']="3GP (.3gp) Video";
                $this->data['streams'][$this->rcount]['fmt']="3gp";
                                $this->data['streams'][$this->rcount]['vres']="N/A";
                                $this->data['streams'][$this->rcount]['vcod']="MPEG-4 Visual";
                                $this->data['streams'][$this->rcount]['vbit']="0.5";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="N/A";
                                $this->data['streams'][$this->rcount]['rawtype']="video/3gpp";
                                continue;
                case '17' :     $this->data['streams'][$this->rcount]['type']="3GP (.3gp) Video";
                $this->data['streams'][$this->rcount]['fmt']="3gp";
                                $this->data['streams'][$this->rcount]['vres']="144";
                                $this->data['streams'][$this->rcount]['vcod']="MPEG-4 Visual";
                                $this->data['streams'][$this->rcount]['vbit']="0.05";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="24";
                                $this->data['streams'][$this->rcount]['rawtype']="video/3gpp";
                                continue;
                case '18' :     $this->data['streams'][$this->rcount]['type']="MP4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="360";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="0.5";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="96";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                continue;
                case '22' :     $this->data['streams'][$this->rcount]['type']="MP4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="720";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="2.4";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;                
                case '34' :     $this->data['streams'][$this->rcount]['type']="FLV (.flv) Video";
                $this->data['streams'][$this->rcount]['fmt']="flv";
                                $this->data['streams'][$this->rcount]['vres']="360";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="0.5";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="128";
                                $this->data['streams'][$this->rcount]['rawtype']="video/x-flv";
                                continue;
                
                case '35' :     $this->data['streams'][$this->rcount]['type']="FLV (.flv) Video";
                $this->data['streams'][$this->rcount]['fmt']="flv";
                                $this->data['streams'][$this->rcount]['vres']="480";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="1";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="128";
                                $this->data['streams'][$this->rcount]['rawtype']="video/x-flv";
                                continue;
                case '36' :     $this->data['streams'][$this->rcount]['type']="3GP (.3gp) Video";
                $this->data['streams'][$this->rcount]['fmt']="3gp";
                                $this->data['streams'][$this->rcount]['vres']="240";
                                $this->data['streams'][$this->rcount]['vcod']="MPEG-4 Visual";
                                $this->data['streams'][$this->rcount]['vbit']="0.17";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="38";
                                $this->data['streams'][$this->rcount]['rawtype']="video/3gpp";
                                continue;
                case '37' :     $this->data['streams'][$this->rcount]['type']="MP4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="1080";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="3.7";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;        
                case '38' :     $this->data['streams'][$this->rcount]['type']="MP4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="3072";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="4.5";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;        
                case '43' :     $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="360";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="0.5";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="128";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                continue;        
                case '44' :     $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="480";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="1";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="128";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                continue;   
                case '45' :     $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="720";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="2";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;   
                case '46' :     $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="1080";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="N/A";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '82' :     $this->data['streams'][$this->rcount]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="360";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="0.5";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="96";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '83' :     $this->data['streams'][$this->rcount]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="240";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="0.5";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="96";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '84' :     $this->data['streams'][$this->rcount]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="720";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="2.4";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="152";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '85' :     $this->data['streams'][$this->rcount]['type']="MPEG-4 (.mp4) Video";
                $this->data['streams'][$this->rcount]['fmt']="mp4";
                                $this->data['streams'][$this->rcount]['vres']="520";
                                $this->data['streams'][$this->rcount]['vcod']="H.264";
                                $this->data['streams'][$this->rcount]['vbit']="2.4";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="152";
                                $this->data['streams'][$this->rcount]['rawtype']="video/mp4";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '100' :    $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="360";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="N/A";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="128";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '101' :    $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="360";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="N/A";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '102' :    $this->data['streams'][$this->rcount]['type']="WebM (.webm) Video";
                $this->data['streams'][$this->rcount]['fmt']="webm";
                                $this->data['streams'][$this->rcount]['vres']="720";
                                $this->data['streams'][$this->rcount]['vcod']="VP8";
                                $this->data['streams'][$this->rcount]['vbit']="N/A";
                                $this->data['streams'][$this->rcount]['acod']="Vorbis";
                                $this->data['streams'][$this->rcount]['abit']="192";
                                $this->data['streams'][$this->rcount]['rawtype']="video/webm";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                continue;
                case '120' :    $this->data['streams'][$this->rcount]['type']="FLV (.flv) Video";
                						$this->data['streams'][$this->rcount]['fmt']="flv";
                              $this->data['streams'][$this->rcount]['vres']="720";
                                $this->data['streams'][$this->rcount]['vcod']="AVC";
                                $this->data['streams'][$this->rcount]['vbit']="2";
                                $this->data['streams'][$this->rcount]['acod']="AAC";
                                $this->data['streams'][$this->rcount]['abit']="128";
                                $this->data['streams'][$this->rcount]['rawtype']="video/x-flv";
                                $this->data['streams'][$this->rcount]['isHD']=true;
                                $this->data['streams'][$this->rcount]['isLive']=true;
                                continue;
                default :$this->status[]=__METHOD__." : Problem in getting data from itag value ";// If this occurs to you, Send me an email  / PM with the video id
                					return false;
                					continue;
                } 
			$this->rcount++;        
        } 
        $this->status[]=__METHOD__." : Successfully created links";
        $this->data['sucess']=true;
        return true; 
}
public function get_vid($vurl)
   {
	$this->vurl_exp=explode('?v=',$vurl);
	if($this->vurl_exp===$vurl) 
	{
	$this->status[]=__METHOD__." : Improper URL was given";		
	return false;	
	}
	$this->vid=$this->vurl_exp[1];
	$this->status[]=__METHOD__." : Video ID extracted successfully.";
	return $this->vid;
	}
public function search_key($haystack,$needle)
{
	$this->count=$this->data['numstreams'];
	while($this->count) 
{
  if($this->data['streams'][$this->count][$haystack]==$needle)
  {
   $this->status[]=__METHOD__." : Successfully found the Key with ".$haystack." as ".$needle;
   $this->search_results[]=$this->count;
  }
  $this->count--;
}
if(!empty($this->search_results))
   {
   	return true;
	}
$this->status[]=__METHOD__." : Failure in finding Key with ".$haystack." as ".$needle;
return false;
}
public function stream_video($key)
{
$this->headers=get_headers($this->data['streams'][$key]['url']);//Get headers from YouTube
foreach ($this->headers as $this->value)//For sending each and every header
{
	header($this->value);
}
$this->ch=curl_init($this->data['streams'][$key]['url']);//Initialize cURL 
curl_exec($this->ch);
curl_close($this->ch);
}
}
?>
