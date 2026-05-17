<?php

namespace App\Enums;

enum SongAuthorRole: string
{
    case Author = 'author';
    case Composer = 'composer';
    case Performer = 'performer';
}