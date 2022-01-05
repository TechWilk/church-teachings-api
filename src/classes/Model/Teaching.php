<?php
declare(strict_types=1);

namespace TechWilk\Church\Teachings\Model;

use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    public function organiser() {
        return $this->belongsTo(Organisation::class);
    }

    public function passages() {
        return $this->hasMany(Passage::class);
    }

    public function series() {
        return $this->belongsTo(Passage::class);
    }

    public function speaker() {
        return $this->belongsTo(Speaker::class);
    }
}