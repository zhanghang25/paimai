<?php
/**
 *
 * 版权所有：素材火<qwadmin.sucaihuo.com>
 * 作    者：素材水<hanchuan@sucaihuo.com>
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class AuctionShopController extends ComController
{
    public function showShop()
    {
        if (empty(session('hid')))
        {
            $this->display('User/login');
        }else{

        $id = I('param.id');
        $time = time();
        $auction =  M('auction_info')->where("id=".$id)->find();
        if ($auction['status'] == 0&& $auction['start_time']<=$time&&$auction['end_time']>=$time)
        {
            $update_set['status'] = 1;
            M('auction_info')->data($update_set)->where('id='.$auction['id'])->save();
            $auction['status'] = 1;
        }
        $this->assign('auction',$auction);
        $this->display();
        }
    }

    public function setOneToTwo()
    {
        $auction_id = I('post.auction_id');

        $auction = M('auction_info')->where("id=".$auction_id)->find();

        if ($auction['status'] == 1)
        {
            $update_set['status'] = 2;
            M('auction_info')->data($update_set)->where('id='.$auction['id'])->save();
            
        }

    }

    public function add_bid()
    {

        $price = I('post.price');
        $auction_id = I('post.auction_id');
        $high_price = I('post.high_price');

        //当拍卖结束，用户还停留在加价页面，提示退出到主页
        $test_auction = M('auction_info')->where("id=".$auction_id)->find();
        if($test_auction['status'] == 2)
        {
            $data4['msg'] = "拍卖已结束，请参与下次竞拍！";
            $data4['url'] = U('Home/Index/index');
            $this->ajaxReturn($data4);
        }

        //拍卖次数加一
        M('auction_info')->where("id=".$auction_id)->setInc('success_times',1);

        //所有加价信息状态置0
        $data['status'] = 0;
        M('bidding')->data($data)->where("auction_id=".$auction_id)->save();
        $add_price = intval(I('post.add_price'));

        $data['auction_id'] = $auction_id;
        $data['profit'] = C("two_profit");
        $data['price'] = $price;
        $data['status'] = 1;
        $data['user_id'] = session('hid');
        $data['time'] = getMillisecond();

        M('bidding')->data($data)->add();
        $data1['msg'] = "恭喜您，加价成功！！";
        //加入返佣
        $biddings = M('bidding')->where('auction_id='.$auction_id)->order("time desc")->select();
        if(count($biddings)>1)
        {
            $user_id = $biddings[1]['user_id'];
            $user = M('user')->where('id='.$user_id)->find();
        //加入二级返佣
            M('user')->where('id='.$user_id)->setInc('available_balance',C('two_profit'));
        //加入一级返佣
            M('user')->where('id='.$user['parent_id'])->setInc('available_balance',C('one_profit'));

        }


        $data3['success_price']= $price;
        M('auction_info')->data($data3)->where("id=".$auction_id)->save();

        $data1['url'] = U('Home/AuctionShop/showShop',array('id'=>$auction_id));
        //达到最高成交价，拍卖结束
        if($price>=$high_price)
        {
            $data2['status'] = 2;
            M('auction_info')->data($data2)->where("id=".$auction_id)->save();
        }
        $this->ajaxReturn($data1);

    }

    public function per_time()
    {
        $auction_id = I('get.auction_id');
        $bidding_id = I('get.bidding_id');
        $status = I('get.status');
        $data['code'] = 1;
//        if($status == 2)
//        {
//            $data['code']  = 2;
//            $this->ajaxReturn($data);
//        }
     $data['data'] = M('bidding')->alias('b')->join("qw_user AS u ON b.user_id=u.id ")
                                   ->where('auction_id='.$auction_id)
                                   ->order("time DESC ")
                                   ->field("b.*,u.name")
                                   ->select();
      $data['length'] = count($data['data']);
        $this->ajaxReturn($data);
    }


    public function convert()
    {



        $res = I('post.res');
        $auction_id = I('post.auction_id');
        $auction = M('auction_info')->where('id='.$auction_id)->find();
        $user = M('user')->where('id='.session('hid'))->find();
        //判断用户保证金是否大于拍卖商品的保证金
        if($auction['guaranty']>$user['guaranty'])
        {
            $data['data'] = 0;
            $this->ajaxReturn($data);
        }
        //如果首次加价，并且保证金够，将保证金扣除到冻结金里
        if($res == 0)
        {
            $data1['guaranty'] = $user['guaranty']-$auction['guaranty'];
            $data1['freeze'] = $user['freeze']+$auction['guaranty'];
            M('user')->data($data1)->where('id='.session('hid'))->save();
        }


        $bidding = M('bidding')->where('auction_id='.$auction_id)->select();
        if(empty($bidding))
        {
            $data['data'] = 2;
        }
        $data['msg'] = "拍卖已结束！！";

        $this->ajaxReturn($data);

    }


}