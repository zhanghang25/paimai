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
        M('auction_info')->where("id=".$auction_id)->setInc('success_times',1);
        if($price>=$high_price)
        {
            $data2['status'] = 2;
            M('auction_info')->data($data2)->where("id=".$auction_id)->save();
        }
        $data['status'] = 0;
        M('bidding')->data($data)->where("auction_id=".$auction_id)->save();
        $add_price = intval(I('post.add_price'));
        $anti_price = $add_price*0.15;
        $data['auction_id'] = $auction_id;
        $data['profit'] = $anti_price;
        $data['price'] = $price;
        $data['status'] = 1;
        $data['time'] = getMillisecond();
        $data1['test'] = $data['time'];
        M('bidding')->data($data)->add();
        $data1['msg'] = "恭喜您，加价成功！！";

        $data3['success_price']= $price;
        M('auction_info')->data($data3)->where("id=".$auction_id)->save();

        $data1['url'] = U('Home/AuctionShop/showShop',array('id'=>$auction_id));
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
     $data['data'] = M('bidding')->where('auction_id='.$auction_id)->order("time DESC")->select();
      $data['length'] = count($data['data']);
        $this->ajaxReturn($data);
    }


    public function convert()
    {
        //客户是否足够保证金验证
        $auction_id = I('post.auction_id');
        $bidding = M('bidding')->where('auction_id='.$auction_id)->select();
        if(empty($bidding))
        {
            $data['data'] = 2;
        }
        $this->ajaxReturn($data);

    }


}