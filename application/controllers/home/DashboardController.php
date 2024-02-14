<?
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardController extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }


    public function index()
    {
        $page_view_data['title'] = '메인';
        $this->layout->view('dashboard/dashboard', $page_view_data);
    }
}