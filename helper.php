<?php
/**
 * Live_stream_informer
 * 
 * @version 	1.0.5	
 * @author	Ametov S.I.
 * @copyright	© 2013. All rights reserved. 
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

class modLSIHelper
{
	const URL_STREAM_API = "https://api.twitch.tv/kraken/streams/"; 
	
	public      $clients = array();
	public      $jsons = array();
        protected   $urls = array();
       
	function __construct($id_clients, $display = 0)
	{
            $this->clients = self::getId($id_clients);
            $this->urls =  self::getUrls($id_clients);
            $this->jsons = self::getJsonObjList($this->urls, $this->clients, $display);
	}
        
        static public function inizialize($theme = 0, $local = 1)  
        {
            $doc = JFactory::getDocument();
            $jversion = new JVersion();
            $ver = (version_compare($jversion->getShortVersion(), '3.2') == -1) ? 1 : 0;
            
            switch ($local):
                case 0: if ($ver == 1){$doc->addScript(JURI::root(true) ."/modules/mod_live_stream_informer/js/jquery-1.10.2.min.js");}
                         $doc->addScript(JURI::root(true) ."/modules/mod_live_stream_informer/js/jquery-ui-1.10.4.custom.min.js");
                    break;
                case 1: if ($ver == 1){$doc->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js");}
                         $doc->addScript("//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js");                         
                    break;
                default:break;    
            endswitch;           
            switch ($theme):
                case 0:  $doc->addStyleSheet(JURI::root(true) ."/modules/mod_live_stream_informer/css/jquery-ui-1.10.4.custom.min.css");
                    break;
                case 1:  $doc->addStyleSheet(JURI::root(true) ."/modules/mod_live_stream_informer/css/jquery-ui-bootstrap.min.css");               
                    break;
                case 2:  $doc->addStyleSheet(JURI::root(true) ."/modules/mod_live_stream_informer/css/jquery-ui-1.10.4.darkness.min.css");               
                    break;
                case 3:  $doc->addStyleSheet(JURI::root(true) ."/modules/mod_live_stream_informer/css/jquery-ui-1.10.4.lightness.min.css");               
                    break;
                default:break;
            endswitch;
            $doc->addScript(JURI::root(true) ."/modules/mod_live_stream_informer/js/jq-live-stream-info.min.js");    
            $doc->addStyleSheet(JURI::root(true) ."/modules/mod_live_stream_informer/css/live-stream-info.min.css"); 

        }
	
	static public function request($links)
	{           
            $mh = curl_multi_init(); 
            $curl_array = array(); 
            $timeout = 5;
            $headers = array("Accept: application/vnd.twitchtv.v3+json", "Client-ID: fvglvnix8as0ct9txt2g6e6rfuna65v");
            foreach($links as $i => $url) 
            { 
                $curl_array[$i] = curl_init($url); 
                curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, 1); 
                curl_setopt($curl_array[$i], CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_multi_add_handle($mh, $curl_array[$i]); 
            } 
            $running = NULL; 
            do 
            { 
                usleep(1000); 
                curl_multi_exec($mh, $running); 
            } while($running > 0); 

            $res = array(); 
            foreach($links as $i => $url) 
            { 
                $res[$i] = curl_multi_getcontent($curl_array[$i]); 
            } 

            foreach($links as $i => $url)
            { 
                curl_multi_remove_handle($mh, $curl_array[$i]); 
            } 
            curl_multi_close($mh);        
            return $res; 
	} 
	
	static protected function getJsonObjList($links, $clients, $display)
	{
            $json_list = array();
            $json_obj_list = array();

            $jsons = self::request($links);
            foreach ($jsons as $i => $json)
            {
                $json_list[$i] = json_decode($json);
            }
            $json_obj_list = array_combine($clients, $json_list);
            
            if ($display == 0) return self::unsetOffId($json_obj_list);
            
            return $json_obj_list;      
        }    
	      
        static public function unsetOffId($json_obj_list)
	{
            $live_id_list = array();
            
                foreach ($json_obj_list as $i => $item)
                {           
                    if ( isset($item) && is_object($item) )
                    {       
                        if ( isset($item->stream) || isset($item->error)  ) $live_id_list[$i] = $item;
                    }
                }     
            return $live_id_list; 
     
	}
             
        
	static protected function getId($id_clients)
	{
            $id_list = array();

            $id_clients_list = trim(strtolower($id_clients));
            $clients= explode(" ", $id_clients_list);
            foreach ($clients as $i => $client)
            {
                $id_list[$i] = trim($client);
                if ( empty($id_list[$i]) ) unset($id_list[$i]);
            }		
            return $id_list;
	}
	
	static protected function getUrls($id)
	{
            $urls = array();
            $tokens = self::getId($id);
            foreach($tokens as $i => $token)
            {
                $urls[$i] = self::URL_STREAM_API.$token;
            }
            return $urls;
	}
        
        static public function cutStr($str, $len, $end = "&hellip;")
        {
            $str = ucfirst($str);
            if (mb_strlen($str, "utf-8")<= $len || $len == 0)
                return $str;
            else
            {
                $str = mb_substr($str, 0, $len, "utf-8");
                return $str.$end;   
            }
        }
	static public function copyright()
        {            
            return base64_decode("PGRpdiBpZD0ibHNpLXBocC1wb3dlcmVkIiBjbGFzcz0ibHNpLXBvd2VyZWQiICA+PGEgaHJlZj0iaHR0cDovL21tb3RpbWVzLnJ1LyIgPlBvd2VyZWQgYnkgTU1PVElNRVMuUlU8L2E+PC9kaXY+");         
        }
        
        static public function formatstr($str) 
        {
            $str = trim($str);
            $str = stripslashes($str);
            $str = strip_tags($str);
            $str = htmlspecialchars($str);
            return $str;
        }
        static public function getIsAjaxRequest()
	{
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}
        
        public static function getContent($json_list, $char=0, $typePlayer = 0, $height=400, $width=600)
        {
            $content = "";
            if (sizeof($json_list) == 0) $content = "<div id='lsi-rows' class='lsi-note' ><p>Нет активных трансляций</p></div>";
            foreach ($json_list as $id => $item)
            {       
                    $shortId = self::cutStr((ucfirst($id)), $char);
                    $content .=  "<div id = 'lsi-rows'><span class = 'lsi-left-cell'>";
                    if ( isset($item) && is_object($item) && !isset($item->error) )
                    {      	
                        if ( isset($item->stream) ) 
                        {   
                            $display_name = htmlspecialchars($item->stream->channel->display_name);
                            $game = htmlspecialchars($item->stream->game, ENT_QUOTES);
                            $viewers = htmlspecialchars($item->stream->viewers);
                            $name = htmlspecialchars($item->stream->channel->name);
                            $status_txt = htmlspecialchars($item->stream->channel->status, ENT_QUOTES);
                            
                            $status = "LIVE"; $class = 'live';
                            $dialogForm = self::getDialogForm($typePlayer, $height, $width, $name, $item->stream->channel->logo, $game, $status_txt, $item->stream->channel->url, $id, $display_name);
                            
                            
                            $content .= " 
                                <button data-state = '1' data-client = '{$id}'  "
                                . "title='Канал:  {$display_name} Игра: {$game} Зрители: {$viewers}'"
                                . "id='lsi-id-{$id}'>".$shortId."</button></span><span class = 'lsi-right-cell'>"
                                . "<em class='{$class}'>{$status}</em></span>$dialogForm</div>";
                        }
                        else 
                        {
                            $status = "OFFLINE"; $class = 'offline'; 
                            $attr = "Нет активных трансляций";
                            
                              $content .= "<button data-state = '0' data-client = '{$id}' title = '{$attr}' id='lsi-id-{$id}' >".$shortId."</button></span><span  class = 'lsi-right-cell'>"
                                    . "<em class='{$class}'>{$status}</em></span></div>"; 
                          }               
                    }
                    else 
                    {
                        $status = htmlspecialchars($item->status);
                        $error = htmlspecialchars($item->error);
                        $message = htmlspecialchars($item->message, ENT_QUOTES);
                        
                        $attr = "Error {$status}: {$error} ({$message})";
                        $content .= "<button  data-state = '0' data-client = '{$id}' title = '{$attr}' id='lsi-id-{$id}' >".$shortId."</button></span><span title = '{$attr}' class = 'lsi-right-cell'><em id = 'status' class='error'>ERROR</em></span></div>";
                    }  
            }
            return $content;
        }
        static public function getDialogForm($typePlayer = 0, $height, $width, $client, $img, $game, $status, $clientURL, $id, $display_name)
        {
            
            if ($typePlayer == 0) $player = "<div  class = 'lsi-player'><object type='application/x-shockwave-flash' height='".$height."' width='".$width."' id='live_embed_player_flash' data='http://www.twitch.tv/widgets/live_embed_player.swf?channel=".$client."' bgcolor='#000000'><param name='allowFullScreen' value='true' /><param name='allowScriptAccess' value='always' /><param name='allowNetworking' value='all' /><param name='movie' value='http://www.twitch.tv/widgets/live_embed_player.swf' /><param name='flashvars' value='hostname=www.twitch.tv&channel=".$client."&auto_play=true&start_volume=25' /></object></div>";
            else 
            $player = "<div  class = 'lsi-player'><iframe id='player' type='text/html' width='" . $width . "' height='" . $height . "' src='http://www.twitch.tv/".$client."/hls' frameborder='0'></iframe></div>"; 
            
                  
            $info =  "<div id = 'lsi-info-streamer-{$id}' class = 'lsi-info-streamer'><a href='http://www.twitch.tv/{$client}' target='_blank'><div class = 'lsi-img-streamer' ><img width = '60px'  height = '60px' src='".$img."' alt='logo'></div></a>"
                    . "<div  class = 'lsi-token-streamer'>".$display_name."</div> "
                    . " <div class = 'lsi-desc-streamer'> играет в ".$game." <br/> ".$status." </div></div>";
            $hideBtn = "<div id = 'lsi-info-hide-{$id}' class='lsi-info-hide'><a class = 'lsi-show' href='javascript:void(0)'>Скрыть описание</a></div>";   
            
            $chat = "<div id = 'lsi-chat-{$id}' class = 'lsi-chat' style = 'display: none;'><iframe frameborder='0' scrolling='no' id='chat_embed' src='http://twitch.tv/chat/embed?channel=".$client."&amp;popout_chat=true' height='340' width='".$width."'></iframe></div>";
            $powered = self::copyright();
            
            return $dialog = "<div id='lsi-dialog-".$id."' style='display: none;' title='".$display_name." :: ".$game."'>$info $hideBtn $player $chat $powered</div>";  
            
        }

}