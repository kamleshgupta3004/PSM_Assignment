<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeModal extends CI_Model {

    public function save_preview($data=array()) {
        $return = array("status"=>"0","msg"=>"");
        if(count($data)>0){
            $inserted = $this->db->insert('psm_record', $data);
            //check inserted or not
            if ($inserted) {
                $return['status'] = "1";
                $return['msg'] = "inserted successfully.";
            } else {
                // Insert failed
                $return['msg']= 'Failed to insert preview data: ' . $this->db->error()['message'];
            }
        }
        else{
            $return['msg']= "Record Not Found";
        }
        return $return;
    }

    //this functon return all count
    public function countRecord() {
        return $this->db->count_all('psm_record');
    }

    //this functon return record from table 
    public function fetchRecord($limit, $offset) {
        $this->db->order_by('created_date', 'DESC');
        $query = $this->db->get('psm_record', $limit, $offset);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return "0";
        }
        
    }

}
?>
