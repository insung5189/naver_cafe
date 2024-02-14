<?php
class Layout 
{

	public function __construct()
	{
		$this->obj = &get_instance(); // ci 코어 객체를 가져옴
	}

	function view($view = "", $page_view_data = array())
	{
		$layout_view_data = array(
			"title" => isset($page_view_data['title']) ? $page_view_data['title'] : '기본 제목',
			"contents" => $this->obj->load->view($view, $page_view_data, true)
		);
		$this->obj->load->view("/layouts/main_layout_view", $layout_view_data);
	}
}