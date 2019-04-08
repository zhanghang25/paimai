<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/4/8
     * Time: 14:32
     */
    namespace Home\Controller;
    use Vendor\Page;
    class QianbaoController extends ComController{
        public function _initialize ()
        {
            if (!session('hid')){
                $this->display('user/login');
                exit;
            
            }
        }
        public function index(){
            $uid=session ('hid');
            $user=M('user');
            $account=M('account');
            $data=$user->where ('id='.$uid)->find ();
            $msg=array ();
            
            $date1=$account->where ('type=0')->Sum('amount');
            $date2=$account->where ('type=7')->Sum('amount');
            $date3=$account->where ('type=8')->Sum('amount');
            $date4=$account->where ('type=10')->Sum('amount');
            
            if (!$data1){
                $data1['amount']="0.00";
            }
            if (!$data2){
                $data2['amount']="0.00";
            }
            if (!$data3){
                $data3['amount']="0.00";
            }
            if (!$data4){
                $data4['amount']="0.00";
            }
            
            $this->assign ('data1',$data1);
            $this->assign ('data2',$data2);
            $this->assign ('data3',$data3);
            $this->assign ('data4',$data4);
            $this->assign ('data',$data);
            $this->display('qianbao/qianbao');
        }
    }