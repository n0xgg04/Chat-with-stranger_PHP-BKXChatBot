<?php

 function _noConnection(){
     _mainMenu();
 }
 
function isConnected(){
     global $userData;
     return $userData['isPaired'];
 }
  
function isWaiting(){
     global $userData;
     return $userData['isWaiting'];
 }
 
 
 
 //Chat Action

  function _mainMenu() {
      global $builder;
      global $bot;
      global $userId;
      $firstButton = $builder->createButton("postback", _BUTTON_START,'#NEW_CHAT'); 
	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_MALE,"#SEARCH_FOR_MALE",_IMAGE_MALE_ICON);
	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_FEMALE,"#SEARCH_FOR_FEMALE",_IMAGE_FEMALE_ICON);
	   $templateElement[] = $builder->createTemplateElement(_MENU_TITLE,_MENU_SUBTITLE, null, [
	  $firstButton], _IMAGE_CHAT);
	  $genericTemplate = $builder->createGenericTemplate($templateElement,$QR);
	  $bot->sendMessage($userId, $genericTemplate);
  } 
	
    function _sendNoti1(){
      global $builder;
      global $bot;
      global $userId;
      $firstButton = $builder->createButton("postback",_BUTTON_END_CHAT,'#END_CHAT'); 
	  $QR[] = $builder->createButtonForQuickReply(_BUTTON_REPORT,"#REPORT_USER",_IMAGE_REPORT_ICON);
	  $QR[] = $builder->createButtonForQuickReply(_BUTTON_BLOCK,"#BLOCK_USER",_IMAGE_BLOCK_ICON);
	   $templateElement[] = $builder->createTemplateElement(_MENU_END_CHAT_TITLE,_MENU_END_CHAT_SUBTITLE, null, [
	  $firstButton],_IMAGE_CHAT);
	  $genericTemplate = $builder->createGenericTemplate($templateElement,$QR);
	  $bot->sendMessage($userId, $genericTemplate);
    }
    
        function _sendNoti2(){
      global $builder;
      global $bot;
      global $userId;
      $firstButton = $builder->createButton("postback",_BUTTON_OUT_WAITING,'#END_CHAT'); 
	   $templateElement[] = $builder->createTemplateElement(_MENU_OUT_WAITING_TITLE,_MENU_OUT_WAITING_SUBTITLE, null, [
	  $firstButton],_IMAGE_CHAT);
	  $genericTemplate = $builder->createGenericTemplate($templateElement,null);
	  $bot->sendMessage($userId, $genericTemplate);
    }
    
    
         
     function _endChat(){
     global $builder;
     global $userId;
     global $bot;
     global $userData;
     
     if($userData['isWaiting']) _endChatNow(); else 
     if($userData['isPaired']){
	  $QR[] = $builder->createButtonForQuickReply(_BUTTON_ASK_END,"#END_CHAT_NOW",_IMAGE_SURE_ICON);
	   $templateElement[] = $builder->createTemplateElement(_ASK_END_TITLE,_ASK_END_SUBTITLE, null,null,null);
	  $genericTemplate = $builder->createGenericTemplate($templateElement,$QR);
	  $bot->sendMessage($userId, $genericTemplate);
        }else{
            _mainMenu();
        }
     }
    
    function _sendNotiOW($senderId,$subtitle,$title=null){
          global $builder;
          global $bot;
          $QR[] = $builder->createButtonForQuickReply(_SEARCH_RANDOM,"#NEW_CHAT",_IMAGE_RANDOM_ICON);
      	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_MALE,"#SEARCH_FOR_MALE",_IMAGE_MALE_ICON);
    	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_FEMALE,"#SEARCH_FOR_FEMALE",_IMAGE_FEMALE_ICON);
    	   if($title==null){
        	  $QR[] = $builder->createButtonForQuickReply(_QR_ACTION_BLOCK,"#BLOCK_USER",_IMAGE_BLOCK_ICON); 
        	  $QR[] = $builder->createButtonForQuickReply(_QR_ACTION_RATE,"#RATE",_IMAGE_RATING_ICON);
    	   }
    	  if($title==null) $templateElement[] = $builder->createTemplateElement(_NOTI_CHAT_ENDED,$subtitle, null,null,null); else
    	  $templateElement[] = $builder->createTemplateElement($title,$subtitle, null,null,null);
    	  $genericTemplate = $builder->createGenericTemplate($templateElement,$QR);
    	  $bot->sendMessage($senderId, $genericTemplate);
    }
    
    function _endChatNow(){
         global $userData;
         global $userId;
         $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
          
          if($userData['isPaired']){
          $pid=$userData['PID'];
          if(getPartnerID($userData['PID'])==$userId){
          updateUserData($pid,array('PID'=>"",'lPID'=>$userId,'isPaired'=>false,'isWaiting'=>false));
           _sendNotiOW($pid,_NOTI_PARTNER_ENDED);
          }
          updateUserData($userId,array('PID'=>"",'lPID'=>$pid,'isPaired'=>false,'isWaiting'=>false));
          $quer=$db->query("UPDATE users SET timeConn=null WHERE mid='".$userId."' OR mid='".$pid."' ");
           
            _sendNotiOW($userId,_NOTI_YOU_ENDED);
          }else if($userData['isWaiting']){
            updateUserData($userId,array('isWaiting'=>false));
            _sendNotiOW($userId,_NOTI_OW_SUBTITLE,_NOTI_OW_TITLE);
            $del = $db->query("DELETE FROM waiting_room WHERE uid='".$userId."' ");
          }else{
              _mainMenu();
          }
          $db->close();
    }
    
    
	
    function _searchFor($code){
        global $bot;
        global $userId;
        global $userFBData;
        
        if(!isUser($userId)&&$code!=0){
            _errorUser();
        }else{
             sleep(mt_rand(0,4));
             if($code==0) $coinneed=0;
             if($code==1) $coinneed=_CONFIG_COIN_NEED_TO_FIND_MALE;
             if($code==2) $coinneed=_CONFIG_COIN_NEED_TO_FIND_FEMALE;
             if($code>2) $coinneed=0;
             
             if(isConnected()) _sendNoti1(); else 
             if(isWaiting()){
                 _sendNoti2();
             }else{
                 if($coinneed>0) $userCoin=getCoin($userId); else $userCoin=5;
                 
                 if($userCoin<$coinneed&&$coinneed>0) _noEnoughCoin(); else{
                     $coinLeft=$userCoin-$coinneed;
                     if($coinneed!=0)_noti($userId,"Đã sử dụng $coinneed C !","Tài khoản của bạn còn $coinLeft C ","-",$QR);
                     setCoin($userId,$coinLeft);
                     _search($code);
                 }
             }
        }
     }
     
    function isVIP($uid){
        $ud=json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_Data/'.$uid.'.json'),true);
        return $ud['isVIP'];
    }     

    function createPersonId($name,$picurl){
         global $accessToken;
          $param = array(
                'name' => $name,
                'profile_picture_url' => $picurl
            );
             
            $url = 'https://graph.facebook.com/me/personas?access_token='.$accessToken;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, count($param));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param); 
            $result = curl_exec($ch);
            curl_close($ch);
            $re=json_decode($result,true);
            return $re['id'];
    }

     function _pair($id1,$id2){
          global $bot;
          $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
          $ar1=array('PID'=>$id2,'isPaired'=>true,'isWaiting'=>false);
          $ar2=array('PID'=>$id1,'isPaired'=>true,'isWaiting'=>false);
          
          $quer=$db->query("UPDATE users SET timeConn='".time()."' WHERE mid='".$id1."' OR mid='".$id2."' ");
          $db->close();
          
          //isVIP
          if(isVIP($id1)){
              _noti($id2,_NOTI_CONNECTED_VIP_TITLE,_NOTI_CONNECTED_VIP_SUBTITLE);
             $ar2['pisVIP']=true;
              
          }
          if(isVIP($id2)){
              _noti($id1,_NOTI_CONNECTED_VIP_TITLE,_NOTI_CONNECTED_VIP_SUBTITLE);
              $ar1['pisVIP']=true;
          }
          updateUserData($id1,$ar1,true);
          updateUserData($id2,$ar2,true);
          $bot->sendTextMessage($id1,'Xin chào',_PERSONASID_PARTNER);
          $bot->sendTextMessage($id2,'Xin chào',_PERSONASID_PARTNER);
     }
     
     
     function updateUserData($uid,$data,$updateChatCount=false){
         global $encryptCode;
          if(file_exists('./databin/user_Data/'.$uid.'.json')){
              $udata=json_decode(file_get_contents('./databin/user_Data/'.$uid.'.json'),true);
              foreach($data as $key => $value) $udata[$key]=$value;
              if(!isset($udata['chatCount'])) $udata['chatCount']=0;
              if($updateChatCount){
                   $udata['chatCount']++;
                   if($udata['chatCount']==20){
                       _noti($uid,'Hoàn thành nhiệm vụ : Thành viên mới !','Bạn được thưởng 20C, sử dụng C để sử dụng tính năng nâng cao nhé !');
                       setCoin($uid,getCoin($uid)+20);
                   }else if($udata['chatCount']==50){
                       _noti($uid,'Hoàn thành nhiệm vụ : Thành viên thân thiết !','Bạn được thưởng 50C, sử dụng C để sử dụng tính năng nâng cao nhé !');
                       setCoin($uid,getCoin($uid)+50);
                   }else if($udata['chatCount']==100){
                       _noti($uid,'Hoàn thành nhiệm vụ : Thành viên lâu năm !','Bạn được thưởng 100C, sử dụng C để sử dụng tính năng nâng cao nhé !');
                       setCoin($uid,getCoin($uid)+100);
                   }
               }
              file_put_contents('./databin/user_Data/'.$uid.'.json',json_encode($udata));
          }
     }
     
     function _noEnoughCoin(){
          global $builder;
          global $bot;
          global $userId;
          
          $QR[] = $builder->createButtonForQuickReply(_QR_COIN_EARN,"#COIN_EARN",_IMAGE_EARN_COIN);
          $QR[] = $builder->createButtonForQuickReply(_SEARCH_RANDOM,"#NEW_CHAT",_IMAGE_RANDOM_ICON);
      	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_MALE,"#SEARCH_FOR_MALE",_IMAGE_MALE_ICON);
    	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_FEMALE,"#SEARCH_FOR_FEMALE",_IMAGE_FEMALE_ICON);
    	  $templateElement[] = $builder->createTemplateElement(_WARN_COIN_NOENOUGH_TITLE,_WARN_COIN_NOENOUGH_SUBTITLE, null,null,null);
    	  $genericTemplate = $builder->createGenericTemplate($templateElement,$QR);
    	  $bot->sendMessage($userId, $genericTemplate);
     }
     
     function getPartnerID($uid){
         $ud=json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_Data/'.$uid.'.json'),true);
        return $ud['PID'];
    }     
    

     
     function _noti($senderId,$title,$subtitle,$img="",$qr=""){
          global $builder;
          global $bot;
          global $userId;
    	  if($img="") $templateElement[] = $builder->createTemplateElement($title,$subtitle, null, null, _IMAGE_CHAT); else
    	  if($img="-")
    	  $templateElement[] = $builder->createTemplateElement($title,$subtitle, null, null, null); else
    	  $templateElement[] = $builder->createTemplateElement($title,$subtitle, null, null, $img);
    	  if($qr!="") $genericTemplate = $builder->createGenericTemplate($templateElement,$qr); else
    	  $genericTemplate = $builder->createGenericTemplate($templateElement,null);
    	  $bot->sendMessage($senderId, $genericTemplate);
     }
     
     function _block($bid){
         global $userData;
         global $userId;
         _sendNotiOW($userId,_ACTION_BLOCKED_SUBTITILE,_ACTION_BLOCKED_TITLE);
         $blockList = (isset($userData['blocklist'])?$userData['blocklist']:array());
         $blockList[]=$bid;
         updateUserData($userId,array('blocklist'=>$blockList));
         $ud=json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_Data/'.$bid.'.json'),true);
         if(!isset($ud['blockedFromOther'])) $ud['blockedFromOther']=0;
         $ud['blockedFromOther']++;
         file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_Data/'.$bid.'.json',json_encode($ud));
     }
     
     function isUser($uid){
         global $bot;
          $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
          $sql="SELECT * FROM users WHERE mid='".$uid."' ";
          $quer=$db->query($sql);
          $ch=$quer->numRows();
          $db->close();
          return ($ch);
     }
     
     function _search($code){
         global $userId;
         global $userData;
         global $bot;
         global $builder;
         $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
         if(!isset($userData['blocklist'])) $userData['blocklist']=array();
         $mygender=$userData['gender'];
         $sql="SELECT * FROM waiting_room WHERE search_for='".$mygender."' OR search_for='0' AND uid!='".$userId."'";
         foreach($userData['blocklist'] as $bid){
             $sql.=" AND uid!='".$bid."'";
         }
         $sql.=" AND blocklist NOT LIKE '%".$userId."%' ORDER BY id DESC LIMIT 1 ";
         $quer=$db->query($sql);
         $check=$quer->numRows();
         if($check!=0){
             $partner=$quer->fetchArray();
             _noti($userId,_NOTI_CONNECTED_TITLE,_NOTI_CONNECTED_SUBTITLE);
             _noti($partner['uid'],_NOTI_CONNECTED_TITLE,_NOTI_CONNECTED_SUBTITLE);
             _pair($userId,$partner['uid']);
             $del = $db->query("DELETE FROM waiting_room WHERE id='".$partner['id']."' ");
           //  $gen=($code==1)?"nam":"nữ";
             
         }else{
             updateUserData($userId,array('isWaiting'=>true));
             $quer=$db->query('INSERT INTO waiting_room(uid,search_for,time,blocklist)VALUES(?,?,?,?)',$userId,$code,time(),json_encode($userData['blocklist']));
              $QR[] = $builder->createButtonForQuickReply(_QR_CANCEL_SEARCH,"#END_CHAT_NOW",_IMAGE_BLOCK_ICON); 
             _noti($userId,_NOTI_SEARCHING_TITLE,_NOTI_SEARCHING_SUBTITLE,"-",$QR);
         }
         $db->close();
     }
     
     
     function _coinEarn(){
         global $bot;
         global $userId;
         $bot->sendTextMessage($userId,"Làm cách nào để kiểm xu?\n\n1. Bạn có thể kiếm xu bằng cách tham gia cách event trên page hoặc group\n\n2. Điểm danh hàng ngày bằng cách nhấp điểm danh ở menu để nhận xu miễn phí mỗi ngày nhé !\n\n Nếu tổng số cuộc trò chuyện của bạn đạt mốc 20, 50, 100 thì sẽ nhận được 20 xu, 50 xu, 100 xu đấy nhé !");
     }
     
    function getCoin($uid){
         $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
         $quer=$db->query("SELECT coin FROM users WHERE mid='".$uid."' ");
         $data=$quer->fetchArray();
         $db->close();
         return $data['coin'];
    }
     
     
     function setCoin($uid,$coin){
         $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
         $quer=$db->query("UPDATE users SET coin='".$coin."' WHERE mid='".$uid."' ");
     }
     
     function _errorUser(){
         global $userId;
         _sendNotiOW($userId,_ERROR_1_SUBTITLE,_ERROR_1_TITLE);
     }
     
     function _AboutMe(){
          global $builder;
          global $bot;
          global $userId;
          global $userFBData;
             if(!isUser($userId)){
                  _errorUser();
                   exit();    
             }
          $firstButton = $builder->createButton("web_url", 'Trang cá nhân', "", _CONFIG_DOMAIN.'/bkx/user/profile.php?code='.urlencode(openssl_encrypt((string) json_encode(array('uid' => $userId,'time' => time())) , 'aes-256-cbc', "n0xgg04_bkx")));
          $QR[] = $builder->createButtonForQuickReply(_QR_COIN_EARN,"#COIN_EARN",_IMAGE_EARN_COIN);
          $QR[] = $builder->createButtonForQuickReply(_SEARCH_RANDOM,"#NEW_CHAT",_IMAGE_RANDOM_ICON);
      	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_MALE,"#SEARCH_FOR_MALE",_IMAGE_MALE_ICON);
    	  $QR[] = $builder->createButtonForQuickReply(_SEARCH_FOR_FEMALE,"#SEARCH_FOR_FEMALE",_IMAGE_FEMALE_ICON);
    	  $fullname=$userFBData['last_name'].' '.$userFBData['first_name'];
    	  $coin=getCoin($userId);
    	  $point='0/0';
    	  $templateElement[] = $builder->createTemplateElement($fullname,"Độ uy tín : $point\nXu: $coin C", null,[$firstButton],_IMAGE_USER_AM);
    	  $genericTemplate = $builder->createGenericTemplate($templateElement,$QR);
    	  $bot->sendMessage($userId, $genericTemplate);
     }
     
     
     
     
     
     
     function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}