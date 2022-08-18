<?php
        include $_SERVER['DOCUMENT_ROOT'].'/bkx/include/lang.php';
	$link=urldecode($_GET['code']);
	$link=str_replace('n0x','https',$link);
        $link=str_replace('c3te','scontent',$link);
        $link=str_replace('le0','fbcdn',$link);
        $url=str_replace('an5','net',$link);
	
?>

<!DOCTYPE html>
<head>
    <title>Đính kèm từ người lạ</title>
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
            
            <h5 class="my-3">Bạn có muốn mở tệp đính kèm :</h5>
            <p class="text-muted mb-1">Đính kèm này được gửi từ người lạ, BKX ChatBOT không chịu trách nhiệm nếu có nội dung xấu.</p>
            
            <div class="d-flex justify-content-center mb-2">
              <a href="<?php echo _CONFIG_LINK_BOT;?>" class="btn btn-primary">Quay về bot</a>
              <a class="btn btn-outline-primary ms-1" href="<?php echo $url;?>">Xem đính kèm</a>
              
            </div>
          </div>
        </div>
      </div>
     
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">UID của bạn :</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $_GET['uid'];?></p>
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