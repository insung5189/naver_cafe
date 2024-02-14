<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('layout');
        $this->load->library('session');
        // AJAX 요청이 아닐 때만 프로파일러를 활성화
        if (!$this->input->is_ajax_request()) {
            $this->output->enable_profiler(TRUE);
        }
    }
}