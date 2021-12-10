<?php

namespace Maize\Nps\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Maize\Nps\CanAnswerNps;

class User extends Authenticatable
{
    use HasFactory;
    use CanAnswerNps;
}
