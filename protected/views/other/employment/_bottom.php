<style>
    div#students_wrapper {
        width:50%
    }
</style>
<?php
/**
 * @var OtherController $this
 * @var FilterForm $model
 */
?>

<table id="students" class="table table-striped table-bordered table-hover small-rows" >
    <thead>
        <tr>
            <th style="width:15%"><?=tt('Рейтинг')?></th>
            <th><?=tt('Ф.И.О.')?></th>
            <th><?=tt('Общий балл')?></th>
        </tr>
    </thead>

    <tbody>
    <?php
        $html = '';
        $sum = $rating = 0;
        foreach ($students as $student) {

            if ($student['s'] != $sum) {
                $sum = $student['s'];
                $rating++;
            }

            $html .= <<<HTML
        <tr>
            <td>$rating</td>
            <td>$student[st2] $student[st3] $student[st4]</td>
            <td>$sum</td>
        </tr>
HTML;

        }

        echo $html;
    ?>
    </tbody>
</table>