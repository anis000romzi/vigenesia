<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Login extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Credentials: true");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->model('user');
    }

    public function index_post()
    {
        // Get the post data
        $email = $this->post('email');
        $password = $this->post('password');

        // Validate the post data
        if ($email && $password) {

            // Check if any user exists with the given credentials
            $con['returnType'] = 'single';
            $con['conditions'] = [
                'email' => $email,
                'password' => md5($password),
                'is_active' => 1
            ];
            $user = $this->user->getRows($con);

            if ($user) {
                // Set the response and exit
                $this->response([
                    'status' => 'success',
                    'is_active' => TRUE,
                    'message' => 'User login berhasil bro',
                    'data' => $user
                ], REST_Controller::HTTP_OK);
            } else {
                // Set the response and exit
                $this->response([
                    "status" => "fail",
                    "message" => "Ada kesalahan di email / password"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            // Set the response and exit
            $this->response([
                "status" => "fail",
                "message" => "Belum mengisi email dan password"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
