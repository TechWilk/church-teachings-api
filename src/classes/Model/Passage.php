<?php
declare(strict_types=1);

namespace TechWilk\Church\Teachings\Model;

use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    public $table = 'teaching_passages';

    public function teaching() {
        return $this->belongsTo(Teaching::class);
    }
}