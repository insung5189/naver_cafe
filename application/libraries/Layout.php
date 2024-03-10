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

		if ($memberId = $this->obj->session->userdata('user_data')['user_id'] ?? null) {
			$activityInfo = $this->getUserActivityInfo($memberId);
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
		$queryBuilder = $this->obj->doctrine->em->createQueryBuilder();
		$queryBuilder->select('m.nickName')
			->from('Models\Entities\Member', 'm')
			->where('m.role = :role')
			->andwhere('m.isActive = 1')
			->andwhere('m.blacklist = 0')
			->setParameter('role', 'ROLE_MASTER')
			->setMaxResults(1);

		$query = $queryBuilder->getQuery();
		$result = $query->getOneOrNullResult();

		return $result ? $result['nickName'] : '마스터 계정 없음';
	}

	protected function getTotalMemberCount()
	{
		$queryBuilder = $this->obj->doctrine->em->createQueryBuilder();
		$queryBuilder->select('COUNT(m.id)')
			->from('Models\Entities\Member', 'm')
			->where('m.isActive = 1')
			->andwhere('m.blacklist = 0');
	
		$query = $queryBuilder->getQuery();
		$totalMemberCount = $query->getSingleScalarResult();
	
		return $totalMemberCount;
	}

	protected function getTotalArticleCount()
	{
		$queryBuilder = $this->obj->doctrine->em->createQueryBuilder();
		$queryBuilder->select('COUNT(a.id)')
			->from('Models\Entities\Article', 'a')
			->where('a.isActive = 1');
	
		$query = $queryBuilder->getQuery();
		$totalArticleCount = $query->getSingleScalarResult();
	
		return $totalArticleCount;
	}

	protected function getUserActivityInfo($memberId)
	{
		$articleCount = count($this->obj->MyActivityModel->getArticlesByMemberId($memberId));
		$commentCount = count($this->obj->MyActivityModel->getCommentsByMemberId($memberId));

		return [
			'articleCount' => $articleCount,
			'commentCount' => $commentCount,
		];
	}
}
