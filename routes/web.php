<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\CustomFieldsetController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FormDemoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusLabelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return to login
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Route group middleware for authenticated users
Route::middleware(['auth'])->group(function () {
    // User management routes with permissions
    Route::middleware(['permission:management.users.create'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware(['permission:management.users.delete'])->group(function () {
        Route::get('/users/trash', [UserController::class, 'trash'])->name('users.trash');
        Route::get('/users/trash/data', [UserController::class, 'getTrashData'])->name('users.trash.data');
    });

    Route::middleware(['permission:management.users.view'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
        Route::get('/users/logs', [UserController::class, 'logs'])->name('users.logs');
        Route::get('/users/logs/data', [UserController::class, 'getLogsData'])->name('users.logs.data');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/logs', [UserController::class, 'userLogs'])->name('users.user-logs');
        Route::get('/users/{user}/logs/data', [UserController::class, 'getUserLogsData'])->name('users.user-logs.data');
    });

    Route::middleware(['permission:management.users.edit'])->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}', [UserController::class, 'update']);
    });

    Route::middleware(['permission:management.users.permissions'])->group(function () {
        Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.update-permissions');
        Route::post('/users/{user}/apply-template', [UserController::class, 'applyTemplate'])->name('users.apply-template');
        Route::get('/roles/{role}/permissions', [UserController::class, 'getTemplatePermissions'])->name('roles.permissions');
    });

    Route::middleware(['permission:management.users.delete'])->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    });

    Route::middleware(['permission:management.users.restore'])->group(function () {
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    });

    // Role management routes with permissions
    Route::middleware(['permission:management.roles.create'])->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware(['permission:management.roles.view'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/data', [RoleController::class, 'getData'])->name('roles.data');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });

    Route::middleware(['permission:management.roles.edit'])->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::patch('/roles/{role}', [RoleController::class, 'update']);
    });

    Route::middleware(['permission:management.roles.delete'])->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Company management routes with permissions
    Route::middleware(['permission:settings.companies.create'])->group(function () {
        Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    });

    Route::middleware(['permission:settings.companies.view'])->group(function () {
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/data', [CompanyController::class, 'getData'])->name('companies.data');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    });

    Route::middleware(['permission:settings.companies.edit'])->group(function () {
        Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}', [CompanyController::class, 'update']);
    });

    Route::middleware(['permission:settings.companies.delete'])->group(function () {
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    });

    // Category management routes with permissions
    Route::middleware(['permission:settings.categories.create'])->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    });

    Route::middleware(['permission:settings.categories.view'])->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/data', [CategoryController::class, 'getData'])->name('categories.data');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });

    Route::middleware(['permission:settings.categories.edit'])->group(function () {
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{category}', [CategoryController::class, 'update']);
    });

    Route::middleware(['permission:settings.categories.delete'])->group(function () {
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Manufacturer management routes with permissions
    Route::middleware(['permission:settings.manufacturers.create'])->group(function () {
        Route::get('/manufacturers/create', [ManufacturerController::class, 'create'])->name('manufacturers.create');
        Route::post('/manufacturers', [ManufacturerController::class, 'store'])->name('manufacturers.store');
    });

    Route::middleware(['permission:settings.manufacturers.view'])->group(function () {
        Route::get('/manufacturers', [ManufacturerController::class, 'index'])->name('manufacturers.index');
        Route::get('/manufacturers/data', [ManufacturerController::class, 'getData'])->name('manufacturers.data');
        Route::get('/manufacturers/{manufacturer}', [ManufacturerController::class, 'show'])->name('manufacturers.show');
    });

    Route::middleware(['permission:settings.manufacturers.edit'])->group(function () {
        Route::get('/manufacturers/{manufacturer}/edit', [ManufacturerController::class, 'edit'])->name('manufacturers.edit');
        Route::put('/manufacturers/{manufacturer}', [ManufacturerController::class, 'update'])->name('manufacturers.update');
        Route::patch('/manufacturers/{manufacturer}', [ManufacturerController::class, 'update']);
    });

    Route::middleware(['permission:settings.manufacturers.delete'])->group(function () {
        Route::delete('/manufacturers/{manufacturer}', [ManufacturerController::class, 'destroy'])->name('manufacturers.destroy');
    });

    // Supplier management routes with permissions
    Route::middleware(['permission:settings.suppliers.create'])->group(function () {
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    });

    Route::middleware(['permission:settings.suppliers.view'])->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/data', [SupplierController::class, 'getData'])->name('suppliers.data');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    });

    Route::middleware(['permission:settings.suppliers.edit'])->group(function () {
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update']);
    });

    Route::middleware(['permission:settings.suppliers.delete'])->group(function () {
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // Status label management routes with permissions
    Route::middleware(['permission:settings.status_labels.create'])->group(function () {
        Route::get('/status-labels/create', [StatusLabelController::class, 'create'])->name('status-labels.create');
        Route::post('/status-labels', [StatusLabelController::class, 'store'])->name('status-labels.store');
    });

    Route::middleware(['permission:settings.status_labels.view'])->group(function () {
        Route::get('/status-labels', [StatusLabelController::class, 'index'])->name('status-labels.index');
        Route::get('/status-labels/data', [StatusLabelController::class, 'getData'])->name('status-labels.data');
        Route::get('/status-labels/{statusLabel}', [StatusLabelController::class, 'show'])->name('status-labels.show');
    });

    Route::middleware(['permission:settings.status_labels.edit'])->group(function () {
        Route::get('/status-labels/{statusLabel}/edit', [StatusLabelController::class, 'edit'])->name('status-labels.edit');
        Route::put('/status-labels/{statusLabel}', [StatusLabelController::class, 'update'])->name('status-labels.update');
        Route::patch('/status-labels/{statusLabel}', [StatusLabelController::class, 'update']);
    });

    Route::middleware(['permission:settings.status_labels.delete'])->group(function () {
        Route::delete('/status-labels/{statusLabel}', [StatusLabelController::class, 'destroy'])->name('status-labels.destroy');
    });

    // Custom field management routes with permissions
    Route::middleware(['permission:settings.custom_fields.create'])->group(function () {
        Route::get('/custom-fields/create', [CustomFieldController::class, 'create'])->name('custom-fields.create');
        Route::post('/custom-fields', [CustomFieldController::class, 'store'])->name('custom-fields.store');
    });

    Route::middleware(['permission:settings.custom_fields.view'])->group(function () {
        Route::get('/custom-fields', [CustomFieldController::class, 'index'])->name('custom-fields.index');
        Route::get('/custom-fields/data', [CustomFieldController::class, 'getData'])->name('custom-fields.data');
        Route::get('/custom-fields/{custom_field}', [CustomFieldController::class, 'show'])->name('custom-fields.show');
    });

    Route::middleware(['permission:settings.custom_fields.edit'])->group(function () {
        Route::get('/custom-fields/{custom_field}/edit', [CustomFieldController::class, 'edit'])->name('custom-fields.edit');
        Route::put('/custom-fields/{custom_field}', [CustomFieldController::class, 'update'])->name('custom-fields.update');
        Route::patch('/custom-fields/{custom_field}', [CustomFieldController::class, 'update']);
    });

    Route::middleware(['permission:settings.custom_fields.delete'])->group(function () {
        Route::delete('/custom-fields/{custom_field}', [CustomFieldController::class, 'destroy'])->name('custom-fields.destroy');
    });

    // Custom fieldset management routes with permissions
    Route::middleware(['permission:settings.custom_fieldsets.create'])->group(function () {
        Route::get('/custom-fieldsets/create', [CustomFieldsetController::class, 'create'])->name('custom-fieldsets.create');
        Route::post('/custom-fieldsets', [CustomFieldsetController::class, 'store'])->name('custom-fieldsets.store');
    });

    Route::middleware(['permission:settings.custom_fieldsets.view'])->group(function () {
        Route::get('/custom-fieldsets', [CustomFieldsetController::class, 'index'])->name('custom-fieldsets.index');
        Route::get('/custom-fieldsets/data', [CustomFieldsetController::class, 'getData'])->name('custom-fieldsets.data');
        Route::get('/custom-fieldsets/{custom_fieldset}', [CustomFieldsetController::class, 'show'])->name('custom-fieldsets.show');
    });

    Route::middleware(['permission:settings.custom_fieldsets.edit'])->group(function () {
        Route::get('/custom-fieldsets/{custom_fieldset}/edit', [CustomFieldsetController::class, 'edit'])->name('custom-fieldsets.edit');
        Route::put('/custom-fieldsets/{custom_fieldset}', [CustomFieldsetController::class, 'update'])->name('custom-fieldsets.update');
        Route::patch('/custom-fieldsets/{custom_fieldset}', [CustomFieldsetController::class, 'update']);
    });

    Route::middleware(['permission:settings.custom_fieldsets.delete'])->group(function () {
        Route::delete('/custom-fieldsets/{custom_fieldset}', [CustomFieldsetController::class, 'destroy'])->name('custom-fieldsets.destroy');
    });

    // Location management routes with permissions
    Route::middleware(['permission:settings.locations.create'])->group(function () {
        Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    });

    Route::middleware(['permission:settings.locations.view'])->group(function () {
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::get('/locations/data', [LocationController::class, 'getData'])->name('locations.data');
        Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');
    });

    Route::middleware(['permission:settings.locations.edit'])->group(function () {
        Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
        Route::patch('/locations/{location}', [LocationController::class, 'update']);
    });

    Route::middleware(['permission:settings.locations.delete'])->group(function () {
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    });

    // Department management routes with permissions
    Route::middleware(['permission:settings.departments.create'])->group(function () {
        Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    });

    Route::middleware(['permission:settings.departments.view'])->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/data', [DepartmentController::class, 'getData'])->name('departments.data');
        Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    });

    Route::middleware(['permission:settings.departments.edit'])->group(function () {
        Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::patch('/departments/{department}', [DepartmentController::class, 'update']);
    });

    Route::middleware(['permission:settings.departments.delete'])->group(function () {
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    // User role assignment routes
    Route::middleware(['permission:management.users.edit'])->group(function () {
        Route::get('/users/{user}/roles', [UserRoleController::class, 'show'])->name('users.roles');
        Route::put('/users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');
        Route::post('/users/{user}/roles/assign', [UserRoleController::class, 'assignRole'])->name('users.roles.assign');
        Route::delete('/users/{user}/roles/remove', [UserRoleController::class, 'removeRole'])->name('users.roles.remove');
    });

    // Generic FilePond routes
    Route::post('/uploads/process', [FileUploadController::class, 'process'])->name('uploads.process');
    Route::delete('/uploads/revert', [FileUploadController::class, 'revert'])->name('uploads.revert');
    Route::get('/uploads/load', [FileUploadController::class, 'load'])->name('uploads.load');

    // Form demo routes
    Route::get('/form-demo', [FormDemoController::class, 'index'])->name('form-demo.index');
    Route::post('/form-demo', [FormDemoController::class, 'store'])->name('form-demo.store');
});
