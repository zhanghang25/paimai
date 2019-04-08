<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 2019/3/14
 * Time: 9:17
 */

namespace Qwadmin\Controller;


class OrderController extends ComController
{
    public function edit()
    {
        $id = I('get.aid');
        $this->assign('id',$id);

        $logistics = M('logistics')->where("id=".$id)->find();

        $this->assign('logistics',$logistics);
        if($logistics['user_id']){

            $success_man = M('user')->field('name')->where('id='.$logistics['user_id'])->find();
            //给成交人姓名赋值

            $this->assign('success_man',$success_man['name']);
        }
        if($logistics['auction_person']){

            $tmp = $logistics['auction_person'];
            $ids = explode(',',$tmp);
            $where['id'] = array('in',$ids);

            $part_man = M('user')->field('name')->where($where)->select();
            $particiment = '';
            for ($i = 0;$i < count($part_man);$i++)
            {
                if($i == 0){

                    $particiment .= $part_man[$i]['name'];
                }else{

                    $particiment .= ','.$part_man[$i]['name'];
                }
            }
            //给参拍人姓名赋值
            $this->assign('particiment',$particiment);
        }

        $sessions = M('session')->select();
        $this->assign('sessions',$sessions);
        $this->display('form');
    }

    public function auxtion_info()
    {

        $ids = I('post.ids');
        $start_time = I('post.start_time');
        $end_time = I('post.end_time');
        $session_time = intval(I('post.session_time'));

        $session_id = I('post.session_id');
        $end_time = strtotime($end_time);
        $start_time = strtotime($start_time);
        if($end_time<$start_time)
        {
            $this->ajaxReturn("您的拍卖结束时间小于了开始时间");
        }else{
            if ($end_time-$start_time>3600){
                $this->ajaxReturn("您的结束时间和开始时间间隔大于1小时");
            }
            if ($start_time>$session_time)
            {
                if($start_time-$session_time>3600)
                {
                    $this->ajaxReturn("您的开始时间超出场次时间");
                }

                if($end_time-$session_time>3600)
                {
                    $this->ajaxReturn("您的结束时间超出场次时间");
                }

            }else{
                $this->ajaxReturn("您的开始时间超出场次开始时间");
            }

        }
        $session = M('session')->where("id=".$session_id)->find();
        foreach ( $ids as $id )
        {
            //加入订单表信息
            $logistics = M('logistics')->where("id=".$id)->find();
            $auction['logistics_name'] = $logistics['name'];
            $auction['logistics_time'] = $logistics['addtime'];
            $auction['logistics_status'] = $logistics['status'];
            $auction['thumbnail'] = $logistics['thumbnail'];
            $auction['carousel_figure1'] = $logistics['carousel_figure1'];
            $auction['carousel_figure2'] = $logistics['carousel_figure2'];
            $auction['carousel_figure3'] = $logistics['carousel_figure3'];
            $auction['detail'] = $logistics['detail'];
            $auction['start_price'] = $logistics['start_price'];
            $auction['additional_shot_range'] = $logistics['additional_shot_range'];
            $auction['reference_price'] = $logistics['reference_price'];
            $auction['high_price'] = $logistics['high_price'];
            $auction['guaranty'] = $logistics['guaranty'];
            //加入场次表信息

            $auction['session_status'] = $session['status'];
            $auction['session_name'] = $session['name'];
            $auction['session_time'] = $session['time'];
            $auction['start_time'] = $start_time;
            $auction['end_time'] = $end_time;
            M('logistics')->data($auction)->add();

        }

        $this->ajaxReturn("恭喜你，成功添加拍卖信息");



//        return json_encode($ids);

    }

    public function index($sid = 0, $p = 1)
    {


        $p = intval($p) > 0 ? $p : 1;

        $logistics = M('logistics');
        $pagesize = 15  ;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $prefix = C('DB_PREFIX');
//        $sid = isset($_GET['sid']) ? $_GET['sid'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $keyword1 = isset($_GET['keyword1']) ? htmlentities($_GET['keyword1']) : '';

        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $where = '1 = 1 ';
//        if ($sid) {
//            $sids_array = category_get_sons($sid);
//            $sids = implode(',',$sids_array);
//            $where .= "and {$prefix}logistics.sid in ($sids) ";
//        }
        if ($keyword) {
            $where .= " and {$prefix}logistics.bianhao = '{$keyword}' ";
        }

        if(!empty($keyword1)){
            $where .= " and {$prefix}logistics.status = ".$keyword1;
        }
        //默认按照时间降序
        $orderby = "placetime desc";
        if ($order == "asc") {

            $orderby = "placetime asc";
        }



        $count = $logistics->where($where)->count();
        $list = $logistics->field("{$prefix}logistics.*")->where($where)->order($orderby)->limit($offset . ',' . $pagesize)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();

//        print_r($sessions);
//        exit;
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }


    

    public function update($aid = 0)
    {

        $aid = intval(I('post.aid'));
        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['thumbnail'] = I('post.thumbnail', '', 'strip_tags');
        $data['carousel_figure1'] = I('post.carousel_figure1', '', 'strip_tags');
        $data['carousel_figure2'] = I('post.carousel_figure2', '', 'strip_tags');
        $data['carousel_figure3'] = I('post.carousel_figure3', '', 'strip_tags');
        $data['start_price'] = isset($_POST['start_price']) ? intval($_POST['start_price']) : 0;
        $data['additional_shot_range'] = isset($_POST['additional_shot_range']) ? intval($_POST['additional_shot_range']) : 0;
        $data['reference_price'] = isset($_POST['reference_price']) ? intval($_POST['reference_price']) : 0;
        $data['high_price'] = isset($_POST['high_price']) ? intval($_POST['high_price']) : 0;
        $data['guaranty'] = isset($_POST['guaranty']) ? intval($_POST['guaranty']) : 0;
        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $data['start_time'] = strtotime($_POST['start_time']);
        $data['end_time'] = strtotime($_POST['end_time']);
        $data['session_id'] = $_POST['session_id'];
        $tmp_data = M('session')->where("id=".$data['session_id'])->find();
        $data['session_time'] = $tmp_data['time'];
        $data['detail'] = isset($_POST['detail']) ? $_POST['detail'] : false;
        $data['addtime'] = time();
        $data['short_show'] = $_POST['short_show'];

        if ($aid) {
            M('logistics')->data($data)->where('id=' . $aid)->save();
            addlog('编辑订单，AID：' . $aid);
            $this->success('恭喜！订单编辑成功！');
        } else {
            $aid = M('logistics')->data($data)->add();
            if ($aid) {
                addlog('新增订单，AID：' . $aid);
                $this->success('恭喜！订单新增成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }

        }
    }

    public function del()
    {

        $aids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if ($aids) {
            if (is_array($aids)) {
                $aids = implode(',', $aids);
                $map['id'] = array('in', $aids);
            } else {
                $map = 'id=' . $aids;
            }
            if (M('logistics')->where($map)->delete()) {
                addlog('删除订单，AID：' . $aids);
                $this->success('恭喜，订单删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            $this->error('参数错误！');
        }

    }

}