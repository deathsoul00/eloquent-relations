<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;

class UnsupportedRelationException extends Exception
{
    /**
     * create instance
     *
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation
     */
    public function __construct(Relation $relation)
    {
        parent::__construct(sprintf('Relation %s is not yet currently supported', get_class($relation)));
    }
}
