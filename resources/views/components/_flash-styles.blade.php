@once
<style>
.flash-banner {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.6rem;
    border-radius: 0.2rem;
    margin-bottom: 0.5rem;
    font-size: 0.72rem;
    line-height: 1.3;
    animation: flashBannerIn 0.2s ease-out;
}

.flash-banner-icon {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.65rem;
    flex-shrink: 0;
}

.flash-banner-text {
    flex: 1;
    min-width: 0;
}

.flash-banner--success {
    background: #ECFDF5;
    border-left: 3px solid #10B981;
}

.flash-banner--success .flash-banner-icon {
    background: #10B981;
}

.flash-banner--success .flash-banner-text {
    color: #065F46;
}

.flash-banner--error {
    background: #FEF2F2;
    border-left: 3px solid #EF4444;
}

.flash-banner--error .flash-banner-icon {
    background: #EF4444;
}

.flash-banner--error .flash-banner-text {
    color: #991B1B;
}

.flash-banner--warning {
    background: #FFFBEB;
    border-left: 3px solid #F59E0B;
}

.flash-banner--warning .flash-banner-icon {
    background: #F59E0B;
}

.flash-banner--warning .flash-banner-text {
    color: #92400E;
}

.flash-banner--info {
    background: #EFF6FF;
    border-left: 3px solid #3B82F6;
}

.flash-banner--info .flash-banner-icon {
    background: #3B82F6;
}

.flash-banner--info .flash-banner-text {
    color: #1E40AF;
}

.flash-banner--secondary {
    background: #F3F4F6;
    border-left: 3px solid #6B7280;
}

.flash-banner--secondary .flash-banner-icon {
    background: #6B7280;
}

.flash-banner--secondary .flash-banner-text {
    color: #374151;
}

.flash-banner--light {
    background: #F9FAFB;
    border-left: 3px solid #D1D5DB;
}

.flash-banner--light .flash-banner-text {
    color: #374151;
}

@keyframes flashBannerIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endonce
