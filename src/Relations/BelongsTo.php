<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Relations;

use Deathsoul\Eloquent\Concerns\Relations;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;

class BelongsTo extends EloquentBelongsTo
{
    use Relations;
}
