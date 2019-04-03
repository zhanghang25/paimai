<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/4/2
     * Time: 16:30
     */
    namespace Home\Controller;
    use vendor\Page;
    class PaiController extends ComController{
        public function index(){
            $pai=M('bidding');
            $ud=M('auction_info');
            $data1=$pai->where('status=0 and user_id='.session('hid'))->select();
            $data2=$pai->where('status=1 and user_id='.session('hid'))->select();
            $data3=$pai->where('status=2 and user_id='.session('hid'))->select();
            foreach($data2 as $v){
                foreach ($data1 as $ke=>$value){
                    if ($v['auction_id']==$value['auction_id']){
                        unset($data1[]);
                    }
                }
            }
            $data=array();
            $data[]=$data1;
            $data[]=$data2;
            $data[]=$data3;
            foreach($data as $k=>$v){
                foreach($v as $key=>$value){
                 $dh=$ud->where ('id='.$value['auction_id'])->find();
                 $data[$k][$key]['thumbnail']=$dh['thumbnail'];
                }
            }
            $this->assign('data',$data);
            $this->display();
        }
    }