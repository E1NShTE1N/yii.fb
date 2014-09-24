<?php
/**
 *
 * @var OtherController $this
 * @var TimeTableForm $model
 * @var CActiveForm $form
 */

$html = $this->renderPartial('/timeTable/group', array(
    'model'      => $model,
    'timeTable'  => $timeTable,
    'minMax'     => $minMax,
    'maxLessons' => $maxLessons,
    'rz'         => Rz::model()->getRzArray(),
), true);


$this->pageHeader=tt('Заказ переноса занятий');
$this->breadcrumbs=array(
    tt('Другое'),
);

Yii::app()->clientScript->registerPackage('jquery.ui');
Yii::app()->clientScript->registerPackage('datepicker');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/other/orderLesson.js', CClientScript::POS_HEAD);

$orderLesson = tt('Заказать перенос занятия?');
$cancel      = tt('Отмена');
Yii::app()->clientScript->registerScript('orderLesson-messages', <<<JS
    tt.orderLesson  = '{$orderLesson}';
    tt.cancel       = '{$cancel}';
JS
    , CClientScript::POS_READY);

echo $html;

$this->renderPartial('orderLesson/popup', array());
