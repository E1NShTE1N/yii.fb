<?php

/**
 * This is the model class for table "us".
 *
 * The followings are the available columns in table 'us':
 * @property integer $us1
 * @property integer $us2
 * @property integer $us3
 * @property integer $us4
 * @property double $us5
 * @property double $us6
 * @property integer $us10
 * @property integer $us11
 * @property integer $us12
 * @property integer $us13
 * @property double $us14
 * @property integer $us15
 */
class Us extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'us';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('us1, us2, us3, us4, us10, us11, us12, us13, us15', 'numerical', 'integerOnly'=>true),
			array('us5, us6, us14', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('us1, us2, us3, us4, us5, us6, us10, us11, us12, us13, us14, us15', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'us1' => 'Us1',
			'us2' => 'Us2',
			'us3' => 'Us3',
			'us4' => 'Us4',
			'us5' => 'Us5',
			'us6' => 'Us6',
			'us10' => 'Us10',
			'us11' => 'Us11',
			'us12' => 'Us12',
			'us13' => 'Us13',
			'us14' => 'Us14',
			'us15' => 'Us15',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('us1',$this->us1);
		$criteria->compare('us2',$this->us2);
		$criteria->compare('us3',$this->us3);
		$criteria->compare('us4',$this->us4);
		$criteria->compare('us5',$this->us5);
		$criteria->compare('us6',$this->us6);
		$criteria->compare('us10',$this->us10);
		$criteria->compare('us11',$this->us11);
		$criteria->compare('us12',$this->us12);
		$criteria->compare('us13',$this->us13);
		$criteria->compare('us14',$this->us14);
		$criteria->compare('us15',$this->us15);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Us the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getHoursForWorkLoad($pd1, $year)
    {
        if (empty($pd1) || empty($year))
            return array();

        $sql= <<<SQL
           SELECT sem5, us4, nr6, nr7, nr8, nr9, sum(nr3)
		   FROM sem
            INNER JOIN us on (sem.sem1 = us.us12)
            INNER JOIN nr on (us.us1 = nr.nr2)
            INNER JOIN uo on (us.us2 = uo.uo1)
            INNER JOIN u on (uo.uo22 = u.u1)
            INNER JOIN c on (u.u15 = c.c1)
            INNER JOIN pd ON (nr.nr6 = pd.pd1) OR (nr.nr7 = pd.pd1) OR (nr.nr8 = pd.pd1)
		   WHERE pd1=:PD1 AND sem3=:SEM3 AND c8 != 3
		   GROUP BY sem5, us4, nr6, nr7, nr8, nr9
		   ORDER BY sem5, us4
SQL;
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $pd1);
        $command->bindValue(':SEM3', $year);
        $res = $command->queryAll();

        $data = array(
            0 => array(
                0 => array('sum' => null)
            ),
            1 => array(
                0 => array('sum' => null)
            ),
        );

        $map = array(
            9  => 1, // УЛк
            10 => 2, // УПз
            11 => 3, // УСем
            12 => 4, // УЛб
        );

        foreach ($res as $arr) {

            $sem5 = $arr['sem5'];
            $us4  = $arr['us4'];

            // sub groups {{{
            $subGroups = array($arr['nr6'], $arr['nr7'], $arr['nr8'], $arr['nr9']);
            $countSubGroups = array_count_values($subGroups);
            $arr['sum'] = $arr['sum']*$countSubGroups[$pd1];
            // }}}

            $data[$sem5][$us4] = $arr;

            if (in_array($us4, array(9, 10, 11, 12))) {

                $_us4 = $map[$us4];

                if (! isset($data[$sem5][$_us4]))
                    $data[$sem5][$_us4] = $data[$sem5][$us4];
                else
                    $data[$sem5][$_us4]['sum'] += $arr['sum'];

            }

            $data[$sem5][0]['sum'] += $arr['sum']; // Всего
        }

        for ($sem5=0; $sem5<=1; $sem5++) {

            $prak = $this->getPrakFor($pd1, $year, $sem5);
            $data[$sem5]['Prak']['sum'] = $prak;

            $dipl = $this->getDiplFor($pd1, $year, $sem5);
            $data[$sem5]['Dipl']['sum'] = $dipl;

            $gek = $this->getGekFor($pd1, $year, $sem5);
            $data[$sem5]['Gek']['sum'] = $gek;

            $asp = $this->getAspFor($pd1, $year, $sem5);
            $data[$sem5]['Asp']['sum'] = $asp;

            $dop = $this->getDopFor($pd1, $year, $sem5);
            $data[$sem5]['Dop']['sum'] = $dop;

            $data[$sem5][0]['sum'] += $prak+$dipl+$gek+$asp+$dop;
        }

        return $data;
    }

    private function getPrakFor($pd1, $year, $sem5)
    {
        $sql = <<<SQL
          SELECT sum(prun3)
          FROM spr
          INNER JOIN pru on (spr.spr1 = pru.pru3)
          INNER JOIN prun on (pru.pru1 = prun.prun1)
          INNER JOIN pd on (prun.prun2 = pd.pd1)
          INNER JOIN sg on (pru.pru2 = sg.sg1)
          INNER JOIN sem on (sg.sg1 = sem.sem2)
          WHERE pru4 = sem7 and pd1=:PD1 and sem3=:SEM3 and sem5 = :SEM5
SQL;

        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $pd1);
        $command->bindValue(':SEM3', $year);
        $command->bindValue(':SEM5', $sem5);
        $sum = $command->queryScalar();

        if ($sum == 0)
            $sum = '';

        return $sum;
    }

    private function getDiplFor($pd1, $year, $sem5)
    {
        $sql = <<<SQL
          SELECT sum(dipn3)
		  FROM sem
		  INNER JOIN dnk on (sem.sem1 = dnk.dnk2)
		  INNER JOIN dipn on (dnk.dnk1 = dipn.dipn1)
		  INNER JOIN pd on (dipn.dipn2 = pd.pd1)
		  WHERE pd1=:PD1 AND sem3=:SEM3 and sem5 = :SEM5
SQL;

        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $pd1);
        $command->bindValue(':SEM3', $year);
        $command->bindValue(':SEM5', $sem5);
        $sum = $command->queryScalar();

        if ($sum == 0)
            $sum = '';

        return $sum;
    }

    private function getGekFor($pd1, $year, $sem5)
    {
        $sql = <<<SQL
            SELECT sum(nr3)
            FROM sem
            INNER JOIN us on (sem.sem1 = us.us12)
            INNER JOIN nr on (us.us1 = nr.nr2)
            INNER JOIN uo on (us.us2 = uo.uo1)
            INNER JOIN u on (uo.uo22 = u.u1)
            INNER JOIN c on (u.u15 = c.c1)
            INNER JOIN pd ON (nr.nr6 = pd.pd1) OR (nr.nr7 = pd.pd1) OR (nr.nr8 = pd.pd1) OR (nr.nr9 = pd.pd1)
            WHERE c8=3 and pd1=:PD1 AND sem3=:SEM3 AND sem5=:SEM5
            GROUP BY sem5, us4, nr6, nr7, nr8, nr9
            ORDER BY sem5, us4
SQL;

        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $pd1);
        $command->bindValue(':SEM3', $year);
        $command->bindValue(':SEM5', $sem5);
        $sum = $command->queryScalar();

        if ($sum == 0)
            $sum = '';

        return $sum;
    }

    private function getAspFor($pd1, $year, $sem5)
    {
        $sql = <<<SQL
        SELECT vrnar4
        FROM vrn
        INNER JOIN vrna on (vrn.vrn1 = vrna.vrna3)
        INNER JOIN vrnar on (vrna.vrna1 = vrnar.vrnar2)
        WHERE (vrnar3 = :PD1) and (vrna4 = :SEM3) and (vrna6 = :SEM5)
SQL;

        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $pd1);
        $command->bindValue(':SEM3', $year);
        $command->bindValue(':SEM5', $sem5);
        $sum = $command->queryScalar();

        if ($sum == 0)
            $sum = '';

        return $sum;
    }

    private function getDopFor($pd1, $year, $sem5)
    {
        $sql = <<<SQL
        SELECT DNAR4
        FROM dna
        INNER JOIN dnar on (dna.dna1 = dnar.dnar2)
        INNER JOIN d on (dna.dna2 = d.d1)
        WHERE DNAR3=:PD1 and DNA4=:SEM3 and DNA7=:SEM5
SQL;

        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $pd1);
        $command->bindValue(':SEM3', $year);
        $command->bindValue(':SEM5', $sem5);
        $sum = $command->queryScalar();

        if ($sum == 0)
            $sum = '';

        return $sum;
    }

    public function getHoursForWorkLoadAmount(FilterForm $model, array $discipline)
    {
        $condition = null;
        if ($model->extendedForm == 1)
            $condition = 'and ug3 = '.$discipline['ug3'].' and us4 = '.$discipline['us4'];

        $sql= <<<SQL
            SELECT d1, d2, us4, nr3, ug3
            FROM sem
            INNER JOIN us ON (sem.sem1 = us.us12)
            INNER JOIN nr ON (us.us1 = nr.nr2)
            INNER JOIN pd ON (nr.nr6 = pd.pd1) or (nr.nr7 = pd.pd1) or (nr.nr8 = pd.pd1) or (nr.nr9 = pd.pd1)
            INNER JOIN ug ON (nr.nr1 = ug.ug3)
            INNER JOIN uo ON (us.us2 = uo.uo1)
            INNER JOIN d ON (uo.uo3 = d.d1)
            INNER JOIN gr ON (ug.ug2 = gr.gr1)
            WHERE pd1=:PD1 and sem3=:SEM3 and sem5=:SEM5 and d1 = :D1 {$condition}
            GROUP BY d1, d2, us4, nr3, ug3
            ORDER BY us4,d1,d2
SQL;
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':PD1', $model->teacher);
        $command->bindValue(':SEM3', $model->year);
        $command->bindValue(':SEM5', $model->semester);
        $command->bindValue(':D1', $discipline['d1']);
        $res = $command->queryAll();

        $data = array();

        $map = array(
            9  => 1, // УЛк
            10 => 2, // УПз
            11 => 3, // УСем
            12 => 4, // УЛб
        );

        foreach ($res as $arr) {

            $us4 = $arr['us4'];

            if (in_array($us4, array(9, 10, 11, 12)))
                $us4 = $map[$us4];

            if (! isset($data[$us4]))
                $data[$us4] = null;

            $data[$us4] += round($arr['nr3']);
        }

        return $data;
    }
}
