<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Fingerprint;
use App\Models\FingerprintClocklogs;
use App\Models\AttendanceSchedule;
use App\Models\FaceDescriptor;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'fingerprint_id',
        'position_id',
        'type',
        'hire_date', 
        'status',
        'ic',
        'dob',
        'gender',
        'phone',
        'marital_status',
        'nationality',
        'address',
        'bank_name',
        'bank_account_holder_name', 
        'bank_account_number',
        'annual_leave_days',
        'profile_picture',
        'profile_completed',
        'is_malaysian',
        'is_admin',
        'face_descriptor',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'hire_date' => 'date:Y-m-d',
        ];
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function kpiEntries()
    {
        return $this->hasMany(KpiEntry::class, 'users_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function fingerprintClocklogs()
    {
        return $this->hasMany(FingerprintClocklogs::class);
    }

    public function attendanceSchedules()
    {
        return $this->hasMany(AttendanceSchedule::class);
    }

    public function annualLeaveBalances()
    {
        return $this->hasMany(AnnualLeaveBalance::class);
    }

    public function faceDescriptors()
    {
        return $this->hasMany(FaceDescriptor::class);
    }
}
