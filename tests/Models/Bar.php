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
}
