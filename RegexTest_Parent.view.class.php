<?php


class RegexTest_ParentView
{
    protected $mode = null;
    protected $child_view = null;

    public function __construct(
        RegexTest_ParentModel $model,
        RegexTest_ChildView $view
    ) {
        $this->model = $model;
        $this->child_view = $view;
    }
}
