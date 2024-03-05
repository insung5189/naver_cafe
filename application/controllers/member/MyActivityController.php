<?
defined('BASEPATH') or exit('No direct script access allowed');
class MyActivityController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/MyActivityModel', 'MyActivityModel');
        $this->load->library('doctrine');
        $this->em = $this->doctrine->em;
    }
}