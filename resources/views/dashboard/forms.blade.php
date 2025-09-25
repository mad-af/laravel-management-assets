@extends('layouts.dashboard')

@section('title', 'Forms')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-3xl font-bold text-base-content">Forms</h1>
            <p class="text-base-content/70 mt-1">Different form variations using DaisyUI components.</p>
        </div>

        <!-- Basic Form -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg font-semibold mb-4">Basic Form</h2>
                <form class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Full Name</span>
                        </label>
                        <input type="text" placeholder="Enter your full name" class="input input-bordered" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" placeholder="Enter your email" class="input input-bordered" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Phone Number</span>
                        </label>
                        <input type="tel" placeholder="Enter your phone number" class="input input-bordered" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Message</span>
                        </label>
                        <textarea class="textarea textarea-bordered" placeholder="Enter your message"></textarea>
                    </div>

                    <div class="form-control mt-6">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Advanced Form -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg font-semibold mb-4">Advanced Form</h2>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">First Name</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <input type="text" placeholder="First name" class="input input-bordered" required />
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Last Name</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <input type="text" placeholder="Last name" class="input input-bordered" required />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email Address</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="email" placeholder="email@example.com" class="input input-bordered" required />
                        <label class="label">
                            <span class="label-text-alt">We'll never share your email with anyone else.</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Country</span>
                        </label>
                        <select class="select select-bordered">
                            <option disabled selected>Select your country</option>
                            <option>Indonesia</option>
                            <option>United States</option>
                            <option>Canada</option>
                            <option>United Kingdom</option>
                            <option>Australia</option>
                            <option>Germany</option>
                            <option>France</option>
                            <option>Japan</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Gender</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="label cursor-pointer">
                                <input type="radio" name="gender" class="radio" value="male" />
                                <span class="label-text ml-2">Male</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input type="radio" name="gender" class="radio" value="female" />
                                <span class="label-text ml-2">Female</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input type="radio" name="gender" class="radio" value="other" />
                                <span class="label-text ml-2">Other</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Interests</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" class="checkbox" />
                                <span class="label-text ml-2">Technology</span>
                            </label>
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" class="checkbox" />
                                <span class="label-text ml-2">Sports</span>
                            </label>
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" class="checkbox" />
                                <span class="label-text ml-2">Music</span>
                            </label>
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" class="checkbox" />
                                <span class="label-text ml-2">Travel</span>
                            </label>
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" class="checkbox" />
                                <span class="label-text ml-2">Reading</span>
                            </label>
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" class="checkbox" />
                                <span class="label-text ml-2">Cooking</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Bio</span>
                        </label>
                        <textarea class="textarea textarea-bordered h-24"
                            placeholder="Tell us about yourself..."></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input type="checkbox" class="checkbox" />
                            <span class="label-text ml-2">I agree to the terms and conditions</span>
                        </label>
                    </div>

                    <div class="form-control mt-6">
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary flex-1">Submit</button>
                            <button type="reset" class="btn ">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form with Validation States -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg font-semibold mb-4">Form with Validation States</h2>
                <form class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Valid Input</span>
                        </label>
                        <input type="text" placeholder="This is valid" class="input input-bordered input-success"
                            value="john@example.com" />
                        <label class="label">
                            <span class="label-text-alt text-success">✓ Looks good!</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Invalid Input</span>
                        </label>
                        <input type="text" placeholder="This has an error" class="input input-bordered input-error"
                            value="invalid-email" />
                        <label class="label">
                            <span class="label-text-alt text-error">✗ Please provide a valid email address.</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Warning Input</span>
                        </label>
                        <input type="text" placeholder="This has a warning" class="input input-bordered input-warning"
                            value="short" />
                        <label class="label">
                            <span class="label-text-alt text-warning">⚠ Password should be at least 8 characters.</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Info Input</span>
                        </label>
                        <input type="text" placeholder="This has info" class="input input-bordered input-info" />
                        <label class="label">
                            <span class="label-text-alt text-info">ℹ This field is optional.</span>
                        </label>
                    </div>

                    <div class="form-control mt-6">
                        <button class="btn btn-primary">Submit Form</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Input Sizes and Variants -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg font-semibold mb-4">Input Sizes and Variants</h2>
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Large Input</span>
                        </label>
                        <input type="text" placeholder="Large input" class="input input-bordered input-lg" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Normal Input</span>
                        </label>
                        <input type="text" placeholder="Normal input" class="input input-bordered" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Small Input</span>
                        </label>
                        <input type="text" placeholder="Small input" class="input input-bordered input-sm" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Extra Small Input</span>
                        </label>
                        <input type="text" placeholder="Extra small input" class="input input-bordered input-xs" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Ghost Input</span>
                        </label>
                        <input type="text" placeholder="Ghost input" class="input input-ghost" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Disabled Input</span>
                        </label>
                        <input type="text" placeholder="Disabled input" class="input input-bordered" disabled />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection