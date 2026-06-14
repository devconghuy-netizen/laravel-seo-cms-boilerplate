<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminAffiliateLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminMediaController;
use App\Http\Controllers\AdminTagController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminAuditLogController;
use App\Http\Controllers\AdminHealthController;
use App\Http\Controllers\AdminSiteSettingController;
use App\Http\Controllers\AuthorPostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostPreviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TaxonomyController;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{affiliateLink:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/go/{affiliateLink:slug}', [ProductController::class, 'redirect'])
    ->middleware('throttle:30,1')
    ->name('affiliate.redirect');
Route::get('/categories/{category:slug}', [TaxonomyController::class, 'category'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TaxonomyController::class, 'tag'])->name('tags.show');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:3,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/email/verify', [AuthController::class, 'showEmailVerification'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::middleware('verified')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{key}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/creator/posts', [AuthorPostController::class, 'index'])->name('creator.posts.index');
        Route::get('/creator/posts/create', [AuthorPostController::class, 'create'])->name('creator.posts.create');
        Route::post('/creator/posts', [AuthorPostController::class, 'store'])->name('creator.posts.store');
        Route::get('/creator/posts/{post:slug}/edit', [AuthorPostController::class, 'edit'])->name('creator.posts.edit');
        Route::put('/creator/posts/{post:slug}', [AuthorPostController::class, 'update'])->name('creator.posts.update');
        Route::delete('/creator/posts/{post:slug}', [AuthorPostController::class, 'destroy'])->name('creator.posts.destroy');
        Route::get('/preview/posts/{post:slug}', PostPreviewController::class)
            ->middleware('signed')
            ->name('posts.preview');

        Route::get('/admin/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');
        Route::get('/admin/posts/{post:slug}/edit', [AdminPostController::class, 'edit'])->name('admin.posts.edit');
        Route::put('/admin/posts/{post:slug}', [AdminPostController::class, 'update'])->name('admin.posts.update');
        Route::post('/admin/posts/{post:slug}/publish', [AdminPostController::class, 'publish'])->name('admin.posts.publish');
        Route::delete('/admin/posts/{post:slug}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');

        Route::resource('/admin/categories', AdminCategoryController::class)
            ->except(['show'])
            ->names('admin.categories');
        Route::resource('/admin/tags', AdminTagController::class)
            ->except(['show'])
            ->names('admin.tags');
        Route::get('/admin/media', [AdminMediaController::class, 'index'])->name('admin.media.index');
        Route::delete('/admin/media/{media}', [AdminMediaController::class, 'destroy'])->name('admin.media.destroy');
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::get('/admin/audit-logs', [AdminAuditLogController::class, 'index'])->name('admin.audit-logs.index');
        Route::get('/admin/health', [AdminHealthController::class, 'index'])->name('admin.health.index');
        Route::get('/admin/settings', [AdminSiteSettingController::class, 'edit'])->name('admin.settings.edit');
        Route::put('/admin/settings', [AdminSiteSettingController::class, 'update'])->name('admin.settings.update');
        Route::get('/admin/affiliate-links/export', [AdminAffiliateLinkController::class, 'export'])->name('admin.affiliate-links.export');
        Route::post('/admin/affiliate-links/import', [AdminAffiliateLinkController::class, 'import'])->name('admin.affiliate-links.import');
        Route::post('/admin/affiliate-links/{affiliateLink:slug}/conversion', [AdminAffiliateLinkController::class, 'recordConversion'])->name('admin.affiliate-links.conversion');
        Route::resource('/admin/affiliate-links', AdminAffiliateLinkController::class)
            ->except(['show'])
            ->names('admin.affiliate-links');
    });
});
