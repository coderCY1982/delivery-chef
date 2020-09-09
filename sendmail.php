<?php

session_start();
// json 形式のデータを扱うための定義
header('Content-type: application/json');
// PHP5.1.0以上はタイムゾーンの定義が必須
date_default_timezone_set('Asia/Tokyo');
 
// --------------------------
// 個別設定項目（３つ）
// --------------------------
// 送信先メールアドレス
$to = 'zimatsuvc@gmail.com';
// メールタイトル
$subject = '【こだわりシェフ】お問い合わせがありました。';
// ドメイン（リファラチェックと送信元メールアドレスに利用）
$domain = 'https://kodawari-chef.com/lp';
 
//変数初期化
$errflg = 0;    // エラー判定フラグ
$dispmsg ='';  // 画面出力内容

function setPost($s) {
    $val = '';
    if(isset($_POST[$s])){ $val = htmlspecialchars($_POST[$s]); }
    return $val;
}

$company = setPost('company');
$name = setPost('name');
$phone = setPost('phone');
$content = setPost('message');
$referrer = setPost('referrer');

function pushItem($checkitems) {
    $uninputItems = '';
    foreach($checkitems as $key => $value) {
        if($value == '') {
            if($uninputItems != '') {
                $uninputItems.='・';
            }
            $uninputItems.=$key;
        }
    }
    return $uninputItems;
}
 
if(strpos($_SERVER['HTTP_REFERER'], $domain) === false){
    // リファラチェック
    $dispmsg = '<p id="errmsg">【リファラチェックエラー】お問い合わせフォームから入力されなかったため、メール送信できませんでした。</p>';
    $errflg = 1;
}
else if($name == '' || $phone == ''){
    //必須チェック
    $checkitems = array(
        "お名前" => $name,
        "お電話番号" => $phone
    );
    $uninputItems = pushItem($checkitems);
  
    $dispmsg = '<p id="errmsg">【エラー】'.$uninputItems.'を入力してください。</p>';
    $errflg = 1;
}
else{
    // メールデータ作成
    $subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject,'JIS','UTF-8'))."?=";
    $messageCom='以下のお客様からお問い合わせがありました。'."\n\n";
    $message= 'ーーーーーーーーー'."\n";
    $message.= '【会社名/施設名】：'.$company."\n";
    $message.= '【ご担当者様】 ：'.$name."\n";
    $message.= '【お電話番号】：'.$phone."\n";
    $message.= '【お問い合わせ内容】：'.$content."\n";
    $message.= 'ーーーーーーーーー';
    $messageCom.=$message;
    $messageCom= mb_convert_encoding($messageCom,'JIS','UTF-8');
    $fromName = mb_encode_mimeheader(mb_convert_encoding($name,'JIS','UTF-8'));
    $header ='From: '.$fromName.'<wordpress@'.$domain.'>'."\n";
    $header.='Reply-To: '.$email."\n";
    $header.='Content-Type:text/plain;charset=iso-2022-jp\nX-mailer: PHP/'.phpversion();
    // メール送信
    $retmail = mail($to,$subject,$messageCom,$header);
    
    if( $retmail ){
        $dispmsg ='<p class="success">お問い合わせありがとうございます。<br>お客様のお問い合わせは無事送信されました。</p>';
    }else{
        $dispmsg .= '<p id="errmsg">【エラー】メール送信に失敗しました。。</p>';
        $errflg = 1;
    }
}
 
// 処理結果を画面に戻す
$result = array('errflg'=>$errflg, 'dispmsg'=>$dispmsg, 'mailsend'=>$mailsend);
echo json_encode( $result );
 
// HTMLエスケープ処理
function hsc_utf8($str) {
    return htmlspecialchars($str, ENT_QUOTES,'UTF-8');
}
?>