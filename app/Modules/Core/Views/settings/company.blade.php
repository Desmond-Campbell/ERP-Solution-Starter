@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="lead-text">{{ ___( "Settings" ) }}</span> &middot; <span class="secondary-text">{{ ___( "Company Info" ) }}</span>
  &nbsp; &nbsp; <a href="/companies" class="btn btn-warning">{{ ___('View All Companies') }}</a>
</h1>

@stop

@section('content')
<div class="container" id="vCompanyInfo" v-cloak>

  <div class="row">

    <div class="col-md-9">

      <div v-show="mode != 'edit'">
      
        <div class="row">

          <div class="col-md-12">  

            <div class="alert alert-info bg-white">
              <i class="fa fa-lock"></i> {{__('View Mode')}} &nbsp; &nbsp; <button class="btn btn-primary btn-sm" @click="mode = 'edit'; vars.company_edit_tab = 'company_details'">{{ ___( 'Click to Edit' ) }}</button>
            </div>

            <div class="row push-down">

              <div class="col-md-6">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-info"><i class="fa fa-bars nav-item-icon"></i> {{ ___( "Details" ) }} <a href="javascript:void(0);" class="text-secondary push-right" @click="getSection('company_details')"><i class="fa fa-edit"></i></a></h5>

                    <div class="row">
                      <div class="col-md-4">
                        <label class="primary-label">{{ ___( "Company Name" ) }}:</label>
                      </div>
                      <div class="col-md-8">
                        @{{ company.name }}
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-4">
                        <label class="primary-label">{{ ___( "Business Type" ) }}:</label>
                      </div>
                      <div class="col-md-8">
                        @{{ company.business_type }}
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-4">
                        <label class="primary-label">{{ ___( "Tax ID" ) }}:</label>
                      </div>
                      <div class="col-md-8">
                        @{{ company.tax_id }}
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-4">
                        <label class="primary-label">{{ ___( "Licence Number" ) }}:</label>
                      </div>
                      <div class="col-md-8">
                        @{{ company.licence_number }}
                      </div>
                    </div>

                  </div>
                </div>

                <div class="card push-down">
                  <div class="card-body">
                    <h5 class="card-title text-info"><i class="fa fa-map-marker nav-item-icon"></i> {{ ___( "Addresses" ) }} <a href="javascript:void(0);" class="text-secondary push-right" @click="getSection('company_addresses')"><i class="fa fa-edit"></i></a></h5>

                    <div class="row push-down" v-for="(a, index) in company.addresses">
                      <div class="col-md-4">
                        <label class="primary-label">@{{ a.type || ___t('General') }}:</label>
                      </div>
                      <div class="col-md-8 nl2br">@{{ a.address }}</div>
                    </div>

                  </div>
                </div>

                <div class="card push-down">
                  <div class="card-body">
                    <h5 class="card-title text-info"><i class="fa fa-phone nav-item-icon"></i> {{ ___( "Phone Numbers" ) }} <a href="javascript:void(0);" class="text-secondary push-right" @click="getSection('company_phone_numbers')"><i class="fa fa-edit"></i></a></h5>

                    <div class="row push-down" v-for="(p, index) in company.phone_numbers">
                      <div class="col-md-4">
                        <label class="primary-label">@{{ p.type }}:</label>
                      </div>
                      <div class="col-md-8">@{{ p.number }}</div>
                    </div>

                  </div>
                </div>

              </div>

              <div class="col-md-6">

                <div class="card">
                  <div class="card-body h-700">
                    <h5 class="card-title text-info"><i class="fa fa-file nav-item-icon"></i> {{ ___( "Documents" ) }} <a href="javascript:void(0);" class="text-secondary push-right" @click="getSection('company_documents')"><i class="fa fa-edit"></i></a></h5>
                    <div v-show="company.documents.length < 1">
                      {{ ___('No documents have been added yet.') }}
                    </div>
                    <table class="table" v-show="company.documents.length > 0">
                      <thead>
                        <tr>
                          <th>{{___("Name")}}</th>
                          <th>{{___("Date")}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(d, index) in company.documents">
                          <td><a :href="'/public/documents/' + d.id + '/download/?file_name=' + d.file_name"><i class="fa fa-download text-secondary"></i></a> &nbsp; @{{ d.name }}</td>
                          <td>@{{ moment( d.created_at ).fromNow() }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="card push-down">
                  <div class="card-body">
                    <h5 class="card-title text-info"><i class="fa fa-briefcase nav-item-icon"></i> {{ ___( "Directors" ) }} <a href="javascript:void(0);" class="text-secondary push-right" @click="getSection('company_directors')"><i class="fa fa-edit"></i></a></h5>
                    <table class="table">
                      <thead>
                        <tr>
                          <th>{{___("Director's Info")}}</th>
                          <th>{{___("Title")}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(d, index) in company.directors">
                          <td>@{{ d.name }}
                            <span v-show="d.email"><br />@{{ d.email }}</span>
                            <span v-show="d.phone_mobile" title="{{ ___('Phone (mobile)') }}"><br /><i class="fa fa-mobile-alt"></i> @{{ d.phone_mobile }}</span>
                            <span v-show="d.phone_office" title="{{ ___('Phone (office)') }}"><br /><i class="fa fa-phone"></i> @{{ d.phone_office }}</span>
                          </td>
                          <td>@{{ d.title || ___t( 'No title' ) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

      <div v-show="mode == 'edit'">

        <div class="row">

          <div class="col-md-12">

            <div class="alert alert-info bg-white">
              <i class="fa fa-lock-open"></i> {{__('Edit Mode')}} &nbsp; &nbsp; <button class="btn btn-info btn-sm" @click="mode = 'view'; fetchCompany()"><i class="fa fa-undo"></i></button> &nbsp; &nbsp; <button class="btn btn-success btn-sm" @click="updateCompany()" v-show="dirty">{{ ___( 'Save Changes' ) }}</button>
            </div>

            <div class="row push-down">

              <div class="col-md-3">

                <div class="side-menu">
                  <nav class="nav flex-column">
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.company_edit_tab == 'company_details' || vars.company_edit_tab == '', 'is-error-target' : error_targets.company_details }" @click="setCompanyEditTab('')">
                      <i class="fa fa-bars nav-item-icon"></i>
                        {{ ___( 'Details' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.company_edit_tab == 'company_addresses', 'is-error-target' : error_targets.company_addresses }" @click="setCompanyEditTab('company_addresses')">
                      <i class="fa fa-map-marker nav-item-icon"></i>
                        {{ ___( 'Addresses' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.company_edit_tab == 'company_phone_numbers', 'is-error-target' : error_targets.company_phone_numbers }" @click="setCompanyEditTab('company_phone_numbers')">
                      <i class="fa fa-phone nav-item-icon"></i>
                      {{ ___( 'Phone Numbers' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.company_edit_tab == 'company_directors', 'is-error-target' : error_targets.company_directors }" @click="setCompanyEditTab('company_directors')">
                      <i class="fa fa-briefcase nav-item-icon"></i>
                        {{ ___( 'Directors' ) }}
                    </a>
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.company_edit_tab == 'company_documents', 'is-error-target' : error_targets.company_documents }" @click="setCompanyEditTab('company_documents')">
                      <i class="fa fa-file nav-item-icon"></i>
                        {{ ___( 'Documents' ) }}
                    </a>
                  </nav>   
                </div>

              </div>

              <div class="col-md-9">

                <div class="tab-content">
                  
                  <div v-show="vars.company_edit_tab == 'company_details' || vars.company_edit_tab == ''">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Edit Company Details')}}</h3>

                      <span class="help-block"><i class="fa fa-info-circle"></i> {{ ___( 'The information you edit below will be saved globally for this company.' ) }}</span>

                      <div class="push-down edit-item-container">

                        @php 

                        elements( Config::get( 'forms.company.details' ) )

                        @endphp

                        <div class="form-group">
                          <button class="btn btn-primary" @click="updateCompany()"><i class="fa fa-check"></i> {{ ___( "OK" ) }}</button>
                          &nbsp;
                          <button class="btn btn-default" @click="mode = 'view'; fetchCompany()"><i class="fa fa-undo"></i> {{ ___( "Cancel" ) }}</button>
                        </div>

                      </div>

                    </div>

                  </div>

                  <div v-show="vars.company_edit_tab == 'company_phone_numbers'">

                    <div class="form-container">

                      <h3 class="form-heading">{{__('Manage Phone Numbers')}}</h3>
                      
                      <button class="btn btn-info push-down" @click="editItem('phone_number', -1)"><i class="fa fa-plus"></i> {{ ___( "Add Number" ) }}</button>

                      <div class="push-down edit-item-container" v-show="edit_mode == 'phone_number'">
                        
                        @php

                        elements( Config::get( 'forms.company.phone_numbers' ) )

                        @endphp

                        <div class="form-group">
                          <button class="btn btn-primary" @click="changeItem('phone_number')"><i class="fa fa-check"></i> {{ ___( "OK" ) }}</button>
                          &nbsp;
                          <button class="btn btn-default" @click="cancelChangeItem('phone_number')"><i class="fa fa-undo"></i> {{ ___( "Cancel" ) }}</button>
                        </div>

                      </div>

                      <ul class="list-group push-down container-80">

                        <li v-for="(p, index) in company.phone_numbers" class="list-group-item">
                          <div class="row">
                            <div class="col-md-8 clickable" @click="editItem('phone_number', index)">
                              <span v-show="p.type"><strong>@{{ p.type }}: </strong></span>@{{ p.number }}
                            </div>
                            <div class="col-md-4 text-right">
                              <span class="list-group-control-buttons">
                                <a href="javascript:void(0);" class="text-primary" @click="editItem('phone_number', index)"><i class="fa fa-edit"></i> {{__('Edit')}}</a> &nbsp; &nbsp; 
                                <a href="javascript:void(0);" class="text-danger" @click="deleteItem('phone_number', index)"><i class="fa fa-trash"></i> {{__('Delete')}}</a>
                              </span>
                            </div>
                          </div>
                        </li>

                      </ul>

                    </div>

                  </div>

                  <div v-show="vars.company_edit_tab == 'company_addresses'">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Edit Addresses')}}</h3>
                      
                      <button class="btn btn-info push-down" @click="editItem('address', -1)"><i class="fa fa-plus"></i> {{ ___( "Add Address" ) }}</button>

                      <div v-show="edit_mode == 'address'" class="edit-item-container">
                        
                        @php 

                        elements( Config::get( 'forms.company.addresses' ) )

                        @endphp

                        <div class="form-group">
                          <button class="btn btn-primary" @click="changeItem('address')"><i class="fa fa-check"></i> {{ ___( "OK" ) }}</button>
                          &nbsp;
                          <button class="btn btn-default" @click="cancelChangeItem('address')"><i class="fa fa-undo"></i> {{ ___( "Cancel" ) }}</button>
                        </div>
                      
                      </div>

                      <ul class="list-group push-down container-80">

                        <li v-for="(a, index) in company.addresses" class="list-group-item">
                          <div class="row">
                            <div class="col-md-8 clickable" @click="editItem('address', index)">
                              <span v-show="a.type"><strong>@{{ a.type }}</strong></span>
                              <p class="nl2br">@{{ a.address }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                              <span class="list-group-control-buttons">
                                <a href="javascript:void(0);" class="text-primary" @click="editItem('address', index)"><i class="fa fa-edit"></i> {{__('Edit')}}</a> &nbsp; &nbsp; 
                                <a href="javascript:void(0);" class="text-danger" @click="deleteItem('address', index)"><i class="fa fa-trash"></i> {{__('Delete')}}</a>
                              </span>
                            </div>
                          </div>
                        </li>

                      </ul>

                    </div>

                  </div>

                  <div v-show="vars.company_edit_tab == 'company_directors'">
               
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Company Directors')}}</h3>

                      <button class="btn btn-info push-down" @click="editItem('director', -1)"><i class="fa fa-plus"></i> {{ ___( "Add Director" ) }}</button>

                      <div v-show="edit_mode == 'director'" class="edit-item-container">

                        @php

                        elements( Config::get( 'forms.company.directors' ) )

                        @endphp

                        <div class="form-group">
                          <button class="btn btn-primary" @click="changeItem('director')"><i class="fa fa-check"></i> {{ ___( "OK" ) }}</button>
                          &nbsp;
                          <button class="btn btn-default" @click="cancelChangeItem('director')"><i class="fa fa-undo"></i> {{ ___( "Cancel" ) }}</button>
                        </div>

                      </div>

                      <ul class="list-group push-down container-80">

                        <li v-for="(d, index) in company.directors" class="list-group-item">
                          <div class="row">
                            <div class="col-md-8 clickable" @click="editItem('director', index)">
                              <strong>@{{ d.name }}</strong>
                              <span class="help-block">
                                <span v-show="d.title"> | @{{ d.title }}</span>
                                <span v-show="d.email"> | @{{ d.email }}</span>
                                <span v-show="d.phone_mobile" title="{{ ___('Phone (mobile)') }}"><br /><i class="fa fa-mobile-alt"></i> @{{ d.phone_mobile }}</span>
                                <span v-show="d.phone_office" title="{{ ___('Phone (office)') }}"><br /><i class="fa fa-phone"></i> @{{ d.phone_office }}</span>
                              </span>
                            </div>
                            <div class="col-md-4 text-right">
                              <span class="list-group-control-buttons">
                                <a href="javascript:void(0);" class="text-primary" @click="editItem('director', index)"><i class="fa fa-edit"></i> {{__('Edit')}}</a> &nbsp; &nbsp; 
                                <a href="javascript:void(0);" class="text-danger" @click="deleteItem('director', index)"><i class="fa fa-trash"></i> {{__('Delete')}}</a>
                              </span>
                            </div>
                          </div>
                        </li>

                      </ul>

                    </div>

                  </div>

                  <div v-show="vars.company_edit_tab == 'company_documents'">
                    
                    <div class="form-container">
                      <h3 class="form-heading">{{__('Document Management')}} <a href="javascript:void(0);" @click="fetchDocuments()"><i class="fa fa-sync-alt"></i></a></h3>

                      <span class="help-block push-down">
                        {{__('Upload a variety of file types and make them available to other people in the company.')}}
                      </span>

                      <br /><br />

                      <div v-for="file in upload.files">
                        <strong>@{{file.name}} s-@{{file.success}} e-@{{file.error}}</strong>
                      </div>

                      <br />

                      <file-upload
                        ref="upload"
                        v-model="upload.files"
                        post-action="/api/settings/company/upload-documents"
                        @input-file="inputFile"
                        @input-filter="inputFilter"
                        class="btn btn-info w-200"
                        :maximum="5"
                        :multiple="true"
                        :headers="{'X-CSRF-TOKEN': '{{ csrf_token() }}' }"
                      >
                      {{__('Select up to 5 files')}}
                      </file-upload>

                      <div class="form-group">
                        <button class="btn btn-primary" v-show="(!$refs.upload || !$refs.upload.active) && upload.files.length > 0" @click.prevent="$refs.upload.active = true" type="button">{{__('Upload')}}</button>
                      </div>

                      <hr />

                      <div class="push-down h-700" v-show="company.documents.length > 0">

                        <ul class="list-group p-b-100">

                          <div class="hidden">
                            @{{cache}}
                            @{{company.documents}}
                          </div>

                          <li v-for="( document, index ) in company.documents" class="list-group-item">

                            <div v-show="cache.document_id == document.id" class="bg-light p-20 m-b-15">

                              @php

                              elements( Config::get( 'forms.company.documents' ) )

                              @endphp

                              <button class="btn btn-primary push-down" @click="updateDocument(document)"><i class="fa fa-check"></i> {{ ___( 'Save' ) }}</button> &nbsp;
                              <button class="btn btn-danger push-down" @click="cancelEditDocument(index)"><i class="fa fa-undo"></i> {{ ___( 'Cancel' ) }}</button>

                            </div>

                            <div class="row" v-show="cache.document_id != document.id">
                                
                              <div class="col-md-1">
                                @{{ index + 1 }}
                              </div>
                              
                              <div class="col-md-9">
                                <strong>@{{ document.name }}</strong><br />
                                <span class="help-block">
                                  @{{ document.size_info }} &middot;
                                  @{{ document.file_type }} &middot; 
                                  <span :title="document.created_at">@{{ moment( document.created_at ).fromNow() }}</span>
                                </span>
                              </div>
                              
                              <div class="col-md-2">
                                
                                <div class="dropdown">
                                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="javascript:void(0);" @click="editDocument(index); refresh()">{{ ___( 'Edit' ) }}</a>
                                    <a class="dropdown-item" href="javascript:void(0);" @click="deleteDocument(document.id)">{{ ___( 'Delete' ) }}</a>
                                    <a class="dropdown-item" href="javascript:void(0);" @click="archiveDocument(document.id)">{{ ___( 'Archive' ) }}</a>
                                    <a class="dropdown-item" :href="'/public/documents/' + document.id + '/download/?file_name=' + document.file_name">{{ ___( 'Download' ) }}</a>
                                  </div>
                                </div>

                              </div>

                            </div>

                          </li>

                        </ul>

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
    include( [ 'Core/CompanyInfo' ] );
    @php if ( $document_id ?? 0 ): @endphp
    jQuery(document).ready( function() {
    
      CompanyInfo.mode = 'edit';
      CompanyInfo.vars.company_edit_tab = 'company_documents';
    
    });
    @php endif @endphp
  </script>

@endsection
