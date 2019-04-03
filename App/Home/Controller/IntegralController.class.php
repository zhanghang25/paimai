<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/4/1
     * Time: 13:31
     */
    namespace Home\Controller;
    use Think\Controller;
    use Vendor\Page;
    class IntegralController extends ComController{
        public function _initialize ()
        {
            if (!session('hid')){
                $this->display('user/login');
                exit;
        
            }
        }
        public function index(){
            
            $user=M ('user');
            $integral=M('integral');
            $uid=session ('hid');
            $fen=$user->where('id='.$uid)->find ();
            $data=$integral->where('status=1 and count>0')->select ();
            $this->assign('data',$data);
            $this->assign('fen',$fen);
            $this->display('personal/jifen');
        }
        public function lists(){
            $id=I ('get.id');
            $user=M ('integral');
            $data=$user->where ('id='.$id)->find();
            $data['detail']= htmlspecialchars_decode($data['detail']);
            $this->assign('data',$data);
            $this->display('jifen/xiang');
        }
        public function money(){
            $id=I ('get.id');
            $user=M ('integral');
            $address=M('address');
            $msg=array ();
            $uid=session('hid');
            $msg['uid']=array('eq',$uid);
            $msg['default']=1;
            $address=$address->where ($msg)->find();
            if (!$address){
                $address=array ();
            }
            $data=$user->where ('id='.$id)->find();
            $data['detail']= htmlspecialchars_decode($data['detail']);
            
            $this->assign('data',$data);
            $this->assign('address',$address);
            
            $this->display('jifen/money');
        }
        public function buys(){
           
            $data=I('post.');
            $user=M('user');
            $integral=M ('integral');
            $data1=$user->where ('id='.session ('hid'))->find ();
            $data2=$integral->where ('id='.$data['integral_id'])->find();
            if ((float)$data1['point']<(float)($data2['fen_price'])){
                $this->ajaxReturn (array ('code'=>0,'msg'=>'您的积分不足'));
            }
            if ((float)$data1['anti_money']<(float)$data2['e_price']){
                $this->ajaxReturn (array ('code'=>0,'msg'=>'您的反拍额不足'));
            }
            $address=M('address');
            $msg=array ();
            $msg['default']=array ('eq',1);
            $msg['uid']=session('hid');
            $address=$address->where ($msg)->find ();
            if (!address){
                $this->ajaxReturn (array ('code'=>0,'msg'=>'请设置您的收货地址'));
            }
            $money=array();
            $money['sid']=$data2['id'];
            $money['status']=1;
            $money['logistics_price']=$data2['freight'];
            $money['uid']=session('hid');
            $money['placetime']=time ();
            $money['shop_name']=$data2['name'];
            $money['pic']=$data2['thumbnail'];
            $money['uds']=1;
            $money['address_id']=$address['id'];
            $money['paytime']=time ();
            $money['bianhao']=date('Ymd',time()).uniqid ().rand (100,999);
            $logisit=M ('logistics');
            M()->startTrans();
            $poain=(float)($data1['point'])-(float)($data2['fen_price']);
            $anti_money=(float)($data1['anti_money'])-(float)($data2['e_price']);
            $sm=array ();
            $sm['point']=$poain;
            $sm['anti_money']=$anti_money;
            
            $up=$user->where ('id',session ('hid'))->save($sm);
            $inser=$logisit->add($money);
            if ($up&&$inser){
                M()->commit();
              $this->ajaxReturn (array ('code'=>1,'msg'=>'购买成功，请继续选购商品','url'=>U('integral/index')));
            }else{
                M()->rollback();
                $this->ajaxReturn (array ('code'=>0,'msg'=>'网络错误请稍后重试'));
            }
            
            
        }
        public function address(){
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
                    $data=$user->where ('uid='.session('hid'))->find();
                    $user->where('id='.$datq['id'])->save(array('default'=>1));
                }
            }
            $data=$user->where ('uid='.session('hid'))->order ('id asc')->select();
            if (!$data){
                $data=array();
            }
            $this->assign ('data',$data);
            $this->assign ('id',$uio);
            $this->display ('jifen/address');
        }
        public function article(){
            $id=I('get.id');
            $article=M('article');
            $data=$article->where ('aid='.$id)->find();
            if (!$data){
                $data=array();
            }
            $data["content"]=htmlspecialchars_decode($data['content']);
            $this->assign('data',$data);
            $this->display('user/1');
        }
        public function articles(){
            $id=I('get.id');
            $set=M('category');
            $article=M('article');
            $data=$set->where ('dir='.$id)->find ();
            if ($data){
                $data1=$article->where ('sid='.$data['id'])->find ();
            }
            if (!$data1){
                $data1=array();
            }
            $data1["content"]=htmlspecialchars_decode($data1['content']);
            
            $this->assign('data',$data1);
            
            $this->display('user/1');
         }
      
    }