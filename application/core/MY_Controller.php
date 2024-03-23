<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->em = $this->doctrine->em;
        // // AJAX 요청이 아닐 때만 프로파일러를 활성화
        // if (!$this->input->is_ajax_request()) {
        //     $this->output->enable_profiler(TRUE);
        // }

        // 게시글 상세보기 페이지에서만 세션에 articleId값이 저장되도록 실시.
        $this->removeArticleIdFromSession();
    }

    protected function setRedirectCookie($path)
    {
        $this->load->helper('cookie');
        $cookie = [
            'name'   => 'redirect_url',
            'value'  => $path,
            'expire' => '3600', // 단위 : 초 = 1시간
            'secure' => FALSE, // HTTPS를 사용하지 않는 경우
            'httponly' => FALSE // JavaScript에서 접근 가능하게 설정
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

    private function removeArticleIdFromSession()
    {
        // AJAX 요청이면 세션에서 값을 제거하지 않고 반환
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return;
        }

        $currentUrl = current_url();

        // 게시글 상세보기 페이지의 URL 패턴
        $articleDetailUrlPattern = '/article\/articledetailcontroller\/index\/\d+/';

        // 게시글 수정 페이지의 URL 패턴
        $articleEditUrlPattern = '/article\/articleeditcontroller\/editForm\/\d+/';

        // 현재 URL이 게시글 상세보기, 수정, 관련 게시글 보기 페이지의 URL 패턴과 일치하지 않는 경우
        // 해당 세션 값을 제거
        if (
            !preg_match($articleDetailUrlPattern, $currentUrl) &&
            !preg_match($articleEditUrlPattern, $currentUrl)
        ) {
            $this->session->unset_userdata('viewedArticleId');
            $this->session->unset_userdata('editArticleId');
        }
    }
}
