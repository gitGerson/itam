<?php

namespace App\Http\Controllers;

class FormDemoController extends Controller
{
    public function index()
    {
        $roleTemplateOptions = [
            ['id' => 'admin_template', 'display_name' => 'Administrator Template'],
            ['id' => 'user_manager_template', 'display_name' => 'User Manager Template'],
            ['id' => 'viewer_template', 'display_name' => 'Viewer Template'],
        ];

        $statusOptions = [
            'draft' => 'Draft',
            'review' => 'In Review',
            'published' => 'Published',
        ];

        return view('form-demo.index', compact('roleTemplateOptions', 'statusOptions'));
    }
}
