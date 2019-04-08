<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/4/8
     * Time: 15:42
     */
  
    namespace Home\Controller;
    use Vendor\Page;
    class  PayconfigController extends ComController{
        public function _initialize()
        {
            if (!session('hid')){
                $this->display('user/login');
                exit;
            
            }
        }
      public function zhifu(){
            $id=session('hid');
            $user=M('user');
            $data=$user->where ('id='.$id)->find ();
            $this->assign ('data',$data);
            $this->display ('User/zfgai');
      }
      public function zfgai(){
          $data=I('post.');
          $user=M('user');
          $id=session('hid');
          $name=$user->where("zhifu='".$data['zhifu']."'")->find();
          if ($name){
              return $this->ajaxReturn (array('code'=>0,'msg'=>'不可与当前支付宝号重复'));
          }
          $ty=$user->where('id='.$id)->save($data);
          if ($ty){
              return $this->ajaxReturn (array ('code'=>1,'msg'=>'修改成功','url'=>U('personal/info')));
          }
      }
      public function weixin(){
          $id=session('hid');
          $user=M('user');
          $data=$user->where ('id='.$id)->find ();
          $this->assign ('data',$data);
          $this->display ('User/wxgai');
      }
      public function wxgai(){
          $data=I('post.');
          $user=M('user');
          $id=session('hid');
          $name=$user->where("wexin='".$data['zhifu']."'")->find();
          if ($name){
              return $this->ajaxReturn (array('code'=>0,'msg'=>'不可与当前微信号重复'));
          }
          $ty=$user->where('id='.$id)->save($data);
          if ($ty){
              return $this->ajaxReturn (array ('code'=>1,'msg'=>'修改成功','url'=>U('personal/info')));
          }
      }
      public function tixian(){
            $this->display ('tixian/tixian');
      }
      
      public function diszhifubao(){
            $this->display ('pay/zhifubao');
      }
    
    
    
    }