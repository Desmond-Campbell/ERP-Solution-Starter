<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\UserBranch;
use App\Modules\Core\Models\UserRole;

use Mail, Config, Audit, Search;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'dob', 'about', 'gender', 'email', 'email', 'password', 'permission_overrides', 'permission_restrictions', 'roles', 'avatar_path'
    ];

    public static $object_fields = [ 'permission_overrides', 'permission_restrictions', 'roles' ];

    public static $audit_class = 'user';

    protected static function boot()
    {
      parent::boot();
      static::saved(
        function( $object )
        {          
          Audit::log( [ 'object' => $object, 'class' => self::$audit_class ] );
          Search::catch( [ 'object' => $object, 'class' => self::$audit_class ] );
          return true;
        }
      );
      static::deleted(
        function( $object )
        {
          Audit::log( [ 'object' => $object, 'class' => self::$audit_class, 'type' => 'delete' ] );
          Search::catch( [ 'object' => $object, 'class' => self::$audit_class, 'type' => 'delete' ] );
          return true;
        }
      );
    } 

    public function branches() {
        return $this->hasMany(\App\Modules\Core\UserBranch::class);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function validate( $id, $data ){

        return [];

    }

    public static function fetch( $id = 0, $args = null ) {

        $people = self::where( 'id', '>', 0 );
        
        if ( $id ) $people = $people->where( 'id', $id );

        if ( !$id && $args['paging'] ) {

          $limit = $args['paging']['limit'] ?? 15;
          $page = $args['paging']['page'] ?? 1;
          $sortIcons = $args['paging']['sortIcons'] ?? [];
          $sortField = $args['paging']['sortField'] ?? 'first_name';
          $sortOrder = $args['paging']['sortOrder'] ?? 'asc';

          $people = $people->orderBy( $sortField, $sortOrder );

          $keywords = $args['paging']['keywords'] ?? [];

          if ( $keywords ) {

            $people = $people->where(function($q) use ($keywords) { 
              $q->where('first_name', 'like', '%' . $keywords . '%');
              $q->where('last_name', 'like', '%' . $keywords . '%');
              $q->orWhere('email', 'like', '%' . $keywords . '%');
            });

          }

          $people = $people->paginate( $limit, ['*'], null, $page );
          $paging = [ 'page' => $people->currentPage(), 'total' => $people->total(), 'pages' => $people->lastPage(), 'limit' => $limit, 'sortIcons' =>  $sortIcons ];
          $people = $people->items();

          return [ 'people' => $people, 'paging' => $paging ];

        } else {

          $people = $people->get();
        
        }

        if ( $id ) return $people[0] ?? (object) [];

        return $people;

      }

    public static function prepare( $id ) {

        $user = self::find( $id );

        if ( $user ) {

          $user = User::transform( $user );
          $company_id = Config::get('company_id');

          $user->branches = UserBranch::mergeBranches( $user->id, $company_id );
          $user->roles = UserRole::mergeRoles( $user->id, $company_id );

          $overrides = expand_array( $user->permission_overrides, [ 'branches', 'active' ], '\App\Modules\Core\Models\Permission' );
          $overrides = array_map( function( $override ) { 
                                    $override['branches'] = $override['branches'] ?? []; 
                                    $override['active'] = $override['active'] ?? false; 
                                    return $override; 
                                  }, $overrides );

          $user->permission_overrides = $overrides;

          $restrictions = expand_array( $user->permission_restrictions, [ 'branches' ], '\App\Modules\Core\Models\Permission' );
          $restrictions = array_map( function( $restriction ) { 
                                    $restriction['branches'] = $restriction['branches'] ?? []; 
                                    $restriction['active'] = $restriction['active'] ?? false; 
                                    return $restriction; 
                                  }, $restrictions );

          $user->permission_restrictions = $restrictions;

        }

        return $user;

    }

    public static function sendPasswordNotification( $user, $password ){

        if ( !( $user->id ?? null ) ) {

            $user = self::find( $user );

        }

        $body = ___( 'A password has been set for your account. Please log in with these new credentials below.' ) . "\n\n";
        $body .= ___( 'Email address:' ) . " {$user->email}.\n\n";
        $body .= ___( 'Password:' ) . " $password";

        if ( $user ) {

            Mail::raw( $body, function ($message) use ( $user ) {
        
                $message->to( $user->email );
                $message->subject( ___('Your Account Details') );
                $message->from( env( 'MAIL_SENDER_EMAIL', 'example@example.com' ) );

            });

        }

    }

    public static function updatePermissions( $user_id ) {

        $args = [];
        $args['user_id'] = $user_id;

        $user = self::find( $user_id );

        if ( !$user ) return [ 'errors' => ___( 'User not found.' ) ];

        $args['company_id'] = Config::get('company_id');

        $args['permission_overrides'] = json_decode( $user->permission_overrides ?? '[]' );
        $args['permission_restrictions'] = json_decode( $user->permission_restrictions ?? '[]' );
        $args['roles'] = json_decode( $user->roles ?? '[]' );

        $result = Permission::mapPermissions( $args );

        return $result;

    }

    public static function updateBranches( $user_id, $branches, $company_id ) {

        return UserBranch::mapBranches( $user_id, $branches, $company_id );

    }

    public static function updateRoles( $user_id, $roles, $company_id ) {

          return UserRole::mapRoles( $user_id, $roles, $company_id );

      }

    public static function transform( $user, $single = true ) {

        $users = $single ? [ $user ] : $user;

        foreach ( $users as $user ) {

            foreach ( self::$object_fields as $field ) {

                if ( $user->{$field} ?? null ) {

                    $user->{$field} = json_decode( $user->{$field} );

                } else {

                    $user->{$field} = [];

                }

            }
        
        }

        if ( $single ) return $users[0];

        return $users;

    }

}
