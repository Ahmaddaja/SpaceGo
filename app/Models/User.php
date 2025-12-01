<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable //implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'password',
        'role',
        'telepon',
        'perusahaan',
        'alamat',
        'foto',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }
    public function rakDibeli()
    {
        return $this->belongsToMany(Rak::class, 'transaksis', 'user_id', 'rak_id')
            ->withPivot(['tanggal_mulai', 'tanggal_berakhir', 'status', 'total_harga'])
            ->withTimestamps();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Relasi ke Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
