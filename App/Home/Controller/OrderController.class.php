<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/3/27
     * Time: 10:37
     */
    
    namespace Home\Controller;
    use Vendor\Page;
    
    class OrderController extends ComController
    {
        public function orders(){

           $wait_pay = M('logistics')->where('uid='.session('hid').' and status = 0')->select();
           $this->assign('wait_pay',$wait_pay);

           $wait_deal =  M('logistics')->where('uid='.session('hid').' and status = 1')->select();
            $this->assign('wait_deal',$wait_deal);

           $wait_delivery =  M('logistics')->where('uid='.session('hid').' and status = 2')->select();
            $this->assign('wait_delivery',$wait_delivery);

           $total =  M('logistics')->where('uid='.session('hid'))->order("status asc,placetime desc")->select();
           $this->assign('total',$total);

            $this->display();
        }
    }