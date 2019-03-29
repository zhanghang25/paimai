<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/3/29
     * Time: 14:42
     */
    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class IntegralController extends ComController{
        public function index(){
            $data=M('integral');
            $data=$data->select ();
            $this->assign ('data',$data);
            
            return $this->display();
        }
        public function add(){
            return $this->display();
        }
        public function doadd(){
            $data=I ('post.');
            if (!$data['price']){
                $this->error ('积分商品价格必填');
            }
            if (!data['count']){
                $this->error ('商品库存必填');
            }
            if (!$data['freight']){
                $this->error ('商品运费必填');
            }
            $integral=M('integral');
            $f=$integral->add ($data);
            if ($f){
                $this->success ('商品添加成功，即将返回添加商品页面');
            }else{
                $this->error ('商品添加失败');
            }
            
        }
    }