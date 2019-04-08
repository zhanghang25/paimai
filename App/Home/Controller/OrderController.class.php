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

        public  function yu_e()
        {
            $address_id = I('post.address_id');
            $logistics_id = I('post.id');
            $logistic = M('logistics')->alias('l')
                ->join("left join qw_shop as s on l.sid=s.id")->where('l.id='.$logistics_id)->field('l.shop_price,s.guaranty')->find();
            $user = M('user')->where('id='.session('hid'))->find();
            if($user['available_balance']<$logistic['shop_price'])
            {
                $data1['code'] = 1;
                $data1['msg'] = "您的账户余额小于您的支付额，不能进行支付";

                $this->ajaxReturn($data1);
            }
            $data['address_id'] = $address_id;
            $data['status'] = 2;
            $test =  M('logistics')->data($data)->where('id='.$logistics_id)->save();

            M('user')->where('id='.session('hid'))->setDec('available_balance',$logistic['shop_price']);
            getAccount(session('hid'),time(),$logistic['shop_price'],4,1);


            $data1['code'] = 2;
            $data1['msg'] = "恭喜您，出价成功！";

            $this->ajaxReturn($data1);

        }


        public function orders(){

           $wait_pay = M('logistics')->where('uid='.session('hid').' and status = 1')->order("placetime desc")->select();
           $this->assign('wait_pay',$wait_pay);

           $wait_deal =  M('logistics')->where('uid='.session('hid').' and status = 2')->order("placetime desc")->select();
            $this->assign('wait_deal',$wait_deal);

           $wait_delivery =  M('logistics')->where('uid='.session('hid').' and status = 3')->order("placetime desc")->select();
            $this->assign('wait_delivery',$wait_delivery);

            $deliveried =  M('logistics')->where('uid='.session('hid').' and status = 4')->order("placetime desc")->select();
            $this->assign('deliveried',$deliveried);

           $total =  M('logistics')->where('uid='.session('hid'))->order("status asc,placetime desc")->select();
           $this->assign('total',$total);

            $this->display();
        }

        public function address()
        {
            $uio=I('get.id');
            $user=M('address');

            $data=$user->where ('uid='.session('hid'))->order ('id asc')->select();
            if (!$data){
                $data=array();
            }
            if (count ($data)==1){
                $user->where ('uid='.session ('hid'))->save (array('default'=>1));
            }elseif (count ($data)>1){
                $map=array();
                $r=session('hid');
                $map['uid']=array('eq',$r);
                $map['default']=array('eq',1);
                $b=$user->where($map)->find();

                if (!$b){
                    $datq=$user->where ('uid='.session('hid'))->find();
                    $user->where('id='.$datq['id'])->save(array('default'=>1));
                }
            }
            $data=$user->where ('uid='.session('hid'))->order ('id asc')->select();

            if (!$data){
                $data=array();
            }
            $this->assign ('data',$data);
            $this->assign ('id',$uio);
            $this->display ();
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

                $address['user_name'] = $user['name'];
                $address['phone'] = $user['phone_num'];


            $this->assign('address',$address);
            $this->assign('user',$user);
            $this->assign('auction',$auction);
            $this->assign('id',$id);
            error_reporting(0);
            header("Content-type: text/html; charset=utf-8");
            $pay_memberid = "10091";   //商户ID
            $pay_orderid = 'E'.date("YmdHis").rand(100000,999999);    //订单号
            $pay_amount = "11.00";    //交易金额
            $pay_applydate = date("Y-m-d H:i:s");  //订单时间
            $pay_notifyurl = "http://www.yourdomain.com/demo/server.php";   //服务端返回地址
            $pay_callbackurl = "http://www.yourdomain.com/demo/page.php";  //页面跳转返回地址
            $Md5key = "wtxemijw85ugrqzng7s1lf01niz69qg4";   //密钥
            $tjurl = "http://api.rg92q.cn/Pay_Index.html";   //提交地址
            $pay_bankcode = "915";   //银行编码
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