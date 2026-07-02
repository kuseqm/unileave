@extends('layouts.master')

@section('title', 'Letterhead Settings')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Settings', 'url' => '#'],
        ['name' => 'Letterhead', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LETTERHEAD SETTINGS" description="Configure university letterhead branding">
    </x-page-header>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row g-2">
        <!-- Settings Form -->
        <div class="col-lg-6">
            <form action="{{ route('admin.settings.letterhead.update') }}" method="POST" enctype="multipart/form-data" id="letterheadForm">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-building me-1"></i>
                        <span style="font-weight: 600;">Basic Information</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="row g-1">
                            <div class="col-md-8 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">University Name</label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-sm" value="{{ old('name', $university->name) }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Code</label>
                                <input type="text" name="code" class="form-control form-control-sm rounded-sm" value="{{ old('code', $university->code) }}" style="font-size: 0.7rem;" required>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">University Logo</label>
                            <input type="file" name="logo" class="form-control form-control-sm rounded-sm" accept="image/*" style="font-size: 0.7rem;">
                            @if($university->logo_path)
                                <div class="mt-1">
                                    <img src="{{ Storage::url($university->logo_path) }}" style="max-height: 50px;">
                                    <small class="text-muted d-block">Current logo</small>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="active" {{ $university->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $university->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- University Name Styling -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-text-size me-1"></i>
                        <span style="font-weight: 600;">University Name Styling</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Size</label>
                                <select name="uni_name_font_size" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="14pt" {{ $university->uni_name_font_size == '14pt' ? 'selected' : '' }}>14pt</option>
                                    <option value="16pt" {{ $university->uni_name_font_size == '16pt' ? 'selected' : '' }}>16pt</option>
                                    <option value="18pt" {{ $university->uni_name_font_size == '18pt' ? 'selected' : '' }}>18pt</option>
                                    <option value="20pt" {{ $university->uni_name_font_size == '20pt' ? 'selected' : '' }}>20pt</option>
                                    <option value="22pt" {{ $university->uni_name_font_size == '22pt' ? 'selected' : '' }}>22pt</option>
                                    <option value="24pt" {{ $university->uni_name_font_size == '24pt' ? 'selected' : '' }}>24pt</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Weight</label>
                                <select name="uni_name_font_weight" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="normal" {{ $university->uni_name_font_weight == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="bold" {{ $university->uni_name_font_weight == 'bold' ? 'selected' : '' }}>Bold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Office Title -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-briefcase me-1"></i>
                        <span style="font-weight: 600;">Office Title</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Office Title Text</label>
                            <input type="text" name="office_title" class="form-control form-control-sm rounded-sm" value="{{ old('office_title', $university->office_title) }}" style="font-size: 0.7rem;">
                        </div>
                        <div class="row g-1">
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Size</label>
                                <select name="office_title_font_size" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="10pt" {{ $university->office_title_font_size == '10pt' ? 'selected' : '' }}>10pt</option>
                                    <option value="11pt" {{ $university->office_title_font_size == '11pt' ? 'selected' : '' }}>11pt</option>
                                    <option value="12pt" {{ $university->office_title_font_size == '12pt' ? 'selected' : '' }}>12pt</option>
                                    <option value="13pt" {{ $university->office_title_font_size == '13pt' ? 'selected' : '' }}>13pt</option>
                                    <option value="14pt" {{ $university->office_title_font_size == '14pt' ? 'selected' : '' }}>14pt</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Color</label>
                                <input type="color" name="office_title_font_color" class="form-control form-control-sm rounded-sm" value="{{ old('office_title_font_color', $university->office_title_font_color ?? '#000000') }}" style="height: 34px;">
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Weight</label>
                                <select name="office_title_font_weight" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="normal" {{ $university->office_title_font_weight == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="bold" {{ $university->office_title_font_weight == 'bold' ? 'selected' : '' }}>Bold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Colors -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-palette me-1"></i>
                        <span style="font-weight: 600;">Brand Colors</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Primary Color</label>
                                <input type="color" name="primary_color" class="form-control form-control-sm rounded-sm" value="{{ old('primary_color', $university->primary_color ?? '#000000') }}" style="height: 34px;">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Secondary Color</label>
                                <input type="color" name="secondary_color" class="form-control form-control-sm rounded-sm" value="{{ old('secondary_color', $university->secondary_color ?? '#cccccc') }}" style="height: 34px;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative Lines -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-layout-grid me-1"></i>
                        <span style="font-weight: 600;">Decorative Lines</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Primary Line (Thick)</label>
                                <div class="d-flex gap-1">
                                    <input type="color" name="primary_line_color" class="form-control form-control-sm rounded-sm" value="{{ old('primary_line_color', $university->primary_line_color ?? '#000000') }}" style="height: 34px; width: 60px;">
                                    <input type="text" name="primary_line_thickness" class="form-control form-control-sm rounded-sm" value="{{ old('primary_line_thickness', $university->primary_line_thickness ?? '2px') }}" style="font-size: 0.7rem; width: 80px;" placeholder="2px">
                                    <span class="text-muted" style="font-size: 0.6rem; line-height: 32px;">Thickness</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.55rem;">e.g., 2px, 3px, 4px</small>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Secondary Line (Thin)</label>
                                <div class="d-flex gap-1">
                                    <input type="color" name="secondary_line_color" class="form-control form-control-sm rounded-sm" value="{{ old('secondary_line_color', $university->secondary_line_color ?? '#cccccc') }}" style="height: 34px; width: 60px;">
                                    <input type="text" name="secondary_line_thickness" class="form-control form-control-sm rounded-sm" value="{{ old('secondary_line_thickness', $university->secondary_line_thickness ?? '1px') }}" style="font-size: 0.7rem; width: 80px;" placeholder="1px">
                                    <span class="text-muted" style="font-size: 0.6rem; line-height: 32px;">Thickness</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.55rem;">e.g., 1px, 2px, 3px</small>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-light rounded-sm">
                            <small class="text-muted" style="font-size: 0.55rem;">
                                <i class="ti ti-info-circle"></i> These are the two horizontal lines below your letterhead. The primary line is thicker at the top, secondary line is thinner below it.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information (Website as TEXT, not URL) -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-phone me-1"></i>
                        <span style="font-weight: 600;">Contact Information</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Primary Phone</label>
                                <input type="text" name="phone" class="form-control form-control-sm rounded-sm" value="{{ old('phone', $university->phone) }}" style="font-size: 0.7rem;">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Secondary Phone</label>
                                <input type="text" name="phone_secondary" class="form-control form-control-sm rounded-sm" value="{{ old('phone_secondary', $university->phone_secondary) }}" style="font-size: 0.7rem;">
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Primary Email</label>
                                <input type="email" name="email" class="form-control form-control-sm rounded-sm" value="{{ old('email', $university->email) }}" style="font-size: 0.7rem;">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Secondary Email</label>
                                <input type="email" name="email_secondary" class="form-control form-control-sm rounded-sm" value="{{ old('email_secondary', $university->email_secondary) }}" style="font-size: 0.7rem;">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Website (Text Only - Not Clickable)</label>
                            <input type="text" name="website" class="form-control form-control-sm rounded-sm" value="{{ old('website', $university->website) }}" style="font-size: 0.7rem;" placeholder="www.example.com">
                            <small class="text-muted" style="font-size: 0.55rem;">This will appear as plain text, not a clickable link.</small>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media - NAMES ONLY (No URL field) -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-brand-facebook me-1"></i>
                        <span style="font-weight: 600;">Social Media (Names Only)</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <p class="text-muted mb-2" style="font-size: 0.6rem;">Enter social media platform names (Facebook, Twitter, Instagram, etc.)</p>
                        @for($i = 1; $i <= 3; $i++)
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.6rem;">Platform {{ $i }}</label>
                            <input type="text" name="social_media_{{ $i }}_name" class="form-control form-control-sm rounded-sm" placeholder="e.g., Facebook, Twitter, Instagram" value="{{ old('social_media_' . $i . '_name', $university->{'social_media_' . $i . '_name'}) }}" style="font-size: 0.7rem;">
                        </div>
                        @endfor
                        <small class="text-muted" style="font-size: 0.55rem;">Only the platform name will appear on the letterhead (not clickable).</small>
                    </div>
                </div>
                
                <!-- Address -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-map-pin me-1"></i>
                        <span style="font-weight: 600;">Address</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Full Address</label>
                            <textarea name="address" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">{{ old('address', $university->address) }}</textarea>
                        </div>
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">P.O. Box</label>
                                <input type="text" name="postal_box" class="form-control form-control-sm rounded-sm" value="{{ old('postal_box', $university->postal_box) }}" style="font-size: 0.7rem;">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">City</label>
                                <input type="text" name="city" class="form-control form-control-sm rounded-sm" value="{{ old('city', $university->city) }}" style="font-size: 0.7rem;">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Country</label>
                            <input type="text" name="country" class="form-control form-control-sm rounded-sm" value="{{ old('country', $university->country ?? 'Tanzania') }}" style="font-size: 0.7rem;">
                        </div>
                    </div>
                </div>
                
                <!-- Motto -->
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-quote me-1"></i>
                        <span style="font-weight: 600;">Motto / Slogan</span>
                    </div>
                    <div class="card-body py-2 px-2">
                        <div class="mb-1">
                            <input type="text" name="motto" class="form-control form-control-sm rounded-sm" value="{{ old('motto', $university->motto) }}" style="font-size: 0.7rem;" placeholder="Enter university motto...">
                        </div>
                        <div class="row g-1">
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Size</label>
                                <select name="motto_font_size" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="8pt" {{ $university->motto_font_size == '8pt' ? 'selected' : '' }}>8pt</option>
                                    <option value="9pt" {{ $university->motto_font_size == '9pt' ? 'selected' : '' }}>9pt</option>
                                    <option value="10pt" {{ $university->motto_font_size == '10pt' ? 'selected' : '' }}>10pt</option>
                                    <option value="11pt" {{ $university->motto_font_size == '11pt' ? 'selected' : '' }}>11pt</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Color</label>
                                <input type="color" name="motto_font_color" class="form-control form-control-sm rounded-sm" value="{{ old('motto_font_color', $university->motto_font_color ?? '#666666') }}" style="height: 34px;">
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Font Style</label>
                                <select name="motto_font_style" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="normal" {{ $university->motto_font_style == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="italic" {{ $university->motto_font_style == 'italic' ? 'selected' : '' }}>Italic</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save Settings
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm rounded-sm" onclick="refreshPreview()" style="font-size: 0.7rem;">
                        <i class="ti ti-refresh me-1"></i> Refresh Preview
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Live Preview -->
        <div class="col-lg-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ti ti-eye me-1"></i>
                            <span style="font-weight: 600;">Live Preview</span>
                        </div>
                        <span class="badge bg-info rounded-sm" style="font-size: 0.55rem;">Real-time preview</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div id="letterPreview" class="border rounded-sm p-3" style="background: #FDF8F0; min-height: 400px;">
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-loader"></i> Loading preview...
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <small class="text-muted">This is how your letterhead will appear on official letters</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function refreshPreview() {
        const form = document.getElementById('letterheadForm');
        const formData = new FormData(form);
        
        formData.delete('_method');
        
        fetch('{{ route("admin.settings.letterhead.preview") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('letterPreview').innerHTML = html;
        })
        .catch(error => {
            console.error('Preview error:', error);
            document.getElementById('letterPreview').innerHTML = '<div class="text-center py-5 text-danger">Failed to load preview</div>';
        });
    }
    
    document.querySelectorAll('#letterheadForm input, #letterheadForm select, #letterheadForm textarea').forEach(element => {
        element.addEventListener('change', refreshPreview);
        element.addEventListener('keyup', refreshPreview);
    });
    
    refreshPreview();
</script>
@endsection