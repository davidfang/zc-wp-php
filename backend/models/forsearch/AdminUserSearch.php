<?php

namespace app\models\forsearch;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AdminUser;

/**
 * AdminUserSearch represents the model behind the search form about `app\models\AdminUser`.
 */
class AdminUserSearch extends AdminUser
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username',  'userphoto','email'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AdminUser::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
           // ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'eamil', $this->email])
            ->andFilterWhere(['like', 'userphoto', $this->userphoto]);

        return $dataProvider;
    }
}
