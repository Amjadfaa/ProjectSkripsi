<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MONPASKU') }}</title>

    <!-- Early Sidebar State Initialization to prevent layout shift / FOUC -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('monpasku_sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --sidebar-w: 260px;
            --sidebar-w-collapsed: 76px;
            --topbar-h: 64px;
            --floating-gap: 16px;
            --content-padding-right: 20px;
            --primary-accent: #2563eb;
            --primary-accent-hover: #1d4ed8;
        }

        * {
            font-family: 'Plus Jakarta Sans', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        html {
            scrollbar-gutter: stable;
        }

        body {
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Modern Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.16);
            border-radius: 999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        /* -------------------------------------------------------------------------- */
        /* FLOATING SIDEBAR STYLES                                                    */
        /* -------------------------------------------------------------------------- */
        .app-sidebar {
            width: var(--sidebar-w);
            position: fixed;
            top: var(--floating-gap);
            bottom: var(--floating-gap);
            left: var(--floating-gap);
            height: calc(100vh - (var(--floating-gap) * 2));
            z-index: 50;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #090e1a 0%, #0f172a 45%, #080d19 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.35), 0 0 1px 1px rgba(255, 255, 255, 0.05);
            transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1), transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        /* Ambient Glow behind Logo */
        .sidebar-ambient-glow {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 180px;
            height: 100px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, transparent 70%);
            pointer-events: none;
            z-index: 1;
        }

        /* Sidebar Header */
        .sidebar-header {
            position: relative;
            z-index: 2;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            overflow: hidden;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .brand-link:hover .brand-icon {
            transform: scale(1.04);
        }
        .brand-icon img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.25));
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease, width 0.2s ease;
        }

        .brand-name {
            font-size: 16.5px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }
        .brand-name span {
            color: #f59e0b;
        }

        .brand-role-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .brand-role-pill .status-pulse {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Sidebar Toggle Chevron Button */
        .sidebar-collapse-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .sidebar-collapse-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }
        .sidebar-collapse-btn i {
            font-size: 11px;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Sidebar Nav Container */
        .sidebar-nav {
            position: relative;
            z-index: 2;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 10px;
            min-height: 0;
        }

        .nav-group {
            margin-bottom: 10px;
        }

        .nav-group-title {
            padding: 8px 12px 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s ease;
        }

        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            height: 40px;
            padding: 0 12px;
            margin: 2px 0;
            border-radius: 10px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
        }

        .nav-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(2px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 14px -1px rgba(37, 99, 235, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .nav-item-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #94a3b8;
            flex-shrink: 0;
            transition: color 0.18s ease, transform 0.18s ease;
        }
        .nav-item:hover .nav-item-icon {
            color: #38bdf8;
            transform: scale(1.08);
        }
        .nav-item.active .nav-item-icon {
            color: #ffffff;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            transform: none;
        }

        .nav-item-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: opacity 0.2s ease, width 0.2s ease;
        }

        /* Sidebar User Footer */
        .sidebar-footer {
            position: relative;
            z-index: 2;
            padding: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.15);
        }

        .user-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 8px 10px;
            transition: all 0.2s ease;
        }
        .user-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .user-card-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
        }

        .user-online-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #10b981;
            border: 2px solid #0f172a;
            border-radius: 50%;
        }

        .user-details {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }
        .user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .user-role-text {
            font-size: 10.5px;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 1px;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 7px;
        }

        .user-profile-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 8px;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .user-profile-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .user-logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 7px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .user-logout-btn:hover {
            background: rgba(239, 68, 68, 0.22);
            color: #ffffff;
            border-color: rgba(239, 68, 68, 0.4);
        }

        /* -------------------------------------------------------------------------- */
        /* COLLAPSED FLOATING SIDEBAR (DESKTOP)                                       */
        /* -------------------------------------------------------------------------- */
        @media (min-width: 1024px) {
            .sidebar-collapsed .app-sidebar {
                width: var(--sidebar-w-collapsed);
            }

            .sidebar-collapsed .brand-text {
                opacity: 0;
                width: 0;
                display: none;
            }

            .sidebar-collapsed .sidebar-header {
                padding: 12px 0 8px 0;
                height: auto;
                flex-direction: column;
                justify-content: center;
                gap: 8px;
            }

            .sidebar-collapsed .brand-link {
                justify-content: center;
            }

            .sidebar-collapsed .sidebar-collapse-btn {
                width: 32px;
                height: 24px;
                border-radius: 6px;
            }

            .sidebar-collapsed .sidebar-collapse-btn i {
                transform: rotate(180deg);
            }

            .sidebar-collapsed .nav-group-title {
                height: 1px;
                margin: 8px 10px;
                padding: 0;
                background: rgba(255, 255, 255, 0.08);
                font-size: 0;
                color: transparent;
            }

            .sidebar-collapsed .nav-item {
                width: 44px;
                height: 40px;
                padding: 0;
                margin: 4px auto;
                justify-content: center;
            }

            .sidebar-collapsed .nav-item:hover {
                transform: scale(1.05);
            }

            .sidebar-collapsed .nav-item-text {
                display: none !important;
            }

            .sidebar-collapsed .user-details,
            .sidebar-collapsed .user-actions {
                display: none !important;
            }

            .sidebar-collapsed .user-card {
                padding: 4px;
                display: flex;
                justify-content: center;
                background: transparent;
                border-color: transparent;
            }

            .sidebar-collapsed .user-avatar {
                width: 38px;
                height: 38px;
                cursor: pointer;
            }

            .sidebar-collapsed .app-main {
                margin-left: calc(var(--sidebar-w-collapsed) + (var(--floating-gap) * 2));
                width: calc(100% - (var(--sidebar-w-collapsed) + (var(--floating-gap) * 2)));
            }
        }

        /* -------------------------------------------------------------------------- */
        /* MAIN CONTENT & FLOATING TOPBAR                                             */
        /* -------------------------------------------------------------------------- */
        .app-main {
            margin-left: calc(var(--sidebar-w) + (var(--floating-gap) * 2));
            width: calc(100% - (var(--sidebar-w) + (var(--floating-gap) * 2)));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-right: var(--content-padding-right);
            transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .app-topbar {
            height: var(--topbar-h);
            margin-top: var(--floating-gap);
            margin-bottom: var(--floating-gap);
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 18px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: var(--floating-gap);
            z-index: 40;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .topbar-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            flex-shrink: 0;
        }
        .topbar-toggle-btn:hover {
            background: #f8fafc;
            color: #2563eb;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .topbar-toggle-btn:active {
            transform: scale(0.96);
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        /* Realtime Clock & Date Capsule */
        .time-capsule {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 12px;
            color: #64748b;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .time-capsule .clock-text {
            font-family: 'Plus Jakarta Sans', monospace;
            font-weight: 700;
            color: #1e3a5f;
            letter-spacing: 0.04em;
        }
        .time-capsule .divider {
            width: 1px;
            height: 14px;
            background: #cbd5e1;
        }

        /* Notification Center */
        .topbar-action-btn {
            position: relative;
            width: 38px;
            height: 38px;
            border-radius: 11px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .topbar-action-btn:hover {
            background: #f8fafc;
            color: #2563eb;
            border-color: #cbd5e1;
        }

        .topbar-badge-count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
            animation: bounce-subtle 3s infinite;
        }

        @keyframes bounce-subtle {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-3px); }
            60% { transform: translateY(-1.5px); }
        }

        /* Notification Dropdown */
        .notif-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 340px;
            max-height: 440px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.18), 0 0 1px 1px rgba(0,0,0,0.04);
            z-index: 100;
            overflow: hidden;
            animation: dropdown-fade-in 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .notif-dropdown.show {
            display: block;
        }

        @keyframes dropdown-fade-in {
            from { opacity: 0; transform: translateY(-8px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .notif-header {
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .notif-header h4 {
            margin: 0;
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .notif-list {
            max-height: 310px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 12px 18px;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            gap: 12px;
            transition: background 0.15s ease;
        }
        .notif-item:hover {
            background: #f8fafc;
        }
        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item.danger {
            border-left: 3px solid #ef4444;
            background: #fffafa;
        }
        .notif-item.warning {
            border-left: 3px solid #f59e0b;
        }

        .notif-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .notif-item.danger .notif-icon {
            background: #fee2e2;
            color: #ef4444;
        }
        .notif-item.warning .notif-icon {
            background: #fef3c7;
            color: #d97706;
        }

        .notif-content {
            flex: 1;
            min-width: 0;
        }
        .notif-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }
        .notif-meta {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.35;
        }
        .notif-badge-time {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            margin-top: 4px;
        }
        .notif-item.danger .notif-badge-time {
            background: #fecaca;
            color: #b91c1c;
        }
        .notif-item.warning .notif-badge-time {
            background: #fed7aa;
            color: #c2410c;
        }

        .notif-footer {
            padding: 10px 16px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .notif-footer a {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
        }
        .notif-footer a:hover {
            text-decoration: underline;
        }

        /* Topbar User Profile Dropdown Pill */
        .topbar-user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 10px 4px 5px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .topbar-user-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .topbar-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        .topbar-user-info {
            text-align: left;
            line-height: 1.15;
        }
        .topbar-user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .topbar-user-role {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            text-transform: capitalize;
        }

        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 240px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.18), 0 0 1px 1px rgba(0,0,0,0.04);
            z-index: 100;
            overflow: hidden;
            animation: dropdown-fade-in 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .user-dropdown-menu.show {
            display: block;
        }

        .user-dropdown-header {
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
        }
        .user-dropdown-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }
        .user-dropdown-email {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-dropdown-links {
            padding: 6px;
        }
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #334155;
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .user-dropdown-item:hover {
            background: #f1f5f9;
            color: #2563eb;
        }
        .user-dropdown-item i {
            width: 16px;
            font-size: 13px;
            color: #94a3b8;
        }
        .user-dropdown-item:hover i {
            color: #2563eb;
        }

        .user-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 4px 6px;
        }

        .user-dropdown-logout {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #ef4444;
            background: transparent;
            border: none;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
        }
        .user-dropdown-logout:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* Floating Tooltip in Collapsed Mode */
        #sidebarFloatingTooltip {
            position: fixed;
            z-index: 9999;
            background: #090e1a;
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.45);
            pointer-events: none;
            opacity: 0;
            transform: translateX(-4px);
            transition: opacity 0.15s ease, transform 0.15s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #sidebarFloatingTooltip.show {
            opacity: 1;
            transform: translateX(0);
        }
        #sidebarFloatingTooltip::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
            width: 8px;
            height: 8px;
            background: #090e1a;
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }

        /* Page Content */
        .app-content {
            padding: 0 0 28px 0;
            flex: 1;
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 45;
            transition: opacity 0.28s ease;
        }
        .sidebar-overlay.show {
            display: block;
        }

        /* -------------------------------------------------------------------------- */
        /* RESPONSIVE (MOBILE & TABLET)                                               */
        /* -------------------------------------------------------------------------- */
        @media (max-width: 1023px) {
            .app-sidebar {
                top: 10px;
                bottom: 10px;
                left: 10px;
                height: calc(100vh - 20px);
                width: 270px;
                border-radius: 20px;
                transform: translateX(-120%);
            }
            .app-sidebar.open-mobile {
                transform: translateX(0);
            }
            .app-main {
                margin-left: 0 !important;
                width: 100% !important;
                padding-right: 0 !important;
                padding-left: 12px;
                padding-right: 12px;
            }
            .app-topbar {
                margin: 10px 0 12px 0;
                padding: 0 14px;
                border-radius: 16px;
            }
            .topbar-toggle-desktop {
                display: none !important;
            }
            .sidebar-collapse-btn {
                display: none !important;
            }
        }

        @media (min-width: 1024px) {
            .topbar-toggle-mobile {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .time-capsule {
                display: none !important;
            }
            .notif-dropdown {
                width: 300px;
                right: -40px;
            }
            .topbar-user-info {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .notif-dropdown {
                width: 280px;
                right: -70px;
            }
        }
    </style>
</head>
<body>

@auth
<!-- Mobile Backdrop Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebarMobile()"></div>

<!-- Floating Tooltip for Collapsed Sidebar -->
<div id="sidebarFloatingTooltip">
    <span id="tooltipText">Tooltip</span>
</div>

<div style="display: flex; min-height: 100vh; width: 100%;">

    <!-- ======================================================================== -->
    <!-- FLOATING APP SIDEBAR                                                     -->
    <!-- ======================================================================== -->
    <aside class="app-sidebar custom-scrollbar" id="appSidebar">
        <!-- Ambient Top Light Glow -->
        <div class="sidebar-ambient-glow"></div>

        <!-- Sidebar Header (Logo & Toggle Button) -->
        <div class="sidebar-header">
            <a href="{{ auth()->user()->role === 'operator' ? route('operator.dashboard') : route('administrator.dashboard') }}" class="brand-link">
                <div class="brand-icon">
                    <img src="{{ asset('images/pesawat.png') }}" alt="Logo">
                </div>
                <div class="brand-text">
                    <div class="brand-name">MONPAS<span>KU</span></div>
                    <div class="brand-role-pill">
                        <span class="status-pulse"></span>
                        <span>{{ auth()->user()->role === 'operator' ? 'Operator' : 'Administrator' }}</span>
                    </div>
                </div>
            </a>

            <!-- Sidebar Minimize / Expand Chevron Button -->
            <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Kecilkan / Luaskan Sidebar (Ctrl+B)">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>

        <!-- Sidebar Nav Links with Clear Groupings -->
        <nav class="sidebar-nav custom-scrollbar">
            @if(auth()->user()->role === 'operator')
                <!-- OPERATOR NAVIGATION -->
                <div class="nav-group">
                    <div class="nav-group-title">Menu Utama</div>
                    <a href="{{ route('operator.dashboard') }}"
                       class="nav-item {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}"
                       data-tooltip="Dashboard">
                        <div class="nav-item-icon"><i class="fas fa-home"></i></div>
                        <span class="nav-item-text">Dashboard</span>
                    </a>
                </div>

                <div class="nav-group">
                    <div class="nav-group-title">Operasional Scanner</div>
                    <a href="{{ route('operator.kamera.index') }}"
                       class="nav-item {{ request()->routeIs('operator.kamera.index') ? 'active' : '' }}"
                       data-tooltip="Akses Kamera">
                        <div class="nav-item-icon"><i class="fas fa-video"></i></div>
                        <span class="nav-item-text">Akses Kamera</span>
                    </a>
                    <a href="{{ route('operator.kamera.scanner') }}"
                       class="nav-item {{ request()->routeIs('operator.kamera.scanner') ? 'active' : '' }}"
                       data-tooltip="Scanner QR">
                        <div class="nav-item-icon"><i class="fas fa-qrcode"></i></div>
                        <span class="nav-item-text">Scanner QR</span>
                    </a>
                    <a href="{{ route('operator.kamera.logs') }}"
                       class="nav-item {{ request()->routeIs('operator.kamera.logs') ? 'active' : '' }}"
                       data-tooltip="Log Pemindaian">
                        <div class="nav-item-icon"><i class="fas fa-history"></i></div>
                        <span class="nav-item-text">Log Pemindaian</span>
                    </a>
                </div>

                <div class="nav-group">
                    <div class="nav-group-title">Pengaturan Akun</div>
                    <a href="{{ route('profile.edit') }}"
                       class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       data-tooltip="Edit Profil">
                        <div class="nav-item-icon"><i class="fas fa-user-circle"></i></div>
                        <span class="nav-item-text">Edit Profil</span>
                    </a>
                </div>

            @else
                <!-- ADMINISTRATOR NAVIGATION -->
                <!-- 1. Menu Utama -->
                <div class="nav-group">
                    <div class="nav-group-title">Menu Utama</div>
                    <a href="{{ route('administrator.dashboard') }}"
                       class="nav-item {{ request()->routeIs('administrator.dashboard') ? 'active' : '' }}"
                       data-tooltip="Dashboard">
                        <div class="nav-item-icon"><i class="fas fa-chart-pie"></i></div>
                        <span class="nav-item-text">Dashboard</span>
                    </a>
                </div>

                <!-- 2. Manajemen Kartu PAS -->
                <div class="nav-group">
                    <div class="nav-group-title">Manajemen Kartu PAS</div>
                    <a href="{{ route('administrator.kartu-pas.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.kartu-pas.*') ? 'active' : '' }}"
                       data-tooltip="Data Kartu PAS">
                        <div class="nav-item-icon"><i class="fas fa-id-card"></i></div>
                        <span class="nav-item-text">Data Kartu PAS</span>
                    </a>
                    <a href="{{ route('administrator.template-kartu.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.template-kartu.*') ? 'active' : '' }}"
                       data-tooltip="Template Desain Kartu">
                        <div class="nav-item-icon"><i class="fas fa-palette"></i></div>
                        <span class="nav-item-text">Template Kartu</span>
                    </a>
                    <a href="{{ route('administrator.monitoring-kuota.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.monitoring-kuota.*') ? 'active' : '' }}"
                       data-tooltip="Monitoring Kuota Instansi">
                        <div class="nav-item-icon"><i class="fas fa-chart-line"></i></div>
                        <span class="nav-item-text">Monitoring Kuota</span>
                    </a>
                </div>

                <!-- 3. Data Master & Instansi -->
                <div class="nav-group">
                    <div class="nav-group-title">Data Master & Instansi</div>
                    <a href="{{ route('administrator.instansi.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.instansi.*') ? 'active' : '' }}"
                       data-tooltip="Data Instansi & Perusahaan">
                        <div class="nav-item-icon"><i class="fas fa-building"></i></div>
                        <span class="nav-item-text">Data Instansi</span>
                    </a>
                    <a href="{{ route('administrator.dokumen-persyaratan.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.dokumen-persyaratan.*') ? 'active' : '' }}"
                       data-tooltip="Dokumen Persyaratan">
                        <div class="nav-item-icon"><i class="fas fa-folder-open"></i></div>
                        <span class="nav-item-text">Dokumen Persyaratan</span>
                    </a>
                </div>

                <!-- 4. Keamanan & Operator -->
                <div class="nav-group">
                    <div class="nav-group-title">Keamanan & Operator</div>
                    <a href="{{ route('administrator.perangkat-kamera.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.perangkat-kamera.*') ? 'active' : '' }}"
                       data-tooltip="Perangkat Kamera & Scanner">
                        <div class="nav-item-icon"><i class="fas fa-video"></i></div>
                        <span class="nav-item-text">Perangkat Kamera</span>
                    </a>
                    <a href="{{ route('administrator.users.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.users.*') ? 'active' : '' }}"
                       data-tooltip="Manajemen Akun Operator">
                        <div class="nav-item-icon"><i class="fas fa-user-shield"></i></div>
                        <span class="nav-item-text">Akun Operator</span>
                    </a>
                </div>

                <!-- 5. Laporan & Audit -->
                <div class="nav-group">
                    <div class="nav-group-title">Laporan & Audit</div>
                    <a href="{{ route('administrator.laporan.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.laporan.index') || request()->routeIs('administrator.laporan.export*') ? 'active' : '' }}"
                       data-tooltip="Laporan Rekapitulasi Kartu">
                        <div class="nav-item-icon"><i class="fas fa-file-invoice"></i></div>
                        <span class="nav-item-text">Laporan Kartu PAS</span>
                    </a>
                    <a href="{{ route('administrator.laporan-aktivitas.index') }}"
                       class="nav-item {{ request()->routeIs('administrator.laporan-aktivitas.*') ? 'active' : '' }}"
                       data-tooltip="Log Aktivitas & Pemindaian">
                        <div class="nav-item-icon"><i class="fas fa-shoe-prints"></i></div>
                        <span class="nav-item-text">Laporan Aktivitas</span>
                    </a>
                </div>

                <!-- 6. Pengaturan Akun -->
                <div class="nav-group">
                    <div class="nav-group-title">Pengaturan Akun</div>
                    <a href="{{ route('profile.edit') }}"
                       class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       data-tooltip="Edit Profil">
                        <div class="nav-item-icon"><i class="fas fa-user-gear"></i></div>
                        <span class="nav-item-text">Edit Profil</span>
                    </a>
                </div>
            @endif
        </nav>

        <!-- Sidebar User Footer -->
        <div class="sidebar-footer">
            <div class="user-card" id="sidebarUserCard" data-tooltip="{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})">
                <div class="user-card-content">
                    <div class="user-avatar-wrap">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="user-online-dot"></span>
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role-text">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="user-actions">
                    <a href="{{ route('profile.edit') }}" class="user-profile-btn" title="Edit Profil Saya">
                        <i class="fas fa-pen-to-square text-xs"></i> Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="user-logout-btn" title="Keluar Dari Sistem">
                            <i class="fas fa-arrow-right-from-bracket text-xs"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- ======================================================================== -->
    <!-- MAIN CONTENT CONTAINER                                                   -->
    <!-- ======================================================================== -->
    <div class="app-main" id="appMain">

        <!-- Floating Topbar -->
        <header class="app-topbar">
            <!-- Left: Toggle & Title -->
            <div class="topbar-left">
                <!-- Desktop Sidebar Toggle Button -->
                <button type="button" class="topbar-toggle-btn topbar-toggle-desktop" id="topbarToggleDesktop" title="Kecilkan / Luaskan Sidebar (Ctrl+B)">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>

                <!-- Mobile Hamburger Button -->
                <button type="button" class="topbar-toggle-btn topbar-toggle-mobile" id="topbarToggleMobile" onclick="openSidebarMobile()" title="Buka Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Dynamic Page Title from Slot -->
                <div class="topbar-title">
                    @isset($header)
                        {{ $header }}
                    @else
                        <span class="text-slate-800 font-bold">MONPASKU Management Hub</span>
                    @endisset
                </div>
            </div>

            <!-- Right: Actions, Clock, Notifications, User -->
            <div class="topbar-right">

                <!-- Operator Live Camera Badge -->
                @if(auth()->user()->role === 'operator')
                    @if(session('camera_device_id'))
                        <a href="{{ route('operator.kamera.scanner') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-full text-xs font-semibold transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <i class="fas fa-video text-emerald-600"></i>
                            <span>{{ session('camera_name') }} ({{ session('camera_area') }})</span>
                        </a>
                    @else
                        <a href="{{ route('operator.kamera.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 rounded-full text-xs font-semibold transition-colors">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <i class="fas fa-video-slash text-amber-500"></i>
                            <span>Hubungkan Kamera</span>
                        </a>
                    @endif
                @endif

                <!-- Live Date & Monospace Clock Capsule -->
                <div class="time-capsule">
                    <span class="flex items-center gap-1.5">
                        <i class="far fa-calendar-alt text-blue-600"></i>
                        <span id="topbarTanggal">Memuat...</span>
                    </span>
                    <span class="divider"></span>
                    <span class="flex items-center gap-1.5 clock-text">
                        <i class="far fa-clock text-blue-600"></i>
                        <span id="topbarJam">--:--:--</span>
                    </span>
                </div>

                <!-- Notification Center (Khusus Administrator) -->
                @if(auth()->user()->role === 'administrator')
                @php
                    $kartuHampirKadaluarsa = App\Models\KartuPas::where('status', 'aktif')
                        ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])
                        ->orderBy('tanggal_berlaku')
                        ->get();
                @endphp
                <div class="relative" id="notifBellContainer">
                    <button type="button" class="topbar-action-btn" id="notifBellBtn" onclick="toggleNotifDropdown(event)" title="Notifikasi Kartu PAS">
                        <i class="far fa-bell text-base"></i>
                        @if($kartuHampirKadaluarsa->count() > 0)
                            <span class="topbar-badge-count">{{ $kartuHampirKadaluarsa->count() }}</span>
                        @endif
                    </button>

                    <!-- Modern Notification Dropdown -->
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <div>
                                <h4>Notifikasi Kartu PAS</h4>
                                <span class="text-[11px] text-slate-400">Masa berlaku < 30 hari</span>
                            </div>
                            @if($kartuHampirKadaluarsa->count() > 0)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                    {{ $kartuHampirKadaluarsa->count() }} Perhatian
                                </span>
                            @endif
                        </div>

                        <div class="notif-list custom-scrollbar">
                            @forelse($kartuHampirKadaluarsa as $kartu)
                                @php $sisaHari = (int) now()->diffInDays($kartu->tanggal_berlaku); @endphp
                                <div class="notif-item {{ $sisaHari <= 7 ? 'danger' : 'warning' }}">
                                    <div class="notif-icon">
                                        <i class="fas {{ $sisaHari <= 7 ? 'fa-triangle-exclamation' : 'fa-clock' }}"></i>
                                    </div>
                                    <div class="notif-content">
                                        <div class="notif-title">{{ $kartu->nama_pemegang }}</div>
                                        <div class="notif-meta">
                                            No: <strong>{{ $kartu->nomor_kartu }}</strong> · {{ $kartu->perusahaan }}<br>
                                            Berlaku s/d: {{ $kartu->tanggal_berlaku->format('d M Y') }}
                                        </div>
                                        <span class="notif-badge-time">
                                            {{ $sisaHari == 0 ? 'Hari Ini Berakhir!' : "Sisa {$sisaHari} Hari" }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 mx-auto flex items-center justify-center text-lg mb-2">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="text-xs font-bold text-slate-700">Semua Kartu Terkendali</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Tidak ada kartu mendekati masa kadaluarsa</div>
                                </div>
                            @endforelse
                        </div>

                        <div class="notif-footer">
                            <a href="{{ route('administrator.kartu-pas.index') }}">
                                Kelola & Lihat Semua Kartu PAS <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- User Profile Pill & Dropdown -->
                <div class="relative" id="topbarUserDropdownContainer">
                    <button type="button" class="topbar-user-btn" id="topbarUserBtn" onclick="toggleUserDropdown(event)">
                        <div class="topbar-user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="topbar-user-info">
                            <div class="topbar-user-name">{{ auth()->user()->name }}</div>
                            <div class="topbar-user-role">{{ auth()->user()->role }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                    </button>

                    <!-- User Profile Dropdown Menu -->
                    <div class="user-dropdown-menu" id="topbarUserDropdownMenu">
                        <div class="user-dropdown-header">
                            <div class="user-dropdown-name">{{ auth()->user()->name }}</div>
                            <div class="user-dropdown-email">{{ auth()->user()->email }}</div>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wider">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                        <div class="user-dropdown-links">
                            <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                <i class="fas fa-user-gear"></i>
                                <span>Edit Profil</span>
                            </a>
                            @if(auth()->user()->role === 'administrator')
                                <a href="{{ route('administrator.kartu-pas.index') }}" class="user-dropdown-item">
                                    <i class="fas fa-id-card"></i>
                                    <span>Data Kartu PAS</span>
                                </a>
                                <a href="{{ route('administrator.monitoring-kuota.index') }}" class="user-dropdown-item">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Monitoring Kuota</span>
                                </a>
                            @else
                                <a href="{{ route('operator.kamera.scanner') }}" class="user-dropdown-item">
                                    <i class="fas fa-qrcode"></i>
                                    <span>Buka Scanner QR</span>
                                </a>
                            @endif
                        </div>
                        <div class="user-dropdown-divider"></div>
                        <div class="p-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-dropdown-logout">
                                    <i class="fas fa-arrow-right-from-bracket"></i>
                                    <span>Keluar dari Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Page Content -->
        <main class="app-content">
            {{ $slot }}
        </main>

    </div>

</div>
@endauth

<!-- Global App Scripts -->
<script>
    // -------------------------------------------------------------------------
    // 1. Sidebar Collapse & Minimize State Management
    // -------------------------------------------------------------------------
    const STORAGE_KEY = 'monpasku_sidebar_collapsed';

    function isSidebarCollapsed() {
        return document.documentElement.classList.contains('sidebar-collapsed');
    }

    function toggleSidebarCollapse() {
        if (window.innerWidth < 1024) {
            // Mobile: toggle drawer
            const sidebar = document.getElementById('appSidebar');
            if (sidebar.classList.contains('open-mobile')) {
                closeSidebarMobile();
            } else {
                openSidebarMobile();
            }
            return;
        }

        // Desktop: toggle mini / expanded rail
        const newState = !isSidebarCollapsed();
        if (newState) {
            document.documentElement.classList.add('sidebar-collapsed');
            localStorage.setItem(STORAGE_KEY, 'true');
        } else {
            document.documentElement.classList.remove('sidebar-collapsed');
            localStorage.setItem(STORAGE_KEY, 'false');
        }
    }

    const btnSidebarCollapse = document.getElementById('sidebarCollapseBtn');
    if (btnSidebarCollapse) {
        btnSidebarCollapse.addEventListener('click', toggleSidebarCollapse);
    }

    const btnHeaderToggle = document.getElementById('topbarToggleDesktop');
    if (btnHeaderToggle) {
        btnHeaderToggle.addEventListener('click', toggleSidebarCollapse);
    }

    // Keyboard Shortcut (Ctrl+B / Cmd+B) to toggle sidebar
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            toggleSidebarCollapse();
        }
    });

    // -------------------------------------------------------------------------
    // 2. Mobile Sidebar Drawer
    // -------------------------------------------------------------------------
    function openSidebarMobile() {
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.add('open-mobile');
        if (overlay) overlay.classList.add('show');
    }

    function closeSidebarMobile() {
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.remove('open-mobile');
        if (overlay) overlay.classList.remove('show');
    }

    // -------------------------------------------------------------------------
    // 3. Floating Tooltip for Collapsed Sidebar Items
    // -------------------------------------------------------------------------
    const floatingTooltip = document.getElementById('sidebarFloatingTooltip');
    const tooltipText     = document.getElementById('tooltipText');

    function attachFloatingTooltips() {
        const items = document.querySelectorAll('.app-sidebar .nav-item[data-tooltip], .app-sidebar #sidebarUserCard[data-tooltip]');

        items.forEach(item => {
            item.addEventListener('mouseenter', function() {
                if (!isSidebarCollapsed() || window.innerWidth < 1024) return;

                const text = item.getAttribute('data-tooltip');
                if (!text || !floatingTooltip) return;

                tooltipText.textContent = text;
                const rect = item.getBoundingClientRect();
                const tooltipX = rect.right + 12;
                const tooltipY = rect.top + (rect.height / 2) - 15;

                floatingTooltip.style.left = `${tooltipX}px`;
                floatingTooltip.style.top  = `${tooltipY}px`;
                floatingTooltip.classList.add('show');
            });

            item.addEventListener('mouseleave', function() {
                if (floatingTooltip) {
                    floatingTooltip.classList.remove('show');
                }
            });
        });
    }
    attachFloatingTooltips();

    // -------------------------------------------------------------------------
    // 4. Realtime Indonesian Clock & Date
    // -------------------------------------------------------------------------
    function updateClock() {
        const now       = new Date();
        const hari      = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const bulan     = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const namaHari  = hari[now.getDay()];
        const tgl       = now.getDate();
        const namaBulan = bulan[now.getMonth()];
        const tahun     = now.getFullYear();
        const jam       = String(now.getHours()).padStart(2, '0');
        const menit     = String(now.getMinutes()).padStart(2, '0');
        const detik     = String(now.getSeconds()).padStart(2, '0');

        const elTanggal = document.getElementById('topbarTanggal');
        const elJam     = document.getElementById('topbarJam');
        if (elTanggal) elTanggal.textContent = `${namaHari}, ${tgl} ${namaBulan} ${tahun}`;
        if (elJam) elJam.textContent         = `${jam}:${menit}:${detik}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // -------------------------------------------------------------------------
    // 5. Dropdowns: Notification & User Profile
    // -------------------------------------------------------------------------
    function toggleNotifDropdown(e) {
        if (e) e.stopPropagation();
        const dropdown = document.getElementById('notifDropdown');
        const userMenu = document.getElementById('topbarUserDropdownMenu');
        if (userMenu) userMenu.classList.remove('show');
        if (dropdown) dropdown.classList.toggle('show');
    }

    function toggleUserDropdown(e) {
        if (e) e.stopPropagation();
        const userMenu = document.getElementById('topbarUserDropdownMenu');
        const notif    = document.getElementById('notifDropdown');
        if (notif) notif.classList.remove('show');
        if (userMenu) userMenu.classList.toggle('show');
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function(e) {
        const notifContainer = document.getElementById('notifBellContainer');
        const notifDropdown  = document.getElementById('notifDropdown');
        if (notifContainer && notifDropdown && !notifContainer.contains(e.target)) {
            notifDropdown.classList.remove('show');
        }

        const userContainer = document.getElementById('topbarUserDropdownContainer');
        const userMenu      = document.getElementById('topbarUserDropdownMenu');
        if (userContainer && userMenu && !userContainer.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    });
</script>

{{-- Global Reusable Toast Notifications (Session Flash & JS API) --}}
<x-toast-notification />

{{-- Global Reusable Delete Confirmation Modal --}}
<x-modal-delete />

<script>
        // Modernized SweetAlert2 Confirmation Helper
        function SwalConfirm(title, text, confirmButtonText = 'Ya, Lanjutkan!') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                backdrop: 'rgba(15, 23, 42, 0.6)',
                customClass: {
                    popup: 'rounded-3xl border border-slate-100 shadow-2xl p-6 font-sans',
                    title: 'text-base font-bold text-slate-800',
                    htmlContainer: 'text-xs text-slate-500 leading-relaxed mt-1',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-500/20 cursor-pointer ml-2',
                    cancelButton: 'px-4 py-2.5 rounded-xl font-semibold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer'
                },
                buttonsStyling: false
            });
        }
    </script>

</body>
</html>