# laravel-cascade-deletes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.txt)
[![Build Status][ico-github-actions]][link-github-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This Laravel/Lumen package provides application level cascading deletes for the Laravel's Eloquent ORM. When referential integrity is not, or cannot be, enforced at the data storage level, this package makes it easy to set this up at the application level.

For example, if you are using `SoftDeletes`, or are using polymorphic relationships, these are situations where foreign keys in the database cannot enforce referential integrity, and the application needs to step in. This package can help.

## Versions

This package has been tested on Laravel 4.1 through Laravel 10.x, though it may continue to work on later versions as they are released. This section will be updated to reflect the versions on which the package has actually been tested.

This readme has been updated to show information for the most currently supported versions (9.x - 10.x). For Laravel 4.1 through Laravel 8.x, view the 1.x branch.

## Install

Via Composer

``` bash
composer require shiftonelabs/laravel-cascade-deletes
```

## Usage

Enabling cascading deletes can be done two ways. Either:
- update your `Model` to extend the `\ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletesModel`, or
- update your `Model` to use the `\ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes` trait.

Once that is done, define the `$cascadeDeletes` property on the `Model`. The `$cascadeDeletes` property should be set to an array of the relationships that should be deleted when a parent record is deleted.

Now, when a parent record is deleted, the defined child records will also be deleted. Furthermore, in the case where a child record also has cascading deletes defined, the delete will cascade down and delete the related records of the child, as well. This will continue on until all children, grandchildren, great grandchildren, etc. are deleted.

Additionally, all cascading deletes are performed within a transaction. This makes the delete an "all or nothing" event. If, for any reason, a child record could not be deleted, the transaction will rollback and no records will be deleted at all. The `Exception` that caused the child not to be deleted will bubble up to where the `delete()` originally started, and will need to be caught and handled.**

#### Code Example

User Model:

``` php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class User extends Model {
    use CascadesDeletes;

    protected $cascadeDeletes = ['posts', 'profile'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
```

Profile Model:

``` php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Profile extends Model {
    use CascadesDeletes;

    protected $cascadeDeletes = ['addresses'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->morphsMany(Address::class, 'addressable');
    }
}
```

In the example above, the `CascadesDeletes` trait has been added to the `User` model to enable cascading deletes. Since the user is considered a parent of posts and profiles, these relationships have been added to the `$cascadeDeletes` property. Additionally, the `Profile` model has been set up to delete its related address records.

Given this setup, when a user record is deleted, all related posts and profile records will be deleted. The delete will also cascade down into the profile record, and it will delete all the addresses related to the profile, as well.

If any one of the posts, profiles, or addresses fails to be deleted, the transaction will roll back and no records will be deleted, including the original user record.**

** Transaction rollback will only occur if the database being used actually supports transactions. Most do, but some do not. For example, the MySQL `InnoDB` engine supports transactions, but the MySQL `MyISAM` engine does not.

#### SoftDeletes

This package also works with Models that are setup with `SoftDeletes`.

When using `SoftDeletes`, the delete method being used will cascade to the rest of the deletes, as well. That is, if you `delete()` a record, all the child records will also use `delete()`; if you `forceDelete()` a record, all the child records will also use `forceDelete()`.

The deletes will also cross the boundary between soft deletes and hard deletes. In the code example above, the the `User` record was setup to soft delete, but the `Profile` record was not, then when a user is deleted, the `User` record would be soft deleted, but the child `Profile` record would be hard deleted, and vice versa.

## Notes

- The functionality in this package is provided through the `deleting` event on the `Model`. Therefore, in order for the cascading deletes to work, `delete()` must be called on a model instance. Deletes will not cascade if a delete is performed through the query builder. For example, `App\User::where('active', 0)->delete();` will only delete those user records, and will not perform any cascading deletes, since the `delete()` was performed on the query builder and not on a model instance.

- Do not add a `BelongsTo` relationship to the `$cascadeDeletes` array. This will cause a `LogicException`, and no records will be deleted. This is done as a `BelongsTo` typically represents a child record, and it usually does not make sense to delete a parent record from a child record.

## Contributing

Contributions are welcome. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email patrick@shiftonelabs.com instead of using the issue tracker.

## Credits

- [Patrick Carlo-Hickman][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.txt) for more information.

[ico-version]: https://img.shields.io/packagist/v/shiftonelabs/laravel-cascade-deletes.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-github-actions]: https://img.shields.io/github/actions/workflow/status/shiftonelabs/laravel-cascade-deletes/.github/workflows/phpunit.yml?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/shiftonelabs/laravel-cascade-deletes.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/shiftonelabs/laravel-cascade-deletes.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/shiftonelabs/laravel-cascade-deletes.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/shiftonelabs/laravel-cascade-deletes
[link-github-actions]: https://github.com/shiftonelabs/laravel-cascade-deletes/actions
[link-scrutinizer]: https://scrutinizer-ci.com/g/shiftonelabs/laravel-cascade-deletes/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/shiftonelabs/laravel-cascade-deletes
[link-downloads]: https://packagist.org/packages/shiftonelabs/laravel-cascade-deletes
[link-author]: https://github.com/patrickcarlohickman
[link-contributors]: ../../contributors
