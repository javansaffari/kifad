<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id', // for main/sub category relation
        'type',      // 'expense' or 'income'
    ];

    /**
     * Get the subcategories of this category
     */
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the parent category (if this is a subcategory)
     */
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
