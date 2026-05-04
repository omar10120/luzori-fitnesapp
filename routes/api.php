<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[ API\UserController::class, 'register']);
Route::post('login',[ API\UserController::class, 'login']);
Route::post('forget-password',[ API\UserController::class, 'forgetPassword']);
Route::post('social-mail-login',[ API\UserController::class, 'socialMailLogin' ]);
Route::post('social-otp-login',[ API\UserController::class, 'socialOTPLogin' ]);
Route::get('user-detail',[ API\UserController::class, 'userDetail']);
Route::get('get-appsetting', [ API\UserController::class, 'getAppSetting'] );
Route::get('language-table-list',[API\LanguageTableController::class, 'getList']);
Route::get('get-macro-nutrient',[API\DashboardController::class,'getMacroNurtrient']);

    Route::get('get-setting',[ API\DashboardController::class, 'getSetting']);
    Route::get('dashboard-detail',[ API\DashboardController::class, 'dashboard']);

    Route::get('equipment-list', [ API\EquipmentController::class, 'getList' ]);

    Route::get('categorydiet-list', [ API\CategoryDietController::class, 'getList' ]);

    Route::get('workouttype-list', [ API\WorkoutTypeController::class, 'getList' ]);

    Route::get('diet-list', [ API\DietController::class, 'getList' ]);
    Route::post('diet-detail', [ API\DietController::class, 'getDetail' ]);

    Route::get('category-list', [ API\CategoryController::class, 'getList' ]);
    Route::get('tags-list', [ API\TagsController::class, 'getList' ]);

    Route::get('level-list', [ API\LevelController::class, 'getList' ]);
    
    Route::get('bodypart-list', [ API\BodyPartController::class, 'getList' ]);
    
    Route::get('workout-list', [ API\WorkoutController::class, 'getList' ]);
    Route::get('workout-detail', [ API\WorkoutController::class, 'getDetail' ]);

    Route::get('exercise-list', [ API\ExerciseController::class, 'getList' ]);
    Route::get('exercise-detail', [ API\ExerciseController::class, 'getDetail' ]);

    Route::get('post-list', [ API\PostController::class, 'getList' ]);
    Route::post('post-detail', [ API\PostController::class, 'getDetail' ]);

    Route::get('product-list', [ API\ProductController::class, 'getlist']);
    Route::get('productcategory-list', [ API\ProductCategoryController::class, 'getlist']);
    Route::post('product-detail', [ API\ProductController::class, 'getDetail']);

    Route::get('diet-dashboard', [ API\DietController::class, 'dashboard' ]);
    Route::get('product-dashboard', [ API\ProductController::class, 'dashboard' ]);

    Route::get('workout-exercise-detail', [ API\V1\WorkoutController::class, 'exerciseDetail' ]);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('update-profile', [ API\UserController::class, 'updateProfile']);
    Route::post('change-password', [ API\UserController::class, 'changePassword']);
    Route::post('update-user-status', [ API\UserController::class, 'updateUserStatus']);
    Route::post('delete-user-account', [ API\UserController::class, 'deleteUserAccount']);
    Route::get('logout',[ API\UserController::class, 'logout']);

    Route::get('payment-gateway-list', [ API\PaymentGatewayController::class, 'getList'] );

    Route::get('assign-diet-list', [ API\AssignUserController::class, 'getAssignDiet' ]);
    Route::get('assign-workout-list', [ API\AssignUserController::class, 'getAssignWorkout' ]);

    Route::get('workoutday-list', [ API\WorkoutController::class, 'workoutDayList' ]);
    Route::get('workoutday-exercise-list', [ API\WorkoutController::class, 'workoutDayExerciseList' ]);
    
    Route::get('get-favourite-diet', [ API\DietController::class, 'getUserFavouriteDiet' ]);
    Route::post('set-favourite-diet', [ API\DietController::class, 'userFavouriteDiet' ]);


    Route::get('get-favourite-workout', [ API\WorkoutController::class, 'getUserFavouriteWorkout' ]);
    Route::post('set-favourite-workout', [ API\WorkoutController::class, 'userFavouriteWorkout' ]);
    
    Route::post('store-user-exercise', [ API\ExerciseController::class, 'storeUserExercise' ]);
    Route::get('get-user-exercise', [ API\ExerciseController::class, 'getUserExercise' ]);
    

    Route::get('package-list', [ API\PackageController::class, 'getList' ]);

    Route::get('subscriptionplan-list',[ API\SubscriptionController::class, 'getList']);
    Route::post('subscribe-package',[ API\SubscriptionController::class, 'subscriptionSave']);
    Route::post('cancel-subscription',[ API\SubscriptionController::class, 'cancelSubscription']);



    Route::post('usergraph-save', [ API\UserGraphController::class, 'saveGraphData']);
    Route::get('usergraph-list', [ API\UserGraphController::class, 'getGraphDataList']);
    Route::post('usergraph-delete', [ API\UserGraphController::class, 'deleteGraphData']);
    Route::get('usergraph-detail', [ API\UserGraphController::class, 'getGraphDetails']);

    Route::post('notification-list', [ API\NotificationController::class, 'getList'] );
    Route::get('notification-detail', [ API\NotificationController::class, 'getNotificationDetail'] );

    Route::get('user-profile-detail',[ API\UserController::class, 'userProfileDetail']); 

    Route::get('chatgpt-fit-bot-list',[ API\ChatgptFitBotController::class, 'getList']); 
    Route::post('chatgpt-fit-bot-save',[ API\ChatgptFitBotController::class, 'store']); 
    Route::post('chatgpt-fit-bot-delete',[ API\ChatgptFitBotController::class, 'destroy']); 

    Route::get('class-schedule-list',[ API\ClassScheduleController::class, 'getList']);
    Route::post('class-schedule-plan-save',[ API\ClassScheduleController::class, 'storeClassSchedulePlan']);

    Route::post('save-score', [ API\GameDataController::class, 'saveScore']);
    Route::get('get-score', [ API\GameDataController::class, 'getScore']);
    
    Route::get('userpost-list', [ API\PostingController::class, 'getPostList']);
    Route::get('userpost-detail', [ API\PostingController::class, 'getPostDetail']);
    Route::post('save-userpost', [ API\PostingController::class, 'savePostData']);
    Route::post('update-userpost', [ API\PostingController::class, 'updatePostData']);
    Route::post('delete-userpost', [ API\PostingController::class, 'deletePostData']);
    Route::post('remove-userpost-media', [ API\PostingController::class, 'removePostMedia']);

    Route::post('like-userpost', [ API\PostingController::class, 'userLikePost']);
    Route::get('like-userpost-list', [ API\PostingController::class, 'getUserLikePostList']);
    
    Route::post('bookmark-userpost', [ API\PostingController::class, 'userBookmarkPost']);
    Route::get('my-bookmark-post-list', [ API\PostingController::class, 'getMyBookmarkPostList']);

    Route::get('comment-list', [ API\CommentController::class, 'getCommentList']);
    Route::post('save-comment', [ API\CommentController::class, 'saveComment']);
    Route::post('update-comment', [ API\CommentController::class, 'updateComment']);
    Route::post('delete-comment', [ API\CommentController::class, 'deleteComment']);
    
    Route::get('comment-reply-list', [ API\CommentReplyController::class, 'getList']);
    Route::post('save-comment-reply', [ API\CommentReplyController::class, 'saveCommentReply']);
    Route::post('delete-comment-reply', [ API\CommentReplyController::class, 'deleteCommentReply']);
    
    Route::post('report-on-posting', [ API\PostingController::class, 'reportOnPosting']);

    Route::get('user-daily-water-goal-list', [ API\UserDailyGoalController::class, 'getDailyWaterGoalList']);
    Route::post('user-daily-water-goal-save', [ API\UserDailyGoalController::class, 'saveDailyWaterGoal']);
    
    Route::get('user-daily-steps-goal-list', [ API\UserDailyGoalController::class, 'getDailyStepsGoalList']);
    Route::post('user-daily-steps-goal-save', [ API\UserDailyGoalController::class, 'saveDailyStepsGoal']);
   
    Route::group(['prefix' => 'v1'], function () {
        
        Route::get('assign-diet-list', [ API\AssignUserController::class, 'getAssignDietV1' ]);
        Route::get('assign-workout-list', [ API\AssignUserController::class, 'getAssignWorkoutV1' ]);
        
        Route::get('workoutday-exercise-list', [ API\V1\WorkoutController::class, 'upNext' ]);

        Route::post('store-user-workout-exercise', [ API\V1\WorkoutController::class, 'storeUserWorkoutExercise' ]);
        Route::get('get-user-workout-exercise', [ API\V1\WorkoutController::class, 'getUserWorkoutExercise' ]);
        Route::get('user-daily-water-goal-list', [ API\UserDailyGoalController::class, 'getV1DailyWaterGoalList']);
        Route::get('user-daily-steps-goal-list', [ API\UserDailyGoalController::class, 'getV1DailyStepGoalList']);
    });

    Route::get('daily-plan-detail', [ API\DailyPlanController::class, 'getDailyPlanDetail' ]);
    Route::post('save-daily-plan-recipe', [ API\DailyPlanController::class, 'saveDailyPlanRecipeData']);
    Route::post('daily-plan-delete', [ API\DailyPlanController::class, 'deleteDailyPlan']);
    Route::post('daily-plan-recipe-delete', [ API\DailyPlanController::class, 'deleteDailyPlanRecipeData']);
    Route::post('daily-plan-recipe-delete-all', [ API\DailyPlanController::class, 'deleteDailyPlanRecipeAllData']);

    Route::get('shopping-list', [ API\ShoppingListController::class, 'getList' ]);
    Route::get('shopping-list-detail', [ API\ShoppingListController::class, 'getDetail' ]);
    Route::post('daily-plan-shopping-list-generate', [ API\ShoppingListController::class, 'generateFromDailyPlan' ]);
    Route::post('shopping-list-delete', [ API\ShoppingListController::class, 'deleteShoppingList' ]);
    Route::post('shopping-list-item-toggle', [ API\ShoppingListController::class, 'toggleItem' ]);
    Route::post('shopping-list-item-delete', [ API\ShoppingListController::class, 'deleteItem' ]);
    Route::post('shopping-list-item-add', [ API\ShoppingListController::class, 'addCustomItem' ]);
    Route::post('shopping-list-item-update', [ API\ShoppingListController::class, 'updateItem' ]);

    Route::post('set-reminder-settings',[API\UserController::class,'setReminderSetting']);
    Route::post('save-recipe-review', [ API\RecipeReviewController::class, 'saveRecipeReview']);

    Route::get('get-favourite-recipe', [ API\RecipeController::class, 'getUserFavouriteRecipe' ]);
    Route::post('set-favourite-recipe', [ API\RecipeController::class, 'saveUserFavouriteRecipe' ]);

});

Route::get('recipetag-list', [ API\RecipeTagController::class, 'getList' ]);
Route::get('recipecategory-list', [ API\RecipeCategoryController::class, 'getList' ]);

Route::get('recipe-filter-list', [ API\RecipeController::class, 'getRecipeFilterList' ]);
Route::get('recipe-detail/{id}', [ API\RecipeController::class, 'recipeDetail']);

Route::get('recipe-review-detail', [ API\RecipeReviewController::class, 'getReviewDetail']);

Route::get('get-measurementunit', [ API\ShoppingListController::class, 'getMeasurementUnit' ]);
