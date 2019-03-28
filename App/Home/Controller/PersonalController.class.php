<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/3/27
     * Time: 15:31
     */
    namespace Home\Controller;
    use Vendor\Page;
    class  PersonalController extends ComController{
        public function _initialize()
        {
          if (!session('hid')){
              $this->display('user/login');
              exit;
              
          }
        }
        public function setup(){
            return $this->display('user/setup');
        }
        public function info(){
            return $this->display('user/info');
        }
        public function mgai(){
            return $this->display('user/mgai');
        }
        public function  editName(){
            $data=I('post.');
            $user=M('user');
            $id=session('hid');
            $name=$user->where("name='".$data['name']."'")->find();
            if ($name){
                return $this->ajaxReturn (array('code'=>0,'msg'=>'该用户名已被占用,请重新填写'));
            }
            $ty=$user->where('id='.$id)->save($data);
            if ($ty){
                return $this->ajaxReturn (array ('code'=>1,'msg'=>'修改成功','url'=>U('personal/info')));
            }
        }
        
    }