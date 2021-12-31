<?php
declare(strict_types=1);

namespace TechWilk\Church\Teachings\Model;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    public $table = 'teaching_series';

    public function teachings() {
        return $this->hasMany(Teaching::class);
    }
}