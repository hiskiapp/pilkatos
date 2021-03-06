<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use CB;
use QrCode;
use PDF;
use Excel;
use File;
use Hash;
use Carbon\Carbon;

class AdminStudentController extends \crocodicstudio\crudbooster\controllers\CBController {

	public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "username";
		$this->limit = "20";
		$this->orderby = "name,asc";
		$this->global_privilege = false;
		$this->button_table_action = true;
		$this->button_bulk_action = true;
		$this->button_action_style = "button_icon";
		$this->button_add = true;
		$this->button_edit = true;
		$this->button_delete = true;
		$this->button_detail = true;
		$this->button_show = true;
		$this->button_filter = true;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "users";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label"=>"Kode / NIS","name"=>"username"];
		if (CRUDBooster::isSuperadmin()) {
			$this->col[] = ["label"=>"Email","name"=>"email","callback"=>function($row){
				if ($row->email == NULL) {
					$res = '<span class="btn btn-danger btn-xs btn-document dropdown-toggle"><span class="fa fa-inbox"></span> NULL</span>';
				}else{
					$res = $row->email;
				}

				return $res;
			}];
		}else{
			if (CB::checkSecurity() == 1) {
				$this->col[] = ["label"=>"Email","name"=>"email","callback"=>function($row){
					if ($row->email == NULL) {
						$res = '<span class="btn btn-danger btn-xs btn-document dropdown-toggle"><span class="fa fa-inbox"></span> NULL</span>';
					}else{
						$res = $row->email;
					}

					return $res;
				}];
			}
		}
		$this->col[] = ["label"=>"Nama","name"=>"name"];
		$this->col[] = ["label"=>"Kelas","name"=>"class_id","join"=>"class,name"];
		$this->col[] = ["label"=>"Status","name"=>"status","callback"=>function($row){
			if ($row->status == 0) {
				$res = '<span class="btn btn-warning btn-xs btn-document dropdown-toggle"><span class="fa fa-user"></span> Belum Memilih</span>';
			}else{
				$res = '<div class="dropdown">
				<button type="button" class="btn btn-primary btn-xs btn-document dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user"></span> Sudah Memilih <span class="fa fa-caret-down"></span>
				</button>
				<ul class="dropdown-menu">
				<li><a href="javascript:void(0)" onclick="swal({
					title: &quot;Reset Sekarang ?&quot;,
					text: &quot;&quot;,
					type: &quot;warning&quot;,
					showCancelButton: true,
					confirmButtonColor: &quot;#3C8DBC&quot;,
					confirmButtonText: &quot;Ya!&quot;,
					cancelButtonText: &quot;Tidak&quot;,
					closeOnConfirm: false },
					function(){  location.href=&quot;'.CRUDBooster::mainPath('reset/').$row->id.'&quot; });">Reset</a></li>
					</ul>
					</div>';
				}
				return $res;
			}];
			if (CRUDBooster::isSuperadmin()) {
				$this->col[] = ["label"=>"Sekolah","name"=>"cms_users_id","join"=>"cms_users,name"];
			}

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Kode / NIS','name'=>'username','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			if (CRUDBooster::isSuperadmin()) {
				$this->form[] = ['label'=>'Email','name'=>'email','type'=>'email','validation'=>'min:8|max:70','width'=>'col-sm-10','help'=>'Please leave empty if you don"t want to fill in an email.'];
			}else{
				if (CB::checkSecurity() == 1) {
					$this->form[] = ['label'=>'Email','name'=>'email','type'=>'email','validation'=>'required|min:8|max:70','width'=>'col-sm-10','placeholder'=>'Ex : username@mail.com'];
				}
			}
			$this->form[] = ['label'=>'Nama','name'=>'name','type'=>'text','validation'=>'required|string|min:3|max:70','width'=>'col-sm-10','placeholder'=>'You can only enter the letter only'];
			$this->form[] = ['label'=>'Password','name'=>'password','type'=>'password','validation'=>'min:8|max:32','width'=>'col-sm-10','help'=>'Minimum 8 characters. Please leave empty if you did not change the password.'];
			if (CRUDBooster::isSuperadmin()) {
				$this->form[] = ['label'=>'Sekolah','name'=>'cms_users_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'cms_users,name','datatable_where'=>'id != 1','datatable_ajax'=>false];
				$this->form[] = ['label'=>'Kelas','name'=>'class_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'class,name','parent_select'=>'cms_users_id'];
			}else{
				$this->form[] = ['label'=>'Kelas','name'=>'class_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'class,name','datatable_where'=>'cms_users_id='.CRUDBooster::myId()];
			}

			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ['label'=>'Kode / NIS','name'=>'username','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Nama','name'=>'name','type'=>'text','validation'=>'required|string|min:3|max:70','width'=>'col-sm-10','placeholder'=>'You can only enter the letter only'];
			//$this->form[] = ['label'=>'Password','name'=>'password','type'=>'password','validation'=>'min:3|max:32','width'=>'col-sm-10','help'=>'Minimum 5 characters. Please leave empty if you did not change the password.'];
			//$this->form[] = ['label'=>'Kelas','name'=>'class_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'class,name'];
			//if (CRUDBooster::myId() == 1) {
			//$this->form[] = ['label'=>'Sekolah','name'=>'cms_users_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'cms_users,name'];
			//}
			# OLD END FORM

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();
	        if (CRUDBooster::isSuperadmin() || CRUDBooster::isSuperadmin() != 1 && CB::checkSecurity() == 1) {
	        	$this->addaction[] = ['label'=>'Kirim Email','url'=>CRUDBooster::mainpath('send-email/[id]'),'icon'=>'fa fa-inbox','color'=>'success','showIf'=>"[email] != NULL"];
	        }


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();
	        $this->button_selected[] = ['label'=>'Reset Pilihan','icon'=>'fa fa-repeat','name'=>'reset_election'];


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();


	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array(); 
	        $this->table_row_color[] = ['condition'=>"[status] == 1","color"=>"success"];	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = '<div class="box box-default">
	        <div class="box-header">
	        <h1 class="box-title">Total = '.number_format(CB::totalTurnOut('students')).' Siswa</h1>
	        </div>
	        </div>';
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        
	        
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	    	if($button_name == 'reset_election') {
	    		DB::table('users')->whereIn('id',$id_selected)->update(['status'=>0]);
	    		DB::table('election_data')->whereIn('users_id',$id_selected)->delete();
	    	}
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
	    	if (CRUDBooster::isSuperadmin() != 1) {
	    		$query
	    		->where('users.cms_users_id',CRUDBooster::myId())
	    		->where('users.type',0);
	    	}else{
	    		$query
	    		->where('cms_users.status', 'Active')
	    		->where('users.type',0);
	    	}
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {        
	        //Your code here
	    	$postdata['type'] = 0;
	    	if (CRUDBooster::isSuperadmin() != 1) {
	    		$postdata['cms_users_id'] = CRUDBooster::myId();
	    	}
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here
	    }



	    //By the way, you can still create your own method in here... :) 
	    public function getPrintcard(){
	    	$data['data'] = DB::table('users')->where([
	    		'type' => 0,
	    		'cms_users_id' => CRUDBooster::myId()
	    	])->get();
	    	$data['logo'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first()->photo;

	    	return view('backend.export.voter_card', $data);
	    }

	    public function postPrintcard(){
	    	$data['data'] = DB::table('users')->where([
	    		'type' => 0,
	    		'cms_users_id' => g('cms_users_id')
	    	])->get();
	    	$data['logo'] = DB::table('cms_users')->where('id', g('cms_users_id'))->first()->photo;

	    	return view('backend.export.voter_card', $data);
	    }

	    public function getDetail($id) {
	    	if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {    
	    		CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
	    	}

	    	$data = [];
	    	$data['page_title'] = 'Detail Siswa';
	    	$data['row'] = DB::table('users')
	    	->select('users.email','users.username','users.password','users.name','users.password','users.cms_users_id','class.name as class','cms_users.name as school','users.type')
	    	->join('cms_users','users.cms_users_id','=','cms_users.id')
	    	->join('class','users.class_id','=','class.id')
	    	->where('users.id',$id)
	    	->where('type', 0)
	    	->first();

	    	$qr['username'] = $data['row']->username;
	    	$qr['password'] = $data['row']->password;
	    	$qr['cms_users_id'] = $data['row']->cms_users_id;

	    	$data['image'] = QrCode::format('png')
	    	->merge(url('images/logo.png'), 0.3, true)
	    	->size(300)->errorCorrection('H')
	    	->generate(json_encode($qr));

	    	$this->cbView('backend.detail',$data);
	    }

	    public function getReset($id){
	    	DB::table('users')->where('id', $id)->update(['status' => 0]);
	    	DB::table('election_data')->where('users_id', $id)->delete();

	    	return redirect(CRUDBooster::mainPath());
	    }

	    public function getSendEmail($id){

	    	$users = DB::table('users')
	    	->select('users.email','users.name','cms_users.name as school','users.username','cms_users.path')
	    	->join('cms_users','users.cms_users_id','=','cms_users.id')
	    	->where('cms_users.with_email', 1)
	    	->where('users.id', $id)
	    	->first();

	    	if (!$users) {
	    		return redirect()->back()->with(['message_type'=>'warning','message'=>'Error!']);
	    	}else{
	    		$token = str_random(64);

	    		$check_token = DB::table('password_token')->where('email',$users->email)->first();
	    		if ($check_token) {
	    			DB::table('password_token')->where(['email' => $users->email,'is_users' => 1])->update([
	    				'token' => $token,
	    				'created_at' => now()
	    			]);
	    		}else{
	    			DB::table('password_token')->insert([
	    				'email' => $users->email,
	    				'is_users' => 1,
	    				'token' => $token
	    			]);
	    		}

	    		$data['name'] = $users->name;
	    		$data['school'] = $users->school;
	    		$data['token'] = $token;
	    		$data['username'] = $users->username;
	    		$data['path'] = $users->path;

	    		CRUDBooster::sendEmail([
	    			'to' 		=>$users->email,
	    			'data' 		=> $data,
	    			'template'  =>'password_token']);

	    		return redirect()->back()->with(['message_type'=>'success','message'=>'Password Token Untuk '.$users->name.' Telah Dikirim!']);
	    	}
	    }

	    public function getSendAllEmail(){

	    	$users = DB::table('users')
	    	->select('users.email','users.name','cms_users.name as school','users.username','cms_users.path')
	    	->join('cms_users','users.cms_users_id','=','cms_users.id')
	    	->where('cms_users.with_email', 1)
	    	->where('users.cms_users_id', CRUDBooster::myId())
	    	->where('users.email','!=',NULL)
	    	->where('users.type',0)
	    	->get();

	    	foreach ($users as $row) {
	    		$token = str_random(64);

	    		$check_token = DB::table('password_token')->where('email',$row->email)->first();
	    		if ($check_token) {
	    			DB::table('password_token')->where(['email' => $users->email,'is_users' => 1])->update([
	    				'token' => $token,
	    				'created_at' => now()
	    			]);
	    		}else{
	    			DB::table('password_token')->insert([
	    				'email' => $row->email,
	    				'is_users' => 1,
	    				'token' => $token
	    			]);
	    		}
	    		
	    		$data['name'] = $row->name;
	    		$data['school'] = $row->school;
	    		$data['token'] = $token;
	    		$data['username'] = $row->username;
	    		$data['path'] = $row->path;

	    		CRUDBooster::sendEmail([
	    			'to' 		=>$row->email,
	    			'data' 		=> $data,
	    			'template'  =>'password_token']);
	    	}
	    	
	    	return redirect()->back()->with(['message_type'=>'success','message'=>'Semua Password Token Telah Dikirim!']);
	    }

	    public function postSendAllEmail(){

	    	$users = DB::table('users')
	    	->select('users.email','users.name','cms_users.name as school','users.username','cms_users.path')
	    	->join('cms_users','users.cms_users_id','=','cms_users.id')
	    	->where('cms_users.with_email', 1)
	    	->where('users.cms_users_id', g('cms_users_id'))
	    	->where('users.email','!=',NULL)
	    	->where('users.type',0)
	    	->get();

	    	foreach ($users as $row) {
	    		$token = str_random(64);
	    		if ($check_token) {
	    			DB::table('password_token')->where(['email' => $users->email,'is_users' => 1])->update([
	    				'token' => $token,
	    				'created_at' => now()
	    			]);
	    		}else{
	    			DB::table('password_token')->insert([
	    				'email' => $row->email,
	    				'is_users' => 1,
	    				'token' => $token
	    			]);
	    		}

	    		$data['name'] = $row->name;
	    		$data['school'] = $row->school;
	    		$data['token'] = $token;
	    		$data['username'] = $row->username;
	    		$data['path'] = $row->path;

	    		CRUDBooster::sendEmail([
	    			'to' 		=>$row->email,
	    			'data' 		=> $data,
	    			'template'  =>'password_token']);
	    	}
	    	
	    	return redirect()->back()->with(['message_type'=>'success','message'=>'Semua Password Token Telah Dikirim!']);
	    }

	    public function postImportData(){
	    	$extension = File::extension(Request::file('importdata')->getClientOriginalName());
	    	if($extension == "xlsx" || $extension == "xls" || $extension == "csv"){
	    		$path = Request::file('importdata')->getRealPath();
	    		$data = Excel::load($path, function($reader) {
	    		})
	    		->get();
	    		foreach($data as $key => $d){
	    			if (!CRUDBooster::isSuperadmin()) {
	    				if (CB::isWithEmail()) {
	    					$save['email']     	= $d->email;
	    				}
	    			}
	    			$save['username']    	= $d->username;
	    			$save['name']         	= ucwords(strtolower($d->name));
	    			$save['password']    	= Hash::make(Carbon::parse($d->password)->format('Y-m-d'));
	    			$save['type']          	= 0;
	    			$save['class_id']       = $d->class_id;
	    			$save['status'] 		= 0;
	    			if (g('cms_users_id')) {
	    				$save['cms_users_id']     = g('cms_users_id');
	    			}else{
	    				$save['cms_users_id']     = CRUDBooster::myId();
	    			}

	    			$check = DB::table('users')->where(['username' => $d->username, 'cms_users_id' => $save['cms_users_id']])->first();
	    			$failed = 0;

	    			if ($d->username == null || $check != null) {
	    				$failed += 1;
	    			}else{
	    				DB::table('users')->insert($save);
	    			}
	    		}

	    		if(!empty($data) && $data->count()){
	    			if ($failed != 0) {
	    				return redirect()->back()->with(['message_type'=>'success','message'=>'Berhasil mengimport data! Total Data Yang Failed:'.$failed]);
	    			}else{
	    				return redirect()->back()->with(['message_type'=>'success','message'=>'Berhasil mengimport data!']);
	    			}
	    		}else{
	    			return redirect()->back()->with(['message_type'=>'error','message'=>'Tidak berhasil mengimport data!']);
	    		}
	    	}else {
	    		return redirect()->back()->with(['message_type'=>'error','message'=>'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!']);
	    	}
	    }

	    public function getUcwords(){
	    	$row = DB::table('users')->get();

	    	foreach ($row as $key => $val) {
	    		DB::table('users')->where('id',$val->id)->update([
	    			'name' => ucwords(strtolower($val->name))
	    		]);
	    	}

	    	return 'done';
	    }

	}