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

class IndexController extends ComController
{
    public function index()
    {
        $time = time();
        //获得当天的最前面5个场次
        $timd_duplicate =date('Y-m-d',$time);
        $time_duplicate = strtotime($timd_duplicate);
        $time_add_one_day = $time_duplicate + 86400;
        $day_where['end_time'] = array('egt',$time_duplicate);
        $day_where['time'] = array('elt',$time_add_one_day);


        $sessions = M('session')->where($day_where)->order('time',ASC)->limit(0,5)->select();
        $this->assign('sessions',$sessions);



        //获取结束时间里现在时间最近的场次
        $session_where['end_time'] = array('egt',$time);

        $now_session = M('session')->where($session_where)->order('end_time ASC')->limit(0,1)->select();
        //获取当前场次，并取得拍卖信息

//        $where['end_time'] = array('elt',$now_session[0]['end_time']);
////        $where['session_time'] = array('elt',time());
//        $where['start_time'] = array('egt',$now_session[0]['time']);

        $where['session_id']  = array('eq',$now_session[0]['id']);
        $where['end_time'] = array('egt',$time);

        $list = M('auction_info')->where($where)->order("start_time ASC")->select();
        $count = count($list);
        $time = time();
        for($i = 0;$i<$count;$i++)
        {
            if ($list[$i]['status'] == 0&& $list[$i]['start_time']<=$time&&$list[$i]['end_time']>=$time)
            {
                $update_set['status'] = 1;
                M('auction_info')->data($update_set)->where('id='.$list[$i]['id'])->save();
                $list[$i]['status'] = 1;
            }


        }

//        $changid = $auction_infos[0]['session_id'];
//
//        $this->changid=$changid;

        if($now_session){
        $changid = $now_session[0]['id'];
        $this->assign('changid',$changid);

        $timedate = date('Y/m/d H:i:s',$now_session[0]['end_time']);
        }else{
            $timedate = "";
        }

        $this->assign('timedate',$timedate);
        $this->assign('auction_infos',$list);
        $this->display();
    }

    public function addHtml()
    {
        $session_id = I('post.session_id');
        $session = M('session')->where("id=".$session_id)->find();
        $session_start_time = $session['time'];

        $now_time = time();
        $session_end_time = $session['end_time'];

        $date = date("Y/m/d H:i:s",$session_end_time);
        $data['date'] = $date;

        if($now_time<=$session_end_time&&$now_time>=$session_start_time){


            $where['session_id']  = array('eq',$session_id);
            $where['end_time'] = array('egt',$now_time);
            $list = M('auction_info')->where($where)->order('start_time',ASC)->select();
            $count = count($list);
            $time = time();
            for($i = 0;$i<$count;$i++)
            {
                if ($list[$i]['status'] == 0&& $list[$i]['start_time']<=$time&&$list[$i]['end_time']>=$time)
                {
                    $update_set['status'] = 1;
                    M('auction_info')->data($update_set)->where('id='.$list[$i]['id'])->save();
                    $list[$i]['status'] = 1;
                }


            }

            $data['data'] = $list;
        }elseif($now_time>$session_end_time){
            $timedate = "已结束";
        }else{
            $timedate = "暂未开始";

            $list = M('auction_info')->where("session_id =".$session_id)->order('start_time',ASC)->select();

            $data['data'] = $list;
        }

        $timealert = M('session')->where('id='.$session_id)->find();
        $timedate = date('Y/m/d H:i:s',$timealert['end_time']);
        $data['timedate'] = $timedate;







        $this->ajaxReturn($data);

    }


    /*
    //一些前台DEMO
    //单页
    public function single($aid){

        $aid = intval($aid);
        $article = M('article')->where('aid='.$aid)->find();
        $this->assign('article',$article);
        $this->assign('nav',$aid);
        $this -> display();
    }
    //文章
    public function article($aid){

        $aid = intval($aid);
        $article = M('article')->where('aid='.$aid)->find();
        $sort = M('asort')->field('name,id')->where("id='{$article['sid']}'")->find();
        $this->assign('article',$article);
        $this->assign('sort',$sort);
        $this -> display();
    }

    //列表
    public function articlelist($sid='',$p=1){
        $sid = intval($sid);
        $p = intval($p)>=1?$p:1;
        $sort = M('asort')->field('name,id')->where("id='$sid'")->find();
        if(!$sort) {
            $this -> error('参数错误！');
        }
        $sorts = M('asort')->field('id')->where("id='$sid' or pid='$sid'")->select();
        $sids = array();
        foreach($sorts as $k=>$v){
            $sids[] = $v['id'];
        }
        $sids = implode(',',$sids);

        $m = M('article');
        $pagesize = 2;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量
        $count = $m->where("sid in($sids)")->count();
        $list  = $m->field('aid,title,description,thumbnail,t')->where("sid in($sids)")->order("aid desc")->limit($offset.','.$pagesize)->select();
        //echo $m->getlastsql();
        $params = array(
            'total_rows'=>$count, #(必须)
            'method'    =>'html', #(必须)
            'parameter' =>"/list-{$sid}-?.html",  #(必须)
            'now_page'  =>$p,  #(必须)
            'list_rows' =>$pagesize, #(可选) 默认为15
        );
        $page = new Page($params);
        $this->assign('list',$list);
        $this->assign('page',$page->show(1));
        $this->assign('sort',$sort);
        $this->assign('p',$p);
        $this->assign('n',$count);

        $this -> display();
    }
    */
}