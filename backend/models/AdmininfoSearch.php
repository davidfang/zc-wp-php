<?php
/**
* Admininfo搜索模型
* Created by David
* User: David.Fang
* Date: 2015-07-21* Time: 13:45:56*/
namespace app\models;

use app\models\AdminUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AdminInfo;

/**
 * AdmininfoSearch represents the model behind the search form about `app\models\Admininfo`.
 */
class AdmininfoSearch extends AdminInfo
{
    public $username;
    public $email;
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            //'username'=>'姓名',
            'city' => 'City',
            'department' => '部门',
            'status' => '状态',
            'in_time' => '入职时间',
            'id_number' => '身份证号',
            'sex' => '性别',
            'birthday' => '生日',
            'birthday_month' => '生日月份',
            'age' => '年龄',
            'mobile' => '联系电话',
            'created_at' => '建立时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'birthday_month', 'age', 'created_at', 'updated_at'], 'integer'],
            [['city', 'department', 'status', 'in_time', 'id_number', 'sex', 'birthday', 'mobile','username','email'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AdminInfo::find();
        //$query->with('user');
        $query->joinWith('user');
        //$join_tale = $this->getDb()->quoteTableName('{{%admin_user}}');
        //$join_tale = $this->getDb()->quoteTableName("admin_user");
        //$join_tale = AdminUser::tableName();
        //echo $join_tale;exit;
        //$query->joinWith([$join_tale]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => '10',
            ]
        ]);

        $sort = $dataProvider->getSort();
        $tmp_sort =  [
            /* 其它字段不要动 */
            /*  下面这段是加入的 */
            /*=============*/
            'username' => [
                'asc' => ['username' => SORT_ASC],
                'desc' => ['username' => SORT_DESC],
                'label' => 'username'
            ],
            'email' => [
                'asc' => ['email' => SORT_ASC],
                'desc' => ['email' => SORT_DESC],
                'label' => 'email'
            ],
            /*=============*/

        ];
        $dataProvider->setSort(['attributes' => array_merge($sort->attributes,$tmp_sort)]);

//        $dataProvider->setSort([
//            'attributes' => [
//                //'age',
//                //'sex',
//                /* 其它字段不要动 */
//                /*  下面这段是加入的 */
//                /*=============*/
//                'username' => [
//                    'asc' => ['username' => SORT_ASC],
//                    'desc' => ['username' => SORT_DESC],
//                    'label' => '姓名'
//                ],
//                'email' => [
//                    'asc' => ['email' => SORT_ASC],
//                    'desc' => ['email' => SORT_DESC],
//                    'label' => '邮箱'
//                ],
//                /*=============*/
//            ]
//        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'birthday_month' => $this->birthday_month,
            'age' => $this->age,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['like','username',$this->username]);//添加用户名搜索
        $query->andFilterWhere(['like','email',$this->email]);//添加用户邮箱搜索
        $query->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'in_time', $this->in_time])
            ->andFilterWhere(['like', 'id_number', $this->id_number])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'birthday', $this->birthday])
            ->andFilterWhere(['like', 'mobile', $this->mobile]);

        return $dataProvider;
    }
}
