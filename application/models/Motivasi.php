<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Motivasi extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        // Load the database library
        $this->load->database();

        $this->mTbl = 'motivasi';
        $this->muser='user';


    }

    /*
     * Insert motivasi data
     */
    public function insert($data)
    {
        //add created and modified date if not exists
        if (!array_key_exists("tanggal_input", $data)) {
            $data['tanggal_input'] = date("Y-m-d H:i:s");
        }



        //insert motivasi data to motiasi table
        $insert = $this->db->insert($this->mTbl, $data);

        //return the status
        return $insert ? $this->db->insert_id() : false;
    }

    /*
     * Update materi data
     */
    public function update($data, $id)
    {
        //add modified date if not exists
        if (!array_key_exists("tanggal_update", $data)) {
            $data['tanggal_update'] = date("Y-m-d H:i:s");
        }

        //update materi data in materi table
        $update = $this->db->update($this->mTbl, $data, array('id' => $id));

        //return the status
        return $update ? true : false;
    }

    /*
     * Delete materi data
     */
    public function delete($id)
    {
        //update mater from materi table
        $delete = $this->db->delete('materi', array('id' => $id));
        //return the status
        return $delete ? true : false;
    }

    function getRows($params = array())
    {
        $this->db->select('*');
        $this->db->from($this->mTbl);

        //fetch data by conditions
        if (array_key_exists("conditions", $params)) {
            foreach ($params['conditions'] as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        if (array_key_exists("id", $params)) {
            $this->db->where('id', $params['id']);
            $query = $this->db->get();
            $result = $query->row_array();
        } else {
            //set start and limit
            if (array_key_exists("start", $params) && array_key_exists("limit", $params)) {
                $this->db->limit($params['limit'], $params['start']);
            } elseif (!array_key_exists("start", $params) && array_key_exists("limit", $params)) {
                $this->db->limit($params['limit']);
            }

            if (array_key_exists("returnType", $params) && $params['returnType'] == 'count') {
                $result = $this->db->count_all_results();
            } else if (array_key_exists("returnType", $params) && $params['returnType'] == 'single') {
                $query = $this->db->get();
                $result = ($query->num_rows() > 0) ? $query->row_array() : false;
            } else {
                $query = $this->db->get();
                $result = ($query->num_rows() > 0) ? $query->result_array() : false;
            }
        }

        //return fetched data
        return $result;
    }
}
