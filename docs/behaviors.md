# Vote behaviors

In case when app renders a lot of some items (models) and you need to attach vote module to each it's better to add vote behaviors to your model.

With this behaviors you'll decrease the count of sql queries dramatically.

For this you have to include these behaviors:

- *VoteBehavior*: allows you to get vote data (votes count, user vote status).
- *VoteQueryBehavior*: allows you to include vote search condition to your query.


## Configuration

Imagine that you have model **Item** (`app\models\Item.php`):

```php
<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use hauntd\vote\behaviors\VoteBehavior;

class Item extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%item}}';
    }

    public function behaviors()
    {
        return [
            VoteBehavior::className(), // add VoteBehavior class to your model
        ];
    }

    public static function find()
    {
        return new ItemQuery(get_called_class()); // override find() method
    }
}
```

If you don't have `ItemQuery` class (or other query class for you model) - create new one and attach **VoteQueryBehavior**:

```php
<?php

namespace app\models;

use hauntd\vote\behaviors\VoteQueryBehavior;

class ItemQuery extends \yii\db\ActiveQuery
{
    public function behaviors()
    {
        return [
            VoteQueryBehavior::className(),
        ];
    }
}
```

After that you can use `withVoteAggregate($entity)` and `withUserVote($entity)` query methods.

## Example

`app/controllers/ItemsController.php`:

```php
<?php

namespace app\controllers;

use app\models\Item;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class ItemsController extends Controller
{
    public function actionIndex()
    {
        $query = Item::find();

        foreach (['itemVote', 'itemFavorite'] as $entity) {
            $query->withVoteAggregate($entity); // include votes and favorites
            $query->withUserVote($entity); // include user vote status
        }

        /**
         * After attaching behaviors, you'll get access to new attributes - positive, negative and rating
         * So, if you have 'itemVote' entity, you should use 'itemVotePositive', 'itemVoteNegative' and
         * 'itemVoteRating' attributes.
         *
         * For example:
         */
        $query->orderBy('itemVoteRating desc');
        // or
        $query->orderBy('itemFavoritePositive desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ]
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}
```

`app/views/items/index.php`:

```php
<?= \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items} {pager}',
    'itemView' => '_item',
]) ?>
```

`app/views/items/_item.php`:

```php
<div class="item">
    <div class="item-content">
        <?= \yii\helpers\Html::encode($model->content) ?>
    </div>
    <div class="item-buttons'>
        <?= \hauntd\vote\widgets\Vote::widget([
            'entity' => 'itemVote',
            'model' => $model,
        ]); ?>
        <?= \hauntd\vote\widgets\Favorite::widget([
            'entity' => 'itemFavorite',
            'model' => $model,
        ]); ?>
    </div>
</div>
```
