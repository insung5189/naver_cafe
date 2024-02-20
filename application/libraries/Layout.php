<?php
// /application/libraries/Layout.php
class Layout
{
	public function __construct()
	{
		$this->obj = &get_instance();
		$this->obj->load->model('member/MyActivityModel', 'MyActivityModel');
		$this->obj->load->model('article/ArticleListModel', 'ArticleListModel');
	}

	public function view($view = "", $page_view_data = array())
	{
		$page_view_data['masterNickName'] = $this->getMasterAdminNickName();
		$page_view_data['totalMemberCount'] = $this->getTotalMemberCount();
		$page_view_data['totalArticleCount'] = $this->getTotalArticleCount();

		if ($userId = $this->obj->session->userdata('user_data')['user_id'] ?? null) {
			$activityInfo = $this->getUserActivityInfo($userId);
			$page_view_data = array_merge($page_view_data, $activityInfo);
		}

		$layout_view_data = array(
			"title" => isset($page_view_data['title']) ? $page_view_data['title'] : '기본 제목',
			"contents" => $this->obj->load->view($view, $page_view_data, true),
			'masterNickName' => $page_view_data['masterNickName'],
			'totalMemberCount' => $page_view_data['totalMemberCount'],
			'totalArticleCount' => $page_view_data['totalArticleCount']
		);

		$this->obj->load->view("/layouts/main_layout_view", $layout_view_data);
	}

	protected function getMasterAdminNickName()
	{
		$masterAdmin = $this->obj->doctrine->em->getRepository('Models\Entities\Member')->findOneBy(['role' => 'ROLE_MASTER']);
		return $masterAdmin ? $masterAdmin->getNickName() : '마스터 계정 없음';
	}

	protected function getTotalMemberCount()
    {
        $totalMemberCount = $this->obj->doctrine->em->getRepository('Models\Entities\Member')->count([]);
        return $totalMemberCount;
    }

	protected function getTotalArticleCount()
    {
        $totalArticleCount = $this->obj->doctrine->em->getRepository('Models\Entities\Article')->count([]);
        return $totalArticleCount;
    }

	protected function getUserActivityInfo($userId)
	{
		$articleCount = $this->obj->MyActivityModel->getArticleCount($userId);
		$commentCount = $this->obj->MyActivityModel->getCommentCount($userId);

		return [
			'articleCount' => $articleCount,
			'commentCount' => $commentCount,
		];
	}
}
