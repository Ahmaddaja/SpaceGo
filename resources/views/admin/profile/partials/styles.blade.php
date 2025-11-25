<style>
    .profile-avatar {
        width: 120px;
        height: 120px;
        font-size: 48px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
    }

    .profile-avatar:hover .profile-avatar-overlay {
        opacity: 1;
    }

    .profile-avatar-overlay i {
        font-size: 24px;
        color: white;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #007bff;
    }

    .password-input-wrapper {
        position: relative;
    }

    .password-input-wrapper .form-control {
        padding-right: 40px;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .alert {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .badge {
        transition: all 0.2s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    .info-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }

    .card-header {
        border-bottom: 2px solid #007bff;
    }

    .form-group label {
        color: #495057;
        margin-bottom: 8px;
    }

    .text-danger {
        font-weight: 600;
    }

    hr {
        border-top: 2px solid #e9ecef;
    }

    /* Custom scrollbar untuk textarea */
    textarea::-webkit-scrollbar {
        width: 8px;
    }

    textarea::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    textarea::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    textarea::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        font-size: 2.5rem;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .profile-avatar-lg {
        width: 150px;
        height: 150px;
        font-size: 3rem;
        position: relative;
        overflow: hidden;
    }

    .profile-avatar img,
    .profile-avatar-lg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .info-label {
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #343a40;
        font-weight: 600;
        font-size: 0.9rem;
    }

    #new-photo-preview {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .photo-preview-container {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
    }

    /* Animation untuk form */
    .form-control, .form-group {
        animation: fadeInUp 0.4s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-avatar {
            width: 100px;
            height: 100px;
            font-size: 36px;
        }

        .card:hover {
            transform: none;
        }

        .btn:hover {
            transform: none;
        }
    }

    #photo-input {
        display: none;
    }
</style>    