<?
defined('BASEPATH') OR exit('No direct script access allowed');

class Layoutmade extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }


    public function index()
    {
        $this->load->view('templates/header');
        $this->load->view('dashboard/layoutmade');
        $this->load->view('templates/footer');
    }
}