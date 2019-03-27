<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/3/25
     * Time: 11:16
     */
    namespace Home\Controller;
    use Vendor\Page;
    class UserController extends  ComController{
        public function login(){
            $this->display();
        }
        public function dologin(){
            $data=I('post.');
            $user=M('user');
            $password=md5(I('post.password').config['salt']);
            $data=$user->where ("phone_num=".$data['mobile']." and status=1"." and password='".$password."'")->find ();
            
            if ($data){
                
                session('hid',$data['id']);
                $this->ajaxReturn (array('code'=>1,'msg'=>'登陆成功','url'=>U('home/index/index')));
            }else{
                $this->ajaxReturn (array('code'=>0,'msg'=>'账号或密码错误请重试'));
            }
        }
            public function sms(){
                $statusStr = array(
                    "0" => "短信发送成功",
                    "-1" => "网络错误",
                    "-2" => "网络错误",
                    "30" => "密码错误",
                    "40" => "网络错误",
                    "41" => "网络错误",
                    "42" => "网络错误",
                    "43" => "网络错误",
                    "50" => "网络错误"
                );
                $phone=I('post.phone');
                $user=M('user');
                $ph=$user->where('phone_num='.$phone)->find();
                if ($ph){
                    return $this->ajaxReturn (array('code'=>800,'msg'=>'手机号已被占用'));
                }
                if (session($phone)&&session($phone)['num']>4){
                    return $this->ajaxReturn (array('code'=>900,'msg'=>'当前手机号发送验证码过多,请稍后重试'));
                }else{
                    $num=1;
                }
                
                $smsapi = "http://www.smsbao.com/"; //短信网关
                $user = "linxipai"; //短信平台帐号
                $sms=rand(10000,99999);
                $pass = md5("linxipai"); //短信平台密码
                $content="您的验证码为{$sms}，在10分钟内有效";//要发送的短信内容
                $phone = $phone;
                
                $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
                $result =file_get_contents($sendurl) ;
               
                if (!$result){
                       $num=session($phone)['num']+1;
                       session($phone,array('code'=>$sms,'num'=>$num,'time'=>time()+600));
                return $this->ajaxReturn (array('code'=>1,'msg'=>$statusStr[$result]));
                }else{
                    return $this->ajaxReturn (array('code'=>0,'msg'=>$statusStr[$result]));
                }
                
                
            }
            public function authentication(){
                $data=I('post.');
                if (!preg_match_all("/^1[34578]\d{9}$/",$data['mobile'])){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'请输入正确的手机号'));
                }
                if ($data['code']!=session($data['mobile'])['code']||session($data['mobile'])['time']<time()){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'您的验证码不正确'));
                }
                if (!preg_match_all('/^[a-zA-Z0-9\~\!\@\#\$\%\^\&\*\_\+\{\}\:\"\|\<\>\?\-\=\;\'\,\.\/]{6,15}$/',$data['password'])){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'请输入数字字母特殊符号6-15位'));
                }
                $user=M('user');
                $name=$user->where("name='".$data['name']."'")->find();
                $invite_code=$user->where("invite_code='".$data['invite_code']."'")->find();
                if ($name){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'用户昵称已被占用'));
                }
                if(!trim($data['name'])){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'用户昵称不能为空'));
                }
                if (!trim ($data['invite_code'])){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'邀请码不能为空'));
                }
                
                if (!$invite_code){
                    return $this->ajaxReturn (array('code'=>0,'msg'=>'请输入正确的邀请码'));
                }
                
                $lt=array();
                $lt['name']=$data['name'];
                $lt['invite_code']=substr(md5(uniqid(microtime(true),true)),1,8);
                $lt['status']=1;
                $lt['parent_id']=$invite_code['id'];
                $lt['create_time']=time();
                $lt['phone_num']=$data['mobile'];
                $lt['ming_password']=I('post.password');
                $lt['password']=md5(I('post.password').config['salt']);
                $io=M('user')->data($lt)->add();
                if ($io){
                    return $this->ajaxReturn (array('code'=>1,'msg'=>'注册成功请稍后','url'=>U('user/login')));
                }else{
                    return $this->ajaxReturn(array('code'=>0,'msg'=>'未知错误'));
                }
            }
            
    }