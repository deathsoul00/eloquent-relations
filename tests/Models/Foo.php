<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Deathsoul\Eloquent\Concerns\ExtendsEloquentModel;

class Foo extends Model
{
    use ExtendsEloquentModel;

    /**
     * bar relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bar()
    {
        return $this->hasOne(Bar::Class, 'foo_id');
    }

    /**
     * apple relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function apple()
    {
        return $this->hasOne(Apple::class, 'foo_id');
    }
}
