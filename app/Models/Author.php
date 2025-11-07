<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $table = 'authors';
    protected $fillable = ['name'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Hitung rata-rata rating semua buku milik author ini
     */
    public function averageRating()
    {
        return $this->hasManyThrough(Rating::class, Book::class, 'author_id', 'book_id');
    }
}
