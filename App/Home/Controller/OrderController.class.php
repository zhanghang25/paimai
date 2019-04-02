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

           $wait_pay = M('logistics')->where('uid='.session('hid').' and status = 0')->order("placetime desc")->select();
           $this->assign('wait_pay',$wait_pay);

           $wait_deal =  M('logistics')->where('uid='.session('hid').' and status = 1')->order("placetime desc")->select();
            $this->assign('wait_deal',$wait_deal);

           $wait_delivery =  M('logistics')->where('uid='.session('hid').' and status = 2')->order("placetime desc")->select();
            $this->assign('wait_delivery',$wait_delivery);

           $total =  M('logistics')->where('uid='.session('hid'))->order("status asc,placetime desc")->select();
           $this->assign('total',$total);

            $this->display();
        }

        public function oneStep()
        {
            $user = M('user')->where('id='.session('hid'))->find();
            $id=I ('get.id');
            $auction = M('logistics')->where('id='.$id)->find();

            $address=M('address');
            $msg=array ();
            $uid=session('hid');
            $msg['uid']=array('eq',$uid);
            $msg['default']=1;
            $address=$address->where($msg)->find();
            if (!$address){
                $address=array ();
            }
            for($i=0;$i<count($address);$i++)
            {
                $address[$i]['user_name'] = $user['name'];
                $address[$i]['phone'] = $user['phone_num'];
            }

            $this->assign('address',$address);
            $this->assign('user',$user);
            $this->assign('auction',$auction);
            error_reporting(0);
            header("Content-type: text/html; charset=utf-8");
            $pay_memberid = "10091";   //商户ID
            $pay_orderid = 56321231;    //订单号
            $pay_amount = "0.01";    //交易金额
            $pay_applydate = date("Y-m-d H:i:s");  //订单时间
            $pay_notifyurl = "http://www.yourdomain.com/demo/server.php";   //服务端返回地址
            $pay_callbackurl = "http://www.yourdomain.com/demo/page.php";  //页面跳转返回地址
            $Md5key = "wtxemijw85ugrqzng7s1lf01niz69qg4";   //密钥
            $tjurl = "http://api.kedeer.cn/Pay_Index.html";   //提交地址
            $pay_bankcode = "913";   //银行编码
//扫码
            $native = array(
                "pay_memberid" => $pay_memberid,
                "pay_orderid" => $pay_orderid,
                "pay_amount" => $pay_amount,
                "pay_applydate" => $pay_applydate,
                "pay_bankcode" => $pay_bankcode,
                "pay_notifyurl" => $pay_notifyurl,
                "pay_callbackurl" => $pay_callbackurl,
            );
            ksort($native);
            $md5str = "";
            foreach ($native as $key => $val) {
                $md5str = $md5str . $key . "=" . $val . "&";
            }
//echo($md5str . "key=" . $Md5key);
            $sign = strtoupper(md5($md5str . "key=" . $Md5key));
            $native["pay_md5sign"] = $sign;
            $native['pay_attach'] = "1234|456";
            $native['pay_productname'] ='VIP基础服务';
            $this->assign('native',$native);
            $this->assign('tjurl',$tjurl);
            $this->assign('pay_amount',$pay_amount);

            $this->display();
        }
    }