@extends('layouts.sneat')

@section('title')
    Form Demo
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Form Components Demo</h4>
                <p class="text-muted mb-0">Reference page for the reusable <code>x-form.*</code> Blade components.</p>
            </div>
        </div>

        <form action="javascript:void(0)" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Basic Inputs</h5>
                        </div>
                        <div class="card-body">
                            <x-form.text
                                name="title"
                                label="Title"
                                value="Demo Content Title"
                                placeholder="Enter title"
                                help="Basic text input with label, help text, and old value support."
                                required
                            />

                            <x-form.number
                                name="quantity"
                                label="Quantity"
                                value="12"
                                min="0"
                                help="Numeric input example."
                            />

                            <x-form.select
                                name="status"
                                label="Status"
                                :options="$statusOptions"
                                value="review"
                                placeholder="Choose status"
                                help="Native select component with placeholder support."
                            />

                            <x-form.select
                                name="role_template"
                                label="Role Template"
                                :options="$roleTemplateOptions"
                                optionValue="id"
                                optionLabel="display_name"
                                value="admin_template"
                                placeholder="Select template"
                                help="Select using object arrays."
                            />

                            <x-form.checkbox
                                name="featured"
                                label="Featured content"
                                :checked="true"
                                help="Standard checkbox component."
                            />

                            <x-form.switch
                                name="is_active"
                                label="Active"
                                :checked="true"
                                help="Bootstrap switch-style toggle."
                            />
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Date Inputs</h5>
                        </div>
                        <div class="card-body">
                            <x-form.date
                                name="publish_date"
                                label="Publish Date"
                                value="2026-04-06"
                                help="Native browser date input."
                            />

                            <x-form.date
                                name="event_date"
                                label="Event Date"
                                mode="daterangepicker"
                                :single="true"
                                value="2026-04-10"
                                help="Single-date picker mode powered by Date Range Picker."
                            />

                            <x-form.date
                                name="booking_window"
                                label="Booking Window"
                                mode="daterangepicker"
                                :single="false"
                                value="2026-04-10 - 2026-04-20"
                                help="Range picker mode using the same component."
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Text Areas</h5>
                        </div>
                        <div class="card-body">
                            <x-form.textarea
                                name="summary"
                                label="Summary"
                                rows="4"
                                value="Short summary text for the demo module."
                                placeholder="Write a summary"
                                help="Plain textarea component."
                            />

                            <x-form.rich-editor
                                name="body"
                                label="Rich Editor"
                                value="<p>This rich editor component currently degrades gracefully to a styled textarea until a full editor module is wired in.</p>"
                                help="Rich-editor-shaped component with a future enhancement hook."
                            />
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">File Input</h5>
                        </div>
                        <div class="card-body">
                            <x-form.file
                                name="attachment"
                                label="Attachment"
                                help="Reusable file input with optional current file preview."
                                preview="https://example.com/files/demo-brief.pdf"
                            />

                            <div class="alert alert-info mb-0" role="alert">
                                This module is intended as a live visual reference. It is not connected to a save action.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
