<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Deathsoul\Eloquent\Concerns\ExtendsEloquentModel;

class Bar extends Model
{
    use ExtendsEloquentModel;

    /**
     * foo inverse relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function foo()
    {
        return $this->belongsTo(Foo::class, 'foo_id');
    }
}
