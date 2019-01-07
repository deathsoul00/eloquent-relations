<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Concerns;

use Deathsoul\Eloquent\Builder;
use Deathsoul\Eloquent\Concerns\HasRelations;

trait ExtendsEloquentModel
{
    use HasRelations;

    /**
     * create instance of eloquent query builder
     *
     * @param  \Illuminate\Database\Eloquent\Query $query
     *
     * @return \Deathsoul\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        $builder = new Builder($query);

        // .. reserved

        return $builder;
    }
}
