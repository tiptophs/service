<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 系统邮件发送函数
 * @param string $tomail 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author tiptop
 */
function send_mail($tomail, $name, $subject, $body, $attachment = null){
    // 助手函数
    @import('PHPMailer.PHPMailerAutoload', EXTEND_PATH,'.php');

    // 实例化PHPMailer核心类
    $mail = new \PHPMailer;
    $mail->SMTPDebug = 0;									// SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息;
    $mail->isSMTP();										// 设定使用SMTP服务
    $mail->SMTPAuth = true;                               	// SMTP服务器的认证;
    $mail->CharSet = "UTF-8";							  	//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->SMTPSecure = 'ssl';          					// 使用安全协议;加密方式;                           //

    $mail->Host = 'smtp.aliyun.com;';  					 	// 设置默认使用阿里云服务器;
    $mail->Username = 'softlink@aliyun.com';          		// SMTP 用户名称;
    $mail->Password = 'HS214304anywn';                    	// SMTP 用户密码;
    $mail->Port = 465;                                   	// 端口,默认25;

    $mail->setFrom('softlink@aliyun.com', 'softlink官方助手');		    // 发件人;
    $mail->addReplyTo('softlink@aliyun.com', 'softlink官方助手');  	//回复邮箱;

    $mail->addAddress($tomail, $name);      						// 收件人;
    //$mail->addAddress('ellen@example.com');                 		// 第二个人地址;

    //$mail->￥('cc@example.com');									//抄送;
    //$mail->addBCC('bcc@example.com');								//密送;

    $mail->Subject = $subject;										//邮件主题
    $mail->Body    = $body;											//邮件内容
    $mail->isHTML(true);                                  			// email正文部分是否允许还有html;
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; //没有带html的内容;

    //$mail->addAttachment('/var/tmp/file.tar.gz');        			// 附件
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');   			// 附件
    if (is_array($attachment)) { 									// 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    if(!$mail->send()) {
        return false;
        //return '错误信息:'. $mail->ErrorInfo;
    } else {
        return true;
    }
}


/**
 * 获取相应位数随机数
 * @param int $len 生成随机数的位数
 * @return string	随机数zifuchaun
 * @author tiptop
 */
function get_random($len = 6 ){
    $min = pow(10 , ($len - 1));
    $max = pow(10, $len) - 1;
    return rand($min, $max);
}


/**
 * 修改图片尺寸
 * @param string $filename 文件名。
 * @param string $tmpname	文件路径，如上传中的临时目录。
 * @param int $xmax	修改后最大宽度。
 * @param int $ymax	修改后最大高度。
 * @return resource
 */
function resize_image($filename, $tmpname, $xmax, $ymax)
{
    $ext = explode(".", $filename);
    $ext = strtolower($ext[count($ext)-1]);

    if($ext == "jpg" || $ext == "jpeg")
        $im = imagecreatefromjpeg($tmpname);
    elseif($ext == "png")
        $im = imagecreatefrompng($tmpname);
    elseif($ext == "gif")
        $im = imagecreatefromgif($tmpname);

    $x = imagesx($im);
    $y = imagesy($im);

    /**
     * 禁止缩放处理部分
    if($x <= $xmax && $y <= $ymax)
    return $im;

    if($x >= $y) {
    $newx = $xmax;
    $newy = $newx * $y / $x;
    }
    else {
    $newy = $ymax;
    $newx = $x / $y * $newy;
    }**/

    $im2 = imagecreatetruecolor($xmax, $ymax);
    imagecopyresized($im2, $im, 0, 0, 0, 0, floor($xmax), floor($ymax), $x, $y);

    //删除原有图片
    unlink($tmpname);
    if($ext == "jpg" || $ext == "jpeg")
        $status = imagejpeg($im2, $tmpname);
    elseif($ext == "png")
        $status = imagepng($im2, $tmpname);
    elseif($ext == "gif")
        $status = imagegif($im2, $tmpname);
    //销毁
    imagedestroy($im);
    return $status;
}


/**
 * delDir()依次删除文件夹下的所有文件
 * @param unknown_type $dir
 */
function delDir($dir){
    //先删除目录下的文件：
    if(!is_dir($dir)){
        return true;exit();
    }
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹：
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}



