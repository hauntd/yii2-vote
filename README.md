# Yii 2 Vote

[![Latest Version on Packagist][ico-version]][link-packagist]
[![License (3-Clause BSD)](https://img.shields.io/badge/license-BSD%203--Clause-blue.svg?style=flat-square)](LICENSE.md)
[![Code Climate](https://codeclimate.com/github/hauntd/yii2-vote/badges/gpa.svg?style=flat-square)](https://codeclimate.com/github/hauntd/yii2-vote)
[![Total Downloads][ico-downloads]][link-downloads]

Votes, ratings, likes, favorites.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist hauntd/yii2-vote "*"
```

or add

```
"hauntd/yii2-vote": "*"
```

to the require section of your `composer.json` file.

## Configuration

Add module settings to your application config (`config/main.php`):
Entity names like `item.vote`, `item.vote.guests`, `item.like` and `item.favorite` are used in widgets.

```php
<?php
return [
  'modules' => [
    'vote' => [
      'class' => hauntd\vote\Module::class,
        'guestTimeLimit' => 3600,
        'entities' => [
          // Entity -> Settings()
          'item.vote' => app\models\Item::class, // your model
          'item.vote.guests' => [
              'modelName' => app\models\Item::class, // your model
              'allowGuests' => true,
          ],
          'item.like' => [
              'modelName' => app\models\Item::class, // your model
              'type' => hauntd\vote\Module::TYPE_TOGGLE, // like/favorite button
          ],
          'item.favorite' => [
              'modelName' => app\models\Item::class, // your model
              'type' => hauntd\vote\Module::TYPE_TOGGLE, // like/favorite button
          ],
      ],
    ],
  ],
];
```

After you downloaded and configured `hauntd/yii2-vote`, the last thing you need to do is updating your database schema by applying the migrations:

```
php yii migrate/up --migrationPath=@vendor/hauntd/yii2-vote/migrations/
```

## Usage

Vote widget:

```php
<?= \hauntd\vote\widgets\Vote::widget([
  'entity' => 'item.vote',
  'model' => $model,
  'options' => ['class' => 'vote vote-visible-buttons']
]); ?>
```

Like/Favorite widgets:

```php
<?= \hauntd\vote\widgets\VoteToggle::widget([
    'entity' => 'item.favorite',
    'model' => $model,
]); ?>

<?= \hauntd\vote\widgets\Like::widget([
    'entity' => 'item.like',
    'model' => $model,
]); ?>
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

BSD 3-Clause License. Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hauntd/yii2-vote.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hauntd/yii2-vote.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/hauntd/yii2-vote
[link-downloads]: https://packagist.org/packages/hauntd/yii2-vote
[link-author]: https://github.com/hauntd
