<?php
declare(strict_types=1);

namespace TechWilk\Church\Teachings\Model;

use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    public function teachings() {
        return $this->hasMany(Teaching::class);
    }
}