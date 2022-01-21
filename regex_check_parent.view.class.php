<?php


class regex_check_parent_view
{
	protected $mode = null;
	protected $child_view = null;

	public function __construct( regex_check_parent_model $model , regex_check_child_view $view )
	{
		$this->model = $model;
		$this->child_view = $view;
	}
}