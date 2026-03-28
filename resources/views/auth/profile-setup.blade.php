@extends('layout')

@section('content')
    <div class="animate-fade-in" style="margin-top: 40px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="font-size: 1.75rem;">{{ Auth::check() ? 'Edit Profile' : 'Profile Info' }}</h1>
            <p style="color: var(--text-dim); margin-top: 8px;">Please provide your name and an optional profile photo.</p>
        </div>

        <div class="card">
            <form action="{{ Auth::check() ? route('profile.update') : url('/profile-setup') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 32px;">
                    <div id="avatar-preview"
                        style="width: 100px; height: 100px; border-radius: 50px; background: var(--glass-border); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; cursor: pointer; border: 2px solid {{ $errors->has('avatar') ? 'var(--accent)' : 'var(--primary-light)' }};">
                        @if (Auth::check() && Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fa-solid fa-camera" style="font-size: 1.5rem; color: var(--text-dim);"></i>
                        @endif
                        <input type="file" name="avatar" id="avatar-input" accept="image/*"
                            style="opacity: 0; position: absolute; inset: 0; cursor: pointer;">
                    </div>
                    @error('avatar')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 8px;"><i
                                class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                    <p style="font-size: 0.75rem; color: var(--primary-light); margin-top: 12px; font-weight: 600;">Change
                        Profile Photo</p>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 8px;">Your
                        Name</label>
                    <input type="text" name="name" placeholder="Enter your name"
                        value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid {{ $errors->has('name') ? 'var(--accent)' : 'var(--glass-border)' }}; border-radius: 12px; padding: 14px; color: white; outline: none; font-size: 1rem;">
                    @error('name')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 8px;"><i
                                class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width: 100%; padding: 16px;">{{ Auth::check() ? 'Save Changes' : 'Finish Setup' }}</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ex) {
                    const preview = document.getElementById('avatar-preview');
                    let img = preview.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        preview.insertBefore(img, preview.firstChild);
                    }
                    img.src = ex.target.result;
                    const icon = preview.querySelector('i');
                    if (icon) icon.style.display = 'none';
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>

    <style>
        .bottom-nav,
        .fab {
            display: none !important;
        }

        body {
            padding-bottom: 20px;
        }
    </style>
@endsection
