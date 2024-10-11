<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class BookSearch extends Book
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['title'], 'safe'],
            [['published_year'], 'integer'],
            [['isbn'], 'integer'],
        ];
    }

    /**
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Book::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['published_year' => $this->published_year])
            ->andFilterWhere(['isbn' => $this->isbn]);

        return $dataProvider;
    }
}
