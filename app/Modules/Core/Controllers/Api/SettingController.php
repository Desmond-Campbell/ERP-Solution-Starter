<?php

namespace App\Modules\Core\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Config;
use \App\User;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\CompanyDocument;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\UserBranch;
use App\Modules\Core\Models\UserRole;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\RolePermission;
use App\Modules\Core\Models\Lists;
use App\Modules\Core\Models\ListCategory;
use App\Modules\Core\Models\ListField;
use App\Modules\Core\Models\ListItemData;
use App\Modules\Core\Models\ListItem;

use App\Modules\Core\Services\Main;
use App\Modules\Core\Services\Validation;

class SettingController extends Controller
{
  
	public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth']); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  // Company

    public function fetchCompany( Request $R ) {

      $company_id = Config::get( 'company' )->id ?? 0;

      allow( 'view_company_information', $company_id );

      $company = Company::fetch( $company_id, 1 );

      return response()->json( $company );

    }

    public function updateCompany( Request $R ) {

    	$error = '';
    	$result = $form = [];
      $error_details = [ 'messages' => [], 'targets' => [] ];

      $mode = $R->route('mode');

      $data = [ 
          'name' => $R->input( 'name' ), 
          'type' => $R->input( 'type' )['value'] ?? 'Company' ,
          'tax_id' => $R->input( 'tax_id' ), 
          'licence_number' => $R->input( 'licence_number' ) 
        ];

      $fields = [ 
                  'company_emails',
                  'company_directors',
                  'company_addresses',
                  'company_phone_numbers' 
                ];

    	foreach ( $fields as $field ) {

        $form[$field] = $R->input( $field, $R->input( str_replace( 'company_', '', $field ), null ) );

        if ( !is_array( $form[$field] ) ) {

          if ( $form[$field] ) {

            $form[$field] = json_decode( $form[$field] ) ;

          }

        }

      }

      $fields[] = 'company_details';

      $form['company_details'] = $data;

      $section = $R->input('section', '');
      $company_id = Config::get( 'company' )->id ?? 0;
      $company = Company::find( $company_id );

      if ( $company ) {

        $company_id = Config::get( 'company' )->id;
        allow( 'update_company', $company_id );

        $error_count = 0;

        if ( $mode != 'update' ) {

          $fields = [ $section ];

        }

        foreach ( $fields as $field ) {

          $error_count_subtotal = 0;

          if ( $form[$field] ?? null ) {


            $items = $form[$field];

            if ( is_array( $items ) && !in_array( $field, [ 'company_details' ] ) ) {

              foreach ( $items as $item ) {

                if ( is_array( $item  ) ) {

                  $validated = Company::validate( $field, $item, $company_id );

                  if ( !( $validated['valid'] ?? false ) ) {

                    $error_details = [ 
                          'messages' => array_merge( $validated['errors'], $error_details['messages'] ), 
                          'targets' => (object) array_merge( (array) $validated['targets'], (array) $error_details['targets'] ) 
                        ];

                    $error_count_subtotal++;
                    $error_count++;

                  }

                }

              }

            } else {

              $validated = Company::validate( $field, $items, $company_id );

              if ( !( $validated['valid'] ) ) {

                $error_details = [ 
                      'messages' => array_merge( $validated['errors'], $error_details['messages'] ?? [] ), 
                      'targets' => (object) array_merge( (array) $validated['targets'], (array) $error_details['targets'] ) 
                    ];

                $error_count_subtotal++;
                $error_count++;

              }

            }

          }

          if ( !$error_count_subtotal ) {

            if ( $mode == 'update' ) {

              if ( $field == 'company_details' ) {

                  Company::saveDetails( $company_id, $form['company_details'] );

              } else {

                Company::saveField( $company_id, $form[$field], $field );

              }

            }

          }

        }

        if ( !$error_count ) {

          $result['success'] = 1;
          $error_details = [];

        } else {

          $error = ___( 'We could not update some of the information you submitted. Please check sections marked in red.' );
          
          $result['success'] = 0;
          $result['errors'] = $error;
          $result['error_details'] = $error_details;

        }

      } else {

        $error = ___( 'Company does not exist.' );

      }

    	if ( !$error ) {

    		$result['success'] = 1;

    	} else {

    		$result['success'] = 0;
        $result['errors'] = $error;
    		$result['error_details'] = $error_details;

    	}

    	return response()->json( $result );

    }

  // Branch

    public function fetchBranches( Request $R ) {

      $branch_id = $R->route( 'id' );
      $company_id = Config::get('company_id');

      if ( $branch_id ) {

        $results = Branch::fetch( $branch_id, $company_id );

      } else {
      
        $paging = $R->input('paging') ?? [ 'page' => 1, 'limit' => 15 ];
        $results = Branch::fetch( null, $company_id, [ 'paging' => $paging ] );

      }

      $columns = Config::get('tables.branches');

      $results['columns'] = $columns;

      allow( 'view_branches', $company_id, $branch_id );

      return response()->json( $results );

    }

    public function updateBranch( Request $R ) {

      $error = '';
      $result = $form = [];

      $data = [ 
          'name' => $R->input( 'name' ), 
          'manager' => $R->input( 'manager' ) ,
          'address' => $R->input( 'address' ), 
          'code' => $R->input( 'code' ) 
        ];

      $id = $R->route( 'id' );
      $branch = Branch::find( $id );

      $company_id = Config::get( 'company' )->id;
      
      $validated = Validation::validate( 'branches', $data, [ 'company_id' => $company_id ] );

      if ( !( $validated['valid'] ?? false ) ) {

        $error = ___('Please check the fields marked red for errors.');
        $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

        return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

      } else {   

        if ( $branch ) {
        
          allow( 'update_branch', $company_id, $id );
          
          foreach ( $data as $field => $value ) {

            $branch->{$field} = $value;

          }

          $branch->save();

        } else {

          allow( 'create_branch', $company_id );

          $result['success'] = 1;
          $data['company_id'] = $company_id;

          Branch::create( $data );

        }

      }

      return response()->json( $result );

    }

    public function closeBranch( Request $R ) {

      $id = $R->route( 'id' );

      $company_id = Config::get( 'company' )->id;
      allow( 'close_branch', $company_id );

      Branch::where( 'id', $id )->update( [ 'status' => 2 ] );

      return [ 'success' => 1 ];

    }

  // People

    public function fetchPeople( Request $R ) {

      $company_id = Config::get( 'company' )->id;

      allow( 'view_people', $company_id );

      $paging = $R->input('paging') ?? [ 'page' => 1, 'limit' => 15 ];
      $results = User::fetch( null, [ 'paging' => $paging ] );

      $columns = Config::get('tables.people');
      

      $results['columns'] = $columns;

      return response()->json( $results );

    }

    public function fetchPerson( Request $R ) {

      $company_id = Config::get( 'company' )->id;

      
      $user_id = $R->route( 'id' );

      allow( 'view_person', $company_id, null, $user_id );

      if ( $user_id == 'new' ) {

        $person = (object) [];

        $permissions = Permission::all();

        foreach ( $permissions as $p ) {

          $p->active = false;

        }

        $roles = Role::where('company_id', $company_id)->get();

        foreach ( $roles as $r ) {

          $r->active = false;

        }

        $person->permission_overrides = $permissions;
        $person->permission_restrictions = $permissions;
        $person->roles = $roles;
        $person->branches = Branch::all();

      } else {

        $person = User::prepare( $user_id );

      }

      return response()->json( $person );

    }

    public function updatePerson( Request $R ) {

      $company_id = Config::get( 'company' )->id;

      $error = '';
      $result = $form = [];

      $permission_overrides = is_array( $R->input( 'permission_overrides' ) ) ? $R->input( 'permission_overrides' ) : [];
      $permission_restrictions = is_array( $R->input( 'permission_restrictions' ) ) ? $R->input( 'permission_restrictions' ) : [];

      if ( $R->input( 'name' ) ) {

        $name = explode( ' ', $R->input( 'name' ) );

        if ( count( $name ) > 2 ) {

          $first_name = $name[0];
          $middle_name = $name[1];
          $last_name = array_splice( $name, 2, count( $name ) - 2 );

        } elseif ( count( $name ) > 1 ) {

          $first_name = $name[0];
          $middle_name = '';
          $last_name = $name[1];

        } else {

          $first_name = $name;
          $middle_name = '';
          $last_name = '';

        }

      } else {

        $first_name = $R->input( 'first_name' );
        $middle_name = $R->input( 'middle_name' );
        $last_name = $R->input( 'last_name' );

      }

      $data = [ 
          'first_name' => $first_name, 
          'middle_name' => $middle_name, 
          'last_name' => $last_name, 
          'dob' => Carbon::parse( $R->input( 'dob' ) )->toDateString(), 
          'about' => $R->input( 'about' ), 
          'gender' => $R->input( 'gender' ), 
          'email' => $R->input( 'email' ),
          'roles' => json_encode( reduce_array( $R->input( 'roles' ), [ 'id' ] ) ),
          'permission_overrides' => json_encode( reduce_array( $permission_overrides, [ 'id', 'branches', 'active' ] ) ),
          'permission_restrictions' => json_encode( reduce_array( $permission_restrictions, [ 'id', 'branches', 'active' ] ) ),
        ];

      $branches = $R->input( 'branches', [] );
      $roles = $R->input( 'roles', [] );

      $new_password = null;

      if ( $R->input( 'new_password' ) ) {

        $new_password = $R->input( 'new_password' );

      } elseif ( $R->input( 'generate_password' ) ) {

        $new_password = str_random( 8 );

      }

      if ( $new_password ) {

        $data['password'] = bcrypt( $new_password );
        
      } else {

        $data['password'] = bcrypt( str_random( 8 ) );

      }

      $id = $R->route( 'id' );
      $user = User::find( $id );

      $validated = Validation::validate( 'person', $data, [ 'company_id' => $company_id ] );

      if ( !( $validated['valid'] ?? false ) ) {

        $error = ___('Please check the fields marked red for errors.');
        $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

        return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

      } else { 

        if ( $user ) {
        
          allow( 'update_person', $company_id, null, $id );

          foreach ( $data as $field => $value ) {

            $user->{$field} = $value;

          }

          $user->save();

        } else {

          allow( 'create_person', $company_id );

          $data['company_id'] = $company_id;

          $user = User::create( $data );

          if ( $R->input( 'generate_password_email' ) ) {

            User::sendPasswordNotification( $user, $new_password );

          }

          $result['success'] = 1;

        }

      }

      if ( $user ) {


        User::updatePermissions( $user->id );
        User::updateBranches( $user->id, $branches, $company_id );
        User::updateRoles( $user->id, $roles, $company_id );

        $result['user'] = User::prepare( $user->id );
        
      }

      return response()->json( $result );

    }

    public function deletePerson( Request $R ) {

      $id = $R->route( 'id' );

      $company_id = Config::get( 'company' )->id;

      allow( 'delete_person', $company_id, null, $id );

      User::where( 'id', $id )->delete();

      return [ 'success' => 1 ];

    }

  // Role

    public function fetchRoles( Request $R ) {

      $company_id = Config::get( 'company' )->id;

      allow( 'view_roles', $company_id );
      
      $role_id = $R->route( 'id' );

      if ( $role_id ) {

        $results = Role::fetch( $role_id );

      } else {
      
        $paging = $R->input('paging') ?? [ 'page' => 1, 'limit' => 15 ];
        $results = Role::fetch( null, $company_id, [ 'paging' => $paging ] );

      }

      $columns = Config::get('tables.roles');

      $results['columns'] = $columns;

      return response()->json( $results );

    }

    public function fetchRole( Request $R ) {

      $company_id = Config::get( 'company' )->id;
      $role_id = $R->route( 'id' );
      
      allow( 'view_role', $company_id, null, $role_id );

      $all_permissions = Permission::all();

      if ( $role_id == 'new' ) {

        $role = (object) [];

        $role->permissions = $all_permissions;
        $role->all_permissions = false;

        foreach ( $role->permissions as $p ) {

          $p->active = false;

        }

      } else {

        $role = Role::prepare( $company_id, $role_id );
        $role->all_permissions = $role->all_permissions ? 1 : 0;

      }

      return response()->json( $role );

    }

    public function updateRole( Request $R ) {

      $company_id = Config::get( 'company' )->id;

      $error = '';
      $result = $form = [];

      if ( $R->input('all_permissions', false ) ) {

        $permissions = Permission::all();

        foreach ( $permissions as $p ) {

          $p->active = true;

        }

      } else {

        $permissions = $R->input( 'permissions', '[]' );

      }

      $data = [ 
          'company_id' => $company_id, 
          'name' => $R->input( 'name' ), 
          'all_permissions' => $R->input( 'all_permissions', 0 ), 
          'description' => $R->input( 'description' ), 
        ];

      
      $id = $R->route( 'id' );
      $role = Role::find( $id );

      $validated = Validation::validate( 'role', $data, [ 'company_id' => $company_id ] );

      if ( !( $validated['valid'] ?? false ) ) {

        $error = ___('Please check the fields marked red for errors.');
        $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

        return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

      } else { 

        if ( $role ) {
        
          allow( 'update_role', $company_id, null, $id );

          foreach ( $data as $field => $value ) {

            $role->{$field} = $value;

          }

          $role->save();

        } else {

          allow( 'create_role', $company_id );

          $data['company_id'] = $company_id;

          $role = Role::create( $data );
          $result['success'] = 1;

        }

      }

      if ( $role ) {

        RolePermission::reassign( $company_id, $role->id, $permissions );

        $result['role'] = Role::prepare( $company_id, $role->id );
        
      }

      return response()->json( $result );

    }

    public function duplicateRole( Request $R ) {

      $company_id = Config::get( 'company' )->id;


      $id = $R->route( 'id' );
      $role = Role::with('permissions')->find( $id );

      if ( !$role ) {

        $error = ___('We could not copy that role because it does not exist anymore.');

        return response()->json( [ 'errors' => $error ] );

      }

      allow( 'duplicate_role', $company_id, null, $id );

      $data = [ 
          'company_id' => $company_id, 
          'name' => $role->name . ' (' . ___('copy') . ')', 
          'description' => $role->description, 
          'all_permissions' => $role->all_permissions, 
      ];

      $new_role = Role::create( $data );
      $result['success'] = 1;

      foreach ( $role->permissions as $permission ) {

        $new_permission = $permission->toArray();
        unset( $new_permission['id'] );
        $new_permission['role_id'] = $new_role->id;
        RolePermission::create( $new_permission );

      }

      $result['role'] = Role::prepare( $company_id, $new_role->id );

      return response()->json( $result );

    }

    public function deleteRole( Request $R ) {

      $id = $R->route( 'id' );

      $company_id = Config::get( 'company' )->id;

      allow( 'delete_role', $company_id, null, $id );

      Role::where( 'id', $id )->delete();

      return [ 'success' => 1 ];

    }

    /// Branding

      public function setLogo(Request $R) {

        $company_id = Config::get( 'company' )->id;

        allow( 'update_company_logo', $company_id );

        $file = $_FILES['file'];
        $ext = pathinfo( $file['name'] )['extension'];
        $root_path = base_path();
        $env_storage_path = '/' . Config::get('settings.logo_storage_dir');
        $company_storage_path = '/' . $company_id;
        $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

        if ( !file_exists( $full_company_storage_path ) ) {

          mkdir( $full_company_storage_path, 0777, true );

        }
        
        $new_file_name = str_random( 28 );

        $public_path = $company_storage_path . '/' . $new_file_name . '.' . $ext;
        $full_path = $full_company_storage_path . '/' . $new_file_name . '.' . $ext;

        Setting::set( 'logo_path', $public_path, 'branding', $company_id );

        move_uploaded_file( $file['tmp_name'], $full_path );

      }

      public function removeLogo() {

        $company_id = Config::get( 'company' )->id;

        allow( 'delete_company_logo', $company_id );

        $root_path = base_path();
        $env_storage_path = '/' . Config::get('settings.logo_storage_dir');
        $company_storage_path = '/' . $company_id;
        $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

        $logo_path = Setting::get( 'logo_path', '', $company_id );
        
        Setting::kill('logo_path', $company_id);

        if ( $logo_path ) {

          $full_logo_path = $full_company_storage_path . $logo_path;

          if ( file_exists( $full_logo_path ) ) {

            @unlink( $full_logo_path );

          }

        }

      }

      public function setFavicon(Request $R) {

        $company_id = Config::get( 'company' )->id;

        allow( 'update_company_favicon', $company_id );
        
        $file = $_FILES['file'];
        $ext = pathinfo( $file['name'] )['extension'];
        $root_path = base_path();
        $env_storage_path = '/' . Config::get('settings.favicon_storage_dir');
        $company_storage_path = '/' . $company_id;
        $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

        if ( !file_exists( $full_company_storage_path ) ) {

          mkdir( $full_company_storage_path, 0777, true );

        }
        
        $new_file_name = str_random( 28 );
        
        $public_path = $company_storage_path . '/' . $new_file_name . '.' . $ext;
        $full_path = $full_company_storage_path . '/' . $new_file_name . '.' . $ext;

        Setting::set( 'favicon_path', $public_path, 'branding', $company_id );

        move_uploaded_file( $file['tmp_name'], $full_path );

      }

      public function removeFavicon() {

        $company_id = Config::get( 'company' )->id;

        allow( 'delete_company_favicon', $company_id );

        $root_path = base_path();
        $env_storage_path = '/' . Config::get('settings.favicon_storage_dir');
        $company_storage_path = '/' . $company_id;
        $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

        $favicon_path = Setting::get( 'favicon_path', '', $company_id );
        
        Setting::kill('favicon_path', $company_id);

        if ( $favicon_path ) {

          $full_favicon_path = $full_company_storage_path . $favicon_path;

          if ( file_exists( $full_favicon_path ) ) {

            @unlink( $full_favicon_path );

          }

        }

      }

      public function updateBranding(Request $R) {

        $company_id = Config::get( 'company' )->id;

        allow( 'update_company_branding', $company_id );
        
        $header_background_colour = $R->input('header_background_colour')['hex'] ?? '#009EB5';
        $header_text_colour = $R->input('header_text_colour')['hex'] ?? '#FFFFFF';

        Setting::set( 'text_header_text_colour', $header_text_colour, 'branding', $company_id );
        Setting::set( 'text_header_background_colour', $header_background_colour, 'branding', $company_id );

        $title_background_colour = $R->input('title_background_colour')['hex'] ?? '#E2ECF7';
        $title_text_colour = $R->input('title_text_colour')['hex'] ?? '#000000';
        $company_name_colour = $R->input('company_name_colour')['hex'] ?? '#FFFFFF';

        Setting::set( 'text_title_text_colour', $title_text_colour, 'branding', $company_id );
        Setting::set( 'text_title_background_colour', $title_background_colour, 'branding', $company_id );
        Setting::set( 'text_company_name_colour', $company_name_colour, 'branding', $company_id );

        $logo_size = $R->input('logo_size');
        $title_size = $R->input('title_size');
        $title_spacing = $R->input('title_spacing');

        if ( $logo_size ) { 
          Setting::set( 'number_logo_size', $logo_size, 'branding', $company_id );
        } else {
          Setting::kill( 'number_logo_size', $company_id );
        }

        if ( $title_size ) { 
          Setting::set( 'number_title_size', $title_size, 'branding', $company_id );
        } else {
          Setting::kill( 'number_title_size', $company_id );
        }

        if ( $title_spacing ) { 
          Setting::set( 'number_title_spacing', $title_spacing, 'branding', $company_id );
        } else {
          Setting::kill( 'number_title_spacing', $company_id );
        }


        $fields = [
          'flag_display_company_name',
          'flag_display_company_logo',
        ];

        foreach ( $fields as $field ) {

          $setting = $R->input( $field, false );

          Setting::set( $field, $setting, 'branding', $company_id );

        }

      }

      public function fetchBranding(Request $R) {

        $company_id = Config::get( 'company' )->id;

        allow( 'update_company_branding', $company_id );
        
        $header_background_colour = Setting::get( 'text_header_background_colour', '#009EB5', $company_id );
        $header_text_colour = Setting::get( 'text_header_text_colour', '#FFFFFF', $company_id );
        $title_background_colour = Setting::get( 'text_title_background_colour', '#E2ECF7', $company_id );
        $title_text_colour = Setting::get( 'text_title_text_colour', '#000000', $company_id );
        $company_name_colour = Setting::get( 'text_company_name_colour', '#FFFFFF', $company_id );

        $logo_size = Setting::get( 'number_logo_size', '', $company_id );
        $title_size = Setting::get( 'number_title_size', '', $company_id );
        $title_spacing = Setting::get( 'number_title_spacing', '', $company_id );

        $branding = [];
        $branding['header_text_colour'] = [ 'hex' => $header_text_colour ];
        $branding['header_background_colour'] = [ 'hex' => $header_background_colour ];
        $branding['title_text_colour'] = [ 'hex' => $title_text_colour ];
        $branding['title_background_colour'] = [ 'hex' => $title_background_colour ];
        $branding['company_name_colour'] = [ 'hex' => $company_name_colour ];
        $branding['title_size'] = $title_size;
        $branding['title_spacing'] = $title_spacing;
        $branding['logo_size'] = $logo_size;

        $branding['logo_path'] = Setting::get( 'logo_path', null, $company_id );
        $branding['favicon_path'] = Setting::get( 'favicon_path', null, $company_id );

        $fields = [
          'flag_display_company_name',
          'flag_display_company_logo',
        ];

        foreach ( $fields as $field ) {

          $branding[$field] = Setting::get( $field, '', $company_id );

          if ( substr( $field, 0, 4 ) == 'flag' ) {

            $branding[$field] = (bool) $branding[$field];

          }

        }

        return response()->json( $branding );

      }

      public function fetchBrandingImages(Request $R) {

        $company_id = Config::get( 'company' )->id;

        allow( 'update_company_branding', $company_id );
        
        $branding['logo_path'] = Setting::get( 'logo_path', null, $company_id );
        $branding['favicon_path'] = Setting::get( 'favicon_path', null, $company_id );

        return response()->json( $branding );

      }

    /// Company Documents

      public function uploadDocuments(Request $R) {

        $company_id = Config::get( 'company' )->id;

        allow( 'upload_company_document', $company_id );

        $file = $_FILES['file'];
        
        $ext = pathinfo( $file['name'] )['extension'];
        $root_path = base_path();
        $env_storage_path = '/' . Config::get('settings.document_storage_dir');
        $company_storage_path = '/' . $company_id;
        $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

        if ( !file_exists( $full_company_storage_path ) ) {

          mkdir( $full_company_storage_path, 0777, true );

        }

        $new_file_name = str_random( 28 );
      
        $public_path = $company_storage_path . '/' . $new_file_name . '.' . $ext;
        $full_path = $full_company_storage_path . '/' . $new_file_name . '.' . $ext;

        move_uploaded_file( $file['tmp_name'], $full_path );

        $file_info = [];
        $file_info['company_id'] = $company_id;  
        $file_info['name'] = $file['name'];  
        $file_info['file_name'] = $file['name'];  
        $file_info['file_size'] = $file['size'];  
        $file_info['file_type'] = $file['type'];  
        $file_info['file_path'] = $public_path;  
        $file_info['status'] = 0;  

        $documents[] = CompanyDocument::create( $file_info );

      }

      public function getDocuments() {

        $company_id = Config::get( 'company' )->id;

        allow( 'view_company_documents', $company_id );
        
        $documents = CompanyDocument::where('company_id', $company_id)->where('status', '<', 2)->orderBy('created_at', 'desc')->get();

        foreach ( $documents as $document ) {

          $document->size_info = byteSize( $document->file_size, 1 );

        }

        return response()->json( $documents );

      }

      public function updateDocument(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $document = CompanyDocument::where('company_id', $company_id)->find($id);

        if ( $document ) {

          allow( 'update_company_document', $company_id, null, $id );

          $name = $R->input('name');

          $validated = Validation::validate( 'company_documents', [ 'name' => $name ], [ 'company_id' => $company_id ] );

          if ( !( $validated['valid'] ?? false ) ) {

            $error = ___('Please enter a name for the selected document.');
            $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

            return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

          } else {

            $document->name = $name;
            $document->save();

          }

        }

        return response()->json( [] );

      }

      public function deleteDocument(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $document = CompanyDocument::where('company_id', $company_id)->find($id);

        if ( $document ) {

          allow( 'delete_company_document', $company_id, null, $id );

          $document->delete();

        }

        return response()->json( [] );

      }

      public function archiveDocument(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $document = CompanyDocument::where('company_id', $company_id)->find($id);

        if ( $document ) {

          allow( 'archive_company_document', $company_id, null, $id );

          $document->status = 2;
          $document->save();

        }

        return response()->json( [] );

      }

    // List manager

      public function fetchLists( Request $R ) {

        $list_id = $R->route( 'id' );
        $company_id = Config::get('company_id');

        allow( 'view_lists', $company_id );

        if ( $list_id ) {

          $results = Lists::fetch( $list_id, $company_id );

        } else {
        
          $options = [];
          $options['paging'] = $R->input('paging', [ 'page' => 1, 'limit' => 15 ] );
          $options['category_filter'] = $R->input('category_filter', null );
          $results = Lists::fetch( null, $company_id, $options );

        }

        return response()->json( $results );

      }

      public function updateList( Request $R ) {

        $error = '';
        $result = $form = [];

        $data = [ 
            'name' => $R->input( 'name' ), 
            'global' => $R->input( 'global', 0 ) ? 1 : 0,
          ];

        $id = $R->route( 'id' );
        $list = Lists::find( $id );
        
        if ( !$list ) {

          $data['list_category_id'] = $R->input( 'category_id');

        }

        $company_id = Config::get( 'company' )->id;
        
        $validated = Validation::validate( 'lists', $data, [ 'company_id' => $company_id ] );

        if ( !( $validated['valid'] ?? false ) ) {

          $error = ___('Please check the fields marked red for errors.');
          $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

          return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

        } else {   

          if ( $list ) {
          
            allow( 'update_list', $company_id, null, $id );
            
            foreach ( $data as $field => $value ) {

              $list->{$field} = $value;

            }

            $list->save();

          } else {

            allow( 'create_list', $company_id );

            $data['company_id'] = $company_id;
            $data['slug'] = str_slug( $data['name'] );

            $list = Lists::create( $data );

            $result['success'] = 1;
            $result['list'] = $list;

          }

        }

        return response()->json( $result );

      }

      public function deleteList(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $list = Lists::find($id);

        if ( $list ) {

          allow( 'delete_list', $company_id, null, $id );

          $list->delete();

          return response()->json( [ 'success' => 1 ] );

        } else {

          return response()->json( [ 'errors' => ___('List not found.') ] );

        }

        return response()->json( [] );

      }

      public function emptyList(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $list = Lists::find($id);

        if ( $list ) {

          allow( 'empty_list', $company_id, null, $id );

          ListItem::where('lists_id', $id)->delete();

        }

        return response()->json( [] );

      }

    // Category

      public function updateListCategory(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $category = ListCategory::find($id);

        $data = [ 'name' => $R->input('name'), 'global' => $R->input('global') ? 1 : 0 ];

        $validated = Validation::validate( 'list_categories', $data, [ 'company_id' => $company_id ] );

        if ( !( $validated['valid'] ?? false ) ) {

          $error = ___('Please check the information you entered.');
          $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

          return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

        } else {

          if ( $category ) {

            allow( 'update_list_category', $company_id, null, $id );

            $category->name = $data['name'];
            $category->slug = str_slug( $data['name'] );
            $category->global = $data['global'] ? 1 : 0;
            $category->save();

          } else {

            allow( 'create_list_category', $company_id );

            $data['company_id'] = $company_id;
            $data['slug'] = str_slug( $data['name'] );
            $category = ListCategory::create( $data );

          }

          return response()->json( [ 'success' => 1, 'category' => $category ] );         

        }

        return response()->json( [] );

      }

      public function deleteListCategory(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        if ( ListCategory::where('company_id', $company_id)->orWhere('global', 1)->count() < 2 ) {

          $error = ___("You cannot delete this category because it's the last one that exists for this company.");

          return response()->json( [ 'errors' => $error ] );

        }

        $category = ListCategory::find($id);

        if ( $category ) {

          allow( 'delete_list_category', $company_id, null, $id );

          $new_category_id = ListCategory::where('id', '<>', $id)->orderBy('id', 'asc')->first()->id ?? 0;
          Lists::where('list_category_id', $id)->update(['list_category_id' => $new_category_id]);
          $category->delete();

        }

        return response()->json( [] );

      }

      public function fetchListCategories( Request $R ) {

        $company_id = Config::get('company_id');

        allow( 'update_list_category', $company_id );

        $categories = ListCategory::where('company_id', $company_id)->orWhere('global', 1)->get();

        return response()->json( $categories );

      }

    // Fields

      public function fetchListFields( Request $R ) {

        $field_id = $R->route( 'id' );
        $company_id = Config::get('company_id');

        allow( 'view_list_fields', $company_id );

        $field = ListField::find( $field_id );

        return response()->json( $field );

      }

      public function updateListField(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );
        $lists_id = $R->route( 'lists_id' );

        $field = ListField::find($id);

        $data = [ 'label' => $R->input('label'), 'type' => $R->input('type') ];

        if ( is_array( $R->input('type') ) ) {

          $data['type'] = $R->input('type')['value'];

        }

        $validated = Validation::validate( 'list_fields', $data, [ 'company_id' => $company_id ] );

        if ( !( $validated['valid'] ?? false ) ) {

          $error = ___('Please check the information you entered.');
          $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

          return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

        } else {


          if ( $field ) {

            allow( 'update_list_fields', $company_id, null, $id );

            $field->label = $data['label'];
            $field->type = $data['type'];
            $field->save();

          } else {

            allow( 'create_list_fields', $company_id );

            $data['lists_id'] = $lists_id;
            ListField::create( $data );

          }

          $fields = ListField::where('lists_id', $lists_id)->get();
          
          return response()->json( [ 'success' => 1, 'fields' => $fields ] );         

        }

        return response()->json( [] );

      }

      public function deleteListField(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $field = ListField::find($id);

        if ( $field ) {

          allow( 'delete_list_field', $company_id, null, $id );

          $field->delete();

        }

        return response()->json( [] );

      }

      public function updateListFieldStatus(Request $R) {

        $company_id = Config::get( 'company' )->id;
        $id = $R->route( 'id' );

        $field = ListField::find($id);

        if ( $field ) {

          allow( 'update_list_field', $company_id, null, $id );

          $field->status = !$field->status;
          $field->save();

          return response()->json( [ 'status' => $field->status ] );

        }

        return response()->json( [] );

      }

    // Data

      public function fetchListData( Request $R ) {

        $lists_id = $R->route( 'id' );
        $company_id = Config::get('company_id');

        allow( 'view_list_data', $company_id, null, $lists_id );

        $options = [];
        $options['paging'] = $R->input('paging', [ 'page' => 1, 'limit' => 15 ] );
        $results = ListItemData::fetch( $lists_id, $company_id, $options );
        $results['paging'] = $options['paging'];

        return response()->json( $results );

      }

      public function fetchListItemData( Request $R ) {

        $id = $R->route( 'id' );
        $lists_id = $R->route( 'lists_id' );
        $company_id = Config::get('company_id');

        allow( 'view_list_data', $company_id, null, $lists_id );

        $item_data = ListItemData::fetchSingle( $lists_id, $id );

        return response()->json( $item_data );

      }

      public function updateListItemData( Request $R ) {

        $id = $R->route( 'id' );
        $lists_id = $R->route( 'lists_id' );
        $company_id = Config::get('company_id');

        allow( 'update_list_data', $company_id, null, $lists_id );
        
        $results = ListItemData::updateSingle( $lists_id, $id, $R->input( 'data', [] ) );

        return response()->json( $results );

      }

      public function deleteListItemData( Request $R ) {

        $id = $R->route( 'id' );
        $company_id = Config::get('company_id');

        allow('delete_list_item', $company_id, null, $id );

        ListItem::where('id', $id)->delete();

        return response()->json( [] );

      }

}
