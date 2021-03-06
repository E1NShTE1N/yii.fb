<?php

/**
 * @var FilterForm $model
 * @var CActiveForm $form
 */

$options = array('class'=>'chosen-select', 'autocomplete' => 'off', 'empty' => '&nbsp;', 'style' => 'width:200px');

$form=$this->beginWidget('CActiveForm', array(
    'id'=>'filter-form',
    'htmlOptions' => array('class' => 'form-inline')
));

    $html  = '<div>';
        $html .= '<fieldset>';
        $filials = CHtml::listData(Ks::model()->findAll(), 'ks1', 'ks2');
        if (count($filials) > 1) {
            $html .= '<div class="row-fluid span2">';
            $html .= $form->label($model, 'filial');
            $html .= $form->dropDownList($model, 'filial', $filials, $options);
            $html .= '</div>';
        }

        $chairs = CHtml::listData(K::model()->getOnlyChairsFor($model->filial), 'k1', 'k3');
        $html .= '<div class="row-fluid span2">';
        $html .= $form->label($model, 'chair');
        $html .= $form->dropDownList($model, 'chair', $chairs, $options);
        $html .= '</div>';

        $teachers = P::model()->getTeachersWorkLoad($model->chair);
        $html .= '<div class="row-fluid span2">';
        $html .= $form->label($model, 'teacher');
        $html .= $form->dropDownList($model, 'teacher', $teachers, $options);
        $html .= '</fieldset>';
    $html .= '</div>';


    echo $html;

$this->endWidget();