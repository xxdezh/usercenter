<?php

class Activity_wenda_user extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Wx the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{activity_wenda_user}}';
    }

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

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
            'member'=>array(self::BELONGS_TO, 'Member', '', 'on'=>'t.mid=member.id')
        );
    }


    public static function getPartakeNum($AId){
        $sql = "select count(distinct Mid) as amount from {{activity_wenda_user}} where  FAid=".$AId."";
        $count = Mod::app()->db->createCommand($sql)->queryRow();
        return $count['amount'];
    }


    //活动列表带分页
    public function getUserListPager($id,$search,$username){
        $as_list = array();
        $list = null;
        $asModel = new activity_wenda_user;
        $criteria = new CDbCriteria();
        $criteria->order = 'time DESC';

        if (!empty($search) && !empty($username)) {
            $criteria->condition = 't.wendaid =:wendaid and t.code like :code and member.username like :username';
            $criteria->params = array(':wendaid' => $id, ':code' => '%' . $search . '%', ':username' => '%' . $username . '%');
        } elseif (!empty($search) && empty($username)) {
            $criteria->condition = 't.wendaid =:wendaid and t.code like :code';
            $criteria->params = array(':wendaid' => $id, ':code' => '%' . $search . '%');
        } elseif (!empty($username) && empty($search)) {
            $criteria->condition = 't.wendaid =:wendaid and member.username like :username';
            $criteria->params = array(':wendaid' => $id, ':username' => '%' . $username . '%');
        } else {
            $criteria->condition = 'wendaid=' . $id;
        }
        
        $count = $asModel->with('member')->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = 10;
        $criteria->limit = $pages->pageSize;
        $criteria->offset = $pages->currentPage * $pages->pageSize;
        $as_list['count'] = $count;
        $as_list['pagebar'] = $pages;
        $as_list['criteria'] = $asModel->with('member')->findAll($criteria);
        return $as_list;
    }
}