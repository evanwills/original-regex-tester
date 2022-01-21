<?php

require_once('bootstrap.inc.php');
require_once('preg_test/regex.class.php');
require_once('regex_check_parent.model.class.php');
require_once('regex_check_parent.view.class.php');
require_once('regex_check_child.model.class.php');
require_once('regex_check_child.view.class.php');

if( __FILE__ == 'json.php' )
{
	$output_type = 'json';
}
else
{
	$output_type = 'html';
}

require_once('regex_check_parent_'.$output_type.'.view.class.php');
require_once('regex_check_child_'.$output_type.'.view.class.php');

// ==================================================================


$model = new regex_check_parent_model();
$classname = 'regex_check_child_view_'.$output_type;
$child_view = new $classname($model);

if( !$classname::set_len($model->get_prop('sample_len'),'sample') )
{
	$model->set_len_not_ok('sample');
}
if( !$classname::set_len($model->get_prop('matched_len'),'matched') )
{
	$model->set_len_not_ok('matched');
}

$classname = 'regex_check_parent_view_'.$output_type;
if( $model->get_prop('split_sample') )
{
	$classname .= '_multi';
}

$parent_view = new $classname($model,$child_view);

echo $parent_view->get_output();
