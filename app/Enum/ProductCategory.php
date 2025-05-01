<?php

namespace App\Enum;

enum ProductCategory: string
{
    case COFFEE = 'coffee';
    case TEA = 'tea';
    case PASTRY = 'pastry';
    case SNACK = 'snack';
    case MERCHANDISE = 'merchandise';
}
