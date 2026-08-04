<?php

namespace App\Enums;

enum FilmStatus: string
{
    case Published = 'published';
    case Draft     = 'draft';
}
