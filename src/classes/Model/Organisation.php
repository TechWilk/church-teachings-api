<?php
declare(strict_types=1);

namespace TechWilk\Church\Teachings\Model;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    public $table = 'organisers';

    public function teachings() {
        return $this->hasMany(Teaching::class);
    }
}