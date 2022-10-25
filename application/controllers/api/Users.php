<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller
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
        $this->load->model('user');
    }

    // Get users/user by id, link: vigenesia/api/users/{id}, method: GET
    public function index_get($id = '')
    {
        if ($id == '') {
            $api = $this->db->get('user')->result();
        } else {
            $this->db->where('iduser', $id);
            $api = $this->db->get('user')->result();
        }
        $this->response(
            [
            "status" => "success",
            "data" => $api
            ], REST_Controller::HTTP_OK
        );
    }

    // Add user, link: vigenesia/api/users, method: POST
    public function index_post()
    {
        $nama = $this->post('nama');
        $profesi = $this->post('profesi');
        $email = $this->post('email');
        $password = $this->post('password');

        // Validate the post data
        if ($nama && $profesi && $email && $password) {
            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = [
                'email' => $email,
            ];
            $userCount = $this->user->getRows($con);

            if ($userCount > 0) {
                // Set the response and exit
                $this->response(
                    [
                        "status" => "fail",
                        "message" => "The given email already exists"
                    ],
                    REST_Controller::HTTP_BAD_REQUEST
                );
            } else {
                // Insert user data
                $userData = [
                    'nama' => $nama,
                    'profesi' => $profesi,
                    'email' => $email,
                    'password' => md5($password),
                ];
                $insert = $this->user->insert($userData);

                // Check if the user data is inserted
                if ($insert) {
                    // Set the response and exit
                    $this->response(
                        [
                        'status' => 'success',
                        'is_active' => true,
                        'message' => 'The user has been added successfully',
                        'data' => $userData
                        ], REST_Controller::HTTP_CREATED
                    );
                } else {
                    // Set the response and exit
                    $this->response(
                        [
                        "status" => "fail",
                        "message" => "Some problems occurred, please try again"
                        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
        } else {
            // Set the response and exit
            $this->response(
                [
                "status" => "fail",
                "message" => "Provide complete user info to add"
                ], REST_Controller::HTTP_BAD_REQUEST
            );
        }
    }

    // Edit user's data, link: vigenesia/api/users/{id}, method: PUT
    public function index_put($id = '')
    {
        $nama = $this->put('nama');
        $profesi = $this->put('profesi');
        $email = $this->put('email');
        $password = $this->put('password');

        // Validate the post data
        if ($nama  || $profesi || $email || $password) {
            $con['returnType'] = 'count';
            $con['conditions'] = [
                'iduser' => $id,
            ];
            $userCount = $this->user->getRows($con);

            if ($userCount < 1) {
                // Set the response and exit
                $this->response(
                    [
                        "status" => "fail",
                        "message" => "User doesn't exist"
                    ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                // Update user's account data
                $userData = [];
                if ($nama) {
                    $userData['nama'] = $nama;
                }
                if ($profesi) {
                    $userData['profesi'] = $profesi;
                }
                if ($email) {
                    $userData['email'] = $email;
                }
                if ($password) {
                    $userData['password'] = md5($password);
                }

                $update = $this->user->update($userData, $id);

                // Check if the user data is updated
                if ($update) {
                    // Set the response and exit
                    $this->response(
                        [
                        'status' => 'success',
                        'message' => 'user berhasil updated profile baru'
                        ], REST_Controller::HTTP_OK
                    );
                } else {
                    // Set the response and exit
                    $this->response(
                        [
                        "status" => "fail",
                        "message" => "Some problems occurred, please try again"
                        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
        } else {
            // Set the response and exit
            $this->response(
                [
                "status" => "fail",
                "message" => "Provide complete user info to update"
                ], REST_Controller::HTTP_BAD_REQUEST
            );
        }
    }
}
