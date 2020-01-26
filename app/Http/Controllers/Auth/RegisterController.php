<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        if ( $data['name'] ?? null ) {

            $name = explode( ' ', $data['name'] );

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

            $first_name = $data['first_name'] ?? '';
            $middle_name = $data['middle_name'] ?? '';
            $last_name = $data['last_name'] ?? '';

          }

      return User::create([
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
