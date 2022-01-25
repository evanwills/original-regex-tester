<?php
/**
 * This file sets up the environment and does all the basic work of
 * the Regex Debugger applications
 *
 * PHP Version 5.4, 7.x, 8.0
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */

require_once 'bootstrap.inc.php';
require_once 'regex.class.php';
require_once 'RegexTest_Parent.model.class.php';
require_once 'RegexTest_Parent.view.class.php';
require_once 'RegexTest_Child.model.class.php';
require_once 'RegexTest_Child.view.class.php';

if (__FILE__ == 'json.php') {
    $outputType = 'Json';
} else {
    $outputType = 'Html';
}

require_once 'RegexTest_Parent'.$outputType.'.view.class.php';
require_once 'RegexTest_Child'.$outputType.'.view.class.php';

// ==================================================================


$model = new RegexTest_ParentModel();
$className = 'RegexTest_ChildView'.$outputType;
// debug($className);
$childView = new $className($model);

if (!$className::setLen($model->getProp('sampleLen'), 'sample')) {
    $model->setLenNotOk('sample');
}
if (!$className::setLen($model->getProp('matchedLen'), 'matched')) {
    $model->setLenNotOk('matched');
}

$className = 'RegexTest_ParentView'.$outputType;
// debug($className);
if ($model->getProp('splitSample')) {
    $className .= 'Multi';
}

$parent_view = new $className($model, $childView);

echo $parent_view->getOutput();
