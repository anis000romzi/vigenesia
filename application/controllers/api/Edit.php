<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Edit extends REST_Controller
{

    function __construct($config = 'rest')
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

    function index_put()
    {
        $id = $this->put('iduser');

        // Get the post data
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
                    $this->response([
                        'status' => 'success',
                        'message' => 'user berhasil updated profile baru'
                    ], REST_Controller::HTTP_OK);
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
                "message" => "Provide complete user info to update"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
