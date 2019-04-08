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
        public function _initialize ()
        {
            if (!session('hid')){
                $this->display('user/login');
                exit;
            
            }
        }
        public function index(){
            $pai=M('bidding');
            $ud=M('auction_info');
            $data1=$pai->join ("qw_auction_info u on qw_bidding.auction_id=u.id","LEFT")->field ('*,qw_bidding.user_id,qw_bidding.status,qw_bidding.id')->where('qw_bidding.status=0 and qw_bidding.user_id='.session('hid'))->group('auction_id')->select();
            $data2=$pai->join ("qw_auction_info u on qw_bidding.auction_id=u.id","LEFT")->field ('*,qw_bidding.user_id,qw_bidding.status')->where('qw_bidding.status=1 and qw_bidding.user_id='.session('hid'))->select();
            $data3=$pai->join ("qw_auction_info u on qw_bidding.auction_id=u.id","LEFT")->field ('*,qw_bidding.user_id,qw_bidding.status')->where('qw_bidding.status=2 and qw_bidding.user_id='.session('hid'))->group('qw_bidding.auction_id')->select();
            
            foreach($data2 as $k=>$v){
                foreach($data3 as $key=>$value){
                    
                    if ($v['auction_id']==$value['auction_id']){
                        unset($data3[$key]);
                    }
                }
            }
            
            foreach($data1 as $v){
               
                if (time()>$v['end_time']){
                    $data6=$pai->where ('auction_id='.$v['auction_id'])->order('price desc')->find ();
                    $data7=$pai->where ('auction_id='.$v['auction_id'])->save (array ('status'=>2,'time'=>getMillisecond()));
                    if ($data7){
                        $pai->where ('id='.$data6['id'])->save (array ('status'=>1,'time'=>getMillisecond()));
                    }
                    $data8=$pai->where ('auction_id='.$v['auction_id'])->group('user_id')->select();
                    $a='';
                    foreach($data8 as $va){
                        $a.=$va['user_id'].',';
                        
                    }
                    $a=rtrim ($a,',');
                   
                    $msg=array ();
                    
                    $msg['user_id']=$data6['user_id'];
                    $msg['status']=2;
                    $msg['auction_person']=$a;
                    $ud->where('id='.$v['auction_id'])->save($msg);
                }
            }
            $pai=M('bidding');
            $ud=M('auction_info');
            $data1=$pai->join ("qw_auction_info u on qw_bidding.auction_id=u.id","LEFT")->field ('*,qw_bidding.user_id,qw_bidding.status,qw_bidding.id')->where('qw_bidding.status=0 and qw_bidding.user_id='.session('hid'))->group('auction_id')->select();
            $data2=$pai->join ("qw_auction_info u on qw_bidding.auction_id=u.id","LEFT")->field ('*,qw_bidding.user_id,qw_bidding.status')->where('qw_bidding.status=1 and qw_bidding.user_id='.session('hid'))->select();
            $data3=$pai->join ("qw_auction_info u on qw_bidding.auction_id=u.id","LEFT")->field ('*,qw_bidding.user_id,qw_bidding.status')->where('qw_bidding.status=2 and qw_bidding.user_id='.session('hid'))->group('qw_bidding.auction_id')->select();
    
            foreach($data2 as $k=>$v){
                foreach($data3 as $key=>$value){
            
                    if ($v['auction_id']==$value['auction_id']){
                        unset($data3[$key]);
                    }
                }
            }
            $data=array ();
            $data[]=$data1;
            $data[]=$data2;
            $data[]=$data3;
            
            $this->assign('data',$data);
            $this->display();
        }
    }