<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\LandingPage\LandingPageManagementController;
use App\Http\Controllers\LandingPage\SectionImageController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductImageController;
use App\Http\Controllers\Store\StoreController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["prefix" => "v1",], function () {
    //

    require __DIR__ . '/auth.php';

    Route::group(["as" => "landingpage.", "prefix" => "landing-page", 'middleware' => 'jwt.verify'], function () {
        Route::delete("/{id}", [LandingPageManagementController::class, "destroy"])
            ->can('delete')
            ->name("destroy");

        Route::put("/{id}", [LandingPageManagementController::class, "update"])
            ->can('update')
            ->name("update");

        Route::post("/", [LandingPageManagementController::class, "store"])
            ->can('create', App\Models\LandingPage\Section::class)
            ->name("store");

        Route::get("/", [LandingPageManagementController::class, "index"])
            ->withoutMiddleware('jwt.verify')
            ->name("index");

        Route::as('section_images')->prefix('section-images')->group(function () {
            Route::delete("/{id}", [SectionImageController::class, "destroy"])
                ->can('update')
                ->name("delete");
            Route::put("/{id}", [SectionImageController::class, "update"])
                ->name("update");
            Route::post("/", [SectionImageController::class, "store"])
                ->name("store");
        });
    });

    Route::group(["as" => "products.", "prefix" => "products", 'middleware' => 'jwt.verify'], function () {
        Route::put("/images", [ProductImageController::class, 'update'])->name("images.update");
        Route::delete("/{product}", [ProductController::class, "destroy"])->name("destroy");
        Route::put("/{id}", [ProductController::class, "update"])->name("update");
        Route::get("/{id}", [ProductController::class, "show"])->name("show")->withoutMiddleware('jwt.verify');
        Route::post("/", [ProductController::class, "store"])->name("store");
        Route::get("/", [ProductController::class, "index"])->name("index")->withoutMiddleware('jwt.verify');
    });

    Route::group(["as" => "store.", "prefix" => "stores", 'middleware' => 'jwt.verify'], function () {
        Route::put('/{slug}', [StoreController::class, "update"])->name("update");
        Route::delete('/{slug}', [StoreController::class, "destroy"])->name("delete");
        Route::post('/', [StoreController::class, "create"])->name("create");
        Route::get('/', [StoreController::class, "index"])->withoutMiddleware('jwt.verify');
    });

    Route::group(["as" => "user.", "prefix" => "users", 'middleware' => 'jwt.verify'], function () {
        Route::group(["as" => 'roles.', 'prefix' => 'roles'], function () {
            Route::post('/', [RoleController::class, 'store'])->name("store");
        });
    });
});
