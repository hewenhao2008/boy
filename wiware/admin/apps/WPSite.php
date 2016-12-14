<?php
define("WSITE_BASED_IR",ADMIN_ROOT_PATH."/templates/wsite/");
define("WWW_CONTENTS_DIR","/wiware/www/contents/");
//为站点数据存储
define("WP_WSITE_STORE_FILE",ADMIN_ROOT_PATH."/templates/wsite/wsite_data.json");
define("M_BASE_URL","http://m.wipark.cn");
define("M_BASE_DIR","/wiware/www/");
define("W_BASE_URL","http://w.wipark.cn");


function write_default_data(){
    $dataArr = array(
        //微站标题
        "wsite_title" => "微站标题",
        //成功获取时长后跳转网址
        "wsite_success_redirecturl" => "",
        //轮播图片
        "wsite_carousels" => array(
            "carousel_0" => array(),
            "carousel_1" => array(),
            "carousel_2" => array(),
            "carousel_3" => array()
        )
    );
    $dataJson = json_encode($dataArr);
    file_put_contents(WP_WSITE_STORE_FILE,$dataJson);
}

/**
 * 获取轮播图片
 */
function wsite_get_store(){
    $json = file_get_contents(WP_WSITE_STORE_FILE);
    $json = rtrim($json);
    $json = ltrim($json);
    //没有JSON则写入空的
    if(empty($json)) {
        write_default_data();
        $json = file_get_contents(WP_WSITE_STORE_FILE);
    }
    return json_decode($json,true);
}

/**
 * 写入轮播图片数据
 *
 * @param $megerData
 */
function wsite_write_store($newStore){
    $newStoreJson = json_encode($newStore);
    file_put_contents(WP_WSITE_STORE_FILE,$newStoreJson);
}

function render($app){
    if(is_dir(M_BASE_DIR)){
        $destDir = M_BASE_DIR;
        $indexFile = M_BASE_DIR."/index.html";
        $wwwPackDir = WSITE_BASED_IR."/www_pack/";
        exec("cp -r {$wwwPackDir}/* {$destDir}/");
        $wpWSiteStore = wsite_get_store();
        $contents = $app->view->fetch('wsite/preview.php',array(
            "wpWSiteStore" => $wpWSiteStore,
            "runEnv" => "_M_",
            "M_BASE_URL" => M_BASE_URL,
            "W_BASE_URL" => W_BASE_URL
        ));
        file_put_contents($indexFile,$contents);
    }else{
        return true;
    }
}


if(!file_exists(WP_WSITE_STORE_FILE)){
    write_default_data();
}

$app->get(
    '/wisite',
    $authenticateUser('user'),
    function () use ($app) {
        $wpWSiteStore = wsite_get_store();
        $app->render('wsite/home.php',array(
            "wpWSiteStore" => $wpWSiteStore,
            "wpSSiteStoreJson" => json_encode($wpWSiteStore)
        ));
    }
);

$app->get(
    '/wisite/init',
    $authenticateUser('user'),
    function () use ($app) {
        write_default_data();
        $app->response->redirect("/wsite");
    }
);

$app->get(
    '/wisite/preview',
    $authenticateUser('user'),
    function () use ($app) {
        $wpWSiteStore = wsite_get_store();
        $app->render('wsite/preview.php',array(
            "wpWSiteStore" => $wpWSiteStore,
            "runEnv" => "_P_"
        ));
    }
);


$app->post(
    '/wisite/editcarousel/',
    $authenticateUser('user'),
    function () use ($app) {
        $post = $app->request->post();
        if(empty($post)){
            die(json_encode(array("status"=>"error","参数错误")));
        }
        $nowStore = wsite_get_store();
        $carouselId = $post['carousel_id'];
        if(isset($nowStore["wsite_carousels"][$carouselId])){

            //加入白名单
            if($nowStore["wsite_carousels"][$carouselId]["carousel_redirecturl"] != $post['carousel_redirecturl']){
                if(!empty($post['carousel_redirecturl'])){
                    exec("/wiware/bin/whitedomainadd.sh {$post['carousel_redirecturl']}");
                }
                if(!empty($nowStore["wsite_carousels"][$carouselId]["carousel_redirecturl"])){
                    exec("/wiware/bin/whitedomaindel.sh {$nowStore["wsite_carousels"][$carouselId]["carousel_redirecturl"]}");
                }
            }

            $nowStore["wsite_carousels"][$carouselId]["carousel_redirecturl"] = isset($post['carousel_redirecturl'])?$post['carousel_redirecturl']:"";


            $nowStore["wsite_carousels"][$carouselId]["carousel_showbtn"] = isset($post['carousel_showbtn'])?$post['carousel_showbtn']:0;
            wsite_write_store($nowStore);
            render($app);
            die(json_encode(array("status"=>"ok","rsp"=>"success")));
        }else{
            die(json_encode(array("status"=>"error","rsp"=>"要更新的轮播图片ID不存在")));
        }
    }
);



$app->post(
    '/wisite/basicedit/',
    $authenticateUser('user'),
    function () use ($app) {
        $post = $app->request->post();
        if(empty($post)){
            die(json_encode(array("status"=>"error","参数错误")));
        }
        $nowStore = wsite_get_store();
        $nowStore['wsite_title'] = $post['wsite_title'];
        //加入白名单并移除原来的
        if($nowStore['wsite_success_redirecturl'] != $post['wsite_success_redirecturl']){
            if(!empty($post['wsite_success_redirecturl'])){
                exec("/wiware/bin/whitedomainadd.sh {$post['wsite_success_redirecturl']}");
            }
            if(!empty($nowStore['wsite_success_redirecturl'])){
                exec("/wiware/bin/whitedomaindel.sh {$nowStore['wsite_success_redirecturl']}");
            }
        }
        $nowStore['wsite_success_redirecturl'] = $post['wsite_success_redirecturl'];
        wsite_write_store($nowStore);
        render($app);
        die(json_encode(array("status"=>"ok","rsp"=>"success")));
    }
);

$app->post(
    '/wisite/remove/',
    $authenticateUser('user'),
    function () use ($app) {
        $carouselId = $app->request->post("carousel_id",false);
        if(false === $carouselId){
            die(json_encode(array("status"=>"error","rsp"=>"提交的参数错误")));
        }
        $nowStore = wsite_get_store();
        if(isset($nowStore["wsite_carousels"][$carouselId])){
            $nowStore["wsite_carousels"][$carouselId] = array();
            wsite_write_store($nowStore);
            render($app);
            die(json_encode(array("status"=>"ok","rsp"=>"success")));
        }else{
            die(json_encode(array("status"=>"error","rsp"=>"要更新的轮播图片ID不存在")));
        }
    }
);


$app->post(
    '/wisite/upload/',
    $authenticateUser('user'),
    function () use ($app) {

        $allowExt = array("png","jpeg","gif","jpg","bmp");

        if(empty($_FILES)){
            die(json_encode(array("status"=>"error","rsp"=>"上传图片失败，可能是你上传的文件过大导致了该问题，请尝试使用500k以内的图片")));
        }

        $carouselId = $app->request->post("carousel_id",false);
        if(false === $carouselId){
            die(json_encode(array("status"=>"error","rsp"=>"轮播ID未提交")));
        }

        $uploadFileInfo = pathinfo($_FILES['file']['name']);

        $fileExt = strtolower($uploadFileInfo['extension']);

        if(!in_array($fileExt,$allowExt)){
            die(json_encode(array("status"=>"error","rsp"=>"上传的文件不是图片文件，请上传jpeg,jpg,png,bmp,gif格式的图片")));
        }

        //限制图片为500Kb
        if($_FILES['file']['size'] > 800000){
            die(json_encode(array("status"=>"error","rsp"=>"上传图片失败：文件大小超过800K。请使用800K以内的图片")));
        }


        if( function_exists('getimagesize') && !@getimagesize($_FILES['file']['tmp_name'])){
            die(json_encode(array("status"=>"error","rsp"=>"上传的文件不是图片文件，请上传jpeg,jpg,png,bmp,gif格式的图片")));
        }

        if(!is_dir(WWW_CONTENTS_DIR)){
            mkdir(WWW_CONTENTS_DIR);
        }

        $imgDestDir = WWW_CONTENTS_DIR."/images/";

        if(!is_dir($imgDestDir)){
            mkdir($imgDestDir);
        }

        $imgDestFile=$imgDestDir."{$carouselId}.{$fileExt}";

        if(move_uploaded_file($_FILES['file']['tmp_name'],$imgDestFile)){

            $carousel = array(
                "carousel_img" => "{$carouselId}.{$fileExt}",
                "carousel_redirecturl" => "",
                "carousel_showbtn" => 0
            );

            $nowStore = wsite_get_store();
            $nowStore["wsite_carousels"][$carouselId] = $carousel;
            wsite_write_store($nowStore);
            $result = array(
                "status" => "ok",
                "rsp" => $carousel
            );
            render($app);
            echo json_encode($result);
        }else{
            $result = array(
                "status" => "error",
                "rsp" => "无法获取到上传文件:x022"
            );
            echo json_encode($result);
        }
    }
);
?>