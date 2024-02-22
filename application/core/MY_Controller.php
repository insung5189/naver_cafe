<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('layout');
        $this->load->library('session');
        // AJAX 요청이 아닐 때만 프로파일러를 활성화
        if (!$this->input->is_ajax_request()) {
            $this->output->enable_profiler(TRUE);
        }
    }

    protected function setRedirectCookie($path)
    {
        $this->load->helper('cookie');
        $cookie = [
            'name'   => 'redirect_url',
            'value'  => $path,
            'expire' => '3600', // 단위 : 초
            'secure' => FALSE // HTTPS를 사용하지 않는 경우
        ];
        $this->input->set_cookie($cookie);
    }

    protected function getRedirectCookie()
    {
        $this->load->helper('cookie');
        return $this->input->cookie('redirect_url', TRUE);
    }

    protected function deleteRedirectCookie()
    {
        delete_cookie('redirect_url');
    }
}
