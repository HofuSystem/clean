<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to Store...</title>
    @if($settings->facebook_pixel_id)
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '{{ $settings->facebook_pixel_id }}');fbq('track', 'AppInstallIntent');</script>
    @endif
    
    @if($settings->snapchat_pixel_id)
    <script>(function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};a.queue=[];var s='script';r=t.createElement(s);r.async=!0;r.src=n;var u=t.getElementsByTagName(s)[0];u.parentNode.insertBefore(r,u);})(window,document,'https://sc-static.net/scevent.min.js');snaptr('init', '{{ $settings->snapchat_pixel_id }}');snaptr('track', 'APP_INSTALL');</script>
    @endif

    <meta http-equiv="refresh" content="1;url={{ $destination }}">
</head>
<body style="background:#f0f9ff; display:flex; justify-content:center; align-items:center; height:100vh; flex-direction:column; font-family:sans-serif;">
    <div style="width:50px; height:50px; border:5px solid #0ea5e9; border-top:5px solid transparent; border-radius:50%; animation:spin 1s linear infinite;"></div>
    <p style="margin-top:20px; color:#555;">جاري تحويلك للمتجر...</p>
    <style>@keyframes spin {0% {transform:rotate(0deg);} 100% {transform:rotate(360deg);}}</style>
    <script>
        setTimeout(function(){ window.location.href = "{{ $destination }}"; }, 1000);
    </script>
</body>
</html>
