<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Deathsoul\Eloquent\Concerns\ExtendsEloquentModel;

class Crate extends Model
{
    use ExtendsEloquentModel,
        SoftDeletes;

    /**
     * foo inverse relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function foo()
    {
        return $this->belongsTo(Foo::class, 'foo_id');
    }

    /**
     * apples relations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apples()
    {
        return $this->hasMany(Apple::class, 'crate_id');
    }
}
