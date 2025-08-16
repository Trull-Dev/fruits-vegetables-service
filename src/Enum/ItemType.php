<?php
declare(strict_types=1);

namespace App\Enum;

enum ItemType: string
{
    case Fruit = 'fruit';
    case Vegetable = 'vegetable';
}
