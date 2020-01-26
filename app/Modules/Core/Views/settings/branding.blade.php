@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="lead-text">{{ ___( "Settings" ) }}</span> &middot; <span class="secondary-text">{{ ___( "Branding" ) }}</span>
</h1>

@stop

@section('content')
<div class="container" id="vBranding" v-cloak>

  <div class="row">

    <div class="col-md-9">

      <div>

        <div class="row">

          <div class="col-md-12">

            <div class="alert alert-info bg-white" v-show="dirty">
              <button class="btn btn-info btn-sm" @click="fetchBranding()" title="{{___('Reset')}}"><i class="fa fa-undo"></i></button> &nbsp; &nbsp; <button class="btn btn-success btn-sm" @click="updateBranding()">{{ ___( 'Save Changes' ) }}</button>
            </div>

            <div class="row push-down">

              <div class="col-md-3">

                <div class="side-menu">
                  <nav class="nav flex-column">
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.branding_edit_tab == 'logo' || vars.branding_edit_tab == '' }" @click="setBrandingEditTab('')">
                      <i class="fa fa-image nav-item-icon"></i>
                        {{ ___( 'Logo &amp; Icon' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.branding_edit_tab == 'options' }" @click="setBrandingEditTab('options')">
                      <i class="fa fa-cog nav-item-icon"></i>
                        {{ ___( 'Options' ) }}
                    </a>
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.branding_edit_tab == 'colours' }" @click="setBrandingEditTab('colours')">
                      <i class="fa fa-paint-brush nav-item-icon"></i>
                        {{ ___( 'Colours' ) }}
                    </a>
                  </nav>   
                </div>

              </div>

              <div class="col-md-9">

                <div class="tab-content">
                  
                  <div v-show="vars.branding_edit_tab == 'logo' || vars.branding_edit_tab == ''">
                    
                    <div class="edit-item-container m-t-0">

                      <div class="form-group">

                        <div class="row">

                          <div class="col-md-7">
                        
                            <h3 class="form-heading">{{ ___( "Set Logo" ) }}</h3>

                            <br />
                            
                            <div v-for="file in logo.files" class="files-list">
                              <img :src="file.blob" class="logo-preview" /><br />
                              @{{file.name}} <span v-show="file.error">- {{ ___('Error') }}: @{{file.error}}</span>
                            </div>

                            <file-upload
                              ref="logo"
                              v-model="logo.files"
                              post-action="/api/settings/branding/set-logo"
                              @input-file="inputFile"
                              @input-filter="inputFilter"
                              class="btn btn-info w-200"
                              :maximum="1"
                              :headers="{'X-CSRF-TOKEN': '{{ csrf_token() }}' }"
                            >
                            {{__('Select a File')}}
                            </file-upload>

                            <div class="form-group push-down">
                              <button class="btn btn-primary" v-show="( !$refs.logo || !$refs.logo.active ) && logo.files.length > 0" @click.prevent="$refs.logo.active = true" type="button">{{__('Upload')}}</button>
                            </div>

                          </div>

                          <div class="col-md-5 text-center" v-if="branding.logo_path">

                            <label>{{___('Current Logo')}}:</label>

                            <br />

                            <img :src="'{{ Config::get('settings.logo_public_dir') }}/' + branding.logo_path" class="logo-preview" />

                            <div class="form-group push-down">
                              <button class="btn btn-danger" @click="removeLogo()">{{ ___('Remove Logo') }}</button>
                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                    <hr />

                    <div class="edit-item-container">

                      <div class="form-group">
                        
                        <div class="row">

                          <div class="col-md-7">

                            <h3 class="form-heading">{{ ___( "Set Favicon" ) }}</h3>

                            <br />
                        
                            <div v-for="file in favicon.files" class="files-list">
                              <img :src="file.blob" class="logo-preview" /><br />
                              @{{file.name}} <span v-show="file.error">- {{ ___('Error') }}: @{{file.error}}</span>
                            </div>

                            <file-upload
                              ref="favicon"
                              v-model="favicon.files"
                              post-action="/api/settings/branding/set-favicon"
                              @input-file="inputFile"
                              @input-filter="inputFilter"
                              class="btn btn-primary w-200"
                              :maximum="1"
                              :headers="{'X-CSRF-TOKEN': '{{ csrf_token() }}' }"
                            >
                            {{__('Select a File')}}
                            </file-upload>

                            <div class="form-group push-down">
                              <button class="btn btn-primary" v-show="( !$refs.favicon || !$refs.favicon.active ) && favicon.files.length > 0" @click.prevent="$refs.favicon.active = true" type="button">{{__('Upload')}}</button>
                            </div>

                          </div>

                          <div class="col-md-5 text-center" v-if="branding.favicon_path">

                            <label>{{___('Current Favicon')}}:</label>

                            <br />

                            <img :src="'{{ Config::get('settings.favicon_public_dir') }}/' + branding.favicon_path" class="logo-preview" />

                            <div class="form-group push-down">
                              <button class="btn btn-danger" @click="removeFavicon()">{{ ___('Remove Favicon') }}</button>
                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                  <div v-show="vars.branding_edit_tab == 'options'">

                    <div class="form-container">

                      <h3 class="form-heading">{{___('Options')}}</h3>
                          
                      <div class="form-group">

                        <b-form-checkbox id="flag_display_company_name"
                                       v-model="branding.flag_display_company_name"
                                       value="true"
                                       >
                          {{ ___( "Display company name on header" ) }}
                        </b-form-checkbox>

                      </div>

                      <div class="form-group">

                        <b-form-checkbox id="flag_display_company_logo"
                                       v-model="branding.flag_display_company_logo"
                                       value="true"
                                       >
                          {{ ___( "Display logo on header" ) }}
                        </b-form-checkbox>

                      </div>

                      <br />

                      <h3 class="form-heading">{{___('Size Options (enter numbers only')}}</h3>

                      {{ element( [ 'id' => 'branding_logo_size',
                        'field_name' => 'logo_size',
                        'label' => [ 'text' => ___( "Logo Size (height, in pixels):" ) ],
                        'element' => [ 'type' => 'text', 'model' => 'branding.logo_size' ],
                        'section' => 'branding' ] ) }}

                      {{ element( [ 'id' => 'branding_title_size',
                        'field_name' => 'title_size',
                        'label' => [ 'text' => ___( "Page Title Font (size in pixels):" ) ],
                        'element' => [ 'type' => 'text', 'model' => 'branding.title_size' ],
                        'section' => 'branding' ] ) }}

                      {{ element( [ 'id' => 'branding_title_spacing',
                        'field_name' => 'title_spacing',
                        'label' => [ 'text' => ___( "Page Title Spacing (size in pixels):" ) ],
                        'element' => [ 'type' => 'text', 'model' => 'branding.title_spacing' ],
                        'section' => 'branding' ] ) }}

                      <div class="form-group">
                        <button class="btn btn-success btn-sm" @click="updateBranding()">{{ ___( 'Save Changes' ) }}</button>
                      </div>

                    </div>

                  </div>

                  <div v-show="vars.branding_edit_tab == 'colours'">

                    <div class="form-container">

                      <h3 class="form-heading">{{__('Colour Branding')}}</h3>

                      <div style="-webkit-box-shadow: 0px 0px 3px 1px rgba(0,0,0,0.39); -moz-box-shadow: 0px 0px 3px 1px rgba(0,0,0,0.39); box-shadow: 0px 0px 3px 1px rgba(0,0,0,0.39);">
                      
                        <div class="push-down" :style="'padding: 10px; background: ' + branding.header_background_colour.hex + '; color: ' + branding.header_text_colour.hex">
                          {{ ___('Home | Companies | Settings | Dashboards |') }} <strong>{{ ___('This is a preview') }}</strong>
                        </div>
                        <div class="content-header" :style="'padding: 10px; background: ' + branding.title_background_colour.hex + '; color: ' + branding.title_text_colour.hex">
                          <h1 class="page-title"><span class="lead-text">{{ ___('Settings') }}</span> &middot; <span class="secondary-text">{{ ___('Branding') }}</span></h1>
                        </div>

                      </div>

                      <p class="push-down">{{ ___('Click on the colour circles to change the colours.') }}</p>
                      
                      <div class="form-group push-down">
                        <label class="clickable" @click="setColourMode('header_background'); refresh()"><button class="btn btn-circle btn-lg" :style="'padding: 10px; background: ' + branding.header_background_colour.hex + '; color: ' + branding.header_text_colour.hex"><i class="fa fa-edit"></i></button> {{ ___( 'Header Background Colour' ) }}</label>
                        <div class="colour-picker-container" v-show="vars.colour_edit_mode == 'header_background'">
                          <chrome-picker v-model="branding.header_background_colour"></chrome-picker>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="clickable" @click="setColourMode('header_text'); refresh()"><button class="btn btn-circle btn-lg" :style="'padding: 10px; color: ' + branding.header_background_colour.hex + '; background: ' + branding.header_text_colour.hex"><i class="fa fa-edit"></i></button> {{ ___( 'Header Text Colour' ) }}</label>
                        <div class="colour-picker-container" v-show="vars.colour_edit_mode == 'header_text'">
                          <chrome-picker v-model="branding.header_text_colour"></chrome-picker>
                        </div>
                      </div>

                      <div class="form-group push-down">
                        <label class="clickable" @click="setColourMode('title_background'); refresh()"><button class="btn btn-circle btn-lg" :style="'padding: 10px; background: ' + branding.title_background_colour.hex + '; color: ' + branding.title_text_colour.hex"><i class="fa fa-edit"></i></button> {{ ___( 'Title Background Colour' ) }}</label>
                        <div class="colour-picker-container" v-show="vars.colour_edit_mode == 'title_background'">
                          <chrome-picker v-model="branding.title_background_colour"></chrome-picker>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="clickable" @click="setColourMode('title_text'); refresh()"><button class="btn btn-circle btn-lg" :style="'padding: 10px; color: ' + branding.title_background_colour.hex + '; background: ' + branding.title_text_colour.hex"><i class="fa fa-edit"></i></button> {{ ___( 'Title Text Colour' ) }}</label>
                        <div class="colour-picker-container" v-show="vars.colour_edit_mode == 'title_text'">
                          <chrome-picker v-model="branding.title_text_colour"></chrome-picker>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="clickable" @click="setColourMode('company_name'); refresh()"><button class="btn btn-circle btn-lg" :style="'padding: 10px; color: ' + branding.company_name_colour.hex + '; background: ' + branding.company_name_colour.hex"><i class="fa fa-edit"></i></button> {{ ___( 'Company Name Colour' ) }}</label>
                        <div class="colour-picker-container" v-show="vars.colour_edit_mode == 'company_name'">
                          <chrome-picker v-model="branding.company_name_colour"></chrome-picker>
                        </div>
                      </div>

                      <div style="height: 200px;">
                        &nbsp;
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
    include( [ 'Core/Branding' ] );
  </script>
@endsection
