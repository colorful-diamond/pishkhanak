# Optimized Varnish Configuration for Laravel with Authentication
# Version: 4.0
# Compatible with: Laravel 11, CloudPanel, Production Environment

vcl 4.0;

import std;
import directors;

# Backend configuration
backend default {
    .host = "127.0.0.1";
    .port = "8080";
    .first_byte_timeout = 300s;
    .connect_timeout = 5s;
    .between_bytes_timeout = 60s;
    .max_connections = 800;
    .probe = {
        .url = "/health-check";
        .timeout = 2s;
        .interval = 5s;
        .window = 10;
        .threshold = 5;
    }
}

# ACL for purging
acl purger {
    "localhost";
    "127.0.0.1";
    "172.17.0.1";
}

# ACL for admin IPs
acl admin_network {
    "127.0.0.1";
    "localhost";
}

sub vcl_init {
    # Initialize directors for load balancing if needed
    new vdir = directors.round_robin();
    vdir.add_backend(default);
}

sub vcl_recv {
    # Restart handling
    if (req.restarts > 0) {
        set req.hash_always_miss = true;
    }

    # Set backend hint
    set req.backend_hint = vdir.backend();

    # === PURGE Request Handling ===
    if (req.method == "PURGE") {
        if (client.ip !~ purger) {
            return (synth(405, "Purging not allowed from " + client.ip));
        }
        
        # Handle different purge strategies
        if (req.http.X-Purge-Method == "regex") {
            ban("obj.http.X-URL ~ " + req.url + " && obj.http.X-Host == " + req.http.host);
            return (synth(200, "Regex purged"));
        } elsif (req.http.X-Cache-Tags) {
            ban("obj.http.X-Cache-Tags ~ " + req.http.X-Cache-Tags);
            return (synth(200, "Cache tags purged"));
        } else {
            return (purge);
        }
    }

    # === Health Check Endpoint ===
    if (req.url == "/varnish-health") {
        return (synth(200, "Varnish is healthy"));
    }

    # === Method Filtering ===
    if (req.method != "GET" &&
        req.method != "HEAD" &&
        req.method != "PUT" &&
        req.method != "POST" &&
        req.method != "TRACE" &&
        req.method != "OPTIONS" &&
        req.method != "DELETE" &&
        req.method != "PATCH") {
        return (pipe);
    }

    # === POST/PUT/PATCH/DELETE - Never Cache ===
    if (req.method == "POST" || req.method == "PUT" || 
        req.method == "PATCH" || req.method == "DELETE") {
        return (pass);
    }

    # === URL Normalization ===
    # Remove the protocol and host from URL
    set req.url = regsub(req.url, "^https?://[^/]+", "");
    
    # Remove trailing slash except for root
    if (req.url != "/" && req.url ~ "/$") {
        set req.url = regsub(req.url, "/$", "");
    }
    
    # Sort query parameters for better cache hit ratio
    set req.url = std.querysort(req.url);
    
    # Remove tracking parameters
    if (req.url ~ "(\?|&)(utm_[a-z]+|gclid|fbclid|msclkid|mc_[a-z]+|_ga|_ke)=") {
        set req.url = regsuball(req.url, "(utm_[a-z]+|gclid|fbclid|msclkid|mc_[a-z]+|_ga|_ke)=[^&]+&?", "");
        set req.url = regsub(req.url, "[?|&]+$", "");
    }

    # === Laravel Specific Rules ===
    
    # Admin Panel - Always Pass
    if (req.url ~ "^/admin" || 
        req.url ~ "^/filament" ||
        req.url ~ "^/livewire" ||
        req.url ~ "^/panel") {
        return (pass);
    }
    
    # User Dashboard/Account - Always Pass
    if (req.url ~ "^/user" ||
        req.url ~ "^/dashboard" ||
        req.url ~ "^/account" ||
        req.url ~ "^/profile") {
        return (pass);
    }
    
    # Authentication Routes - Always Pass
    if (req.url ~ "^/login" ||
        req.url ~ "^/logout" ||
        req.url ~ "^/register" ||
        req.url ~ "^/password" ||
        req.url ~ "^/verify" ||
        req.url ~ "^/two-factor" ||
        req.url ~ "^/sanctum") {
        return (pass);
    }
    
    # API Routes - Conditional Caching
    if (req.url ~ "^/api/") {
        # Public API endpoints can be cached
        if (req.url ~ "^/api/public/" ||
            req.url ~ "^/api/v1/services" ||
            req.url ~ "^/api/v1/categories") {
            unset req.http.Cookie;
            unset req.http.Authorization;
            return (hash);
        }
        # Authenticated API - Pass
        return (pass);
    }
    
    # Payment/Transaction Routes - Always Pass
    if (req.url ~ "^/payment" ||
        req.url ~ "^/wallet" ||
        req.url ~ "^/gateway" ||
        req.url ~ "^/transaction" ||
        req.url ~ "^/checkout") {
        return (pass);
    }
    
    # Form Submission Routes - Always Pass
    if (req.url ~ "/submit" ||
        req.url ~ "/store" ||
        req.url ~ "/create" ||
        req.url ~ "/update" ||
        req.url ~ "/delete" ||
        req.url ~ "/process") {
        return (pass);
    }
    
    # OTP and Verification - Always Pass
    if (req.url ~ "/otp" ||
        req.url ~ "/sms-verify" ||
        req.url ~ "/verify-otp" ||
        req.url ~ "/captcha") {
        return (pass);
    }
    
    # Service Results (Dynamic) - Always Pass
    if (req.url ~ "/result/" ||
        req.url ~ "/preview/" ||
        req.url ~ "/sms-result/") {
        return (pass);
    }
    
    # Telegram Bot Webhook - Always Pass
    if (req.url ~ "^/telegram" ||
        req.url ~ "^/webhook/telegram") {
        return (pass);
    }

    # === Cookie Handling ===
    
    # Authenticated Users - Check Laravel Session Cookie
    if (req.http.Cookie ~ "laravel_session=" ||
        req.http.Cookie ~ "XSRF-TOKEN=" ||
        req.http.Cookie ~ "remember_web_") {
        # User is potentially logged in
        # For certain pages, we can still cache with Vary
        if (req.url ~ "^/$" ||
            req.url ~ "^/blog" ||
            req.url ~ "^/services" ||
            req.url ~ "^/category") {
            # These pages can have different cached versions
            # We'll use Vary headers
            return (hash);
        } else {
            # Dynamic user content
            return (pass);
        }
    }
    
    # Remove Google Analytics cookies
    set req.http.Cookie = regsuball(req.http.Cookie, "(^|;\s*)(_ga|_gat|_gid|_gcl_au|_fbp)=[^;]*", "");
    
    # Remove tracking cookies
    set req.http.Cookie = regsuball(req.http.Cookie, "(^|;\s*)(_utm[a-z]+|has_js)=[^;]*", "");
    
    # Remove empty cookies
    set req.http.Cookie = regsuball(req.http.Cookie, "^;\s*", "");
    set req.http.Cookie = regsuball(req.http.Cookie, ";\s*$", "");
    
    # If no cookies left, remove the header
    if (req.http.Cookie == "") {
        unset req.http.Cookie;
    }

    # === Static Assets - Cache Aggressively ===
    if (req.url ~ "^/build/" ||
        req.url ~ "^/assets/" ||
        req.url ~ "^/css/" ||
        req.url ~ "^/js/" ||
        req.url ~ "^/images/" ||
        req.url ~ "^/fonts/" ||
        req.url ~ "^/public/" ||
        req.url ~ "\.(jpg|jpeg|png|gif|ico|svg|webp|woff|woff2|ttf|eot|css|js|map)(\?.*)?$") {
        unset req.http.Cookie;
        unset req.http.Authorization;
        return (hash);
    }
    
    # === Content Type Specific Rules ===
    if (req.url ~ "\.(pdf|zip|tar|gz|bz2|mp3|mp4|avi|mov|wmv|flv)(\?.*)?$") {
        unset req.http.Cookie;
        return (hash);
    }

    # === Accept-Encoding Normalization ===
    if (req.http.Accept-Encoding) {
        if (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elsif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            unset req.http.Accept-Encoding;
        }
    }

    # === Authorization Header ===
    if (req.http.Authorization) {
        # Bearer tokens for API
        return (pass);
    }

    # Default - Hash the request
    return (hash);
}

sub vcl_hash {
    # Standard hash data
    hash_data(req.url);
    
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }
    
    # Hash based on protocol (HTTP vs HTTPS)
    if (req.http.X-Forwarded-Proto) {
        hash_data(req.http.X-Forwarded-Proto);
    }
    
    # For logged-in users, create user-specific cache
    if (req.http.Cookie ~ "laravel_session=") {
        # Extract session ID for user-specific caching
        set req.http.X-Session-Hash = regsub(req.http.Cookie, ".*laravel_session=([^;]+).*", "\1");
        hash_data(req.http.X-Session-Hash);
    }
    
    # Mobile vs Desktop (if you want separate caches)
    if (req.http.User-Agent ~ "(Mobile|Android|iPhone|iPad)") {
        hash_data("mobile");
    } else {
        hash_data("desktop");
    }
    
    # Language preference (for multi-language sites)
    if (req.http.Accept-Language) {
        if (req.http.Accept-Language ~ "^fa") {
            hash_data("fa");
        } else {
            hash_data("en");
        }
    }
    
    return (lookup);
}

sub vcl_backend_response {
    # Store URL and Host for purging
    set beresp.http.X-URL = bereq.url;
    set beresp.http.X-Host = bereq.http.host;
    
    # === Grace Period ===
    # Keep objects in cache for 6 hours past their TTL for grace mode
    set beresp.grace = 6h;
    
    # === ESI Processing ===
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }
    
    # === Error Page Caching ===
    if (beresp.status >= 400 && beresp.status < 600) {
        if (beresp.status == 404) {
            # Cache 404s for a short time
            set beresp.ttl = 1m;
        } elsif (beresp.status >= 500) {
            # Don't cache 5xx errors
            set beresp.ttl = 0s;
            set beresp.uncacheable = true;
            return (deliver);
        } else {
            # Other 4xx errors - brief cache
            set beresp.ttl = 10s;
        }
    }
    
    # === Laravel Cache Headers ===
    if (beresp.http.Cache-Control ~ "private" ||
        beresp.http.Cache-Control ~ "no-cache" ||
        beresp.http.Cache-Control ~ "no-store") {
        set beresp.uncacheable = true;
        set beresp.ttl = 120s;
        return (deliver);
    }
    
    # === Set-Cookie Handling ===
    if (beresp.http.Set-Cookie) {
        # Check if it's a tracking cookie we can ignore
        if (beresp.http.Set-Cookie ~ "(^|;\s*)(_ga|_gat|_gid|_gcl_au|_fbp)=") {
            unset beresp.http.Set-Cookie;
        } elsif (bereq.url ~ "^/build/" || bereq.url ~ "\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2)(\?.*)?$") {
            # Remove cookies for static assets
            unset beresp.http.Set-Cookie;
        } else {
            # Has important cookies - don't cache
            set beresp.uncacheable = true;
            set beresp.ttl = 120s;
            return (deliver);
        }
    }
    
    # === TTL Strategy Based on Content Type ===
    
    # Static assets - Long cache
    if (bereq.url ~ "^/build/" || bereq.url ~ "\.(css|js|jpg|jpeg|png|gif|ico|svg|webp|woff|woff2|ttf|eot)(\?.*)?$") {
        set beresp.ttl = 30d;
        set beresp.http.Cache-Control = "public, max-age=2592000";
    }
    # HTML pages - Moderate cache
    elsif (beresp.http.Content-Type ~ "text/html") {
        if (bereq.url ~ "^/$") {
            # Homepage - shorter cache
            set beresp.ttl = 5m;
        } elsif (bereq.url ~ "^/blog") {
            # Blog pages - moderate cache
            set beresp.ttl = 15m;
        } elsif (bereq.url ~ "^/services" || bereq.url ~ "^/category") {
            # Service pages - longer cache
            set beresp.ttl = 1h;
        } else {
            # Other HTML
            set beresp.ttl = 10m;
        }
    }
    # API responses
    elsif (bereq.url ~ "^/api/public/") {
        set beresp.ttl = 5m;
    }
    # Default
    elsif (!beresp.http.Cache-Control) {
        set beresp.ttl = 1m;
    }
    
    # === Compression ===
    if (beresp.http.Content-Type ~ "(text|application/(javascript|json|xml))") {
        set beresp.do_gzip = true;
    }
    
    return (deliver);
}

sub vcl_deliver {
    # === Debug Headers (remove in production) ===
    if (client.ip ~ admin_network) {
        if (obj.hits > 0) {
            set resp.http.X-Cache = "HIT (" + obj.hits + ")";
        } else {
            set resp.http.X-Cache = "MISS";
        }
        set resp.http.X-Cache-TTL = obj.ttl;
    } else {
        # Remove debug headers for regular users
        unset resp.http.X-Varnish;
        unset resp.http.Via;
        unset resp.http.X-Cache;
        unset resp.http.X-Cache-TTL;
    }
    
    # === Security Headers ===
    set resp.http.X-Frame-Options = "SAMEORIGIN";
    set resp.http.X-Content-Type-Options = "nosniff";
    set resp.http.X-XSS-Protection = "1; mode=block";
    set resp.http.Referrer-Policy = "strict-origin-when-cross-origin";
    
    # === Clean up internal headers ===
    unset resp.http.X-URL;
    unset resp.http.X-Host;
    unset resp.http.X-Session-Hash;
    unset resp.http.X-Powered-By;
    unset resp.http.Server;
    
    # === Add custom headers ===
    set resp.http.X-Served-By = "Varnish";
    
    return (deliver);
}

sub vcl_hit {
    # Handle grace mode
    if (obj.ttl >= 0s) {
        # Object is fresh
        return (deliver);
    }
    
    if (std.healthy(req.backend_hint)) {
        # Backend is healthy but object is stale
        # Deliver stale object and fetch fresh in background
        if (obj.ttl + obj.grace > 0s) {
            return (deliver);
        }
    } else {
        # Backend is sick - deliver stale object if within grace
        if (obj.ttl + obj.grace > 0s) {
            return (deliver);
        }
    }
    
    return (miss);
}

sub vcl_miss {
    return (fetch);
}

sub vcl_backend_error {
    # Generate a synthetic error page
    set beresp.http.Content-Type = "text/html; charset=utf-8";
    set beresp.http.Retry-After = "5";
    
    synthetic({"<!DOCTYPE html>
<html>
<head>
    <title>خطای سرور</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Tahoma, Arial; text-align: center; padding: 50px; direction: rtl; }
        h1 { color: #e74c3c; }
        p { color: #555; }
    </style>
</head>
<body>
    <h1>خطای موقت در سرور</h1>
    <p>متأسفیم، در حال حاضر امکان پردازش درخواست شما وجود ندارد.</p>
    <p>لطفاً چند لحظه دیگر مجدداً تلاش کنید.</p>
    <p><small>Error "} + beresp.status + " " + beresp.reason + {"</small></p>
</body>
</html>"});
    
    return (deliver);
}

sub vcl_synth {
    set resp.http.Content-Type = "text/html; charset=utf-8";
    set resp.http.Retry-After = "5";
    
    if (resp.status == 720) {
        # Redirect
        set resp.http.Location = resp.reason;
        set resp.status = 302;
        return (deliver);
    }
    
    synthetic({"<!DOCTYPE html>
<html>
<head>
    <title>Error "} + resp.status + {"</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>Error "} + resp.status + {"</h1>
    <p>"} + resp.reason + {"</p>
</body>
</html>"});
    
    return (deliver);
}

sub vcl_fini {
    return (ok);
}