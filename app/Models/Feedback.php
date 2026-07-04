<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    // Mengunci nama tabel secara tegas agar Eloquent tidak mencari nama 'feedback' tunggal
    protected $table = 'feedbacks'; 

    protected $fillable = [
        'kategori',
        'pesan',
    ];
}