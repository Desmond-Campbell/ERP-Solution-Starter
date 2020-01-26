<?php

$settings = [];

/* Timezone hours specify the offset between the timezone of the app and UTC. Default is 0 */

$settings['timezone_hours'] = -5;

/* 
Instant search indexing lets the system update the search index for a record as soon as that record is changed. May affect performance if you are writing plenty of changes, but ideal otherwise. If instant indexing is off, you can utilise scheduled indexing.
*/

$settings['instant_search_indexing'] = true;

/* Password reset control. This value disables password reset feature. It is enabled by default. */

$settings['disable_password_reset'] = false;

/* Registration control. This value disables registration of new user accounts. It is enabled by default. */

$settings['disable_registration'] = false;

/* Disable permission checks to allow a user to not be screened based on roles and permissions. */

$settings['disable_permission_checks'] = true;

/* Storage directories for files and documents */

$settings['logo_storage_dir'] = 'public/storage/logos';
$settings['logo_public_dir'] = '/storage/logos';

$settings['favicon_storage_dir'] = 'public/storage/favicons';
$settings['favicon_public_dir'] = '/storage/favicons';

$settings['avatar_storage_dir'] = 'public/storage/avatars';
$settings['avatar_public_dir'] = '/storage/avatars';

$settings['document_storage_dir'] = 'public/storage/documents';
$settings['document_public_dir'] = 'public/storage/documents';

return $settings;
