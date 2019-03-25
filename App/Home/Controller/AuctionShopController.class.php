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

    }

}