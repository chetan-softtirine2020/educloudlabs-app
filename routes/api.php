<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'API\Auth\AuthController@register');
Route::post('login', 'API\Auth\AuthController@login');
Route::post('create-organization', 'API\Organization\OrganizationController@createOrganization');

Route::middleware('auth:api')->group( function () {
    //Common
    Route::post('auth-details', 'API\UserController@authDetails');
    Route::post('get-register-org', 'API\Organization\OrganizationController@getOrganizationsForRegister');
    Route::post('logout', 'API\Auth\AuthController@logout');
    
    /********************************** Start Admin **********************************/
    Route::post('get-org-approved', 'API\Organization\OrganizationController@getOrganizationForApproved');
    Route::post('org-approved', 'API\Organization\OrganizationController@approvedOrganization');

    /********************************** End Admin **********************************/


     /********************************** Start Learning Provider**********************************/
    //Training
    Route::post('/create-training', 'API\LearningProvider\TrainingController@createTraining');
    Route::post('/update-training', 'API\LearningProvider\TrainingController@updateTraining');
    Route::post('/get-training', 'API\LearningProvider\TrainingController@getTraining');
    Route::post('/all-trainings', 'API\LearningProvider\TrainingController@allTrainings');
    Route::post('/delete-training', 'API\LearningProvider\TrainingController@deleteTraining');
   
    // Training User
    Route::post('/lp/add-training-user', 'API\LearningProvider\LPUserTraining@addLearningProviderTrainingUser');
    Route::post('/lp/import-training-user', 'API\LearningProvider\LPUserTraining@importLearningProviderTrainingUser');     
    Route::post('/lp/get-training-users', 'API\LearningProvider\LPUserTraining@getTrainingUsers');
    
     ////// Provider User
         
     Route::post('/lpu/get-trainings', 'API\LearningProvider\ProviderUser\TrainingController@getTrainingUsersWise');
     Route::post('/lpu/update-join-status', 'API\LearningProvider\ProviderUser\TrainingController@updateTrainingJoinStatus');
    //Add Learning Provider user 
    /**********************************END Learning Provider**********************************/
    //Admin 

    

});