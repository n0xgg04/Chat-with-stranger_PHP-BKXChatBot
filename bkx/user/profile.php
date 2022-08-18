<!DOCTYPE html>
<head>
        <?php
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $oke=0;
            include $_SERVER['DOCUMENT_ROOT'].'/bkx/include/lang.php';
            if(isset($_GET['code'])){
                $json=openssl_decrypt(urldecode($_GET['code']), 'aes-256-cbc', "n0xgg04_bkx");
                $arr=json_decode($json,true);
                $uid=$arr['uid'];
                $oke=1;
                $timeToken=$arr['time'];
                if($timeToken+15*60<time()){
                    echo 'Timeout!';
                    exit();
                }
                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_FBdata/'.$uid.'.json')){
                    $ud=json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_FBdata/'.$uid.'.json'),true);
                }else{
                    $ud['first_name']='data';
                    $ud['last_name']="No";
                }
                
                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_Data/'.$uid.'.json')){
                    $usd=json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bkx/databin/user_Data/'.$uid.'.json'),true);
                }else{
                    echo 'Error !';
                    exit();
                }
            }
        ?>

    <title><?php echo $ud['last_name'].' '.$ud['first_name'];?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
</head>
<body>

    <section style="background-color: #eee;">
  <div class="container py-5">
   

    <div class="row">
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-body text-center">
            <img src="<?php echo $ud['profile_pic'];?>" alt="avatar"
              class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3"><?php echo $ud['last_name'].' '.$ud['first_name'];?></h5>
            <p class="text-muted mb-1">Điểm uy tín :</p>
            <p class="text-muted mb-1">Cấp bậc :</p>
            <p class="text-muted mb-4">Tài khoản : <?php
             include $_SERVER['DOCUMENT_ROOT'].'/bkx/include/connectDB.php';
             $db = new db(_DBHOST, _DBUSER, _DBPASS, _DBNAME);
             $quer=$db->query("SELECT coin FROM users WHERE mid='".$uid."' ");
             $isUser=$quer->numRows();
             if($isUser){
                 $data=$quer->fetchArray();
                 echo $data['coin'];
             }else{
                 echo '0';
             }
             $db->close();
            
            ?> C</p>
            <div class="d-flex justify-content-center mb-2">
              <a href="<?php echo _CONFIG_LINK_BOT;?>" class="btn btn-primary">Quay về bot</a>
              <button type="button" class="btn btn-outline-primary ms-1" data-toggle="modal" data-target="#exampleModal" data-whatever="">Sửa thông tin</button>
               <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form>
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Recipient:</label>
                            <input type="text" class="form-control" id="recipient-name">
                          </div>
                          <div class="form-group">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea class="form-control" id="message-text"></textarea>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Send message</button>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      <script type="text/javascript">
        $('#exampleModal').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget) // Button that triggered the modal
          var recipient = button.data('whatever') // Extract info from data-* attributes
          // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
          // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
          var modal = $(this)
          modal.find('.modal-title').text('New message to ' + recipient)
          modal.find('.modal-body input').val(recipient)
        })
      </script>
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Tên của bạn :</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $ud['last_name'].' '.$ud['first_name'];?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">UID</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php if($oke) echo $uid;?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Giới tính :</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php if(isset($ud['gender'])) echo($ud['gender']=='male')?"Nam":"Nữ"; else echo "Chưa gắn";?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Trường :</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php if(!empty($usd['userInfo']['school'])) echo $usd['userInfo']['school']; else echo 'Chưa đặt';?> </p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Số cuộc trò chuyện :</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php if(!empty($usd['chatCount'])) echo $cc=$usd['chatCount']; else echo 'Chưa đặt';?> </p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card mb-4 mb-md-0">
              <div class="card-body">
                <p class="mb-4"><span class="text-primary font-italic me-1">Thông số</span> 
                </p>
                <p class="mb-1" style="font-size: .77rem;">Tỉ lệ chặn</p>
                <?php
                   $ba=count($usd['blocklist']);
                   $rate=round($ba/$cc*100);
                   $brate=round($usd['blockedFromOther']/$cc*100);
                ?>
                <div class="progress rounded" style="height: 5px;">
                  <div class="progress-bar" role="progressbar" style="width: <?php echo $rate;?>%" aria-valuenow="<?php echo $rate;?>"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="mt-4 mb-1" style="font-size: .77rem;">Tỉ lệ bị chặn</p>
                <div class="progress rounded" style="height: 5px;">
                  <div class="progress-bar" role="progressbar" style="width: <?php echo $brate;?>%" aria-valuenow="<?php echo $brate;?>"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>

</html>