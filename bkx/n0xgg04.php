<?php
    //Chat with Strangers chatbot - N0xgg04's Project
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $hubVerifyToken = 'n0xgg04_chatb0t';
    if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
      echo $_REQUEST['hub_challenge'];
      exit;
    }
    
    $encryptCode="n0xgg04";

    $chatbotId=isset($_GET['chatbotId'])?$_GET['chatbotId']:"";
    $CBList=json_decode(file_get_contents('./server/list.json'),true);
    $accessToken = $CBList[$chatbotId]['accessToken']; 
    
    include_once 'autoload.php';
    include_once 'include/lang.php';
    include_once 'dist/Function.php';
    $bot = new \KaiserStudio5\ChatFramework($accessToken, TRUE);
    $builder = new \KaiserStudio5\MessageBuilder();
    $userId = $bot->getSenderId();
    $pageId= $bot->getPageId();
    include __DIR__.'/include/connectDB.php';
   

    //CACHED AUTO REFRESH AT 00:00 BY CRONJOB
    if(!file_exists('./databin/user_FBdata/'.$userId.'.json')){
      //  file_put_contents('./databin/user_FBdata/'.$userId.'.json',json_encode($bot->getUserData($userId)));
        $userFBData=$bot->getUserData($userId);
        file_put_contents('./databin/user_FBdata/'.$userId.'.json',json_encode($userFBData));
        if(!empty($userFBData['first_name'])){
	 $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
         $ugender=($userFBData['gender']=='male')?"1":"2";
         $fullname=$userFBData['last_name'].' '.$userFBData['first_name'];
         $fullname=preg_replace('/[^\p{L}\p{N}]+/u', ' ',$fullname);
         //Check exists
         $accounts = $db->query("SELECT mid FROM users WHERE mid='".$userId."';");
         if($accounts->numRows()==0)
         $insert = $db->query('INSERT INTO users (mid,fullname,profile_pic,gender) VALUES (?,?,?,?)', $userId, $fullname,(!empty($userFBData['profile_pic'])?$userFBData['profile_pic']:""), $ugender);
         else $update = $db->query('UPDATE users SET fullname=?, profile_pic=?, gender=? WHERE mid=? ',$fullname,(!empty($userFBData['profile_pic'])?$userFBData['profile_pic']:""), $ugender,$userId);
         
         
         $db->close();
	        
  }
   
   
    }else{
        $userFBData=json_decode(file_get_contents('./databin/user_FBdata/'.$userId.'.json'),true);
    }
    
    //SAVE ACCESS TOKEN FOR MULTI_SERVER
    if(!file_exists('./databin/user_accessToken/'.$userId.'.json')){
        file_put_contents('./databin/user_accessToken/'.$userId.'.json',$accessToken);
    }
    
    //NO SQL USER DATA
     if(!file_exists('./databin/user_Data/'.$userId.'.json')){
         $userData=array(
             "userId" => $userId,
             "isPaired" => false,
             "PID" => "",
             "checkPID" => false,
             "lPID" => "",
             "chatCount" => 0,
             "isVIP" => false,
             "myPersonID"=>null,
             "pisVIP" => false,
             "seen" => false,
             "blockedFromOther" => '0',
             "blocklist" => array(),
             "gender" => ($userFBData['gender']=='male'?"1":"2"),
             "isWaiting" => false,
             "userInfo" => array(
                    "school" => "",
                    "age" => "",
                    "interestedIn" => "male",
                    "bio" => ""
                 )
             );
         file_put_contents('./databin/user_Data/'.$userId.'.json',json_encode($userData));
      // include_once './include/connectDB.php';
      /*
         $ugender=($userFBData['gender']=='male')?"1":"2";
         $fullname=$userFBData['last_name'].' '.$userFBData['first_name'];
         $fullname=preg_replace('/[^\p{L}\p{N}]+/u', ' ',$fullname);
         if(isset($userFBData['profile_pic'])){
         $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
         
         //Check exists
         $accounts = $db->query("SELECT mid FROM users WHERE mid='".$userId."';");
         if($accounts->numRows()==0)
         $insert = $db->query('INSERT INTO users (mid,fullname,profile_pic,gender) VALUES (?,?,?,?)', $userId, $fullname,(!empty($userFBData['profile_pic'])?$userFBData['profile_pic']:""), $ugender);
         $db->close();
         }*/
     }else{
         $userData=json_decode(file_get_contents('./databin/user_Data/'.$userId.'.json'),true);
     }
     
     //check VIP
     if(isset($userData['isVIP'])) {
         if($userData['isVIP']) $personid=_VIP_USER_PID; else $personid=_PERSONASID_PARTNER; 
     }else $personid=_PERSONASID_PARTNER; 
     
         if($userData['pisVIP']&&isset($userFBData['profile_pic'])){
             if($userData['myPersonId']==null){
                 $personid=createPersonId('Người lạ',$userFBData['profile_pic']);
                 updateUserData($userId,array('myPersonId'=>$personid));
             }else $personid=$userData['myPersonId'];
         }
     
     
     
     
    //When user seen
    if ($bot->isRead) {
            if(!isset($userData['seen'])) $userData['seen']=0;
    	    if($userData['isPaired']&&!$userData['seen']){
    	        $bot->sendSeen($userData['PID']); 
    	        updateUserData($userId,array('seen'=>true));
    	    }
    	  exit();
    }
    
    //When user send sticker
    if($bot->isSticker){
        if($userData['isPaired']){
            $Type = $bot->getAttachmentsType(); 
    		$url_file = $bot->getMessageLink(); 
    		$upload = $builder->createUploadFile($Type, $url_file); 
    		$bot->sendMessage($userData['PID'], $upload,$personid);
             updateUserData($userData['PID'],array('seen'=>false));
        }else{
            _mainMenu();
        }
        exit();
    }
    
    //Where user send attachments
    if($bot->isAttachments){
        if($userData['isPaired']){
            $Type = $bot->getAttachmentsType(); 
            $url_file = $bot->getMessageLink(); 
            
            if($Type=='audio'){
                        $upload = $builder->createUploadFile($Type, $url_file); 
        		$bot->sendMessage($userData['PID'], $upload,$personid);
                        updateUserData($userData['PID'],array('seen'=>false));
            }else{
                    $link='bkx/user/attachment.php?uid='.$userId.'&code='.urlencode($url_file);
                    $link=str_replace('https','n0x',$link);
                    $link=str_replace('scontent','c3te',$link);
                    $link=str_replace('fbcdn','le0',$link);
                    $link=str_replace('net','an5',$link);
                    $bot->sendTextMessage($userData['PID'], "Người lạ gửi đính kèm ({$Type}), nhấp link "._CONFIG_DOMAIN."/{$link} để xem ! ");
                    updateUserData($userData['PID'],array('seen'=>false));
            }
        }else{
            _mainMenu();
        }
        exit();
    }
    
   //BOT LISTENING
	if ($bot->isText) {
	
	        
	
		$message = $bot->getMessageText();
		
		if($message[0] == '#'){
			if ($message == '#chat' || $message == '$batdau'){
			         $bot->TypingOn($userId);
				_mainMenu();
				$bot->TypingOff($userId);
			}else if($message == "#testData"){
			     
			   $bot->sendTextMessage($userId,$pageId);
			
			}else if($message == "#profile"){
			    $bot->TypingOn($userId);
			    _AboutMe();
			    $bot->TypingOff($userId);
			}else if($message == "#profile_pic_url"){
			     $bot->sendTextMessage($userId,$userFBData['profile_pic']);
			}else if($message == "#end"){
			                 $bot->TypingOn($userId);
			                 _endChat();
			                 $bot->TypingOff($userId);
			}
		}else{
		   if(!$userData['isPaired']){
    		    $bot->TypingOn($userId);
    		    _noConnection();
    		    $bot->TypingOff($userId);
		    }else{
		       if(!isset($userData['checkPID'])) $userData['checkPID']=false;
		       if(!$userData['checkPID']){
		          if(getPartnerID($userData['PID']!=$userId)){
		            updateUserData($userId,array('PID'=>"",'isPaired'=>false,'pisVIP'=>false));
		            _sendNotiOW($userId,_NOTI_PARTNER_ENDED);
		          }else updateUserData($userId,array('checkPID'=>true));
		        }else{
		        $bot->sendTextMessage($userData['PID'],$message,$personid);
		        updateUserData($userData['PID'],array('seen'=>false));
		        }
		    }
		  }
		exit();
	}
	
	
	
	
	if($bot->isPostBack || $bot->isQuickReply){
	    
	   	switch($bot->getPayload()){
	   	    case '#START_BOT':
	   	        $bot->TypingOn($userId);
	   	        _mainMenu();
	   	        $bot->TypingOff($userId);
	   	    break;
	   	    
	   	    case '#SEARCH_FOR_MALE' :
	   	        $bot->TypingOn($userId);
	   	        _searchFor(1);
	   	        $bot->TypingOff($userId);
	   	    break;
	   	    
	   	     case '#SEARCH_FOR_FEMALE' : 
	   	          $bot->TypingOn($userId);
	   	          _searchFor(2);
	   	          $bot->TypingOff($userId);
	   	     break;
	   	     
	   	     case '#NEW_CHAT' : 
	   	     $bot->TypingOn($userId);
	   	     _searchFor(0);
	   	     $bot->TypingOff($userId);
	   	     break;
	   	     
	   	     case '#END_CHAT' :
	   	     $bot->TypingOn($userId);
	   	        _endChat();
	   	      $bot->TypingOff($userId);
	   	     break;
	   	     
	   	     case '#BLOCK_USER':
	   	         //$bot->sendTextMessage($userId,$userData['lPID']);
	   	        _block($userData['lPID']);
	   	     break;
	   	     
	   	     case '#END_CHAT_NOW' :
	   	         $bot->TypingOn($userId);
	   	        _endChatNow();
	   	        $bot->TypingOff($userId);
	   	     break;
	   	     
	   	     case '#PROFILE':
	   	         _AboutMe();
	   	     break;
	   	     
	   	     case '#COIN_EARN' :
	   	       _coinEarn();
	   	     break;
	   	     
	   	     
	   	}
	   
	}
	
	
	
	

	
