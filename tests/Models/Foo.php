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
     * bar relations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bar()
    {
        return $this->hasOne(Bar::Class, 'foo_id');
    }

    /**
     * crate relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function crate()
    {
        return $this->hasOne(Crate::class, 'foo_id');
    }

    /**
     * bars relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bars()
    {
        return $this->hasMany(Bar::class, 'foo_id');
    }

    /**
     * crates relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function crates()
    {
        return $this->hasMany(Crate::class, 'foo_id');
    }

    /**
     * creates relation without trashes
     *
     * @return void
     */
    public function untrashedCrates()
    {
        return $this->crates()
            ->withoutTrashed();
    }

    /**
     * crates relation trashes only
     *
     * @return void
     */
    public function trashedCrates()
    {
        return $this->crates()
            ->onlyTrashed();
    }

    /**
     * creates relation active only
     *
     * @return void
     */
    public function activeCrates()
    {
        return $this->crates()
            ->where('status', 'A');
    }

    /**
     * creates relation active only
     *
     * @return void
     */
    public function someCrates()
    {
        return $this->crates()
            ->orWhere('status', 'P');
    }

    /**
     * apple crates relations
     *
     * @return void
     */
    public function appleCrates()
    {
        return $this->hasManyThrough(Crate::class, Apple::class);
    }
}
