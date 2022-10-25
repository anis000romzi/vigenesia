<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Motivations extends REST_Controller
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->model('motivasi');
    }

    public function index_get($iduser = '')
    {
        if ($iduser == '') {
            $this->db->order_by('tanggal_input', 'DESC');
            $api = $this->db->get('motivasi')->result();
        } else {
            $this->db->where('iduser', $iduser);
            $api = $this->db->get('motivasi')->result();
        }
        $this->response($api, REST_Controller::HTTP_OK);
    }

    public function index_post($iduser = '')
    {
        if ($iduser == '') {
            // Set the response and exit
            $this->response(
                [
                    "status" => "fail",
                    "message" => "Please provide iduser"
                ],
                REST_Controller::HTTP_BAD_REQUEST
            );
        } else {
            $isi_motivasi = $this->input->post("isi_motivasi");

            if ($isi_motivasi) {
                $materiData = [
                    'isi_motivasi' => $isi_motivasi,
                    'iduser' => $iduser
                ];

                $insert = $this->motivasi->insert($materiData);

                // Check if the user data is inserted
                if ($insert) {
                    // Set the response and exit
                    $this->response(
                        [
                            'status' => 'success',
                            'message' => 'Postingan berhasil di tambah added successfully.',
                            'data' => $materiData
                        ],
                        REST_Controller::HTTP_CREATED
                    );
                } else {
                    // Set the response and exit
                    $this->response(
                        [
                            "status" => "fail",
                            "message" => "Some problems occurred, please try again"
                        ],
                        REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            } else {
                // Set the response and exit
                $this->response(
                    [
                        "status" => "fail",
                        "message" => "Provide complete motivasi info to add"
                    ],
                    REST_Controller::HTTP_BAD_REQUEST
                );
            }
        }
    }

    public function index_put($id = '')
    {
        // Get the post data
        $isi_motivasi = $this->put('isi_motivasi');

        // Validate the post data
        if ($isi_motivasi) {
            $con['returnType'] = 'count';
            $con['conditions'] = [
                'id' => $id,
            ];
            $motivasiCount = $this->motivasi->getRows($con);

            if ($motivasiCount < 1) {
                // Set the response and exit
                $this->response(
                    [
                        "status" => "fail",
                        "message" => "Post doesn't exist"
                    ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                // Update motivasi's 
                $Data = [];
                if ($isi_motivasi) {
                    $Data['isi_motivasi'] = $isi_motivasi;
                }

                $update = $this->motivasi->update($Data, $id);

                // Check if the user data is updated
                if ($update) {
                    // Set the response and exit
                    $this->response([
                        'status' => 'success',
                        'message' => 'user berhasil updated postingan'
                    ], REST_Controller::HTTP_OK);
                } else {
                    // Set the response and exit
                    $this->response(
                        [
                            "status" => "fail",
                            "message" => "Some problems occurred, please try again"
                        ],
                        REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
        } else {
            // Set the response and exit
            $this->response([
                "status" => "fail",
                "message" => "Provide at least one user info to update"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function index_delete($id = '')
    {
        $con['returnType'] = 'count';
        $con['conditions'] = [
            'id' => $id,
        ];
        $motivasiCount = $this->motivasi->getRows($con);

        if ($motivasiCount < 1) {
            // Set the response and exit
            $this->response(
                [
                    "status" => "fail",
                    "message" => "Post doesn't exist"
                ],
                REST_Controller::HTTP_NOT_FOUND
            );
        } else {
            $this->delete('id');
            $this->db->where('id', $id);
            $delete = $this->db->delete('motivasi');
            if ($delete) {
                $this->response([
                    'status' => 'success',
                    'message' => 'Postingan Berhasil di Hapus.',
                    'data' => $delete
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response(
                    [
                        "status" => "fail",
                        "message" => "Some problems occurred, please try again"
                    ],
                    REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
