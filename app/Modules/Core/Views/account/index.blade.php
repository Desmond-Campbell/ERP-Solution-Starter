@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="secondary-text">{{ ___( "My Account" ) }}</span>
</h1>

@stop

@section('content')
<div class="container" id="vAccount" v-cloak>

  <div class="row">

    <div class="col-md-9">
  
      <div>

        <div class="row">

          <div class="col-md-12">

            <div class="row">

              <div class="col-md-3">

                <div class="side-menu">
                  <nav class="nav flex-column">
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.account_edit_tab == 'account_personal' || vars.account_edit_tab == '' }" @click="setAccountEditTab('account_personal')">
                      <i class="fa fa-bars nav-item-icon"></i>
                      {{ ___( 'Personal Info' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.account_edit_tab == 'account_password' }" @click="setAccountEditTab('account_password')">
                      <i class="fa fa-hand-point-right nav-item-icon"></i>
                      {{ ___( 'Password' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.account_edit_tab == 'account_avatar' }" @click="setAccountEditTab('account_avatar')">
                      <i class="fa fa-lock-open nav-item-icon"></i>
                      {{ ___( 'Avatar' ) }}
                    </a>
                  </nav>   
                </div>

              </div>

              <div class="col-md-9">

                <div class="tab-content">
                  
                  <div v-show="vars.account_edit_tab == 'account_personal' || vars.account_edit_tab == ''">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Personal Info')}}</h3>

                      @php

                      elements( Config::get( 'forms.account.personal' ) );

                      @endphp

                      <div class="form-group">
                        <button class="btn btn-primary" @click="updateAccount()"><i class="fa fa-check"></i> {{ ___( "OK" ) }}</button>
                        &nbsp;
                        <button class="btn btn-default" @click="fetchAccount()"><i class="fa fa-undo"></i> {{ ___( "Cancel" ) }}</button>
                      </div>

                    </div>

                  </div>

                  <div v-show="vars.account_edit_tab == 'account_password'">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Password') }}</h3>

                      <br />

                      <div class="form-group">
                        <label>{{ ___( "New Password" ) }}:</label>
                        <input type="text" class="form-control" id="new_password" v-model="account.new_password" />
                      </div>

                      <div class="form-group">
                        <label>{{ ___( "Confirm New Password" ) }}:</label>
                        <input type="text" class="form-control" id="new_password_confirmation" v-model="account.new_password_confirmation" />
                      </div>

                      <div class="form-group">
                        <label>{{ ___( "Enter Current Password" ) }}:</label>
                        <input type="text" class="form-control" id="current_password" v-model="account.password" />
                      </div>

                      <div class="form-group">
                        <button class="btn btn-primary" @click="changePassword()"><i class="fa fa-arrow-right"></i> {{ ___( "Change Password" ) }}</button>
                      </div>
                        
                    </div>

                  </div>

                  <div v-show="vars.account_edit_tab == 'account_avatar'">
                    
                    <div class="edit-item-container m-t-0">

                      <div class="form-group">

                        <div class="row">

                          <div class="col-md-7">
                        
                            <h3 class="form-heading">{{ ___( "Set Avatar" ) }}</h3>

                            <br />
                            
                            <div v-for="file in avatar.files" class="files-list">
                              <img :src="file.blob" class="logo-preview" /><br />
                              @{{file.name}} <span v-show="file.error">- {{ ___('Error') }}: @{{file.error}}</span>
                            </div>

                            <file-upload
                              ref="avatar"
                              v-model="avatar.files"
                              post-action="/api/account/avatar/upload"
                              @input-file="inputFile"
                              @input-filter="inputFilter"
                              class="btn btn-info w-200"
                              :maximum="1"
                              :headers="{'X-CSRF-TOKEN': '{{ csrf_token() }}' }"
                            >
                            {{__('Select a File')}}
                            </file-upload>

                            <div class="form-group push-down">
                              <button class="btn btn-primary" v-show="( !$refs.avatar || !$refs.avatar.active ) && avatar.files.length > 0" @click.prevent="$refs.avatar.active = true" type="button">{{__('Upload')}}</button>
                            </div>

                          </div>

                          <div class="col-md-5 text-center" v-if="account.avatar_path">

                            <label>{{___('Current Avatar')}}:</label>

                            <br />

                            <img :src="'{{ Config::get('settings.avatar_public_dir') }}/' + account.avatar_path" class="logo-preview" />

                            <div class="form-group push-down">
                              <button class="btn btn-danger" @click="removeAvatar()">{{ ___('Remove Avatar') }}</button>
                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')

  <script type="text/javascript">
    include( [ 'Core/Account' ] );
  </script>

@endsection
