<?
defined('BASEPATH') or exit('No direct script access allowed');
class LayoutController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
    }

    public function cafeInfo() {
        $page_view_data['title'] = '카페 소개';
        $this->layout->view('cafeinfo/cafeinfo', $page_view_data);
    }
}
