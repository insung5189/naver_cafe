<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class ArticleEditModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }
    
}