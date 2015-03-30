<?php namespace App;

use App\Contracts\RideService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'uber_token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['uber_token'];

    /**
     * Clear user rider report from cache
     *
     * @return void
     */
    public function clearRiderReportCache()
    {
        $key = $this->getRiderReportCacheKey();

        Cache::forget($key);
    }

    /**
     * Get rider report from service
     *
     * @param  RideService  $service
     *
     * @return App\Report
     */
    public function getRiderReport(RideService $service)
    {
        $key = $this->getRiderReportCacheKey();

        return Cache::rememberForever($key, function() use ($service) {
            return $service->getReport($this);
        });
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        //$this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get cache key for user rider report
     *
     * @return string
     */
    private function getRiderReportCacheKey($vendor_prefix = null)
    {
        return 'report.'.($vendor_prefix ? $vendor_prefix.'.' : '').$this->getKey();
    }
}
