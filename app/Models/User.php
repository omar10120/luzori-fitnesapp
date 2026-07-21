<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Sanctum\HasApiTokens;
use Modules\Frontend\Models\UserPreference;
use App\Traits\DailyPlanTrait;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;
    use DailyPlanTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'username', 'first_name', 'last_name', 'phone_number', 'status', 'email', 'password', 'gender', 'display_name', 'login_type', 'user_type', 'player_id', 'is_subscribe', 'timezone','last_notification_seen', 'apple_user_identifier' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_subscribe'  => 'integer',
    ];

    public function userProfile() {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function userGraph(){
        return $this->hasMany(UserGraph::class, 'user_id', 'id');
    }

    public function userAssignDiet(){
        return $this->hasMany(AssignDiet::class, 'user_id', 'id');
    }

    public function userAssignWorkout(){
        return $this->hasMany(AssignWorkout::class, 'user_id', 'id');
    }

    public function userFavouriteDiet(){
        return $this->hasMany(UserFavouriteDiet::class, 'user_id', 'id');
    }

    public function userFavouriteWorkout(){
        return $this->hasMany(UserFavouriteWorkout::class, 'user_id', 'id');
    }

    public function userNotification(){
        return $this->hasMany(Notification::class, 'notifiable_id', 'id');
    }

    public function chatgptFitBot(){
        return $this->hasMany(ChatgptFitBot::class, 'user_id', 'id');
    }

    public function foodAnalysisRequests()
    {
        return $this->hasMany(FoodAnalysisRequest::class, 'user_id', 'id');
    }

    public function foodAnalysisUsages()
    {
        return $this->hasMany(FoodAnalysisUsage::class, 'user_id', 'id');
    }

    public function userPreference()
    {
        return $this->hasMany(UserPreference::class, 'user_id', 'id');
    }
    public function packages() {
        return $this->belongsToMany(Package::class, 'assign_package_user');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($row) {
            switch ($row->user_type) {
                case 'user':
                    $row->userProfile()->delete();
                    $row->userGraph()->delete();
                    $row->userAssignDiet()->delete();
                    $row->userAssignWorkout()->delete();
                    $row->userFavouriteDiet()->delete();
                    $row->userFavouriteWorkout()->delete();
                    $row->userNotification()->delete();
                    $row->chatgptFitBot()->delete();
                    $row->posting()->delete();
                    $row->postingBookmark()->delete();
                    $row->comment()->delete();
                    $row->postingLike()->delete();
                    $row->reportPosting()->delete();
                    
                break;
                default:
                    # code...
                break;
            }
        });

        static::updated(function($model) {
            if ($model->isDirty('first_name') || $model->isDirty('last_name') ) {
                $model->display_name = $model->first_name.' '.$model->last_name;
                $model->saveQuietly(); 
            }
            if ($model->wasChanged('gender')) {
                $model->refresh();
                self::resetDailyPlan($model);
            }
        });
    }

    public function routeNotificationForOneSignal()
    {
        return $this->player_id;
    }

    public function subscriptionPackage(){
        return $this->hasOne(Subscription::class, 'user_id','id')->where('status',config('constant.SUBSCRIPTION_STATUS.ACTIVE'));
    }

    public function classSchedulePlan(){
        return $this->hasMany(ClassSchedulePlan::class, 'user_id', 'id');
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = $value ? str_replace('+', '', $value) : null;
    }

    public function getPhoneNumberAttribute()
    {
        return $this->attributes['phone_number'] ? '+' . $this->attributes['phone_number'] : null;
    }

    public function posting()
    {
        return $this->hasMany(Posting::class, 'user_id', 'id');
    }

    public function postingBookmark()
    {
        return $this->hasMany(PostingBookmark::class, 'user_id', 'id');
    }

    public function postingLike()
    {
        return $this->hasMany(PostingLike::class, 'user_id', 'id');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function reportPosting() {
        return $this->hasMany(ReportPosting::class, 'user_id', 'id');
    }

    public function scopeUserReport($query)
    {
        return $query->where('user_type', 'user')
            ->when(request()->filled('from_date'), function ($q) {
                $q->whereDate('created_at', '>=', request('from_date'));
            })->when(request()->filled('to_date'), function ($q) {
                $q->whereDate('created_at', '<=', request('to_date'));
            })->when(request()->filled('user_id'), function ($q) {
                $q->where('id', request('user_id'));
            });
    }

    public function scopeIsStatus($query, $status = 'active')
    {
        return $query->where('status', $status);
    }
}
