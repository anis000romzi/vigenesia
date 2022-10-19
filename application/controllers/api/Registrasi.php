<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class Registrasi extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        // Load the user model
        $this->load->model('user');
    }

    public function index_post()
    {
        // Get the post data
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
                    $this->response([
                        'status' => 'success',
                        'is_active' => TRUE,
                        'message' => 'The user has been added successfully',
                        'data' => $insert
                    ], REST_Controller::HTTP_CREATED);
                } else {
                    // Set the response and exit
                    $this->response([
                        "status" => "fail",
                        "message" => "Some problems occurred, please try again"
                    ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } else {
            // Set the response and exit
            $this->response([
                "status" => "fail",
                "message" => "Provide complete user info to add"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
