<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'product_review';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewHelpful::class, 'product_review_id'); // Ensure 'product_review_id' matches the actual foreign key in the database
    }

    // public function getHelpfulCountAttribute()
    // {
    //     return $this->helpfulVotes()->count(); // Count the number of helpful votes
    // }
}