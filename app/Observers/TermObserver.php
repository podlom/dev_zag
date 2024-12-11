<?php

namespace App\Observers;

use App\Models\Term;

class TermObserver
{
    public function deleted(Term $term)
    {
        $term->translations()->delete();
    }
}
