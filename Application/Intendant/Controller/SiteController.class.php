<?php
namespace Intendant\Controller;
use Think\Controller;
use Think\Auth;
class SiteController extends AuthController {
	//后台菜单显示
	public function menu(){
		$allRulekey = 'all-rule.cache';
		$menu = S($allMenukey);
		if($menu == null){
			$menu = M('AuthRule')->order('sort asc')->select();
			$menu = recursive($menu);
			S($allMenukey,$menu,C('AUTH_MENU_TIME'));
		}
		$this->menu = $menu;
		$this->display();
	}
	//添加或修改菜单
	public function addEditMenu(){
		if(IS_POST && I('addEditMenu')){//处理提交
			$rules = array(
				array('title','require',-1), //菜单名称不能为空
				array('name','require',-2), //菜单规则不能为空
				array('title','',-3,0,'unique',1), //菜单名称唯一性
				array('name','',-4,0,'unique',1), //菜单规则唯一性
			);
			$tableRule = M("AuthRule");
			if($tableRule->validate($rules)->create()){
				if(I('id','','int')){//修改菜单
					$status = $tableRule->save();
					$msg = returnMsg("菜单修改成功","菜单修改失败",$status);
					if($status){
						S('all-rule.cache',null);
						S('all-rule-id.cache',null);
						S('all-rule-select.cache',null);
					}					
					exit(json_encode($msg));
				}else{//添加菜单
					$status = $tableRule->add();
					$msg = returnMsg("菜单添加成功","菜单添加失败",$status);
					if($status){
						S('all-rule.cache',null);
						S('all-rule-id.cache',null);
						S('all-rule-select.cache',null);
					}	
					exit(json_encode($msg));
				}				
			}
			else
			{
				exit(json_encode($tableRule->getError()));
			}
		}else{//转到页面
			$allRuleSelectkey = 'all-rule-select.cache';
			$menu = S($allRuleSelectkey);
			if($menu == null){
				$menu = M('AuthRule')->order('sort asc')->select();
				$menu = recursiveTwo($menu);
				S($allRuleSelectkey,$menu,C('AUTH_MENU_TIME'));
			}
			$this->selectMenu = $menu;
			$id = I('id','','int');
			if($id){//转到编辑菜单页				
				$thisRule = M('AuthRule')->where(array('id'=>$id))->find();
				$this->rule = $thisRule;
				$this->display('editMenu');
			}else{//转到新增页面
				$pid = I('pid','int');
                $pid = $pid != '' ? $pid : 0;
                $this->pid = $pid;
				$this->display('addMenu');
			}
		}		
	}
	// 删除菜单
	public function delMenu()
	{
		if(IS_POST && I('id')){			
			$id = I('id','','int');
			$db = M('AuthRule');
			$cateid = $db->field('id,pid')->select();
			$delid = get_all_child($cateid,$id);
			$delid[] = $id;			
			$where = array('id' => array('IN',$delid));
			$status = $db->where($where)->delete();
			$msg = returnMsg("菜单删除成功","菜单删除失败",$status);
			if($status){
				S('all-rule.cache',null);
				S('all-rule-id.cache',null);
				S('all-rule-select.cache',null);
			}	
			exit(json_encode(($msg)));
		}
	}
	//菜单排序
    public function sortMenu(){  
    	if(IS_POST){
    		$sortMenu = I('sort');
	        $ids = implode(',', array_keys($sortMenu));
	        $sql = "UPDATE hm_auth_rule SET sort = CASE id ";
	        foreach ($sortMenu as $id => $sort) {
	            $sql .= sprintf("WHEN %d THEN %d ", $id, $sort);
	        }
	        $sql .= "END WHERE id IN ($ids)";
	        $Model = M();
	        $status = $Model->execute($sql);
	        $msg = returnMsg("菜单排序成功","菜单排序失败",$status);
	        if($status){
				S('all-rule.cache',null);
				S('all-rule-select.cache',null);
			}	
	        exit(json_encode($msg));
    	}         
    }
    //导出菜单
	public function exportMenu(){
		$type = I('download') ? I('download') : false;
		if($type){
			$table_Rule = M("AuthRule")->select();
			header("Content-type:application/data");
			header("Content-Disposition:attachment;filename=菜单备份.data");
			exit(base64_encode(serialize($table_Rule)));
		} else {
			$sess_downRule = strtoupper(substr(md5(time().rand(10,10000)),5,10));
			session('downRule',$sess_downRule);
			$data['status'] = true;
			$data['info'] = "菜单导出成功";
			$data['url'] = U("Site/exportMenu",array("download"=>$sess_downRule));
			$this->ajaxReturn($data);
		}
	}
	//菜单导入
	public function importMenu(){
		if(IS_POST){
			$fileTypes = array('data'); // File extensions
			$fileParts = pathinfo($_FILES['file']['name']);
			if (in_array($fileParts['extension'],$fileTypes)) {
				//获取临时上传文件路径
				$tempFile = $_FILES['file']['tmp_name'];
				// echo $tempFile;die;
				$data_Rule = file_get_contents($tempFile);
				$data_Rule = unserialize(base64_decode($data_Rule));
				if(is_array($data_Rule)){
					M("AuthRule")->where("1")->delete();
					$status = M("AuthRule")->addAll($data_Rule);
					if($status > 0){
						$data['status'] = true;
						$data['info'] = '菜单导入成功';
					} else {
						$data['status'] = false;
						$data['info'] = '菜单数据导入失败，请手动恢复你备份的菜单数据';
					}
				} else {
					$data['status'] = false;
					$data['info'] = '非法数据';
				}
				$this->ajaxReturn($data);
			} else {
				echo 'Invalid file type.';
			}
		}
	}
	//用户管理
	public function user(){
		$p = !empty(I('p')) ? I('p') : 1;
		$userListKey = 'user-list.cache'.$p;
		$userListPageKey = 'user-list-page.cache'.$p;
		$userListPageCount = 'user-list-page-count.cache';
	    $user = S($userListKey);
	    $show = S($userListPageKey);
	    if($user == null){
			$count = D('UserRelation')->count();
			$Page  = new \Think\Page($count,C('ADMIN_PAGE_NUM'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
			$total = ceil($count / C('ADMIN_PAGE_NUM'));
			$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$Page->setConfig('prev','上一页');
	        $Page->setConfig('next','下一页');
	        $Page->setConfig('first','第一页');
	        $Page->setConfig('last','最后一页');
	        $user = D('UserRelation')->relation(true)->order('uid asc')->limit($Page->firstRow.','.$Page->listRows)->select();
	        $show = $Page->show();// 分页显示输出	        
	        S($userListKey,$user,C('ADMIN_USER_MANAGE_TIME'));
	        S($userListPageKey,$show,C('ADMIN_USER_MANAGE_TIME'));
	        S($userListPageCount,$total,C('ADMIN_USER_MANAGE_TIME'));
	    }
        $this->assign("userlist", $user);
        $this->assign("page", $show);
		$this->display();
	}
	//添加或修改用户
	public function addEditUser(){
		if(IS_POST && I('addEditUser')){//处理提交
			$rules = array(
				//-1,账号以字母开头,5-17 字母、数字、下划线'_'
		        array('username','/^[a-zA-Z][\w]{4,16}$/',-2,0),
		        //-4,账号被占用
		        array('username', '', -3, 0, 'unique', 1),
		        //-1,昵称必须是中文
		        array('nickname','/^[\x{4e00}-\x{9fa5}]+$/u',-4,0),//
		        //-4,昵称被占用
		        array('nickname', '', -5, 0, 'unique', 1),       
		        
		        // 验证确认密码是否和密码一致
		        array('passworded','password',-7,0,'confirm'),
		        //-6,QQ不合法！
		        // array('qq','/^[1-9]\d{4,9}$/',-8,0),
		        //-3,邮箱格式不正确
		        array('email','email',-9,0),
		        //-6,手机号不合法！
		        // array('phone','/(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])[0-9]{8}$/',-10,0),
		        //-5,QQ被占用
		        // array('qq','',-11,0,'unique',1),
		        //-5,邮箱被占用
		        array('email','',-12,0,'unique',1),
		        //-7,手机号被占用
		        // array('phone','',-13,0,'unique',1),
			);
			//密码以字母开头,8-17 字母、数字、下划线'_'
			if(empty(I('uid','','int'))){
				$rules[] = array('password','/^[a-zA-Z][\w]{7,16}$/',-6,0);
			}
			$tableUser = D("UserRelation");
			if($tableUser->validate($rules)->create()){
				if(I('uid','','int')){//修改
					if(I('password') == '' || I('passworded') == ''){//当没有修改密码时
					unset($tableUser->password);
					}
					else
					{
						$tableUser->password = jiami(I('password'));
					}
					$tableUser->usergroup = I('group_id');
					$status = $tableUser->relation(true)->save();
					$msg = returnMsg("用户修改成功","用户修改失败",$status);					
				}else{//添加
					$tableUser->password = jiami(I('password'));
					$tableUser->usergroup = I('group_id');
					$tableUser->create_time = time();
					$tableUser->create_ip = get_client_ip();
					$status = $tableUser->relation(true)->add();
					$msg = returnMsg("添加用户成功","添加用户失败",$status);								
				}
				//如果成功清楚用户管理缓存
				if($status){
					$userListPageCount = 'user-list-page-count.cache';
					if(!empty(S($userListPageCount))){
						for($i=1;$i<=S($userListPageCount);$i++){
							$userListKey = 'user-list.cache'.$i;
							$userListPageKey = 'user-list-page.cache'.$i;
							S($userListKey,null);
							S($userListPageKey,null);
						}
					}						
				}		
				exit(json_encode($msg));			
			}
			else
			{
				exit(json_encode($tableUser->getError()));
			}
		}else{//转到页面	
			$userGroupKey = 'user-group.cache';
			$group = S($userGroupKey);
			if($group == null){
				$group = M('AuthGroup')->order('sort asc')->select();				
				S($userGroupKey,$group,C('ADMIN_USER_MANAGE_TIME'));
			}
			$this->group = $group;		
			$id = I('uid','','int');
			if($id){//转到编辑页				
				$thisUser = D('UserRelation')->relation(true)->where(['uid'=>$id])->find();
				$this->user = $thisUser;
				$this->display('editUser');
			}else{//转到新增页				
				$this->display('addUser');
			}
		}
	}
	//删除单个管理员
	public function delUser(){
		$id = I('id');
		if($id == '1'){
			$msg = returnMsg("","不允许删除原始超级管理员",false);
			exit(json_encode(($msg)));
		}
		if(IS_POST && $id){
			$status = D("UserRelation")->relation(true)->where(['uid'=>$id])->delete();
			$msg = returnMsg("删除用户成功","删除用户失败，请联系管理员",$status);
			//如果成功清楚用户管理缓存
			if($status){
				$userListPageCount = 'user-list-page-count.cache';
				if(!empty(S($userListPageCount))){
					for($i=1;$i<=S($userListPageCount);$i++){
						$userListKey = 'user-list.cache'.$i;
						$userListPageKey = 'user-list-page.cache'.$i;
						S($userListKey,null);
						S($userListPageKey,null);
					}
				}						
			}	
			exit(json_encode(($msg)));
		}
	}
	//批量删除用户
	public function batchDelUser(){
		$ids = I('ids');
		if(in_array(1, $ids)) {
		    $msg = returnMsg("","不允许删除原始超级管理员",false);
			exit(json_encode(($msg)));
		}
		if(IS_POST && $ids){
			$where = array('uid' => array('IN',$ids));
			$status = D("UserRelation")->relation(true)->where($where)->delete();
			$msg = returnMsg("删除用户成功","删除用户失败，请联系管理员",$status);
			//如果成功清楚用户管理缓存
			if($status){
				$userListPageCount = 'user-list-page-count.cache';
				if(!empty(S($userListPageCount))){
					for($i=1;$i<=S($userListPageCount);$i++){
						$userListKey = 'user-list.cache'.$i;
						$userListPageKey = 'user-list-page.cache'.$i;
						S($userListKey,null);
						S($userListPageKey,null);
					}
				}						
			}	
			exit(json_encode(($msg)));
		}
	}
	//角色管理
	public function role(){
		$p = !empty(I('p')) ? I('p') : 1;
		$roleListKey = 'role-list.cache'.$p;
		$roleListPageKey = 'role-list-page.cache'.$p;
		$roleListPageCount = 'role-list-page-count.cache';
	    $role = S($roleListKey);
	    $show = S($roleListPageKey);
	    if($role == null){
			$count = M('AuthGroup')->count();
			$Page  = new \Think\Page($count,C('ADMIN_PAGE_NUM'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
			$total = ceil($count / C('ADMIN_PAGE_NUM'));
			$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$Page->setConfig('prev','上一页');
	        $Page->setConfig('next','下一页');
	        $Page->setConfig('first','第一页');
	        $Page->setConfig('last','最后一页');
	        $role = M('AuthGroup')->order('sort asc')->limit($Page->firstRow.','.$Page->listRows)->select();
	        $show = $Page->show();// 分页显示输出	        
	        S($roleListKey,$role,C('ADMIN_ROLE_MANAGE_TIME'));
	        S($roleListPageKey,$show,C('ADMIN_ROLE_MANAGE_TIME'));
	        S($roleListPageCount,$total,C('ADMIN_ROLE_MANAGE_TIME'));
	    }
        $this->assign("rolelist", $role);
        $this->assign("page", $show);
		$this->display();
	}
	//添加或修改角色
	public function addEditRole(){
		if(IS_POST && I('addEditRole')){//处理提交
			$rules = array(
	        //-4,角色名称被占用
	        array('title', '', -3, 0, 'unique', 1),
	        //-1,角色名称必须是中文
	        array('title','/^[\x{4e00}-\x{9fa5}]+$/u',-4,0),
	        );
			$tableRole = M('AuthGroup');
			if($tableRole->validate($rules)->create()){
				if(I('id','','int')){//修改					
					$status = $tableRole->save();
					$msg = returnMsg("角色修改成功","角色修改失败",$status);					
				}else{//添加					
					$status = $tableRole->add();
					$msg = returnMsg("角色用户成功","角色用户失败",$status);								
				}
				//如果成功清楚用户管理缓存
				if($status){
					$roleListPageCount = 'role-list-page-count.cache';
					if(!empty(S($roleListPageCount))){
						for($i=1;$i<=S($roleListPageCount);$i++){
							$roleListKey = 'role-list.cache'.$i;
							$roleListPageKey = 'role-list-page.cache'.$i;
							S($roleListKey,null);
							S($roleListPageKey,null);
						}
					}						
				}		
				exit(json_encode($msg));			
			}
			else
			{
				exit(json_encode($tableRole->getError()));
			}
		}else{//转到页面					
			$id = I('id','','int');
			if($id){//转到编辑页				
				$thisRole = M('AuthGroup')->where(['id'=>$id])->find();
				$this->role = $thisRole;
				$this->display('editRole');
			}else{//转到新增页				
				$this->display('addRole');
			}
		}
	}
	//删除单个角色
	public function delRole(){
		$id = I('id');
		if($id == '1'){
			$msg = returnMsg("","不允许删除原始超级管理员角色",false);
			exit(json_encode(($msg)));
		}
		if(IS_POST && $id){
			$status = M('AuthGroup')->where(['id'=>$id])->delete();
			$msg = returnMsg("删除角色成功","删除角色失败，请联系管理员",$status);
			//如果成功清楚用户管理缓存
			if($status){
				$roleListPageCount = 'role-list-page-count.cache';
				if(!empty(S($roleListPageCount))){
					for($i=1;$i<=S($roleListPageCount);$i++){
						$roleListKey = 'role-list.cache'.$i;
						$roleListPageKey = 'role-list-page.cache'.$i;
						S($roleListKey,null);
						S($roleListPageKey,null);
					}
				}						
			}	
			exit(json_encode(($msg)));
		}
	}
	//角色排序
    public function sortRole(){  
    	if(IS_POST){
    		$sortRole = I('sort');
	        $ids = implode(',', array_keys($sortRole));
	        $sql = "UPDATE hm_auth_group SET sort = CASE id ";
	        foreach ($sortRole as $id => $sort) {
	            $sql .= sprintf("WHEN %d THEN %d ", $id, $sort);
	        }
	        $sql .= "END WHERE id IN ($ids)";
	        $Model = M();
	        $status = $Model->execute($sql);
	        $msg = returnMsg("角色排序成功","角色排序失败",$status);
	        //如果成功清楚用户管理缓存
			if($status){
				$roleListPageCount = 'role-list-page-count.cache';
				if(!empty(S($roleListPageCount))){
					for($i=1;$i<=S($roleListPageCount);$i++){
						$roleListKey = 'role-list.cache'.$i;
						$roleListPageKey = 'role-list-page.cache'.$i;
						S($roleListKey,null);
						S($roleListPageKey,null);
					}
				}						
			}
	        exit(json_encode($msg));
    	}         
    }
    //设置权限
	public function setRole()
	{
		if(I('id') == '1'){
			echo "不允许授权原始超级管理员角色";
			exit;
		}		
		if(IS_POST && I('setRole') == 'setRole'){
			$id = I('id','0','int');
			$data = array(
				'id' => $id,
				'rules' => I('ruleid')
			);
			$status = M("AuthGroup")->save($data);
			$msg = returnMsg("授权权限成功","授权权限失败",$status);
			if($status){
				S('role-auth.cache'.$id,null);
			}
			exit(json_encode($msg));
		}else{
			$id = I('id');
			$roleAuthkey = 'role-auth.cache'.$id;
			$auth = S($roleAuthkey);
			if($auth == null){				
				//加载角色节点权限数据		
				$tableRule = M("AuthRule")->field("id,title as name,pid")->order('sort asc')->select();	
				$tableGroup = M("AuthGroup")->find($id);
				$RuleIds = explode(",",$tableGroup['rules']);
				$data = treeState($tableRule,$RuleIds);	
				$auth = json_encode($data);
				S($roleAuthkey,$auth,C('ADMIN_ROLE_AUTH_TIME'));
			}
			$this->json = $auth;
			$this->id = $id;
			$this->title = I('title');
			$this->display();
		}				
	}
	//开发日志管理
	public function version(){
		$p = !empty(I('p')) ? I('p') : 1;
		$versionListKey = 'version-list.cache'.$p;
		$versionListPageKey = 'version-list-page.cache'.$p;
		$versionListPageCount = 'version-list-page-count.cache';
	    $version = S($versionListKey);
	    $show = S($versionListPageKey);
	    if($version == null){
			$count = M('version')->count();
			$Page  = new \Think\Page($count,C('ADMIN_PAGE_NUM'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
			$total = ceil($count / C('ADMIN_PAGE_NUM'));
			$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$Page->setConfig('prev','上一页');
	        $Page->setConfig('next','下一页');
	        $Page->setConfig('first','第一页');
	        $Page->setConfig('last','最后一页');
	        $version = M('version')->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	        $show = $Page->show();// 分页显示输出	        
	        S($versionListKey,$version,C('ADMIN_VERSION_MANAGE_TIME'));
	        S($versionListPageKey,$show,C('ADMIN_VERSION_MANAGE_TIME'));
	        S($versionListPageCount,$total,C('ADMIN_VERSION_MANAGE_TIME'));
	    }
        $this->assign("versionlist", $version);
        $this->assign("page", $show);
		$this->display();
	}
	//添加或修改开发日志
	public function addEditVersion(){
		if(IS_POST && I('addEditVersion')){//处理提交			
			$tableVersion = M('version');
			if($tableVersion->create()){
				if(I('id','','int')){//修改					
					$status = $tableVersion->save();
					$msg = returnMsg("开发日志修改成功","开发日志修改失败",$status);			
				}else{//添加		
					$tableVersion->time = time();			
					$status = $tableVersion->add();
					$msg = returnMsg("开发日志添加成功","开发日志添加失败",$status);			
				}
				//如果成功清楚用户管理缓存
				if($status){
					$versionListPageCount = 'version-list-page-count.cache';
					if(!empty(S($versionListPageCount))){
						for($i=1;$i<=S($versionListPageCount);$i++){
							$versionListKey = 'version-list.cache'.$i;
							$versionListPageKey = 'version-list-page.cache'.$i;
							S($versionListKey,null);
							S($versionListPageKey,null);
						}
					}						
				}		
				exit(json_encode($msg));			
			}
			else
			{
				exit(json_encode($tableRole->getError()));
			}
		}else{//转到页面					
			$id = I('id','','int');
			if($id){//转到编辑页				
				$thisVersion = M('version')->where(['id'=>$id])->find();
				$this->version = $thisVersion;
				$this->display('editVersion');
			}else{//转到新增页				
				$this->display('addVersion');
			}
		}
	}
	//批量删除开发日志
	public function batchDelVersion(){
		$ids = empty(I('id'))?I('ids'):I('id');		
		if(IS_POST && $ids){
			$where = array('id' => array('IN',$ids));
			$status = M('version')->where($where)->delete();
			$msg = returnMsg("删除开发日志成功","删除开发日志失败，请联系管理员",$status);
			//如果成功清楚用户管理缓存
			if($status){
				$versionListPageCount = 'version-list-page-count.cache';
				if(!empty(S($versionListPageCount))){
					for($i=1;$i<=S($versionListPageCount);$i++){
						$versionListKey = 'version-list.cache'.$i;
						$versionListPageKey = 'version-list-page.cache'.$i;
						S($versionListKey,null);
						S($versionListPageKey,null);
					}
				}						
			}
			exit(json_encode(($msg)));
		}
	}
}