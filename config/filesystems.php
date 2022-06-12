<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 'gcs'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/storage'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', 'your-project-id'),
            'key_file' => [
                'type' => "service_account",
                'private_key_id' => "063f1132faf8bf4c4b84f908b8e39b7cefb220aa",
                'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDEnnerx6WZd+6q\nNfm9IUPPUolc1TTntXbVJgihyP4loGbl/cDCAW3Lyqv571rjtor6dgjHcqFWkJ+f\nSz4FGpafzAfO++zpUzyJc/Qg9thLdNByQ9WPKiGrvkqYioB/yHVk3evX1TsBv477\n7quLWG+Dih9ttdg8UF09x2gl72lv4mvOToqtufL/Lp4Yn0ZqVO2HKmzKeLz5UiIx\n10G38IdmJyvBKo7m+gys1tMOOXBo4Y6BliNsC0Xmns5pNH7tSbh38nPGzRSfWUl3\nHBPAAef0BxCkc919DSaV3GFiQUToWy/eyhv/+1SulK1VY0d0jlz/60CDegPqosFL\nHcQXNvFJAgMBAAECggEAGxzTrrs57Cw8G8+T77nJvSnfVDmi8SgLn2p+ZTovRZFV\nWctV4crK+6pScFrKYz01npeuhTua4NzSOL0/lk+WIVSZZ7hDRozE+8VNZEr1r44H\np8IP4w0xwG+In3lLLT5qEG3ZCusAlzCgRGr4enTIR9DA8w17fccChzWF+I6HhmG/\nFPDD90nY3QDl8AVxjsMfU2Z3RRWGKTNB20blrqmiqGX1UZzuYXDpmD4EzRLfczau\nOi/R3nmvdLHNpAfc4bWCu983rWov1oDyqHGDKeTOPfYvue+L+5bM7L5Mw9db2KfW\nu3Ykz33epBm8xEiBmQrnGxgXFxcFxj3rMviFSGaiMwKBgQD+PVg4JmvBUWC22mwW\nAPV0IaDqeJ8vUzdbs7TzCnESjlqZ2z8mJ9SQGgw41gKgjuGYaIcIsWFw1orTaUDa\nnUpt6+d0xCECCMP9Q6wNCCR0xZNMyVGLhd0C2P4v8UEYm/6nVwzU9oX1v/ozuPUi\nWvdTT5cLm/pVlK27bR9MKFuI7wKBgQDF+vydHPvN1ppEyr7MfEHuTuLDmooJMVYd\ndQFguHrc7mflvGc/YMZOKfZhm/gUgC8V8klsomY+sdDYJcc6E+dQSWPKuUBQAGDP\nzdAO2weci8/mR6OvP7BaoWN/STdJyp8ELjf5k43yJComphY/iGb8TKc4Q/OhD8QB\nALBeAZV5RwKBgQDWLYOmOefJAe9B25v2pXQiEzpmjJs92RLns+qaXI+JHFeDCcqX\nMDLH+smVD+VCsVunc1deoV//GR32n9K82IkdNQDVw2wzNIM3Vs0YZWWg4dkHdQm3\nSlw3y+nHJay7OJ89Bf6PYebUcpgq/oO0H4H/sysB4rLvacM6A+G73LvQDQKBgE20\n3I/iP2ckUzqrBUaHgu1BYzn5HzLKfY1kTl0jK34pyPfgAfpbRpqJV94p9K4/Pbv0\ndCNCfrUe3+TmtxKxmznlBFkeawK7k0Qc+QvLpmooajptZe60Jcj8zWu5Vg0NjWp7\npZa89prS3QR8bgETzbA7eerYqPhPktP97DuJjZyjAoGAd2SDvNTJEeJbAJXKFiti\nT8AAYmOaxhFjlcQCz7QDB8aLPTgxHv6BjB+DQjhjTFCyC9R+zvqKFljlo2Em8FOp\nyRqvbX4C7FukTaQljUOy2WLpF9AZNKb0Ax5W6C/0iD1GGwvs0FCNVxyEbehqxgaH\n55dJgO6qimD5cm+ZRS14GGA=\n-----END PRIVATE KEY-----\n",
                'client_email' => "educloudlabs-app@educloudlabs-318104.iam.gserviceaccount.com",
                'client_id' => "110656075147339811813",
                'auth_uri' => "https://accounts.google.com/o/oauth2/auth",
                'token_uri' => "https://oauth2.googleapis.com/token",
                'auth_provider_x509_cert_url' => "https://www.googleapis.com/oauth2/v1/certs",
                'client_x509_cert_url' => "https://www.googleapis.com/robot/v1/metadata/x509/educloudlabs-app%40educloudlabs-318104.iam.gserviceaccount.com",
            ], 
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET', 'your-bucket'),
            'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', null), // optional: /default/path/to/apply/in/bucket
            'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI', null), // see: Public URLs below
            'visibility' => 'public', // optional: public|private
        ],

    ],


    // {
    //     "type": "service_account",
    //     "project_id": "educloudlabs-318104",
    //     "private_key_id": "063f1132faf8bf4c4b84f908b8e39b7cefb220aa",
    //     "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDEnnerx6WZd+6q\nNfm9IUPPUolc1TTntXbVJgihyP4loGbl/cDCAW3Lyqv571rjtor6dgjHcqFWkJ+f\nSz4FGpafzAfO++zpUzyJc/Qg9thLdNByQ9WPKiGrvkqYioB/yHVk3evX1TsBv477\n7quLWG+Dih9ttdg8UF09x2gl72lv4mvOToqtufL/Lp4Yn0ZqVO2HKmzKeLz5UiIx\n10G38IdmJyvBKo7m+gys1tMOOXBo4Y6BliNsC0Xmns5pNH7tSbh38nPGzRSfWUl3\nHBPAAef0BxCkc919DSaV3GFiQUToWy/eyhv/+1SulK1VY0d0jlz/60CDegPqosFL\nHcQXNvFJAgMBAAECggEAGxzTrrs57Cw8G8+T77nJvSnfVDmi8SgLn2p+ZTovRZFV\nWctV4crK+6pScFrKYz01npeuhTua4NzSOL0/lk+WIVSZZ7hDRozE+8VNZEr1r44H\np8IP4w0xwG+In3lLLT5qEG3ZCusAlzCgRGr4enTIR9DA8w17fccChzWF+I6HhmG/\nFPDD90nY3QDl8AVxjsMfU2Z3RRWGKTNB20blrqmiqGX1UZzuYXDpmD4EzRLfczau\nOi/R3nmvdLHNpAfc4bWCu983rWov1oDyqHGDKeTOPfYvue+L+5bM7L5Mw9db2KfW\nu3Ykz33epBm8xEiBmQrnGxgXFxcFxj3rMviFSGaiMwKBgQD+PVg4JmvBUWC22mwW\nAPV0IaDqeJ8vUzdbs7TzCnESjlqZ2z8mJ9SQGgw41gKgjuGYaIcIsWFw1orTaUDa\nnUpt6+d0xCECCMP9Q6wNCCR0xZNMyVGLhd0C2P4v8UEYm/6nVwzU9oX1v/ozuPUi\nWvdTT5cLm/pVlK27bR9MKFuI7wKBgQDF+vydHPvN1ppEyr7MfEHuTuLDmooJMVYd\ndQFguHrc7mflvGc/YMZOKfZhm/gUgC8V8klsomY+sdDYJcc6E+dQSWPKuUBQAGDP\nzdAO2weci8/mR6OvP7BaoWN/STdJyp8ELjf5k43yJComphY/iGb8TKc4Q/OhD8QB\nALBeAZV5RwKBgQDWLYOmOefJAe9B25v2pXQiEzpmjJs92RLns+qaXI+JHFeDCcqX\nMDLH+smVD+VCsVunc1deoV//GR32n9K82IkdNQDVw2wzNIM3Vs0YZWWg4dkHdQm3\nSlw3y+nHJay7OJ89Bf6PYebUcpgq/oO0H4H/sysB4rLvacM6A+G73LvQDQKBgE20\n3I/iP2ckUzqrBUaHgu1BYzn5HzLKfY1kTl0jK34pyPfgAfpbRpqJV94p9K4/Pbv0\ndCNCfrUe3+TmtxKxmznlBFkeawK7k0Qc+QvLpmooajptZe60Jcj8zWu5Vg0NjWp7\npZa89prS3QR8bgETzbA7eerYqPhPktP97DuJjZyjAoGAd2SDvNTJEeJbAJXKFiti\nT8AAYmOaxhFjlcQCz7QDB8aLPTgxHv6BjB+DQjhjTFCyC9R+zvqKFljlo2Em8FOp\nyRqvbX4C7FukTaQljUOy2WLpF9AZNKb0Ax5W6C/0iD1GGwvs0FCNVxyEbehqxgaH\n55dJgO6qimD5cm+ZRS14GGA=\n-----END PRIVATE KEY-----\n",
    //     "client_email": "educloudlabs-app@educloudlabs-318104.iam.gserviceaccount.com",
    //     "client_id": "110656075147339811813",
    //     "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    //     "token_uri": "https://oauth2.googleapis.com/token",
    //     "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
    //     "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/educloudlabs-app%40educloudlabs-318104.iam.gserviceaccount.com"
    //   }
      



    // 'key_file' => [
    //     'type' => env('GOOGLE_CLOUD_ACCOUNT_TYPE'),
    //     'private_key_id' => env('GOOGLE_CLOUD_PRIVATE_KEY_ID'),
    //     'private_key' => env('GOOGLE_CLOUD_PRIVATE_KEY'),
    //     'client_email' => env('GOOGLE_CLOUD_CLIENT_EMAIL'),
    //     'client_id' => env('GOOGLE_CLOUD_CLIENT_ID'),
    //     'auth_uri' => env('GOOGLE_CLOUD_AUTH_URI'),
    //     'token_uri' => env('GOOGLE_CLOUD_TOKEN_URI'),
    //     'auth_provider_x509_cert_url' => env('GOOGLE_CLOUD_AUTH_PROVIDER_CERT_URL'),
    //     'client_x509_cert_url' => env('GOOGLE_CLOUD_CLIENT_CERT_URL'),
    // ], 

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
